<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    public function available(Request $request)
    {
        return response()->json(['seat_id' => 1]);
    }
}
