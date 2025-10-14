<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
     protected $fillable = [
        'brand_code',
        'name',
        'description',
        'logo',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that have default values.
     *
     * @var array
     */
    protected $attributes = [
        'is_active' => true,
    ];

    // Relationship: one brand has many products (matched by brand name)
    public function products()
    {
        return $this->hasMany(Product::class, 'product_brand', 'name');
    }
}