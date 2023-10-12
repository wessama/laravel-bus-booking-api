<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seat>
 */
class SeatFactory extends Factory
{
    protected $model = Seat::class;

    public function definition() : array
    {
        return [
            //
        ];
    }

    public function withBus(Bus $bus, int $seatNumber) : SeatFactory
    {
        return $this->state(function () use ($bus, $seatNumber) {
            return [
                'bus_id' => $bus->id,
                'seat_number' => $seatNumber,
            ];
        });
    }
}
