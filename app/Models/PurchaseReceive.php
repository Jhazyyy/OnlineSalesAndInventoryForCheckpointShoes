<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PurchaseReceive extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'receive_id';
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'receive_id';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receive_number',
        'reference_number',
        'purchase_order_id',
        'delivery_id',  // Added: Link to delivery
        'supplier_id',
        'receive_date',
        'status',
        'total_quantity_expected',
        'total_quantity_received',
        'total_amount_expected',
        'total_amount_received',
        'receiving_notes',
        'damage_notes',
        'receiver_name',
        'delivery_address',
        'is_short_closed',
        'short_close_reason',
        'short_closed_at',
        'short_closed_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'receive_date' => 'date',
        'total_amount_expected' => 'decimal:2',
        'total_amount_received' => 'decimal:2',
        'is_short_closed' => 'boolean',
        'short_closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the purchase order that owns the receive.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'order_id');
    }

    /**
     * Get the delivery that owns the receive.
     * 
     * WORKFLOW: A receive can be created from a delivery (optional)
     * This links the shipment tracking to the inventory receipt.
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(PurchaseDelivery::class, 'delivery_id', 'delivery_id');
    }

    /**
     * Get the supplier that owns the receive.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get the receive items for the purchase receive.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReceiveItem::class, 'receive_id', 'receive_id');
    }

    /**
     * Generate unique GRN (Goods Receipt Note) number.
     * Format: GRN-0001, GRN-0002, etc.
     */
    public static function generateReceiveNumber(): string
    {
        $prefix = 'PR-';
        $lastReceive = static::where('receive_number', 'LIKE', $prefix . '%')
                          ->orderBy('receive_number', 'desc')
                          ->first();
        
        if ($lastReceive) {
            // Extract the numeric part after "GRN-"
            $lastNumber = intval(substr($lastReceive->receive_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to auto-generate receive number.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($receive) {
            if (empty($receive->receive_number)) {
                $receive->receive_number = static::generateReceiveNumber();
            }
            if (empty($receive->reference_number)) {
                try {
                    $receive->reference_number = \App\Services\ReferenceNumberService::generate('purchase_receives', 'reference_number', 'GR');
                } catch (\Throwable $e) {
                    // Fallback to receive number
                    $receive->reference_number = $receive->receive_number;
                }
            }
        });
    }

    /**
     * Scope a query to only include in transit receives.
     */
    public function scopeInTransit(Builder $query): Builder
    {
        return $query->where('status', 'in_transit');
    }

    /**
     * Scope a query to only include received items.
     */
    public function scopeReceived(Builder $query): Builder
    {
        return $query->where('status', 'received');
    }

    /**
     * Scope a query to only include partially received items.
     */
    public function scopePartiallyReceived(Builder $query): Builder
    {
        return $query->where('status', 'partially_received');
    }

    /**
     * Scope a query to only include damaged items.
     */
    public function scopeDamaged(Builder $query): Builder
    {
        return $query->where('status', 'damaged');
    }

    /**
     * Scope a query to only include cancelled items.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include receives from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('receive_date', Carbon::today());
    }

    /**
     * Scope a query to only include receives from this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('receive_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include receives from this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('receive_date', Carbon::now()->month)
                     ->whereYear('receive_date', Carbon::now()->year);
    }

    /**
     * Get status badge class for UI styling.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'in_transit' => 'bg-yellow-100 text-yellow-800',
            'received' => 'bg-green-100 text-green-800',
            'partially_received' => 'bg-blue-100 text-blue-800',
            'damaged' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Calculate completion percentage.
     */
    public function getCompletionPercentageAttribute(): float
    {
        if ($this->total_quantity_expected == 0) {
            return 0;
        }
        
        return round(($this->total_quantity_received / $this->total_quantity_expected) * 100, 1);
    }

    /**
     * Check if receive can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['in_transit', 'partially_received']);
    }

    /**
     * Check if receive can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['in_transit', 'partially_received']) && !$this->is_short_closed;
    }

    /**
     * Check if receive can be short closed.
     * A receive can be short closed if:
     * - It has status 'partially_received' (not all items received)
     * - It hasn't been fully received yet
     * - It's not already short closed
     */
    public function canBeShortClosed(): bool
    {
        return in_array($this->status, ['partially_received', 'in_transit']) && 
               !$this->is_short_closed &&
               $this->total_quantity_received < $this->total_quantity_expected;
    }

    /**
     * Get the user who short closed this receive.
     */
    public function shortClosedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'short_closed_by');
    }
}
