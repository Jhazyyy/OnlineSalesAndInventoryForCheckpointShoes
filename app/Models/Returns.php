<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Returns extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'return_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'customer_id',
        'sales_order_id',
        'quantity',
        'return_status',
        'price',
        'return_date',
        'reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'return_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Return status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PROCESSED = 'processed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Boot the model and add validation for duplicate returns.
     */
    protected static function boot()
    {
        parent::boot();

        // Prevent creating duplicate returns for the same sales order and product
        static::creating(function ($return) {
            if ($return->sales_order_id && $return->product_id) {
                $existingReturn = self::where('sales_order_id', $return->sales_order_id)
                    ->where('product_id', $return->product_id)
                    ->whereIn('return_status', [self::STATUS_APPROVED, self::STATUS_PROCESSED])
                    ->first();

                if ($existingReturn) {
                    throw new \Exception(
                        'A return has already been processed for this product in sales order #' . 
                        $return->sales_order_id . '. Return #' . $existingReturn->return_id . 
                        ' is already ' . $existingReturn->return_status . '.'
                    );
                }
            }
        });
    }

    /**
     * Get all available return statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_PROCESSED,
            self::STATUS_REFUNDED,
        ];
    }

    /**
     * Get the product that owns the return.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the customer that owns the return.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the sales order associated with the return.
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'order_id');
    }

    /**
     * Scope a query to only include returns from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('return_date', Carbon::today());
    }

    /**
     * Scope a query to only include returns from this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('return_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include returns from this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('return_date', Carbon::now()->month)
                    ->whereYear('return_date', Carbon::now()->year);
    }

    /**
     * Scope a query to filter returns by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('return_status', $status);
    }

    /**
     * Scope a query to only include pending returns.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('return_status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved returns.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('return_status', self::STATUS_APPROVED);
    }

    /**
     * Get the total return amount.
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->quantity * $this->price;
    }

    /**
     * Check if the return is pending.
     */
    public function isPending(): bool
    {
        return $this->return_status === self::STATUS_PENDING;
    }

    /**
     * Check if the return is approved.
     */
    public function isApproved(): bool
    {
        return $this->return_status === self::STATUS_APPROVED;
    }

    /**
     * Check if the return is processed.
     */
    public function isProcessed(): bool
    {
        return $this->return_status === self::STATUS_PROCESSED;
    }

    /**
     * Process a new return.
     */
    public static function processReturn(
        int $productId,
        int $quantity,
        float $price,
        string $status = self::STATUS_PENDING,
        $returnDate = null
    ): ?self {
        $product = Product::find($productId);
        
        if (!$product) {
            return null;
        }

        return self::create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'return_status' => $status,
            'price' => $price,
            'return_date' => $returnDate ?? now(),
        ]);
    }

    /**
     * Approve the return and update inventory.
     */
    public function approve(): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->return_status = self::STATUS_APPROVED;
        $saved = $this->save();

        if ($saved) {
            // Add back to inventory using InventoryService which properly updates both 
            // the Inventory table and Product quantity
            \App\Services\InventoryService::adjust(
                productId: $this->product_id,
                quantityChange: $this->quantity,
                unitCost: $this->price,
                movementType: \App\Models\StockMovement::TYPE_RETURN,
                referenceType: 'sales_return',
                referenceId: $this->return_id,
                syncProductQuantity: true
            );
        }

        return $saved;
    }

    /**
     * Reject the return.
     */
    public function reject(): bool
    {
        if (!$this->isPending()) {
            return false;
        }

        $this->return_status = self::STATUS_REJECTED;
        return $this->save();
    }

    /**
     * Mark the return as processed.
     */
    public function markAsProcessed(): bool
    {
        // Only approved returns can be marked as processed
        if (!$this->isApproved()) {
            return false;
        }

        // Prevent processing already processed returns
        if ($this->isProcessed()) {
            return false;
        }

        $this->return_status = self::STATUS_PROCESSED;
        return $this->save();
    }

    /**
     * Calculate total return amount for a given period.
     */
    public static function totalReturnAmount($startDate = null, $endDate = null): float
    {
        $query = self::approved();
        
        if ($startDate && $endDate) {
            $query->whereBetween('return_date', [$startDate, $endDate]);
        }
        
        return $query->get()->sum('total_amount');
    }

    /**
     * Get most returned products.
     */
    public static function mostReturnedProducts(int $limit = 10)
    {
        return self::selectRaw('product_id, SUM(quantity) as total_returned, COUNT(*) as return_count')
                   ->with('product')
                   ->approved()
                   ->groupBy('product_id')
                   ->orderByDesc('total_returned')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Get return statistics by status.
     */
    public static function returnsByStatus()
    {
        return self::selectRaw('return_status, COUNT(*) as count, SUM(quantity * price) as total_amount')
                   ->groupBy('return_status')
                   ->get()
                   ->keyBy('return_status');
    }
}
