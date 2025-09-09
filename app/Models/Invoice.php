<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'invoice_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'sales_order_id',
        'invoice_date',
        'due_date',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'payment_date',
        'notes',
        'terms_conditions',
        'billing_address',
        'payment_terms',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the invoice.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the sales order that this invoice is based on (if any).
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'order_id');
    }

    /**
     * Get the invoice items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'invoice_id');
    }

    /**
     * Generate unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $date = Carbon::now();
        $prefix = 'INV' . $date->format('Ymd');
        $lastInvoice = static::where('invoice_number', 'LIKE', $prefix . '%')
                          ->orderBy('invoice_number', 'desc')
                          ->first();
        
        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to auto-generate invoice number.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
    }

    /**
     * Scope a query to only include draft invoices.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include sent invoices.
     */
    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope a query to only include paid invoices.
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include overdue invoices.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope a query to only include cancelled invoices.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include pending payment invoices.
     */
    public function scopePendingPayment(Builder $query): Builder
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope a query to only include partially paid invoices.
     */
    public function scopePartiallyPaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'partial');
    }

    /**
     * Scope a query to only include fully paid invoices.
     */
    public function scopeFullyPaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope a query to only include invoices from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('invoice_date', Carbon::today());
    }

    /**
     * Scope a query to only include invoices from this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('invoice_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include invoices from this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('invoice_date', Carbon::now()->month)
                    ->whereYear('invoice_date', Carbon::now()->year);
    }

    /**
     * Scope a query to only include invoices from a date range.
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to search invoices by invoice number, customer name, or email.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'LIKE', "%{$search}%")
              ->orWhere('notes', 'LIKE', "%{$search}%")
              ->orWhereHas('customer', function ($customerQuery) use ($search) {
                  $customerQuery->where('first_name', 'LIKE', "%{$search}%")
                               ->orWhere('last_name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%")
                               ->orWhere('company_name', 'LIKE', "%{$search}%");
              });
        });
    }

    /**
     * Scope a query to only include invoices for a specific customer.
     */
    public function scopeForCustomer(Builder $query, $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope a query to only include invoices of a specific status.
     */
    public function scopeOfStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include invoices with a specific payment status.
     */
    public function scopeOfPaymentStatus(Builder $query, string $paymentStatus): Builder
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Get the total quantity of items in the invoice.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get the total number of different products in the invoice.
     */
    public function getTotalProductsAttribute(): int
    {
        return $this->items->count();
    }

    /**
     * Check if the invoice is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date || in_array($this->status, ['paid', 'cancelled'])) {
            return false;
        }
        
        return Carbon::parse($this->due_date)->isPast() && !in_array($this->payment_status, ['paid']);
    }

    /**
     * Get the days until due date or days overdue.
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date) {
            return null;
        }
        
        return Carbon::now()->diffInDays(Carbon::parse($this->due_date), false);
    }

    /**
     * Get the remaining balance.
     */
    public function getRemainingBalanceAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Check if the invoice can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft']);
    }

    /**
     * Check if the invoice can be sent.
     */
    public function canBeSent(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the invoice can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['paid', 'cancelled']);
    }

    /**
     * Check if payment can be recorded.
     */
    public function canRecordPayment(): bool
    {
        return !in_array($this->status, ['cancelled', 'paid']) && $this->remaining_balance > 0;
    }

    /**
     * Get status badge class for UI.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'sent' => 'bg-blue-100 text-blue-800',
            'paid' => 'bg-green-100 text-green-800',
            'overdue' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-red-100 text-red-800',
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
     * Calculate and update invoice totals based on items.
     */
    public function calculateTotals(): void
    {
        $this->load('items');
        
        $this->subtotal = $this->items->sum('line_total');
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();
    }

    /**
     * Record a payment for this invoice.
     */
    public function recordPayment(float $amount, string $method = 'cash', ?string $notes = null): bool
    {
        if (!$this->canRecordPayment()) {
            return false;
        }

        $this->paid_amount += $amount;
        $this->payment_method = $method;

        if ($this->paid_amount >= $this->total_amount) {
            $this->payment_status = 'paid';
            $this->status = 'paid';
            $this->payment_date = Carbon::now();
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'partial';
        }

        return $this->save();
    }

    /**
     * Mark invoice as sent.
     */
    public function markAsSent(): bool
    {
        if (!$this->canBeSent()) {
            return false;
        }

        $this->status = 'sent';
        return $this->save();
    }

    /**
     * Check and update overdue status.
     */
    public function updateOverdueStatus(): bool
    {
        if ($this->is_overdue && $this->status === 'sent') {
            $this->status = 'overdue';
            return $this->save();
        }

        return false;
    }

    /**
     * Get recent invoices.
     */
    public static function recent(int $limit = 10)
    {
        return static::with(['customer', 'items'])
                     ->latest('created_at')
                     ->limit($limit)
                     ->get();
    }

    /**
     * Get overdue invoices.
     */
    public static function getOverdue()
    {
        return static::with(['customer'])
                     ->whereNotNull('due_date')
                     ->where('due_date', '<', Carbon::today())
                     ->whereNotIn('status', ['paid', 'cancelled'])
                     ->whereNotIn('payment_status', ['paid'])
                     ->get();
    }

    /**
     * Get invoices requiring attention (overdue, pending approval, etc.).
     */
    public static function requiresAttention()
    {
        return static::with(['customer'])
                     ->where(function ($query) {
                         $query->where('status', 'draft')
                               ->orWhere(function ($q) {
                                   $q->whereNotNull('due_date')
                                     ->where('due_date', '<', Carbon::today())
                                     ->whereNotIn('status', ['paid', 'cancelled'])
                                     ->whereNotIn('payment_status', ['paid']);
                               });
                     })
                     ->get();
    }
}
