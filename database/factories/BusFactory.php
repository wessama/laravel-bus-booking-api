<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bus>
 */
class BusFactory extends Factory
{
    protected $model = Bus::class;

    public function definition() : array
    {
        return [
            // 'trip_id' => Trip::factory(),
        ];
    }
}
