<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\SalesOrder;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShipmentService
{
    /**
     * Get paginated shipments with filters.
     */
    public function getPaginatedShipments(Request $request): LengthAwarePaginator
    {
        $query = Shipment::with(['salesOrder.customer', 'items']);

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Apply status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Apply priority filter
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Apply carrier filter
        if ($request->has('carrier') && $request->carrier) {
            $query->where('carrier', $request->carrier);
        }

        // Apply date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('shipment_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('shipment_date', '<=', $request->end_date);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        $allowedSortFields = ['shipment_number', 'shipment_date', 'expected_delivery_date', 'status', 'priority', 'carrier', 'total_shipping_cost', 'created_at'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->latest('created_at');
        }

        return $query->paginate(20)->withQueryString();
    }

    /**
     * Get filter options for the index page.
     */
    public function getFilterOptions(): array
    {
        return [
            'carriers' => Shipment::whereNotNull('carrier')
                                 ->distinct()
                                 ->pluck('carrier')
                                 ->filter()
                                 ->sort()
                                 ->values(),
            'salesOrders' => SalesOrder::with('customer')
                                     ->whereIn('status', ['confirmed', 'processing', 'shipped'])
                                     ->get(),
            'products' => Product::select('product_id', 'product_name', 'product_brand')
                                ->orderBy('product_name')
                                ->get(),
        ];
    }

    /**
     * Create a new shipment.
     */
    public function createShipment(array $data): Shipment
    {
        return DB::transaction(function () use ($data) {
            // Create the shipment
            $shipment = Shipment::create([
                'sales_order_id' => $data['sales_order_id'],
                'carrier' => $data['carrier'] ?? null,
                'service_type' => $data['service_type'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'priority' => $data['priority'] ?? 'normal',
                'shipment_date' => $data['shipment_date'] ?? now(),
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'billing_address' => $data['billing_address'] ?? null,
                'return_address' => $data['return_address'] ?? null,
                'recipient_name' => $data['recipient_name'],
                'recipient_phone' => $data['recipient_phone'] ?? null,
                'recipient_email' => $data['recipient_email'] ?? null,
                'total_packages' => $data['total_packages'] ?? 1,
                'total_weight' => $data['total_weight'] ?? null,
                'package_dimensions' => $data['package_dimensions'] ?? null,
                'shipping_cost' => $data['shipping_cost'] ?? 0.00,
                'insurance_cost' => $data['insurance_cost'] ?? 0.00,
                'additional_fees' => $data['additional_fees'] ?? 0.00,
                'is_insured' => $data['is_insured'] ?? false,
                'insurance_value' => $data['insurance_value'] ?? 0.00,
                'requires_signature' => $data['requires_signature'] ?? false,
                'is_fragile' => $data['is_fragile'] ?? false,
                'is_perishable' => $data['is_perishable'] ?? false,
                'special_instructions' => $data['special_instructions'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? null,
                'created_by' => auth()->user()?->name ?? 'System',
            ]);

            // Create shipment items
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    $product = Product::find($itemData['product_id']);
                    
                    if (!$product) {
                        throw new \Exception("Product with ID {$itemData['product_id']} not found.");
                    }
                    
                    ShipmentItem::create([
                        'shipment_id' => $shipment->shipment_id,
                        'sales_order_item_id' => $itemData['sales_order_item_id'] ?? null,
                        'product_id' => $itemData['product_id'],
                        'product_sku' => $product->product_brand ?? 'N/A',
                        'product_name' => $product->product_name ?? 'Unknown Product',
                        'quantity_shipped' => $itemData['quantity_shipped'],
                        'unit_price' => $itemData['unit_price'] ?? ($product->price ?? 0.00),
                        'package_number' => $itemData['package_number'] ?? '1',
                        'item_weight' => $itemData['item_weight'] ?? null,
                        'condition' => $itemData['condition'] ?? 'new',
                        'status' => 'pending',
                        'serial_numbers' => $itemData['serial_numbers'] ?? null,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            return $shipment->load(['salesOrder.customer', 'items.product']);
        });
    }

    /**
     * Update an existing shipment.
     */
    public function updateShipment(Shipment $shipment, array $data): Shipment
    {
        return DB::transaction(function () use ($shipment, $data) {
            // Update shipment
            $shipment->update([
                'carrier' => $data['carrier'] ?? $shipment->carrier,
                'service_type' => $data['service_type'] ?? $shipment->service_type,
                'tracking_number' => $data['tracking_number'] ?? $shipment->tracking_number,
                'reference_number' => $data['reference_number'] ?? $shipment->reference_number,
                'status' => $data['status'] ?? $shipment->status,
                'priority' => $data['priority'] ?? $shipment->priority,
                'shipment_date' => $data['shipment_date'] ?? $shipment->shipment_date,
                'expected_delivery_date' => $data['expected_delivery_date'] ?? $shipment->expected_delivery_date,
                'shipping_address' => $data['shipping_address'] ?? $shipment->shipping_address,
                'billing_address' => $data['billing_address'] ?? $shipment->billing_address,
                'return_address' => $data['return_address'] ?? $shipment->return_address,
                'recipient_name' => $data['recipient_name'] ?? $shipment->recipient_name,
                'recipient_phone' => $data['recipient_phone'] ?? $shipment->recipient_phone,
                'recipient_email' => $data['recipient_email'] ?? $shipment->recipient_email,
                'total_packages' => $data['total_packages'] ?? $shipment->total_packages,
                'total_weight' => $data['total_weight'] ?? $shipment->total_weight,
                'package_dimensions' => $data['package_dimensions'] ?? $shipment->package_dimensions,
                'shipping_cost' => $data['shipping_cost'] ?? $shipment->shipping_cost,
                'insurance_cost' => $data['insurance_cost'] ?? $shipment->insurance_cost,
                'additional_fees' => $data['additional_fees'] ?? $shipment->additional_fees,
                'is_insured' => $data['is_insured'] ?? $shipment->is_insured,
                'insurance_value' => $data['insurance_value'] ?? $shipment->insurance_value,
                'requires_signature' => $data['requires_signature'] ?? $shipment->requires_signature,
                'is_fragile' => $data['is_fragile'] ?? $shipment->is_fragile,
                'is_perishable' => $data['is_perishable'] ?? $shipment->is_perishable,
                'special_instructions' => $data['special_instructions'] ?? $shipment->special_instructions,
                'internal_notes' => $data['internal_notes'] ?? $shipment->internal_notes,
                'updated_by' => auth()->user()?->name ?? 'System',
            ]);

            // Update items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                // Delete existing items
                $shipment->items()->delete();

                // Create new items
                foreach ($data['items'] as $itemData) {
                    $product = Product::find($itemData['product_id']);
                    
                    if (!$product) {
                        throw new \Exception("Product with ID {$itemData['product_id']} not found.");
                    }
                    
                    ShipmentItem::create([
                        'shipment_id' => $shipment->shipment_id,
                        'sales_order_item_id' => $itemData['sales_order_item_id'] ?? null,
                        'product_id' => $itemData['product_id'],
                        'product_sku' => $product->product_brand ?? 'N/A',
                        'product_name' => $product->product_name ?? 'Unknown Product',
                        'quantity_shipped' => $itemData['quantity_shipped'],
                        'unit_price' => $itemData['unit_price'] ?? ($product->price ?? 0.00),
                        'package_number' => $itemData['package_number'] ?? '1',
                        'item_weight' => $itemData['item_weight'] ?? null,
                        'condition' => $itemData['condition'] ?? 'new',
                        'status' => $itemData['status'] ?? 'pending',
                        'serial_numbers' => $itemData['serial_numbers'] ?? null,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            return $shipment->load(['salesOrder.customer', 'items.product']);
        });
    }

    /**
     * Delete a shipment.
     */
    public function deleteShipment(Shipment $shipment): void
    {
        if (!$shipment->canBeCancelled()) {
            throw new \Exception('Cannot delete shipment that has already been delivered or is in transit.');
        }

        DB::transaction(function () use ($shipment) {
            // Delete items first (cascade should handle this, but explicit is better)
            $shipment->items()->delete();
            
            // Delete the shipment
            $shipment->delete();
        });
    }

    /**
     * Change shipment status.
     */
    public function changeShipmentStatus(Shipment $shipment, string $status): Shipment
    {
        $allowedTransitions = $this->getAllowedStatusTransitions($shipment->status);
        
        if (!in_array($status, $allowedTransitions)) {
            throw new \Exception("Cannot change status from {$shipment->status} to {$status}");
        }

        $shipment->update([
            'status' => $status,
            'updated_by' => auth()->user()?->name ?? 'System',
        ]);

        // Add tracking history entry
        $shipment->addTrackingUpdate([
            'status' => $status,
            'description' => "Status changed to " . ucfirst($status),
            'location' => null,
            'updated_by' => auth()->user()?->name ?? 'System',
        ]);

        // Update timestamps for specific statuses
        switch ($status) {
            case 'shipped':
                if (!$shipment->picked_up_at) {
                    $shipment->update(['picked_up_at' => now()]);
                }
                break;
            case 'delivered':
                $shipment->update([
                    'delivered_at' => now(),
                    'actual_delivery_date' => now()->toDateString(),
                ]);
                break;
        }

        return $shipment->refresh();
    }

    /**
     * Get allowed status transitions.
     */
    private function getAllowedStatusTransitions(string $currentStatus): array
    {
        return match ($currentStatus) {
            'pending' => ['preparing', 'cancelled'],
            'preparing' => ['shipped', 'cancelled'],
            'shipped' => ['in_transit', 'exception', 'cancelled'],
            'in_transit' => ['out_for_delivery', 'delivered', 'exception', 'returned'],
            'out_for_delivery' => ['delivered', 'exception', 'returned'],
            'delivered' => ['returned'],
            'exception' => ['in_transit', 'returned', 'cancelled'],
            'returned' => [],
            'cancelled' => [],
            default => [],
        };
    }

    /**
     * Process shipment for delivery.
     */
    public function processShipmentForDelivery(Shipment $shipment): Shipment
    {
        if (!$shipment->canBeShipped()) {
            throw new \Exception('Shipment cannot be processed for delivery at this time.');
        }

        return DB::transaction(function () use ($shipment) {
            // Update shipment status
            $shipment->update([
                'status' => 'shipped',
                'shipment_date' => $shipment->shipment_date ?? now()->toDateString(),
                'picked_up_at' => now(),
                'updated_by' => auth()->user()?->name ?? 'System',
            ]);

            // Update all items to shipped status
            $shipment->items()->update(['status' => 'shipped']);

            // Add tracking history
            $shipment->addTrackingUpdate([
                'status' => 'shipped',
                'description' => 'Package has been shipped and is on its way',
                'location' => null,
                'updated_by' => auth()->user()?->name ?? 'System',
            ]);

            return $shipment->refresh();
        });
    }

    /**
     * Add tracking update to shipment.
     */
    public function addTrackingUpdate(Shipment $shipment, array $update): Shipment
    {
        $shipment->addTrackingUpdate($update);
        return $shipment->refresh();
    }

    /**
     * Get shipment analytics.
     */
    public function getShipmentAnalytics(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'total_shipments' => Shipment::count(),
            'pending_shipments' => Shipment::pending()->count(),
            'in_transit_shipments' => Shipment::inTransit()->count(),
            'delivered_today' => Shipment::delivered()->whereDate('delivered_at', $today)->count(),
            'delivered_this_month' => Shipment::delivered()->whereDate('delivered_at', '>=', $thisMonth)->count(),
            'overdue_shipments' => Shipment::overdue()->count(),
            'exception_shipments' => Shipment::exception()->count(),
            'total_shipping_cost' => Shipment::sum('total_shipping_cost'),
            'average_shipping_cost' => Shipment::avg('total_shipping_cost'),
            'carriers_used' => Shipment::whereNotNull('carrier')
                                    ->distinct()
                                    ->count('carrier'),
            'recent_shipments' => Shipment::recent(5),
            'status_distribution' => Shipment::select('status', DB::raw('count(*) as count'))
                                           ->groupBy('status')
                                           ->get()
                                           ->pluck('count', 'status'),
            'carrier_distribution' => Shipment::whereNotNull('carrier')
                                            ->select('carrier', DB::raw('count(*) as count'))
                                            ->groupBy('carrier')
                                            ->get()
                                            ->pluck('count', 'carrier'),
        ];
    }

    /**
     * Get shipments requiring attention.
     */
    public function getShipmentsRequiringAttention(): array
    {
        return [
            'overdue' => Shipment::overdue()->with(['salesOrder.customer'])->get(),
            'exceptions' => Shipment::exception()->with(['salesOrder.customer'])->get(),
            'pending_quality_check' => ShipmentItem::pendingQualityCheck()
                                                  ->with(['shipment.salesOrder.customer', 'product'])
                                                  ->get(),
        ];
    }

    /**
     * Create shipment from sales order.
     */
    public function createShipmentFromSalesOrder(SalesOrder $salesOrder, array $additionalData = []): Shipment
    {
        if (!in_array($salesOrder->status, ['confirmed', 'processing'])) {
            throw new \Exception('Sales order must be confirmed or processing to create shipment.');
        }

        $customer = $salesOrder->customer;
        
        $shipmentData = array_merge([
            'sales_order_id' => $salesOrder->order_id,
            'shipping_address' => $salesOrder->shipping_address ?? $customer->full_address,
            'billing_address' => $salesOrder->billing_address ?? $customer->full_address,
            'recipient_name' => $customer->display_name,
            'recipient_phone' => $customer->phone,
            'recipient_email' => $customer->email,
            'priority' => $salesOrder->priority ?? 'normal',
            'items' => $salesOrder->items->map(function ($item) {
                return [
                    'sales_order_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity_shipped' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ];
            })->toArray(),
        ], $additionalData);

        return $this->createShipment($shipmentData);
    }

    /**
     * Check inventory availability for shipment items.
     */
    public function checkInventoryAvailability(array $items): array
    {
        $issues = [];
        
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                $issues[] = "Product with ID {$item['product_id']} not found";
                continue;
            }

            if ($product->quantity < $item['quantity_shipped']) {
                $issues[] = "Insufficient stock for {$product->name}. Available: {$product->quantity}, Required: {$item['quantity_shipped']}";
            }
        }

        return $issues;
    }

    /**
     * Process inventory updates for shipped items.
     */
    public function processInventoryUpdates(Shipment $shipment): void
    {
        if ($shipment->status !== 'shipped') {
            throw new \Exception('Can only process inventory updates for shipped items.');
        }

        DB::transaction(function () use ($shipment) {
            foreach ($shipment->items as $item) {
                $product = $item->product;
                if ($product && $product->quantity >= $item->quantity_shipped) {
                    $product->decrement('quantity', $item->quantity_shipped);
                    
                    // Record stock movement
                    \App\Models\StockMovement::create([
                        'product_id' => $product->product_id,
                        'type' => 'outbound',
                        'quantity' => $item->quantity_shipped,
                        'reference_type' => 'shipment',
                        'reference_id' => $shipment->shipment_id,
                        'notes' => "Shipped in shipment {$shipment->shipment_number}",
                        'performed_by' => auth()->user()?->name ?? 'System',
                    ]);
                }
            }
        });
    }
}
