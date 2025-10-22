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
        'product_category',
        'price',
        'image',
        'description',
        'supplier_name',
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
     * Movement tracking fields (used by product movement service)
     */
    protected $movementTrackingFillable = [
        'movement_category',
        'total_sales_quantity',
        'movement_velocity',
        'days_since_last_sale',
        'last_sale_date',
        'movement_analysis_start_date',
        'movement_analysis_end_date',
        'last_movement_check',
        'is_promotional',
        'promotional_reason'
    ];

    /**
     * Costing fields (used by product costing service)
     */
    protected $costingFillable = [
        'raw_material_cost',
        'labor_cost',
        'overhead_cost',
        'manufacturing_cost',
        'shipping_cost_per_unit',
        'tax_amount_per_unit',
        'handling_cost',
        'total_cost',
        'profit_margin',
        'profit_amount',
        'cost_calculation_method',
        'last_cost_update',
        'cost_notes'
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
     * Temporarily add movement tracking fields to fillable
     */
    public function enableMovementTrackingFields()
    {
        $this->fillable = array_merge($this->fillable, $this->movementTrackingFillable);
        return $this;
    }

    /**
     * Temporarily add costing fields to fillable
     */
    public function enableCostingFields()
    {
        $this->fillable = array_merge($this->fillable, $this->costingFillable);
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
     * Get the product properties (variants) for the product.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(ProductProperty::class, 'product_id', 'product_id');
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
     * Get the stock movements for the product.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'product_id');
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
     * Get the category associated with this product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'product_category', 'name');
    }

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock(Builder $query, int $threshold = 10): Builder
    {
        // This will need to be handled differently - using subquery to get latest stock from movements
        return $query->whereHas('stockMovements', function($q) use ($threshold) {
            $q->whereRaw('quantity_after <= ?', [$threshold])
              ->whereIn('movement_id', function($subQ) {
                  $subQ->selectRaw('MAX(movement_id)')
                       ->from('stock_movements')
                       ->groupBy('product_id');
              });
        });
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
     * Get the actual total quantity from all product properties.
     * The quantity field on products table is now just a reference.
     */
    public function getActualQuantityAttribute(): int
    {
        return $this->properties()->where('is_active', true)->sum('quantity');
    }

    /**
     * Get current stock quantity from the latest stock movement.
     * This is the primary way to get product quantity now.
     */
    public function getQuantityAttribute(): int
    {
        // Get the latest stock movement for this product
        $latestMovement = $this->stockMovements()
            ->orderBy('movement_id', 'desc')
            ->first();
        
        return $latestMovement ? $latestMovement->quantity_after : 0;
    }

    /**
     * Check if the product has any properties (variants).
     */
    public function hasProperties(): bool
    {
        return $this->properties()->exists();
    }

    /**
     * Get quantity for display - uses actual_quantity if properties exist, otherwise uses reference quantity.
     */
    public function getDisplayQuantityAttribute(): int
    {
        return $this->hasProperties() ? $this->actual_quantity : $this->quantity;
    }

    /**
     * Check if the product is in stock.
     */
    public function isInStock(int $requestedQuantity = 1): bool
    {
        $availableQuantity = $this->hasProperties() ? $this->actual_quantity : $this->quantity;
        return $availableQuantity >= $requestedQuantity;
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
     * Now uses StockMovement to track changes instead of direct quantity field.
     */
    public function decreaseStock(int $quantity): bool
    {
        if (!$this->isInStock($quantity)) {
            return false;
        }

        $currentQuantity = $this->quantity;
        
        // Create a stock movement record instead of directly modifying quantity
        StockMovement::recordMovement(
            productId: $this->product_id,
            quantityBefore: $currentQuantity,
            quantityChange: -$quantity,
            quantityAfter: $currentQuantity - $quantity,
            movementType: StockMovement::TYPE_SALE,
            userId: auth()->id()
        );
        
        return true;
    }

    /**
     * Update stock quantity after a purchase or return.
     * Now uses StockMovement to track changes instead of direct quantity field.
     */
    public function increaseStock(int $quantity): bool
    {
        $currentQuantity = $this->quantity;
        
        // Create a stock movement record instead of directly modifying quantity
        StockMovement::recordMovement(
            productId: $this->product_id,
            quantityBefore: $currentQuantity,
            quantityChange: $quantity,
            quantityAfter: $currentQuantity + $quantity,
            movementType: StockMovement::TYPE_PURCHASE,
            userId: auth()->id()
        );
        
        return true;
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
        // Get products where the latest stock movement shows 0 or negative quantity
        return self::whereHas('stockMovements', function($q) {
            $q->where('quantity_after', '<=', 0)
              ->whereIn('movement_id', function($subQ) {
                  $subQ->selectRaw('MAX(movement_id)')
                       ->from('stock_movements')
                       ->groupBy('product_id');
              });
        })->get();
    }

    /**
     * Scope a query to only include products with stock.
     */
    public function scopeInStock(Builder $query): Builder
    {
        // Products where the latest stock movement shows positive quantity
        return $query->whereHas('stockMovements', function($q) {
            $q->where('quantity_after', '>', 0)
              ->whereIn('movement_id', function($subQ) {
                  $subQ->selectRaw('MAX(movement_id)')
                       ->from('stock_movements')
                       ->groupBy('product_id');
              });
        });
    }

    /**
     * Scope a query to only include out of stock products.
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        // Products where the latest stock movement shows 0 or negative quantity
        return $query->whereHas('stockMovements', function($q) {
            $q->where('quantity_after', '<=', 0)
              ->whereIn('movement_id', function($subQ) {
                  $subQ->selectRaw('MAX(movement_id)')
                       ->from('stock_movements')
                       ->groupBy('product_id');
              });
        });
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
        // Calculate from latest stock movements
        $total = 0;
        foreach (self::all() as $product) {
            $total += ($product->quantity * $product->price);
        }
        return $total;
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
