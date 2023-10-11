<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Seat;
use App\Models\Trip;
use App\Models\TripStation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validated();

        $seat = Seat::with('bus.trip')->findOrFail($validatedData['seat_id']);
        if (! $this->isValidSeatForTrip($seat, $validatedData)) {
            return response()->json([
                'error' => 'Invalid stations for the chosen seat.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $booking = $this->placeBooking($validatedData);

        return response()->json([
            'message' => 'Booking successful!',
            'booking' => $booking
        ], Response::HTTP_CREATED);
    }

    protected function isValidSeatForTrip(Seat $seat, array $validatedData): bool
    {
        $trip = $seat->bus?->trip;
        // Early return if the seat is not assigned to a trip
        if (! $trip) {
            return false;
        }

        $startOrder = TripStation::where('trip_id', $trip->id)
            ->where('station_id', $validatedData['start_station'])->first()?->order;
        $endOrder = TripStation::where('trip_id', $trip->id)
            ->where('station_id', $validatedData['end_station'])->first()?->order;

        return $startOrder && $endOrder && $startOrder < $endOrder;
    }

    private function placeBooking(array $validatedData): bool
    {
        try {
            DB::beginTransaction();

            $booking = Booking::create([
                'user_id' => Auth::guard('api')->id(),
                'seat_id' => $validatedData['seat_id'],
                'start_station_id' => $validatedData['start_station'],
                'end_station_id' => $validatedData['end_station'],
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('An error occurred while placing a Booking.', [
                'exception' => $e,
                'booking' => $validatedData,
            ]);
        }

        return $booking ?? false;
    }
}

