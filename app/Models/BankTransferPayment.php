<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankTransferPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'bank_name',
        'reference_no',
        'amount',
        'proof',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Payment belongs to a Sales Order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'order_id', 'order_id');
    }

    /**
     * Relationship: Payment reviewed by admin user
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope: Get only pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get only confirmed payments
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope: Get only cancelled payments
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if payment is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status display text
     */
    public function getStatusDisplayAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Get proof image URL
     */
    public function getProofUrlAttribute(): ?string
    {
        if (!$this->proof) {
            return null;
        }
        
        return asset('storage/' . $this->proof);
    }
}
