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
        'sku',
        'barcode',
        'property_name',
        'property_value',
        'product_brand',
        'product_category',
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
     * Check if the image is a URL (external link)
     */
    public function isImageUrl(): bool
    {
        if (empty($this->image)) {
            return false;
        }
        
        return filter_var($this->image, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Get the full image URL for display
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return asset('images/no-image.png'); // Default placeholder
        }
        
        // If it's already a URL, return it as-is
        if ($this->isImageUrl()) {
            return $this->image;
        }
        
        // Otherwise, it's a local file in storage
        return asset('storage/' . $this->image);
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
        // Product Costing casts
        'raw_material_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'overhead_cost' => 'decimal:2',
        'manufacturing_cost' => 'decimal:2',
        'shipping_cost_per_unit' => 'decimal:2',
        'tax_amount_per_unit' => 'decimal:2',
        'handling_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'profit_amount' => 'decimal:2',
        'last_cost_update' => 'datetime',
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
     * Get the inventory records for the product.
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'product_id', 'product_id');
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
     * Check if the product has property information (size, color, etc.).
     */
    public function hasProperties(): bool
    {
        return !empty($this->property_name) && !empty($this->property_value);
    }


    /**
     * Get the category associated with this product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'product_category', 'name');
    }

    /**
     * Get the brand associated with this product.
     * Matches the product_brand string to Brand.name.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'product_brand', 'name');
    }

    /**
     * Scope a query to only include low stock products.
     * Excludes out of stock items (quantity must be > 0).
     */
    public function scopeLowStock(Builder $query, int $threshold = 10): Builder
    {
        // Low stock: quantity > 0 AND quantity <= threshold
        return $query->where('quantity', '>', 0)
                     ->where('quantity', '<=', $threshold);
    }

    /**
     * Scope a query to only include products that need reordering.
     * Uses product-specific reorder levels or falls back to system default.
     */
    public function scopeNeedsReordering(Builder $query): Builder
    {
        $defaultThreshold = lowStockThreshold();
        
        return $query->where(function ($q) use ($defaultThreshold) {
            // Products with configured reorder_level
            $q->whereNotNull('reorder_level')
              ->whereColumn('quantity', '<=', 'reorder_level')
              // Products without configured reorder_level (use default)
              ->orWhere(function ($subQ) use ($defaultThreshold) {
                  $subQ->whereNull('reorder_level')
                       ->where('quantity', '<=', $defaultThreshold);
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
     * Get the actual total quantity.
     * Returns the quantity field from the products table.
     */
    public function getActualQuantityAttribute(): int
    {
        return (int) $this->quantity;
    }

    /**
     * Get current stock quantity from the latest stock movement.
     * This is the primary way to get product quantity now.
     * 
     * NOTE: This accessor has been commented out because the products table now has
     * a quantity column that is kept in sync with stock movements. The accessor was
     * causing conflicts with increment/decrement operations in PurchaseReceiveService.
     * 
     * If you need to get quantity from stock movements directly, use:
     * $product->getQuantityFromStockMovements()
     */
    public function getQuantityFromStockMovements(): int
    {
        // Get the latest stock movement for this product
        $latestMovement = $this->stockMovements()
            ->orderBy('movement_id', 'desc')
            ->first();
        
        return $latestMovement ? $latestMovement->quantity_after : 0;
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
        $availableQuantity = $this->hasProperties() ? $this->actual_quantity : $this->getAvailableQuantity();
        return $availableQuantity >= $requestedQuantity;
    }

    /**
     * Check if the product is low in stock using configured thresholds.
     * Returns true if available stock is above critical but at or below reorder level.
     */
    public function isLowStock(int $threshold = null): bool
    {
        $available = $this->getAvailableQuantity();
        $reorderLevel = $threshold ?? $this->reorder_level ?? lowStockThreshold();
        $criticalLevel = $this->critical_level ?? criticalStockLevel();
        
        // Low stock: available > criticalLevel AND available <= reorderLevel
        return $available > $criticalLevel && $available <= $reorderLevel;
    }

    /**
     * Check if the product is at critical stock level using configured thresholds.
     * Returns true if available stock is above 0 but at or below critical level.
     */
    public function isCriticalStock(): bool
    {
        $available = $this->getAvailableQuantity();
        $criticalLevel = $this->critical_level ?? criticalStockLevel();
        
        // Critical: available > 0 AND available <= criticalLevel
        return $available > 0 && $available <= $criticalLevel;
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
     * Check if product needs reordering using industry-standard formula.
     * Returns true if available stock is at or below reorder level.
     */
    public function needsReordering(): bool
    {
        $available = $this->getAvailableQuantity();
        $reorderLevel = $this->reorder_level ?? lowStockThreshold();
        return $available <= $reorderLevel;
    }

    /**
     * Check if auto-reorder is enabled for this product.
     * Uses product-level setting OR global setting.
     */
    public function shouldAutoReorder(): bool
    {
        // If explicitly set at product level, use that
        if ($this->auto_reorder_enabled !== null) {
            return (bool) $this->auto_reorder_enabled;
        }
        
        // Otherwise, use global setting
        return isAutoReorderEnabled();
    }

    /**
     * Check if negative stock is allowed for this product.
     * Uses global setting.
     */
    public function allowsNegativeStock(): bool
    {
        return isNegativeStockAllowed();
    }

    /**
     * Get available quantity (total stock - reserved).
     */
    public function getAvailableQuantity(): int
    {
        $reserved = $this->inventories()->sum('quantity_reserved');
        return max(0, $this->quantity - $reserved);
    }

        /**
     * Get stock status with threshold information.
     * 
     * Formula:
     * - Use product's configured thresholds (reorder_level, critical_level)
     * - Fall back to system defaults if not set
     * - Out of Stock: available <= 0
     * - Critical: available <= critical_level
     * - Low: available <= reorder_level (but above critical)
     * - In Stock: available > reorder_level
     */
    public function getStockStatus(): array
    {
        $available = $this->getAvailableQuantity();
        $reorderLevel = $this->reorder_level ?? lowStockThreshold();
        $criticalLevel = $this->critical_level ?? criticalStockLevel();
        $lowLevel = $reorderLevel;
        
        // Determine status based on configured thresholds
        if ($available <= 0) {
            $status = 'Out of Stock';
            $statusClass = 'red';
        } elseif ($available <= $criticalLevel) {
            $status = 'Critical';
            $statusClass = 'red';
        } elseif ($available <= $lowLevel) {
            $status = 'Low';
            $statusClass = 'yellow';
        } else {
            $status = 'In Stock';
            $statusClass = 'green';
        }
        
        // Calculate percentage based on reorder level
        $percentage = $lowLevel > 0 ? min(100, ($available / $lowLevel) * 100) : 0;
        
        return [
            'available' => $available,
            'total_stock' => $this->quantity,
            'reserved' => $this->quantity - $available,
            'reorder_level' => $reorderLevel,
            'critical_level' => $criticalLevel,
            'low_level' => $lowLevel,
            'status' => $status,
            'status_class' => $statusClass,
            'percentage' => round($percentage, 2)
        ];
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
     * Get the product SKU (accessor for compatibility).
     * Returns the SKU from inventory table if available, otherwise from product SKU column.
     */
    public function getSkuAttribute($value): string
    {
        // First, try to get SKU from the first inventory record
        $inventory = $this->inventories()->first();
        if ($inventory && !empty($inventory->sku)) {
            return $inventory->sku;
        }
        
        // If product SKU column has a value, return it
        if (!empty($value)) {
            return $value;
        }
        
        // Fallback: generate SKU from brand and product name
        $brandCode = strtoupper(substr($this->attributes['product_brand'] ?? 'XX', 0, 3));
        $productCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->attributes['product_name'] ?? ''), 0, 3));
        $uniqueId = str_pad($this->product_id, 3, '0', STR_PAD_LEFT);
        return "{$brandCode}-{$productCode}{$uniqueId}";
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
