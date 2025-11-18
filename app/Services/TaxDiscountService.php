<?php

namespace App\Services;

use App\Models\TaxDiscount;
use App\Models\Product;
use Illuminate\Support\Collection;

class TaxDiscountService
{
    /**
     * Calculate taxes and discounts for an order
     * 
     * @param float $subtotal The order subtotal before taxes and discounts
     * @param Collection|array $items The order items (with product_id)
     * @param string $orderType 'sales' or 'purchase'
     * @return array ['taxes' => [...], 'discounts' => [...], 'total_tax' => 0.00, 'total_discount' => 0.00]
     */
    public function calculateForOrder(float $subtotal, $items, string $orderType = 'sales'): array
    {
        // Get applicable taxes and discounts
        $taxes = TaxDiscount::active()->taxes()->orderBy('priority')->get();
        $discounts = TaxDiscount::active()->discounts()->orderBy('priority')->get();
        
        $result = [
            'taxes' => [],
            'discounts' => [],
            'total_tax' => 0,
            'total_discount' => 0,
            'breakdown' => []
        ];
        
        // Calculate taxes
        $taxAccumulator = 0;
        foreach ($taxes as $tax) {
            if ($this->isApplicableToOrder($tax, $items)) {
                $taxAmount = $tax->calculateAmount($subtotal, $taxAccumulator);
                $result['taxes'][] = [
                    'id' => $tax->id,
                    'name' => $tax->name,
                    'code' => $tax->code,
                    'rate' => $tax->rate,
                    'amount' => $taxAmount,
                    'is_compound' => $tax->is_compound,
                ];
                $taxAccumulator += $taxAmount;
            }
        }
        $result['total_tax'] = round($taxAccumulator, 2);
        
        // Calculate discounts
        $discountAccumulator = 0;
        foreach ($discounts as $discount) {
            if ($this->isApplicableToOrder($discount, $items)) {
                $discountAmount = $discount->calculateAmount($subtotal, $discountAccumulator);
                $result['discounts'][] = [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'code' => $discount->code,
                    'rate' => $discount->rate,
                    'amount' => $discountAmount,
                    'is_compound' => $discount->is_compound,
                ];
                $discountAccumulator += $discountAmount;
            }
        }
        $result['total_discount'] = round($discountAccumulator, 2);
        
        // Create detailed breakdown
        $result['breakdown'] = [
            'subtotal' => $subtotal,
            'total_tax' => $result['total_tax'],
            'total_discount' => $result['total_discount'],
            'net_amount' => $subtotal + $result['total_tax'] - $result['total_discount'],
        ];
        
        return $result;
    }
    
    /**
     * Check if a tax/discount is applicable to the order items
     * 
     * @param TaxDiscount $taxDiscount
     * @param Collection|array $items
     * @return bool
     */
    protected function isApplicableToOrder(TaxDiscount $taxDiscount, $items): bool
    {
        // If applies to all products, return true
        if ($taxDiscount->applies_to === 'all') {
            return true;
        }
        
        // Check if any order item matches the applicable categories or products
        $applicableCategories = $taxDiscount->applicable_categories ?? [];
        if (is_string($applicableCategories)) {
            $applicableCategories = json_decode($applicableCategories, true) ?? [];
        }
        $applicableCategories = is_array($applicableCategories) ? $applicableCategories : [];
        
        $applicableProducts = $taxDiscount->applicable_products ?? [];
        if (is_string($applicableProducts)) {
            $applicableProducts = json_decode($applicableProducts, true) ?? [];
        }
        $applicableProducts = is_array($applicableProducts) ? $applicableProducts : [];
        
        foreach ($items as $item) {
            // Get the product
            $productId = is_array($item) ? ($item['product_id'] ?? null) : $item->product_id;
            
            if (!$productId) {
                continue;
            }
            
            // Check if product is directly applicable
            if (in_array($productId, $applicableProducts)) {
                return true;
            }
            
            // Check if product's category is applicable
            if (!empty($applicableCategories)) {
                $product = Product::find($productId);
                if ($product && in_array($product->product_category, $applicableCategories)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Calculate taxes only for an order
     * 
     * @param float $subtotal
     * @param Collection|array $items
     * @return float
     */
    public function calculateTaxes(float $subtotal, $items): float
    {
        $result = $this->calculateForOrder($subtotal, $items);
        return $result['total_tax'];
    }
    
    /**
     * Calculate discounts only for an order
     * 
     * @param float $subtotal
     * @param Collection|array $items
     * @return float
     */
    public function calculateDiscounts(float $subtotal, $items): float
    {
        $result = $this->calculateForOrder($subtotal, $items);
        return $result['total_discount'];
    }
    
    /**
     * Get active taxes
     * 
     * @return Collection
     */
    public function getActiveTaxes(): Collection
    {
        return TaxDiscount::active()->taxes()->orderBy('priority')->get();
    }
    
    /**
     * Get active discounts
     * 
     * @return Collection
     */
    public function getActiveDiscounts(): Collection
    {
        return TaxDiscount::active()->discounts()->orderBy('priority')->get();
    }
    
    /**
     * Format tax/discount breakdown for display
     * 
     * @param array $calculation Result from calculateForOrder
     * @return array
     */
    public function formatBreakdown(array $calculation): array
    {
        $formatted = [
            'subtotal' => '₱' . number_format($calculation['breakdown']['subtotal'], 2),
            'taxes' => [],
            'discounts' => [],
            'total_tax' => '₱' . number_format($calculation['total_tax'], 2),
            'total_discount' => '₱' . number_format($calculation['total_discount'], 2),
            'net_amount' => '₱' . number_format($calculation['breakdown']['net_amount'], 2),
        ];
        
        foreach ($calculation['taxes'] as $tax) {
            $formatted['taxes'][] = [
                'name' => $tax['name'],
                'amount' => '₱' . number_format($tax['amount'], 2),
            ];
        }
        
        foreach ($calculation['discounts'] as $discount) {
            $formatted['discounts'][] = [
                'name' => $discount['name'],
                'amount' => '₱' . number_format($discount['amount'], 2),
            ];
        }
        
        return $formatted;
    }
}
