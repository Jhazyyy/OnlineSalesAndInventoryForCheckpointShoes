<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockName extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'stock_code',
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get products associated with this stock name.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'stock_name_id');
    }
}
