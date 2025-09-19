<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryAlert;
use App\Models\InventoryAuditLog;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class InventoryThresholdService
{
    /**
     * Get paginated products with threshold information for listing.
     */
    public function getPaginatedProducts(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::query();

        // Apply search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_brand', 'like', "%{$search}%");
            });
        }

        // Apply filters
        if ($status = $request->get('status')) {
            switch ($status) {
                case 'low_stock':
                    $query->where(function ($q) {
                        $q->whereColumn('quantity', '<=', 'reorder_level')
                          ->whereNotNull('reorder_level');
                    });
                    break;
                case 'critical_stock':
                    $query->where(function ($q) {
                        $q->whereColumn('quantity', '<=', 'critical_level')
                          ->whereNotNull('critical_level');
                    });
                    break;
                case 'overstocked':
                    $query->where(function ($q) {
                        $q->whereColumn('quantity', '>', 'ceiling_level')
                          ->whereNotNull('ceiling_level');
                    });
                    break;
                case 'no_thresholds':
                    $query->whereNull('reorder_level')
                          ->whereNull('critical_level')
                          ->whereNull('ceiling_level');
                    break;
            }
        }

        // Apply sorting
        $sortField = $request->get('sort', 'product_name');
        $sortOrder = $request->get('order', 'asc');
        
        if (in_array($sortField, ['product_name', 'product_brand', 'quantity', 'reorder_level', 'critical_level'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->with(['preferredSupplier'])->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for product threshold listing.
     */
    public function getFilterOptions(): array
    {
        return [
            'suppliers' => Supplier::orderBy('supplier_name')->get(['supplier_id', 'supplier_name']),
            'status_options' => [
                'low_stock' => 'Low Stock',
                'critical_stock' => 'Critical Stock',
                'overstocked' => 'Overstocked',
                'no_thresholds' => 'No Thresholds Set'
            ]
        ];
    }

    /**
     * Get threshold data for a specific product.
     */
    public function getProductThresholdData(Product $product): array
    {
        $stockStatus = $product->getStockStatus();
        
        return [
            'stock_status' => $stockStatus,
            'suggested_order_quantity' => $product->getSuggestedOrderQuantity(),
            'days_until_stockout' => $this->calculateDaysUntilStockout($product),
            'reorder_needed' => $product->needsReordering(),
            'last_threshold_check' => $product->last_threshold_check,
            'threshold_coverage' => $this->calculateThresholdCoverage($product)
        ];
    }

    /**
     * Get recent alerts for a product.
     */
    public function getProductRecentAlerts(Product $product, int $limit = 10): Collection
    {
        return $product->inventoryAlerts()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get suppliers for dropdown options.
     */
    public function getSuppliers(): Collection
    {
        return Supplier::orderBy('supplier_name')
            ->get(['supplier_id', 'supplier_name']);
    }

    /**
     * Get paginated alerts with filters.
     */
    public function getPaginatedAlerts(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = InventoryAlert::with(['product']);

        // Apply search
        if ($search = $request->get('search')) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_brand', 'like', "%{$search}%");
            });
        }

        // Apply filters
        if ($alertType = $request->get('alert_type')) {
            $query->where('alert_type', $alertType);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($severity = $request->get('severity')) {
            $query->where('severity', $severity);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortField, ['created_at', 'severity', 'alert_type', 'status'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for alerts.
     */
    public function getAlertFilterOptions(): array
    {
        return [
            'alert_types' => [
                'out_of_stock' => 'Out of Stock',
                'critical_stock' => 'Critical Stock',
                'low_stock' => 'Low Stock',
                'overstock' => 'Overstocked',
                'reorder_needed' => 'Reorder Needed'
            ],
            'severities' => [
                'urgent' => 'Urgent',
                'critical' => 'Critical',
                'warning' => 'Warning',
                'info' => 'Info'
            ],
            'statuses' => [
                'active' => 'Active',
                'resolved' => 'Resolved',
                'acknowledged' => 'Acknowledged'
            ]
        ];
    }

    /**
     * Resolve a single alert.
     */
    public function resolveAlert(InventoryAlert $alert, User $user): InventoryAlert
    {
        $alert->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => $user->id
        ]);

        // Log the resolution
        if ($alert->product) {
            InventoryAuditLog::logAction(
                $alert->product,
                'alert_resolved',
                "Manually resolved {$alert->alert_type} alert for {$alert->product->product_name}",
                ['status' => 'active'],
                ['status' => 'resolved'],
                $user,
                $alert,
                'Manual'
            );
        }

        return $alert->fresh();
    }

    /**
     * Bulk resolve multiple alerts.
     */
    public function bulkResolveAlerts(array $alertIds, User $user): array
    {
        $resolved = 0;
        $errors = [];

        DB::transaction(function () use ($alertIds, $user, &$resolved, &$errors) {
            foreach ($alertIds as $alertId) {
                try {
                    $alert = InventoryAlert::findOrFail($alertId);
                    $this->resolveAlert($alert, $user);
                    $resolved++;
                } catch (\Exception $e) {
                    $errors[] = "Alert {$alertId}: " . $e->getMessage();
                }
            }
        });

        return ['resolved' => $resolved, 'errors' => $errors];
    }

    /**
     * Run threshold checks and return results.
     */
    public function runThresholdChecks(): array
    {
        $results = $this->checkAllThresholds();
        
        return [
            'products_checked' => $results['products_checked'],
            'alerts_generated' => $results['alerts_created'],
            'alerts_resolved' => $results['alerts_resolved'],
            'errors_count' => count($results['errors']),
            'success' => empty($results['errors'])
        ];
    }

    /**
     * Get analytics data for dashboard.
     */
    public function getAnalytics(Request $request): array
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        return [
            'summary' => $this->getThresholdStatistics(),
            'products_needing_attention' => $this->getProductsNeedingAttention(),
            'alert_trends' => $this->getAlertTrends($dateFrom, $dateTo),
            'top_problematic_products' => $this->getTopProblematicProducts(10)
        ];
    }

    /**
     * Calculate days until stockout based on sales velocity.
     */
    private function calculateDaysUntilStockout(Product $product): ?int
    {
        if ($product->quantity <= 0) {
            return 0;
        }

        // Calculate average daily sales over last 30 days
        $thirtyDaysAgo = now()->subDays(30);
        $totalSold = $product->sales()
            ->where('date', '>=', $thirtyDaysAgo)
            ->sum('quantity');
        
        $averageDailySales = $totalSold / 30;
        
        if ($averageDailySales <= 0) {
            return null; // No sales data
        }
        
        return (int) ceil($product->quantity / $averageDailySales);
    }

    /**
     * Calculate threshold coverage percentage.
     */
    private function calculateThresholdCoverage(Product $product): int
    {
        $thresholdFields = ['reorder_level', 'critical_level', 'ceiling_level', 'floor_level'];
        $setThresholds = 0;
        
        foreach ($thresholdFields as $field) {
            if ($product->$field !== null) {
                $setThresholds++;
            }
        }
        
        return (int) (($setThresholds / count($thresholdFields)) * 100);
    }

    /**
     * Get alert trends over a period.
     */
    private function getAlertTrends(string $dateFrom, string $dateTo): array
    {
        return InventoryAlert::selectRaw('DATE(created_at) as date, alert_type, COUNT(*) as count')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date', 'alert_type')
            ->orderBy('date')
            ->get()
            ->groupBy('date')
            ->toArray();
    }

    /**
     * Get products with most alerts.
     */
    private function getTopProblematicProducts(int $limit = 10): Collection
    {
        return Product::withCount(['inventoryAlerts as alerts_count' => function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            }])
            ->having('alerts_count', '>', 0)
            ->orderByDesc('alerts_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Check all products against their thresholds and generate alerts
     */
    public function checkAllThresholds(): array
    {
        $results = [
            'products_checked' => 0,
            'alerts_created' => 0,
            'alerts_resolved' => 0,
            'errors' => []
        ];

        try {
            $products = Product::where('threshold_alerts_enabled', true)->get();
            $results['products_checked'] = $products->count();

            foreach ($products as $product) {
                try {
                    $this->checkProductThresholds($product);
                    $results['alerts_created'] += $this->countNewAlerts($product);
                    $results['alerts_resolved'] += $this->resolveObsoleteAlerts($product);
                } catch (\Exception $e) {
                    $results['errors'][] = "Product {$product->product_name}: " . $e->getMessage();
                    Log::error("Threshold check error for product {$product->product_id}", [
                        'error' => $e->getMessage(),
                        'product' => $product->product_name
                    ]);
                }
            }
        } catch (\Exception $e) {
            $results['errors'][] = "General threshold check error: " . $e->getMessage();
            Log::error("General threshold check error", ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Check a specific product's thresholds and generate alerts
     */
    public function checkProductThresholds(Product $product): Collection
    {
        $alerts = collect();
        $currentQuantity = $product->quantity;
        
        // Update last threshold check
        $product->update(['last_threshold_check' => now()]);

        // Check for out of stock
        if ($currentQuantity <= 0) {
            $alerts->push($this->createOrUpdateAlert($product, 'out_of_stock', 'urgent', 
                "Product '{$product->product_name}' is out of stock", [
                    'current_quantity' => $currentQuantity,
                    'reorder_level' => $product->reorder_level
                ]));
        }

        // Check for critical stock level
        if ($product->critical_level && $currentQuantity <= $product->critical_level && $currentQuantity > 0) {
            $alerts->push($this->createOrUpdateAlert($product, 'critical_stock', 'critical',
                "Product '{$product->product_name}' has reached critical stock level ({$currentQuantity} units)", [
                    'current_quantity' => $currentQuantity,
                    'critical_level' => $product->critical_level,
                    'reorder_level' => $product->reorder_level
                ]));
        }

        // Check for low stock (reorder level)
        if ($product->reorder_level && $currentQuantity <= $product->reorder_level && $currentQuantity > ($product->critical_level ?? 0)) {
            $alerts->push($this->createOrUpdateAlert($product, 'low_stock', 'warning',
                "Product '{$product->product_name}' is running low on stock ({$currentQuantity} units)", [
                    'current_quantity' => $currentQuantity,
                    'reorder_level' => $product->reorder_level,
                    'suggested_order_quantity' => $product->getSuggestedOrderQuantity()
                ]));
        }

        // Check for overstock
        if ($product->ceiling_level && $currentQuantity > $product->ceiling_level) {
            $alerts->push($this->createOrUpdateAlert($product, 'overstock', 'info',
                "Product '{$product->product_name}' is overstocked ({$currentQuantity} units)", [
                    'current_quantity' => $currentQuantity,
                    'ceiling_level' => $product->ceiling_level,
                    'excess_quantity' => $currentQuantity - $product->ceiling_level
                ]));
        }

        // Generate reorder suggestion if auto-reorder is enabled
        if ($product->auto_reorder_enabled && $product->needsReordering()) {
            $alerts->push($this->createOrUpdateAlert($product, 'reorder_needed', 'warning',
                "Auto-reorder triggered for '{$product->product_name}' - suggested quantity: {$product->getSuggestedOrderQuantity()}", [
                    'current_quantity' => $currentQuantity,
                    'reorder_level' => $product->reorder_level,
                    'suggested_quantity' => $product->getSuggestedOrderQuantity(),
                    'preferred_supplier' => $product->preferred_supplier_id,
                    'lead_time_days' => $product->lead_time_days
                ]));
        }

        return $alerts->filter(); // Remove null values
    }

    /**
     * Create or update an alert for a product
     */
    protected function createOrUpdateAlert(
        Product $product, 
        string $alertType, 
        string $severity, 
        string $message, 
        array $alertData = []
    ): ?InventoryAlert {
        // Check if a similar active alert already exists
        $existingAlert = InventoryAlert::where('product_id', $product->getKey())
            ->where('alert_type', $alertType)
            ->where('status', 'active')
            ->first();

        if ($existingAlert) {
            // Update existing alert with new data
            $existingAlert->update([
                'message' => $message,
                'alert_data' => $alertData,
                'severity' => $severity,
            ]);

            InventoryAuditLog::logAction(
                $product, 
                'alert_updated', 
                "Updated {$alertType} alert for {$product->product_name}",
                ['old_message' => $existingAlert->getOriginal('message')],
                ['new_message' => $message],
                null,
                $existingAlert,
                'System'
            );

            return $existingAlert;
        }

        // Create new alert
        $alert = InventoryAlert::create([
            'product_id' => $product->getKey(),
            'alert_type' => $alertType,
            'severity' => $severity,
            'message' => $message,
            'alert_data' => $alertData,
            'status' => 'active',
            'auto_dismiss' => true,
            'expires_at' => now()->addDays(7) // Auto-expire after 7 days
        ]);

        // Log the alert generation
        InventoryAuditLog::logAction(
            $product, 
            'alert_generated', 
            "Generated {$alertType} alert for {$product->product_name}",
            null,
            ['alert_type' => $alertType, 'severity' => $severity],
            null,
            $alert,
            'System'
        );

        return $alert;
    }

    /**
     * Resolve alerts that are no longer valid (e.g., stock was replenished)
     */
    public function resolveObsoleteAlerts(Product $product): int
    {
        $resolvedCount = 0;
        $currentQuantity = $product->quantity;
        
        $activeAlerts = InventoryAlert::where('product_id', $product->getKey())
            ->where('status', 'active')
            ->where('auto_dismiss', true)
            ->get();

        foreach ($activeAlerts as $alert) {
            $shouldResolve = false;

            switch ($alert->alert_type) {
                case 'out_of_stock':
                    $shouldResolve = $currentQuantity > 0;
                    break;
                case 'critical_stock':
                    $shouldResolve = !$product->isCriticalStock();
                    break;
                case 'low_stock':
                    $shouldResolve = !$product->isLowStock();
                    break;
                case 'overstock':
                    $shouldResolve = !$product->isOverstocked();
                    break;
                case 'reorder_needed':
                    $shouldResolve = !$product->needsReordering();
                    break;
            }

            if ($shouldResolve) {
                $alert->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'resolution_notes' => 'Auto-resolved: stock level condition no longer met'
                ]);

                InventoryAuditLog::logAction(
                    $product, 
                    'alert_resolved', 
                    "Auto-resolved {$alert->alert_type} alert for {$product->product_name}",
                    ['status' => 'active'],
                    ['status' => 'resolved'],
                    null,
                    $alert,
                    'System'
                );

                $resolvedCount++;
            }
        }

        return $resolvedCount;
    }

    /**
     * Update product thresholds
     */
    public function updateProductThresholds(Product $product, array $thresholds, ?User $user = null): Product
    {
        $oldValues = $product->only([
            'reorder_level', 'critical_level', 'ceiling_level', 'floor_level',
            'auto_reorder_enabled', 'threshold_alerts_enabled', 'preferred_supplier_id',
            'lead_time_days', 'economic_order_quantity'
        ]);

        // Enable threshold fields for this update operation
        $product->enableThresholdFields();
        
        try {
            $product->update($thresholds);
        } finally {
            // Always reset fillable fields back to basic fields
            $product->resetFillable();
        }

        $newValues = $product->fresh()->only(array_keys($oldValues));

        // Log the threshold update
        InventoryAuditLog::logAction(
            $product,
            'threshold_update',
            "Updated inventory thresholds for {$product->product_name}",
            $oldValues,
            $newValues,
            $user,
            null,
            'Manual'
        );

        // Re-check thresholds after update
        if ($product->threshold_alerts_enabled) {
            $this->checkProductThresholds($product);
        }

        return $product;
    }

    /**
     * Get products that need attention based on thresholds
     */
    public function getProductsNeedingAttention(): array
    {
        return [
            'out_of_stock' => Product::where('quantity', '<=', 0)->with('inventoryAlerts')->get(),
            'critical_stock' => Product::whereColumn('quantity', '<=', 'critical_level')
                ->whereNotNull('critical_level')
                ->with('inventoryAlerts')->get(),
            'low_stock' => Product::whereColumn('quantity', '<=', 'reorder_level')
                ->whereNotNull('reorder_level')
                ->where('quantity', '>', 0)
                ->with('inventoryAlerts')->get(),
            'overstock' => Product::whereColumn('quantity', '>', 'ceiling_level')
                ->whereNotNull('ceiling_level')
                ->with('inventoryAlerts')->get(),
        ];
    }

    /**
     * Get threshold statistics
     */
    public function getThresholdStatistics(): array
    {
        $totalProducts = Product::count();
        $productsWithThresholds = Product::whereNotNull('reorder_level')->count();
        
        return [
            'total_products' => $totalProducts,
            'products_with_thresholds' => $productsWithThresholds,
            'threshold_coverage' => $totalProducts > 0 ? round(($productsWithThresholds / $totalProducts) * 100, 2) : 0,
            'active_alerts' => InventoryAlert::where('status', 'active')->count(),
            'critical_alerts' => InventoryAlert::where('status', 'active')->whereIn('severity', ['critical', 'urgent'])->count(),
            'auto_reorder_enabled' => Product::where('auto_reorder_enabled', true)->count(),
        ];
    }

    /**
     * Count new alerts for a product (for reporting)
     */
    protected function countNewAlerts(Product $product): int
    {
        return InventoryAlert::where('product_id', $product->getKey())
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();
    }

    /**
     * Bulk update thresholds for multiple products.
     */
    public function bulkUpdateThresholds(array $productIds, array $thresholds, ?User $user = null): array
    {
        $results = ['updated' => 0, 'errors' => []];

        DB::transaction(function () use ($productIds, $thresholds, $user, &$results) {
            foreach ($productIds as $productId) {
                try {
                    $product = Product::findOrFail($productId);
                    $this->updateProductThresholds($product, $thresholds, $user);
                    $results['updated']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "Product {$productId}: " . $e->getMessage();
                }
            }
        });

        return $results;
    }
    
    /**
     * Export threshold data to CSV or Excel.
     */
    public function exportThresholdData(Request $request)
    {
        $products = $this->getPaginatedProducts($request, 0)->items();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="threshold_data_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Product ID', 'Product Name', 'Brand', 'Current Stock', 
                'Reorder Level', 'Critical Level', 'Ceiling Level', 'Floor Level',
                'Auto Reorder', 'Alerts Enabled', 'Preferred Supplier', 
                'Lead Time (Days)', 'Economic Order Quantity', 'Stock Status'
            ]);
            
            // Data
            foreach ($products as $product) {
                $stockStatus = $product->getStockStatus();
                $statusText = !empty($stockStatus) ? $stockStatus[0]['type'] : 'normal';
                
                fputcsv($file, [
                    $product->product_id,
                    $product->product_name,
                    $product->product_brand,
                    $product->quantity,
                    $product->reorder_level,
                    $product->critical_level,
                    $product->ceiling_level,
                    $product->floor_level,
                    $product->auto_reorder_enabled ? 'Yes' : 'No',
                    $product->threshold_alerts_enabled ? 'Yes' : 'No',
                    $product->preferredSupplier?->supplier_name ?? '',
                    $product->lead_time_days,
                    $product->economic_order_quantity,
                    $statusText
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}