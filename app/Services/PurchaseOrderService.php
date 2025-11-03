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

/**
 * Purchase Order Service
 * 
 * PURCHASE WORKFLOW OVERVIEW:
 * ============================
 * 1. CREATE PURCHASE ORDER (this service)
 *    - Status: pending → approved → ordered
 *    - Select supplier and products
 *    - No inventory changes yet
 * 
 * 2. CREATE DELIVERY (optional - PurchaseDeliveryService)
 *    - Track shipment from supplier
 *    - Carrier, tracking number, estimated delivery
 *    - Status updates: scheduled → in_transit → delivered
 *    - Still no inventory changes
 * 
 * 3. CREATE RECEIVE (PurchaseReceiveService)
 *    - Record actual receipt of goods
 *    - Can link to delivery if tracking was used
 *    - Updates inventory via StockMovement
 *    - Updates PO status: partial_received → received
 * 
 * 4. CREATE PAYMENT (PurchasePaymentService)
 *    - Record payment to supplier
 *    - Can be partial or full
 *    - Updates PO payment_status
 */
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
                                 ->orderBy('supplier_name')
                                 ->get()
                                 ->map(function ($supplier) {
                                     return [
                                         'id' => $supplier->supplier_id,
                                         'name' => $supplier->supplier_name,
                                     ];
                                 }),
            'products' => Product::orderBy('product_name')
                               ->get()
                               ->map(function ($product) {
                                   // Use quantity from product
                                   $stock = $product->quantity;
                                   
                                   return [
                                       'id' => $product->product_id,
                                       'product_name' => $product->product_name,
                                       // Expose SKU and descriptive fields so UIs can always show synced info
                                       'sku' => $product->sku,
                                       'product_brand' => $product->product_brand,
                                       'product_category' => $product->product_category,
                                       'price' => $product->price,
                                       'stock' => $stock,
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

        // Log activity
        \App\Models\SupplierActivityLog::log(
            supplierId: $order->supplier_id,
            activityType: 'purchase_order_created',
            description: "Purchase Order {$order->order_number} created with " . count($data['items'] ?? []) . " items",
            relatedId: $order->order_id,
            relatedType: 'PurchaseOrder',
            amount: $order->total_amount,
            metadata: [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'priority' => $order->priority,
                'items_count' => count($data['items'] ?? []),
            ]
        );

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

        $oldStatus = $order->status;
        $order->update(['status' => $status]);

        // Update received date when status changes to received
        if ($status === 'received' && !$order->received_date) {
            $order->update(['received_date' => Carbon::now()]);
        }

        // Log activity based on status change
        $activityType = match($status) {
            'approved' => 'purchase_order_approved',
            'received' => 'purchase_order_received',
            'cancelled' => 'purchase_order_cancelled',
            default => 'purchase_order_updated',
        };

        \App\Models\SupplierActivityLog::log(
            supplierId: $order->supplier_id,
            activityType: $activityType,
            description: "Purchase Order {$order->order_number} status changed from {$oldStatus} to {$status}",
            relatedId: $order->order_id,
            relatedType: 'PurchaseOrder',
            amount: $order->total_amount,
            metadata: [
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $status,
            ]
        );

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
     * Receive items (DEPRECATED - Use PurchaseReceiveService instead).
     * 
     * This method is deprecated and should not be used.
     * All receiving operations must go through the Goods Receipt (Purchase Receive) module.
     * 
     * @deprecated Use App\Services\PurchaseReceiveService::createReceive() instead
     * @throws \Exception Always throws exception to prevent direct receiving
     */
    public function receiveItems(PurchaseOrder $order, array $receivedItems, string $receivingNotes = null): PurchaseOrder
    {
        throw new \Exception(
            'Direct receiving through Purchase Orders is disabled. ' .
            'Please use the Goods Receipt module to receive items. ' .
            'Navigate to Purchase Receives or click "Create Goods Receipt" from the Purchase Order page.'
        );
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
    
    /**
     * Get receiving report data.
     */
    public function getReceivingReport(array $filters = []): array
    {
        $query = PurchaseOrder::with(['supplier', 'items.product'])
                             ->whereIn('status', ['partial_received', 'received']);
        
        // Apply date filters if provided
        if (!empty($filters['start_date'])) {
            $query->whereDate('received_date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('received_date', '<=', $filters['end_date']);
        }
        
        // Apply supplier filter if provided
        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }
        
        $orders = $query->latest('received_date')->get();
        
        $summary = [
            'total_orders_received' => $orders->count(),
            'fully_received_orders' => $orders->where('status', 'received')->count(),
            'partially_received_orders' => $orders->where('status', 'partial_received')->count(),
            'total_value_received' => 0,
            'total_items_received' => 0,
            'unique_products_received' => collect(),
        ];
        
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                if ($item->quantity_received > 0) {
                    $receivedValue = $item->quantity_received * $item->unit_price;
                    $summary['total_value_received'] += $receivedValue;
                    $summary['total_items_received'] += $item->quantity_received;
                    $summary['unique_products_received']->put($item->product_id, $item->product->product_name);
                }
            }
        }
        
        $summary['unique_products_count'] = $summary['unique_products_received']->count();
        unset($summary['unique_products_received']);
        
        return [
            'orders' => $orders,
            'summary' => $summary,
            'filters' => $filters
        ];
    }
    
    /**
     * Get receiving statistics for dashboard.
     */
    public function getReceivingStats(): array
    {
        $today = Carbon::today();
        $thisWeek = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
        $thisMonth = [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        
        return [
            'today' => [
                'orders_received' => PurchaseOrder::whereDate('received_date', $today)->count(),
                'items_received' => PurchaseOrder::whereDate('received_date', $today)
                    ->with('items')
                    ->get()
                    ->sum(function($order) { return $order->items->sum('quantity_received'); }),
                'value_received' => PurchaseOrder::whereDate('received_date', $today)
                    ->with('items')
                    ->get()
                    ->sum(function($order) { 
                        return $order->items->sum(function($item) {
                            return $item->quantity_received * $item->unit_price;
                        });
                    }),
            ],
            'this_week' => [
                'orders_received' => PurchaseOrder::whereBetween('received_date', $thisWeek)->count(),
                'items_received' => PurchaseOrder::whereBetween('received_date', $thisWeek)
                    ->with('items')
                    ->get()
                    ->sum(function($order) { return $order->items->sum('quantity_received'); }),
            ],
            'this_month' => [
                'orders_received' => PurchaseOrder::whereBetween('received_date', $thisMonth)->count(),
                'items_received' => PurchaseOrder::whereBetween('received_date', $thisMonth)
                    ->with('items')
                    ->get()
                    ->sum(function($order) { return $order->items->sum('quantity_received'); }),
            ],
            'pending_orders' => PurchaseOrder::whereIn('status', ['ordered', 'partial_received'])->count(),
            'overdue_orders' => PurchaseOrder::overdue()->count(),
        ];
    }
}
