<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

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
        'stock_name',
        'stock_name_id',
        'sku',
        'barcode',
        'size',
        'color',
        'property_name',
        'property_value',
        'product_brand',
        'product_category',
        'preferred_supplier_id',
        'price',
        'pricing_method',
        'markup_price_id',
        'image',
        'description',
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
            'stock_name',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * Mutator to prevent negative quantity values
     */
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = max(0, (int) $value);
    }

    /**
     * Mutator to prevent negative price values
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = max(0, (float) $value);
    }

    /**
     * Get the sales for the product.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'product_id', 'product_id');
    }

    /**
     * Get the sales order items for the product.
     */
    public function salesItems(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'product_id', 'product_id');
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
     * Get the stock name (parent product) associated with this product.
     */
    public function stockName()
    {
        return $this->belongsTo(StockName::class, 'stock_name_id');
    }

    /**
     * Get the markup price configuration associated with this product.
     */
    public function markupPrice()
    {
        return $this->belongsTo(MarkupPrice::class, 'markup_price_id');
    }

    /**
     * Calculate the selling price based on the pricing method.
     * 
     * @return float|null
     */
    public function calculateSellingPrice()
    {
        switch ($this->pricing_method) {
            case 'manual':
                // Use the manually set price
                return $this->price;
                
            case 'markup':
                // Calculate from markup price if set
                if ($this->markupPrice && $this->total_cost) {
                    return $this->markupPrice->calculatePrice($this->total_cost);
                }
                return $this->price; // Fallback to manual price
                
            default:
                return $this->price;
        }
    }


    /**
     * Scope a query to only include products that need reordering.
     * 
     * Uses calculated reorder point based on:
     * ROP = (Average Daily Demand × Lead Time) + Safety Stock
     * 
     * For performance, checks against reorder_level field if set,
     * otherwise uses default threshold of 10.
     */
    public function scopeNeedsReordering(Builder $query): Builder
    {
        // For query efficiency, use reorder_level field if set, otherwise default to 10
        return $query->where(function ($q) {
            $q->whereRaw('quantity <= COALESCE(reorder_level, 10)')
              ->orWhereNull('reorder_level')->where('quantity', '<=', 10);
        });
    }

    /**
     * Scope a query to search products by name, SKU, barcode, brand, category, and other fields.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('product_name', 'LIKE', "%{$search}%")
              ->orWhere('sku', 'LIKE', "%{$search}%")
              ->orWhere('barcode', 'LIKE', "%{$search}%")
              ->orWhere('stock_name', 'LIKE', "%{$search}%")
              ->orWhere('product_brand', 'LIKE', "%{$search}%")
              ->orWhere('product_category', 'LIKE', "%{$search}%")
              ->orWhere('size', 'LIKE', "%{$search}%")
              ->orWhere('color', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
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
     * .
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
     * Check if the product is low in stock.
     * Standard formula: quantity > 5 AND quantity <= 10
     */
    public function isLowStock(int $threshold): bool
    {
        $available = $this->getAvailableQuantity();
        $lowThreshold = $threshold ?? 10; // Standard low stock threshold
        
        return $available > 0 && $available <= $lowThreshold;
    }

    /**
     * Check if the product is at critical stock level.
     * 
     * Standard Formula: 
     * Critical Level = Average Daily Demand × Lead Time Days
     * If no sales data: uses critical_level field or defaults to 5
     * 
     * Critical condition: current stock <= critical level
     */
    public function isCriticalStock(): bool
    {
        $available = $this->getAvailableQuantity();
        $criticalLevel = $this->calculateCriticalLevel();
        
        return $available > 0 && $available <= $criticalLevel;
    }

    /**
     * Calculate critical stock level based on demand and lead time.
     * 
     * Formula: Average Daily Demand × Lead Time Days
     * 
     * @return int
     */
    public function calculateCriticalLevel(): int
    {
        // Use predefined critical level if set
        if ($this->critical_level && $this->critical_level > 0) {
            return $this->critical_level;
        }

        $averageDailyDemand = $this->getAverageDailyDemand();
        $leadTimeDays = $this->lead_time_days ?? 7; // Default 7 days if not set

        // If no sales data, use default critical level
        if ($averageDailyDemand <= 0) {
            return 5; // Default critical level
        }

        return (int) ceil($averageDailyDemand * $leadTimeDays);
    }

    /**
     * Check if product needs reordering based on Reorder Point (ROP).
     * 
     * Standard Formula:
     * ROP = (Average Daily Demand × Lead Time Days) + Safety Stock
     * Safety Stock = (Max Daily Demand - Avg Daily Demand) × Lead Time
     * 
     * @return bool
     */
    public function needsReordering(): bool
    {
        $available = $this->getAvailableQuantity();
        $reorderPoint = $this->calculateReorderPoint();
        
        return $available <= $reorderPoint;
    }

    /**
     * Calculate Reorder Point (ROP) using standard formula.
     * 
     * Formula: ROP = (Average Daily Demand × Lead Time) + Safety Stock
     * 
     * @return int
     */
    public function calculateReorderPoint(): int
    {
        // Use predefined reorder level if set
        if ($this->reorder_level && $this->reorder_level > 0) {
            return $this->reorder_level;
        }

        $averageDailyDemand = $this->getAverageDailyDemand();
        $leadTimeDays = $this->lead_time_days ?? 7;
        $safetyStock = $this->calculateSafetyStock();

        // If no sales data, use default reorder level
        if ($averageDailyDemand <= 0) {
            return 10; // Default reorder level
        }

        return (int) ceil(($averageDailyDemand * $leadTimeDays) + $safetyStock);
    }

    /**
     * Calculate Safety Stock using standard formula.
     * 
     * Formula: Safety Stock = (Max Daily Demand - Avg Daily Demand) × Lead Time
     * 
     * @return float
     */
    public function calculateSafetyStock(): float
    {
        $averageDailyDemand = $this->getAverageDailyDemand();
        $maxDailyDemand = $this->getMaxDailyDemand();
        $leadTimeDays = $this->lead_time_days ?? 7;

        if ($averageDailyDemand <= 0) {
            return 0;
        }

        return ($maxDailyDemand - $averageDailyDemand) * $leadTimeDays;
    }

    /**
     * Get average daily demand from sales data.
     * 
     * @param int $days Number of days to analyze (default 30)
     * @return float
     */
    public function getAverageDailyDemand(int $days = 30): float
    {
        // Use movement_velocity if available and recent
        if ($this->movement_velocity && $this->last_movement_check) {
            $hoursSinceCheck = now()->diffInHours($this->last_movement_check);
            if ($hoursSinceCheck < 24) {
                return (float) $this->movement_velocity;
            }
        }

        // Calculate from sales data
        $startDate = now()->subDays($days);
        $totalSold = $this->salesItems()
            ->whereHas('order', function ($query) use ($startDate) {
                $query->where('order_date', '>=', $startDate)
                      ->whereIn('status', ['confirmed', 'processing', 'ready', 'shipped', 'delivered']);
            })
            ->sum('quantity');

        return $totalSold > 0 ? $totalSold / $days : 0;
    }

    /**
     * Get maximum daily demand from sales data.
     * 
     * @param int $days Number of days to analyze (default 30)
     * @return float
     */
    public function getMaxDailyDemand(int $days = 30): float
    {
        $startDate = now()->subDays($days);
        
        $maxDaily = $this->salesItems()
            ->join('sales_orders', 'sales_order_items.order_id', '=', 'sales_orders.order_id')
            ->where('sales_orders.order_date', '>=', $startDate)
            ->whereIn('sales_orders.status', ['confirmed', 'processing', 'ready', 'shipped', 'delivered'])
            ->selectRaw('DATE(sales_orders.order_date) as sale_date, SUM(sales_order_items.quantity) as daily_total')
            ->groupBy('sale_date')
            ->orderByDesc('daily_total')
            ->first();

        if ($maxDaily) {
            return (float) $maxDaily->daily_total;
        }

        // If no data, use 150% of average as estimate
        $avgDemand = $this->getAverageDailyDemand($days);
        return $avgDemand * 1.5;
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
     * Get stock status with dynamic formula-based thresholds.
     * 
     * Standard Formulas:
     * - Critical Level = Average Daily Demand × Lead Time
     * - Reorder Point = (Average Daily Demand × Lead Time) + Safety Stock
     * - Out of Stock: quantity <= 0
     * - Critical: quantity > 0 AND quantity <= critical level
     * - Low: quantity > critical level AND quantity <= reorder point
     * - In Stock: quantity > reorder point
     */
    public function getStockStatus(): array
    {
        $available = $this->getAvailableQuantity();
        $criticalLevel = $this->calculateCriticalLevel();
        $reorderPoint = $this->calculateReorderPoint();
        $safetyStock = $this->calculateSafetyStock();
        
        // Determine status based on calculated thresholds
        if ($available <= 0) {
            $status = 'Out of Stock';
            $statusClass = 'red';
            $percentage = 0;
        } elseif ($available <= $criticalLevel) {
            $status = 'Critical';
            $statusClass = 'red';
            $percentage = ($available / max($criticalLevel, 1)) * 100;
        } elseif ($available <= $reorderPoint) {
            $status = 'Low';
            $statusClass = 'yellow';
            $percentage = ($available / max($reorderPoint, 1)) * 100;
        } else {
            $status = 'In Stock';
            $statusClass = 'green';
            $percentage = 100;
        }
        
        return [
            'available' => $available,
            'total_stock' => $this->quantity,
            'reserved' => $this->quantity - $available,
            'status' => $status,
            'status_class' => $statusClass,
            'percentage' => round($percentage, 2),
            'critical_level' => $criticalLevel,
            'reorder_point' => $reorderPoint,
            'safety_stock' => round($safetyStock, 2),
            'average_daily_demand' => round($this->getAverageDailyDemand(), 2),
        ];
    }

    /**
     * Calculate suggested order quantity.
     * Standard formula: bring stock to 20 units (double the low stock threshold)
     */
    public function getSuggestedOrderQuantity(): int
    {
        $targetLevel = 20; // Standard target stock level
        $currentQuantity = $this->quantity;
        
        return max(0, $targetLevel - $currentQuantity);
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
            $q->where('quantity_after', '==', 0)
              ->whereIn('movement_id', function($subQ) {
                  $subQ->selectRaw('MAX(movement_id)')
                       ->from('stock_movements')
                       ->groupBy('product_id');
              });
        })->get();
    }

    /**
     * Scope a query to only include products with stock (quantity > 5).
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('quantity', '>', 10);
    }

    public function allStock(Builder $query): Builder
    {
        return $query->where('quantity', '');
    }

    /**
     * Scope a query to only include out of stock products (quantity <= 0).
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('quantity', '<=', 0);
    }

        /**
     * Scope a query to only include low stock products.
     * Low stock: quantity > 5
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('quantity', '>', 5)
                        ->where('quantity', '<=', 10);
    }

    /**
     * Scope a query to only include critical stock products.
     * Critical stock: quantity <= 5 AND quantity > 0
     */
    public function scopeCriticalStock(Builder $query): Builder
    {
        return $query->where('quantity', '<=', 5)
                     ->where('quantity', '>', 0);
    }

    /**
     * Scope a query to filter by stock name.
     */
    public function scopeByStockName(Builder $query, string $stockName): Builder
    {
        return $query->where('stock_name', $stockName);
    }

    /**
     * Get all product variants with the same stock name.
     */
    public function getVariantsAttribute()
    {
        if (!$this->stock_name) {
            return collect([]);
        }
        
        return static::where('stock_name', $this->stock_name)
            ->where('product_id', '!=', $this->product_id)
            ->get();
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
                $oldQuantity = $product->quantity;
                $product->quantity = $quantity;
                $results[$productId] = $product->save();
                
                // Log stock change to audit trail
                if ($results[$productId]) {
                    $difference = $quantity - $oldQuantity;
                    $action = $difference >= 0 ? 'stock increase' : 'stock decrease';
                    
                    \App\Models\AuditLog::logAction(
                        $action,
                        \App\Models\AuditLog::MODULE_INVENTORY,
                        "Bulk update: {$product->product_name} stock changed from {$oldQuantity} to {$quantity}",
                        'Product',
                        $productId,
                        $product->product_name,
                        ['quantity' => $oldQuantity],
                        ['quantity' => $quantity],
                        \App\Models\AuditLog::SEVERITY_INFO
                    );
                }
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
        $oldQuantity = $this->quantity;
        $this->quantity = 0;
        $result = $this->save();
        
        // Log to audit trail if stock was changed
        if ($result && $oldQuantity != 0) {
            \App\Models\AuditLog::logAction(
                'stock_decrease',
                \App\Models\AuditLog::MODULE_INVENTORY,
                "{$this->product_name} marked as out of stock",
                'Product',
                $this->product_id,
                $this->product_name,
                ['quantity' => $oldQuantity],
                ['quantity' => 0],
                \App\Models\AuditLog::SEVERITY_INFO
            );
        }
        
        return $result;
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



    /**
     * Get the effective selling price based on price source
     * 
     * @return float|null
     */


}
