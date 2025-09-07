<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'product_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_name',
        'product_brand',
        'quantity',
        'price',
        'image',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sales for the product.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'product_id', 'product_id');
    }

    /**
     * Get the purchases for the product.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'product_id', 'product_id');
    }

    /**
     * Get the returns for the product.
     */
    public function returns(): HasMany
    {
        return $this->hasMany(Returns::class, 'product_id', 'product_id');
    }

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock(Builder $query, int $threshold = 10): Builder
    {
        return $query->where('quantity', '<=', $threshold);
    }

    /**
     * Scope a query to search products by name or brand.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('product_name', 'LIKE', "%{$search}%")
              ->orWhere('product_brand', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Check if the product is in stock.
     */
    public function isInStock(int $requestedQuantity = 1): bool
    {
        return $this->quantity >= $requestedQuantity;
    }

    /**
     * Check if the product is low in stock.
     */
    public function isLowStock(int $threshold = 10): bool
    {
        return $this->quantity <= $threshold;
    }

    /**
     * Get the total quantity sold.
     */
    public function getTotalSoldAttribute(): int
    {
        return $this->sales()->sum('quantity');
    }

    /**
     * Get the total quantity purchased.
     */
    public function getTotalPurchasedAttribute(): int
    {
        return $this->purchases()->sum('quantity');
    }

    /**
     * Get the total quantity returned.
     */
    public function getTotalReturnedAttribute(): int
    {
        return $this->returns()->sum('quantity') ?? 0;
    }

    /**
     * Update stock quantity after a sale.
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
     * Update stock quantity after a purchase or return.
     */
    public function increaseStock(int $quantity): bool
    {
        $this->quantity += $quantity;
        return $this->save();
    }

    /**
     * Get the product's full name (name + brand).
     */
    public function getFullNameAttribute(): string
    {
        return $this->product_name . ' - ' . $this->product_brand;
    }

    /**
     * Get the product name (accessor for compatibility).
     */
    public function getNameAttribute(): string
    {
        return $this->product_name;
    }

    /**
     * Get the product SKU (accessor for compatibility - uses brand as SKU).
     */
    public function getSkuAttribute(): string
    {
        return $this->product_brand ?? 'SKU-' . $this->product_id;
    }

    /**
     * Calculate total revenue from sales.
     */
    public function getTotalRevenueAttribute(): float
    {
        return $this->sales()->sum('quantity') * $this->price;
    }

    /**
     * Get products that need reordering (low stock).
     */
    public static function needsReordering(int $threshold = 10)
    {
        return self::lowStock($threshold)->get();
    }

    /**
     * Get out of stock products.
     */
    public static function outOfStock()
    {
        return self::where('quantity', '<=', 0)->get();
    }

    /**
     * Scope a query to only include products with stock.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Scope a query to only include out of stock products.
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('quantity', '<=', 0);
    }

    /**
     * Get inventory value for this product.
     */
    public function getInventoryValueAttribute(): float
    {
        return $this->quantity * $this->price;
    }

    /**
     * Calculate total inventory value for all products.
     */
    public static function totalInventoryValue(): float
    {
        return self::selectRaw('SUM(quantity * price) as total')->value('total') ?? 0;
    }

    /**
     * Get profit margin based on average purchase price.
     */
    public function getProfitMarginAttribute(): float
    {
        $avgPurchasePrice = $this->purchases()->avg('price');
        
        if (!$avgPurchasePrice) {
            return 0;
        }

        return (($this->price - $avgPurchasePrice) / $this->price) * 100;
    }

    /**
     * Get stock turnover rate (sales / average stock).
     */
    public function getStockTurnoverAttribute(): float
    {
        $totalSold = $this->total_sold;
        $averageStock = ($this->total_purchased + $this->quantity) / 2;
        
        if ($averageStock <= 0) {
            return 0;
        }
        
        return $totalSold / $averageStock;
    }

    /**
     * Update product price.
     */
    public function updatePrice(float $newPrice): bool
    {
        $this->price = $newPrice;
        return $this->save();
    }

    /**
     * Bulk update stock for multiple products.
     */
    public static function bulkUpdateStock(array $stockUpdates): array
    {
        $results = [];
        
        foreach ($stockUpdates as $productId => $quantity) {
            $product = self::find($productId);
            if ($product) {
                $product->quantity = $quantity;
                $results[$productId] = $product->save();
            } else {
                $results[$productId] = false;
            }
        }
        
        return $results;
    }

    /**
     * Get products by brand.
     */
    public static function getByBrand(string $brand)
    {
        return self::where('product_brand', 'LIKE', "%{$brand}%")->get();
    }

    /**
     * Get top revenue generating products.
     */
    public static function topRevenueProducts(int $limit = 10)
    {
        return self::with(['sales'])
                   ->get()
                   ->sortByDesc('total_revenue')
                   ->take($limit);
    }

    /**
     * Get products with negative stock (oversold).
     */
    public static function oversoldProducts()
    {
        return self::where('quantity', '<', 0)->get();
    }

    /**
     * Set stock to zero (mark as out of stock).
     */
    public function markAsOutOfStock(): bool
    {
        $this->quantity = 0;
        return $this->save();
    }

    /**
     * Get stock movement history (sales + purchases + returns).
     */
    public function getStockMovementAttribute(): array
    {
        $movements = [];
        
        // Sales (outgoing)
        foreach ($this->sales as $sale) {
            $movements[] = [
                'type' => 'sale',
                'quantity' => -$sale->quantity,
                'date' => $sale->date,
                'id' => $sale->sales_id
            ];
        }
        
        // Purchases (incoming)
        foreach ($this->purchases as $purchase) {
            $movements[] = [
                'type' => 'purchase',
                'quantity' => $purchase->quantity,
                'date' => $purchase->purchase_date,
                'id' => $purchase->purchase_id
            ];
        }
        
        // Returns (incoming)
        foreach ($this->returns()->approved()->get() as $return) {
            $movements[] = [
                'type' => 'return',
                'quantity' => $return->quantity,
                'date' => $return->return_date,
                'id' => $return->return_id
            ];
        }
        
        // Sort by date
        usort($movements, function($a, $b) {
            return $a['date'] <=> $b['date'];
        });
        
        return $movements;
    }
}
