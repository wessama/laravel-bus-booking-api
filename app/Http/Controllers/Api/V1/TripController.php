<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;

#[Group("Trip Management", "APIs for managing trips")]
class TripController extends Controller
{
    /**
     * Get All Trips
     *
     * This endpoint allows you to get all trips.
     */
    #[Authenticated]
    public function index()
    {
        $trips = Trip::with(['tripStations.station'])->get();

        return response()->json([
            'data' => TripResource::collection($trips),
        ], Response::HTTP_OK);
    }
}
