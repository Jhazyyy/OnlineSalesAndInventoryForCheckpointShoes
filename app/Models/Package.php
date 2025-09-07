<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Package extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'package_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'package_name',
        'package_type',
        'description',
        'weight',
        'dimensions',
        'price',
        'quantity',
        'tracking_code',
        'status',
        'image',
        'contents',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'weight' => 'decimal:2',
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'contents' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Package types
     */
    const TYPE_STANDARD = 'standard';
    const TYPE_CUSTOM = 'custom';
    const TYPE_BUNDLE = 'bundle';

    /**
     * Package statuses
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DISCONTINUED = 'discontinued';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($package) {
            if (empty($package->tracking_code)) {
                $package->tracking_code = self::generateTrackingCode();
            }
        });
    }

    /**
     * Get the products included in this package.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'package_products', 'package_id', 'product_id')
                    ->withPivot('quantity', 'notes')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active packages.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include inactive packages.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Scope a query to only include discontinued packages.
     */
    public function scopeDiscontinued(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DISCONTINUED);
    }

    /**
     * Scope a query to only include packages with stock.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Scope a query to only include out of stock packages.
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('quantity', '<=', 0);
    }

    /**
     * Scope a query to only include low stock packages.
     */
    public function scopeLowStock(Builder $query, int $threshold = 5): Builder
    {
        return $query->where('quantity', '<=', $threshold)->where('quantity', '>', 0);
    }

    /**
     * Scope a query to search packages by name or tracking code.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('package_name', 'LIKE', "%{$search}%")
              ->orWhere('tracking_code', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by package type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('package_type', $type);
    }

    /**
     * Check if the package is in stock.
     */
    public function isInStock(int $requestedQuantity = 1): bool
    {
        return $this->quantity >= $requestedQuantity && $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the package is low in stock.
     */
    public function isLowStock(int $threshold = 5): bool
    {
        return $this->quantity <= $threshold && $this->quantity > 0;
    }

    /**
     * Check if the package is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
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
     * Update stock quantity after restocking.
     */
    public function increaseStock(int $quantity): bool
    {
        $this->quantity += $quantity;
        return $this->save();
    }

    /**
     * Get the package's full display name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->package_name . ($this->tracking_code ? ' (' . $this->tracking_code . ')' : '');
    }

    /**
     * Get formatted dimensions.
     */
    public function getFormattedDimensionsAttribute(): string
    {
        return $this->dimensions ?: 'Not specified';
    }

    /**
     * Get formatted weight.
     */
    public function getFormattedWeightAttribute(): string
    {
        return $this->weight ? number_format($this->weight, 2) . ' kg' : 'Not specified';
    }

    /**
     * Calculate total inventory value for this package.
     */
    public function getInventoryValueAttribute(): float
    {
        return $this->quantity * $this->price;
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'green',
            self::STATUS_INACTIVE => 'yellow',
            self::STATUS_DISCONTINUED => 'red',
            default => 'gray'
        };
    }

    /**
     * Get stock status badge color.
     */
    public function getStockStatusColorAttribute(): string
    {
        if ($this->quantity <= 0) {
            return 'red';
        } elseif ($this->quantity <= 5) {
            return 'yellow';
        } else {
            return 'green';
        }
    }

    /**
     * Get stock status text.
     */
    public function getStockStatusTextAttribute(): string
    {
        if ($this->quantity <= 0) {
            return 'Out of Stock';
        } elseif ($this->quantity <= 5) {
            return 'Low Stock';
        } else {
            return 'In Stock';
        }
    }

    /**
     * Get packages that need reordering (low stock).
     */
    public static function needsReordering(int $threshold = 5)
    {
        return self::active()->lowStock($threshold)->get();
    }

    /**
     * Get out of stock packages.
     */
    public static function outOfStock()
    {
        return self::active()->where('quantity', '<=', 0)->get();
    }

    /**
     * Calculate total inventory value for all active packages.
     */
    public static function totalInventoryValue(): float
    {
        return self::active()->selectRaw('SUM(quantity * price) as total')->value('total') ?? 0;
    }

    /**
     * Get packages by type.
     */
    public static function getByType(string $type)
    {
        return self::active()->byType($type)->get();
    }

    /**
     * Bulk update stock for multiple packages.
     */
    public static function bulkUpdateStock(array $stockUpdates): array
    {
        $results = [];
        
        foreach ($stockUpdates as $packageId => $quantity) {
            $package = self::find($packageId);
            if ($package) {
                $package->quantity = $quantity;
                $results[$packageId] = $package->save();
            } else {
                $results[$packageId] = false;
            }
        }
        
        return $results;
    }

    /**
     * Generate unique tracking code.
     */
    public static function generateTrackingCode(): string
    {
        do {
            $code = 'PKG-' . strtoupper(substr(uniqid(), -8));
        } while (self::where('tracking_code', $code)->exists());
        
        return $code;
    }

    /**
     * Set package as active.
     */
    public function activate(): bool
    {
        $this->status = self::STATUS_ACTIVE;
        return $this->save();
    }

    /**
     * Set package as inactive.
     */
    public function deactivate(): bool
    {
        $this->status = self::STATUS_INACTIVE;
        return $this->save();
    }

    /**
     * Set package as discontinued.
     */
    public function discontinue(): bool
    {
        $this->status = self::STATUS_DISCONTINUED;
        return $this->save();
    }

    /**
     * Get the package contents as a formatted array.
     */
    public function getContentsFormattedAttribute(): array
    {
        if (!$this->contents || !is_array($this->contents)) {
            return [];
        }

        $formatted = [];
        foreach ($this->contents as $item) {
            if (isset($item['product_id']) && isset($item['quantity'])) {
                $product = Product::find($item['product_id']);
                $formatted[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $product ? $product->product_name : 'Unknown Product',
                    'quantity' => $item['quantity'],
                    'product' => $product
                ];
            }
        }

        return $formatted;
    }
}
