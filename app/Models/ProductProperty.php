<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductProperty extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'property_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'property_name',
        'property_value',
        'quantity',
        'sku',
        'price_adjustment',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price_adjustment' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the property.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the final price for this product property.
     */
    public function getFinalPriceAttribute(): float
    {
        return $this->product->price + $this->price_adjustment;
    }

    /**
     * Check if this property has sufficient stock.
     */
    public function isInStock(int $quantity = 1): bool
    {
        return $this->quantity >= $quantity;
    }

    /**
     * Decrease stock quantity.
     */
    public function decreaseStock(int $quantity): bool
    {
        if (!$this->isInStock($quantity)) {
            return false;
        }

        $this->quantity -= $quantity;
        return $this->save();
    }

    /**
     * Increase stock quantity.
     */
    public function increaseStock(int $quantity): bool
    {
        $this->quantity += $quantity;
        return $this->save();
    }

    /**
     * Get the display name for this property.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->property_name}: {$this->property_value}";
    }
}
