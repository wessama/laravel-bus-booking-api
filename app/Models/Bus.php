<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bus extends BaseModel
{
    use HasFactory;

    public const SEATS_PER_BUS = 12;

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function seats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Seat::class);
    }
}
