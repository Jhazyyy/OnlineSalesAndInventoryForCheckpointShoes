<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class PackageService
{
    /**
     * Get package dashboard data.
     */
    public function getPackageDashboard(): array
    {
        $totalPackages = Package::count();
        $activePackages = Package::active()->count();
        $lowStockPackages = Package::needsReordering();
        $outOfStockPackages = Package::outOfStock();
        $totalInventoryValue = Package::totalInventoryValue();

        return [
            'total_packages' => $totalPackages,
            'active_packages' => $activePackages,
            'low_stock_count' => $lowStockPackages->count(),
            'low_stock_packages' => $lowStockPackages,
            'out_of_stock_count' => $outOfStockPackages->count(),
            'out_of_stock_packages' => $outOfStockPackages,
            'total_inventory_value' => $totalInventoryValue,
            'average_package_value' => $totalPackages > 0 ? $totalInventoryValue / $totalPackages : 0,
        ];
    }

    /**
     * Process a package sale.
     */
    public function processPackageSale(int $packageId, int $quantity, $date = null): array
    {
        $package = Package::find($packageId);
        
        if (!$package) {
            return [
                'success' => false,
                'message' => 'Package not found',
                'data' => null
            ];
        }

        if (!$package->isInStock($quantity)) {
            return [
                'success' => false,
                'message' => "Insufficient stock. Available: {$package->quantity}, Requested: {$quantity}",
                'data' => null
            ];
        }

        if (!$package->isActive()) {
            return [
                'success' => false,
                'message' => 'Package is not active for sale',
                'data' => null
            ];
        }

        // Decrease package stock
        $package->decreaseStock($quantity);

        // If package contains products, decrease their stock too
        $productIssues = [];
        if ($package->contents && is_array($package->contents)) {
            foreach ($package->contents as $item) {
                if (isset($item['product_id']) && isset($item['quantity'])) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $requiredQuantity = $item['quantity'] * $quantity;
                        if (!$product->isInStock($requiredQuantity)) {
                            $productIssues[] = "Product {$product->product_name} has insufficient stock";
                        } else {
                            $product->decreaseStock($requiredQuantity);
                        }
                    }
                }
            }
        }

        return [
            'success' => true,
            'message' => 'Package sale processed successfully',
            'data' => [
                'package' => $package,
                'remaining_stock' => $package->fresh()->quantity,
                'total_amount' => $package->price * $quantity,
                'product_issues' => $productIssues
            ]
        ];
    }

    /**
     * Restock a package.
     */
    public function restockPackage(int $packageId, int $quantity): array
    {
        $package = Package::find($packageId);
        
        if (!$package) {
            return [
                'success' => false,
                'message' => 'Package not found',
                'data' => null
            ];
        }

        $package->increaseStock($quantity);

        return [
            'success' => true,
            'message' => 'Package restocked successfully',
            'data' => [
                'package' => $package,
                'new_stock_level' => $package->quantity,
                'added_quantity' => $quantity
            ]
        ];
    }

    /**
     * Get package analytics for a given period.
     */
    public function getPackageAnalytics(): array
    {
        $totalPackages = Package::count();
        $activePackages = Package::active()->count();
        $inStockPackages = Package::inStock()->count();
        $lowStockPackages = Package::needsReordering();
        $outOfStockPackages = Package::outOfStock();
        $totalInventoryValue = Package::totalInventoryValue();

        $packagesByType = [
            'standard' => Package::getByType(Package::TYPE_STANDARD)->count(),
            'custom' => Package::getByType(Package::TYPE_CUSTOM)->count(),
            'bundle' => Package::getByType(Package::TYPE_BUNDLE)->count(),
        ];

        $packagesByStatus = [
            'active' => Package::active()->count(),
            'inactive' => Package::inactive()->count(),
            'discontinued' => Package::discontinued()->count(),
        ];

        return [
            'total_packages' => $totalPackages,
            'active_packages' => $activePackages,
            'in_stock_packages' => $inStockPackages,
            'low_stock_count' => $lowStockPackages->count(),
            'out_of_stock_count' => $outOfStockPackages->count(),
            'total_inventory_value' => $totalInventoryValue,
            'average_package_value' => $totalPackages > 0 ? $totalInventoryValue / $totalPackages : 0,
            'packages_by_type' => $packagesByType,
            'packages_by_status' => $packagesByStatus,
        ];
    }

    /**
     * Get packages that need attention (low stock, out of stock, discontinued).
     */
    public function getPackagesNeedingAttention(): array
    {
        $lowStockPackages = Package::needsReordering(5);
        $outOfStockPackages = Package::outOfStock();
        $discontinuedPackages = Package::discontinued()->get();

        return [
            'low_stock' => $lowStockPackages->map(function($package) {
                return [
                    'id' => $package->package_id,
                    'name' => $package->full_name,
                    'current_stock' => $package->quantity,
                    'type' => $package->package_type,
                    'status' => 'low_stock'
                ];
            }),
            'out_of_stock' => $outOfStockPackages->map(function($package) {
                return [
                    'id' => $package->package_id,
                    'name' => $package->full_name,
                    'current_stock' => $package->quantity,
                    'type' => $package->package_type,
                    'status' => 'out_of_stock'
                ];
            }),
            'discontinued' => $discontinuedPackages->map(function($package) {
                return [
                    'id' => $package->package_id,
                    'name' => $package->full_name,
                    'current_stock' => $package->quantity,
                    'type' => $package->package_type,
                    'status' => 'discontinued'
                ];
            })
        ];
    }

    /**
     * Bulk update package stock levels.
     */
    public function bulkUpdatePackageStock(array $stockUpdates): array
    {
        $results = Package::bulkUpdateStock($stockUpdates);
        $successCount = array_sum(array_map('intval', $results));
        $totalCount = count($results);

        return [
            'success' => $successCount === $totalCount,
            'updated_count' => $successCount,
            'total_count' => $totalCount,
            'failed_packages' => array_keys(array_filter($results, function($result) {
                return !$result;
            })),
            'details' => $results
        ];
    }

    /**
     * Search packages with advanced filtering.
     */
    public function searchPackages(array $filters): Collection
    {
        $query = Package::query();

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['package_type'])) {
            $query->byType($filters['package_type']);
        }

        if (!empty($filters['status'])) {
            switch ($filters['status']) {
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->inactive();
                    break;
                case 'discontinued':
                    $query->discontinued();
                    break;
            }
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['min_weight'])) {
            $query->where('weight', '>=', $filters['min_weight']);
        }

        if (isset($filters['max_weight'])) {
            $query->where('weight', '<=', $filters['max_weight']);
        }

        if (isset($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'in_stock':
                    $query->inStock();
                    break;
                case 'low_stock':
                    $query->lowStock($filters['low_stock_threshold'] ?? 5);
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
            }
        }

        $orderBy = $filters['order_by'] ?? 'package_name';
        $orderDirection = $filters['order_direction'] ?? 'asc';
        $query->orderBy($orderBy, $orderDirection);

        return $query->get();
    }

    /**
     * Create a new package with validation.
     */
    public function createPackage(array $data): array
    {
        try {
            // Generate tracking code if not provided
            if (empty($data['tracking_code'])) {
                $data['tracking_code'] = Package::generateTrackingCode();
            }

            // Validate package contents
            if (isset($data['contents']) && is_array($data['contents'])) {
                foreach ($data['contents'] as $item) {
                    if (isset($item['product_id'])) {
                        $product = Product::find($item['product_id']);
                        if (!$product) {
                            return [
                                'success' => false,
                                'message' => 'One or more products in package contents not found',
                                'data' => null
                            ];
                        }
                    }
                }
            }

            $package = Package::create($data);

            return [
                'success' => true,
                'message' => 'Package created successfully',
                'data' => $package
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error creating package: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Update package with validation.
     */
    public function updatePackage(Package $package, array $data): array
    {
        try {
            // Generate tracking code if not provided
            if (empty($data['tracking_code'])) {
                $data['tracking_code'] = Package::generateTrackingCode();
            }

            // Validate package contents
            if (isset($data['contents']) && is_array($data['contents'])) {
                foreach ($data['contents'] as $item) {
                    if (isset($item['product_id'])) {
                        $product = Product::find($item['product_id']);
                        if (!$product) {
                            return [
                                'success' => false,
                                'message' => 'One or more products in package contents not found',
                                'data' => null
                            ];
                        }
                    }
                }
            }

            $package->update($data);

            return [
                'success' => true,
                'message' => 'Package updated successfully',
                'data' => $package
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error updating package: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Calculate total value of package contents based on individual product prices.
     */
    public function calculatePackageContentsValue(Package $package): float
    {
        if (!$package->contents || !is_array($package->contents)) {
            return 0;
        }

        $totalValue = 0;
        foreach ($package->contents as $item) {
            if (isset($item['product_id']) && isset($item['quantity'])) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $totalValue += $product->price * $item['quantity'];
                }
            }
        }

        return $totalValue;
    }

    /**
     * Validate package availability including its contents.
     */
    public function validatePackageAvailability(Package $package, int $requestedQuantity): array
    {
        $issues = [];

        // Check package stock
        if (!$package->isInStock($requestedQuantity)) {
            $issues[] = "Package {$package->package_name} has insufficient stock";
        }

        // Check package status
        if (!$package->isActive()) {
            $issues[] = "Package {$package->package_name} is not active";
        }

        // Check contents availability
        if ($package->contents && is_array($package->contents)) {
            foreach ($package->contents as $item) {
                if (isset($item['product_id']) && isset($item['quantity'])) {
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $requiredQuantity = $item['quantity'] * $requestedQuantity;
                        if (!$product->isInStock($requiredQuantity)) {
                            $issues[] = "Product {$product->product_name} has insufficient stock for package contents";
                        }
                    }
                }
            }
        }

        return [
            'available' => empty($issues),
            'issues' => $issues
        ];
    }
}
