<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SalesOrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductMovementService
{
    /**
     * Movement category thresholds (sales per day)
     */
    const FAST_MOVING_THRESHOLD = 3;      // 3+ units per day
    const SLOW_MOVING_THRESHOLD = 1;      // 1-3 units per day
    const NON_MOVING_DAYS = 30;           // No sales in 30+ days

    /**
     * Calculate and update movement for all products
     * 
     * @param int $days Number of days to analyze (default 90)
     * @return array Statistics about the update
     */
    public function calculateAllProductMovements(int $days = 90): array
    {
        $startDate = now()->subDays($days);
        $endDate = now();
        
        $products = Product::all();
        $stats = [
            'total_products' => $products->count(),
            'fast_moving' => 0,
            'slow_moving' => 0,
            'non_moving' => 0,
            'updated' => 0
        ];

        foreach ($products as $product) {
            $this->calculateProductMovement($product, $startDate, $endDate);
            
            // Update stats
            $stats['updated']++;
            switch ($product->movement_category) {
                case 'fast':
                    $stats['fast_moving']++;
                    break;
                case 'slow':
                    $stats['slow_moving']++;
                    break;
                case 'non-moving':
                    $stats['non_moving']++;
                    break;
            }
        }

        return $stats;
    }

    /**
     * Calculate movement for a single product
     * 
     * @param Product $product
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return Product Updated product
     */
    public function calculateProductMovement(
        Product $product, 
        ?Carbon $startDate = null, 
        ?Carbon $endDate = null
    ): Product {
        $startDate = $startDate ?? now()->subDays(90);
        $endDate = $endDate ?? now();
        $daysDiff = max(1, $startDate->diffInDays($endDate));

        // Get sales data from sales_order_items
        $salesData = SalesOrderItem::where('product_id', $product->product_id)
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('order_date', [$startDate, $endDate])
                      ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);
            })
            ->selectRaw('SUM(quantity) as total_quantity, MAX(created_at) as last_sale')
            ->first();

        $totalSalesQuantity = $salesData->total_quantity ?? 0;
        $lastSaleDate = $salesData->last_sale ? Carbon::parse($salesData->last_sale) : null;

        // Calculate velocity (units per day)
        $velocity = $daysDiff > 0 ? $totalSalesQuantity / $daysDiff : 0;

        // Calculate days since last sale
        $daysSinceLastSale = $lastSaleDate ? now()->diffInDays($lastSaleDate) : null;

        // Determine movement category
        $category = $this->determineMovementCategory($velocity, $daysSinceLastSale);

        // Enable movement tracking fields and update
        $product->enableMovementTrackingFields();
        $product->fill([
            'movement_category' => $category,
            'total_sales_quantity' => $totalSalesQuantity,
            'movement_velocity' => round($velocity, 2),
            'days_since_last_sale' => $daysSinceLastSale,
            'last_sale_date' => $lastSaleDate,
            'movement_analysis_start_date' => $startDate->toDateString(),
            'movement_analysis_end_date' => $endDate->toDateString(),
            'last_movement_check' => now(),
        ]);

        $product->save();

        return $product->fresh();
    }

    /**
     * Determine movement category based on velocity and last sale
     * 
     * @param float $velocity Units per day
     * @param int|null $daysSinceLastSale
     * @return string Category: fast, slow, non-moving
     */
    protected function determineMovementCategory(float $velocity, ?int $daysSinceLastSale): string
    {
        // Non-moving: No sales in specified days OR zero velocity
        if ($daysSinceLastSale >= self::NON_MOVING_DAYS || $velocity == 0) {
            return 'non-moving';
        }

        // Fast moving: High velocity
        if ($velocity >= self::FAST_MOVING_THRESHOLD) {
            return 'fast';
        }

        // Slow moving: Between thresholds
        return 'slow';
    }

    /**
     * Mark products as promotional based on criteria
     * 
     * @param array $criteria Criteria for promotion
     * @return int Number of products marked
     */
    public function markProductsForPromotion(array $criteria = []): int
    {
        $query = Product::query();

        // Default criteria: slow or non-moving products
        if (empty($criteria)) {
            $query->whereIn('movement_category', ['slow', 'non-moving']);
        } else {
            // Apply custom criteria
            if (isset($criteria['movement_category'])) {
                $query->whereIn('movement_category', (array)$criteria['movement_category']);
            }
            if (isset($criteria['min_stock'])) {
                $query->where('quantity', '>=', $criteria['min_stock']);
            }
            if (isset($criteria['days_since_last_sale'])) {
                $query->where('days_since_last_sale', '>=', $criteria['days_since_last_sale']);
            }
        }

        $productsToMark = $query->where('is_promotional', false)->get();

        foreach ($productsToMark as $product) {
            $reason = $this->generatePromotionalReason($product);
            $product->update([
                'is_promotional' => true,
                'promotional_reason' => $reason
            ]);
        }

        return $productsToMark->count();
    }

    /**
     * Generate promotional reason text
     * 
     * @param Product $product
     * @return string
     */
    protected function generatePromotionalReason(Product $product): string
    {
        $reasons = [];

        if ($product->movement_category === 'non-moving') {
            $reasons[] = 'No sales in ' . $product->days_since_last_sale . ' days';
        } elseif ($product->movement_category === 'slow') {
            $reasons[] = 'Slow moving product (avg ' . $product->movement_velocity . ' units/day)';
        }

        // Remove ceiling_level check since it's been removed
        if ($product->quantity > 50) { // Simple overstocked check
            $reasons[] = 'Overstocked';
        }

        return implode('. ', $reasons) ?: 'Selected for promotion';
    }

    /**
     * Remove promotional flag from products
     * 
     * @param array $productIds
     * @return int Number of products updated
     */
    public function unmarkPromotionalProducts(array $productIds = []): int
    {
        $query = Product::where('is_promotional', true);

        if (!empty($productIds)) {
            $query->whereIn('product_id', $productIds);
        }

        return $query->update([
            'is_promotional' => false,
            'promotional_reason' => null
        ]);
    }

    /**
     * Get movement statistics
     * 
     * @return array
     */
    public function getMovementStatistics(): array
    {
        $total = Product::count();
        $fastMoving = Product::where('movement_category', 'fast')->count();
        $slowMoving = Product::where('movement_category', 'slow')->count();
        $nonMoving = Product::where('movement_category', 'non-moving')->count();
        $promotional = Product::where('is_promotional', true)->count();
        $uncategorized = $total - ($fastMoving + $slowMoving + $nonMoving);

        $lastCheck = Product::max('last_movement_check');

        return [
            'total_products' => $total,
            'fast_moving' => $fastMoving,
            'fast_moving_percentage' => $total > 0 ? round(($fastMoving / $total) * 100, 1) : 0,
            'slow_moving' => $slowMoving,
            'slow_moving_percentage' => $total > 0 ? round(($slowMoving / $total) * 100, 1) : 0,
            'non_moving' => $nonMoving,
            'non_moving_percentage' => $total > 0 ? round(($nonMoving / $total) * 100, 1) : 0,
            'uncategorized' => $uncategorized,
            'promotional_products' => $promotional,
            'last_analysis' => $lastCheck ? Carbon::parse($lastCheck)->diffForHumans() : 'Never',
            'thresholds' => [
                'fast_moving_threshold' => self::FAST_MOVING_THRESHOLD . ' units/day',
                'slow_moving_threshold' => self::SLOW_MOVING_THRESHOLD . ' units/day',
                'non_moving_days' => self::NON_MOVING_DAYS . ' days'
            ]
        ];
    }

    /**
     * Get products by movement category
     * 
     * @param string $category
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getProductsByCategory(string $category, int $perPage = 15)
    {
        return Product::where('movement_category', $category)
            ->orderBy('movement_velocity', $category === 'fast' ? 'desc' : 'asc')
            ->paginate($perPage);
    }

    /**
     * Get promotional products
     * 
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPromotionalProducts(int $perPage = 15)
    {
        return Product::where('is_promotional', true)
            ->orderBy('days_since_last_sale', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get top fast moving products
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopFastMovingProducts(int $limit = 10)
    {
        return Product::where('movement_category', 'fast')
            ->orderBy('movement_velocity', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get products needing attention (non-moving with high stock)
     * 
     * @param int $minStock Minimum stock level to consider
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProductsNeedingAttention(int $minStock = 10)
    {
        return Product::where('movement_category', 'non-moving')
            ->where('quantity', '>=', $minStock)
            ->where('is_promotional', false)
            ->orderBy('quantity', 'desc')
            ->orderBy('days_since_last_sale', 'desc')
            ->get();
    }
}
