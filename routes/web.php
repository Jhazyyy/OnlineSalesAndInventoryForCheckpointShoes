<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseReceiveController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BankTransferPaymentController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Auth as ContractsAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('welcome');
    
});
Route::view('/terms', 'terms')->name('terms');

// Debug route to check authentication and session
Route::get('/debug', function () {
    return [
        'authenticated' => auth()->check(),
        'user' => auth()->user(),
        'session_id' => session()->getId(),
        'csrf_token' => csrf_token(),
        'session_driver' => config('session.driver'),
        'session_domain' => config('session.domain'),
        'app_url' => config('app.url'),
        'session_lifetime' => config('session.lifetime'),
        'app_key' => config('app.key') ? 'Set' : 'Not set',
        'session_data' => session()->all(),
    ];
});

// CSRF Test routes
Route::get('/csrf-test', function () {
    return view('csrf-test');
});

Route::post('/csrf-test', function () {
    return response()->json(['success' => true, 'message' => 'CSRF token is working!']);
});

// Livewire demo pages (non-invasive)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/livewire/products', fn () => view('livewire-pages.products'))->name('livewire.products');
    Route::get('/livewire/inventory', fn () => view('livewire-pages.inventory'))->name('livewire.inventory');
    Route::get('/livewire/customers', fn () => view('livewire-pages.customers'))->name('livewire.customers');
    Route::get('/livewire/sales-orders', fn () => view('livewire-pages.sales-orders'))->name('livewire.sales-orders');
});

