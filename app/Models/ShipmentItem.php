<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ShipmentItem extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'shipment_item_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shipment_id',
        'sales_order_item_id',
        'product_id',
        'product_sku',
        'product_name',
        'quantity_shipped',
        'unit_price',
        'line_total',
        'package_number',
        'item_weight',
        'item_dimensions',
        'condition',
        'status',
        'serial_numbers',
        'notes',
        'quality_checked',
        'checked_by',
        'checked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity_shipped' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'item_weight' => 'decimal:2',
        'item_dimensions' => 'array',
        'serial_numbers' => 'array',
        'quality_checked' => 'boolean',
        'checked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the shipment that owns the shipment item.
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id', 'shipment_id');
    }

    /**
     * Get the product that owns the shipment item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the sales order item that this shipment item is based on.
     */
    public function salesOrderItem(): BelongsTo
    {
        return $this->belongsTo(SalesOrderItem::class, 'sales_order_item_id', 'item_id');
    }

    /**
     * Boot method to calculate line total.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($item) {
            if (!$item->line_total) {
                $item->line_total = $item->quantity_shipped * $item->unit_price;
            }
        });
        
        static::updating(function ($item) {
            $item->line_total = $item->quantity_shipped * $item->unit_price;
        });
    }

    // Scopes
    
    /**
     * Scope a query to only include pending items.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include packed items.
     */
    public function scopePacked(Builder $query): Builder
    {
        return $query->where('status', 'packed');
    }

    /**
     * Scope a query to only include shipped items.
     */
    public function scopeShipped(Builder $query): Builder
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope a query to only include delivered items.
     */
    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope a query to only include returned items.
     */
    public function scopeReturned(Builder $query): Builder
    {
        return $query->where('status', 'returned');
    }

    /**
     * Scope a query to only include damaged items.
     */
    public function scopeDamaged(Builder $query): Builder
    {
        return $query->where('status', 'damaged');
    }

    /**
     * Scope a query to only include items in a specific package.
     */
    public function scopeInPackage(Builder $query, string $packageNumber): Builder
    {
        return $query->where('package_number', $packageNumber);
    }

    /**
     * Scope a query to only include quality checked items.
     */
    public function scopeQualityChecked(Builder $query): Builder
    {
        return $query->where('quality_checked', true);
    }

    /**
     * Scope a query to only include items pending quality check.
     */
    public function scopePendingQualityCheck(Builder $query): Builder
    {
        return $query->where('quality_checked', false);
    }

    // Helper Methods

    /**
     * Get the calculated line total.
     */
    public function getCalculatedLineTotalAttribute(): float
    {
        return $this->quantity_shipped * $this->unit_price;
    }

    /**
     * Check if the item has serial numbers.
     */
    public function hasSerialNumbers(): bool
    {
        return !empty($this->serial_numbers) && is_array($this->serial_numbers) && count($this->serial_numbers) > 0;
    }

    /**
     * Check if the item is quality checked.
     */
    public function isQualityChecked(): bool
    {
        return $this->quality_checked;
    }

    

    /**
     * Mark item as quality checked.
     */
    public function markAsQualityChecked(string $checkedBy): void
    {
        $this->update([
            'quality_checked' => true,
            'checked_by' => $checkedBy ?? auth()->user()?->name,
            'checked_at' => now(),
        ]);
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'packed' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
            'returned' => 'bg-orange-100 text-orange-800',
            'damaged' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get condition badge class for UI.
     */
    public function getConditionBadgeClassAttribute(): string
    {
        return match($this->condition) {
            'new' => 'bg-green-100 text-green-800',
            'used' => 'bg-yellow-100 text-yellow-800',
            'refurbished' => 'bg-blue-100 text-blue-800',
            'damaged' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
