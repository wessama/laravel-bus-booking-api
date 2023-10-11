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

    public function definition(): array
    {
        return [
            'bus_id' => Bus::factory(),
            'seat_number' => $this->faker->unique()->numberBetween(1, 12),
        ];
    }
}
