<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Exchange extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'exchange_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'sales_order_id',
        'exchange_number',
        'exchange_type',
        'status',
        'reason',
        'original_total_amount',
        'new_total_amount',
        'difference_amount',
        'exchange_date',
        'requested_completion_date',
        'actual_completion_date',
        'notes',
        'internal_notes',
        'metadata',
        'processed_by',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'customer_id' => 'integer',
        'sales_order_id' => 'integer',
        'original_total_amount' => 'decimal:2',
        'new_total_amount' => 'decimal:2',
        'difference_amount' => 'decimal:2',
        'exchange_date' => 'date',
        'requested_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'metadata' => 'array',
        'processed_by' => 'integer',
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Exchange status constants.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Exchange type constants.
     */
    const TYPE_PRODUCT_EXCHANGE = 'product_exchange';
    const TYPE_REFUND_EXCHANGE = 'refund_exchange';
    const TYPE_UPGRADE_EXCHANGE = 'upgrade_exchange';

    /**
     * Get all available exchange statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Get all available exchange types.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_PRODUCT_EXCHANGE,
            self::TYPE_REFUND_EXCHANGE,
            self::TYPE_UPGRADE_EXCHANGE,
        ];
    }

    /**
     * Get the customer that owns the exchange.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the sales order associated with the exchange.
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'sales_order_id');
    }

    /**
     * Get the user who processed the exchange.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the exchange items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ExchangeItem::class, 'exchange_id', 'exchange_id');
    }

    /**
     * Get the original items (items being returned).
     */
    public function originalItems(): HasMany
    {
        return $this->hasMany(ExchangeItem::class, 'exchange_id', 'exchange_id')
                    ->where('item_type', 'original');
    }

    /**
     * Get the new items (items being given).
     */
    public function newItems(): HasMany
    {
        return $this->hasMany(ExchangeItem::class, 'exchange_id', 'exchange_id')
                    ->where('item_type', 'new');
    }

    /**
     * Scope a query to only include exchanges from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('exchange_date', Carbon::today());
    }

    /**
     * Scope a query to only include exchanges from this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('exchange_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include exchanges from this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('exchange_date', Carbon::now()->month)
                    ->whereYear('exchange_date', Carbon::now()->year);
    }

    /**
     * Scope a query to filter exchanges by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter exchanges by type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('exchange_type', $type);
    }

    /**
     * Scope a query to only include pending exchanges.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved exchanges.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include processing exchanges.
     */
    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    /**
     * Scope a query to only include completed exchanges.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Check if the exchange is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the exchange is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the exchange is processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Check if the exchange is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the exchange is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Generate a unique exchange number.
     */
    public static function generateExchangeNumber(): string
    {
        do {
            $number = 'EXC-' . date('Y') . '-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('exchange_number', $number)->exists());
        
        return $number;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($exchange) {
            if (empty($exchange->exchange_number)) {
                $exchange->exchange_number = self::generateExchangeNumber();
            }
        });
    }
}
