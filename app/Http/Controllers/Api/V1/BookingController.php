<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;

#[Group('Booking Management', 'APIs for managing bookings')]
class BookingController extends Controller
{
    /**
     * Place a Booking
     *
     * This endpoint allows you to place a booking.
     */
    #[Authenticated]
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

        return new BookingResource($booking);
    }
}
