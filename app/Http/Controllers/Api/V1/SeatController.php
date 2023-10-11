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

    private function getAvailableSeats(int $startStationId, int $endStationId, int $tripId): Collection
    {
        $startOrder = TripStation::intermediaryTrip($startStationId, $tripId)->first()?->order;
        $endOrder = TripStation::intermediaryTrip($endStationId, $tripId)->first()?->order;

        // Retrieve seats that are not booked in the specified order range
        return Seat::whereHas('bus', function ($query) use ($tripId) {
            $query->where('trip_id', $tripId);
        })->whereDoesntHave('bookings', function ($query) use ($tripId, $startOrder, $endOrder) {
            $query->whereExists(function ($query) use ($tripId, $startOrder, $endOrder) {
                $query->select(DB::raw(1))
                    ->from('trip_stations')
                    ->whereColumn('start_station_id', 'trip_stations.station_id')
                    ->where('trip_id', $tripId)
                    ->whereBetween('order', [$startOrder, $endOrder]);
            })->orWhereExists(function ($query) use ($tripId, $startOrder, $endOrder) {
                $query->select(DB::raw(1))
                    ->from('trip_stations')
                    ->whereColumn('end_station_id', 'trip_stations.station_id')
                    ->where('trip_id', $tripId)
                    ->whereBetween('order', [$startOrder, $endOrder]);
            });
        })->get();
    }
}
