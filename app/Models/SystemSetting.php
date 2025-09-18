<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'category',
        'key',
        'value',
        'data_type',
        'description',
        'is_public',
        'is_active'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Accessor for value - automatically decode JSON values
    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->decodeValue($value),
            set: fn ($value) => $this->encodeValue($value)
        );
    }

    /**
     * Decode value based on data type
     */
    private function decodeValue($value)
    {
        if (is_null($value)) {
            return null;
        }

        return match ($this->data_type) {
            'json' => json_decode($value, true),
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (float) $value : $value,
            'integer' => is_numeric($value) ? (int) $value : $value,
            default => $value
        };
    }

    /**
     * Encode value based on data type
     */
    private function encodeValue($value)
    {
        return match ($this->data_type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value
        };
    }

    /**
     * Scope to get settings by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get public settings (accessible by non-admin)
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get a setting value by category and key
     */
    public static function getValue($category, $key, $default = null)
    {
        $cacheKey = "setting_{$category}_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($category, $key, $default) {
            $setting = static::where('category', $category)
                            ->where('key', $key)
                            ->where('is_active', true)
                            ->first();
            
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function setValue($category, $key, $value, $dataType = 'string', $description = null)
    {
        $setting = static::updateOrCreate(
            ['category' => $category, 'key' => $key],
            [
                'value' => $value,
                'data_type' => $dataType,
                'description' => $description,
                'is_active' => true
            ]
        );

        // Clear cache
        Cache::forget("setting_{$category}_{$key}");
        
        return $setting;
    }

    /**
     * Get all settings by category
     */
    public static function getByCategory($category)
    {
        $cacheKey = "settings_category_{$category}";
        
        return Cache::remember($cacheKey, 3600, function () use ($category) {
            return static::byCategory($category)
                        ->active()
                        ->pluck('value', 'key')
                        ->toArray();
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache()
    {
        Cache::flush();
    }

    /**
     * Boot method to clear cache on model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Cache::forget("setting_{$model->category}_{$model->key}");
            Cache::forget("settings_category_{$model->category}");
        });

        static::deleted(function ($model) {
            Cache::forget("setting_{$model->category}_{$model->key}");
            Cache::forget("settings_category_{$model->category}");
        });
    }
}
