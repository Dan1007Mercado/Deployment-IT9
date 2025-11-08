<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $guest = Guest::inRandomOrder()->first() ?? Guest::factory()->create();
        $room = Room::inRandomOrder()->first() ?? Room::factory()->create();
        $checkIn = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $checkOut = (clone $checkIn)->modify('+'.rand(1,7).' days');
        return [
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'status' => $this->faker->randomElement(['booked', 'checked_in', 'cancelled']),
        ];
    }
}
