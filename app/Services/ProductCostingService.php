<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductCostingService
{
    /**
     * Calculate total cost for a product
     * 
     * @param Product $product
     * @param array $costData Cost components
     * @return array Calculated costs
     */
    public function calculateTotalCost(Product $product, array $costData = []): array
    {
        // Get cost components (use provided or existing values)
        $rawMaterialCost = $costData['raw_material_cost'] ?? $product->raw_material_cost ?? 0;
        $laborCost = $costData['labor_cost'] ?? $product->labor_cost ?? 0;
        $overheadCost = $costData['overhead_cost'] ?? $product->overhead_cost ?? 0;
        $shippingCost = $costData['shipping_cost_per_unit'] ?? $product->shipping_cost_per_unit ?? 0;
        $taxAmount = $costData['tax_amount_per_unit'] ?? $product->tax_amount_per_unit ?? 0;
        $handlingCost = $costData['handling_cost'] ?? $product->handling_cost ?? 0;

        // Calculate manufacturing cost
        $manufacturingCost = $rawMaterialCost + $laborCost + $overheadCost;

        // Calculate total cost
        $totalCost = $manufacturingCost + $shippingCost + $taxAmount + $handlingCost;

        // Get selling price
        $price = $costData['price'] ?? $product->price ?? 0;

        // Calculate profit
        $profitAmount = $price - $totalCost;
        $profitMargin = $totalCost > 0 ? (($profitAmount / $totalCost) * 100) : 0;

        return [
            'raw_material_cost' => round($rawMaterialCost, 2),
            'labor_cost' => round($laborCost, 2),
            'overhead_cost' => round($overheadCost, 2),
            'manufacturing_cost' => round($manufacturingCost, 2),
            'shipping_cost_per_unit' => round($shippingCost, 2),
            'tax_amount_per_unit' => round($taxAmount, 2),
            'handling_cost' => round($handlingCost, 2),
            'total_cost' => round($totalCost, 2),
            'price' => round($price, 2),
            'profit_amount' => round($profitAmount, 2),
            'profit_margin' => round($profitMargin, 2),
            'last_cost_update' => now(),
        ];
    }

    /**
     * Update product costing
     * 
     * @param Product $product
     * @param array $costData
     * @return Product
     */
    public function updateProductCosting(Product $product, array $costData): Product
    {
        $calculatedCosts = $this->calculateTotalCost($product, $costData);
        
        // Enable costing fields
        $product->enableCostingFields();
        $product->update($calculatedCosts);

        return $product->fresh();
    }

    /**
     * Bulk update costing for multiple products
     * 
     * @param array $productsData Array of [product_id => cost_data]
     * @return array Statistics
     */
    public function bulkUpdateCosting(array $productsData): array
    {
        $stats = [
            'total' => count($productsData),
            'updated' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($productsData as $productId => $costData) {
            try {
                $product = Product::findOrFail($productId);
                $this->updateProductCosting($product, $costData);
                $stats['updated']++;
            } catch (\Exception $e) {
                $stats['failed']++;
                $stats['errors'][$productId] = $e->getMessage();
            }
        }

        return $stats;
    }

    /**
     * Calculate average costing based on purchase history
     * 
     * @param Product $product
     * @param int $months Number of months to average
     * @return array
     */
    public function calculateAverageCosting(Product $product, int $months = 6): array
    {
        // This would calculate based on purchase orders
        // For now, return empty array - to be implemented with purchase data
        
        return [
            'average_purchase_price' => 0,
            'samples_count' => 0,
            'period_start' => now()->subMonths($months),
            'period_end' => now(),
        ];
    }

    /**
     * Get costing statistics
     * 
     * @return array
     */
    public function getCostingStatistics(): array
    {
        $totalProducts = Product::count();
        $productsWithCosting = Product::whereNotNull('total_cost')->count();
        $productsWithoutCosting = $totalProducts - $productsWithCosting;
        
        $avgTotalCost = Product::whereNotNull('total_cost')
            ->avg('total_cost') ?? 0;
        
        $avgProfitMargin = Product::whereNotNull('profit_margin')
            ->where('profit_margin', '>', 0)
            ->avg('profit_margin') ?? 0;
        
        $lowMarginProducts = Product::where('profit_margin', '<', 20)
            ->whereNotNull('profit_margin')
            ->count();
        
        $negativeMarginProducts = Product::where('profit_margin', '<', 0)
            ->whereNotNull('profit_margin')
            ->count();

        return [
            'total_products' => $totalProducts,
            'products_with_costing' => $productsWithCosting,
            'products_without_costing' => $productsWithoutCosting,
            'costing_completion_percentage' => $totalProducts > 0 
                ? round(($productsWithCosting / $totalProducts) * 100, 1) 
                : 0,
            'average_total_cost' => round($avgTotalCost, 2),
            'average_profit_margin' => round($avgProfitMargin, 2),
            'low_margin_products' => $lowMarginProducts,
            'negative_margin_products' => $negativeMarginProducts,
        ];
    }

    /**
     * Get products by profit margin range
     * 
     * @param float $minMargin
     * @param float|null $maxMargin
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getProductsByMarginRange(float $minMargin, ?float $maxMargin = null, int $perPage = 15)
    {
        $query = Product::whereNotNull('profit_margin')
            ->where('profit_margin', '>=', $minMargin);
        
        if ($maxMargin !== null) {
            $query->where('profit_margin', '<=', $maxMargin);
        }
        
        return $query->orderBy('profit_margin', 'asc')->paginate($perPage);
    }

    /**
     * Get low margin products (below threshold)
     * 
     * @param float $threshold
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowMarginProducts(float $threshold = 20)
    {
        return Product::whereNotNull('profit_margin')
            ->where('profit_margin', '<', $threshold)
            ->where('profit_margin', '>=', 0)
            ->orderBy('profit_margin', 'asc')
            ->get();
    }

    /**
     * Get negative margin products (selling at a loss)
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNegativeMarginProducts()
    {
        return Product::whereNotNull('profit_margin')
            ->where('profit_margin', '<', 0)
            ->orderBy('profit_margin', 'asc')
            ->get();
    }

    /**
     * Suggest optimal price based on desired margin
     * 
     * @param Product $product
     * @param float $desiredMargin Target profit margin percentage
     * @return array
     */
    public function suggestOptimalPrice(Product $product, float $desiredMargin = 30): array
    {
        if (!$product->total_cost) {
            return [
                'success' => false,
                'message' => 'Product must have total cost calculated first',
            ];
        }

        // Price = Total Cost * (1 + Margin%)
        $suggestedPrice = $product->total_cost * (1 + ($desiredMargin / 100));
        $currentPrice = $product->price ?? 0;
        $priceAdjustment = $suggestedPrice - $currentPrice;
        $adjustmentPercentage = $currentPrice > 0 
            ? (($priceAdjustment / $currentPrice) * 100) 
            : 0;

        return [
            'success' => true,
            'current_price' => round($currentPrice, 2),
            'current_margin' => round($product->profit_margin ?? 0, 2),
            'total_cost' => round($product->total_cost, 2),
            'desired_margin' => $desiredMargin,
            'suggested_price' => round($suggestedPrice, 2),
            'price_adjustment' => round($priceAdjustment, 2),
            'adjustment_percentage' => round($adjustmentPercentage, 2),
        ];
    }

    /**
     * Calculate cost breakdown percentages
     * 
     * @param Product $product
     * @return array
     */
    public function getCostBreakdown(Product $product): array
    {
        if (!$product->total_cost || $product->total_cost == 0) {
            return [];
        }

        $totalCost = $product->total_cost;

        return [
            'raw_materials' => [
                'amount' => $product->raw_material_cost ?? 0,
                'percentage' => round((($product->raw_material_cost ?? 0) / $totalCost) * 100, 1),
            ],
            'labor' => [
                'amount' => $product->labor_cost ?? 0,
                'percentage' => round((($product->labor_cost ?? 0) / $totalCost) * 100, 1),
            ],
            'overhead' => [
                'amount' => $product->overhead_cost ?? 0,
                'percentage' => round((($product->overhead_cost ?? 0) / $totalCost) * 100, 1),
            ],
            'shipping' => [
                'amount' => $product->shipping_cost_per_unit ?? 0,
                'percentage' => round((($product->shipping_cost_per_unit ?? 0) / $totalCost) * 100, 1),
            ],
            'tax' => [
                'amount' => $product->tax_amount_per_unit ?? 0,
                'percentage' => round((($product->tax_amount_per_unit ?? 0) / $totalCost) * 100, 1),
            ],
            'handling' => [
                'amount' => $product->handling_cost ?? 0,
                'percentage' => round((($product->handling_cost ?? 0) / $totalCost) * 100, 1),
            ],
            'total' => [
                'amount' => $totalCost,
                'percentage' => 100,
            ],
        ];
    }
}
