<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request) : array
    {
        return [
            'user' => new UserResource($this->user),
            'seat' => $this->seat_id,
            'start_station' => $this->start_station_id,
            'end_station' => $this->end_station_id,
        ];
    }
}
