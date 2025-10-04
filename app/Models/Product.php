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
     * Threshold-specific fillable fields (used by threshold management)
     */
    protected $thresholdFillable = [
        'reorder_level',
        'critical_level',
        'ceiling_level',
        'floor_level',
        'auto_reorder_enabled',
        'threshold_alerts_enabled',
        'preferred_supplier_id',
        'lead_time_days',
        'economic_order_quantity',
        'last_threshold_check'
    ];

    /**
     * Supplier tracking fields (used by purchase receive management)
     */
    protected $supplierTrackingFillable = [
        'last_supplier_id',
        'last_received_at',
        'last_purchase_price'
    ];

    /**
     * Temporarily add threshold fields to fillable for threshold operations
     */
    public function enableThresholdFields()
    {
        $this->fillable = array_merge($this->fillable, $this->thresholdFillable);
        return $this;
    }

    /**
     * Temporarily add supplier tracking fields to fillable
     */
    public function enableSupplierTrackingFields()
    {
        $this->fillable = array_merge($this->fillable, $this->supplierTrackingFillable);
        return $this;
    }

    /**
     * Reset fillable to basic fields only
     */
    public function resetFillable()
    {
        $this->fillable = [
            'product_name',
            'product_brand',
            'product_category',
            'quantity',
            'price',
            'image',
            'description',
        ];
        return $this;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'reorder_level' => 'integer',
        'critical_level' => 'integer',
        'ceiling_level' => 'integer',
        'floor_level' => 'integer',
        'auto_reorder_enabled' => 'boolean',
        'threshold_alerts_enabled' => 'boolean',
        'lead_time_days' => 'integer',
        'economic_order_quantity' => 'integer',
        'last_threshold_check' => 'datetime',
        'last_received_at' => 'datetime',
        'last_purchase_price' => 'decimal:2',
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
     * Get the purchase returns for the product.
     */
    public function purchaseReturns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class, 'product_id', 'product_id');
    }

    /**
     * Get the inventory alerts for the product.
     */
    public function inventoryAlerts(): HasMany
    {
        return $this->hasMany(InventoryAlert::class, 'product_id', 'product_id');
    }

    /**
     * Get the audit logs for the product.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(InventoryAuditLog::class, 'product_id', 'product_id');
    }

    /**
     * Get the preferred supplier for the product.
     */
    public function preferredSupplier()
    {
        return $this->belongsTo(Supplier::class, 'preferred_supplier_id');
    }

    /**
     * Get the last supplier that provided this product.
     */
    public function lastSupplier()
    {
        return $this->belongsTo(Supplier::class, 'last_supplier_id');
    }

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock(Builder $query, int $threshold = 10): Builder
    {
        return $query->where('quantity', '<=', $threshold);
    }

    /**
     * Scope a query to search products by name, brand, or category.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('product_name', 'LIKE', "%{$search}%")
              ->orWhere('product_brand', 'LIKE', "%{$search}%")
              ->orWhere('product_category', 'LIKE', "%{$search}%");
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
    public function isLowStock(int $threshold = null): bool
    {
        $threshold = $threshold ?? $this->reorder_level ?? lowStockThreshold();
        return $this->quantity <= $threshold;
    }

    /**
     * Check if the product is at critical stock level.
     */
    public function isCriticalStock(): bool
    {
        $criticalLevel = $this->critical_level ?? criticalStockLevel();
        return $this->quantity <= $criticalLevel;
    }

    /**
     * Check if the product is overstocked.
     */
    public function isOverstocked(): bool
    {
        return $this->ceiling_level && $this->quantity > $this->ceiling_level;
    }

    /**
     * Check if the product is below floor level.
     */
    public function isBelowFloor(): bool
    {
        return $this->floor_level && $this->quantity < $this->floor_level;
    }

    /**
     * Check if product needs reordering.
     */
    public function needsReordering(): bool
    {
        return $this->reorder_level && $this->quantity <= $this->reorder_level;
    }

    /**
     * Get stock status with threshold context.
     */
    public function getStockStatus(): array
    {
        $status = [];
        
        if ($this->quantity <= 0) {
            $status[] = ['type' => 'out_of_stock', 'severity' => 'urgent'];
        } elseif ($this->isCriticalStock()) {
            $status[] = ['type' => 'critical_stock', 'severity' => 'critical'];
        } elseif ($this->isLowStock()) {
            $status[] = ['type' => 'low_stock', 'severity' => 'warning'];
        }
        
        if ($this->isOverstocked()) {
            $status[] = ['type' => 'overstock', 'severity' => 'info'];
        }
        
        if ($this->needsReordering()) {
            $status[] = ['type' => 'reorder_needed', 'severity' => 'warning'];
        }
        
        return $status;
    }

    /**
     * Calculate suggested order quantity based on EOQ or default logic.
     */
    public function getSuggestedOrderQuantity(): int
    {
        if ($this->economic_order_quantity) {
            return $this->economic_order_quantity;
        }
        
        // Simple calculation: bring to reorder level + safety stock
        $reorderLevel = $this->reorder_level ?? lowStockThreshold();
        $safetyStock = max(10, $reorderLevel * 0.5); // 50% of reorder level as safety stock
        
        return max(0, ($reorderLevel + $safetyStock) - $this->quantity);
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
     * Get the total quantity returned to suppliers.
     */
    public function getTotalPurchaseReturnedAttribute(): int
    {
        return $this->purchaseReturns()->sum('quantity') ?? 0;
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
     * Get products that need reordering (low stock) - Static method.
     */
    public static function getProductsNeedingReorder(int $threshold = 10)
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
     * Get products by category.
     */
    public static function getByCategory(string $category)
    {
        return self::where('product_category', 'LIKE', "%{$category}%")->get();
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
