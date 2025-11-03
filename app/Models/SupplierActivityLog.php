<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierActivityLog extends Model
{
    protected $fillable = [
        'supplier_id',
        'activity_type',
        'description',
        'related_id',
        'related_type',
        'amount',
        'metadata',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the supplier that owns the activity log.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get the user who performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model (polymorphic).
     */
    public function related()
    {
        if ($this->related_type && $this->related_id) {
            $class = "App\\Models\\{$this->related_type}";
            if (class_exists($class)) {
                return $class::find($this->related_id);
            }
        }
        return null;
    }

    /**
     * Create an activity log entry.
     */
    public static function log(
        int $supplierId,
        string $activityType,
        string $description,
        ?int $relatedId = null,
        ?string $relatedType = null,
        ?float $amount = null,
        ?array $metadata = null,
        ?int $userId = null
    ): self {
        return self::create([
            'supplier_id' => $supplierId,
            'activity_type' => $activityType,
            'description' => $description,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'amount' => $amount,
            'metadata' => $metadata,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

    /**
     * Get badge class for activity type.
     */
    public function getActivityBadgeClassAttribute(): string
    {
        return match($this->activity_type) {
            'purchase_order_created' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'purchase_order_approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'purchase_order_received' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
            'purchase_order_cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            'payment_made' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
            'purchase_return' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
            'supplier_updated' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }

    /**
     * Get icon for activity type.
     */
    public function getActivityIconAttribute(): string
    {
        return match($this->activity_type) {
            'purchase_order_created' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
            'purchase_order_approved' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'purchase_order_received' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
            'purchase_order_cancelled' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            'payment_made' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
            'purchase_return' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>',
            'supplier_updated' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
            default => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        };
    }
}
