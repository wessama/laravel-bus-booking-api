<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        return response()->json(['success' => true]);
    }

    public function index()
    {
        return response()->json([]);
    }
}
