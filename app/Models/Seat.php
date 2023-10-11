<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Seat extends BaseModel
{
    use HasFactory;

    public function scopeForTrip($query, $tripId)
    {
        return $query->whereHas('bus', function ($query) use ($tripId) {
            $query->where('trip_id', $tripId);
        });
    }

    public function scopeAvailableBetweenOrders($query, $startOrder, $endOrder)
    {
        return $query->whereDoesntHave('bookings', function ($query) use ($startOrder, $endOrder) {
            $query->whereExists(function ($query) use ($startOrder, $endOrder) {
                $query->select(DB::raw(1))
                    ->from('trip_stations')
                    ->whereColumn('start_station_id', 'trip_stations.station_id')
                    ->whereBetween('order', [$startOrder, $endOrder]);
            })->orWhereExists(function ($query) use ($startOrder, $endOrder) {
                $query->select(DB::raw(1))
                    ->from('trip_stations')
                    ->whereColumn('end_station_id', 'trip_stations.station_id')
                    ->whereBetween('order', [$startOrder, $endOrder]);
            });
        });
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
