<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxDiscount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'applicable_for',
        'rate',
        'calculation_method',
        'fixed_amount',
        'description',
        'applies_to',
        'applicable_categories',
        'applicable_products',
        'is_compound',
        'priority',
        'is_active',
        'valid_from',
        'valid_to',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'fixed_amount' => 'decimal:2',
        'applicable_categories' => 'array',
        'applicable_products' => 'array',
        'is_compound' => 'boolean',
        'is_active' => 'boolean',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'priority' => 'integer',
    ];

    /**
     * Get categories if applicable
     */
    public function categories()
    {
        if ($this->applies_to === 'specific' && $this->applicable_categories) {
            return Category::whereIn('id', $this->applicable_categories)->get();
        }
        return collect();
    }

    /**
     * Get products if applicable
     */
    public function products()
    {
        if ($this->applies_to === 'specific' && $this->applicable_products) {
            return Product::whereIn('id', $this->applicable_products)->get();
        }
        return collect();
    }

    /**
     * Scope to get only active tax/discounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', now());
            });
    }

    /**
     * Scope to get only taxes
     */
    public function scopeTaxes($query)
    {
        return $query->where('type', 'tax');
    }

    /**
     * Scope to get only discounts
     */
    public function scopeDiscounts($query)
    {
        return $query->where('type', 'discount');
    }

    /**
     * Scope to get supplier applicable discounts/taxes
     */
    public function scopeForSupplier($query)
    {
        return $query->whereIn('applicable_for', ['supplier', 'both']);
    }

    /**
     * Scope to get customer applicable discounts/taxes
     */
    public function scopeForCustomer($query)
    {
        return $query->whereIn('applicable_for', ['customer', 'both']);
    }

    /**
     * Calculate the tax/discount amount for a given subtotal
     */
    public function calculateAmount($subtotal, $previousAmount = 0)
    {
        if ($this->calculation_method === 'fixed') {
            return $this->fixed_amount;
        }

        // For compound taxes/discounts, apply to subtotal + previous amounts
        $baseAmount = $this->is_compound ? ($subtotal + $previousAmount) : $subtotal;
        
        return round(($baseAmount * $this->rate) / 100, 2);
    }

    /**
     * Calculate profit breakdown for a product
     * This shows how taxes and discounts affect the final profit
     */
    public static function calculateProfitBreakdown($costPrice, $sellingPrice, $quantity = 1)
    {
        $subtotal = $sellingPrice * $quantity;
        $totalCost = $costPrice * $quantity;
        $grossProfit = $subtotal - $totalCost;
        
        // Get active taxes and discounts ordered by priority
        $taxes = self::active()->taxes()->orderBy('priority')->get();
        $discounts = self::active()->discounts()->orderBy('priority')->get();
        
        $breakdown = [
            'subtotal' => $subtotal,
            'total_cost' => $totalCost,
            'gross_profit' => $grossProfit,
            'gross_profit_percentage' => $subtotal > 0 ? round(($grossProfit / $subtotal) * 100, 2) : 0,
            'taxes' => [],
            'total_tax' => 0,
            'discounts' => [],
            'total_discount' => 0,
            'net_amount' => 0,
            'net_profit' => 0,
            'net_profit_percentage' => 0,
        ];
        
        // Calculate taxes
        $taxAccumulator = 0;
        foreach ($taxes as $tax) {
            $taxAmount = $tax->calculateAmount($subtotal, $taxAccumulator);
            $breakdown['taxes'][] = [
                'name' => $tax->name,
                'rate' => $tax->rate,
                'amount' => $taxAmount,
                'is_compound' => $tax->is_compound,
            ];
            $taxAccumulator += $taxAmount;
        }
        $breakdown['total_tax'] = $taxAccumulator;
        
        // Calculate discounts
        $discountAccumulator = 0;
        foreach ($discounts as $discount) {
            $discountAmount = $discount->calculateAmount($subtotal, $discountAccumulator);
            $breakdown['discounts'][] = [
                'name' => $discount->name,
                'rate' => $discount->rate,
                'amount' => $discountAmount,
                'is_compound' => $discount->is_compound,
            ];
            $discountAccumulator += $discountAmount;
        }
        $breakdown['total_discount'] = $discountAccumulator;
        
        // Calculate final amounts
        $breakdown['net_amount'] = $subtotal + $breakdown['total_tax'] - $breakdown['total_discount'];
        $breakdown['net_profit'] = $breakdown['net_amount'] - $totalCost;
        $breakdown['net_profit_percentage'] = $breakdown['net_amount'] > 0 
            ? round(($breakdown['net_profit'] / $breakdown['net_amount']) * 100, 2) 
            : 0;
        
        return $breakdown;
    }

    /**
     * Check if this tax/discount is currently valid
     */
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->valid_from && $this->valid_from > $now) {
            return false;
        }

        if ($this->valid_to && $this->valid_to < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted rate display
     */
    public function getFormattedRateAttribute()
    {
        if ($this->calculation_method === 'fixed') {
            return '₱' . number_format((float) $this->fixed_amount, 2);
        }
        return $this->rate . '%';
    }

    /**
     * Get type badge color
     */
    public function getTypeBadgeColorAttribute()
    {
        return $this->type === 'tax' ? 'blue' : 'green';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return $this->isValid() ? 'green' : 'red';
    }
}
