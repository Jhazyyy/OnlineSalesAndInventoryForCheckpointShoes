<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'level',
        'link',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): void
    {
        if (!is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
        }
    }

    /**
     * Determine if the notification has been read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Determine if the notification has not been read.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope a query to filter by notification type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by notification level.
     */
    public function scopeOfLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope a query to only include notifications for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the icon class based on the notification level.
     */
    public function getIconClass(): string
    {
        return match ($this->level) {
            'success' => 'text-green-600 dark:text-green-400',
            'warning' => 'text-yellow-600 dark:text-yellow-400',
            'danger' => 'text-red-600 dark:text-red-400',
            default => 'text-blue-600 dark:text-blue-400',
        };
    }

    /**
     * Get the background class based on the notification level.
     */
    public function getBackgroundClass(): string
    {
        return match ($this->level) {
            'success' => 'bg-green-100 dark:bg-green-900',
            'warning' => 'bg-yellow-100 dark:bg-yellow-900',
            'danger' => 'bg-red-100 dark:bg-red-900',
            default => 'bg-blue-100 dark:bg-blue-900',
        };
    }

    /**
     * Get the border class based on the notification level.
     */
    public function getBorderClass(): string
    {
        return match ($this->level) {
            'success' => 'border-green-500',
            'warning' => 'border-yellow-500',
            'danger' => 'border-red-500',
            default => 'border-blue-500',
        };
    }
}
