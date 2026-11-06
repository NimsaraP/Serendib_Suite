<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'full_name',
    'email',
    'phone',
    'password',
    'address',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
     * Get all studios belonging to this photographer.
     */
    public function studios(): HasMany
    {
        return $this->hasMany(Studio::class, 'photographer_id');
    }

    /**
     * Get all packages belonging to this photographer.
     */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'photographer_id');
    }

    /**
     * Get all bookings placed by this client.
     */
    public function clientBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    /**
     * Get all bookings managed by this photographer.
     */
    public function photographerBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'photographer_id');
    }
}
