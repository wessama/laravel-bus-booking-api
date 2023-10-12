<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Trip extends BaseModel
{
    use HasFactory;

    public function bus() : HasOne
    {
        return $this->hasOne(Bus::class);
    }

    public function tripStations() : HasMany
    {
        return $this->hasMany(TripStation::class);
    }
}
