<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseReceiveController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Debug route to check authentication and session
// Route::get('/debug', function () {
//     return [
//         'authenticated' => auth()->check(),
//         'user' => auth()->user(),
//         'session_id' => session()->getId(),
//         'intended_url' => session('url.intended'),
//         'session_driver' => config('session.driver'),
//         'session_domain' => config('session.domain'),
//         'csrf_token' => csrf_token(),
//         'session_lifetime' => config('session.lifetime'),
//         'app_key' => config('app.key') ? 'Set' : 'Not set',
//         'session_table_exists' => \Schema::hasTable('sessions'),
//         'session_data' => session()->all(),
//     ];
// });

// Test login route
// Route::get('/test-login', function () {
//     $user = \App\Models\User::first();
//     if ($user) {
//         auth()->login($user);
//         return redirect()->route('dashboard')->with('success', 'Test login successful!');
//     }
//     return 'No users found';
// });

// CSRF Test routes
Route::get('/csrf-test', function () {
    return view('csrf-test');
});

Route::post('/csrf-test', function () {
    return response()->json(['success' => true, 'message' => 'CSRF token is working!']);
});

// Test dashboard statistics
Route::get('/test-stats', function () {
    try {
        $inventoryStats = [
            'total_products' => \App\Models\Product::count(),
            'low_stock_products' => \App\Models\Product::where('quantity', '<=', 10)->count(),
            'out_of_stock_products' => \App\Models\Product::where('quantity', '<=', 0)->count(),
            'total_inventory_value' => \App\Models\Product::selectRaw('SUM(quantity * price) as total')->value('total') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Dashboard statistics working correctly!',
            'data' => $inventoryStats
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'error' => $e->getTraceAsString()
        ]);
    }
});


