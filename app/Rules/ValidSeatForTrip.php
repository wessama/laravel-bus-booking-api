<?php

namespace App\Rules;

use App\Models\Seat;
use App\Models\TripStation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidSeatForTrip implements ValidationRule
{
    protected ?int $startStation;

    protected ?int $endStation;

    public function __construct(?int $startStation, ?int $endStation)
    {
        $this->startStation = $startStation;
        $this->endStation = $endStation;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail) : void
    {
        $seat = Seat::with('bus.trip')->find($value);

        if (! $seat || ! $seat->bus || ! $seat->bus->trip) {
            $fail('Seat is not valid.');

            return;
        }

        $startOrder = TripStation::where('trip_id', $seat->bus->trip_id)
            ->where('station_id', $this->startStation)->first()?->order;
        $endOrder = TripStation::where('trip_id', $seat->bus->trip_id)
            ->where('station_id', $this->endStation)->first()?->order;

        if (! $startOrder || ! $endOrder || $startOrder >= $endOrder) {
            $fail('Invalid stations for the chosen seat.');
        }
    }
}
