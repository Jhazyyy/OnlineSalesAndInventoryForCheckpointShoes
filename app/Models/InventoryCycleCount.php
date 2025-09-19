<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class InventoryCycleCount extends Model
{
    protected $fillable = [
        'cycle_count_number',
        'status',
        'count_type',
        'scheduled_date',
        'started_date',
        'completed_date',
        'created_by',
        'assigned_to',
        'description',
        'notes',
        'total_items',
        'counted_items',
        'discrepancies',
        'accuracy_percentage',
        'total_variance_value',
        'positive_variances',
        'negative_variances',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'started_date' => 'date',
        'completed_date' => 'date',
        'accuracy_percentage' => 'decimal:2',
        'total_variance_value' => 'decimal:2'
    ];

    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(InventoryCycleCountItem::class, 'cycle_count_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['planned', 'in_progress']);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }
}
