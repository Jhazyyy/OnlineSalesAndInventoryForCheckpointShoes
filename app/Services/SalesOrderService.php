<?php

namespace App\Services;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\InventoryService;
use App\Models\Notification;
use App\Mail\OrderStatusChanged;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * SalesOrderService
 * 
 * This service handles sales orders - can receive orders from e-commerce or create manually.
 * Provides full CRUD operations for sales order management.
 * 
 * Available operations:
 * - Create orders (createOrder)
 * - View orders (getPaginatedOrders)
 * - Update orders (updateOrder)
 * - Delete orders (deleteOrder)
 * - Filter and search orders
 * - Get order analytics
 * - Track shipment status
 * - Manage order status changes
 */
class SalesOrderService
{
    /**
     * Get paginated sales orders with filters and search.
     */
    public function getPaginatedOrders(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = SalesOrder::with(['customer', 'items']);

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

        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // Purchase type filter (in_store vs online)
        if ($purchaseType = $request->get('purchase_type')) {
            $query->where('purchase_type', $purchaseType);
        }

        // Date range filters
        if ($startDate = $request->get('start_date')) {
            $query->where('order_date', '>=', $startDate);
        }

        if ($endDate = $request->get('end_date')) {
            $query->where('order_date', '<=', $endDate);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'updated_at');
        $sortOrder = $request->get('order', 'desc');

        if (in_array($sortField, ['order_number', 'order_date', 'total_amount', 'status', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for sales order listing.
     */
    public function getFilterOptions(): array
    {
        return [
            'customers' => Customer::active()
                                 ->orderBy('first_name')
                                 ->get()
                                 ->map(function ($customer) {
                                     return [
                                         'id' => $customer->customer_id,
                                         'name' => $customer->display_name,
                                     ];
                                 }),
            'products' => Product::with('markupPrice')
                               ->orderBy('product_name')
                               ->get()
                               ->map(function ($product) {
                                   // Use quantity from product
                                   $stock = $product->quantity;
                                   
                                   // Use the product's calculated selling price which includes markup if applied
                                   $sellingPrice = $product->calculateSellingPrice();
                                   
                                   // For display purposes, also calculate what the markup price would be
                                   $markupPrice = null;
                                   $markupPercentage = null;
                                   if ($product->pricing_method === 'markup' && $product->markupPrice) {
                                       // Use total_cost if available, otherwise use current price as base
                                       $baseCost = $product->total_cost && $product->total_cost > 0 
                                           ? $product->total_cost 
                                           : $product->price;
                                       $markupPrice = $baseCost * (1 + ($product->markupPrice->markup_percentage / 100));
                                       $markupPercentage = $product->markupPrice->markup_percentage;
                                   }
                                   
                                   return [
                                       'id' => $product->product_id,
                                       'name' => $product->product_name . ' - ' . $product->product_brand,
                                       'price' => $sellingPrice, // Use calculated selling price
                                       'markup_price' => $markupPrice,
                                       'markup_percentage' => $markupPercentage,
                                       'pricing_method' => $product->pricing_method,
                                       'markup_price_id' => $product->markup_price_id,
                                       'stock' => $stock,
                                       'category' => $product->product_category,
                                       'brand' => $product->product_brand,
                                       'sku' => $product->sku,
                                       'image' => $product->image ? $product->image_url : null,
                                   ];
                               }),
        ];
    }

    /**
     * Create a new sales order.
     */
    public function createOrder(array $data): SalesOrder
    {
        // Set defaults - use Carbon::now() to capture current date AND time
        $data['order_date'] = $data['order_date'] ?? Carbon::now();
        $data['status'] = $data['status'] ?? 'pending';
        $data['priority'] = $data['priority'] ?? 'normal';
        $data['payment_status'] = $data['payment_status'] ?? 'pending';
        $data['purchase_type'] = $data['purchase_type'] ?? 'in_store';

        // Auto-adjust status based on payment status and purchase type
        // For in-store purchases: if paid, customer receives product immediately
        if ($data['purchase_type'] === 'in_store' && $data['payment_status'] === 'paid') {
            // Set status to delivered since customer gets the product immediately
            $data['status'] = 'delivered';
            // Set shipped_date to now since it's instant
            $data['shipped_date'] = $data['shipped_date'] ?? Carbon::now();
        } elseif ($data['purchase_type'] === 'in_store' && $data['payment_status'] !== 'paid') {
            // For in-store but not yet paid, keep as pending or confirmed
            if (!isset($data['status']) || $data['status'] === 'pending') {
                $data['status'] = 'pending';
            }
        }
        // For online purchases, follow normal flow (status set manually or defaults to pending)

        // Create the order
        $order = SalesOrder::create($data);

        // Add items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->addItemsToOrder($order, $data['items']);
        }

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_CREATE,
            \App\Models\AuditLog::MODULE_SALES,
            "Sales Order {$order->order_number} created with " . count($data['items'] ?? []) . " items for total amount of " . number_format((float)$order->total_amount, 2),
            'SalesOrder',
            $order->order_id,
            $order->order_number,
            null,
            [
                'order_number' => $order->order_number,
                'customer_id' => $order->customer_id,
                'status' => $order->status,
                'priority' => $order->priority,
                'purchase_type' => $order->purchase_type,
                'total_amount' => $order->total_amount,
                'items_count' => count($data['items'] ?? []),
            ],
            \App\Models\AuditLog::SEVERITY_INFO
        );

        // Create notification for new order
        Notification::create([
            'title' => 'New Sales Order Created',
            'message' => "Order {$order->order_number} has been created successfully",
            'level' => 'info',
            'type' => 'sales.order_created',
            'link' => route('sales.orders.show', $order->order_id),
        ]);

        return $order->fresh(['customer', 'items.product']);
    }

    /**
     * Update a sales order.
     */
    public function updateOrder(SalesOrder $order, array $data): SalesOrder
    {
        // Capture old values
        $oldValues = [
            'customer_id' => $order->customer_id,
            'status' => $order->status,
            'priority' => $order->priority,
            'payment_status' => $order->payment_status,
            'total_amount' => $order->total_amount,
        ];

        // Update order details
        $order->update($data);

        // Update items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->updateOrderItems($order, $data['items']);
        }

        // Capture new values
        $order->refresh();
        $newValues = [
            'customer_id' => $order->customer_id,
            'status' => $order->status,
            'priority' => $order->priority,
            'payment_status' => $order->payment_status,
            'total_amount' => $order->total_amount,
        ];

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_UPDATE,
            \App\Models\AuditLog::MODULE_SALES,
            "Sales Order {$order->order_number} updated",
            'SalesOrder',
            $order->order_id,
            $order->order_number,
            $oldValues,
            $newValues,
            \App\Models\AuditLog::SEVERITY_INFO
        );

        // Create notification for updated sales order
        Notification::create([
            'title' => 'Sales Order Updated',
            'message' => "Sales Order {$order->order_number} has been updated",
            'level' => 'info',
            'type' => 'sales.order_updated',
            'link' => route('sales.orders.show', $order->order_id),
        ]);

        return $order->fresh(['customer', 'items.product']);
    }

