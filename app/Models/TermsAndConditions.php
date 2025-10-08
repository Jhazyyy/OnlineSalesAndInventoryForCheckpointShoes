<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TermsAndConditions extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'content',
        'version',
        'is_active',
        'requires_acceptance',
        'effective_date',
        'created_by',
        'updated_by',
        'change_summary',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_acceptance' => 'boolean',
        'effective_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($terms) {
            if (empty($terms->slug)) {
                $terms->slug = Str::slug($terms->title);
            }
        });
    }

    /**
     * Get the creator of the terms.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the last updater of the terms.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all user acceptances for this terms.
     */
    public function acceptances()
    {
        return $this->hasMany(UserTermsAcceptance::class, 'terms_id');
    }

    /**
     * Check if a user has accepted this terms.
     */
    public function isAcceptedByUser($userId)
    {
        return $this->acceptances()
            ->where('user_id', $userId)
            ->where('version_accepted', $this->version)
            ->exists();
    }

    /**
     * Get acceptance count for this terms.
     */
    public function getAcceptanceCountAttribute()
    {
        return $this->acceptances()->count();
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'terms_of_service' => 'Terms of Service',
            'privacy_policy' => 'Privacy Policy',
            'data_privacy' => 'Data Privacy Agreement',
            'refund_policy' => 'Refund Policy',
            'shipping_policy' => 'Shipping Policy',
            'cookie_policy' => 'Cookie Policy',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    /**
     * Scope to get active terms.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get terms by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get latest version of each type.
     */
    public function scopeLatestVersion($query)
    {
        return $query->whereIn('id', function($query) {
            $query->selectRaw('MAX(id)')
                ->from('terms_and_conditions')
                ->whereNull('deleted_at')
                ->groupBy('type');
        });
    }
}
