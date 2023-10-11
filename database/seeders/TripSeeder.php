<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Station;
use App\Models\Trip;
use App\Models\TripStation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stations = Station::all();

        Trip::factory(5)->create()->each(function ($trip) use ($stations) {
            $selectedStations = $stations->random(4)->pluck('id');
            foreach ($selectedStations as $order => $station_id) {
                TripStation::create([
                    'trip_id' => $trip->id,
                    'station_id' => $station_id,
                    'order' => $order + 1,
                ]);
            }

            $bus = Bus::all()->random(1)->first();
            if ($bus->doesntHave('trip')) {
                $bus->trip()->associate($trip)->save();
            }
        });
    }
}