// Main Route
Route::get('dashboard', function () {
    // Debug: Check if user is authenticated
    if (!auth()->check()) {
        return redirect()->route('login')->with('error', 'You must be logged in to access the dashboard.');
    }

    // Get comprehensive statistics for dashboard with error handling

    // Inventory Statistics
    try {
        $inventoryStats = [
            'total_products' => \App\Models\Product::count(),
            'active_products' => \App\Models\Product::count(), 
            'low_stock_products' => \App\Models\Product::where('quantity', '<=', 10)->count(), // Low stock threshold of 10
            'out_of_stock_products' => \App\Models\Product::where('quantity', '<=', 0)->count(),
            'total_inventory_value' => \App\Models\Product::selectRaw('SUM(quantity * price) as total')->value('total') ?? 0,
        ];
    } catch (\Exception $e) {
        $inventoryStats = [
            'total_products' => 0,
            'active_products' => 0,
            'low_stock_products' => 0,
            'out_of_stock_products' => 0,
            'total_inventory_value' => 0,
        ];
    }

    // Sales Statistics
    try {
        $salesStats = [
            'total_sales' => \App\Models\Sale::count(),
            'today_sales' => \App\Models\Sale::whereDate('created_at', today())->count(),
            'this_month_sales' => \App\Models\Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'total_sales_value' => \App\Models\Sale::sum('total_amount') ?? 0,
            'today_sales_value' => \App\Models\Sale::whereDate('created_at', today())->sum('total_amount') ?? 0,
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
            'total_purchases' => \App\Models\Purchase::count(),
            'this_month_purchases' => \App\Models\Purchase::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'total_purchase_value' => \App\Models\Purchase::sum('total_amount') ?? 0,
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
            'total_shipments' => \App\Models\Shipment::count(),
            'pending_shipments' => \App\Models\Shipment::pending()->count(),
            'shipped_shipments' => \App\Models\Shipment::shipped()->count(),
            'in_transit_shipments' => \App\Models\Shipment::inTransit()->count(),
            'delivered_shipments' => \App\Models\Shipment::delivered()->count(),
            'overdue_shipments' => \App\Models\Shipment::overdue()->count(),
            'today_shipments' => \App\Models\Shipment::today()->count(),
            'this_week_shipments' => \App\Models\Shipment::thisWeek()->count(),
            'this_month_shipments' => \App\Models\Shipment::thisMonth()->count(),
            'total_shipping_cost' => \App\Models\Shipment::sum('total_shipping_cost') ?? 0,
            'active_shipments' => \App\Models\Shipment::whereNotIn('status', ['delivered', 'cancelled', 'returned'])->count(),
        ];
    } catch (\Exception $e) {
        $shipmentStats = [
            'total_shipments' => 0,
            'pending_shipments' => 0,
            'shipped_shipments' => 0,
            'in_transit_shipments' => 0,
            'delivered_shipments' => 0,
            'overdue_shipments' => 0,
            'today_shipments' => 0,
            'this_week_shipments' => 0,
            'this_month_shipments' => 0,
            'total_shipping_cost' => 0,
            'active_shipments' => 0,
        ];
    }

    return view('dashboard', compact(
        'inventoryStats',
        'salesStats',
        'customerStats',
        'purchaseStats',
        'returnStats',
        'purchaseReturnStats',
        'supplierStats',
        'shipmentStats'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

// CurrentUser UpdateInfo Routes 
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Management Routes
    Route::resource('user-management', UserManagementController::class);

    // Customer Management Routes
    Route::prefix('sales/customers')->name('sales.customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');

        // Import and Export routes
        Route::get('/import/form', [CustomerController::class, 'showImportForm'])->name('import');
        Route::post('/import/process', [CustomerController::class, 'import'])->name('import.process');
        Route::get('/template/download', [CustomerController::class, 'downloadTemplate'])->name('template');
        Route::get('/export', [CustomerController::class, 'export'])->name('export');

        // Status toggle and analytics
        Route::post('/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/analytics', [CustomerController::class, 'analytics'])->name('analytics');
    });

    //Inventory Routes for products
    Route::prefix('inventory/products')->name('inventory.products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');

        // Import routes
        Route::get('/import/form', [ProductController::class, 'showImportForm'])->name('import');
        Route::post('/import', [ProductController::class, 'import'])->name('import');
        Route::get('/template/download', [ProductController::class, 'downloadTemplate'])->name('template');

        // API routes
        Route::post('/bulk-update-stock', [ProductController::class, 'bulkUpdateStock'])->name('bulk-update-stock');
        Route::get('/alerts', [ProductController::class, 'getAlertsData'])->name('alerts');
    });

    // Supplier Management Routes
    Route::prefix('purchases/suppliers')->name('purchases.suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
        Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');

        // Import and Export routes
        Route::get('/import/form', [SupplierController::class, 'showImportForm'])->name('import');
        Route::post('/import/process', [SupplierController::class, 'import'])->name('import.process');
        Route::get('/template/download', [SupplierController::class, 'downloadTemplate'])->name('template');
        Route::get('/export', [SupplierController::class, 'export'])->name('export');

        // Status toggle and analytics
        Route::post('/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/analytics', [SupplierController::class, 'analytics'])->name('analytics');
        Route::get('/alerts', [SupplierController::class, 'getAlertsData'])->name('alerts');
    });

    // Stock Management Routes
    Route::prefix('inventory/product_stock_adjustment')->name('inventory.product_stock_adjustment.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('/create', [StockController::class, 'create'])->name('create');
        Route::post('/', [StockController::class, 'store'])->name('store');
        Route::get('/{product_stock_adjustment}', [StockController::class, 'show'])->name('show');
        Route::get('/{product_stock_adjustment}/edit', [StockController::class, 'edit'])->name('edit');
        Route::put('/{product_stock_adjustment}', [StockController::class, 'update'])->name('update');
        Route::delete('/{product_stock_adjustment}', [StockController::class, 'destroy'])->name('destroy');

        // Stock movement operations
        Route::post('/{product_stocks}/confirm', [StockController::class, 'confirm'])->name('confirm');
        
        // Transfer operations
        Route::get('/transfer/form', [StockController::class, 'showTransferForm'])->name('transfer.form');
        Route::post('/transfer/process', [StockController::class, 'processTransfer'])->name('transfer.process');
        
        // Waste/damage operations
        Route::get('/waste/form', [StockController::class, 'showWasteForm'])->name('waste.form');
        Route::post('/waste/process', [StockController::class, 'processWaste'])->name('waste.process');
        
        // Import and Export routes
        Route::get('/import/form', [StockController::class, 'showImportForm'])->name('import');
        Route::post('/import/process', [StockController::class, 'import'])->name('import.process');
        Route::get('/template/download', [StockController::class, 'downloadTemplate'])->name('template');
        Route::get('/export', [StockController::class, 'export'])->name('export');
        
        // Analytics and reporting
        Route::get('/analytics', [StockController::class, 'analytics'])->name('analytics');
        Route::get('/product/{product}/history', [StockController::class, 'productHistory'])->name('product.history');
        Route::get('/alerts', [StockController::class, 'getAlertsData'])->name('alerts');
    });

    // Purchase Order Management Routes
    Route::prefix('purchases/purchase-orders')->name('purchases.purchase-orders.')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
        Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get('/{order}', [PurchaseOrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [PurchaseOrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [PurchaseOrderController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{order}/change-status', [PurchaseOrderController::class, 'changeStatus'])->name('change-status');
        Route::post('/{order}/receive-items', [PurchaseOrderController::class, 'receiveItems'])->name('receive-items');

        // Analytics and Reports
        Route::get('/analytics', [PurchaseOrderController::class, 'analytics'])->name('analytics');
        Route::get('/receiving-report', [PurchaseOrderController::class, 'receivingReport'])->name('receiving-report');
    });

    // Purchase Receive Management Routes
    Route::prefix('purchases/purchase-receives')->name('purchases.purchase-receives.')->group(function () {
        Route::get('/', [PurchaseReceiveController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseReceiveController::class, 'create'])->name('create');
        Route::post('/', [PurchaseReceiveController::class, 'store'])->name('store');
        Route::get('/{receive}', [PurchaseReceiveController::class, 'show'])->name('show');
        Route::get('/{receive}/edit', [PurchaseReceiveController::class, 'edit'])->name('edit');
        Route::put('/{receive}', [PurchaseReceiveController::class, 'update'])->name('update');
        Route::delete('/{receive}', [PurchaseReceiveController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{receive}/change-status', [PurchaseReceiveController::class, 'changeStatus'])->name('change-status');
        
        // AJAX routes
        Route::get('/purchase-order/{purchaseOrder}/items', [PurchaseReceiveController::class, 'getPurchaseOrderItems'])->name('purchase-order-items');
        
        // Analytics
        Route::get('/analytics', [PurchaseReceiveController::class, 'analytics'])->name('analytics');
    });

    // Purchase Returns Management Routes
    Route::prefix('purchases/purchase-returns')->name('purchases.purchase-returns.')->group(function () {
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

    // Sales Order Management Routes
    Route::prefix('sales/orders')->name('sales.orders.')->group(function () {
        Route::get('/', [SalesOrderController::class, 'index'])->name('index');
        Route::get('/create', [SalesOrderController::class, 'create'])->name('create');
        Route::post('/', [SalesOrderController::class, 'store'])->name('store');
        Route::get('/{order}', [SalesOrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [SalesOrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [SalesOrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [SalesOrderController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{order}/change-status', [SalesOrderController::class, 'changeStatus'])->name('change-status');
        Route::post('/{order}/fulfill', [SalesOrderController::class, 'fulfill'])->name('fulfill');

        // Analytics
        Route::get('/analytics', [SalesOrderController::class, 'analytics'])->name('analytics');
    });

    // Returns Management Routes
    Route::prefix('sales/returns')->name('sales.returns.')->group(function () {
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
    });

    // Payment Management Routes
    Route::prefix('sales/payments')->name('sales.payments.')->group(function () {
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

    // Invoice Management Routes
    Route::prefix('sales/invoices')->name('sales.invoices.')->group(function () {
        Route::get('/', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\InvoiceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [\App\Http\Controllers\InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{invoice}/change-status', [\App\Http\Controllers\InvoiceController::class, 'changeStatus'])->name('change-status');
        Route::post('/{invoice}/mark-as-sent', [\App\Http\Controllers\InvoiceController::class, 'markAsSent'])->name('mark-as-sent');
        Route::post('/{invoice}/record-payment', [\App\Http\Controllers\InvoiceController::class, 'recordPayment'])->name('record-payment');

        // Special operations
        Route::post('/create-from-order/{salesOrder}', [\App\Http\Controllers\InvoiceController::class, 'createFromOrder'])->name('create-from-order');
        Route::get('/{invoice}/duplicate', [\App\Http\Controllers\InvoiceController::class, 'duplicate'])->name('duplicate');
        Route::get('/{invoice}/generate-pdf', [\App\Http\Controllers\InvoiceController::class, 'generatePdf'])->name('generate-pdf');
        Route::post('/{invoice}/send-email', [\App\Http\Controllers\InvoiceController::class, 'sendEmail'])->name('send-email');
        
        // Views and reports
        Route::get('/overdue', [\App\Http\Controllers\InvoiceController::class, 'overdue'])->name('overdue');
        Route::post('/update-overdue-statuses', [\App\Http\Controllers\InvoiceController::class, 'updateOverdueStatuses'])->name('update-overdue-statuses');
        
        // Analytics
        Route::get('/analytics', [\App\Http\Controllers\InvoiceController::class, 'analytics'])->name('analytics');
    });

    // Inventory Threshold Management Routes
    Route::prefix('inventory/thresholds')->name('inventory.thresholds.')->group(function () {
        // Main threshold management (products with thresholds)
        Route::get('/', [\App\Http\Controllers\InventoryThresholdController::class, 'index'])->name('index');
        Route::get('/{product}', [\App\Http\Controllers\InventoryThresholdController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [\App\Http\Controllers\InventoryThresholdController::class, 'edit'])->name('edit');
        Route::put('/{product}', [\App\Http\Controllers\InventoryThresholdController::class, 'update'])->name('update');
        
        // Bulk operations
        Route::post('/bulk-update', [\App\Http\Controllers\InventoryThresholdController::class, 'bulkUpdate'])->name('bulk-update');
        
        // Alert management
        Route::get('/alerts', [\App\Http\Controllers\InventoryThresholdController::class, 'alerts'])->name('alerts');
        Route::get('/alerts/{alert}', [\App\Http\Controllers\InventoryThresholdController::class, 'showAlert'])->name('alerts.show');
        Route::patch('/alerts/{alert}/resolve', [\App\Http\Controllers\InventoryThresholdController::class, 'resolveAlert'])->name('alerts.resolve');
        Route::post('/alerts/bulk-resolve', [\App\Http\Controllers\InventoryThresholdController::class, 'bulkResolveAlerts'])->name('alerts.bulk-resolve');
        
        // Utility routes
        Route::post('/run-check', [\App\Http\Controllers\InventoryThresholdController::class, 'runThresholdCheck'])->name('run-check');
        Route::get('/analytics', [\App\Http\Controllers\InventoryThresholdController::class, 'analytics'])->name('analytics');
        Route::get('/export', [\App\Http\Controllers\InventoryThresholdController::class, 'export'])->name('export');
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
    });
});

// Public Shipment Tracking Route (no authentication required)
Route::get('/sales/shipments/tracking', [\App\Http\Controllers\ShipmentController::class, 'tracking'])->name('sales.shipments.tracking');

require __DIR__ . '/auth.php';
