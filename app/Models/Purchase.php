<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Purchase extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'purchase_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_id',
        'product_id',
        'user_id',
        'price',
        'quantity',
        'purchase_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'purchase_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the purchase.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the supplier that owns the purchase.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get the user that made the purchase.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope a query to only include purchases from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('purchase_date', Carbon::today());
    }

    /**
     * Scope a query to only include purchases from this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('purchase_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include purchases from this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('purchase_date', Carbon::now()->month)
                    ->whereYear('purchase_date', Carbon::now()->year);
    }

    /**
     * Scope a query to only include purchases from a date range.
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('purchase_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter purchases by supplier.
     */
    public function scopeBySupplier(Builder $query, int $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Get the total purchase amount.
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->quantity * $this->price;
    }

    /**
     * Process a new purchase and update product inventory.
     */
    public static function processPurchase(
        int $supplierId,
        int $productId,
        int $userId,
        float $price,
        int $quantity,
        $purchaseDate = null
    ): ?self {
        $product = Product::find($productId);
        
        if (!$product) {
            return null;
        }

        // Create the purchase record
        $purchase = self::create([
            'supplier_id' => $supplierId,
            'product_id' => $productId,
            'user_id' => $userId,
            'price' => $price,
            'quantity' => $quantity,
            'purchase_date' => $purchaseDate ?? now(),
        ]);

        // Update product inventory
        $product->increaseStock($quantity);
        
        // Update product price if different
        if ($product->price != $price) {
            $product->update(['price' => $price]);
        }

        return $purchase;
    }

    /**
     * Calculate total purchase amount for a given period.
     */
    public static function totalPurchaseAmount($startDate = null, $endDate = null): float
    {
        $query = self::query();
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->selectRaw('SUM(price * quantity) as total')
                    ->value('total') ?? 0;
    }

    /**
     * Get purchase statistics by supplier.
     */
    public static function purchasesBySupplier()
    {
        return self::selectRaw('supplier_id, COUNT(*) as total_purchases, SUM(price * quantity) as total_amount')
                   ->with('supplier')
                   ->groupBy('supplier_id')
                   ->orderByDesc('total_amount')
                   ->get();
    }

    /**
     * Get most purchased products.
     */
    public static function mostPurchasedProducts(int $limit = 10)
    {
        return self::selectRaw('product_id, SUM(quantity) as total_purchased, SUM(price * quantity) as total_spent')
                   ->with('product')
                   ->groupBy('product_id')
                   ->orderByDesc('total_purchased')
                   ->limit($limit)
                   ->get();
    }
}
