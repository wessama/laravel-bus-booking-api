<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class TripStation extends BaseModel
{
    public function scopeTripSegments(Builder $query, $startStationId, $endStationId): Builder
    {
        return $query->from('trip_stations as start_station')
            ->join('trip_stations as end_station', function($join) {
                $join->on('start_station.trip_id', '=', 'end_station.trip_id');
            })
            ->where('start_station.station_id', $startStationId)
            ->where('end_station.station_id', $endStationId)
            ->where('start_station.order', '<', DB::raw('end_station.order'))
            ->select('start_station.trip_id')
            ->distinct();
    }

    public function scopeIntermediaryTrip($query, $stationId, $tripId)
    {
        return $query->where('station_id', $stationId)
            ->where('trip_id', $tripId);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
