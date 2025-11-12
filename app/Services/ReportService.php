<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\Returns;
use App\Models\PurchaseReturn;
use App\Models\StockMovement;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Generate blocked items report (refurbished, damaged, wasted)
     *
     * @param array $filters
     * @return array
     */
    public function generateBlockedItemsReport(array $filters = []): array
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date']) : now()->subDays(30);
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date']) : now();

        // Aggregate refurbished and damaged from shipment items
        $refurbished = \App\Models\ShipmentItem::query()
            ->where('condition', 'refurbished')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('product_id', DB::raw('SUM(quantity_shipped) as refurbished_qty'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $damagedShipped = \App\Models\ShipmentItem::query()
            ->where(function ($q) {
                $q->where('condition', 'damaged')->orWhere('status', 'damaged');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('product_id', DB::raw('SUM(quantity_shipped) as damaged_shipped_qty'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Damaged received during purchases
        $inboundDamaged = \App\Models\PurchaseReceiveItem::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('product_id', DB::raw('SUM(quantity_damaged) as inbound_damaged_qty'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Waste/damaged adjustments from stock movements
        $waste = StockMovement::query()
            ->confirmed()
            ->ofType(StockMovement::TYPE_WASTE)
            ->whereBetween('movement_date', [$startDate, $endDate])
            ->select('product_id', DB::raw('SUM(ABS(quantity_change)) as waste_qty'), DB::raw('SUM(total_value) as waste_value'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // Union of all product IDs
        $productIds = collect([$refurbished->keys(), $damagedShipped->keys(), $inboundDamaged->keys(), $waste->keys()])
            ->flatten()
            ->unique()
            ->values();

        $products = Product::whereIn('product_id', $productIds)->get()->keyBy('product_id');

        $rows = [];
        $totals = [
            'total_refurbished_qty' => 0,
            'total_damaged_shipped_qty' => 0,
            'total_inbound_damaged_qty' => 0,
            'total_waste_qty' => 0,
            'total_waste_value' => 0.0,
        ];

        foreach ($productIds as $pid) {
            $p = $products->get($pid);
            $refQty = (int)($refurbished[$pid]->refurbished_qty ?? 0);
            $dmgShipQty = (int)($damagedShipped[$pid]->damaged_shipped_qty ?? 0);
            $dmgInboundQty = (int)($inboundDamaged[$pid]->inbound_damaged_qty ?? 0);
            $wasteQty = (int)($waste[$pid]->waste_qty ?? 0);
            $wasteValue = (float)($waste[$pid]->waste_value ?? 0);

            $rows[] = [
                'product_id' => $pid,
                'product_name' => $p->product_name ?? 'Unknown',
                'product_brand' => $p->product_brand ?? null,
                'product_category' => $p->product_category ?? null,
                'refurbished_qty' => $refQty,
                'damaged_shipped_qty' => $dmgShipQty,
                'inbound_damaged_qty' => $dmgInboundQty,
                'waste_qty' => $wasteQty,
                'waste_value' => round($wasteValue, 2),
            ];

            $totals['total_refurbished_qty'] += $refQty;
            $totals['total_damaged_shipped_qty'] += $dmgShipQty;
            $totals['total_inbound_damaged_qty'] += $dmgInboundQty;
            $totals['total_waste_qty'] += $wasteQty;
            $totals['total_waste_value'] += $wasteValue;
        }

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_refurbished_qty' => $totals['total_refurbished_qty'],
                'total_damaged_shipped_qty' => $totals['total_damaged_shipped_qty'],
                'total_inbound_damaged_qty' => $totals['total_inbound_damaged_qty'],
                'total_waste_qty' => $totals['total_waste_qty'],
                'total_waste_value' => round($totals['total_waste_value'], 2),
                'total_blocked_qty' => $totals['total_refurbished_qty'] + $totals['total_damaged_shipped_qty'] + $totals['total_inbound_damaged_qty'] + $totals['total_waste_qty'],
            ],
            'products' => collect($rows)->sortByDesc(function ($r) {
                return ($r['refurbished_qty'] + $r['damaged_shipped_qty'] + $r['inbound_damaged_qty'] + $r['waste_qty']);
            })->values(),
            'purchase_order_master' => $this->getPurchaseOrderMaster($startDate, $endDate),
        ];
    }

    /**
     * Get purchase order master data (aggregated purchase orders by product)
     */
    protected function getPurchaseOrderMaster(Carbon $startDate, Carbon $endDate)
    {
        // Get database driver to handle CONCAT differently for SQLite
        $driver = DB::connection()->getDriverName();
        $concatSql = $driver === 'sqlite' 
            ? "products.product_brand || '-' || products.product_category"
            : "CONCAT(products.product_brand, '-', products.product_category)";
        
        return DB::table('purchase_order_items')
            ->join('purchase_orders', 'purchase_order_items.order_id', '=', 'purchase_orders.order_id')
            ->join('products', 'purchase_order_items.product_id', '=', 'products.product_id')
            ->leftJoin('inventories', function($join) {
                $join->on('products.product_id', '=', 'inventories.product_id')
                     ->whereNull('inventories.property_id');
            })
            ->whereBetween('purchase_orders.order_date', [$startDate, $endDate])
            ->whereIn('purchase_orders.status', ['approved', 'ordered', 'partial_received', 'received'])
            ->select(
                'products.product_id',
                'products.product_name',
                'products.product_brand',
                'products.product_category',
                DB::raw("COALESCE(inventories.sku, products.sku, {$concatSql}) as product_sku"),
                DB::raw('SUM(purchase_order_items.quantity_ordered) as total_quantity'),
                DB::raw('SUM(purchase_order_items.quantity_ordered * purchase_order_items.unit_price) as total_cost'),
                DB::raw('COALESCE((SELECT SUM(inventories.quantity_on_hand) FROM inventories WHERE inventories.product_id = products.product_id), 0) as instock_qty')
            )
            ->groupBy('products.product_id', 'products.product_name', 'products.product_brand', 'products.product_category', 'inventories.sku', 'products.sku')
            ->orderByDesc('total_cost')
            ->get();
    }

    /**
     * Generate reorder report (products needing reorder based on thresholds)
     *
     * @param array $filters
     * @return array
     */
    public function generateReorderReport(array $filters = []): array
    {
        $query = Product::query();

        // Search by product name/brand/category
        if (!empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                  ->orWhere('product_brand', 'LIKE', "%{$search}%")
                  ->orWhere('product_category', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category if provided
        if (!empty($filters['category'])) {
            $query->where('product_category', $filters['category']);
        }

        // Only include products that have a reorder level defined and are at/below it
        $query->whereNotNull('reorder_level')
              ->whereColumn('quantity', '<=', 'reorder_level');

        $products = $query->with(['preferredSupplier'])->orderBy('product_name')->get();

        $totalCandidates = $products->count();
        $outOfStock = $products->where('quantity', '<=', 0)->count();
        $critical = $products->filter(function ($p) {
            return !is_null($p->critical_level) && $p->quantity <= $p->critical_level;
        })->count();

        // Decorate with suggested order quantity for convenience in views
        $products = $products->map(function ($p) {
            $p->suggested_order_qty = $p->getSuggestedOrderQuantity();
            return $p;
        });

        return [
            'summary' => [
                'total_products' => $totalCandidates,
                'out_of_stock' => $outOfStock,
                'critical' => $critical,
            ],
            'filters' => $filters,
            'products' => $products,
        ];
    }

    /**
     * Generate critical items report (products at or below critical level)
     *
     * @param array $filters
     * @return array
     */
    public function generateCriticalItemsReport(array $filters = []): array
    {
        $query = Product::query();

        // Search by product name/brand/category
        if (!empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                  ->orWhere('product_brand', 'LIKE', "%{$search}%")
                  ->orWhere('product_category', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category if provided
        if (!empty($filters['category'])) {
            $query->where('product_category', $filters['category']);
        }

        // Only include products that have a critical level defined and are at/below it
        $query->whereNotNull('critical_level')
              ->whereColumn('quantity', '<=', 'critical_level');

        $products = $query->with(['preferredSupplier', 'purchases'])
            ->orderBy('quantity', 'asc')
            ->orderBy('product_name')
            ->get();

        // Calculate additional metrics for each product
        $products = $products->map(function ($p) {
            // Calculate shortage (difference from reorder level)
            $p->shortage = ($p->reorder_level ?? $p->critical_level) - $p->quantity;
            
            // Get suggested order quantity
            $p->suggested_order_qty = $p->getSuggestedOrderQuantity();
            
            // Get last purchase date
            $lastPurchase = $p->purchases()
                ->orderBy('purchase_date', 'desc')
                ->first();
            $p->last_purchase_date = $lastPurchase ? $lastPurchase->purchase_date : null;
            
            // Calculate average daily sales (last 30 days)
            $salesData = $p->sales()
                ->where('date', '>=', now()->subDays(30))
                ->selectRaw('SUM(sales.quantity) as total_sold')
                ->first();
            
            $totalSold = $salesData->total_sold ?? 0;
            $p->avg_daily_sales = $totalSold / 30;
            $p->avg_monthly_sales = $totalSold;
            
            // Determine priority level
            if ($p->quantity <= 0) {
                $p->priority_level = 'urgent';
                $p->priority_label = '🔴 Urgent';
            } elseif ($p->quantity <= ($p->critical_level * 0.5)) {
                $p->priority_level = 'critical';
                $p->priority_label = '🟠 Critical';
            } else {
                $p->priority_level = 'warning';
                $p->priority_label = '🟡 Warning';
            }
            
            // Suggested action
            if ($p->quantity <= 0) {
                $p->action_needed = 'Create PO immediately - Out of stock';
            } elseif ($p->avg_daily_sales > 0 && ($p->quantity / $p->avg_daily_sales) < 7) {
                $p->action_needed = 'Urgent reorder - Less than 7 days stock';
            } else {
                $p->action_needed = 'Schedule reorder soon';
            }
            
            return $p;
        });

        $totalItems = $products->count();
        $urgentItems = $products->where('priority_level', 'urgent')->count();
        $criticalItems = $products->where('priority_level', 'critical')->count();

        return [
            'summary' => [
                'total_items' => $totalItems,
                'urgent' => $urgentItems,
                'critical' => $criticalItems,
            ],
            'filters' => $filters,
            'products' => $products,
        ];
    }

    /**
     * Generate sales report
     * 
     * @param array $filters
     * @return array
     */
    public function generateSalesReport(array $filters = []): array
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date']) : now()->subDays(30);
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date']) : now();
        
        // Base query for orders in range
        $query = SalesOrder::whereBetween('order_date', [$startDate, $endDate]);
        
        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }
        
        $orders = $query->with(['items.product', 'customer'])->get();
        
        // Calculate totals
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_amount');
        $totalCost = $orders->sum(function ($order) {
            return $order->items->sum(function ($item) {
                return ($item->product->total_cost ?? 0) * $item->quantity;
            });
        });
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? (($totalProfit / $totalRevenue) * 100) : 0;
        
        // Group by date
        $salesByDate = $orders->groupBy(function ($order) {
            return Carbon::parse($order->order_date)->format('Y-m-d');
        })->map(function ($dayOrders) {
            return [
                'count' => $dayOrders->count(),
                'revenue' => $dayOrders->sum('total_amount'),
            ];
        });
        
        // Top products
        $topProducts = $this->getTopSellingProducts($startDate, $endDate, 10);
        
        // Product sales (all products for the period)
        $productSales = $this->getProductSales($startDate, $endDate);
        
        // Sales by status
        $salesByStatus = $orders->groupBy('status')->map(function ($statusOrders) {
            return [
                'count' => $statusOrders->count(),
                'amount' => $statusOrders->sum('total_amount'),
            ];
        });

        // Top customers
        $topCustomers = $orders->groupBy('customer_id')->map(function ($customerOrders) {
            $customer = $customerOrders->first()->customer;
            return [
                'customer_id' => $customer?->customer_id,
                'customer_name' => $customer?->display_name ?? 'N/A',
                'order_count' => $customerOrders->count(),
                'total_spent' => $customerOrders->sum('total_amount'),
            ];
        })->sortByDesc('total_spent')->take(10)->values();
        
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
            'summary' => [
                'total_orders' => $totalOrders,
                'total_revenue' => round($totalRevenue, 2),
                'total_cost' => round($totalCost, 2),
                'total_profit' => round($totalProfit, 2),
                'profit_margin' => round($profitMargin, 2),
                'average_order_value' => $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0,
            ],
            'sales_by_date' => $salesByDate,
            'sales_by_status' => $salesByStatus,
            'top_products' => $topProducts,
            'top_customers' => $topCustomers,
            'product_sales' => $productSales,
            'orders' => $orders,
        ];
    }

    /**
     * Generate purchase report
     * 
     * @param array $filters
     * @return array
     */
    public function generatePurchaseReport(array $filters = []): array
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date']) : now()->subDays(30);
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date']) : now();
        
        $query = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate]);
        
        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }
        
        $orders = $query->with(['items.product', 'supplier', 'payments'])->get();
        
        // Calculate totals
        $totalOrders = $orders->count();
        $totalAmount = $orders->sum('total_amount');
        $totalItems = $orders->sum(function ($order) {
            return $order->items->sum('quantity');
        });
        
        // Group by date
        $purchasesByDate = $orders->groupBy(function ($order) {
            return Carbon::parse($order->order_date)->format('Y-m-d');
        })->map(function ($dayOrders) {
            return [
                'count' => $dayOrders->count(),
                'amount' => $dayOrders->sum('total_amount'),
            ];
        });
        
        // Top suppliers
        $topSuppliers = $orders->groupBy('supplier_id')->map(function ($supplierOrders) {
            $supplier = $supplierOrders->first()->supplier;
            return [
                'supplier_id' => $supplier->supplier_id,
                'supplier_name' => $supplier->supplier_name,
                'order_count' => $supplierOrders->count(),
                'total_amount' => $supplierOrders->sum('total_amount'),
            ];
        })->sortByDesc('total_amount')->take(10)->values();
        
        // Purchases by status
        $purchasesByStatus = $orders->groupBy('status')->map(function ($statusOrders) {
            return [
                'count' => $statusOrders->count(),
                'amount' => $statusOrders->sum('total_amount'),
            ];
        });
        
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
            'summary' => [
                'total_orders' => $totalOrders,
                'total_amount' => round($totalAmount, 2),
                'total_items' => $totalItems,
                'average_order_value' => $totalOrders > 0 ? round($totalAmount / $totalOrders, 2) : 0,
            ],
            'purchases_by_date' => $purchasesByDate,
            'purchases_by_status' => $purchasesByStatus,
            'top_suppliers' => $topSuppliers,
            'orders' => $orders,
        ];
    }

    /**
     * Generate inventory report
     * 
     * @param array $filters
     * @return array
     */
    public function generateInventoryReport(array $filters = []): array
    {
        $query = Product::query();
        
        // Apply filters
        if (isset($filters['category'])) {
            $query->where('product_category', $filters['category']);
        }
        if (isset($filters['movement_category'])) {
            $query->where('movement_category', $filters['movement_category']);
        }
        if (isset($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('quantity', '<=', 'reorder_level');
                    break;
                case 'critical':
                    $query->whereColumn('quantity', '<=', 'critical_level');
                    break;
                case 'overstocked':
                    $query->whereColumn('quantity', '>', 'ceiling_level');
                    break;
            }
        }
        
        $products = $query->get();
        
        // Calculate totals
        $totalProducts = $products->count();
        $totalStockValue = $products->sum(function ($product) {
            return $product->quantity * ($product->total_cost ?? $product->price);
        });
        $totalQuantity = $products->sum('quantity');
        
        // Stock status breakdown
        $outOfStock = Product::where('quantity', '<=', 0)->count();
        $lowStock = Product::whereColumn('quantity', '<=', 'reorder_level')
            ->whereNotNull('reorder_level')->count();
        $critical = Product::whereColumn('quantity', '<=', 'critical_level')
            ->whereNotNull('critical_level')->count();
        $overstocked = Product::whereColumn('quantity', '>', 'ceiling_level')
            ->whereNotNull('ceiling_level')->count();
        
        // Movement breakdown
        $fastMoving = Product::where('movement_category', 'fast')->count();
        $slowMoving = Product::where('movement_category', 'slow')->count();
        $nonMoving = Product::where('movement_category', 'non-moving')->count();
        
        // Products by category
        $productsByCategory = Product::select('product_category', DB::raw('COUNT(*) as count'), DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_category')
            ->get();
        
        // Top value products
        $topValueProducts = Product::select('*')
            ->selectRaw('quantity * COALESCE(total_cost, price) as stock_value')
            ->orderByDesc('stock_value')
            ->limit(10)
            ->get();
        
        return [
            'summary' => [
                'total_products' => $totalProducts,
                'total_stock_value' => round($totalStockValue, 2),
                'total_quantity' => $totalQuantity,
                'average_value_per_product' => $totalProducts > 0 ? round($totalStockValue / $totalProducts, 2) : 0,
            ],
            'stock_status' => [
                'out_of_stock' => $outOfStock,
                'low_stock' => $lowStock,
                'critical_stock' => $critical,
                'overstocked' => $overstocked,
                'healthy_stock' => $totalProducts - ($outOfStock + $lowStock + $critical + $overstocked),
            ],
            'movement_analysis' => [
                'fast_moving' => $fastMoving,
                'slow_moving' => $slowMoving,
                'non_moving' => $nonMoving,
                'uncategorized' => $totalProducts - ($fastMoving + $slowMoving + $nonMoving),
            ],
            'products_by_category' => $productsByCategory,
            'top_value_products' => $topValueProducts,
            'products' => $products,
        ];
    }

    /**
     * Generate financial report
     * 
     * @param array $filters
     * @return array
     */
    public function generateFinancialReport(array $filters = []): array
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date']) : now()->subDays(30);
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date']) : now();
        
        // Sales revenue
        $salesRevenue = SalesOrder::whereBetween('order_date', [$startDate, $endDate])
            ->whereIn('order_status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('total_amount');
        
        // Purchase expenses
        $purchaseExpenses = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'partially_received', 'received'])
            ->sum('total_amount');
        
        // Returns (refunds)
        $salesReturns = Returns::whereBetween('return_date', [$startDate, $endDate])
            ->where('return_status', 'approved')
            ->sum('total_amount');
        
        $purchaseReturns = PurchaseReturn::whereBetween('return_date', [$startDate, $endDate])
            ->where('return_status', 'approved')
            ->sum('total_amount');
        
        // Net revenue
        $netRevenue = $salesRevenue - $salesReturns;
        $netExpenses = $purchaseExpenses - $purchaseReturns;
        $grossProfit = $netRevenue - $netExpenses;
        
        // Payment analysis
        $paymentsReceived = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('payment_status', 'completed')
            ->sum('amount');
        
        // Invoice analysis
        $invoicesIssued = Invoice::whereBetween('invoice_date', [$startDate, $endDate])->count();
        $invoicesTotal = Invoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('total_amount');
        $invoicesPaid = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->where('payment_status', 'paid')->sum('total_amount');
        $invoicesOutstanding = $invoicesTotal - $invoicesPaid;
        
        // Monthly breakdown
        $monthlyData = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();
            
            $monthRevenue = SalesOrder::whereBetween('order_date', [$monthStart, $monthEnd])
                ->whereIn('order_status', ['confirmed', 'processing', 'shipped', 'delivered'])
                ->sum('total_amount');
            
            $monthExpenses = PurchaseOrder::whereBetween('order_date', [$monthStart, $monthEnd])
                ->whereIn('status', ['confirmed', 'partially_received', 'received'])
                ->sum('total_amount');
            
            $monthlyData[$monthStart->format('Y-m')] = [
                'revenue' => round($monthRevenue, 2),
                'expenses' => round($monthExpenses, 2),
                'profit' => round($monthRevenue - $monthExpenses, 2),
            ];
            
            $currentDate->addMonth();
        }
        
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
            'revenue' => [
                'gross_sales' => round($salesRevenue, 2),
                'sales_returns' => round($salesReturns, 2),
                'net_sales' => round($netRevenue, 2),
            ],
            'expenses' => [
                'gross_purchases' => round($purchaseExpenses, 2),
                'purchase_returns' => round($purchaseReturns, 2),
                'net_purchases' => round($netExpenses, 2),
            ],
            'profit' => [
                'gross_profit' => round($grossProfit, 2),
                'profit_margin' => $netRevenue > 0 ? round(($grossProfit / $netRevenue) * 100, 2) : 0,
            ],
            'payments' => [
                'payments_received' => round($paymentsReceived, 2),
            ],
            'invoices' => [
                'invoices_issued' => $invoicesIssued,
                'invoices_total' => round($invoicesTotal, 2),
                'invoices_paid' => round($invoicesPaid, 2),
                'invoices_outstanding' => round($invoicesOutstanding, 2),
            ],
            'monthly_breakdown' => $monthlyData,
        ];
    }

    /**
     * Generate product movement report
     * 
     * @param array $filters
     * @return array
     */
    public function generateMovementReport(array $filters = []): array
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date']) : now()->subDays(30);
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date']) : now();
        
        // Get stock movements
        $movements = StockMovement::whereBetween('movement_date', [$startDate, $endDate])
            ->with('product')
            ->get();
        
        // Movement by type
        $movementsByType = $movements->groupBy('movement_type')->map(function ($typeMovements) {
            return [
                'count' => $typeMovements->count(),
                'total_quantity' => $typeMovements->sum('quantity_change'),
            ];
        });
        
        // Most active products
        $mostActiveProducts = $movements->groupBy('product_id')->map(function ($productMovements) {
            $product = $productMovements->first()->product;
            return [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'movement_count' => $productMovements->count(),
                'total_quantity_change' => $productMovements->sum('quantity_change'),
            ];
        })->sortByDesc('movement_count')->take(20)->values();
        
        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_movements' => $movements->count(),
                'movements_by_type' => $movementsByType,
            ],
            'most_active_products' => $mostActiveProducts,
            'movements' => $movements,
        ];
    }

    /**
     * Get top selling products
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    protected function getTopSellingProducts(Carbon $startDate, Carbon $endDate, int $limit = 10)
    {
        // Get database driver to handle CONCAT differently for SQLite
        $driver = DB::connection()->getDriverName();
        $concatSql = $driver === 'sqlite' 
            ? "products.product_brand || '-' || products.product_category"
            : "CONCAT(products.product_brand, '-', products.product_category)";
        
        return DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.order_id', '=', 'sales_orders.order_id')
            ->join('products', 'sales_order_items.product_id', '=', 'products.product_id')
            ->leftJoin('inventories', function($join) {
                $join->on('products.product_id', '=', 'inventories.product_id')
                     ->whereNull('inventories.property_id');
            })
            ->whereBetween('sales_orders.order_date', [$startDate, $endDate])
            ->whereIn('sales_orders.status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->select(
                'products.product_id',
                'products.product_name',
                'products.product_brand',
                'products.product_category',
                DB::raw("COALESCE(inventories.sku, products.sku, {$concatSql}) as product_sku"),
                DB::raw('SUM(sales_order_items.quantity) as total_quantity'),
                DB::raw('SUM(sales_order_items.quantity * sales_order_items.unit_price) as total_revenue')
            )
            ->groupBy('products.product_id', 'products.product_name', 'products.product_brand', 'products.product_category', 'inventories.sku', 'products.sku')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    /**
     * Get product sales for period (aggregated across all sales orders)
     */
    protected function getProductSales(Carbon $startDate, Carbon $endDate)
    {
        // Get database driver to handle CONCAT differently for SQLite
        $driver = DB::connection()->getDriverName();
        $concatSql = $driver === 'sqlite' 
            ? "products.product_brand || '-' || products.product_category"
            : "CONCAT(products.product_brand, '-', products.product_category)";
        
        $rows = DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.order_id', '=', 'sales_orders.order_id')
            ->join('products', 'sales_order_items.product_id', '=', 'products.product_id')
            ->leftJoin('inventories', function($join) {
                $join->on('products.product_id', '=', 'inventories.product_id')
                     ->whereNull('inventories.property_id');
            })
            ->whereBetween('sales_orders.order_date', [$startDate, $endDate])
            ->whereIn('sales_orders.status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->select(
                'products.product_id',
                'products.product_name',
                'products.product_brand',
                'products.product_category',
                DB::raw("COALESCE(inventories.sku, products.sku, {$concatSql}) as product_sku"),
                DB::raw('SUM(sales_order_items.quantity) as total_quantity'),
                DB::raw('SUM(sales_order_items.quantity * sales_order_items.unit_price) as total_revenue')
            )
            ->groupBy('products.product_id', 'products.product_name', 'products.product_brand', 'products.product_category', 'inventories.sku', 'products.sku')
            ->orderByDesc('total_revenue')
            ->get();

        // Attach dynamic stock quantities via model accessor
        return $rows->map(function ($row) {
            $product = Product::find($row->product_id);
            $row->instock_qty = $product?->quantity ?? 0;
            return $row;
        });
    }

    /**
     * Export report data to array for Excel/PDF
     * 
     * @param string $reportType
     * @param array $filters
     * @return array
     */
    public function exportReportData(string $reportType, array $filters = []): array
    {
        switch ($reportType) {
            case 'sales':
                return $this->generateSalesReport($filters);
            case 'purchases':
                return $this->generatePurchaseReport($filters);
            case 'inventory':
                return $this->generateInventoryReport($filters);
            case 'financial':
                return $this->generateFinancialReport($filters);
            case 'movement':
                return $this->generateMovementReport($filters);
            case 'reorder':
                return $this->generateReorderReport($filters);
            case 'blocked':
                return $this->generateBlockedItemsReport($filters);
            default:
                return [];
        }
    }
}
