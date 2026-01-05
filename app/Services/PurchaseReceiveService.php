<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseReceiveService
{
    /**
     * Get paginated purchase receives with filters and search.
     */
    public function getPaginatedReceives(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = PurchaseReceive::with(['supplier', 'purchaseOrder', 'items.product']);

        // Apply search
        if ($search = $request->get('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('receive_number', 'like', "%{$search}%")
                    ->orWhere('receiver_name', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function (Builder $sq) use ($search) {
                        $sq->where('supplier_name', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('purchaseOrder', function (Builder $poq) use ($search) {
                        $poq->where('order_number', 'like', "%{$search}%");
                    });
            });
        }

        // Apply filters
        if ($status = $request->get('status')) {
            if ($status === 'short_closed') {
                // Filter for short closed items (received status but marked as short closed)
                $query->where('status', 'received')
                      ->where('is_short_closed', true);
            } elseif ($status === 'received') {
                // Filter for completely received items (received status but NOT short closed)
                $query->where('status', 'received')
                      ->where('is_short_closed', false);
            } else {
                // Normal status filter
                $query->where('status', $status);
            }
        }

        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        if ($purchaseOrderId = $request->get('purchase_order_id')) {
            $query->where('purchase_order_id', $purchaseOrderId);
        }

        // Date range filters
        if ($startDate = $request->get('start_date')) {
            $query->where('receive_date', '>=', $startDate);
        }

        if ($endDate = $request->get('end_date')) {
            $query->where('receive_date', '<=', $endDate);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'receive_date');
        $sortOrder = $request->get('order', 'desc');

        if (in_array($sortField, ['receive_number', 'receive_date', 'total_amount_received', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for purchase receive listing.
     *
     * WORKFLOW NOTE: Receives can ONLY be created for:
     * 1. Orders that are 'ordered' (still expecting goods)
     * 2. A delivery MUST exist for the order (delivery is mandatory)
     * 3. At least one delivery must be in 'delivered' status
     * 4. The receive is linked to a delivered delivery (auto-assigned if not specified)
     */
    public function getFilterOptions(): array
    {
        return [
            'suppliers' => Supplier::where('status', 'active')
                ->orderBy('supplier_name')
                ->get()
                ->map(function ($supplier) {
                    return [
                        'id' => $supplier->supplier_id,
                        'name' => $supplier->supplier_name ?? $supplier->name,
                    ];
                }),
            'purchase_orders' => PurchaseOrder::with(['supplier'])
                ->where('status', 'ordered')
                ->orderBy('order_number')
                ->get()
                ->filter(function ($order) {
                    // Only include orders that can receive items (checks delivery status)
                    return $order->canReceiveItems();
                })
                ->map(function ($order) {
                    return [
                        'id' => $order->order_id,
                        'order_number' => $order->order_number,
                        'supplier_name' => $order->supplier->supplier_name ?? $order->supplier->name,
                        'supplier_id' => $order->supplier_id,
                    ];
                })
                ->values(), // Re-index array after filter
        ];
    }

    /**
     * Create a new purchase receive.
     *
     * WORKFLOW:
     * 1. Can be created directly from a PO (delivery_id optional)
     * 2. Can be created from a delivery (delivery_id provided)
     * 3. Updates inventory via StockMovement
     * 4. Updates PO status to 'partial_received' or 'received'
     * 5. Updates PO items' quantity_received
     */
    public function createReceive(array $data): PurchaseReceive
    {
        // Set defaults
        $data['receive_date'] = $data['receive_date'] ?? Carbon::today();
        $data['status'] = $data['status'] ?? 'in_transit';

        // Get purchase order to auto-fill supplier
        if (isset($data['purchase_order_id'])) {
            $purchaseOrder = PurchaseOrder::find($data['purchase_order_id']);
            if ($purchaseOrder) {
                $data['supplier_id'] = $purchaseOrder->supplier_id;
            }
        }

        // Create the receive
        $receive = PurchaseReceive::create($data);

        // Add items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->addItemsToReceive($receive, $data['items']);
        }

        // Log activity for goods receipt
        if ($receive->supplier_id) {
            \App\Models\SupplierActivityLog::log(
                supplierId: $receive->supplier_id,
                activityType: 'purchase_order_received',
                description: "Goods Receipt {$receive->receive_number} created for Purchase Order ".($receive->purchaseOrder->order_number ?? 'N/A'),
                relatedId: $receive->receive_id,
                relatedType: 'PurchaseReceive',
                amount: $receive->total_amount_received,
                metadata: [
                    'receive_number' => $receive->receive_number,
                    'purchase_order_id' => $receive->purchase_order_id,
                    'items_count' => count($data['items'] ?? []),
                    'status' => $receive->status,
                ]
            );
        }

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_CREATE,
            \App\Models\AuditLog::MODULE_PURCHASES,
            "Purchase Receive {$receive->receive_number} created with " . count($data['items'] ?? []) . " items",
            'PurchaseReceive',
            $receive->receive_id,
            $receive->receive_number,
            null,
            [
                'receive_number' => $receive->receive_number,
                'purchase_order_id' => $receive->purchase_order_id,
                'supplier_id' => $receive->supplier_id,
                'status' => $receive->status,
                'total_amount_received' => $receive->total_amount_received,
                'items_count' => count($data['items'] ?? []),
            ],
            \App\Models\AuditLog::SEVERITY_INFO
        );

        // Create notification for new purchase receive
        Notification::create([
            'title' => 'Goods Receipt Created',
            'message' => "Goods Receipt {$receive->receive_number} has been created successfully",
            'level' => 'success',
            'type' => 'purchases.receive_created',
            'link' => route('purchases.purchase-receives.show', $receive->receive_id),
        ]);

        return $receive->fresh(['supplier', 'purchaseOrder', 'items.product']);
    }

    /**
     * Update a purchase receive.
     */
    public function updateReceive(PurchaseReceive $receive, array $data): PurchaseReceive
    {
        // Capture old values
        $oldValues = [
            'status' => $receive->status,
            'total_amount_received' => $receive->total_amount_received,
        ];

        // Update receive details
        $receive->update($data);

        // Update items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->updateReceiveItems($receive, $data['items']);
        }

        // Capture new values
        $receive->refresh();
        $newValues = [
            'status' => $receive->status,
            'total_amount_received' => $receive->total_amount_received,
        ];

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_UPDATE,
            \App\Models\AuditLog::MODULE_PURCHASES,
            "Purchase Receive {$receive->receive_number} updated",
            'PurchaseReceive',
            $receive->receive_id,
            $receive->receive_number,
            $oldValues,
            $newValues,
            \App\Models\AuditLog::SEVERITY_INFO
        );

        // Create notification for updated purchase receive
        Notification::create([
            'title' => 'Goods Receipt Updated',
            'message' => "Goods Receipt {$receive->receive_number} has been updated",
            'level' => 'info',
            'type' => 'purchases.receive_updated',
            'link' => route('purchases.purchase-receives.show', $receive->receive_id),
        ]);

        return $receive->fresh(['supplier', 'purchaseOrder', 'items.product']);
    }

    /**
     * Add items to a receive.
     */
    public function addItemsToReceive(PurchaseReceive $receive, array $items): void
    {
        $totalQuantityExpected = 0;
        $totalQuantityReceived = 0;
        $totalAmountExpected = 0;
        $totalAmountReceived = 0;

        foreach ($items as $itemData) {
            $product = Product::find($itemData['product_id']);

            if (! $product) {
                continue;
            }

            $quantityExpected = $itemData['quantity_expected'] ?? 0;
            $quantityReceived = $itemData['quantity_received'] ?? 0;
            $quantityDamaged = $itemData['quantity_damaged'] ?? 0;
            $unitPrice = $itemData['unit_price'] ?? 0;
            $updateProductPrice = $itemData['update_product_price'] ?? false;

            // Auto-determine condition if not explicitly set or set to 'good'
            $condition = $itemData['condition'] ?? 'good';
            if ($condition === 'good') {
                // If quantities don't match, set condition to partial
                if ($quantityReceived < $quantityExpected) {
                    $condition = 'shortage';
                } elseif ($quantityReceived > $quantityExpected) {
                   $condition = 'excess';
                }
                else {
                    $condition = 'good';
                }
            }

            $item = PurchaseReceiveItem::create([
                'receive_id' => $receive->receive_id,
                'product_id' => $product->product_id,
                'purchase_order_item_id' => $itemData['purchase_order_item_id'] ?? null,
                'quantity_expected' => $quantityExpected,
                'quantity_received' => $quantityReceived,
                'quantity_damaged' => $quantityDamaged,
                'unit_price' => $unitPrice,
                'update_product_price' => $updateProductPrice,
                'total_amount' => $quantityReceived * $unitPrice,
                'condition' => $condition,
                'item_notes' => $itemData['item_notes'] ?? null,
            ]);

            // Update inventory for received items
            if ($quantityReceived > 0) {
                $this->updateProductInventory($product, $quantityReceived, $receive->supplier_id, $unitPrice);

                // Always update last_purchase_price and total_cost when products are successfully received
                $product->enableSupplierTrackingFields();
                $product->enableCostingFields();
                
                $updateData = [
                    'last_purchase_price' => $unitPrice,
                    'last_supplier_id' => $receive->supplier_id,
                    'last_received_at' => now(),
                    'total_cost' => $unitPrice, // Update total_cost with the purchase price for COGS calculation
                ];

                // Update product master price only if flag is set and receive is successful
                if ($updateProductPrice && $receive->status === 'received') {
                    $updateData['price'] = $unitPrice;
                }

                $product->update($updateData);

                // If linked to a PO item, sync received quantity there too
                if (! empty($itemData['purchase_order_item_id'])) {
                    $poItem = \App\Models\PurchaseOrderItem::find($itemData['purchase_order_item_id']);
                    if ($poItem) {
                        $before = $poItem->quantity_received;
                        $poItem->quantity_received = max(0, $before + $quantityReceived);
                        $poItem->save();
                    }
                }
            }

            // Accumulate totals
            $totalQuantityExpected += $quantityExpected;
            $totalQuantityReceived += $quantityReceived;
            $totalAmountExpected += ($quantityExpected * $unitPrice);
            $totalAmountReceived += ($quantityReceived * $unitPrice);
        }

        // Update receive totals
        $receive->update([
            'total_quantity_expected' => $totalQuantityExpected,
            'total_quantity_received' => $totalQuantityReceived,
            'total_amount_expected' => $totalAmountExpected,
            'total_amount_received' => $totalAmountReceived,
        ]);

        // Update status based on completion
        $this->updateReceiveStatus($receive);

        // Note: Purchase Order status is no longer updated here.
        // POs remain in 'ordered' status. Receiving is tracked separately via PurchaseReceive records.
    }

    /**
     * Update receive items.
     */
    public function updateReceiveItems(PurchaseReceive $receive, array $items): void
    {
        // Delete existing items (this also reverses inventory changes)
        $this->deleteReceiveItems($receive);

        // Add new items
        $this->addItemsToReceive($receive, $items);
    }

    /**
     * Delete receive items and reverse inventory changes.
     */
    protected function deleteReceiveItems(PurchaseReceive $receive): void
    {
        // First reverse inventory changes for existing items
        foreach ($receive->items as $item) {
            // Only reverse inventory if product still exists and quantity was received
            if ($item->quantity_received > 0 && $item->product) {
                $this->updateProductInventory($item->product, -$item->quantity_received);
            }
        }

        // Then delete the items
        $receive->items()->delete();
    }

    /**
     * Update product inventory.
     */
    protected function updateProductInventory(Product $product, int $quantityChange, ?int $supplierId = null, ?float $unitPrice = null): void
    {
        // Adjust Inventory (new source of truth) and record stock movement with inventory-based before/after
        \App\Services\InventoryService::adjust(
            productId: $product->product_id,
            quantityChange: $quantityChange,
            unitCost: $unitPrice,
            movementType: $quantityChange > 0 ? StockMovement::TYPE_PURCHASE : StockMovement::TYPE_RETURN,
            referenceType: 'purchase_receive',
            referenceId: null,
            propertyId: null,
            location: null,
            syncProductQuantity: true // keep Product.quantity in sync for backward compatibility
        );

        // Explicitly touch the product to update its updated_at timestamp
        // This ensures it appears at the top of the inventory list
        $product->touch();

        // Update supplier tracking information only when receiving items (positive quantity)
        if ($quantityChange > 0 && $supplierId && $unitPrice) {
            $product->enableSupplierTrackingFields();
            $product->update([
                'last_supplier_id' => $supplierId,
                'last_received_at' => now(),
                'last_purchase_price' => $unitPrice,
            ]);

            // Automatically update product costs from purchase prices
            // This ensures Cost of Goods Sold (COGS) reflects actual purchase costs
            try {
                $costingService = app(\App\Services\ProductCostingService::class);
                
                // If product doesn't have a cost method set, default to weighted_average
                if (empty($product->cost_calculation_method) || $product->cost_calculation_method === 'manual') {
                    $product->enableCostingFields();
                    $product->update(['cost_calculation_method' => 'weighted_average']);
                }
                
                // Always update cost from inventory for purchased products
                $costingService->updateCostFromInventory($product->fresh());
                
                // After updating cost, update selling price if using markup pricing
                $product = $product->fresh();
                if ($product->pricing_method === 'markup' && $product->markupPrice && $product->total_cost) {
                    $newPrice = $product->markupPrice->calculatePrice($product->total_cost);
                    $product->update(['price' => $newPrice]);
                    
                    Log::info('Auto-updated product price from markup', [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'total_cost' => $product->total_cost,
                        'new_price' => $newPrice,
                        'markup_percentage' => $product->markupPrice->markup_percentage
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to auto-update product cost', [
                    'product_id' => $product->product_id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Update receive status based on completion percentage.
     * Also updates the Purchase Order status to 'completed' when fully received.
     */
    protected function updateReceiveStatus(PurchaseReceive $receive): void
    {
        $completionPercentage = $receive->completion_percentage;

        if ($completionPercentage >= 100) {
            $receive->update(['status' => 'received']);
            
            // Mark Purchase Order as completed when fully received
            if ($receive->purchase_order_id) {
                $purchaseOrder = PurchaseOrder::find($receive->purchase_order_id);
                if ($purchaseOrder && $purchaseOrder->status === 'ordered') {
                    // Use DB::table to avoid enum quoting issue with Eloquent
                    DB::table('purchase_orders')
                        ->where('order_id', $purchaseOrder->order_id)
                        ->update([
                            'status' => 'completed',
                            'received_date' => $receive->receive_date ? $receive->receive_date->format('Y-m-d') : now()->format('Y-m-d'),
                            'updated_at' => now(),
                        ]);
                    
                    // Log activity
                    \App\Models\SupplierActivityLog::log(
                        supplierId: $purchaseOrder->supplier_id,
                        activityType: 'purchase_order_completed',
                        description: "Purchase Order {$purchaseOrder->order_number} marked as completed - all items received",
                        relatedId: $purchaseOrder->order_id,
                        relatedType: 'PurchaseOrder',
                        amount: $purchaseOrder->total_amount,
                        metadata: [
                            'order_number' => $purchaseOrder->order_number,
                            'receive_number' => $receive->receive_number,
                            'completed_via' => 'auto_complete_on_receive',
                            'received_date' => ($receive->receive_date instanceof \Carbon\Carbon) ? $receive->receive_date->format('Y-m-d') : $receive->receive_date,
                        ]
                    );
                }
            }
        } elseif ($completionPercentage > 0) {
            $receive->update(['status' => 'partially_received']);
        }
    }

    /**
     * Delete a purchase receive.
     *
     * This method reverses all changes made when the receive was created:
     * 1. Reverses inventory changes
     * 2. Reverses PO item quantity_received
     * 3. Updates PO status
     * 4. Does NOT auto-cancel deliveries (deliveries remain for future receives)
     */
    public function deleteReceive(PurchaseReceive $receive): bool
    {
        // Check if receive can be deleted
        if (! $receive->canBeCancelled()) {
            throw new \Exception('Cannot delete receive that is already fully processed.');
        }

        // Begin transaction to ensure all updates happen together
        DB::beginTransaction();

        try {
            // Step 1: Reverse PO item quantities before deleting receive items
            if ($receive->purchase_order_id) {
                foreach ($receive->items as $item) {
                    if ($item->purchase_order_item_id && $item->quantity_received > 0) {
                        $poItem = \App\Models\PurchaseOrderItem::find($item->purchase_order_item_id);
                        if ($poItem) {
                            // Subtract the received quantity
                            $poItem->quantity_received = max(0, $poItem->quantity_received - $item->quantity_received);
                            $poItem->save();
                        }
                    }
                }

                // Step 2: Update Purchase Order status
                $purchaseOrder = PurchaseOrder::with('items')->find($receive->purchase_order_id);
                if ($purchaseOrder) {
                    // Note: Purchase Order status is no longer updated during receive deletion
                    // POs remain in their current status. Receiving is tracked separately.
                    // Deliveries remain available for future receives.
                }
            }

            // Step 3: Reverse inventory changes and delete items
            $this->deleteReceiveItems($receive);

            // Step 4: Delete the receive record
            $result = $receive->delete();

            DB::commit();

            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to delete purchase receive: '.$e->getMessage());
        }
    }

    /**
     * Change receive status.
     */
    public function changeReceiveStatus(PurchaseReceive $receive, string $status): PurchaseReceive
    {
        $validTransitions = $this->getValidStatusTransitions($receive->status);

        if (! in_array($status, $validTransitions)) {
            throw new \Exception("Cannot change status from {$receive->status} to {$status}");
        }

        $oldStatus = $receive->status;
        $receive->update(['status' => $status]);

        // Create notification for status change
        $level = match ($status) {
            'received' => 'success',
            'damaged' => 'warning',
            'cancelled' => 'warning',
            default => 'info',
        };

        Notification::create([
            'title' => 'Goods Receipt Status Changed',
            'message' => "Goods Receipt {$receive->receive_number} status changed from {$oldStatus} to {$status}",
            'level' => $level,
            'type' => 'purchases.receive_status_changed',
            'link' => route('purchases.purchase-receives.show', $receive->receive_id),
        ]);

        return $receive->fresh();
    }

    /**
     * Get valid status transitions.
     */
    protected function getValidStatusTransitions(string $currentStatus): array
    {
        return match ($currentStatus) {
            'in_transit' => ['received', 'partially_received', 'damaged', 'cancelled'],
            'partially_received' => ['received', 'damaged', 'cancelled'],
            'received' => ['damaged'], // Only allow marking as damaged after received
            'damaged' => ['cancelled'],
            'cancelled' => [], // No transitions from cancelled
            default => [],
        };
    }

    /**
     * Get analytics data for receives.
     */
    public function getAnalytics(array $filters = []): array
    {
        $query = PurchaseReceive::query();

        // Apply date filters
        if (isset($filters['start_date'])) {
            $query->where('receive_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('receive_date', '<=', $filters['end_date']);
        }

        return [
            'total_receives' => $query->count(),
            'received_count' => $query->clone()->received()->count(),
            'in_transit_count' => $query->clone()->inTransit()->count(),
            'partially_received_count' => $query->clone()->partiallyReceived()->count(),
            'damaged_count' => $query->clone()->damaged()->count(),
            'cancelled_count' => $query->clone()->cancelled()->count(),
            'total_value_received' => $query->sum('total_amount_received'),
            'total_quantity_received' => $query->sum('total_quantity_received'),
        ];
    }

    /**
     * Short close a purchase receive.
     *
     * This allows closing a purchase order when the supplier cannot deliver
     * the full expected quantity. The receive is marked as complete with
     * the quantities that were actually received.
     *
     * WORKFLOW:
     * 1. Mark the receive as short closed with reason
     * 2. Mark all items with shortfall as short closed
     * 3. Update the related Purchase Order status to 'received' (closed)
     * 4. Update PO items to reflect that no more items are expected
     * 5. Log the activity
     *
     * @throws \Exception
     */
    public function shortCloseReceive(PurchaseReceive $receive, string $reason, ?int $userId = null): PurchaseReceive
    {
        // Validate that receive can be short closed
        if (! $receive->canBeShortClosed()) {
            throw new \Exception('This purchase receive cannot be short closed. It may already be fully received or already short closed.');
        }

        // Begin transaction to ensure all updates happen together
        DB::beginTransaction();

        try {
            // Mark the receive as short closed
            $receive->update([
                'is_short_closed' => true,
                'short_close_reason' => $reason,
                'short_closed_at' => now(),
                'short_closed_by' => $userId,
                'status' => 'received', // Mark as received since we're closing it
            ]);

            // Mark all items with shortfall as short closed
            foreach ($receive->items as $item) {
                if ($item->quantity_received < $item->quantity_expected) {
                    $item->update([
                        'is_short_closed' => true,
                        'short_close_reason' => $reason,
                    ]);
                }
            }

            // Update the related Purchase Order
            if ($receive->purchase_order_id) {
                $purchaseOrder = PurchaseOrder::with(['items'])->find($receive->purchase_order_id);

                if ($purchaseOrder) {
                    // Update PO status to completed and set received date when short closing
                    DB::table('purchase_orders')
                        ->where('order_id', $purchaseOrder->order_id)
                        ->update([
                            'status' => 'completed',
                            'received_date' => $receive->receive_date ? $receive->receive_date->format('Y-m-d') : now()->format('Y-m-d'),
                            'updated_at' => now(),
                        ]);

                    // Update PO items - mark them as fully received with the actual quantities
                    // This ensures no more receipts can be created for this PO
                    foreach ($purchaseOrder->items as $poItem) {
                        // Find the corresponding receive item
                        $receiveItem = $receive->items()
                            ->where('purchase_order_item_id', $poItem->item_id)
                            ->first();

                        if ($receiveItem) {
                            // Set quantity_ordered to match what was actually received
                            // This effectively closes the PO item
                            $poItem->update([
                                'quantity_ordered' => $poItem->quantity_received,
                            ]);
                        }
                    }
                    
                    // Log activity for purchase order completion via short close
                    \App\Models\SupplierActivityLog::log(
                        supplierId: $purchaseOrder->supplier_id,
                        activityType: 'purchase_order_completed',
                        description: "Purchase Order {$purchaseOrder->order_number} marked as completed via short close",
                        relatedId: $purchaseOrder->order_id,
                        relatedType: 'PurchaseOrder',
                        amount: $purchaseOrder->total_amount,
                        metadata: [
                            'order_number' => $purchaseOrder->order_number,
                            'receive_number' => $receive->receive_number,
                            'completed_via' => 'short_close',
                            'received_date' => $receive->receive_date ? $receive->receive_date->format('Y-m-d') : now()->format('Y-m-d'),
                            'short_close_reason' => $reason,
                        ]
                    );
                }
            }

            // Log activity for the receive
            if ($receive->supplier_id) {
                \App\Models\SupplierActivityLog::log(
                    supplierId: $receive->supplier_id,
                    activityType: 'purchase_order_short_closed',
                    description: "Purchase Receive {$receive->receive_number} was short closed. Reason: {$reason}",
                    relatedId: $receive->receive_id,
                    relatedType: 'PurchaseReceive',
                    amount: $receive->total_amount_received,
                    metadata: [
                        'receive_number' => $receive->receive_number,
                        'purchase_order_id' => $receive->purchase_order_id,
                        'expected_quantity' => $receive->total_quantity_expected,
                        'received_quantity' => $receive->total_quantity_received,
                        'shortfall' => $receive->total_quantity_expected - $receive->total_quantity_received,
                        'short_close_reason' => $reason,
                    ]
                );
            }

            // Create notification for short close
            Notification::create([
                'title' => 'Purchase Receive Short Closed',
                'message' => "Goods Receipt {$receive->receive_number} has been short closed. Reason: {$reason}",
                'level' => 'warning',
                'type' => 'purchases.receive_short_closed',
                'link' => route('purchases.purchase-receives.show', $receive->receive_id),
            ]);

            DB::commit();

            return $receive->fresh(['supplier', 'purchaseOrder', 'items.product']);

        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to short close purchase receive: '.$e->getMessage());
        }
    }
}
