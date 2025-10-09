<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PurchasePayment extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'payment_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'supplier_id',
        'purchase_order_id',
        'payment_number',
        'amount',
        'unused_amount',
        'payment_date',
        'payment_method',
        'payment_mode',
        'bank_account',
        'reference_number',
        'bank_charges',
        'status',
        'notes',
        'paid_by',
        'bill_number',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'unused_amount' => 'decimal:2',
        'bank_charges' => 'decimal:2',
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method to generate payment number.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = static::generatePaymentNumber();
            }

            // Set unused amount to the full amount initially
            if (is_null($payment->unused_amount)) {
                $payment->unused_amount = $payment->amount;
            }
        });
    }

    /**
     * Generate a unique payment number.
     */
    public static function generatePaymentNumber(): string
    {
        $lastPayment = static::orderBy('payment_id', 'desc')->first();

        if ($lastPayment && preg_match('/^(\d+)$/', $lastPayment->payment_number, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = mt_rand(10000, 99999); // Start with a random 5-digit number
        }

        return (string) $sequence;
    }

    /**
     * Get the supplier that owns the payment.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get the purchase order associated with the payment.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'order_id');
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to filter payments by date range.
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter payments by payment method.
     */
    public function scopeByMethod(Builder $query, string $method): Builder
    {
        return $query->where('payment_method', $method);
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    /**
     * Scope a query to search payments.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('payment_number', 'LIKE', "%{$search}%")
                ->orWhere('reference_number', 'LIKE', "%{$search}%")
                ->orWhere('bill_number', 'LIKE', "%{$search}%")
                ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                    $supplierQuery->where('supplier_name', 'LIKE', "%{$search}%")
                        ->orWhere('contact_person', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
        });
    }

    /**
     * Get the status badge class for styling.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the payment method display name.
     */
    public function getPaymentMethodDisplayAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Credit/Debit Card',
            'bank_transfer' => 'Bank Transfer',
            'check' => 'Check',
            'online' => 'Online Payment',
            'other' => 'Other',
            default => ucfirst($this->payment_method),
        };
    }

    /**
     * Check if the payment can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['pending']);
    }

    /**
     * Check if the payment can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending']);
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted(): bool
    {
        $this->status = 'completed';
        return $this->save();
    }

    /**
     * Mark payment as cancelled.
     */
    public function markAsCancelled(): bool
    {
        $this->status = 'cancelled';
        $this->attributes['unused_amount'] = '0.00';
        return $this->save();
    }

    /**
     * Calculate the used amount.
     */
    public function getUsedAmountAttribute(): float
    {
        return $this->amount - $this->unused_amount;
    }

    /**
     * Apply payment to bills.
     */
    public function applyToBill($billAmount): float
    {
        $currentUnused = (float)$this->unused_amount;
        $amountToApply = min($currentUnused, (float)$billAmount);
        $newUnused = $currentUnused - $amountToApply;
        
        // Update with proper decimal handling
        $this->attributes['unused_amount'] = number_format($newUnused, 2, '.', '');
        $this->save();

        return $amountToApply;
    }

    /**
     * Get the payment mode display name.
     */
    public function getPaymentModeDisplayAttribute(): string
    {
        return match ($this->payment_mode) {
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'standard_chartered' => 'Standard Chartered Bank',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $this->payment_mode)),
        };
    }

}
