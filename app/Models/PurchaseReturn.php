<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PurchaseReturn extends Model
{
    use HasFactory;

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
        'purchase_order_id',
        'product_id',
        'supplier_id',
        'quantity',
        'price',
        'total_amount',
        'return_date',
        'return_status',
        'reason',
        'notes',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'total_amount' => 'decimal:2',
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
     * Get the supplier that owns the return.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Get the purchase order associated with this return.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Get the user who created this return.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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
     * Scope a query to filter by supplier.
     */
    public function scopeBySupplier(Builder $query, int $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Get the calculated total amount if not stored.
     */
    public function getCalculatedTotalAttribute(): float
    {
        if ($this->total_amount) {
            return (float) $this->total_amount;
        }
        
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
     * Check if the return is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->return_status === self::STATUS_REFUNDED;
    }

    /**
     * Process a new purchase return.
     */
    public static function processPurchaseReturn(
        int $productId,
        int $supplierId,
        int $quantity,
        float $price,
        string $status = self::STATUS_PENDING,
        $returnDate = null,
        ?int $purchaseOrderId = null,
        ?string $reason = null,
        ?int $createdBy = null
    ): ?self {
        $product = Product::find($productId);
        $supplier = Supplier::find($supplierId);
        
        if (!$product || !$supplier) {
            return null;
        }

        $totalAmount = $quantity * $price;

        return self::create([
            'purchase_order_id' => $purchaseOrderId,
            'product_id' => $productId,
            'supplier_id' => $supplierId,
            'quantity' => $quantity,
            'price' => $price,
            'total_amount' => $totalAmount,
            'return_date' => $returnDate ?? now(),
            'return_status' => $status,
            'reason' => $reason,
            'created_by' => $createdBy,
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
            // Decrease inventory as we're returning items to supplier
            $this->product->decreaseStock($this->quantity);
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
        if (!$this->isApproved()) {
            return false;
        }

        $this->return_status = self::STATUS_PROCESSED;
        return $this->save();
    }

    /**
     * Mark the return as refunded.
     */
    public function markAsRefunded(): bool
    {
        if (!$this->isProcessed()) {
            return false;
        }

        $this->return_status = self::STATUS_REFUNDED;
        return $this->save();
    }

    /**
     * Calculate total purchase return amount for a given period.
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
     * Get most returned products to suppliers.
     */
    public static function mostReturnedProducts(int $limit = 10)
    {
        return self::selectRaw('product_id, SUM(quantity) as total_returned, COUNT(*) as return_count, SUM(total_amount) as total_amount')
                   ->with('product')
                   ->approved()
                   ->groupBy('product_id')
                   ->orderByDesc('total_returned')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Get return statistics by supplier.
     */
    public static function returnsBySupplier()
    {
        return self::selectRaw('supplier_id, COUNT(*) as return_count, SUM(total_amount) as total_amount')
                   ->with('supplier')
                   ->approved()
                   ->groupBy('supplier_id')
                   ->orderByDesc('total_amount')
                   ->get();
    }

    /**
     * Get return statistics by status.
     */
    public static function returnsByStatus()
    {
        return self::selectRaw('return_status, COUNT(*) as count, SUM(total_amount) as total_amount')
                   ->groupBy('return_status')
                   ->get()
                   ->keyBy('return_status');
    }
}
