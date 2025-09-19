<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCycleCountItem extends Model
{
    protected $fillable = [
        'cycle_count_id',
        'product_id',
        'system_quantity',
        'system_unit_cost',
        'system_total_value',
        'counted_quantity',
        'counted_unit_cost',
        'counted_total_value',
        'quantity_variance',
        'value_variance',
        'variance_percentage',
        'status',
        'variance_type',
        'counted_by',
        'counted_at',
        'verified_by',
        'verified_at',
        'notes',
        'discrepancy_reason',
        'requires_adjustment',
        'adjustment_applied',
        'adjustment_applied_at'
    ];

    protected $casts = [
        'system_unit_cost' => 'decimal:2',
        'system_total_value' => 'decimal:2',
        'counted_unit_cost' => 'decimal:2',
        'counted_total_value' => 'decimal:2',
        'value_variance' => 'decimal:2',
        'variance_percentage' => 'decimal:2',
        'counted_at' => 'datetime',
        'verified_at' => 'datetime',
        'requires_adjustment' => 'boolean',
        'adjustment_applied' => 'boolean',
        'adjustment_applied_at' => 'datetime'
    ];

    public function cycleCount(): BelongsTo
    {
        return $this->belongsTo(InventoryCycleCount::class, 'cycle_count_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
