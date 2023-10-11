<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Seat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buses = Bus::all();
        foreach ($buses as $bus) {
            for ($seatNumber = 1; $seatNumber <= Bus::SEATS_PER_BUS; $seatNumber++) {
                Seat::factory()->create([
                    'bus_id' => $bus->id,
                    'seat_number' => $seatNumber
                ]);
            }
        }
    }
}
