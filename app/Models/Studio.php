<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Studio extends Model
{
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
}