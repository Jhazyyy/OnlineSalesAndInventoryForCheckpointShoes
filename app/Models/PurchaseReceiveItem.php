<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReceiveItem extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'item_id';
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'item_id';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receive_id',
        'product_id',
        'purchase_order_item_id',
        'quantity_expected',
        'quantity_received',
        'quantity_damaged',
        'unit_price',
        'total_amount',
        'condition',
        'item_notes',
        'is_short_closed',
        'short_close_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_short_closed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the purchase receive that owns the item.
     */
    public function purchaseReceive(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceive::class, 'receive_id', 'receive_id');
    }

    /**
     * Get the product that owns the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the purchase order item that this receive item references.
     */
    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id', 'item_id');
    }

    /**
     * Get condition badge class for UI styling.
     */
    public function getConditionBadgeClassAttribute(): string
    {
        return match ($this->condition) {
            'good' => 'bg-green-100 text-green-800',
            'damaged' => 'bg-red-100 text-red-800',
            'expired' => 'bg-orange-100 text-orange-800',
            'partial' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Calculate the receive percentage for this item.
     */
    public function getReceivePercentageAttribute(): float
    {
        if ($this->quantity_expected == 0) {
            return 0;
        }
        
        return round(($this->quantity_received / $this->quantity_expected) * 100, 1);
    }

    /**
     * Check if item is fully received.
     */
    public function isFullyReceived(): bool
    {
        return $this->quantity_received >= $this->quantity_expected;
    }

    /**
     * Check if item is partially received.
     */
    public function isPartiallyReceived(): bool
    {
        return $this->quantity_received > 0 && $this->quantity_received < $this->quantity_expected;
    }

    /**
     * Check if item has damage.
     */
    public function hasDamage(): bool
    {
        return $this->quantity_damaged > 0;
    }
}
