<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTermsAcceptance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'terms_id',
        'version_accepted',
        'accepted_at',
        'ip_address',
        'user_agent',
        'acceptance_metadata',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'acceptance_metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who accepted the terms.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the terms that was accepted.
     */
    public function terms()
    {
        return $this->belongsTo(TermsAndConditions::class, 'terms_id');
    }

    /**
     * Scope to get acceptances by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get acceptances by terms.
     */
    public function scopeByTerms($query, $termsId)
    {
        return $query->where('terms_id', $termsId);
    }

    /**
     * Scope to get recent acceptances.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('accepted_at', '>=', now()->subDays($days));
    }
}
