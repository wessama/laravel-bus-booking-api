<?php

namespace Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Seat;
use App\Models\Station;
use App\Models\Trip;
use App\Models\TripStation;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use WithFaker;

    protected Bus $bus;
    protected User $user;
    protected Seat $seat;
    protected Station$startStation;
    protected Station $endStation;
    protected Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bus = Bus::factory()->create();
        $this->user = User::factory()->create();
        $this->seat = Seat::factory()->for($this->bus)->create([
            'seat_number' => $this->faker->randomNumber()
        ]);
        $this->startStation = Station::factory()->create();
        $this->endStation = Station::factory()->create();
        $this->trip = Trip::factory()->create();

        $this->bus->update(['trip_id' => $this->trip->id]);

        TripStation::factory()->for($this->trip)->create([
            'station_id' => $this->startStation->id,
            'order' => 1
        ]);
        TripStation::factory()->for($this->trip)->create([
            'station_id' => $this->endStation->id,
            'order' => 3
        ]);
    }

    /** @test */
    public function a_user_can_book_a_seat_successfully()
    {
        Sanctum::actingAs($this->user);

        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('api.booking.store'), [
                'seat_id' => $this->seat->id,
                'start_station' => $this->startStation->id,
                'end_station' => $this->endStation->id,
            ]);

        $response->assertStatus(201);
        $this->assertBookingExists($this->user, $this->seat, $this->startStation, $this->endStation);
    }

    /** @test */
    public function cannot_book_a_nonexistent_seat()
    {
        Sanctum::actingAs($this->user);

        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('api.booking.store'), [
                'seat_id' => Seat::latest()->first()?->id + 1,
                'start_station' => $this->startStation->id,
                'end_station' => $this->endStation->id,
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function user_cannot_book_overlapping_seats()
    {
        // Given an existing booking for the user
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'seat_id' => $this->seat->id,
            'start_station_id' => $this->startStation->id,
            'end_station_id' => $this->endStation->id,
        ]);

        // When the user tries to book the same seat with overlapping stations
        $overlappingStartStation = Station::factory()->create();
        TripStation::factory()->for($this->trip)->create([
            'station_id' => $overlappingStartStation->id,
            'order' => 2  // between start and end
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->actingAs($this->user, 'api')
            ->postJson(route('api.booking.store'), [
                'seat_id' => $this->seat->id,
                'start_station' => $overlappingStartStation->id,
                'end_station' => $this->endStation->id,
            ]);

        // Then the booking should be rejected
        $response->assertStatus(422);
    }

    protected function assertBookingExists($user, $seat, $startStation, $endStation): void
    {
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'seat_id' => $seat->id,
            'start_station_id' => $startStation->id,
            'end_station_id' => $endStation->id,
        ]);
    }
}
