<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class StockMovement extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'movement_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'variant_id',
        'user_id',
        'movement_type',
        'quantity_before',
        'quantity_change',
        'quantity_after',
        'unit_cost',
        'total_value',
        'reference_type',
        'reference_id',
        'location_from',
        'location_to',
        'notes',
        'reason',
        'status',
        'movement_date',
    ];

    /**
     * Foreign key attributes for validation.
     *
     * @var array<string, array>
     */
    protected $foreignKeys = [
        'product_id' => ['table' => 'products', 'column' => 'product_id'],
        'variant_id' => ['table' => 'product_variants', 'column' => 'variant_id', 'nullable' => true],
        'user_id' => ['table' => 'users', 'column' => 'id'],
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_change' => 'integer',
        'quantity_after' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_value' => 'decimal:2',
        'movement_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Movement type constants
    const TYPE_SALE = 'sale';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_RETURN = 'return';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_TRANSFER_IN = 'transfer_in';
    const TYPE_TRANSFER_OUT = 'transfer_out';
    const TYPE_AUDIT = 'audit';
    const TYPE_WASTE = 'waste';
    const TYPE_PRODUCTION = 'production';
    const TYPE_INITIAL_STOCK = 'initial_stock';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the product that this movement belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the variant that this movement belongs to.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }

    /**
     * Get the user who performed this movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the display name (variant or product).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->variant_id && $this->variant) {
            return $this->variant->display_name;
        }
        return $this->product->product_name ?? 'Unknown Product';
    }

    /**
     * Get the SKU (variant or product).
     */
    public function getItemSkuAttribute(): ?string
    {
        if ($this->variant_id && $this->variant) {
            return $this->variant->variant_sku;
        }
        return $this->product->sku ?? null;
    }

    /**
     * Scope a query to only include movements of a specific type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('movement_type', $type);
    }

    /**
     * Scope a query to only include confirmed movements.
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope a query to only include pending movements.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include movements within a date range.
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('movement_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include movements for today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('movement_date', Carbon::today());
    }

    /**
     * Scope a query to only include movements for this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('movement_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include movements for this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereBetween('movement_date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ]);
    }

    /**
     * Scope a query to only include inbound movements (increases stock).
     */
    public function scopeInbound(Builder $query): Builder
    {
        return $query->where('quantity_change', '>', 0);
    }

    /**
     * Scope a query to only include outbound movements (decreases stock).
     */
    public function scopeOutbound(Builder $query): Builder
    {
        return $query->where('quantity_change', '<', 0);
    }

    /**
     * Get the movement type label for display.
     */
    public function getMovementTypeLabelAttribute(): string
    {
        return match($this->movement_type) {
            self::TYPE_SALE => 'Sale',
            self::TYPE_PURCHASE => 'Purchase',
            self::TYPE_RETURN => 'Return',
            self::TYPE_ADJUSTMENT => 'Stock Adjustment',
            self::TYPE_TRANSFER_IN => 'Transfer In',
            self::TYPE_TRANSFER_OUT => 'Transfer Out',
            self::TYPE_AUDIT => 'Audit Adjustment',
            self::TYPE_WASTE => 'Waste/Damaged',
            self::TYPE_PRODUCTION => 'Production',
            self::TYPE_INITIAL_STOCK => 'Initial Stock',
            default => ucfirst(str_replace('_', ' ', $this->movement_type)),
        };
    }

    /**
     * Check if this movement increases stock.
     */
    public function isInbound(): bool
    {
        return $this->quantity_change > 0;
    }

    /**
     * Check if this movement decreases stock.
     */
    public function isOutbound(): bool
    {
        return $this->quantity_change < 0;
    }

    /**
     * Get the absolute value of quantity change.
     */
    public function getAbsoluteQuantityAttribute(): int
    {
        return abs($this->quantity_change);
    }

    /**
     * Record a stock movement.
     */
    public static function recordMovement(
        int $productId,
        int $quantityBefore,
        int $quantityChange,
        int $quantityAfter,
        string $movementType,
        ?int $userId = null,
        ?float $unitCost = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $locationFrom = null,
        ?string $locationTo = null,
        ?string $notes = null,
        ?string $reason = null,
        string $status = self::STATUS_CONFIRMED,
        ?Carbon $movementDate = null,
        ?int $variantId = null
    ): self {
        $totalValue = $unitCost ? ($unitCost * abs($quantityChange)) : null;
        
        return self::create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'user_id' => $userId ?? auth()->id(),
            'movement_type' => $movementType,
            'quantity_before' => $quantityBefore,
            'quantity_change' => $quantityChange,
            'quantity_after' => $quantityAfter,
            'unit_cost' => $unitCost,
            'total_value' => $totalValue,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'location_from' => $locationFrom,
            'location_to' => $locationTo,
            'notes' => $notes,
            'reason' => $reason,
            'status' => $status,
            'movement_date' => $movementDate ?? now(),
        ]);
    }

    /**
     * Get stock movements for a specific product.
     */
    public static function forProduct(int $productId)
    {
        return self::where('product_id', $productId)
                  ->with(['user'])
                  ->orderBy('movement_date', 'desc')
                  ->get();
    }

    /**
     * Get total stock changes for a product.
     */
    public static function getTotalStockChange(int $productId, ?string $type = null): int
    {
        $query = self::where('product_id', $productId)->confirmed();
        
        if ($type) {
            $query->ofType($type);
        }
        
        return $query->sum('quantity_change');
    }

    /**
     * Get movement summary by type.
     */
    public static function getMovementSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = self::confirmed();
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->selectRaw('movement_type, COUNT(*) as count, SUM(ABS(quantity_change)) as total_quantity, SUM(total_value) as total_value')
                    ->groupBy('movement_type')
                    ->orderBy('count', 'desc')
                    ->get()
                    ->toArray();
    }

    /**
     * Get recent movements.
     */
    public static function getRecentMovements(int $limit = 10)
    {
        return self::with(['product', 'user'])
                  ->confirmed()
                  ->orderBy('movement_date', 'desc')
                  ->limit($limit)
                  ->get();
    }
}
