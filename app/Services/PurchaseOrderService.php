<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PurchaseOrderService
{
    /**
     * Get paginated purchase orders with filters and search.
     */
    public function getPaginatedOrders(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = PurchaseOrder::with(['supplier', 'items']);

        // Apply search
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Apply filters
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }

        if ($paymentStatus = $request->get('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        // Date range filters
        if ($startDate = $request->get('start_date')) {
            $query->where('order_date', '>=', $startDate);
        }

        if ($endDate = $request->get('end_date')) {
            $query->where('order_date', '<=', $endDate);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortField, ['order_number', 'order_date', 'total_amount', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for purchase order listing.
     */
    public function getFilterOptions(): array
    {
        return [
            'suppliers' => Supplier::where('status', 'active')
                                 ->orderBy('name')
                                 ->get()
                                 ->map(function ($supplier) {
                                     return [
                                         'id' => $supplier->supplier_id,
                                         'name' => $supplier->name,
                                     ];
                                 }),
            'products' => Product::orderBy('product_name')
                               ->get()
                               ->map(function ($product) {
                                   return [
                                       'id' => $product->product_id,
                                       'product_name' => $product->product_name,
                                       'price' => $product->price,
                                       'stock' => $product->quantity,
                                   ];
                               }),
        ];
    }

    /**
     * Create a new purchase order.
     */
    public function createOrder(array $data): PurchaseOrder
    {
        // Set defaults
        $data['order_date'] = $data['order_date'] ?? Carbon::today();
        $data['status'] = $data['status'] ?? 'pending';
        $data['priority'] = $data['priority'] ?? 'normal';
        $data['payment_status'] = $data['payment_status'] ?? 'pending';

        // Create the order
        $order = PurchaseOrder::create($data);

        // Add items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->addItemsToOrder($order, $data['items']);
        }

        return $order->fresh(['supplier', 'items.product']);
    }

    /**
     * Update a purchase order.
     */
    public function updateOrder(PurchaseOrder $order, array $data): PurchaseOrder
    {
        // Update order details
        $order->update($data);

        // Update items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->updateOrderItems($order, $data['items']);
        }

        return $order->fresh(['supplier', 'items.product']);
    }

    /**
     * Add items to an order.
     */
    public function addItemsToOrder(PurchaseOrder $order, array $items): void
    {
        foreach ($items as $itemData) {
            $product = Product::find($itemData['product_id']);
            
            if (!$product) {
                continue;
            }

            // Use current product price if unit price not provided
            $unitPrice = $itemData['unit_price'] ?? $product->price;
            $quantity = $itemData['quantity_ordered'];
            $discountAmount = $itemData['discount_amount'] ?? 0;

            PurchaseOrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $product->product_id,
                'quantity_ordered' => $quantity,
                'quantity_received' => 0,
                'unit_price' => $unitPrice,
                'discount_amount' => $discountAmount,
                'line_total' => ($quantity * $unitPrice) - $discountAmount,
                'notes' => $itemData['notes'] ?? null,
            ]);
        }

        // Recalculate order totals
        $order->calculateTotals();
    }

    /**
     * Update order items.
     */
    public function updateOrderItems(PurchaseOrder $order, array $items): void
    {
        // Delete existing items
        $order->items()->delete();

        // Add new items
        $this->addItemsToOrder($order, $items);
    }

    /**
     * Delete a purchase order.
     */
    public function deleteOrder(PurchaseOrder $order): bool
    {
        // Check if order can be deleted
        if (!$order->canBeEdited()) {
            throw new \Exception('Cannot delete order that is already processed.');
        }

        // Delete order items first (cascade should handle this, but being explicit)
        $order->items()->delete();

        return $order->delete();
    }

    /**
     * Change order status.
     */
    public function changeOrderStatus(PurchaseOrder $order, string $status): PurchaseOrder
    {
        $validTransitions = $this->getValidStatusTransitions($order->status);
        
        if (!in_array($status, $validTransitions)) {
            throw new \Exception("Cannot change status from {$order->status} to {$status}");
        }

        $order->update(['status' => $status]);

        // Update received date when status changes to received
        if ($status === 'received' && !$order->received_date) {
            $order->update(['received_date' => Carbon::now()]);
        }

        return $order->fresh();
    }

    /**
     * Get valid status transitions for current status.
     */
    public function getValidStatusTransitions(string $currentStatus): array
    {
        return match($currentStatus) {
            'pending' => ['approved', 'cancelled'],
            'approved' => ['ordered', 'cancelled'],
            'ordered' => ['partial_received', 'received', 'cancelled'],
            'partial_received' => ['received', 'cancelled'],
            'received' => [],
            'cancelled' => [],
            default => [],
        };
    }

    /**
     * Receive items (update quantities and stock).
     */
    public function receiveItems(PurchaseOrder $order, array $receivedItems): PurchaseOrder
    {
        if (!$order->canReceiveItems()) {
            throw new \Exception('Cannot receive items for this order status.');
        }

        foreach ($receivedItems as $itemData) {
            $orderItem = $order->items()->where('product_id', $itemData['product_id'])->first();
            
            if (!$orderItem) {
                continue;
            }

            $receivedQty = min($itemData['quantity_received'], $orderItem->pending_quantity);
            
            if ($receivedQty > 0) {
                // Update received quantity
                $orderItem->quantity_received += $receivedQty;
                $orderItem->save();

                // Update product stock
                $product = $orderItem->product;
                $product->quantity += $receivedQty;
                $product->save();

                // Create stock movement record if the model exists
                if (class_exists('App\Models\StockMovement')) {
                    \App\Models\StockMovement::create([
                        'product_id' => $product->product_id,
                        'type' => 'in',
                        'quantity' => $receivedQty,
                        'reference_type' => 'purchase_order',
                        'reference_id' => $order->order_id,
                        'notes' => "Received from purchase order {$order->order_number}",
                        'date' => Carbon::now(),
                    ]);
                }
            }
        }

        // Check if order is fully received
        $allReceived = $order->items()->get()->every(function ($item) {
            return $item->isFullyReceived();
        });

        if ($allReceived) {
            $order->update(['status' => 'received', 'received_date' => Carbon::now()]);
        } else {
            $order->update(['status' => 'partial_received']);
        }

        return $order->fresh(['supplier', 'items.product']);
    }

    /**
     * Get purchase order analytics data.
     */
    public function getOrderAnalytics(): array
    {
        $totalOrders = PurchaseOrder::count();
        $pendingOrders = PurchaseOrder::pending()->count();
        $approvedOrders = PurchaseOrder::approved()->count();
        $orderedOrders = PurchaseOrder::ordered()->count();
        $partialReceivedOrders = PurchaseOrder::partialReceived()->count();
        $receivedOrders = PurchaseOrder::received()->count();
        $cancelledOrders = PurchaseOrder::cancelled()->count();

        // Recent orders (last 30 days)
        $recentOrders = PurchaseOrder::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Overdue orders
        $overdueOrders = PurchaseOrder::overdue();

        // Purchase analytics
        $totalPurchaseValue = PurchaseOrder::whereIn('status', ['received', 'partial_received'])->sum('total_amount');
        $monthlyPurchaseValue = PurchaseOrder::whereIn('status', ['received', 'partial_received'])
                                          ->thisMonth()
                                          ->sum('total_amount');
        $todayPurchaseValue = PurchaseOrder::whereIn('status', ['received', 'partial_received'])
                                         ->today()
                                         ->sum('total_amount');

        // Order trend (last 12 months)
        $orderTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = PurchaseOrder::whereYear('created_at', $date->year)
                              ->whereMonth('created_at', $date->month)
                              ->count();
            $value = PurchaseOrder::whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month)
                                ->whereIn('status', ['received', 'partial_received'])
                                ->sum('total_amount');
            
            $orderTrend->push([
                'month' => $date->format('M Y'),
                'orders' => $count,
                'value' => $value
            ]);
        }

        // Top suppliers by order value
        $topSuppliers = PurchaseOrder::with(['supplier'])
                                 ->selectRaw('supplier_id, COUNT(*) as order_count, SUM(total_amount) as total_spent')
                                 ->whereIn('status', ['received', 'partial_received'])
                                 ->groupBy('supplier_id')
                                 ->orderByDesc('total_spent')
                                 ->limit(10)
                                 ->get();

        // Average order value
        $avgOrderValue = PurchaseOrder::whereIn('status', ['received', 'partial_received'])
                                  ->avg('total_amount') ?? 0;

        return [
            'summary' => [
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'approved_orders' => $approvedOrders,
                'ordered_orders' => $orderedOrders,
                'partial_received_orders' => $partialReceivedOrders,
                'received_orders' => $receivedOrders,
                'cancelled_orders' => $cancelledOrders,
                'recent_orders' => $recentOrders,
                'overdue_orders' => $overdueOrders->count(),
                'total_purchase_value' => $totalPurchaseValue,
                'monthly_purchase_value' => $monthlyPurchaseValue,
                'today_purchase_value' => $todayPurchaseValue,
                'avg_order_value' => round($avgOrderValue, 2),
            ],
            'order_trend' => $orderTrend,
            'top_suppliers' => $topSuppliers,
            'overdue_orders' => $overdueOrders,
        ];
    }

    /**
     * Get order history for a supplier.
     */
    public function getSupplierOrderHistory(Supplier $supplier, int $perPage = 15): LengthAwarePaginator
    {
        return $supplier->purchaseOrders()
                       ->with(['items.product'])
                       ->latest('created_at')
                       ->paginate($perPage);
    }

    /**
     * Get orders requiring attention.
     */
    public function getOrdersRequiringAttention(): Collection
    {
        return PurchaseOrder::requiresAttention()
                        ->with(['supplier'])
                        ->get();
    }

    /**
     * Calculate order summary.
     */
    public function calculateOrderSummary(array $items, float $taxRate = 0, float $shippingAmount = 0, float $discountAmount = 0): array
    {
        $subtotal = 0;
        
        foreach ($items as $item) {
            $lineTotal = ($item['quantity_ordered'] * $item['unit_price']) - ($item['discount_amount'] ?? 0);
            $subtotal += $lineTotal;
        }

        $taxAmount = $subtotal * ($taxRate / 100);
        $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'shipping_amount' => round($shippingAmount, 2),
            'discount_amount' => round($discountAmount, 2),
            'total_amount' => round($totalAmount, 2),
        ];
    }
}