// Main Route
Route::get('dashboard', function() {
    // Debug: Check if user is authenticated
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'You must be logged in to access the dashboard.');
    }

    // Get comprehensive statistics for dashboard with error handling

    // Inventory Statistics
    try {
        // Get the low stock threshold from settings (default: 10)
        $lowStockThreshold = lowStockThreshold();
        
        $inventoryStats = [
            'total_products' => \App\Models\Product::count(),
            'active_products' => \App\Models\Product::where('quantity', '>', 0)->count(), 
            // Low stock: items with stock > 0 but <= threshold (excludes out of stock items)
            'low_stock_products' => \App\Models\Product::where('quantity', '>', 0)
                                                       ->where('quantity', '<=', $lowStockThreshold)
                                                       ->count(),
            // Out of stock: items with 0
            'out_of_stock_products' => \App\Models\Product::where('quantity', '=', 0)->count(),
            'critical_stock_products' => \App\Models\Product::where('quantity', '<=', ($lowStockThreshold / 2))->count(),
            'total_inventory_value' => \App\Models\Product::selectRaw('SUM(quantity * price) as total')->value('total') ?? 0,
            // 'total_units' => \App\Models\Product::sum('quantity') ?? 0,
        ];
    } catch (\Exception $e) {
        Log::error('Error fetching inventory stats: ' . $e->getMessage());
        $inventoryStats = [
            'total_products' => 0,
            'active_products' => 0,
            'low_stock_products' => 0,
            'out_of_stock_products' => 0,
            'critical_stock_products' => 0,
            'total_inventory_value' => 0,
            'total_units' => 0,
        ];
    } catch (\Exception $e) {
        Log::error('Error fetching inventory stats: ' . $e->getMessage());
        $inventoryStats = [
            'total_products' => 0,
            'active_products' => 0,
            'low_stock_products' => 0,
            'out_of_stock_products' => 0,
            'critical_stock_products' => 0,
            'total_inventory_value' => 0,
            'total_units' => 0,
        ];
    }

    // Sales Statistics
    try {
        $salesStats = [
            'total_sales' => \App\Models\Sale::count(),
            'today_sales' => \App\Models\Sale::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count(),
            'this_month_sales' => \App\Models\Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'total_sales_value' => \App\Models\Sale::sum('total_amount') ?? 0,
            'today_sales_value' => \App\Models\Sale::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->sum('total_amount') ?? 0,
        ];
    } catch (\Exception $e) {
        $salesStats = [
            'total_sales' => 0,
            'today_sales' => 0,
            'this_month_sales' => 0,
            'total_sales_value' => 0,
            'today_sales_value' => 0,
        ];
    }

    // Customer Statistics
    try {
        $customerStats = [
            'total_customers' => \App\Models\Customer::count(),
            'active_customers' => \App\Models\Customer::where('status', 'active')->count(),
            'new_customers_this_month' => \App\Models\Customer::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];
    } catch (\Exception $e) {
        $customerStats = [
            'total_customers' => 0,
            'active_customers' => 0,
            'new_customers_this_month' => 0,
        ];
    }

    // Purchase Statistics
    try {
        $purchaseStats = [
            'total_purchases' => \App\Models\PurchaseOrder::count(),
            'this_month_purchases' => \App\Models\PurchaseOrder::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'total_purchase_value' => \App\Models\PurchaseOrder::sum('total_amount') ?? 0,
        ];
    } catch (\Exception $e) {
        $purchaseStats = [
            'total_purchases' => 0,
            'this_month_purchases' => 0,
            'total_purchase_value' => 0,
        ];
    }

    // Sales Return Statistics
    try {
        $returnStats = [
            'total_returns' => \App\Models\Returns::count(),
            'pending_returns' => \App\Models\Returns::pending()->count(),
            'approved_returns' => \App\Models\Returns::approved()->count(),
            'total_return_value' => \App\Models\Returns::approved()->get()->sum('total_amount') ?? 0,
            'today_returns' => \App\Models\Returns::today()->count(),
            'this_week_returns' => \App\Models\Returns::thisWeek()->count(),
            'this_month_returns' => \App\Models\Returns::thisMonth()->count(),
        ];
    } catch (\Exception $e) {
        $returnStats = [
            'total_returns' => 0,
            'pending_returns' => 0,
            'approved_returns' => 0,
            'total_return_value' => 0,
            'today_returns' => 0,
            'this_week_returns' => 0,
            'this_month_returns' => 0,
        ];
    }

    // Purchase Return Statistics
    try {
        $purchaseReturnStats = [
            'total_purchase_returns' => \App\Models\PurchaseReturn::count(),
            'pending_purchase_returns' => \App\Models\PurchaseReturn::pending()->count(),
            'approved_purchase_returns' => \App\Models\PurchaseReturn::approved()->count(),
            'processed_purchase_returns' => \App\Models\PurchaseReturn::where('return_status', 'processed')->count(),
            'total_purchase_return_value' => \App\Models\PurchaseReturn::approved()->get()->sum('total_amount') ?? 0,
            'today_purchase_returns' => \App\Models\PurchaseReturn::today()->count(),
            'this_week_purchase_returns' => \App\Models\PurchaseReturn::thisWeek()->count(),
            'this_month_purchase_returns' => \App\Models\PurchaseReturn::thisMonth()->count(),
        ];
    } catch (\Exception $e) {
        $purchaseReturnStats = [
            'total_purchase_returns' => 0,
            'pending_purchase_returns' => 0,
            'approved_purchase_returns' => 0,
            'processed_purchase_returns' => 0,
            'total_purchase_return_value' => 0,
            'today_purchase_returns' => 0,
            'this_week_purchase_returns' => 0,
            'this_month_purchase_returns' => 0,
        ];
    }

    // Supplier Statistics
    try {
        $supplierStats = [
            'total_suppliers' => \App\Models\Supplier::count(),
            'active_suppliers' => \App\Models\Supplier::where('status', 'active')->count(),
        ];
    } catch (\Exception $e) {
        $supplierStats = [
            'total_suppliers' => 0,
            'active_suppliers' => 0,
        ];
    }

    // Shipment Statistics
    try {
        $shipmentStats = [
            'total_shipments' => 0,
            'pending_shipments' => 0,
            'in_transit_shipments' => 0,
            'delivered_shipments' => 0,
        ];
    } catch (\Exception $e) {
        $shipmentStats = [
            'total_shipments' => 0,
            'pending_shipments' => 0,
            'in_transit_shipments' => 0,
            'delivered_shipments' => 0,
        ];
    }

    // Top Selling Items - From Sales Orders (Default: This Month)
    try {
        $topSellingItems = \App\Models\SalesOrderItem::select('product_id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(quantity * unit_price) as total_revenue')
            ->whereNotNull('product_id')
            ->whereHas('order', function ($q) {
                $q->whereMonth('order_date', now()->month)
                  ->whereYear('order_date', now()->year);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get()
            ->filter(function ($item) {
                return $item->product !== null;
            })
            ->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown Product',
                    'sku' => $item->product->sku ?? 'N/A',
                    'size' => $item->product->size ?? null,
                    'color' => $item->product->color ?? null,
                    'quantity' => (int) $item->total_quantity,
                    'revenue' => (float) $item->total_revenue,
                    'image' => $item->product->image ?? null,
                ];
            })
            ->values();
    } catch (\Exception $e) {
    Log::error('Error fetching top selling items: ' . $e->getMessage());
        $topSellingItems = collect();
    }

    // Top Purchase Items - From Purchase Order Items (Default: This Month)
    try {
        $topPurchaseItems = \App\Models\PurchaseOrderItem::select('product_id')
            ->selectRaw('SUM(quantity_ordered) as total_quantity')
            ->selectRaw('SUM(quantity_ordered * unit_price) as total_cost')
            ->whereNotNull('product_id')
            ->whereHas('order', function ($q) {
                $q->whereMonth('order_date', now()->month)
                  ->whereYear('order_date', now()->year);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get()
            ->filter(function ($item) {
                return $item->product !== null;
            })
            ->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown Product',
                    'sku' => $item->product->sku ?? 'N/A',
                    'size' => $item->product->size ?? null,
                    'color' => $item->product->color ?? null,
                    'quantity' => (int) $item->total_quantity,
                    'cost' => (float) $item->total_cost,
                    'image' => $item->product->image ?? null,
                ];
            })
            ->values();
    } catch (\Exception $e) {
        Log::error('Error fetching top purchase items: ' . $e->getMessage());
        $topPurchaseItems = collect();
    }

    // Product Details (Stock Status)
    try {
        $stockStatus = [
            'in_stock' => \App\Models\Product::where('quantity', '>', 10)->count(),
            'low_stock' => \App\Models\Product::whereBetween('quantity', [1, 10])->count(),
            'out_of_stock' => \App\Models\Product::where('quantity', '<=', 0)->count(),
        ];
    } catch (\Exception $e) {
        $stockStatus = [
            'in_stock' => 0,
            'low_stock' => 0,
            'out_of_stock' => 0,
        ];
    }

    // Purchase Order Status
    try {
        $purchaseOrderStatus = [
            'pending' => \App\Models\PurchaseOrder::where('status', 'pending')->count(),
            'approved' => \App\Models\PurchaseOrder::where('status', 'approved')->count(),
            'ordered' => \App\Models\PurchaseOrder::where('status', 'ordered')->count(),
            'cancelled' => \App\Models\PurchaseOrder::where('status', 'cancelled')->count(),
        ];
    } catch (\Exception $e) {
        $purchaseOrderStatus = [
            'pending' => 0,
            'approved' => 0,
            'ordered' => 0,
            'partial_received' => 0,
            'received' => 0,
            'cancelled' => 0,
        ];
    }

    // Purchase Receives Analytics
    try {
        $purchaseReceiveStats = [
            'total_receives' => \App\Models\PurchaseReceive::count(),
            'received_count' => \App\Models\PurchaseReceive::where('status', 'received')->count(),
            'in_transit_count' => \App\Models\PurchaseReceive::where('status', 'in_transit')->count(),
            'total_value_received' => \App\Models\PurchaseReceive::where('status', 'received')->sum('total_amount_received') ?? 0,
            'this_month_receives' => \App\Models\PurchaseReceive::whereMonth('receive_date', now()->month)
                                        ->whereYear('receive_date', now()->year)->count(),
        ];
        
        // Calculate comprehensive "Quantity to be Received" from multiple sources
        // 1. From Purchase Orders - pending items (ordered but not received)
        $pendingFromPurchaseOrders = \App\Models\PurchaseOrderItem::whereHas('order', function($query) {
            $query->whereIn('status', ['ordered', 'partial_received', 'approved']);
        })
        ->selectRaw('SUM(quantity_ordered - quantity_received) as pending_qty')
        ->value('pending_qty') ?? 0;
        
        // 2. From Purchase Receives - items expected but not yet received
        $pendingFromReceives = \App\Models\PurchaseReceiveItem::whereHas('purchaseReceive', function($query) {
            $query->whereIn('status', ['in_transit', 'pending']);
        })
        ->selectRaw('SUM(quantity_expected - quantity_received) as pending_qty')
        ->value('pending_qty') ?? 0;
        
        // Add comprehensive pending quantity to stats
        $purchaseReceiveStats['pending_quantity'] = max(0, $pendingFromPurchaseOrders + $pendingFromReceives);
        $purchaseReceiveStats['pending_from_orders'] = max(0, $pendingFromPurchaseOrders);
        $purchaseReceiveStats['pending_from_receives'] = max(0, $pendingFromReceives);
        
    } catch (\Exception $e) {
        $purchaseReceiveStats = [
            'total_receives' => 0,
            'received_count' => 0,
            'in_transit_count' => 0,
            'total_value_received' => 0,
            'this_month_receives' => 0,
            'pending_quantity' => 0,
            'pending_from_orders' => 0,
            'pending_from_receives' => 0,
        ];
    }

    // Purchase Payment Analytics
    try {
        $purchasePaymentStats = [
            'total_payments' => \App\Models\PurchasePayment::count(),
            'total_paid' => \App\Models\PurchasePayment::sum('amount') ?? 0,
            'pending_payments' => \App\Models\PurchaseOrder::where('payment_status', 'pending')->count(),
            'partial_paid' => \App\Models\PurchaseOrder::where('payment_status', 'partial')->count(),
            'fully_paid' => \App\Models\PurchaseOrder::where('payment_status', 'paid')->count(),
            'this_month_payments' => \App\Models\PurchasePayment::whereMonth('payment_date', now()->month)
                                        ->whereYear('payment_date', now()->year)
                                        ->sum('amount') ?? 0,
        ];
    } catch (\Exception $e) {
        $purchasePaymentStats = [
            'total_payments' => 0,
            'total_paid' => 0,
            'pending_payments' => 0,
            'partial_paid' => 0,
            'fully_paid' => 0,
            'this_month_payments' => 0,
        ];
    }

    // Monthly Revenue (Last 6 months)
    try {
        $last6Months = collect(range(5, 0))->map(function ($months) {
            return now()->subMonths($months)->format('M');
        });
        
        $monthlyRevenue = collect(range(5, 0))->map(function ($months) {
            $date = now()->subMonths($months);
            return \App\Models\Sale::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount') ?? 0;
        });
    } catch (\Exception $e) {
        $last6Months = collect();
        $monthlyRevenue = collect();
    }

    // Ensure all variables are defined
    $topSellingItems = $topSellingItems ?? collect();
    $topPurchaseItems = $topPurchaseItems ?? collect();
    $salesActivity = $salesActivity ?? ['total_invoices' => 0, 'paid_invoices' => 0, 'draft_invoices' => 0, 'past_due' => 0];
    $stockStatus = $stockStatus ?? ['in_stock' => 0, 'low_stock' => 0, 'out_of_stock' => 0];
    $purchaseOrderStatus = $purchaseOrderStatus ?? ['pending' => 0, 'approved' => 0, 'ordered' => 0, 'partial_received' => 0, 'received' => 0, 'cancelled' => 0];
    $purchaseReceiveStats = $purchaseReceiveStats ?? ['total_receives' => 0, 'received_count' => 0, 'in_transit_count' => 0, 'total_value_received' => 0, 'this_month_receives' => 0, 'pending_quantity' => 0, 'pending_from_orders' => 0, 'pending_from_receives' => 0];
    $purchasePaymentStats = $purchasePaymentStats ?? ['total_payments' => 0, 'total_paid' => 0, 'pending_payments' => 0, 'partial_paid' => 0, 'fully_paid' => 0, 'this_month_payments' => 0];
    $last7Days = $last7Days ?? collect();
    $salesOrderData = $salesOrderData ?? collect();
    $last6Months = $last6Months ?? collect();
    $monthlyRevenue = $monthlyRevenue ?? collect();

    return view('dashboard', compact(
        'inventoryStats',
        'salesStats',
        'customerStats',
        'purchaseStats',
        'returnStats',
        'purchaseReturnStats',
        'supplierStats',
        'shipmentStats',
        'last7Days',
        'salesOrderData',
        'salesActivity',
        'topSellingItems',
        'topPurchaseItems',
        'stockStatus',
        'purchaseOrderStatus',
        'purchaseReceiveStats',
        'purchasePaymentStats',
        'last6Months',
        'monthlyRevenue'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard Top Selling Items AJAX Route
Route::get('dashboard/top-selling-items', function (Illuminate\Http\Request $request) {
    if (!Auth::check()) {
        return response()->json(['items' => []], 401);
    }

    $period = $request->get('period', 'this_month');
    $query = \App\Models\SalesOrderItem::query();

    // Apply date filter based on period
    switch ($period) {
        case 'today':
            $query->whereHas('order', function ($q) {
                $q->whereDate('order_date', today());
            });
            break;
        case 'yesterday':
            $query->whereHas('order', function ($q) {
                $q->whereDate('order_date', today()->subDay());
            });
            break;
        case 'this_week':
            $query->whereHas('order', function ($q) {
                $q->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]);
            });
            break;
        case 'last_week':
            $query->whereHas('order', function ($q) {
                $q->whereBetween('order_date', [
                    now()->subWeek()->startOfWeek(),
                    now()->subWeek()->endOfWeek()
                ]);
            });
            break;
        case 'this_month':
            $query->whereHas('order', function ($q) {
                $q->whereMonth('order_date', now()->month)
                  ->whereYear('order_date', now()->year);
            });
            break;
        case 'last_month':
            $query->whereHas('order', function ($q) {
                $q->whereMonth('order_date', now()->subMonth()->month)
                  ->whereYear('order_date', now()->subMonth()->year);
            });
            break;
        case 'this_year':
            $query->whereHas('order', function ($q) {
                $q->whereYear('order_date', now()->year);
            });
            break;
        case 'all_time':
            // No date filter
            break;
    }

    $topSellingItems = $query->select('product_id')
        ->selectRaw('SUM(quantity) as total_quantity')
        ->selectRaw('SUM(quantity * unit_price) as total_revenue')
        ->whereNotNull('product_id')
        ->with('product')
        ->groupBy('product_id')
        ->orderByDesc('total_quantity')
        ->limit(100)
        ->get()
        ->filter(function ($item) {
            return $item->product !== null;
        })
        ->map(function ($item) {
            return [
                'name' => $item->product->name ?? 'Unknown Product',
                'sku' => $item->product->sku ?? 'N/A',
                'size' => $item->product->size ?? null,
                'color' => $item->product->color ?? null,
                'quantity' => (int) $item->total_quantity,
                'revenue' => (float) $item->total_revenue,
                'image' => $item->product->image ?? null,
            ];
        })
        ->values();

    return response()->json(['items' => $topSellingItems]);
})->middleware(['auth', 'verified'])->name('dashboard.top-selling-items');

