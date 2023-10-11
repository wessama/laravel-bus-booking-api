<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreBookingRequest;
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
    public function store(StoreBookingRequest $request)
    {
        $validatedData = $request->validated();

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

            return response()->json([
                'message' => 'An error occurred while placing a Booking.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Booking successful!',
            'booking' => $booking
        ], Response::HTTP_CREATED);
    }
}

