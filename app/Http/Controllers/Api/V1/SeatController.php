<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Traits\ChecksForAvailableSeats;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckAvailableSeatsRequest;
use App\Models\Booking;
use App\Models\Seat;
use App\Models\Trip;
use App\Models\TripStation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SeatController extends Controller
{
    use ChecksForAvailableSeats;

    public function available(CheckAvailableSeatsRequest $request): JsonResponse
    {
        $startStation = $request->input('start_station');
        $endStation = $request->input('end_station');

        $tripStations = TripStation::tripSegments($startStation, $endStation)->get();
        foreach ($tripStations as $tripSegment) {
            $availableSeats = $this->getAvailableSeats($startStation, $endStation, $tripSegment->trip_id);

            if ($availableSeats->isNotEmpty()) {
                return response()->json([
                    'available_seats' => $availableSeats,
                    'trip_id' => $tripSegment->trip_id,
                ], Response::HTTP_OK);
            }
        }

        return response()->json([
            'error' => 'No available seats'
        ], Response::HTTP_NOT_FOUND);
    }
}
