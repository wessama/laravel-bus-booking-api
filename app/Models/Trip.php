<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends BaseModel
{
    use HasFactory;

    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class);
    }

    public function tripStations(): HasMany
    {
        return $this->hasMany(TripStation::class);
    }
}
