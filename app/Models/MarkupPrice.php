<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarkupPrice extends Model
{
    protected $fillable = [
        'name',
        'description',
        'markup_percentage',
        'is_active',
    ];

    protected $casts = [
        'markup_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Calculate selling price based on cost and markup
     */
    public function calculatePrice($cost)
    {
        return $cost * (1 + ($this->markup_percentage / 100));
    }
}
