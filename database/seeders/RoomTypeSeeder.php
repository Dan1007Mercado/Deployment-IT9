<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            [
                'type_name' => 'Deluxe',
                'description' => 'Spacious room with premium amenities',
                'capacity' => 2,
                'base_price' => 4000.00,
                'amenities' => 'AC, WiFi, TV, Mini Bar, King Bed',
                'total_rooms' => 10
            ],
            [
                'type_name' => 'Standard',
                'description' => 'Comfortable room with basic amenities',
                'capacity' => 2,
                'base_price' => 2500.00,
                'amenities' => 'AC, WiFi, TV, Queen Bed',
                'total_rooms' => 15
            ],
            [
                'type_name' => 'Suite',
                'description' => 'Luxurious suite with separate living area',
                'capacity' => 4,
                'base_price' => 7500.00,
                'amenities' => 'AC, WiFi, TV, Mini Bar, King Bed, Living Room, Jacuzzi',
                'total_rooms' => 5
            ],
            [
                'type_name' => 'Executive',
                'description' => 'Executive room with work desk and premium services',
                'capacity' => 2,
                'base_price' => 5500.00,
                'amenities' => 'AC, WiFi, TV, Mini Bar, King Bed, Work Desk, Coffee Maker',
                'total_rooms' => 8
            ],
            [
                'type_name' => 'Family',
                'description' => 'Large room perfect for families',
                'capacity' => 4,
                'base_price' => 6000.00,
                'amenities' => 'AC, WiFi, TV, Two Queen Beds, Mini Fridge',
                'total_rooms' => 12
            ]
        ];

        foreach ($roomTypes as $roomType) {
            RoomType::create($roomType);
        }

        $this->command->info('Room types seeded successfully!');
    }
}