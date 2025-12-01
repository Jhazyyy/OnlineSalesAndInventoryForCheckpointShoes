<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'sales_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'customer_id',
        'quantity',
        'unit_price',
        'total_amount',
        'sale_type',
        'notes',
        'date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the sale.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the customer that owns the sale.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope a query to only include sales from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('date', Carbon::today());
    }

    /**
     * Scope a query to only include sales from this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include sales from this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year);
    }

    /**
     * Scope a query to only include sales from a date range.
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Get the calculated total amount if not stored.
     */
    public function getCalculatedTotalAttribute(): float
    {
        if ($this->total_amount) {
            return (float) $this->total_amount;
        }
        
        $unitPrice = $this->unit_price ?? ($this->product ? $this->product->price : 0);
        return $this->quantity * $unitPrice;
    }

    /**
     * Scope a query to only include sales for a specific customer.
     */
    public function scopeForCustomer(Builder $query, $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope a query to only include sales of a specific type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('sale_type', $type);
    }

    /**
     * Process a new sale and update product inventory.
     */
    public static function processSale(int $productId, int $quantity, ?int $customerId = null, ?float $unitPrice = null, $date = null): ?self
    {
        $product = Product::find($productId);
        
        if (!$product || !$product->isInStock($quantity)) {
            return null;
        }

        $saleUnitPrice = $unitPrice ?? $product->price;
        $totalAmount = $quantity * $saleUnitPrice;

        // Create the sale record
        $sale = self::create([
            'product_id' => $productId,
            'customer_id' => $customerId,
            'quantity' => $quantity,
            'unit_price' => $saleUnitPrice,
            'total_amount' => $totalAmount,
            'date' => $date ?? now(),
        ]);

        // Update product inventory
        $product->decreaseStock($quantity);

        return $sale;
    }

    /**
     * Calculate total sales amount for a given period.
     */
    public static function totalSalesAmount($startDate = null, $endDate = null): float
    {
        $query = self::with('product');
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->get()->sum('total_amount');
    }

    /**
     * Get top selling products.
     */
    public static function topSellingProducts(int $limit = 10)
    {
        return self::selectRaw('product_id, SUM(quantity) as total_sold')
                   ->with('product')
                   ->groupBy('product_id')
                   ->orderByDesc('total_sold')
                   ->limit($limit)
                   ->get();
    }
}
