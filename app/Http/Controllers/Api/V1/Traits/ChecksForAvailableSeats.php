<?php

namespace App\Http\Controllers\Api\V1\Traits;

use App\Models\Seat;
use App\Models\TripStation;
use Illuminate\Support\Collection;

trait ChecksForAvailableSeats
{
    public function getAvailableSeats($startStationId, $endStationId, $tripId): Collection
    {
        $startOrder = TripStation::intermediaryTrip($startStationId, $tripId)->first()?->order;
        $endOrder = TripStation::intermediaryTrip($endStationId, $tripId)->first()?->order;

        return Seat::forTrip($tripId)
            ->availableBetweenTripSegments($startOrder, $endOrder)
            ->get();
    }
}
