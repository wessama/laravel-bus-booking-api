<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Seat;
use App\Models\Trip;
use App\Models\TripStation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SeatController extends Controller
{
    public function available(Request $request)
    {
        $startStation = $request->input('start_station');
        $endStation = $request->input('end_station');

        $trips = TripStation::tripsBetweenStations($startStation, $endStation)->get();
        foreach ($trips as $trip) {
            $availableSeats = $this->getAvailableSeats($startStation, $endStation, $trip);
            if ($availableSeats->isNotEmpty()) {
                return response()->json([
                    'available_seats' => $availableSeats,
                    'trip_id' => $trip->id
                ], Response::HTTP_OK);
            }
        }

        return response()->json([
            'error' => 'No available seats'
        ], Response::HTTP_NOT_FOUND);
    }

    private function getAvailableSeats($startStationId, $endStationId, $tripId): Collection
    {
        $startOrder = TripStation::intermediaryTrip($startStationId, $tripId)->first()?->order;
        $endOrder = TripStation::intermediaryTrip($endStationId, $tripId)->first()?->order;

        return Seat::forTrip($tripId)
            ->availableBetweenOrders($startOrder, $endOrder)
            ->get();
    }

}
