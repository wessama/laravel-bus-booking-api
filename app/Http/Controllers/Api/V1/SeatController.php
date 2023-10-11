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
        $availableSeats = [];

        $startOrder = TripStation::intermediaryTrip($startStationId, $tripId)->first()?->order;
        $endOrder = TripStation::intermediaryTrip($endStationId, $tripId)->first()?->order;

        // Retrieve all seats associated with the trip
        $seats = Seat::whereHas('bus', function ($query) use ($tripId) {
            $query->where('trip_id', $tripId);
        })->get();

        foreach ($seats as $seat) {
            $isAvailable = true;

            // Retrieve all bookings associated with each seat
            $bookings = Booking::where('seat_id', $seat->id)->get();

            foreach ($bookings as $booking) {
                // Get order of booked stations
                $bookedStartOrder = TripStation::intermediaryTrip($booking->start_station_id, $tripId)->first()?->order;
                $bookedEndOrder = TripStation::intermediaryTrip($booking->end_station_id, $tripId)->first()?->order;

                // Check if there is any overlapping booking
                if (! ($bookedEndOrder <= $startOrder || $bookedStartOrder >= $endOrder)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $availableSeats[] = $seat->id;
            }
        }

        return collect($availableSeats);
    }
}
