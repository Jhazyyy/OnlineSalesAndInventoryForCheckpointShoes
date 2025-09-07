<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Shipment extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'shipment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shipment_number',
        'sales_order_id',
        'carrier',
        'service_type',
        'tracking_number',
        'reference_number',
        'status',
        'priority',
        'shipment_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'picked_up_at',
        'delivered_at',
        'shipping_address',
        'billing_address',
        'return_address',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'total_packages',
        'total_weight',
        'package_dimensions',
        'shipping_cost',
        'insurance_cost',
        'additional_fees',
        'total_shipping_cost',
        'is_insured',
        'insurance_value',
        'requires_signature',
        'is_fragile',
        'is_perishable',
        'special_instructions',
        'delivery_notes',
        'internal_notes',
        'tracking_history',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'shipment_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'total_packages' => 'integer',
        'total_weight' => 'decimal:2',
        'package_dimensions' => 'array',
        'shipping_cost' => 'decimal:2',
        'insurance_cost' => 'decimal:2',
        'additional_fees' => 'decimal:2',
        'total_shipping_cost' => 'decimal:2',
        'insurance_value' => 'decimal:2',
        'is_insured' => 'boolean',
        'requires_signature' => 'boolean',
        'is_fragile' => 'boolean',
        'is_perishable' => 'boolean',
        'tracking_history' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the sales order that owns the shipment.
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'order_id');
    }

    /**
     * Get the customer through the sales order.
     */
    public function customer(): BelongsTo
    {
        return $this->salesOrder->customer();
    }

    /**
     * Get the shipment items for the shipment.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class, 'shipment_id', 'shipment_id');
    }

    /**
     * Generate unique shipment number.
     */
    public static function generateShipmentNumber(): string
    {
        $date = Carbon::now();
        $prefix = 'SH' . $date->format('Ymd');
        $lastShipment = static::where('shipment_number', 'LIKE', $prefix . '%')
                              ->orderBy('shipment_number', 'desc')
                              ->first();
        
        if ($lastShipment) {
            $lastNumber = intval(substr($lastShipment->shipment_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to auto-generate shipment number.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($shipment) {
            if (empty($shipment->shipment_number)) {
                $shipment->shipment_number = static::generateShipmentNumber();
            }
            
            // Calculate total shipping cost if not set
            if (!$shipment->total_shipping_cost) {
                $shipment->total_shipping_cost = $shipment->shipping_cost + $shipment->insurance_cost + $shipment->additional_fees;
            }
        });
        
        static::updating(function ($shipment) {
            // Recalculate total shipping cost
            $shipment->total_shipping_cost = $shipment->shipping_cost + $shipment->insurance_cost + $shipment->additional_fees;
        });
    }

    // Scopes
    
    /**
     * Scope a query to only include pending shipments.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include preparing shipments.
     */
    public function scopePreparing(Builder $query): Builder
    {
        return $query->where('status', 'preparing');
    }

    /**
     * Scope a query to only include shipped shipments.
     */
    public function scopeShipped(Builder $query): Builder
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope a query to only include in-transit shipments.
     */
    public function scopeInTransit(Builder $query): Builder
    {
        return $query->where('status', 'in_transit');
    }

    /**
     * Scope a query to only include out for delivery shipments.
     */
    public function scopeOutForDelivery(Builder $query): Builder
    {
        return $query->where('status', 'out_for_delivery');
    }

    /**
     * Scope a query to only include delivered shipments.
     */
    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope a query to only include exception shipments.
     */
    public function scopeException(Builder $query): Builder
    {
        return $query->where('status', 'exception');
    }

    /**
     * Scope a query to only include returned shipments.
     */
    public function scopeReturned(Builder $query): Builder
    {
        return $query->where('status', 'returned');
    }

    /**
     * Scope a query to only include cancelled shipments.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to search shipments by shipment number, tracking number, or recipient.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('shipment_number', 'LIKE', "%{$search}%")
              ->orWhere('tracking_number', 'LIKE', "%{$search}%")
              ->orWhere('recipient_name', 'LIKE', "%{$search}%")
              ->orWhere('recipient_email', 'LIKE', "%{$search}%")
              ->orWhere('carrier', 'LIKE', "%{$search}%")
              ->orWhereHas('salesOrder', function ($orderQuery) use ($search) {
                  $orderQuery->where('order_number', 'LIKE', "%{$search}%")
                            ->orWhereHas('customer', function ($customerQuery) use ($search) {
                                $customerQuery->where('first_name', 'LIKE', "%{$search}%")
                                             ->orWhere('last_name', 'LIKE', "%{$search}%")
                                             ->orWhere('email', 'LIKE', "%{$search}%")
                                             ->orWhere('company_name', 'LIKE', "%{$search}%");
                            });
              });
        });
    }

    /**
     * Scope a query to only include shipments from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('shipment_date', Carbon::today());
    }

    /**
     * Scope a query to only include shipments from this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('shipment_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include shipments from this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('shipment_date', Carbon::now()->month)
                    ->whereYear('shipment_date', Carbon::now()->year);
    }

    /**
     * Scope a query to only include shipments from a date range.
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('shipment_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include overdue shipments.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotNull('expected_delivery_date')
                    ->where('expected_delivery_date', '<', Carbon::today())
                    ->whereNotIn('status', ['delivered', 'cancelled', 'returned']);
    }

    /**
     * Scope a query to only include shipments by carrier.
     */
    public function scopeByCarrier(Builder $query, string $carrier): Builder
    {
        return $query->where('carrier', $carrier);
    }

    /**
     * Scope a query to only include shipments by priority.
     */
    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    // Helper Methods

    /**
     * Check if the shipment is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->expected_delivery_date || in_array($this->status, ['delivered', 'cancelled', 'returned'])) {
            return false;
        }
        
        return Carbon::parse($this->expected_delivery_date)->isPast();
    }

    /**
     * Get the days until expected delivery or days overdue.
     */
    public function getDaysUntilDeliveryAttribute(): ?int
    {
        if (!$this->expected_delivery_date) {
            return null;
        }
        
        return Carbon::now()->diffInDays(Carbon::parse($this->expected_delivery_date), false);
    }

    /**
     * Get the total number of items in the shipment.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity_shipped');
    }

    /**
     * Get the total value of the shipment based on items.
     */
    public function getTotalValueAttribute(): float
    {
        return $this->items->sum('line_total');
    }

    /**
     * Check if the shipment can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['delivered', 'cancelled', 'returned']);
    }

    /**
     * Check if the shipment can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['pending', 'preparing']);
    }

    /**
     * Check if the shipment can be shipped.
     */
    public function canBeShipped(): bool
    {
        return in_array($this->status, ['pending', 'preparing']);
    }

    /**
     * Check if tracking is available.
     */
    public function hasTracking(): bool
    {
        return !empty($this->tracking_number) && !empty($this->carrier);
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'preparing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'in_transit' => 'bg-purple-100 text-purple-800',
            'out_for_delivery' => 'bg-orange-100 text-orange-800',
            'delivered' => 'bg-green-100 text-green-800',
            'exception' => 'bg-red-100 text-red-800',
            'returned' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800',
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
     * Add tracking update to history.
     */
    public function addTrackingUpdate(array $update): void
    {
        $history = $this->tracking_history ?? [];
        $update['timestamp'] = now();
        $history[] = $update;
        $this->tracking_history = $history;
        $this->save();
    }

    /**
     * Get the latest tracking update.
     */
    public function getLatestTrackingUpdateAttribute(): ?array
    {
        $history = $this->tracking_history;
        return $history ? end($history) : null;
    }

    /**
     * Get recent shipments.
     */
    public static function recent(int $limit = 10)
    {
        return static::with(['salesOrder.customer', 'items'])
                     ->latest('created_at')
                     ->limit($limit)
                     ->get();
    }

    /**
     * Get overdue shipments.
     */
    public static function overdue()
    {
        return static::with(['salesOrder.customer'])
                     ->overdue()
                     ->get();
    }

    /**
     * Get shipments requiring attention.
     */
    public static function requiresAttention()
    {
        return static::with(['salesOrder.customer'])
                     ->where(function ($query) {
                         $query->where('status', 'exception')
                               ->orWhere(function ($q) {
                                   $q->whereNotNull('expected_delivery_date')
                                     ->where('expected_delivery_date', '<', Carbon::today())
                                     ->whereNotIn('status', ['delivered', 'cancelled', 'returned']);
                               });
                     })
                     ->get();
    }
}