// Dashboard Revenue Trend AJAX Route
Route::get('dashboard/revenue-trend', function (Illuminate\Http\Request $request) {
    if (!Auth::check()) {
        return response()->json(['labels' => [], 'data' => []], 401);
    }

    $period = $request->get('period', 'this_month');
    $query = \App\Models\SalesOrder::query();
    $labels = [];
    $data = [];

    // Apply date filter and grouping based on period
    switch ($period) {
        case 'today':
            // Group by hour
            $query->whereDate('order_date', today());
            $revenueData = $query
                ->selectRaw('HOUR(order_date) as period_key')
                ->selectRaw('SUM(total_amount) as total_revenue')
                ->groupBy('period_key')
                ->orderBy('period_key')
                ->get()
                ->keyBy('period_key');
            
            for ($i = 0; $i < 24; $i++) {
                $labels[] = sprintf('%02d:00', $i);
                $data[] = isset($revenueData[$i]) ? (float) $revenueData[$i]->total_revenue : 0;
            }
            break;

        case 'yesterday':
            // Group by hour
            $query->whereDate('order_date', today()->subDay());
            $revenueData = $query
                ->selectRaw('HOUR(order_date) as period_key')
                ->selectRaw('SUM(total_amount) as total_revenue')
                ->groupBy('period_key')
                ->orderBy('period_key')
                ->get()
                ->keyBy('period_key');
            
            for ($i = 0; $i < 24; $i++) {
                $labels[] = sprintf('%02d:00', $i);
                $data[] = isset($revenueData[$i]) ? (float) $revenueData[$i]->total_revenue : 0;
            }
            break;

        case 'this_week':
        case 'last_week':
            // Group by day
            if ($period === 'this_week') {
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
            } else {
                $startDate = now()->subWeek()->startOfWeek();
                $endDate = now()->subWeek()->endOfWeek();
            }
            
            $query->whereBetween('order_date', [$startDate, $endDate]);
            $revenueData = $query
                ->selectRaw('DATE(order_date) as period_key')
                ->selectRaw('SUM(total_amount) as total_revenue')
                ->groupBy('period_key')
                ->orderBy('period_key')
                ->get()
                ->keyBy('period_key');
            
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dateKey = $currentDate->format('Y-m-d');
                $labels[] = $currentDate->format('D, M j');
                $data[] = isset($revenueData[$dateKey]) ? (float) $revenueData[$dateKey]->total_revenue : 0;
                $currentDate->addDay();
            }
            break;

        case 'this_month':
        case 'last_month':
            // Group by day
            if ($period === 'this_month') {
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $query->whereMonth('order_date', now()->month)
                      ->whereYear('order_date', now()->year);
            } else {
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                $query->whereMonth('order_date', now()->subMonth()->month)
                      ->whereYear('order_date', now()->subMonth()->year);
            }
            
            $revenueData = $query
                ->selectRaw('DATE(order_date) as period_key')
                ->selectRaw('SUM(total_amount) as total_revenue')
                ->groupBy('period_key')
                ->orderBy('period_key')
                ->get()
                ->keyBy('period_key');
            
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dateKey = $currentDate->format('Y-m-d');
                $labels[] = $currentDate->format('M j');
                $data[] = isset($revenueData[$dateKey]) ? (float) $revenueData[$dateKey]->total_revenue : 0;
                $currentDate->addDay();
            }
            break;

        case 'this_year':
            // Group by month
            $query->whereYear('order_date', now()->year);
            $revenueData = $query
                ->selectRaw('MONTH(order_date) as period_key')
                ->selectRaw('SUM(total_amount) as total_revenue')
                ->groupBy('period_key')
                ->orderBy('period_key')
                ->get()
                ->keyBy('period_key');
            
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = now()->month($i)->format('M');
                $data[] = isset($revenueData[$i]) ? (float) $revenueData[$i]->total_revenue : 0;
            }
            break;

        case 'all_time':
            // Group by month (last 12 months)
            $startDate = now()->subMonths(11)->startOfMonth();
            $endDate = now()->endOfMonth();
            
            $query->whereBetween('order_date', [$startDate, $endDate]);
            $revenueData = $query
                ->selectRaw('DATE_FORMAT(order_date, "%Y-%m") as period_key')
                ->selectRaw('SUM(total_amount) as total_revenue')
                ->groupBy('period_key')
                ->orderBy('period_key')
                ->get()
                ->keyBy('period_key');
            
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dateKey = $currentDate->format('Y-m');
                $labels[] = $currentDate->format('M Y');
                $data[] = isset($revenueData[$dateKey]) ? (float) $revenueData[$dateKey]->total_revenue : 0;
                $currentDate->addMonth();
            }
            break;
    }

    return response()->json([
        'labels' => $labels,
        'data' => $data,
        'total' => array_sum($data)
    ]);
})->middleware(['auth', 'verified'])->name('dashboard.revenue-trend');

