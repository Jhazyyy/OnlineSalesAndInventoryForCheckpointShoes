<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class SalesOrder extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'order_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'customer_id',
        'order_date',
        'required_date',
        'shipped_date',
        'status',
        'priority',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_amount',
        'total_amount',
        'payment_status',
        'payment_method',
        'shipping_address',
        'billing_address',
        'notes',
        'internal_notes',
        'tracking_number',
        'shipping_carrier',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'date',
        'required_date' => 'date',
        'shipped_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the sales order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the order items for the sales order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Get the shipments for the sales order.
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'sales_order_id', 'order_id');
    }

    /**
     * Get the payments for the sales order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id', 'order_id');
    }

    /**
     * Generate unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $date = Carbon::now();
        $prefix = 'SO' . $date->format('Ymd');
        $lastOrder = static::where('order_number', 'LIKE', $prefix . '%')
                          ->orderBy('order_number', 'desc')
                          ->first();
        
        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to auto-generate order number.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed orders.
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include processing orders.
     */
    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include shipped orders.
     */
    public function scopeShipped(Builder $query): Builder
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope a query to only include delivered orders.
     */
    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include returned orders.
     */
    public function scopeReturned(Builder $query): Builder
    {
        return $query->where('status', 'returned');
    }

    /**
     * Scope a query to only include orders from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('order_date', Carbon::today());
    }

    /**
     * Scope a query to only include orders from this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('order_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include orders from this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('order_date', Carbon::now()->month)
                    ->whereYear('order_date', Carbon::now()->year);
    }

    /**
     * Scope a query to only include orders from a date range.
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to search orders by order number, customer name, or email.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('order_number', 'LIKE', "%{$search}%")
              ->orWhere('notes', 'LIKE', "%{$search}%")
              ->orWhere('tracking_number', 'LIKE', "%{$search}%")
              ->orWhereHas('customer', function ($customerQuery) use ($search) {
                  $customerQuery->where('first_name', 'LIKE', "%{$search}%")
                               ->orWhere('last_name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%")
                               ->orWhere('company_name', 'LIKE', "%{$search}%");
              });
        });
    }

    /**
     * Scope a query to only include orders for a specific customer.
     */
    public function scopeForCustomer(Builder $query, $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope a query to only include orders of a specific status.
     */
    public function scopeOfStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include orders with a specific priority.
     */
    public function scopeOfPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include orders that have shipments.
     */
    public function scopeWithShipments(Builder $query): Builder
    {
        return $query->whereHas('shipments');
    }

    /**
     * Scope a query to only include orders without shipments.
     */
    public function scopeWithoutShipments(Builder $query): Builder
    {
        return $query->whereDoesntHave('shipments');
    }

    /**
     * Scope a query to only include orders with active shipments.
     */
    public function scopeWithActiveShipments(Builder $query): Builder
    {
        return $query->whereHas('shipments', function ($shipmentQuery) {
            $shipmentQuery->whereNotIn('status', ['delivered', 'cancelled', 'returned']);
        });
    }

    /**
     * Scope a query to only include orders with pending shipments.
     */
    public function scopeWithPendingShipments(Builder $query): Builder
    {
        return $query->whereHas('shipments', function ($shipmentQuery) {
            $shipmentQuery->where('status', 'pending');
        });
    }

    /**
     * Scope a query to only include fully shipped orders.
     */
    public function scopeFullyShipped(Builder $query): Builder
    {
        return $query->whereHas('shipments', function ($shipmentQuery) {
            // This is a simplified version - in practice, you'd need more complex logic
            // to check if all items are fully shipped across all shipments
            $shipmentQuery->where('status', '!=', 'cancelled');
        })->where('status', 'shipped');
    }

    /**
     * Scope a query to only include partially shipped orders.
     */
    public function scopePartiallyShipped(Builder $query): Builder
    {
        return $query->whereHas('shipments')
                    ->where('status', '!=', 'shipped')
                    ->where('status', '!=', 'delivered');
    }

    /**
     * Get the total quantity of items in the order.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get the total number of different products in the order.
     */
    public function getTotalProductsAttribute(): int
    {
        return $this->items->count();
    }

    /**
     * Check if the order is overdue (required date has passed and not delivered).
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->required_date || in_array($this->status, ['delivered', 'cancelled', 'returned'])) {
            return false;
        }
        
        return Carbon::parse($this->required_date)->isPast();
    }

    /**
     * Get the days until required date or days overdue.
     */
    public function getDaysUntilRequiredAttribute(): ?int
    {
        if (!$this->required_date) {
            return null;
        }
        
        return Carbon::now()->diffInDays(Carbon::parse($this->required_date), false);
    }

    /**
     * Check if the order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['shipped', 'delivered', 'cancelled', 'returned']);
    }

    /**
     * Check if the order can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if the order can be confirmed.
     */
    public function canBeConfirmed(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the order can be shipped.
     */
    public function canBeShipped(): bool
    {
        return in_array($this->status, ['confirmed', 'processing']);
    }

    /**
     * Check if the order has any shipments.
     */
    public function hasShipments(): bool
    {
        return $this->shipments()->exists();
    }

    /**
     * Check if the order has any active shipments (not delivered, cancelled, or returned).
     */
    public function hasActiveShipments(): bool
    {
        return $this->shipments()
                   ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
                   ->exists();
    }

    /**
     * Check if the order has any pending shipments.
     */
    public function hasPendingShipments(): bool
    {
        return $this->shipments()->where('status', 'pending')->exists();
    }

    /**
     * Check if all items in the order are fully shipped.
     */
    public function isFullyShipped(): bool
    {
        if (!$this->hasShipments()) {
            return false;
        }

        // Get all order items with their quantities
        $orderItems = $this->items;
        
        foreach ($orderItems as $orderItem) {
            $shippedQuantity = $this->shipments()
                                   ->with('items')
                                   ->get()
                                   ->flatMap->items
                                   ->where('product_id', $orderItem->product_id)
                                   ->sum('quantity_shipped');
            
            if ($shippedQuantity < $orderItem->quantity) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if the order is partially shipped.
     */
    public function isPartiallyShipped(): bool
    {
        return $this->hasShipments() && !$this->isFullyShipped();
    }

    /**
     * Get the latest shipment for this order.
     */
    public function getLatestShipmentAttribute()
    {
        return $this->shipments()->latest('created_at')->first();
    }

    /**
     * Get all delivered shipments for this order.
     */
    public function getDeliveredShipmentsAttribute()
    {
        return $this->shipments()->where('status', 'delivered')->get();
    }

    /**
     * Get the total shipping cost from all shipments.
     */
    public function getTotalShippingCostAttribute(): float
    {
        return $this->shipments()->sum('total_shipping_cost') ?? 0.00;
    }

    /**
     * Get shipment status summary.
     */
    public function getShipmentStatusSummaryAttribute(): array
    {
        if (!$this->hasShipments()) {
            return ['status' => 'not_shipped', 'count' => 0];
        }

        $shipments = $this->shipments;
        $statusCounts = $shipments->groupBy('status')->map->count();
        
        // Determine overall shipment status
        if ($shipments->where('status', 'delivered')->count() === $shipments->count()) {
            $overallStatus = 'all_delivered';
        } elseif ($shipments->where('status', 'delivered')->count() > 0) {
            $overallStatus = 'partially_delivered';
        } elseif ($shipments->whereIn('status', ['shipped', 'in_transit', 'out_for_delivery'])->count() > 0) {
            $overallStatus = 'in_transit';
        } elseif ($shipments->where('status', 'preparing')->count() > 0) {
            $overallStatus = 'preparing';
        } else {
            $overallStatus = 'pending';
        }
        
        return [
            'status' => $overallStatus,
            'count' => $shipments->count(),
            'breakdown' => $statusCounts->toArray()
        ];
    }

    /**
     * Calculate and update order totals based on items.
     */
    public function calculateTotals(): void
    {
        $this->load('items');
        
        $this->subtotal = $this->items->sum('line_total');
        $this->total_amount = $this->subtotal + $this->tax_amount + $this->shipping_amount - $this->discount_amount;
        $this->save();
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-indigo-100 text-indigo-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'returned' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get priority badge class for UI.
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-green-100 text-green-800',
            'normal' => 'bg-gray-100 text-gray-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the payment status badge class for UI.
     */
    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'partial' => 'bg-orange-100 text-orange-800',
            'paid' => 'bg-green-100 text-green-800',
            'refunded' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get recent orders.
     */
    public static function recent(int $limit = 10)
    {
        return static::with(['customer', 'items'])
                     ->latest('created_at')
                     ->limit($limit)
                     ->get();
    }

    /**
     * Get overdue orders.
     */
    public static function overdue()
    {
        return static::with(['customer'])
                     ->whereNotNull('required_date')
                     ->where('required_date', '<', Carbon::today())
                     ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
                     ->get();
    }

    /**
     * Get orders requiring attention (pending confirmation, overdue, etc.).
     */
    public static function requiresAttention()
    {
        return static::with(['customer'])
                     ->where(function ($query) {
                         $query->where('status', 'pending')
                               ->orWhere(function ($q) {
                                   $q->whereNotNull('required_date')
                                     ->where('required_date', '<', Carbon::today())
                                     ->whereNotIn('status', ['delivered', 'cancelled', 'returned']);
                               });
                     })
                     ->get();
    }
}
