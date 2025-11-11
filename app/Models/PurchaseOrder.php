<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PurchaseOrder extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'order_id';
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'order_id';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'supplier_id',
        'order_date',
        'expected_date',
        'received_date',
        'status',
        'priority',
        'subtotal',
        'shipping_amount',
        'total_amount',
        'paid_amount',
        'payment_status',
        'payment_method',
        'delivery_address',
        'billing_address',
        'notes',
        'internal_notes',
        'reference_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'subtotal' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the supplier that owns the purchase order.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get the order items for the purchase order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Get the payments for the purchase order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class, 'purchase_order_id', 'order_id');
    }

    /**
     * Get the deliveries for the purchase order.
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(PurchaseDelivery::class, 'purchase_order_id', 'order_id');
    }

    /**
     * Generate unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $date = Carbon::now();
        $prefix = 'PO' . $date->format('Ymd');
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
            // Ensure reference number exists for cross-party tracking
            if (empty($order->reference_number)) {
                try {
                    $order->reference_number = \App\Services\ReferenceNumberService::generate('purchase_orders', 'reference_number', 'PO');
                } catch (\Throwable $e) {
                    // Fallback to order number if generation fails
                    $order->reference_number = $order->order_number;
                }
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
     * Scope a query to only include approved orders.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include ordered orders.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->where('status', 'ordered');
    }

    /**
     * Scope a query to only include partially received orders.
     */
    public function scopePartialReceived(Builder $query): Builder
    {
        return $query->where('status', 'partial_received');
    }

    /**
     * Scope a query to only include received orders.
     */
    public function scopeReceived(Builder $query): Builder
    {
        return $query->where('status', 'received');
    }

    /**
     * Scope a query to only include cancelled orders.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
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
     * Scope a query to search orders by order number, supplier name, or reference number.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('order_number', 'LIKE', "%{$search}%")
              ->orWhere('notes', 'LIKE', "%{$search}%")
              ->orWhere('reference_number', 'LIKE', "%{$search}%")
              ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                  $supplierQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('company_name', 'LIKE', "%{$search}%")
                               ->orWhere('contact_person', 'LIKE', "%{$search}%");
              });
        });
    }

    /**
     * Scope a query to only include orders for a specific supplier.
     */
    public function scopeForSupplier(Builder $query, $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
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
     * Get the total quantity of items in the order.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity_ordered');
    }

    /**
     * Get the total number of different products in the order.
     */
    public function getTotalProductsAttribute(): int
    {
        return $this->items->count();
    }

    /**
     * Check if the order is overdue (expected date has passed and not received).
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->expected_date || in_array($this->status, ['received', 'cancelled'])) {
            return false;
        }
        
        return Carbon::parse($this->expected_date)->isPast();
    }

    /**
     * Get the days until expected date or days overdue.
     */
    public function getDaysUntilExpectedAttribute(): ?int
    {
        if (!$this->expected_date) {
            return null;
        }
        
        return Carbon::now()->diffInDays(Carbon::parse($this->expected_date), false);
    }

    /**
     * Check if the order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['received', 'cancelled']);
    }

    /**
     * Check if the order can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    /**
     * Check if the order can be approved.
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the order can be marked as ordered.
     */
    public function canBeOrdered(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if items can be received.
     * 
     * Items can only be received if:
     * 1. The order status is 'ordered' or 'partial_received'
     * 2. A delivery MUST exist for this order (delivery is mandatory)
     * 3. At least one delivery must be in 'delivered' status
     */
    public function canReceiveItems(): bool
    {
        // Check if order status allows receiving
        if (!in_array($this->status, ['ordered', 'partial_received'])) {
            return false;
        }

        // Check if there's any delivery for this order (excluding cancelled)
        $deliveries = $this->deliveries()->whereNotIn('status', ['cancelled'])->get();
        
        // Delivery is MANDATORY - PO cannot be received without delivery
        if ($deliveries->count() === 0) {
            return false;
        }

        // At least one delivery must be in 'delivered' status
        return $deliveries->where('status', 'delivered')->count() > 0;
    }

    /**
     * Calculate and update order totals based on items.
     */
    public function calculateTotals(): void
    {
        $this->load('items');
        
        // Calculate subtotal from items
        $subtotal = 0.0;
        foreach ($this->items as $item) {
            $subtotal += (float) $item->line_total;
        }
        
        $this->subtotal = $subtotal;
        
        // Calculate total (subtotal + shipping)
        $shippingAmount = (float) ($this->shipping_amount ?? 0);
        
        $this->total_amount = $subtotal + $shippingAmount;
        $this->save();
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'ordered' => 'bg-indigo-100 text-indigo-800',
            'partial_received' => 'bg-orange-100 text-orange-800',
            'received' => 'bg-green-100 text-green-800',
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
        return static::with(['supplier', 'items'])
                     ->latest('created_at')
                     ->limit($limit)
                     ->get();
    }

    /**
     * Get overdue orders.
     */
    public static function overdue()
    {
        return static::with(['supplier'])
                     ->whereNotNull('expected_date')
                     ->where('expected_date', '<', Carbon::today())
                     ->whereNotIn('status', ['received', 'cancelled'])
                     ->get();
    }

    /**
     * Get orders requiring attention (pending approval, overdue, etc.).
     */
    public static function requiresAttention()
    {
        return static::with(['supplier'])
                     ->where(function ($query) {
                         $query->where('status', 'pending')
                               ->orWhere(function ($q) {
                                   $q->whereNotNull('expected_date')
                                     ->where('expected_date', '<', Carbon::today())
                                     ->whereNotIn('status', ['received', 'cancelled']);
                               });
                     })
                     ->get();
    }

    /**
     * Get the remaining balance (unpaid amount).
     */
    public function getRemainingBalanceAttribute(): float
    {
        return (float)($this->total_amount - ($this->paid_amount ?? 0));
    }

    /**
     * Update paid amount and payment status based on payments.
     */
    public function updatePaidAmount(): void
    {
        // Only count completed payments
        $totalPaid = $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
        
        $this->paid_amount = $totalPaid;
        
        // Update payment status
        if ($totalPaid >= $this->total_amount) {
            $this->payment_status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'pending';
        }
        
        $this->save();
    }

    /**
     * Check if order can accept more payments.
     */
    public function canAcceptPayment(): bool
    {
        return $this->payment_status !== 'paid' && 
               $this->remaining_balance > 0 &&
               !in_array($this->status, ['cancelled']);
    }

}