// Dashboard Period Statistics AJAX Route
Route::get('dashboard/period-statistics', function (Illuminate\Http\Request $request) {
    if (!Auth::check()) {
        return response()->json([
            'total_revenue' => 0,
            'total_profit' => 0,
            'total_sales' => 0,
            'total_items' => 0
        ], 401);
    }

    $period = $request->get('period', 'this_month');
    $query = \App\Models\SalesOrder::query();

    // Apply date filter based on period
    switch ($period) {
        case 'today':
            $query->whereDate('order_date', today());
            break;
        case 'yesterday':
            $query->whereDate('order_date', today()->subDay());
            break;
        case 'this_week':
            $query->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]);
            break;
        case 'last_week':
            $query->whereBetween('order_date', [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek()
            ]);
            break;
        case 'this_month':
            $query->whereMonth('order_date', now()->month)
                  ->whereYear('order_date', now()->year);
            break;
        case 'last_month':
            $query->whereMonth('order_date', now()->subMonth()->month)
                  ->whereYear('order_date', now()->subMonth()->year);
            break;
        case 'this_year':
            $query->whereYear('order_date', now()->year);
            break;
        case 'all_time':
            // No date filter for all time
            break;
    }

    // Calculate real statistics from database
    try {
        // Get basic statistics from sales orders
        $totalRevenue = (clone $query)->sum('total_amount') ?? 0;
        $totalSales = (clone $query)->count();
        
        // Get sales order IDs for item calculations
        $salesOrderIds = (clone $query)->pluck('order_id')->toArray();
        
        $totalItems = 0;
        $totalProfit = 0;
        
        // Calculate items and profit if we have orders
        if (!empty($salesOrderIds)) {
            $totalItems = \App\Models\SalesOrderItem::whereIn('order_id', $salesOrderIds)
                ->sum('quantity') ?? 0;
                
            // For profit, since we don't have cost data, use a simple calculation
            // This shows total item revenue (could be enhanced with actual cost data later)
            $totalProfit = \App\Models\SalesOrderItem::whereIn('order_id', $salesOrderIds)
                ->selectRaw('SUM(unit_price * quantity) as total')
                ->value('total') ?? 0;
                
            // Apply a simple profit margin estimate (e.g., 30% of revenue)
            $totalProfit = $totalProfit * 0.3; // Assuming 30% profit margin for demonstration
        }
        
    } catch (\Exception $e) {
        // If there's any error, fall back to zeros
        $totalRevenue = 0;
        $totalProfit = 0;
        $totalSales = 0;
        $totalItems = 0;
    }

    return response()->json([
        'total_revenue' => round($totalRevenue, 2),
        'total_profit' => round($totalProfit, 2),
        'total_sales' => $totalSales,
        'total_items' => $totalItems
    ]);
})->middleware(['auth', 'verified'])->name('dashboard.period-statistics');


