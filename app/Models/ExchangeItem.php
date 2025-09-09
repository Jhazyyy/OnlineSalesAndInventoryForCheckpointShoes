<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExchangeItem extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'exchange_item_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exchange_id',
        'item_type',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'condition',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exchange_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Item type constants.
     */
    const TYPE_ORIGINAL = 'original';
    const TYPE_NEW = 'new';

    /**
     * Get the exchange that owns the item.
     */
    public function exchange(): BelongsTo
    {
        return $this->belongsTo(Exchange::class, 'exchange_id', 'exchange_id');
    }

    /**
     * Get the product associated with the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Check if this is an original item (being returned).
     */
    public function isOriginal(): bool
    {
        return $this->item_type === self::TYPE_ORIGINAL;
    }

    /**
     * Check if this is a new item (being given).
     */
    public function isNew(): bool
    {
        return $this->item_type === self::TYPE_NEW;
    }
}
