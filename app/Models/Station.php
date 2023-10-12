<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends BaseModel
{
    use HasFactory;

    public function tripStations() : HasMany
    {
        return $this->hasMany(TripStation::class);
    }
}
