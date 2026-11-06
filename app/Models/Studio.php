<?php

namespace App\Models;

use Database\Factories\StudioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Studio extends Model
{
    /** @use HasFactory<StudioFactory> */
    use HasFactory;

    protected $fillable = [
        'photographer_id',
        'studio_name',
        'description',
        'location',
        'contact_email',
        'contact_phone',
    ];

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }

    /**
     * Get all packages offered by this studio.
     */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class, 'studio_id');
    }

    /**
     * Get all bookings associated with this studio.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'studio_id');
    }
}
