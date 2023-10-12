<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TripController extends Controller
{
    public function index()
    {
        $trips = Trip::with(['tripStations.station'])->get();

        return response()->json([
            'data' => TripResource::collection($trips),
        ], Response::HTTP_OK);
    }
}
