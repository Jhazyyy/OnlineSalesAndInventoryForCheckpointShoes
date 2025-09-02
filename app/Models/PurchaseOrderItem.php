<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
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
        'order_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'discount_amount',
        'line_total',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity_ordered' => 'integer',
        'quantity_received' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the purchase order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id', 'order_id');
    }

    /**
     * Get the product that belongs to the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Check if the item is fully received.
     */
    public function isFullyReceived(): bool
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }

    /**
     * Check if the item is partially received.
     */
    public function isPartiallyReceived(): bool
    {
        return $this->quantity_received > 0 && $this->quantity_received < $this->quantity_ordered;
    }

    /**
     * Get the pending quantity (ordered but not received).
     */
    public function getPendingQuantityAttribute(): int
    {
        return max(0, $this->quantity_ordered - $this->quantity_received);
    }

    /**
     * Get the received percentage.
     */
    public function getReceivedPercentageAttribute(): float
    {
        if ($this->quantity_ordered <= 0) {
            return 0;
        }
        
        return round(($this->quantity_received / $this->quantity_ordered) * 100, 1);
    }

    /**
     * Calculate the actual received total amount.
     */
    public function getReceivedTotalAttribute(): float
    {
        return $this->quantity_received * $this->unit_price - 
               ($this->discount_amount * ($this->quantity_received / $this->quantity_ordered));
    }

    /**
     * Boot method to auto-calculate line total.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            $item->line_total = ($item->quantity_ordered * $item->unit_price) - $item->discount_amount;
        });
    }
}
