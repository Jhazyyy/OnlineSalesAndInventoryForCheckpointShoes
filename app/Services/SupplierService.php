<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SupplierService
{
    /**
     * Get paginated suppliers with filters and search.
     */
    public function getPaginatedSuppliers(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = Supplier::query();

        // Apply search
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Apply filters
        if ($supplierType = $request->get('supplier_type')) {
            $query->where('supplier_type', $supplierType);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }

        if ($country = $request->get('country')) {
            $query->where('country', $country);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortField, ['supplier_name', 'email', 'supplier_type', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for supplier listing.
     */
    public function getFilterOptions(): array
    {
        return [
            'types' => Supplier::whereNotNull('supplier_type')
                              ->distinct()
                              ->pluck('supplier_type')
                              ->filter()
                              ->sort()
                              ->values(),
            'cities' => Supplier::whereNotNull('city')
                               ->distinct()
                               ->pluck('city')
                               ->filter()
                               ->sort()
                               ->values(),
            'countries' => Supplier::whereNotNull('country')
                                  ->distinct()
                                  ->pluck('country')
                                  ->filter()
                                  ->sort()
                                  ->values(),
        ];
    }

    /**
     * Create a new supplier.
     */
    public function createSupplier(array $data): Supplier
    {
        // Set defaults
        $data['status'] = $data['status'] ?? 'active';
        $data['country'] = $data['country'] ?? 'Philippines';
        $data['supplier_type'] = $data['supplier_type'] ?? 'local';

        $supplier = Supplier::create($data);

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_CREATE,
            \App\Models\AuditLog::MODULE_SUPPLIERS,
            "Supplier {$supplier->supplier_name} created",
            'Supplier',
            $supplier->supplier_id,
            $supplier->supplier_name,
            null,
            [
                'supplier_name' => $supplier->supplier_name,
                'email' => $supplier->email,
                'supplier_type' => $supplier->supplier_type,
                'status' => $supplier->status,
            ],
            \App\Models\AuditLog::SEVERITY_INFO
        );

        return $supplier;
    }

    /**
     * Update a supplier.
     */
    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        // Capture old values
        $oldValues = [
            'supplier_name' => $supplier->supplier_name,
            'email' => $supplier->email,
            'supplier_type' => $supplier->supplier_type,
            'status' => $supplier->status,
        ];

        $supplier->update($data);
        $supplier->refresh();

        // Capture new values
        $newValues = [
            'supplier_name' => $supplier->supplier_name,
            'email' => $supplier->email,
            'supplier_type' => $supplier->supplier_type,
            'status' => $supplier->status,
        ];

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_UPDATE,
            \App\Models\AuditLog::MODULE_SUPPLIERS,
            "Supplier {$supplier->supplier_name} updated",
            'Supplier',
            $supplier->supplier_id,
            $supplier->supplier_name,
            $oldValues,
            $newValues,
            \App\Models\AuditLog::SEVERITY_INFO
        );

        return $supplier;
    }

    /**
     * Delete a supplier.
     */
    public function deleteSupplier(Supplier $supplier): bool
    {
        // Check if supplier has purchases
        if ($supplier->purchases()->exists()) {
            throw new \Exception('Cannot delete supplier with existing purchase records. Consider deactivating instead.');
        }

        // Capture supplier details before deletion
        $supplierName = $supplier->supplier_name;
        $supplierId = $supplier->supplier_id;
        $supplierData = [
            'supplier_name' => $supplier->supplier_name,
            'email' => $supplier->email,
            'supplier_type' => $supplier->supplier_type,
            'status' => $supplier->status,
        ];

        $deleted = $supplier->delete();

        // Log to audit trail
        if ($deleted) {
            \App\Models\AuditLog::logAction(
                \App\Models\AuditLog::ACTION_DELETE,
                \App\Models\AuditLog::MODULE_SUPPLIERS,
                "Supplier {$supplierName} deleted",
                'Supplier',
                $supplierId,
                $supplierName,
                $supplierData,
                null,
                \App\Models\AuditLog::SEVERITY_WARNING
            );
        }

        return $deleted;
    }

    /**
     * Toggle supplier status.
     */
    public function toggleSupplierStatus(Supplier $supplier): Supplier
    {
        $supplier->update([
            'status' => $supplier->status === 'active' ? 'inactive' : 'active'
        ]);

        return $supplier->fresh();
    }

    /**
     * Get supplier analytics data.
     */
    public function getSupplierAnalytics(): array
    {
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::active()->count();
        $inactiveSuppliers = Supplier::inactive()->count();

        // Supplier types distribution
        $typeDistribution = Supplier::selectRaw('supplier_type, COUNT(*) as count')
                                  ->whereNotNull('supplier_type')
                                  ->groupBy('supplier_type')
                                  ->orderByDesc('count')
                                  ->get();

        // Recent suppliers (last 30 days)
        $recentSuppliers = Supplier::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Top suppliers by purchase volume
        $topSuppliers = Supplier::topSuppliers(10);

        // Supplier acquisition trend (last 12 months)
        $acquisitionTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Supplier::whereYear('created_at', $date->year)
                           ->whereMonth('created_at', $date->month)
                           ->count();
            
            $acquisitionTrend->push([
                'month' => $date->format('M Y'),
                'count' => $count
            ]);
        }

        // Geographic distribution
        $geographicDistribution = Supplier::selectRaw('country, COUNT(*) as count')
                                        ->whereNotNull('country')
                                        ->groupBy('country')
                                        ->orderByDesc('count')
                                        ->limit(10)
                                        ->get();

        // Purchase statistics
        $totalPurchases = Purchase::count();
        $totalPurchaseValue = Purchase::sum('total_amount') ?? 0;
        $averagePurchaseValue = $totalPurchases > 0 ? $totalPurchaseValue / $totalPurchases : 0;

        // Suppliers with recent activity (last 30 days)
        $recentlyActiveSuppliers = Supplier::withRecentOrders(30)->count();

        return [
            'summary' => [
                'total_suppliers' => $totalSuppliers,
                'active_suppliers' => $activeSuppliers,
                'inactive_suppliers' => $inactiveSuppliers,
                'recent_suppliers' => $recentSuppliers,
                'recently_active_suppliers' => $recentlyActiveSuppliers,
                'activity_rate' => $totalSuppliers > 0 ? round(($recentlyActiveSuppliers / $totalSuppliers) * 100, 2) : 0,
            ],
            'purchase_summary' => [
                'total_purchases' => $totalPurchases,
                'total_purchase_value' => $totalPurchaseValue,
                'average_purchase_value' => round($averagePurchaseValue, 2),
            ],
            'type_distribution' => $typeDistribution,
            'top_suppliers' => $topSuppliers,
            'acquisition_trend' => $acquisitionTrend,
            'geographic_distribution' => $geographicDistribution,
        ];
    }

    /**
     * Get supplier purchase history with pagination.
     */
    public function getSupplierPurchaseHistory(Supplier $supplier, int $perPage = 15): LengthAwarePaginator
    {
        return $supplier->purchases()
                       ->with(['product', 'user'])
                       ->latest('purchase_date')
                       ->paginate($perPage);
    }

    /**
     * Calculate supplier performance metrics.
     */
    public function calculateSupplierPerformance(Supplier $supplier): array
    {
        // Get all purchase orders for this supplier
        $purchaseOrders = $supplier->purchaseOrders()->get();
        
        // Calculate totals from purchase orders
        $totalPurchased = $purchaseOrders->sum('total_amount');
        $totalOrders = $purchaseOrders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalPurchased / $totalOrders : 0;
        
        // Get first and last order dates
        $firstOrderDate = $supplier->purchaseOrders()->oldest('order_date')->first()?->order_date;
        $lastOrderDate = $supplier->purchaseOrders()->latest('order_date')->first()?->order_date;
        
        $relationshipDays = 0;
        if ($firstOrderDate && $lastOrderDate) {
            $relationshipDays = $firstOrderDate->diffInDays($lastOrderDate) ?: 1;
        } elseif ($firstOrderDate) {
            $relationshipDays = $firstOrderDate->diffInDays(Carbon::now()) ?: 1;
        }

        $orderFrequency = $relationshipDays > 0 ? $totalOrders / ($relationshipDays / 30.44) : 0; // Orders per month

        // Recent activity (last 90 days)
        $recentOrders = $supplier->purchaseOrders()
                               ->where('order_date', '>=', Carbon::now()->subDays(90))
                               ->count();

        $recentValue = $supplier->purchaseOrders()
                              ->where('order_date', '>=', Carbon::now()->subDays(90))
                              ->sum('total_amount') ?? 0;

        // Performance rating (based on order frequency, value, and recency)
        $performanceScore = 0;
        
        // Orders volume scoring
        if ($totalOrders >= 20) $performanceScore += 30;
        elseif ($totalOrders >= 10) $performanceScore += 20;
        elseif ($totalOrders >= 5) $performanceScore += 15;
        elseif ($totalOrders >= 1) $performanceScore += 5;

        // Purchase value scoring
        if ($totalPurchased >= 100000) $performanceScore += 30;
        elseif ($totalPurchased >= 50000) $performanceScore += 20;
        elseif ($totalPurchased >= 10000) $performanceScore += 15;
        elseif ($totalPurchased >= 1000) $performanceScore += 5;

        // Recent activity scoring
        if ($recentOrders >= 5) $performanceScore += 25;
        elseif ($recentOrders > 0) $performanceScore += 15;
        
        // Order frequency scoring
        if ($orderFrequency >= 2) $performanceScore += 15;
        elseif ($orderFrequency >= 1) $performanceScore += 10;
        elseif ($orderFrequency >= 0.5) $performanceScore += 5;

        return [
            'total_purchased' => $totalPurchased,
            'total_orders' => $totalOrders,
            'average_order_value' => $avgOrderValue,
            'order_frequency_per_month' => round($orderFrequency, 2),
            'relationship_days' => $relationshipDays,
            'first_order_date' => $firstOrderDate,
            'last_order_date' => $lastOrderDate,
            'recent_orders_90_days' => $recentOrders,
            'recent_value_90_days' => $recentValue,
            'performance_score' => min($performanceScore, 100), // Cap at 100
            'performance_rating' => $this->getPerformanceRating($performanceScore),
            'is_reliable' => $recentOrders > 0 && $totalOrders >= 3, // Reliable if active and has history
        ];
    }

    /**
     * Get performance rating based on score.
     */
    private function getPerformanceRating(int $score): string
    {
        if ($score >= 90) return 'Excellent';
        if ($score >= 70) return 'Good';
        if ($score >= 50) return 'Average';
        if ($score >= 30) return 'Below Average';
        return 'Poor';
    }

    /**
     * Search suppliers with advanced filters.
     */
    public function searchSuppliers(array $criteria, int $perPage = 20): LengthAwarePaginator
    {
        $query = Supplier::query();

        // Text search across multiple fields
        if (!empty($criteria['search'])) {
            $query->search($criteria['search']);
        }

        // Date range filters
        if (!empty($criteria['created_from'])) {
            $query->whereDate('created_at', '>=', $criteria['created_from']);
        }
        if (!empty($criteria['created_to'])) {
            $query->whereDate('created_at', '<=', $criteria['created_to']);
        }

        // Purchase amount filters
        if (!empty($criteria['min_purchased'])) {
            $query->whereHas('purchases', function ($q) use ($criteria) {
                $q->havingRaw('SUM(total_amount) >= ?', [$criteria['min_purchased']]);
            });
        }

        // High-value suppliers
        if (!empty($criteria['high_value_only'])) {
            $highValueThreshold = $criteria['high_value_threshold'] ?? 10000;
            $query->whereHas('purchases', function ($q) use ($highValueThreshold) {
                $q->havingRaw('SUM(total_amount) >= ?', [$highValueThreshold]);
            });
        }

        // Location filters
        if (!empty($criteria['cities'])) {
            $query->whereIn('city', $criteria['cities']);
        }
        if (!empty($criteria['countries'])) {
            $query->whereIn('country', $criteria['countries']);
        }

        // Type filter
        if (!empty($criteria['types'])) {
            $query->whereIn('supplier_type', $criteria['types']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Export suppliers data.
     */
    public function getSuppliersForExport(array $filters = []): Collection
    {
        $query = Supplier::with(['purchases']);

        // Apply filters
        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                switch ($field) {
                    case 'search':
                        $query->search($value);
                        break;
                    case 'supplier_type':
                    case 'status':
                    case 'city':
                    case 'country':
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query->get();
    }

    /**
     * Get suppliers that need attention (inactive, no recent orders, etc.).
     */
    public function getSuppliersNeedingAttention(): array
    {
        $inactiveSuppliers = Supplier::inactive()->get();
        
        $noRecentOrders = Supplier::active()
                                ->whereDoesntHave('purchases', function ($query) {
                                    $query->where('purchase_date', '>=', Carbon::now()->subDays(90));
                                })
                                ->whereHas('purchases') // Has orders but not recent
                                ->get();

        $newSuppliersNoOrders = Supplier::active()
                                      ->whereDoesntHave('purchases')
                                      ->where('created_at', '>=', Carbon::now()->subDays(30))
                                      ->get();

        return [
            'inactive' => $inactiveSuppliers->map(function($supplier) {
                return [
                    'id' => $supplier->supplier_id,
                    'name' => $supplier->supplier_name,
                    'status' => 'inactive',
                    'issue' => 'Supplier is marked as inactive'
                ];
            }),
            'no_recent_orders' => $noRecentOrders->map(function($supplier) {
                return [
                    'id' => $supplier->supplier_id,
                    'name' => $supplier->supplier_name,
                    'status' => 'no_recent_orders',
                    'last_order' => $supplier->last_order_date?->format('Y-m-d'),
                    'issue' => 'No orders in the last 90 days'
                ];
            }),
            'new_no_orders' => $newSuppliersNoOrders->map(function($supplier) {
                return [
                    'id' => $supplier->supplier_id,
                    'name' => $supplier->supplier_name,
                    'status' => 'new_no_orders',
                    'created' => $supplier->created_at->format('Y-m-d'),
                    'issue' => 'New supplier with no orders yet'
                ];
            })
        ];
    }

    /**
     * Get supplier comparison data.
     */
    public function compareSuppliers(array $supplierIds): array
    {
        $suppliers = Supplier::whereIn('supplier_id', $supplierIds)->get();
        
        $comparison = [];
        foreach ($suppliers as $supplier) {
            $performance = $this->calculateSupplierPerformance($supplier);
            $comparison[] = [
                'supplier' => $supplier,
                'performance' => $performance,
            ];
        }

        return $comparison;
    }
}