    /**
     * Add items to an order.
     */
    public function addItemsToOrder(SalesOrder $order, array $items): void
    {
        foreach ($items as $itemData) {
            $product = Product::find($itemData['product_id']);
            
            if (!$product) {
                continue;
            }

            // Use product price if unit price not provided
            $unitPrice = $itemData['unit_price'] ?? $product->price;
            $quantity = $itemData['quantity'];
            $discountAmount = $itemData['discount_amount'] ?? 0;

            // Capture the unit cost at the time of sale (COGS from purchase orders)
            // This ensures we calculate accurate profit even if purchase costs change later
            $unitCostAtSale = $product->total_cost ?? 0;

            $item = SalesOrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_cost_at_sale' => $unitCostAtSale,
                'discount_amount' => $discountAmount,
                'line_total' => ($quantity * $unitPrice) - $discountAmount,
                'notes' => $itemData['notes'] ?? null,
            ]);

            // If this order should immediately affect stock (e.g., in-store paid or delivered), adjust inventory
            try {
                $shouldDeduct = false;

                // Deduct when payment_status is paid (common for POS in-store immediate sales)
                if (isset($order->payment_status) && $order->payment_status === 'paid') {
                    $shouldDeduct = true;
                }

                // Also deduct when order status indicates delivered/processing/confirmed/shipped
                if (in_array($order->status, ['delivered', 'processing', 'confirmed', 'shipped'])) {
                    $shouldDeduct = true;
                }

                    if ($shouldDeduct) {
                    // Use InventoryService to adjust inventories and record stock movement properly
                    InventoryService::adjust(
                        productId: $product->product_id,
                        quantityChange: -1 * (int) $quantity,
                        unitCost: null,
                        movementType: StockMovement::TYPE_SALE,
                        referenceType: 'sales_order',
                        referenceId: $order->order_id,
                        propertyId: null,
                        location: null,
                        syncProductQuantity: true
                    );

                    // Note: Threshold checks are automatically handled by ProductObserver when product quantity is updated

                    Log::info('Inventory adjusted for sale', ['product_id' => $product->product_id, 'quantity' => $quantity, 'order_id' => $order->order_id]);
                }
            } catch (\Exception $e) {
                // Log but continue; inventory sync should not prevent order creation
                Log::error('Failed to adjust inventory for order item', ['error' => $e->getMessage(), 'product_id' => $product->product_id, 'order_id' => $order->order_id]);
            }
        }

        // Recalculate order totals
        // For POS (in_store), don't apply auto tax/discount - user manually selected them (or chose none)
        // For online orders, apply auto calculation
        $applyAutoTaxDiscount = $order->purchase_type !== 'in_store';
        
        Log::info('Before calculateTotals:', [
            'order_id' => $order->order_id,
            'purchase_type' => $order->purchase_type,
            'applyAutoTaxDiscount' => $applyAutoTaxDiscount,
            'tax_amount_before' => $order->tax_amount,
            'discount_amount_before' => $order->discount_amount,
        ]);
        
        $order->calculateTotals($applyAutoTaxDiscount);
        
        Log::info('After calculateTotals:', [
            'order_id' => $order->order_id,
            'tax_amount_after' => $order->tax_amount,
            'discount_amount_after' => $order->discount_amount,
        ]);
    }

    /**
     * Update order items.
     */
    public function updateOrderItems(SalesOrder $order, array $items): void
    {
        // Delete existing items
        $order->items()->delete();

        // Add new items
        $this->addItemsToOrder($order, $items);
    }

    /**
     * Delete a sales order.
     */
    public function deleteOrder(SalesOrder $order): bool
    {
        // Check if order can be deleted
        if (!$order->canBeEdited()) {
            throw new \Exception('Cannot delete order that is already processed.');
        }

        // Capture order details before deletion
        $orderNumber = $order->order_number;
        $orderId = $order->order_id;
        $orderData = [
            'order_number' => $order->order_number,
            'customer_id' => $order->customer_id,
            'status' => $order->status,
            'total_amount' => $order->total_amount,
            'items_count' => $order->items()->count(),
        ];

        // Delete order items first (cascade should handle this, but being explicit)
        $order->items()->delete();

        $deleted = $order->delete();

        // Log to audit trail
        if ($deleted) {
            \App\Models\AuditLog::logAction(
                \App\Models\AuditLog::ACTION_DELETE,
                \App\Models\AuditLog::MODULE_SALES,
                "Sales Order {$orderNumber} deleted",
                'SalesOrder',
                $orderId,
                $orderNumber,
                $orderData,
                null,
                \App\Models\AuditLog::SEVERITY_WARNING
            );
        }

        return $deleted;
    }

    /**
     * Change order status.
     */
    public function changeOrderStatus(SalesOrder $order, string $status): SalesOrder
    {
        $validTransitions = $this->getValidStatusTransitions($order->status);
        
        if (!in_array($status, $validTransitions)) {
            throw new \Exception("Cannot change status from {$order->status} to {$status}");
        }

        $oldStatus = $order->status;
        $order->update(['status' => $status]);

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_UPDATE,
            \App\Models\AuditLog::MODULE_SALES,
            "Sales Order {$order->order_number} status changed from {$oldStatus} to {$status}",
            'SalesOrder',
            $order->order_id,
            $order->order_number,
            ['status' => $oldStatus],
            ['status' => $status],
            \App\Models\AuditLog::SEVERITY_INFO
        );

        // If order is cancelled or returned, restore inventory for items that were previously deducted
        if (in_array($status, ['cancelled', 'returned'])) {
            try {
                DB::beginTransaction();
                foreach ($order->items as $item) {
                    $product = $item->product;
                    if (!$product) continue;

                    InventoryService::adjust(
                        productId: $product->product_id,
                        quantityChange: (int) $item->quantity,
                        unitCost: null,
                        movementType: StockMovement::TYPE_RETURN,
                        referenceType: 'sales_order',
                        referenceId: $order->order_id,
                        propertyId: null,
                        location: null,
                        syncProductQuantity: true
                    );
                }
                DB::commit();
                Log::info('Inventory restored for cancelled/returned order', ['order_id' => $order->order_id]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to restore inventory on order cancel/return', ['order_id' => $order->order_id, 'error' => $e->getMessage()]);
            }
        }

        // Update shipped date when status changes to shipped
        if ($status === 'shipped' && !$order->shipped_date) {
            $order->update(['shipped_date' => Carbon::now()]);
        }

        // Create notifications for status changes
        $this->createOrderStatusNotification($order, $oldStatus, $status);

        return $order->fresh();
    }

    /**
     * Create notification when order status changes.
     */
    protected function createOrderStatusNotification(SalesOrder $order, string $oldStatus, string $newStatus): void
    {
        $notificationData = [
            'type' => 'sales.order_status_changed',
            'link' => route('sales.orders.show', $order->order_id),
        ];

        // Determine notification title, message, and level based on new status
        switch ($newStatus) {
            case 'confirmed':
                $notificationData['title'] = 'Order Confirmed';
                $notificationData['message'] = "Order {$order->order_number} has been confirmed successfully";
                $notificationData['level'] = 'success';
                break;

            case 'delivered':
                $notificationData['title'] = 'Order Delivered Successfully';
                $notificationData['message'] = "Order {$order->order_number} has been delivered to {$order->customer->display_name}";
                $notificationData['level'] = 'success';
                break;

            case 'cancelled':
                $notificationData['title'] = 'Order Cancelled';
                $notificationData['message'] = "Order {$order->order_number} has been cancelled";
                $notificationData['level'] = 'warning';
                break;

            case 'failed':
                $notificationData['title'] = 'Order Failed';
                $notificationData['message'] = "Order {$order->order_number} has failed";
                $notificationData['level'] = 'danger';
                break;

            case 'processing':
                $notificationData['title'] = 'Order Processing';
                $notificationData['message'] = "Order {$order->order_number} is now being processed";
                $notificationData['level'] = 'info';
                break;

            case 'shipped':
                $notificationData['title'] = 'Order Shipped';
                $notificationData['message'] = "Order {$order->order_number} has been shipped";
                $notificationData['level'] = 'info';
                break;

            default:
                return; // Don't create notification for other status changes
        }

        // Create system notification
        Notification::create($notificationData);

        // Send email notification if customer has email
        if ($order->customer && $order->customer->email) {
            try {
                Mail::to($order->customer->email)->send(
                    new OrderStatusChanged($order, $oldStatus, $newStatus)
                );
            } catch (\Exception $e) {
                Log::error('Failed to send order status email: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get valid status transitions for current status.
     */
    
    public function getValidStatusTransitions(string $currentStatus): array
    {
        return match($currentStatus) {
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered', 'returned'],
            'delivered' => ['returned'],
            'cancelled' => [],
            'returned' => [],
            default => [],
        };
    }

    /**
     * Get sales order analytics data.
     */
    public function getOrderAnalytics(): array
    {
        $totalOrders = SalesOrder::count();
        $pendingOrders = SalesOrder::pending()->count();
        $confirmedOrders = SalesOrder::confirmed()->count();
        $processingOrders = SalesOrder::processing()->count();
        $shippedOrders = SalesOrder::shipped()->count();
        $deliveredOrders = SalesOrder::delivered()->count();
        $cancelledOrders = SalesOrder::cancelled()->count();

        // Recent orders (last 30 days)
        $recentOrders = SalesOrder::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Overdue orders
        $overdueOrders = SalesOrder::overdue();

        // Revenue analytics
        $totalRevenue = SalesOrder::whereIn('status', ['delivered', 'shipped'])->sum('total_amount');
        $monthlyRevenue = SalesOrder::whereIn('status', ['delivered', 'shipped'])
                                   ->thisMonth()
                                   ->sum('total_amount');
        $todayRevenue = SalesOrder::whereIn('status', ['delivered', 'shipped'])
                                 ->today()
                                 ->sum('total_amount');

        // Order trend (last 12 months)
        $orderTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = SalesOrder::whereYear('created_at', $date->year)
                              ->whereMonth('created_at', $date->month)
                              ->count();
            $revenue = SalesOrder::whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month)
                                ->whereIn('status', ['delivered', 'shipped'])
                                ->sum('total_amount');
            
            $orderTrend->push([
                'month' => $date->format('M Y'),
                'orders' => $count,
                'revenue' => $revenue
            ]);
        }

        // Top customers by order value
        $topCustomers = SalesOrder::with(['customer'])
                                 ->selectRaw('customer_id, COUNT(*) as order_count, SUM(total_amount) as total_spent')
                                 ->whereIn('status', ['delivered', 'shipped'])
                                 ->groupBy('customer_id')
                                 ->orderByDesc('total_spent')
                                 ->limit(10)
                                 ->get();

        // Average order value
        $avgOrderValue = SalesOrder::whereIn('status', ['delivered', 'shipped'])
                                  ->avg('total_amount') ?? 0;

        return [
            'summary' => [
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'confirmed_orders' => $confirmedOrders,
                'processing_orders' => $processingOrders,
                'shipped_orders' => $shippedOrders,
                'delivered_orders' => $deliveredOrders,
                'cancelled_orders' => $cancelledOrders,
                'recent_orders' => $recentOrders,
                'overdue_orders' => $overdueOrders->count(),
                'total_revenue' => $totalRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'today_revenue' => $todayRevenue,
                'avg_order_value' => round($avgOrderValue, 2),
            ],
            'order_trend' => $orderTrend,
            'top_customers' => $topCustomers,
            'overdue_orders' => $overdueOrders,
        ];
    }

    /**
     * Get order history for a customer.
     */
    public function getCustomerOrderHistory(Customer $customer, int $perPage = 15): LengthAwarePaginator
    {
        return $customer->salesOrders()
                       ->with(['items.product'])
                       ->latest('created_at')
                       ->paginate($perPage);
    }

    /**
     * Check stock availability for order items.
     */
    public function checkStockAvailability(array $items): array
    {
        $stockIssues = [];
        
        // If negative stock is allowed globally, skip stock checks
        if (isNegativeStockAllowed()) {
            return $stockIssues;
        }

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            
            if (!$product) {
                $stockIssues[] = [
                    'product_id' => $item['product_id'],
                    'issue' => 'Product not found',
                ];
                continue;
            }

            $requestedQty = $item['quantity'];
            $availableQty = $product->quantity;

            if ($requestedQty > $availableQty) {
                $stockIssues[] = [
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'requested' => $requestedQty,
                    'available' => $availableQty,
                    'shortage' => $requestedQty - $availableQty,
                    'issue' => 'Insufficient stock',
                ];
            }
        }

        return $stockIssues;
    }

    /**
     * Get orders requiring attention.
     */
    public function getOrdersRequiringAttention(): Collection
    {
        return SalesOrder::requiresAttention()
                        ->with(['customer'])
                        ->get();
    }

    /**
     * Process order fulfillment (reduce inventory).
     */
    public function processOrderFulfillment(SalesOrder $order): bool
    {
        if ($order->status !== 'confirmed') {
            throw new \Exception('Only confirmed orders can be processed for fulfillment.');
        }

        // Check stock availability for all items
        $stockIssues = [];
        foreach ($order->items as $item) {
            if (!$item->hasSufficientStock()) {
                $stockIssues[] = $item;
            }
        }

        if (!empty($stockIssues)) {
            throw new \Exception('Insufficient stock for some items in this order.');
        }

        // Reduce inventory using InventoryService to ensure proper sync
        foreach ($order->items as $item) {
            $product = $item->product;

            // Use InventoryService to adjust inventory and record stock movement properly
            InventoryService::adjust(
                productId: $product->product_id,
                quantityChange: -$item->quantity,
                unitCost: null,
                movementType: \App\Models\StockMovement::TYPE_SALE,
                referenceType: 'sales_order',
                referenceId: $order->order_id,
                propertyId: null,
                location: null,
                syncProductQuantity: true
            );

            Log::info('Inventory adjusted for order fulfillment', [
                'product_id' => $product->product_id,
                'quantity' => $item->quantity,
                'order_id' => $order->order_id
            ]);
        }

        // Update order status
        $order->update(['status' => 'processing']);

        return true;
    }

    /**
     * Calculate order summary.
     */
    public function calculateOrderSummary(array $items, float $taxRate = 0, float $shippingAmount = 0, float $discountAmount = 0): array
    {
        $subtotal = 0;
        
        foreach ($items as $item) {
            $lineTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount_amount'] ?? 0);
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
