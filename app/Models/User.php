<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'username',
        'password',
        'profile_photo',
        'status',
        'last_login_at',
        'login_count',
        'is_active',
        // Note: 'role' removed - now managed by Spatie Permission package
        'bio',
        'department',
        'position',
    ];

    /**
     * The attributes that should be appended to model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * Get the user's full name.
     * Returns "Test User" if both first and last names are empty
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        if (empty($this->first_name) && empty($this->last_name)) {
            return 'Test User';
        }
        
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')) ?: 'Test User';
    }

    /**
     * Get the name attribute.
     * This overrides the database 'name' field to always return the computed full name
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        // If both first and last names are empty, return "Test User"
        if (empty($this->attributes['first_name']) && empty($this->attributes['last_name'])) {
            return 'Test User';
        }
        
        // Otherwise, combine first and last name
        $firstName = $this->attributes['first_name'] ?? '';
        $lastName = $this->attributes['last_name'] ?? '';
        $fullName = trim($firstName . ' ' . $lastName);
        
        return $fullName ?: 'Test User';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a regular user
     */
    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    /**
     * Check if user is active
     */
    public function isActiveUser(): bool
    {
        return $this->is_active && $this->status === 'active';
    }

    /**
     * Get status badge color
     * Considers both is_active and status fields
     */
    public function getStatusColorAttribute(): string
    {
        // If suspended, always show red regardless of is_active
        if ($this->status === 'suspended') {
            return 'red';
        }
        
        // If is_active is false or status is inactive, show gray
        if (!$this->is_active || $this->status === 'inactive') {
            return 'gray';
        }
        
        // If is_active is true and status is active, show green
        if ($this->is_active && $this->status === 'active') {
            return 'green';
        }
        
        return 'gray';
    }

    /**
     * Get role badge color
     * Only supports 'admin' and 'user' roles
     */
    public function getRoleColorAttribute(): string
    {
        if ($this->hasRole('admin')) {
            return 'purple';
        }
        
        if ($this->hasRole('user')) {
            return 'blue';
        }
        
        return 'gray';
    }

    /**
     * Get the user's primary role name
     */
    public function getPrimaryRoleAttribute(): string
    {
        $role = $this->roles->first();
        return $role ? $role->name : 'user';
    }

    /**
     * Scope active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope by role using Spatie's role system
     */
    public function scopeByRole($query, $role)
    {
        return $query->role($role);
    }

    /**
     * Get the user's notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the user's unread notifications
     */
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->whereNull('read_at');
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Search users
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Record user login
     */
    public function recordLogin()
    {
        $this->update([
            'last_login_at' => now(),
            'login_count' => $this->login_count + 1,
        ]);
    }

    /**
     * Assign default user role after creation
     */
    protected static function booted(): void
    {
        static::created(function (User $user) {
            // Only assign default role if user has no roles
            if ($user->roles->isEmpty()) {
                $user->assignRole('user');
            }
        });
    }
}
