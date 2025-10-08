<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        ];
    }


        /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->user_role === 'admin';
    }

        public function isClient()
    {
        return $this->user_role === 'client';   
    }
}
