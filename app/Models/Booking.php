<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'photographer_id',
        'studio_id',
        'package_id',
        'package_price',
        'event_date',
        'event_location',
        'special_requirements',
        'booking_date',
        'booking_status',
        'event_status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'package_price' => 'decimal:2',
            'event_date' => 'date:Y-m-d',
            'booking_date' => 'date:Y-m-d',
        ];
    }

    /**
     * Get the client that placed the booking.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the photographer providing the service.
     */
    public function photographer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }

    /**
     * Get the studio associated with the booking, if any.
     */
    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class, 'studio_id');
    }

    /**
     * Get the package selected for the booking.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    /**
     * Get the invoice associated with the booking.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'booking_id');
    }

    /**
     * Scope for pending booking requests.
     */
    public function scopePending(Builder $query): void
    {
        $query->where('booking_status', 'pending');
    }

    /**
     * Scope for accepted bookings.
     */
    public function scopeAccepted(Builder $query): void
    {
        $query->where('booking_status', 'accepted');
    }
}

