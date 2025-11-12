<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $primaryKey = 'inventory_id';

    protected $fillable = [
        'product_id',
        'variant_id',
        'property_id',
        'sku',
        'location',
        'quantity_on_hand',
        'quantity_reserved',
        'unit_cost',
        'last_movement_at',
    ];

    /**
     * Foreign key attributes for validation.
     *
     * @var array<string, array>
     */
    protected $foreignKeys = [
        'product_id' => ['table' => 'products', 'column' => 'product_id'],
        'variant_id' => ['table' => 'product_variants', 'column' => 'variant_id', 'nullable' => true],
    ];

    protected $casts = [
        'quantity_on_hand' => 'integer',
        'quantity_reserved' => 'integer',
        'unit_cost' => 'decimal:2',
        'last_movement_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the variant that belongs to the inventory.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
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
        return $this->sku ?? $this->product->sku ?? null;
    }

    /**
     * Accessor for quantity (alias for quantity_on_hand for backward compatibility)
     */
    public function getQuantityAttribute(): int
    {
        return (int) $this->quantity_on_hand;
    }

    public function getQuantityAvailableAttribute(): int
    {
        return max(0, (int) $this->quantity_on_hand - (int) $this->quantity_reserved);
    }
}
