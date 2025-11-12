<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\ValidatesForeignKeys;

class ProductVariant extends Model
{
    use HasFactory, ValidatesForeignKeys;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'variant_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_product_id',
        'variant_sku',
        'variant_name',
        'color',
        'size',
        'material',
        'additional_attributes',
        'price_adjustment',
        'barcode',
        'image',
        'quantity',
        'reorder_level',
        'critical_level',
        'is_active',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'additional_attributes' => 'array',
        'price_adjustment' => 'decimal:2',
        'is_active' => 'boolean',
        'quantity' => 'integer',
        'reorder_level' => 'integer',
        'critical_level' => 'integer',
    ];

    /**
     * Foreign key validation rules.
     *
     * @var array
     */
    protected $foreignKeys = [
        'parent_product_id' => [
            'table' => 'products',
            'column' => 'product_id',
            'message' => 'The selected parent product does not exist.',
        ],
    ];

    /**
     * Get the parent product that owns this variant.
     */
    public function parentProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_product_id', 'product_id');
    }

    /**
     * Get the sales order items for this variant.
     */
    public function salesOrderItems(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'variant_id', 'variant_id');
    }

    /**
     * Get the stock movements for this variant.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'variant_id', 'variant_id');
    }

    /**
     * Get the full price for this variant (parent price + adjustment).
     */
    public function getFullPriceAttribute(): float
    {
        $parentPrice = $this->parentProduct ? $this->parentProduct->price : 0;
        return $parentPrice + $this->price_adjustment;
    }

    /**
     * Get the display name with variant details.
     */
    public function getDisplayNameAttribute(): string
    {
        $parts = [];
        
        if ($this->color) {
            $parts[] = $this->color;
        }
        
        if ($this->size) {
            $parts[] = "Size {$this->size}";
        }
        
        if ($this->material) {
            $parts[] = $this->material;
        }
        
        return $this->variant_name . ($parts ? ' (' . implode(', ', $parts) . ')' : '');
    }

    /**
     * Check if variant is low on stock.
     */
    public function isLowStock(): bool
    {
        if (!$this->reorder_level) {
            return false;
        }
        
        return $this->quantity <= $this->reorder_level;
    }

    /**
     * Check if variant is at critical stock level.
     */
    public function isCriticalStock(): bool
    {
        if (!$this->critical_level) {
            return false;
        }
        
        return $this->quantity <= $this->critical_level;
    }

    /**
     * Scope to get only active variants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get variants by color.
     */
    public function scopeByColor($query, $color)
    {
        return $query->where('color', $color);
    }

    /**
     * Scope to get variants by size.
     */
    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    /**
     * Scope to get low stock variants.
     */
    public function scopeLowStock($query)
    {
        return $query->whereNotNull('reorder_level')
                     ->whereRaw('quantity <= reorder_level');
    }

    /**
     * Scope to get critical stock variants.
     */
    public function scopeCriticalStock($query)
    {
        return $query->whereNotNull('critical_level')
                     ->whereRaw('quantity <= critical_level');
    }

    /**
     * Boot method to auto-generate variant SKU.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($variant) {
            // Auto-generate variant_sku if not provided
            if (empty($variant->variant_sku)) {
                $variant->variant_sku = $variant->generateVariantSku();
            }
        });
        
        static::updating(function ($variant) {
            // Regenerate SKU if parent product or key attributes changed
            if ($variant->isDirty(['parent_product_id', 'color', 'size', 'material']) && empty($variant->variant_sku)) {
                $variant->variant_sku = $variant->generateVariantSku();
            }
        });
    }

    /**
     * Generate variant SKU based on parent product SKU and variant attributes.
     * 
     * Pattern: {PARENT_SKU}-{COLOR_CODE}-{SIZE}-{MATERIAL_CODE}
     * Example: LCB-001-BLK-7-NAPPA
     */
    public function generateVariantSku(): string
    {
        $parentProduct = $this->parentProduct;
        
        if (!$parentProduct) {
            // If parent product not loaded, try to load it
            $parentProduct = Product::find($this->parent_product_id);
        }
        
        if (!$parentProduct || empty($parentProduct->sku)) {
            // Fallback if parent SKU not available
            return 'VAR-' . strtoupper(uniqid());
        }
        
        $parts = [$parentProduct->sku];
        
        // Add color code (first 3 letters, uppercase)
        if (!empty($this->color)) {
            $colorCode = strtoupper(substr($this->color, 0, 3));
            $parts[] = $colorCode;
        }
        
        // Add size
        if (!empty($this->size)) {
            $parts[] = $this->size;
        }
        
        // Add material code (abbreviated)
        if (!empty($this->material)) {
            $materialCode = $this->abbreviateMaterial($this->material);
            $parts[] = $materialCode;
        }
        
        return implode('-', $parts);
    }

    /**
     * Abbreviate material name for SKU.
     */
    protected function abbreviateMaterial(string $material): string
    {
        // Common material abbreviations
        $abbreviations = [
            'leather' => 'LTH',
            'nappa' => 'NAPPA',
            'suede' => 'SDE',
            'canvas' => 'CNV',
            'rubber' => 'RBR',
            'synthetic' => 'SYN',
            'mesh' => 'MSH',
            'patent' => 'PAT',
        ];
        
        $materialLower = strtolower($material);
        
        // Check for known abbreviations
        foreach ($abbreviations as $key => $abbr) {
            if (stripos($materialLower, $key) !== false) {
                return $abbr;
            }
        }
        
        // Default: take first 3-4 letters, uppercase
        $words = explode(' ', $material);
        if (count($words) > 1) {
            // Multiple words: take first letter of each word
            return strtoupper(implode('', array_map(fn($w) => substr($w, 0, 1), $words)));
        }
        
        // Single word: take first 3-4 letters
        return strtoupper(substr($material, 0, min(4, strlen($material))));
    }
}
