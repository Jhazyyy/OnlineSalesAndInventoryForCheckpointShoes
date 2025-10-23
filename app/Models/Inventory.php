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
        'property_id',
        'sku',
        'location',
        'quantity_on_hand',
        'quantity_reserved',
        'unit_cost',
        'last_movement_at',
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

    public function property(): BelongsTo
    {
        return $this->belongsTo(ProductProperty::class, 'property_id', 'property_id');
    }

    public function getQuantityAvailableAttribute(): int
    {
        return max(0, (int) $this->quantity_on_hand - (int) $this->quantity_reserved);
    }
}
