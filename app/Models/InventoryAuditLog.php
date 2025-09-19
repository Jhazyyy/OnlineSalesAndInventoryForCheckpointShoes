<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class InventoryAuditLog extends Model
{
    protected $fillable = [
        'product_id',
        'action_type',
        'description',
        'old_values',
        'new_values',
        'old_quantity',
        'new_quantity',
        'quantity_change',
        'user_id',
        'user_name',
        'ip_address',
        'user_agent',
        'source',
        'related_type',
        'related_id',
        'batch_id'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    // Static method to create audit log entry
    public static function logAction(
        Product $product,
        string $actionType,
        string $description,
        array $oldValues = null,
        array $newValues = null,
        ?User $user = null,
        $related = null,
        string $source = 'Manual'
    ): self {
        $log = new static([
            'product_id' => $product->getKey(),
            'action_type' => $actionType,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'source' => $source,
            'batch_id' => Str::uuid()
        ]);

        if ($related) {
            $log->related()->associate($related);
        }

        // Calculate quantity changes if both old and new quantities are provided
        if (isset($oldValues['quantity']) && isset($newValues['quantity'])) {
            $log->old_quantity = $oldValues['quantity'];
            $log->new_quantity = $newValues['quantity'];
            $log->quantity_change = $newValues['quantity'] - $oldValues['quantity'];
        }

        $log->save();
        
        return $log;
    }
}
