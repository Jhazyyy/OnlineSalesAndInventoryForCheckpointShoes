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
        'role',
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
        return $this->role === 'admin';
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
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
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'suspended' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get role badge color
     */
    public function getRoleColorAttribute(): string
    {
        return match($this->role) {
            'admin' => 'purple',
            'manager' => 'blue',
            'user' => 'gray',
            'viewer' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Scope active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
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
     * Check if user is client
     */
    public function isClient(): bool
    {
        return $this->role === 'client';   
    }
}
