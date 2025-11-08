<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numberBetween(100, 999),
            'type' => $this->faker->randomElement(['Single', 'Double', 'Suite']),
            'status' => $this->faker->randomElement(['available', 'occupied', 'maintenance']),
            'price' => $this->faker->randomFloat(2, 50, 500),
        ];
    }
}
