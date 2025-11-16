<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseDeliveryItem extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'item_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'delivery_id',
        'product_id',
        'purchase_order_item_id',
        'quantity_expected',
        'quantity_delivered',
        'quantity_damaged',
        'quantity_missing',
        'unit_price',
        'line_total',
        'condition',
        'item_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity_expected' => 'integer',
        'quantity_delivered' => 'integer',
        'quantity_damaged' => 'integer',
        'quantity_missing' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the delivery that owns the item.
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(PurchaseDelivery::class, 'delivery_id', 'delivery_id');
    }

    /**
     * Get the product that owns the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the purchase order item that owns the delivery item.
     */
    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id', 'item_id');
    }

    /**
     * Boot method to auto-calculate line total.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($item) {
            $item->line_total = $item->quantity_delivered * $item->unit_price;
        });
        
        static::updating(function ($item) {
            $item->line_total = $item->quantity_delivered * $item->unit_price;
        });
    }

    /**
     * Get the delivery percentage for this item.
     */
    public function getDeliveryPercentageAttribute(): float
    {
        if ($this->quantity_expected == 0) {
            return 0;
        }
        
        return round(($this->quantity_delivered / $this->quantity_expected) * 100, 2);
    }

    /**
     * Check if item is fully delivered.
     */
    public function isFullyDelivered(): bool
    {
        return $this->quantity_delivered >= $this->quantity_expected;
    }

    /**
     * Check if item is partially delivered.
     */
    public function isPartiallyDelivered(): bool
    {
        return $this->quantity_delivered > 0 && $this->quantity_delivered < $this->quantity_expected;
    }

    /**
     * Check if item has damages.
     */
    public function hasDamages(): bool
    {
        return $this->quantity_damaged > 0;
    }

    /**
     * Check if item has missing quantities.
     */
    public function hasMissingQuantities(): bool
    {
        return $this->quantity_missing > 0;
    }

    /**
     * Get condition badge class for UI.
     */
    public function getConditionBadgeClassAttribute(): string
    {
        return match($this->condition) {
            'good' => 'bg-green-100 text-green-800',
            'shortage' => 'bg-yellow-100 text-yellow-800',
            'excess' => 'bg-blue-100 text-blue-800',
            'damaged' => 'bg-red-100 text-red-800',
            'partial' => 'bg-yellow-100 text-yellow-800',
            'missing' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
