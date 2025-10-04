<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Returns;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    /**
     * Get inventory dashboard data.
     */
    public function getInventoryDashboard(): array
    {
        $totalProducts = Product::count();
        $lowStockProducts = Product::needsReordering();
        $outOfStockProducts = Product::outOfStock();
        $totalInventoryValue = Product::totalInventoryValue();

        return [
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockProducts->count(),
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_count' => $outOfStockProducts->count(),
            'out_of_stock_products' => $outOfStockProducts,
            'total_inventory_value' => $totalInventoryValue,
            'average_product_value' => $totalProducts > 0 ? $totalInventoryValue / $totalProducts : 0,
        ];
    }

    /**
     * Process a sale transaction.
     */
    public function processSale(int $productId, int $quantity, $date = null): array
    {
        $product = Product::find($productId);

        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found',
                'data' => null
            ];
        }

        if (!$product->isInStock($quantity)) {
            return [
                'success' => false,
                'message' => "Insufficient stock. Available: {$product->quantity}, Requested: {$quantity}",
                'data' => null
            ];
        }

        $sale = Sale::processSale($productId, $quantity, $date);

        if ($sale) {
            return [
                'success' => true,
                'message' => 'Sale processed successfully',
                'data' => [
                    'sale' => $sale,
                    'remaining_stock' => $product->fresh()->quantity,
                    'total_amount' => $sale->total_amount
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to process sale',
            'data' => null
        ];
    }

    /**
     * Process a purchase transaction.
     */
    public function processPurchase(
        int $supplierId,
        int $productId,
        int $userId,
        float $price,
        int $quantity,
        $purchaseDate = null
    ): array {
        $purchase = Purchase::processPurchase(
            $supplierId,
            $productId,
            $userId,
            $price,
            $quantity,
            $purchaseDate
        );

        if ($purchase) {
            $product = Product::find($productId);
            return [
                'success' => true,
                'message' => 'Purchase processed successfully',
                'data' => [
                    'purchase' => $purchase,
                    'new_stock_level' => $product->quantity,
                    'total_cost' => $purchase->total_amount
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to process purchase',
            'data' => null
        ];
    }

    /**
     * Process a return.
     */
    public function processReturn(
        int $productId,
        int $quantity,
        float $price,
        string $status = Returns::STATUS_PENDING,
        $returnDate = null
    ): array {
        $return = Returns::processReturn($productId, $quantity, $price, $status, $returnDate);

        if ($return) {
            return [
                'success' => true,
                'message' => 'Return processed successfully',
                'data' => [
                    'return' => $return,
                    'return_id' => $return->return_id,
                    'status' => $return->return_status,
                    'total_amount' => $return->total_amount
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to process return',
            'data' => null
        ];
    }

    /**
     * Get sales analytics for a given period.
     */
    public function getSalesAnalytics($startDate = null, $endDate = null): array
    {
        $query = Sale::with('product');

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $sales = $query->get();
        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total_amount');
        $topSellingProducts = Sale::topSellingProducts(5);

        return [
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'average_sale_amount' => $totalSales > 0 ? $totalRevenue / $totalSales : 0,
            'top_selling_products' => $topSellingProducts,
            'sales_by_date' => $sales->groupBy(function ($sale) {
                return $sale->date->format('Y-m-d');
            })->map(function ($dailySales) {
                return [
                    'count' => $dailySales->count(),
                    'revenue' => $dailySales->sum('total_amount')
                ];
            })
        ];
    }

    /**
     * Get products that need attention (low stock, high returns, etc.).
     */
    public function getProductsNeedingAttention(): array
    {
        $lowStockProducts = Product::needsReordering(10);
        $outOfStockProducts = Product::outOfStock();
        $oversoldProducts = Product::oversoldProducts();
        $highReturnProducts = Returns::mostReturnedProducts(5);

        return [
            'low_stock' => $lowStockProducts->map(function ($product) {
                return [
                    'id' => $product->product_id,
                    'name' => $product->full_name,
                    'current_stock' => $product->quantity,
                    'status' => 'low_stock'
                ];
            }),
            'out_of_stock' => $outOfStockProducts->map(function ($product) {
                return [
                    'id' => $product->product_id,
                    'name' => $product->full_name,
                    'current_stock' => $product->quantity,
                    'status' => 'out_of_stock'
                ];
            }),
            'oversold' => $oversoldProducts->map(function ($product) {
                return [
                    'id' => $product->product_id,
                    'name' => $product->full_name,
                    'current_stock' => $product->quantity,
                    'status' => 'oversold'
                ];
            }),
            'high_returns' => $highReturnProducts->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'name' => $item->product->full_name,
                    'return_count' => $item->return_count,
                    'total_returned' => $item->total_returned,
                    'status' => 'high_returns'
                ];
            })
        ];
    }

    /**
     * Update product stock levels in bulk.
     */
    public function bulkUpdateStock(array $stockUpdates): array
    {
        $results = Product::bulkUpdateStock($stockUpdates);
        $successCount = array_sum(array_map('intval', $results));
        $totalCount = count($results);

        return [
            'success' => $successCount === $totalCount,
            'updated_count' => $successCount,
            'total_count' => $totalCount,
            'failed_products' => array_keys(array_filter($results, function ($result) {
                return !$result;
            })),
            'details' => $results
        ];
    }

    /**
     * Search products with advanced filtering.
     */
    public function searchProducts(array $filters): Collection
    {
        $query = Product::query();

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['brand'])) {
            $query->where('product_brand', 'LIKE', "%{$filters['brand']}%");
        }

        if (!empty($filters['category'])) {
            $query->where('product_category', 'LIKE', "%{$filters['category']}%");
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['min_stock'])) {
            $query->where('quantity', '>=', $filters['min_stock']);
        }

        if (isset($filters['max_stock'])) {
            $query->where('quantity', '<=', $filters['max_stock']);
        }

        if (isset($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'in_stock':
                    $query->inStock();
                    break;
                case 'low_stock':
                    $query->lowStock($filters['low_stock_threshold'] ?? 10);
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
            }
        }

        $orderBy = $filters['order_by'] ?? 'product_name';
        $orderDirection = $filters['order_direction'] ?? 'asc';
        $query->orderBy($orderBy, $orderDirection);

        return $query->get();
    }
}
