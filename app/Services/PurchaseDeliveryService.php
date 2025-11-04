<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseDelivery;
use App\Models\PurchaseDeliveryItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PurchaseDeliveryService
{
    /**
     * Get paginated purchase deliveries with filters and search.
     */
    public function getPaginatedDeliveries(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = PurchaseDelivery::with(['supplier', 'purchaseOrder', 'items.product']);

        // Apply search
        if ($search = $request->get('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%")
                    ->orWhere('carrier', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
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
            $query->where('status', $status);
        }

        if ($carrier = $request->get('carrier')) {
            $query->where('carrier', $carrier);
        }

        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }

        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        if ($purchaseOrderId = $request->get('purchase_order_id')) {
            $query->where('purchase_order_id', $purchaseOrderId);
        }

        // Date range filters
        if ($startDate = $request->get('start_date')) {
            $query->where('delivery_date', '>=', $startDate);
        }

        if ($endDate = $request->get('end_date')) {
            $query->where('delivery_date', '<=', $endDate);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'delivery_date');
        $sortOrder = $request->get('order', 'desc');

        if (in_array($sortField, ['delivery_number', 'delivery_date', 'scheduled_delivery_date', 'actual_delivery_date', 'status', 'carrier', 'created_at'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for purchase delivery listing.
     * 
     * WORKFLOW NOTE: Deliveries should only be created for orders that are approved/ordered
     * but NOT yet received. Once goods are received, no new deliveries should be created.
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
            // Only show orders that haven't been fully received yet
            'purchase_orders' => PurchaseOrder::with('supplier')
                ->whereIn('status', ['approved', 'ordered', 'partial_received'])
                ->orderBy('order_number')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->order_id,
                        'order_number' => $order->order_number,
                        'supplier_name' => $order->supplier->supplier_name ?? $order->supplier->name,
                        'supplier_id' => $order->supplier_id,
                    ];
                }),
            'carriers' => $this->getCarriers(),
        ];
    }

    /**
     * Get list of carriers.
     */
    public function getCarriers(): array
    {
        return [
            // ['id' => 'FedEx', 'name' => 'FedEx'],
            // ['id' => 'UPS', 'name' => 'UPS'],
            // ['id' => 'DHL', 'name' => 'DHL'],
            // ['id' => 'USPS', 'name' => 'USPS'],
            ['id' => 'Local Courier', 'name' => 'Local Courier'],
            ['id' => 'Supplier Direct', 'name' => 'Supplier Direct'],
            ['id' => 'Other', 'name' => 'Other'],
        ];
    }

    /**
     * Create a new purchase delivery.
     * 
     * WORKFLOW:
     * 1. Created after PO is approved/ordered
     * 2. Tracks shipment from supplier to warehouse (carrier, tracking, etc.)
     * 3. Does NOT update inventory (that happens in Purchase Receive)
     * 4. When delivered, a Purchase Receive should be created to update inventory
     * 5. Links to PO and auto-fills supplier info
     */
    public function createDelivery(array $data): PurchaseDelivery
    {
        return DB::transaction(function () use ($data) {
            // Set defaults
            $data['delivery_date'] = $data['delivery_date'] ?? Carbon::today();
            $data['status'] = $data['status'] ?? 'scheduled';
            $data['priority'] = $data['priority'] ?? 'normal';

            // Get purchase order to auto-fill supplier and delivery address
            if (isset($data['purchase_order_id'])) {
                $purchaseOrder = PurchaseOrder::find($data['purchase_order_id']);
                if ($purchaseOrder) {
                    $data['supplier_id'] = $purchaseOrder->supplier_id;
                    if (empty($data['delivery_address']) && isset($purchaseOrder->delivery_address)) {
                        $data['delivery_address'] = $purchaseOrder->delivery_address;
                    }
                }
            }

            // Create the delivery
            $delivery = PurchaseDelivery::create($data);

            // Add items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                $this->addItemsToDelivery($delivery, $data['items']);
            }

            return $delivery->fresh(['supplier', 'purchaseOrder', 'items.product']);
        });
    }

    /**
     * Update a purchase delivery.
     */
    public function updateDelivery(PurchaseDelivery $delivery, array $data): PurchaseDelivery
    {
        return DB::transaction(function () use ($delivery, $data) {
            // Update delivery details
            $delivery->update($data);

            // Update items if provided
            if (isset($data['items']) && is_array($data['items'])) {
                $this->updateDeliveryItems($delivery, $data['items']);
            }

            return $delivery->fresh(['supplier', 'purchaseOrder', 'items.product']);
        });
    }

    /**
     * Add items to a delivery.
     */
    public function addItemsToDelivery(PurchaseDelivery $delivery, array $items): void
    {
        $totalQuantityExpected = 0;
        $totalQuantityDelivered = 0;
        $totalQuantityDamaged = 0;
        $totalAmountExpected = 0;
        $totalAmountDelivered = 0;

        foreach ($items as $itemData) {
            $item = new PurchaseDeliveryItem($itemData);
            $item->delivery_id = $delivery->delivery_id;
            $item->save();

            $totalQuantityExpected += $item->quantity_expected;
            $totalQuantityDelivered += $item->quantity_delivered;
            $totalQuantityDamaged += $item->quantity_damaged ?? 0;
            $totalAmountExpected += $item->quantity_expected * $item->unit_price;
            $totalAmountDelivered += $item->line_total;
        }

        // Update delivery totals
        $delivery->update([
            'total_quantity_expected' => $totalQuantityExpected,
            'total_quantity_delivered' => $totalQuantityDelivered,
            'total_quantity_damaged' => $totalQuantityDamaged,
            'total_amount_expected' => $totalAmountExpected,
            'total_amount_delivered' => $totalAmountDelivered,
        ]);
    }

    /**
     * Update items for a delivery.
     */
    public function updateDeliveryItems(PurchaseDelivery $delivery, array $items): void
    {
        // Delete existing items
        $delivery->items()->delete();

        // Add new items
        $this->addItemsToDelivery($delivery, $items);
    }

    /**
     * Delete a delivery.
     */
    public function deleteDelivery(PurchaseDelivery $delivery): bool
    {
        return DB::transaction(function () use ($delivery) {
            // Delete items first
            $delivery->items()->delete();

            // Delete the delivery
            return $delivery->delete();
        });
    }

    /**
     * Change delivery status.
     */
    public function changeStatus(PurchaseDelivery $delivery, string $newStatus, ?string $notes = null): PurchaseDelivery
    {
        $oldStatus = $delivery->status;

        $delivery->update([
            'status' => $newStatus,
        ]);

        // Handle status-specific actions
        if ($newStatus === 'delivered' && !$delivery->actual_delivery_date) {
            $delivery->update([
                'actual_delivery_date' => Carbon::today(),
                'delivered_at' => now(),
            ]);
        }

        if ($newStatus === 'in_transit' && !$delivery->picked_up_at) {
            $delivery->update([
                'picked_up_at' => now(),
            ]);
        }

        // Add to tracking history
        $delivery->addTrackingUpdate([
            'status' => $newStatus,
            'previous_status' => $oldStatus,
            'notes' => $notes,
            'timestamp' => now(),
        ]);

        return $delivery->fresh();
    }

    /**
     * Get purchase order items for a delivery.
     */
    public function getPurchaseOrderItems(int $purchaseOrderId): array
    {
        $purchaseOrder = PurchaseOrder::with('items.product')->findOrFail($purchaseOrderId);

        return $purchaseOrder->items->map(function ($item) {
            $product = $item->product;
            $quantityOrdered = $item->quantity_ordered ?? 0;
            $quantityReceived = $item->quantity_received ?? 0;
            
            return [
                'item_id' => $item->item_id,
                'product_id' => $item->product_id,
                'product_name' => $product ? $product->product_name : 'Unknown Product',
                'sku' => $product ? ($product->sku ?? 'N/A') : 'N/A',
                'quantity_ordered' => $quantityOrdered,
                'quantity_received' => $quantityReceived,
                'quantity_remaining' => $quantityOrdered - $quantityReceived,
                'unit_price' => $item->unit_price,
            ];
        })->toArray();
    }

    /**
     * Get delivery statistics.
     */
    public function getStatistics(?string $startDate = null, ?string $endDate = null): array
    {
        $query = PurchaseDelivery::query();

        if ($startDate && $endDate) {
            $query->whereBetween('delivery_date', [$startDate, $endDate]);
        }

        return [
            'total_deliveries' => $query->count(),
            'scheduled' => (clone $query)->where('status', 'scheduled')->count(),
            'in_transit' => (clone $query)->where('status', 'in_transit')->count(),
            'out_for_delivery' => (clone $query)->where('status', 'out_for_delivery')->count(),
            'delivered' => (clone $query)->where('status', 'delivered')->count(),
            'delayed' => (clone $query)->where('status', 'delayed')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'total_amount' => (clone $query)->sum('total_amount_delivered'),
            'total_shipping_cost' => (clone $query)->sum('total_shipping_cost'),
            'overdue_count' => PurchaseDelivery::overdue()->count(),
        ];
    }

    /**
     * Get delivery trends by carrier.
     */
    public function getCarrierPerformance(): array
    {
        return PurchaseDelivery::select('carrier', DB::raw('COUNT(*) as total'), DB::raw('AVG(total_shipping_cost) as avg_cost'))
            ->groupBy('carrier')
            ->orderBy('total', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Update tracking information.
     */
    public function updateTracking(PurchaseDelivery $delivery, string $trackingNumber, ?string $carrier = null, ?array $trackingUpdate = null): PurchaseDelivery
    {
        $updateData = [
            'tracking_number' => $trackingNumber,
        ];

        if ($carrier) {
            $updateData['carrier'] = $carrier;
        }

        $delivery->update($updateData);

        if ($trackingUpdate) {
            $delivery->addTrackingUpdate($trackingUpdate);
        }

        return $delivery->fresh();
    }
}
