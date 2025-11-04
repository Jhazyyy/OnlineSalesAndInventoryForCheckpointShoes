<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PurchaseDelivery extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'delivery_id';
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'delivery_id';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'delivery_number',
        'reference_number',
        'purchase_order_id',
        'supplier_id',
        'carrier',
        'tracking_number',
        'service_type',
        'delivery_date',
        'scheduled_delivery_date',
        'actual_delivery_date',
        'picked_up_at',
        'delivered_at',
        'status',
        'priority',
        'delivery_address',
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
        'total_quantity_expected',
        'total_quantity_delivered',
        'total_quantity_damaged',
        'total_amount_expected',
        'total_amount_delivered',
        'delivery_notes',
        'internal_notes',
        'damage_notes',
        'special_instructions',
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
        'delivery_date' => 'date',
        'scheduled_delivery_date' => 'date',
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
        'total_quantity_expected' => 'integer',
        'total_quantity_delivered' => 'integer',
        'total_quantity_damaged' => 'integer',
        'total_amount_expected' => 'decimal:2',
        'total_amount_delivered' => 'decimal:2',
        'tracking_history' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the purchase order that owns the delivery.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'order_id');
    }

    /**
     * Get the supplier that owns the delivery.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get the delivery items for the purchase delivery.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseDeliveryItem::class, 'delivery_id', 'delivery_id');
    }

    /**
     * Get the receives created from this delivery.
     * 
     * WORKFLOW: When a delivery arrives, one or more receives can be created
     * to record the actual receipt of goods and update inventory.
     */
    public function receives(): HasMany
    {
        return $this->hasMany(PurchaseReceive::class, 'delivery_id', 'delivery_id');
    }

    /**
     * Generate unique delivery number.
     */
    public static function generateDeliveryNumber(): string
    {
        $date = Carbon::now();
        $prefix = 'DEL' . $date->format('Ymd');
        $lastDelivery = static::where('delivery_number', 'LIKE', $prefix . '%')
                          ->orderBy('delivery_number', 'desc')
                          ->first();
        
        if ($lastDelivery) {
            $lastNumber = intval(substr($lastDelivery->delivery_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique tracking number.
     * Format: TRK-YYYYMMDD-XXXX (e.g., TRK-20251104-0001)
     */
    public static function generateTrackingNumber(): string
    {
        $date = Carbon::now();
        $prefix = 'TRK-' . $date->format('Ymd') . '-';
        $lastDelivery = static::where('tracking_number', 'LIKE', $prefix . '%')
                          ->orderBy('tracking_number', 'desc')
                          ->first();
        
        if ($lastDelivery) {
            $lastNumber = intval(substr($lastDelivery->tracking_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to auto-generate delivery number, tracking number, and reference number.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($delivery) {
            // Auto-generate delivery number if not provided
            if (empty($delivery->delivery_number)) {
                $delivery->delivery_number = static::generateDeliveryNumber();
            }
            
            // Auto-generate tracking number if not provided
            if (empty($delivery->tracking_number)) {
                $delivery->tracking_number = static::generateTrackingNumber();
            }
            
            // Auto-generate reference number if not provided
            if (empty($delivery->reference_number)) {
                try {
                    $delivery->reference_number = \App\Services\ReferenceNumberService::generate('purchase_deliveries', 'reference_number', 'PD');
                } catch (\Throwable $e) {
                    // Fallback to delivery number
                    $delivery->reference_number = $delivery->delivery_number;
                }
            }
            
            // Calculate total shipping cost if not set
            if (!$delivery->total_shipping_cost) {
                $delivery->total_shipping_cost = ($delivery->shipping_cost ?? 0) + ($delivery->insurance_cost ?? 0) + ($delivery->additional_fees ?? 0);
            }
        });
        
        static::updating(function ($delivery) {
            // Recalculate total shipping cost
            $delivery->total_shipping_cost = ($delivery->shipping_cost ?? 0) + ($delivery->insurance_cost ?? 0) + ($delivery->additional_fees ?? 0);
        });
    }

    // Scopes
    
    /**
     * Scope a query to only include scheduled deliveries.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope a query to only include in-transit deliveries.
     */
    public function scopeInTransit(Builder $query): Builder
    {
        return $query->where('status', 'in_transit');
    }

    /**
     * Scope a query to only include out for delivery.
     */
    public function scopeOutForDelivery(Builder $query): Builder
    {
        return $query->where('status', 'out_for_delivery');
    }

    /**
     * Scope a query to only include delivered items.
     */
    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope a query to only include delayed deliveries.
     */
    public function scopeDelayed(Builder $query): Builder
    {
        return $query->where('status', 'delayed');
    }

    /**
     * Scope a query to only include failed deliveries.
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include cancelled deliveries.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to search deliveries.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('delivery_number', 'LIKE', "%{$search}%")
              ->orWhere('tracking_number', 'LIKE', "%{$search}%")
              ->orWhere('carrier', 'LIKE', "%{$search}%")
              ->orWhere('recipient_name', 'LIKE', "%{$search}%")
              ->orWhereHas('purchaseOrder', function ($orderQuery) use ($search) {
                  $orderQuery->where('order_number', 'LIKE', "%{$search}%");
              })
              ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                  $supplierQuery->where('supplier_name', 'LIKE', "%{$search}%")
                               ->orWhere('name', 'LIKE', "%{$search}%");
              });
        });
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('delivery_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include overdue deliveries.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotNull('scheduled_delivery_date')
                    ->where('scheduled_delivery_date', '<', Carbon::today())
                    ->whereNotIn('status', ['delivered', 'cancelled']);
    }

    /**
     * Scope a query to filter by carrier.
     */
    public function scopeByCarrier(Builder $query, string $carrier): Builder
    {
        return $query->where('carrier', $carrier);
    }

    /**
     * Scope a query to filter by priority.
     */
    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    // Helper Methods

    /**
     * Check if the delivery is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->scheduled_delivery_date || in_array($this->status, ['delivered', 'cancelled'])) {
            return false;
        }
        
        return Carbon::parse($this->scheduled_delivery_date)->isPast();
    }

    /**
     * Get the days until scheduled delivery or days overdue.
     */
    public function getDaysUntilDeliveryAttribute(): ?int
    {
        if (!$this->scheduled_delivery_date) {
            return null;
        }
        
        return Carbon::now()->diffInDays(Carbon::parse($this->scheduled_delivery_date), false);
    }

    /**
     * Get the completion percentage.
     */
    public function getCompletionPercentageAttribute(): float
    {
        if ($this->total_quantity_expected == 0) {
            return 0;
        }
        
        return round(($this->total_quantity_delivered / $this->total_quantity_expected) * 100, 2);
    }

    /**
     * Check if the delivery can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['delivered', 'cancelled']);
    }

    /**
     * Check if the delivery can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['scheduled', 'in_transit']);
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
            'scheduled' => 'bg-blue-100 text-blue-800',
            'in_transit' => 'bg-purple-100 text-purple-800',
            'out_for_delivery' => 'bg-orange-100 text-orange-800',
            'delivered' => 'bg-green-100 text-green-800',
            'delayed' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
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
     * Get recent deliveries.
     */
    public static function recent(int $limit = 10)
    {
        return static::with(['purchaseOrder', 'supplier', 'items'])
                     ->latest('created_at')
                     ->limit($limit)
                     ->get();
    }

    /**
     * Get overdue deliveries.
     */
    public static function overdueDeliveries()
    {
        return static::with(['purchaseOrder', 'supplier'])
                     ->overdue()
                     ->get();
    }

    /**
     * Get deliveries requiring attention.
     */
    public static function requiresAttention()
    {
        return static::with(['purchaseOrder', 'supplier'])
                     ->where(function ($query) {
                         $query->where('status', 'delayed')
                               ->orWhere('status', 'failed')
                               ->orWhere(function ($q) {
                                   $q->whereNotNull('scheduled_delivery_date')
                                     ->where('scheduled_delivery_date', '<', Carbon::today())
                                     ->whereNotIn('status', ['delivered', 'cancelled']);
                               });
                     })
                     ->get();
    }
}
