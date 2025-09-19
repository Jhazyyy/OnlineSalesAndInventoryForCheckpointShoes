<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class InventoryAlert extends Model
{
    protected $fillable = [
        'product_id',
        'alert_type',
        'severity',
        'message',
        'alert_data',
        'status',
        'acknowledged_by',
        'acknowledged_at',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
        'auto_dismiss',
        'expires_at'
    ];

    protected $casts = [
        'alert_data' => 'array',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
        'expires_at' => 'datetime',
        'auto_dismiss' => 'boolean'
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeUnacknowledged(Builder $query): Builder
    {
        return $query->where('status', 'active')
                    ->whereNull('acknowledged_at');
    }

    public function scopeBySeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
    }

    public function scopeCritical(Builder $query): Builder
    {
        return $query->whereIn('severity', ['critical', 'urgent']);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    // Helper methods
    public function acknowledge(User $user, string $notes = null): bool
    {
        return $this->update([
            'status' => 'acknowledged',
            'acknowledged_by' => $user->id,
            'acknowledged_at' => now(),
            'resolution_notes' => $notes
        ]);
    }

    public function resolve(User $user, string $notes = null): bool
    {
        return $this->update([
            'status' => 'resolved',
            'resolved_by' => $user->id,
            'resolved_at' => now(),
            'resolution_notes' => $notes
        ]);
    }

    public function dismiss(): bool
    {
        return $this->update(['status' => 'dismissed']);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getAgeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'info' => 'blue',
            'warning' => 'yellow',
            'critical' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    public function getAlertTypeDisplayAttribute(): string
    {
        return match($this->alert_type) {
            'low_stock' => 'Low Stock',
            'critical_stock' => 'Critical Stock',
            'overstock' => 'Overstock',
            'out_of_stock' => 'Out of Stock',
            'reorder_needed' => 'Reorder Needed',
            default => ucfirst(str_replace('_', ' ', $this->alert_type))
        };
    }
}
