<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * @hideFromAPIDocumentation
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::prefix('v1')->name('api.booking.')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/trips', [\App\Http\Controllers\Api\V1\TripController::class, 'index']);
    Route::post('/seats/available', [\App\Http\Controllers\Api\V1\SeatController::class, 'available'])->name('check');
    Route::post('/bookings', [\App\Http\Controllers\Api\V1\BookingController::class, 'store'])->name('store');
});
