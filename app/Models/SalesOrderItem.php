<?php

namespace App\Models;

use App\Traits\ValidatesForeignKeys;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class SalesOrderItem extends Model
{
    use HasFactory, ValidatesForeignKeys;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'item_id';

    /**
     * Foreign key validation configuration.
     * Replaces database-level foreign key constraints.
     *
     * @var array
     */
    protected array $foreignKeys = [
        'order_id' => ['table' => 'sales_orders', 'column' => 'order_id'],
        'product_id' => ['table' => 'products', 'column' => 'product_id'],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
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
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sales order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'order_id', 'order_id');
    }

    /**
     * Get the product that this item references.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Boot method to automatically calculate line total.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            // Auto-calculate line total if not explicitly set
            if ($item->isDirty(['quantity', 'unit_price', 'discount_amount'])) {
                $item->line_total = ($item->quantity * $item->unit_price) - $item->discount_amount;
            }
        });
        
        static::saved(function ($item) {
            // Recalculate order totals when item changes
            // For POS orders, don't apply auto tax/discount
            if ($item->order) {
                $applyAuto = $item->order->purchase_type !== 'in_store';
                $item->order->calculateTotals($applyAuto);
            }
        });
        
        static::deleted(function ($item) {
            // Recalculate order totals when item is deleted
            // For POS orders, don't apply auto tax/discount
            if ($item->order) {
                $applyAuto = $item->order->purchase_type !== 'in_store';
                $item->order->calculateTotals($applyAuto);
            }
        });
    }

    /**
     * Calculate the line total manually.
     */
    public function calculateLineTotal(): float
    {
        return ($this->quantity * $this->unit_price) - $this->discount_amount;
    }

    /**
     * Get the discount percentage for this line item.
     */
    public function getDiscountPercentageAttribute(): float
    {
        if ($this->unit_price == 0 || $this->quantity == 0) {
            return 0;
        }
        
        $subtotal = $this->quantity * $this->unit_price;
        return ($this->discount_amount / $subtotal) * 100;
    }

    /**
     * Get the total amount before discount.
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Scope a query to only include items for a specific order.
     */
    public function scopeForOrder(Builder $query, $orderId): Builder
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope a query to only include items for a specific product.
     */
    public function scopeForProduct(Builder $query, $productId): Builder
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Check if there's sufficient stock for this item.
     */
    public function hasSufficientStock(): bool
    {
        if (!$this->product) {
            return false;
        }
        
        return $this->product->quantity >= $this->quantity;
    }

    /**
     * Get the stock shortage amount if any.
     */
    public function getStockShortageAttribute(): int
    {
        if (!$this->product) {
            return $this->quantity;
        }
        
        $shortage = $this->quantity - $this->product->quantity;
        return max(0, $shortage);
    }

    /**
     * Check if this item has a stock shortage.
     */
    public function hasStockShortage(): bool
    {
        return $this->stock_shortage > 0;
    }
}