// Dashboard Top Purchase Items AJAX Route
Route::get('dashboard/top-purchase-items', function (Illuminate\Http\Request $request) {
    if (!Auth::check()) {
        return response()->json(['items' => []], 401);
    }

    $period = $request->get('period', 'this_month');
    $query = \App\Models\PurchaseOrderItem::query();

    // Apply date filter based on period
    switch ($period) {
        case 'today':
            $query->whereHas('order', function ($q) {
                $q->whereDate('order_date', today());
            });
            break;
        case 'yesterday':
            $query->whereHas('order', function ($q) {
                $q->whereDate('order_date', today()->subDay());
            });
            break;
        case 'this_week':
            $query->whereHas('order', function ($q) {
                $q->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]);
            });
            break;
        case 'last_week':
            $query->whereHas('order', function ($q) {
                $q->whereBetween('order_date', [
                    now()->subWeek()->startOfWeek(),
                    now()->subWeek()->endOfWeek()
                ]);
            });
            break;
        case 'this_month':
            $query->whereHas('order', function ($q) {
                $q->whereMonth('order_date', now()->month)
                  ->whereYear('order_date', now()->year);
            });
            break;
        case 'last_month':
            $query->whereHas('order', function ($q) {
                $q->whereMonth('order_date', now()->subMonth()->month)
                  ->whereYear('order_date', now()->subMonth()->year);
            });
            break;
        case 'this_year':
            $query->whereHas('order', function ($q) {
                $q->whereYear('order_date', now()->year);
            });
            break;
        case 'all_time':
            // No date filter
            break;
    }

    $topPurchaseItems = $query->select('product_id')
        ->selectRaw('SUM(quantity_ordered) as total_quantity')
        ->selectRaw('SUM(quantity_ordered * unit_price) as total_cost')
        ->whereNotNull('product_id')
        ->with('product')
        ->groupBy('product_id')
        ->orderByDesc('total_quantity')
        ->limit(10)
        ->get()
        ->filter(function ($item) {
            return $item->product !== null;
        })
        ->map(function ($item) {
            return [
                'name' => $item->product->name ?? 'Unknown Product',
                'sku' => $item->product->sku ?? 'N/A',
                'size' => $item->product->size ?? null,
                'color' => $item->product->color ?? null,
                'quantity' => (int) $item->total_quantity,
                'cost' => (float) $item->total_cost,
                'image' => $item->product->image ?? null,
            ];
        })
        ->values();

    return response()->json(['items' => $topPurchaseItems]);
})->middleware(['auth', 'verified'])->name('dashboard.top-purchase-items');



    //Inventory Product Routes
    Route::prefix('inventory/products')->name('inventory.products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->middleware('permission:view products')->name('index');
        Route::get('/create', [ProductController::class, 'create'])->middleware('permission:create products')->name('create');
        Route::post('/', [ProductController::class, 'store'])->middleware('permission:create products')->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->middleware('permission:view products')->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->middleware('permission:edit products')->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->middleware('permission:edit products')->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->middleware('permission:delete products')->name('destroy');

        // Import routes
        // Route::get('/import/form', [ProductController::class, 'showImportForm'])->name('import');
        // Route::post('/import', [ProductController::class, 'import'])->name('import');
        // Route::get('/template/download', [ProductController::class, 'downloadTemplate'])->name('template');

        // API routes
        Route::post('/bulk-update-stock', [ProductController::class, 'bulkUpdateStock'])->middleware('permission:manage stock')->name('bulk-update-stock');
        Route::get('/alerts', [ProductController::class, 'getAlertsData'])->middleware('permission:view products')->name('alerts');
    });

    // Stock Adjustment Routes
    Route::post('/inventory/stock-adjustments', [StockAdjustmentController::class, 'store'])->middleware('permission:manage stock')->name('inventory.stock-adjustments.store');
    Route::get('/inventory/stock-adjustments/{product}/history', [StockAdjustmentController::class, 'history'])->middleware('permission:view inventory')->name('inventory.stock-adjustments.history');

    // Audit Log Routes - Admin only
    Route::middleware(['role:super_admin,admin'])->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
        Route::get('/audit-logs/module/{module}', [AuditLogController::class, 'moduleLog'])->name('audit-logs.module');
        Route::get('/audit-logs/statistics', [AuditLogController::class, 'statistics'])->name('audit-logs.statistics');
        Route::post('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    });

// CurrentUser UpdateInfo Routes 
Route::middleware(['auth', 'verified'])->group(function () {
    // Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.photo.remove');
    
    // Notifications Routes
    Route::get('/notifications-list', [\App\Http\Controllers\NotificationsController::class, 'index'])->name('notifications.list');
    Route::get('/notifications/latest', [\App\Http\Controllers\NotificationsController::class, 'getLatest'])->name('notifications.latest');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationsController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/delete-all', [\App\Http\Controllers\NotificationsController::class, 'destroyAll'])->name('notifications.destroy-all');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationsController::class, 'destroy'])->name('notifications.destroy');
    
    // User Management Routes (Admin and Super Admin Only)
    Route::middleware(['role:super_admin,admin'])->group(function () {
        Route::resource('user-management', UserManagementController::class);
        Route::post('user-management/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('user-management.toggle-status');
        Route::post('user-management/bulk-delete', [UserManagementController::class, 'bulkDelete'])->name('user-management.bulk-delete');
        Route::get('user-management-export', [UserManagementController::class, 'export'])->name('user-management.export');
    });

    // Customer Management Routes
    Route::prefix('sales/customers')->name('sales.customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->middleware('permission:view customers')->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->middleware('permission:create customers')->name('create');
        Route::post('/', [CustomerController::class, 'store'])->middleware('permission:create customers')->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->middleware('permission:view customers')->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->middleware('permission:edit customers')->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->middleware('permission:edit customers')->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->middleware('permission:delete customers')->name('destroy');

        // Import and Export routes
        Route::get('/import/form', [CustomerController::class, 'showImportForm'])->middleware('permission:create customers')->name('import');
        Route::post('/import/process', [CustomerController::class, 'import'])->middleware('permission:create customers')->name('import.process');
        Route::get('/template/download', [CustomerController::class, 'downloadTemplate'])->middleware('permission:view customers')->name('template');
        Route::get('/export', [CustomerController::class, 'export'])->middleware('permission:view customers')->name('export');

        // Status toggle and analytics
        Route::post('/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->middleware('permission:edit customers')->name('toggle-status');
        Route::get('/analytics', [CustomerController::class, 'analytics'])->middleware('permission:view customers')->name('analytics');
    });


    //Master Data Categories Routes
    Route::prefix('master_data/categories')->name('master_data.categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->middleware('permission:view categories')->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->middleware('permission:manage categories')->name('create');
        Route::post('/', [CategoryController::class, 'store'])->middleware('permission:manage categories')->name('store');
        Route::get('/{category}', [CategoryController::class, 'show'])->middleware('permission:view categories')->name('show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->middleware('permission:manage categories')->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->middleware('permission:manage categories')->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('permission:manage categories')->name('destroy');
    });

    //Master Data Brand Routes
    Route::prefix('master_data/brands')->name('master_data.brands.')->group(function () {
        Route::get('/', [BrandController::class, 'index'])->middleware('permission:view brands')->name('index');
        Route::get('/create', [BrandController::class, 'create'])->middleware('permission:manage brands')->name('create');
        Route::post('/', [BrandController::class, 'store'])->middleware('permission:manage brands')->name('store');
        Route::get('/{brand}', [BrandController::class, 'show'])->middleware('permission:view brands')->name('show');
        Route::get('/{brand}/edit', [BrandController::class, 'edit'])->middleware('permission:manage brands')->name('edit');
        Route::put('/{brand}', [BrandController::class, 'update'])->middleware('permission:manage brands')->name('update');
        Route::delete('/{brand}', [BrandController::class, 'destroy'])->middleware('permission:manage brands')->name('destroy');
    });

    //Master Data Stock Name Routes (Admin only)
    Route::prefix('master_data/stock_names')->name('master_data.stock_names.')->middleware('role:super_admin,admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\StockNameController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\StockNameController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\StockNameController::class, 'store'])->name('store');
        Route::get('/{stockName}', [\App\Http\Controllers\StockNameController::class, 'show'])->name('show');
        Route::get('/{stockName}/edit', [\App\Http\Controllers\StockNameController::class, 'edit'])->name('edit');
        Route::put('/{stockName}', [\App\Http\Controllers\StockNameController::class, 'update'])->name('update');
        Route::delete('/{stockName}', [\App\Http\Controllers\StockNameController::class, 'destroy'])->name('destroy');
    });

    //Master Data Tax & Discount Routes (Admin only)
    Route::prefix('master_data/tax_discounts')->name('master_data.tax_discounts.')->middleware('role:super_admin,admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\TaxDiscountController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\TaxDiscountController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\TaxDiscountController::class, 'store'])->name('store');
        Route::get('/{taxDiscount}', [\App\Http\Controllers\TaxDiscountController::class, 'show'])->name('show');
        Route::get('/{taxDiscount}/edit', [\App\Http\Controllers\TaxDiscountController::class, 'edit'])->name('edit');
        Route::put('/{taxDiscount}', [\App\Http\Controllers\TaxDiscountController::class, 'update'])->name('update');
        Route::delete('/{taxDiscount}', [\App\Http\Controllers\TaxDiscountController::class, 'destroy'])->name('destroy');
        
        // Additional routes
        Route::post('/{taxDiscount}/toggle-status', [\App\Http\Controllers\TaxDiscountController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/profit-breakdown', [\App\Http\Controllers\TaxDiscountController::class, 'profitBreakdown'])->name('profit-breakdown');
    });

    // API route for tax/discount calculation
    Route::post('/api/tax-discounts/calculate', [\App\Http\Controllers\TaxDiscountController::class, 'calculateForOrder'])->name('api.tax-discounts.calculate');


    // Product Movement Management Routes (Fast/Slow/Non-Moving)
    Route::prefix('inventory/product-movement')->name('inventory.product-movement.')->middleware('permission:view inventory')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProductMovementController::class, 'index'])->name('index');
        Route::get('/fast-moving', [\App\Http\Controllers\ProductMovementController::class, 'fastMoving'])->name('fast-moving');
        Route::get('/slow-moving', [\App\Http\Controllers\ProductMovementController::class, 'slowMoving'])->name('slow-moving');
        Route::get('/non-moving', [\App\Http\Controllers\ProductMovementController::class, 'nonMoving'])->name('non-moving');
        Route::get('/promotional', [\App\Http\Controllers\ProductMovementController::class, 'promotional'])->name('promotional');
        
        // Movement calculation - requires edit permission
        Route::post('/calculate-all', [\App\Http\Controllers\ProductMovementController::class, 'calculateMovements'])->middleware('permission:edit inventory')->name('calculate-all');
        Route::post('/{product}/calculate', [\App\Http\Controllers\ProductMovementController::class, 'calculateSingleMovement'])->middleware('permission:edit inventory')->name('calculate-single');
        
        // Promotional management - requires edit permission
        Route::post('/mark-for-promotion', [\App\Http\Controllers\ProductMovementController::class, 'markForPromotion'])->middleware('permission:edit inventory')->name('mark-for-promotion');
        Route::post('/unmark-from-promotion', [\App\Http\Controllers\ProductMovementController::class, 'unmarkFromPromotion'])->middleware('permission:edit inventory')->name('unmark-from-promotion');
        
        // Analytics and export
        Route::get('/analytics', [\App\Http\Controllers\ProductMovementController::class, 'analytics'])->name('analytics');
        Route::get('/export', [\App\Http\Controllers\ProductMovementController::class, 'export'])->name('export');
    });

    // Product Costing Management Routes (Admin only - affects pricing)
    Route::prefix('inventory/product-costing')->name('inventory.product-costing.')->middleware('role:super_admin,admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProductCostingController::class, 'index'])->name('index');
        Route::get('/{product}/edit', [\App\Http\Controllers\ProductCostingController::class, 'edit'])->name('edit');
        Route::put('/{product}', [\App\Http\Controllers\ProductCostingController::class, 'update'])->name('update');
        
        // Bulk operations
        Route::post('/bulk-update', [\App\Http\Controllers\ProductCostingController::class, 'bulkUpdate'])->name('bulk-update');
        
        // Special views
        Route::get('/low-margin', [\App\Http\Controllers\ProductCostingController::class, 'lowMargin'])->name('low-margin');
        Route::get('/negative-margin', [\App\Http\Controllers\ProductCostingController::class, 'negativeMargin'])->name('negative-margin');
        
        // AJAX endpoints
        Route::get('/{product}/suggest-price', [\App\Http\Controllers\ProductCostingController::class, 'suggestPrice'])->name('suggest-price');
        Route::get('/{product}/cost-breakdown', [\App\Http\Controllers\ProductCostingController::class, 'costBreakdown'])->name('cost-breakdown');
        Route::get('/analytics', [\App\Http\Controllers\ProductCostingController::class, 'analytics'])->name('analytics');
    });

    // Supplier Management Routes
    Route::prefix('master_data/suppliers')->name('master_data.suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->middleware('permission:view suppliers')->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->middleware('permission:create suppliers')->name('create');
        Route::post('/', [SupplierController::class, 'store'])->middleware('permission:create suppliers')->name('store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->middleware('permission:view suppliers')->name('show');
        Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->middleware('permission:edit suppliers')->name('edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->middleware('permission:edit suppliers')->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->middleware('permission:delete suppliers')->name('destroy');

        // Import and Export routes
        Route::get('/import/form', [SupplierController::class, 'showImportForm'])->middleware('permission:create suppliers')->name('import');
        Route::post('/import/process', [SupplierController::class, 'import'])->middleware('permission:create suppliers')->name('import.process');
        Route::get('/template/download', [SupplierController::class, 'downloadTemplate'])->middleware('permission:view suppliers')->name('template');
        Route::get('/export', [SupplierController::class, 'export'])->middleware('permission:view suppliers')->name('export');

        // Status toggle and analytics
        Route::post('/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->middleware('permission:edit suppliers')->name('toggle-status');
        Route::get('/analytics', [SupplierController::class, 'analytics'])->middleware('permission:view suppliers')->name('analytics');
        Route::get('/alerts', [SupplierController::class, 'getAlertsData'])->middleware('permission:view suppliers')->name('alerts');
    });

    // Stock Management Routes
    Route::prefix('inventory/product_stock_adjustment')->name('inventory.product_stock_adjustment.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->middleware('permission:view inventory')->name('index');
        Route::get('/create', [StockController::class, 'create'])->middleware('permission:create inventory')->name('create');
        Route::post('/', [StockController::class, 'store'])->middleware('permission:create inventory')->name('store');
        Route::get('/{product_stock_adjustment}', [StockController::class, 'show'])->middleware('permission:view inventory')->name('show');
        Route::get('/{product_stock_adjustment}/edit', [StockController::class, 'edit'])->middleware('permission:edit inventory')->name('edit');
        Route::put('/{product_stock_adjustment}', [StockController::class, 'update'])->middleware('permission:edit inventory')->name('update');
        Route::delete('/{product_stock_adjustment}', [StockController::class, 'destroy'])->middleware('permission:delete inventory')->name('destroy');

        // Stock movement operations
        Route::post('/{product_stocks}/confirm', [StockController::class, 'confirm'])->middleware('permission:manage stock')->name('confirm');
        
        // Transfer operations
        Route::get('/transfer/form', [StockController::class, 'showTransferForm'])->middleware('permission:manage stock')->name('transfer.form');
        Route::post('/transfer/process', [StockController::class, 'processTransfer'])->middleware('permission:manage stock')->name('transfer.process');
        
        // Waste/damage operations
        Route::get('/waste/form', [StockController::class, 'showWasteForm'])->middleware('permission:manage stock')->name('waste.form');
        Route::post('/waste/process', [StockController::class, 'processWaste'])->middleware('permission:manage stock')->name('waste.process');
        
        // Import and Export routes
        Route::get('/import/form', [StockController::class, 'showImportForm'])->middleware('permission:create inventory')->name('import');
        Route::post('/import/process', [StockController::class, 'import'])->middleware('permission:create inventory')->name('import.process');
        Route::get('/template/download', [StockController::class, 'downloadTemplate'])->middleware('permission:view inventory')->name('template');
        Route::get('/export', [StockController::class, 'export'])->middleware('permission:view inventory')->name('export');
        
        // Analytics and reporting
        Route::get('/analytics', [StockController::class, 'analytics'])->middleware('permission:view inventory')->name('analytics');
        Route::get('/product/{product}/history', [StockController::class, 'productHistory'])->middleware('permission:view inventory')->name('product.history');
        Route::get('/alerts', [StockController::class, 'getAlertsData'])->middleware('permission:view inventory')->name('alerts');
    });

    // Purchase Order Management Routes
    Route::prefix('purchases/purchase-orders')->name('purchases.purchase-orders.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->middleware('permission:view purchases')->name('index');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->middleware('permission:create purchases')->name('create');
        Route::post('/', [PurchaseOrderController::class, 'store'])->middleware('permission:create purchases')->name('store');
        Route::get('/{order}', [PurchaseOrderController::class, 'show'])->middleware('permission:view purchases')->name('show');
        Route::get('/{order}/edit', [PurchaseOrderController::class, 'edit'])->middleware('permission:edit purchases')->name('edit');
        Route::put('/{order}', [PurchaseOrderController::class, 'update'])->middleware('permission:edit purchases')->name('update');
        Route::delete('/{order}', [PurchaseOrderController::class, 'destroy'])->middleware('permission:delete purchases')->name('destroy');

        // Status management routes
        Route::post('/{order}/change-status', [PurchaseOrderController::class, 'changeStatus'])->middleware('permission:edit purchases')->name('change-status');

        // Analytics and Reports
        Route::get('/analytics', [PurchaseOrderController::class, 'analytics'])->middleware('permission:view purchases')->name('analytics');
        Route::get('/receiving-report', [PurchaseOrderController::class, 'receivingReport'])->middleware('permission:view purchases')->name('receiving-report');
    });

    // Purchase Receive Management Routes
    Route::prefix('purchases/purchase-receives')->name('purchases.purchase-receives.')->group(function () {
        Route::get('/', [PurchaseReceiveController::class, 'index'])->middleware('permission:view purchases')->name('index');
        Route::get('/create', [PurchaseReceiveController::class, 'create'])->middleware('permission:create purchases')->name('create');
        Route::post('/', [PurchaseReceiveController::class, 'store'])->middleware('permission:create purchases')->name('store');
        Route::get('/{receive}', [PurchaseReceiveController::class, 'show'])->middleware('permission:view purchases')->name('show');
        Route::get('/{receive}/edit', [PurchaseReceiveController::class, 'edit'])->middleware('permission:edit purchases')->name('edit');
        Route::put('/{receive}', [PurchaseReceiveController::class, 'update'])->middleware('permission:edit purchases')->name('update');
        Route::delete('/{receive}', [PurchaseReceiveController::class, 'destroy'])->middleware('permission:delete purchases')->name('destroy');

        // Status management routes
        Route::post('/{receive}/change-status', [PurchaseReceiveController::class, 'changeStatus'])->middleware('permission:edit purchases')->name('change-status');
        Route::post('/{receive}/short-close', [PurchaseReceiveController::class, 'shortClose'])->middleware('permission:edit purchases')->name('short-close');
        
        // AJAX routes
        Route::get('/purchase-order/{purchaseOrder}/items', [PurchaseReceiveController::class, 'getPurchaseOrderItems'])->middleware('permission:view purchases')->name('purchase-order-items');
        
        // Analytics
        Route::get('/analytics', [PurchaseReceiveController::class, 'analytics'])->middleware('permission:view purchases')->name('analytics');
    });

    // Purchase Returns Management Routes
    Route::prefix('purchases/purchase-returns')->name('purchases.purchase-returns.')->middleware('role:super_admin,admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\PurchaseReturnsController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\PurchaseReturnsController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\PurchaseReturnsController::class, 'store'])->name('store');
        Route::get('/{purchaseReturn}', [\App\Http\Controllers\PurchaseReturnsController::class, 'show'])->name('show');
        Route::get('/{purchaseReturn}/edit', [\App\Http\Controllers\PurchaseReturnsController::class, 'edit'])->name('edit');
        Route::put('/{purchaseReturn}', [\App\Http\Controllers\PurchaseReturnsController::class, 'update'])->name('update');
        Route::delete('/{purchaseReturn}', [\App\Http\Controllers\PurchaseReturnsController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{purchaseReturn}/approve', [\App\Http\Controllers\PurchaseReturnsController::class, 'approve'])->name('approve');
        Route::post('/{purchaseReturn}/reject', [\App\Http\Controllers\PurchaseReturnsController::class, 'reject'])->name('reject');
        Route::post('/{purchaseReturn}/mark-as-processed', [\App\Http\Controllers\PurchaseReturnsController::class, 'markAsProcessed'])->name('mark-as-processed');
        Route::post('/{purchaseReturn}/mark-as-refunded', [\App\Http\Controllers\PurchaseReturnsController::class, 'markAsRefunded'])->name('mark-as-refunded');

        // Bulk operations
        Route::post('/bulk-approve', [\App\Http\Controllers\PurchaseReturnsController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [\App\Http\Controllers\PurchaseReturnsController::class, 'bulkReject'])->name('bulk-reject');

        // Analytics
        Route::get('/analytics', [\App\Http\Controllers\PurchaseReturnsController::class, 'analytics'])->name('analytics');
    });
    
    // Purchase Payments Management Routes (Admin only - financial)
    Route::prefix('purchases/payments')->name('purchases.payments.')->middleware('role:super_admin,admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\PurchasePaymentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\PurchasePaymentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\PurchasePaymentController::class, 'store'])->name('store');
        Route::get('/{purchasePayment}', [\App\Http\Controllers\PurchasePaymentController::class, 'show'])->name('show');
        Route::get('/{purchasePayment}/edit', [\App\Http\Controllers\PurchasePaymentController::class, 'edit'])->name('edit');
        Route::put('/{purchasePayment}', [\App\Http\Controllers\PurchasePaymentController::class, 'update'])->name('update');
        Route::delete('/{purchasePayment}', [\App\Http\Controllers\PurchasePaymentController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{purchasePayment}/mark-completed', [\App\Http\Controllers\PurchasePaymentController::class, 'markCompleted'])->name('mark-completed');
        Route::post('/{purchasePayment}/mark-cancelled', [\App\Http\Controllers\PurchasePaymentController::class, 'markCancelled'])->name('mark-cancelled');
        
        // AJAX routes
        Route::get('/order/{orderId}/details', [\App\Http\Controllers\PurchasePaymentController::class, 'getOrderDetails'])->name('order-details');
        Route::get('/supplier/{supplierId}/bills', [\App\Http\Controllers\PurchasePaymentController::class, 'getSupplierBills'])->name('supplier-bills');
    });

    // Sales Order Management Routes
    Route::prefix('sales/orders')->name('sales.orders.')->group(function () {
        Route::get('/', [SalesOrderController::class, 'index'])->middleware('permission:view sales')->name('index');
        Route::get('/create', [SalesOrderController::class, 'create'])->middleware('permission:create sales')->name('create');
        Route::post('/', [SalesOrderController::class, 'store'])->middleware('permission:create sales')->name('store');
        Route::get('/analytics', [SalesOrderController::class, 'analytics'])->middleware('permission:view sales')->name('analytics');
        
        // Wildcard route should be last
        Route::get('/{order}', [SalesOrderController::class, 'show'])->middleware('permission:view sales')->name('show');
    });

    // POS (Point of Sale) - In-Store Purchase Routes
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [POSController::class, 'index'])->middleware('permission:create sales')->name('index');
        Route::get('/create', [POSController::class, 'create'])->middleware('permission:create sales')->name('create');
        Route::post('/', [POSController::class, 'store'])->middleware('permission:create sales')->name('store');
        Route::get('/{order}', [POSController::class, 'show'])->middleware('permission:view sales')->name('show');
        Route::patch('/{order}/complete-payment', [POSController::class, 'completePayment'])->middleware('permission:edit sales')->name('complete-payment');
        
        // AJAX endpoints
        Route::get('/search/customers', [POSController::class, 'searchCustomers'])->middleware('permission:view customers')->name('search.customers');
    });

    // Returns Management Routes (Admin only - requires approval)
    Route::prefix('sales/returns')->name('sales.returns.')->middleware('role:super_admin,admin,salesperson')->group(function () {
        Route::get('/', [ReturnsController::class, 'index'])->name('index');
        Route::get('/create', [ReturnsController::class, 'create'])->name('create');
        Route::post('/', [ReturnsController::class, 'store'])->name('store');
        Route::get('/{return}', [ReturnsController::class, 'show'])->name('show');
        Route::get('/{return}/edit', [ReturnsController::class, 'edit'])->name('edit');
        Route::put('/{return}', [ReturnsController::class, 'update'])->name('update');
        Route::delete('/{return}', [ReturnsController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{return}/approve', [ReturnsController::class, 'approve'])->name('approve');
        Route::post('/{return}/reject', [ReturnsController::class, 'reject'])->name('reject');
        Route::post('/{return}/mark-as-processed', [ReturnsController::class, 'markAsProcessed'])->name('mark-as-processed');

        // Bulk operations
        Route::post('/bulk-approve', [ReturnsController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [ReturnsController::class, 'bulkReject'])->name('bulk-reject');

        // Analytics
        Route::get('/analytics', [ReturnsController::class, 'analytics'])->name('analytics');
        
        // AJAX endpoints
        Route::get('/search/sales-orders', [ReturnsController::class, 'searchSalesOrders'])->name('search.sales-orders');
    });

    // Payment Management Routes (Admin only - financial)
    Route::prefix('sales/payments')->name('sales.payments.')->middleware('role:super_admin,admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\PaymentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\PaymentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [\App\Http\Controllers\PaymentController::class, 'show'])->name('show');
        Route::get('/{payment}/edit', [\App\Http\Controllers\PaymentController::class, 'edit'])->name('edit');
        Route::put('/{payment}', [\App\Http\Controllers\PaymentController::class, 'update'])->name('update');
        Route::delete('/{payment}', [\App\Http\Controllers\PaymentController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{payment}/mark-completed', [\App\Http\Controllers\PaymentController::class, 'markCompleted'])->name('mark-completed');
        Route::post('/{payment}/mark-cancelled', [\App\Http\Controllers\PaymentController::class, 'markCancelled'])->name('mark-cancelled');
    });

    // Exchange Management Routes
    Route::prefix('sales/exchanges')->name('sales.exchanges.')->group(function () {
        Route::get('/', [ExchangeController::class, 'index'])->name('index');
        Route::get('/create', [ExchangeController::class, 'create'])->name('create');
        Route::post('/', [ExchangeController::class, 'store'])->name('store');
        Route::get('/{exchange}', [ExchangeController::class, 'show'])->name('show');
        Route::get('/{exchange}/edit', [ExchangeController::class, 'edit'])->name('edit');
        Route::put('/{exchange}', [ExchangeController::class, 'update'])->name('update');
        Route::delete('/{exchange}', [ExchangeController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{exchange}/approve', [ExchangeController::class, 'approve'])->name('approve');
        Route::post('/{exchange}/start-processing', [ExchangeController::class, 'startProcessing'])->name('start-processing');
        Route::post('/{exchange}/complete', [ExchangeController::class, 'complete'])->name('complete');
        Route::post('/{exchange}/cancel', [ExchangeController::class, 'cancel'])->name('cancel');

        // Analytics
        Route::get('/analytics', [ExchangeController::class, 'analytics'])->name('analytics');
        
        // AJAX endpoint for sales order search
        Route::get('/search/sales-orders', [ExchangeController::class, 'searchSalesOrders'])->name('search.sales-orders');
    });

    // Package Management Routes
    Route::prefix('sales/packages')->name('sales.packages.')->group(function () {
        Route::get('/', [PackageController::class, 'index'])->name('index');
        Route::get('/create', [PackageController::class, 'create'])->name('create');
        Route::post('/', [PackageController::class, 'store'])->name('store');
        Route::get('/{package}', [PackageController::class, 'show'])->name('show');
        Route::get('/{package}/edit', [PackageController::class, 'edit'])->name('edit');
        Route::put('/{package}', [PackageController::class, 'update'])->name('update');
        Route::delete('/{package}', [PackageController::class, 'destroy'])->name('destroy');

        // Import and Export routes
        Route::get('/import/form', [PackageController::class, 'showImportForm'])->name('import');
        Route::post('/import/process', [PackageController::class, 'import'])->name('import.process');
        Route::get('/template/download', [PackageController::class, 'downloadTemplate'])->name('template');

        // Status management and operations
        Route::post('/{package}/toggle-status', [PackageController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/bulk-update-stock', [PackageController::class, 'bulkUpdateStock'])->name('bulk-update-stock');

        // Analytics and alerts
        Route::get('/analytics', [PackageController::class, 'analytics'])->name('analytics');
        Route::get('/alerts', [PackageController::class, 'getAlertsData'])->name('alerts');
    });

    // Shipment Management Routes
    Route::prefix('sales/shipments')->name('sales.shipments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ShipmentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\ShipmentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\ShipmentController::class, 'store'])->name('store');
        Route::get('/{shipment}', [\App\Http\Controllers\ShipmentController::class, 'show'])->name('show');
        Route::get('/{shipment}/edit', [\App\Http\Controllers\ShipmentController::class, 'edit'])->name('edit');
        Route::put('/{shipment}', [\App\Http\Controllers\ShipmentController::class, 'update'])->name('update');
        Route::delete('/{shipment}', [\App\Http\Controllers\ShipmentController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{shipment}/change-status', [\App\Http\Controllers\ShipmentController::class, 'changeStatus'])->name('change-status');
        Route::post('/{shipment}/ship', [\App\Http\Controllers\ShipmentController::class, 'ship'])->name('ship');
        Route::post('/{shipment}/add-tracking', [\App\Http\Controllers\ShipmentController::class, 'addTracking'])->name('add-tracking');
        Route::post('/{shipment}/process-inventory', [\App\Http\Controllers\ShipmentController::class, 'processInventory'])->name('process-inventory');

        // Special operations
        Route::post('/create-from-order/{salesOrder}', [\App\Http\Controllers\ShipmentController::class, 'createFromOrder'])->name('create-from-order');
        
        
        // Analytics and attention
        Route::get('/analytics', [\App\Http\Controllers\ShipmentController::class, 'analytics'])->name('analytics');
        Route::get('/attention', [\App\Http\Controllers\ShipmentController::class, 'attention'])->name('attention');
        
        // Import and Export routes
        Route::get('/import/form', [\App\Http\Controllers\ShipmentController::class, 'showImportForm'])->name('import');
        Route::post('/import/process', [\App\Http\Controllers\ShipmentController::class, 'import'])->name('import.process');
        Route::get('/template/download', [\App\Http\Controllers\ShipmentController::class, 'downloadTemplate'])->name('template');
        Route::get('/export', [\App\Http\Controllers\ShipmentController::class, 'export'])->name('export');
    });

    // Reports Management Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        
        // Report types
        Route::get('/sales', [\App\Http\Controllers\ReportController::class, 'sales'])->name('sales');
        Route::get('/purchases', [\App\Http\Controllers\ReportController::class, 'purchases'])->name('purchases');
        Route::get('/inventory', [\App\Http\Controllers\ReportController::class, 'inventory'])->name('inventory');
        Route::get('/financial', [\App\Http\Controllers\ReportController::class, 'financial'])->name('financial');
        Route::get('/movement', [\App\Http\Controllers\ReportController::class, 'movement'])->name('movement');
        Route::get('/product-movement', [\App\Http\Controllers\ReportController::class, 'productMovement'])->name('product-movement');
        Route::get('/blocked', [\App\Http\Controllers\ReportController::class, 'blocked'])->name('blocked');
    Route::get('/reorder', [\App\Http\Controllers\ReportController::class, 'reorder'])->name('reorder');
    Route::post('/reorder', [\App\Http\Controllers\ReportController::class, 'reorderProduct'])->name('reorder.create');
    Route::post('/reorder/bulk', [\App\Http\Controllers\ReportController::class, 'bulkReorderProducts'])->name('reorder.bulk');
        Route::get('/critical', [\App\Http\Controllers\ReportController::class, 'critical'])->name('critical');
        
        // Export routes
        Route::get('/{reportType}/preview-pdf', [\App\Http\Controllers\ReportController::class, 'previewPdf'])->name('preview-pdf');
        Route::get('/{reportType}/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/{reportType}/export-excel', [\App\Http\Controllers\ReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/{reportType}/data', [\App\Http\Controllers\ReportController::class, 'getData'])->name('data');
    });

    // Settings Management Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SettingsController::class, 'index'])->name('index');
        
        // Category-specific settings pages
        Route::get('/general', [\App\Http\Controllers\SettingsController::class, 'general'])->name('general');
        Route::post('/general', [\App\Http\Controllers\SettingsController::class, 'updateGeneral'])->name('general.update');
        
        Route::get('/financial', [\App\Http\Controllers\SettingsController::class, 'financial'])->name('financial');
        Route::post('/financial', [\App\Http\Controllers\SettingsController::class, 'updateFinancial'])->name('financial.update');
        
        Route::get('/inventory', [\App\Http\Controllers\SettingsController::class, 'inventory'])->name('inventory');
        Route::post('/inventory', [\App\Http\Controllers\SettingsController::class, 'updateInventory'])->name('inventory.update');
        
        Route::get('/sales', [\App\Http\Controllers\SettingsController::class, 'sales'])->name('sales');
        Route::post('/sales', [\App\Http\Controllers\SettingsController::class, 'updateSales'])->name('sales.update');
        
        Route::get('/notifications', [\App\Http\Controllers\SettingsController::class, 'notifications'])->name('notifications');
        Route::post('/notifications', [\App\Http\Controllers\SettingsController::class, 'updateNotifications'])->name('notifications.update');
        
        // Utility routes
        Route::post('/initialize-defaults', [\App\Http\Controllers\SettingsController::class, 'initializeDefaults'])->name('initialize-defaults');
        Route::get('/export', [\App\Http\Controllers\SettingsController::class, 'export'])->name('export');
        Route::post('/clear-cache', [\App\Http\Controllers\SettingsController::class, 'clearCache'])->name('clear-cache');
        
        // API routes
        Route::get('/api/{category?}', [\App\Http\Controllers\SettingsController::class, 'getSettings'])->name('api');
        
        // Terms and Conditions Management
        Route::prefix('terms')->name('terms.')->group(function () {
            Route::get('/', [\App\Http\Controllers\TermsAndConditionsController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\TermsAndConditionsController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\TermsAndConditionsController::class, 'store'])->name('store');
            Route::get('/{term}', [\App\Http\Controllers\TermsAndConditionsController::class, 'show'])->name('show');
            Route::get('/{term}/edit', [\App\Http\Controllers\TermsAndConditionsController::class, 'edit'])->name('edit');
            Route::put('/{term}', [\App\Http\Controllers\TermsAndConditionsController::class, 'update'])->name('update');
            Route::delete('/{term}', [\App\Http\Controllers\TermsAndConditionsController::class, 'destroy'])->name('destroy');
            Route::post('/{term}/toggle-active', [\App\Http\Controllers\TermsAndConditionsController::class, 'toggleActive'])->name('toggle-active');
        });
    });
    
    // User Terms Acceptance Routes (for logged-in users)
    Route::get('/accept-terms', [\App\Http\Controllers\TermsAndConditionsController::class, 'showAcceptanceForm'])->name('terms.accept.form');
    Route::post('/accept-terms', [\App\Http\Controllers\TermsAndConditionsController::class, 'acceptTerms'])->name('terms.accept');
});

// Public Shipment Tracking Route (no authentication required)
Route::get('/sales/shipments/tracking', [\App\Http\Controllers\ShipmentController::class, 'tracking'])->name('sales.shipments.tracking');

// Public Terms and Conditions Route (no authentication required)
Route::get('/terms/{slug}', [\App\Http\Controllers\TermsAndConditionsController::class, 'showPublic'])->name('terms.public');

// Bank Transfer Payment Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Customer routes - submit bank transfer payment proof
    Route::get('/bank-transfer-payments/create', [BankTransferPaymentController::class, 'create'])
        ->name('bank-transfer-payments.create');
    Route::post('/bank-transfer-payments', [BankTransferPaymentController::class, 'store'])
        ->name('bank-transfer-payments.store');
    
    // Admin routes - review and manage bank transfer payments
    Route::prefix('admin/bank-transfer-payments')->name('admin.bank-transfer-payments.')->group(function () {
        Route::get('/', [BankTransferPaymentController::class, 'index'])
            ->name('index');
        Route::post('/{id}/confirm', [BankTransferPaymentController::class, 'confirm'])
            ->name('confirm');
        Route::post('/{id}/cancel', [BankTransferPaymentController::class, 'cancel'])
            ->name('cancel');
        Route::get('/{id}/proof', [BankTransferPaymentController::class, 'showProof'])
            ->name('proof');
    });
});

require __DIR__ . '/auth.php';
