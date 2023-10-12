<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends BaseModel
{
    use HasFactory;

    public function scopeForTrip($query, $tripId)
    {
        return $query->whereHas('bus', function ($query) use ($tripId) {
            $query->where('trip_id', $tripId);
        });
    }

    public function scopeAvailableBetweenTripSegments($query, $startOrder, $endOrder)
    {
        return $query->whereDoesntHave('bookings', function ($query) use ($startOrder, $endOrder) {
            $query->whereHas('seat.bus.trip', function ($tripQuery) use ($startOrder, $endOrder) {
                $tripQuery->whereHas('tripStations', function ($innerQuery) use ($endOrder) {
                    $innerQuery->where('order', '<', $endOrder)
                        ->whereColumn('trip_stations.station_id', 'bookings.start_station_id');
                })->whereHas('tripStations', function ($innerQuery) use ($startOrder) {
                    $innerQuery->where('order', '>', $startOrder)
                        ->whereColumn('trip_stations.station_id', 'bookings.end_station_id');
                });
            });
        });
    }

    public function bus() : BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function bookings() : HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
