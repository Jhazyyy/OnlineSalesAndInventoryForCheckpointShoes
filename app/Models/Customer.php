<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

/**
 * Customer Model - E-commerce Integration
 * 
 * Represents customers from the e-commerce application.
 * Customer data is synchronized from the e-commerce system.
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'customer_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'customer_type',
        'company_name',
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
     * Get the sales for the customer.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the sales orders for the customer.
     */
    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the customer's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the customer's display name (company name for business, full name for individual).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->customer_type === 'business' && $this->company_name) {
            return $this->company_name;
        }
        
        return $this->full_name;
    }

    /**
     * Get the customer's full address.
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
     * Scope a query to only include active customers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive customers.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include individual customers.
     */
    public function scopeIndividual(Builder $query): Builder
    {
        return $query->where('customer_type', 'individual');
    }

    /**
     * Scope a query to only include business customers.
     */
    public function scopeBusiness(Builder $query): Builder
    {
        return $query->where('customer_type', 'business');
    }

    /**
     * Scope a query to search customers by name, email, or company.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('company_name', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get total amount spent by this customer.
     */
    public function getTotalSpentAttribute(): float
    {
        return $this->sales()->with('product')->get()->sum('total_amount');
    }

    /**
     * Get total number of orders by this customer.
     */
    public function getTotalOrdersAttribute(): int
    {
        return $this->sales()->count();
    }

    /**
     * Get average order value for this customer.
     */
    public function getAverageOrderValueAttribute(): float
    {
        $totalOrders = $this->total_orders;
        
        if ($totalOrders === 0) {
            return 0.0;
        }
        
        return $this->total_spent / $totalOrders;
    }

    /**
     * Get the customer's last order date.
     */
    public function getLastOrderDateAttribute(): ?Carbon
    {
        $lastSale = $this->sales()->latest('date')->first();
        
        return $lastSale ? $lastSale->date : null;
    }

    /**
     * Check if customer is a VIP based on total spent.
     */
    public function isVip(float $threshold = 1000.0): bool
    {
        return $this->total_spent >= $threshold;
    }

    /**
     * Get customers by city.
     */
    public static function getByCity(string $city)
    {
        return self::where('city', 'LIKE', "%{$city}%")->get();
    }

    /**
     * Get top spending customers.
     */
    public static function topSpenders(int $limit = 10)
    {
        return self::with(['sales', 'sales.product'])
                   ->get()
                   ->sortByDesc('total_spent')
                   ->take($limit);
    }

    /**
     * Get recent customers.
     */
    public static function recent(int $limit = 10)
    {
        return self::latest('created_at')->limit($limit)->get();
    }

    /**
     * Get customers with birthdays this month.
     */
    public static function birthdaysThisMonth()
    {
        return self::whereMonth('date_of_birth', Carbon::now()->month)
                   ->whereNotNull('date_of_birth')
                   ->get();
    }
}
