<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Traits\ChecksForAvailableSeats;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckAvailableSeatsRequest;
use App\Http\Resources\SeatResource;
use App\Models\Booking;
use App\Models\Seat;
use App\Models\Station;
use App\Models\Trip;
use App\Models\TripStation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;

#[Group("Seat Management", "APIs for managing seats")]
class SeatController extends Controller
{
    use ChecksForAvailableSeats;

    /**
     * Get Available Seats
     *
     * This endpoint allows you to get all available seats for a trip.
     */
    #[Authenticated]
    public function available(CheckAvailableSeatsRequest $request): JsonResponse
    {
        $startStation = Station::find($request->input('start_station'));
        $endStation = Station::find($request->input('end_station'));

        $tripStations = TripStation::with('trip')
            ->tripSegments($startStation->id, $endStation->id)
            ->get();

        foreach ($tripStations as $tripSegment) {
            $availableSeats = $this->getAvailableSeats($startStation->id, $endStation->id, $tripSegment->trip_id);

            if ($availableSeats->isNotEmpty()) {
                return response()->json([
                    'data' => [
                        'trip' => $startStation->name . ' to ' . $endStation->name,
                        'available_seats' => SeatResource::collection($availableSeats)->resolve(),
                    ],
                ], Response::HTTP_OK);
            }
        }

        return response()->json([
            'error' => 'No available seats'
        ], Response::HTTP_NOT_FOUND);
    }
}
