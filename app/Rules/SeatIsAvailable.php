<?php

namespace App\Rules;

use App\Http\Controllers\Api\V1\Traits\ChecksForAvailableSeats;
use App\Models\TripStation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SeatIsAvailable implements ValidationRule
{
    use ChecksForAvailableSeats;

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
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $tripStations = TripStation::tripSegments($this->startStation, $this->endStation)->get();

        foreach ($tripStations as $tripSegment) {
            $availableSeats = $this->getAvailableSeats($this->startStation, $this->endStation, $tripSegment->trip_id);

            if (! $availableSeats->contains('id', $value)) {
                $fail("The selected seat is not available for the trip with id {$tripSegment->trip_id}");
            }
        }
    }
}
