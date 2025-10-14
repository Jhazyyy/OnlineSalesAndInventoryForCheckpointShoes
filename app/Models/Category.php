<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_code',
        'name',
        'description',
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

    // Relationship: one category has many products (matched by name)
    public function products()
    {
        return $this->hasMany(Product::class, 'product_category', 'name');
    }
}
