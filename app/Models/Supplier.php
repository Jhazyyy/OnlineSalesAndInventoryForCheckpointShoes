<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'supplier_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'supplier_name',
        'phone',
        'email',
        'supplier_type',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'tax_id',
        'payment_terms',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the purchases for the supplier.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get the purchase orders for the supplier.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get the supplier's full address.
     */
    public function getFullAddressAttribute(): string
    {
        $addressParts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Get total amount purchased from this supplier.
     */
    public function getTotalPurchasedAttribute(): float
    {
        return $this->purchases()->sum('total_amount') ?? 0;
    }

    /**
     * Get total number of purchase orders from this supplier.
     */
    public function getTotalOrdersAttribute(): int
    {
        return $this->purchases()->count();
    }

    /**
     * Get average order value from this supplier.
     */
    public function getAverageOrderValueAttribute(): float
    {
        $totalOrders = $this->total_orders;

        if ($totalOrders === 0) {
            return 0.0;
        }

        return $this->total_purchased / $totalOrders;
    }

    /**
     * Get the supplier's last order date.
     */
    public function getLastOrderDateAttribute(): ?Carbon
    {
        $lastPurchase = $this->purchases()->latest('purchase_date')->first();

        return $lastPurchase ? $lastPurchase->purchase_date : null;
    }

    /**
     * Scope a query to only include active suppliers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive suppliers.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to search suppliers by name, email, or contact.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('supplier_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('supplier_contact', 'LIKE', "%{$search}%")
                ->orWhere('tax_id', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by supplier type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('supplier_type', $type);
    }

    /**
     * Get top suppliers by purchase volume.
     */
    public static function topSuppliers(int $limit = 10)
    {
        return self::with(['purchases'])
            ->get()
            ->sortByDesc('total_purchased')
            ->take($limit);
    }

    /**
     * Get recent suppliers.
     */
    public static function recent(int $limit = 10)
    {
        return self::latest('created_at')->limit($limit)->get();
    }

    /**
     * Get suppliers by country.
     */
    public static function getByCountry(string $country)
    {
        return self::where('country', 'LIKE', "%{$country}%")->get();
    }

    /**
     * Check if supplier is reliable based on order frequency.
     */
    public function isReliable(int $minOrders = 5): bool
    {
        return $this->total_orders >= $minOrders;
    }

    /**
     * Get suppliers with recent orders (within specified days).
     */
    public static function withRecentOrders(int $days = 30)
    {
        return self::whereHas('purchases', function ($query) use ($days) {
            $query->where('purchase_date', '>=', Carbon::now()->subDays($days));
        })->get();
    }
}
