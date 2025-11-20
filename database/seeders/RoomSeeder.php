<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomType;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $roomTypes = RoomType::all();
        
        $roomData = [
            'Deluxe' => [
                ['room_number' => '101', 'floor' => '1'],
                ['room_number' => '102', 'floor' => '1'],
                ['room_number' => '103', 'floor' => '1'],
                ['room_number' => '104', 'floor' => '1'],
                ['room_number' => '105', 'floor' => '1']
            ],
            'Standard' => [
                ['room_number' => '201', 'floor' => '2'],
                ['room_number' => '202', 'floor' => '2'],
                ['room_number' => '203', 'floor' => '2'],
                ['room_number' => '204', 'floor' => '2'],
                ['room_number' => '205', 'floor' => '2']
            ],
            'Suite' => [
                ['room_number' => '301', 'floor' => '3'],
                ['room_number' => '302', 'floor' => '3'],
                ['room_number' => '303', 'floor' => '3'],
                ['room_number' => '304', 'floor' => '3'],
                ['room_number' => '305', 'floor' => '3']
            ],
            'Executive' => [
                ['room_number' => '401', 'floor' => '4'],
                ['room_number' => '402', 'floor' => '4'],
                ['room_number' => '403', 'floor' => '4'],
                ['room_number' => '404', 'floor' => '4'],
                ['room_number' => '405', 'floor' => '4']
            ],
            'Family' => [
                ['room_number' => '501', 'floor' => '5'],
                ['room_number' => '502', 'floor' => '5'],
                ['room_number' => '503', 'floor' => '5'],
                ['room_number' => '504', 'floor' => '5'],
                ['room_number' => '505', 'floor' => '5']
            ]
        ];

        foreach ($roomTypes as $roomType) {
            if (isset($roomData[$roomType->type_name])) {
                foreach ($roomData[$roomType->type_name] as $roomInfo) {
                    // Check if room already exists
                    $existingRoom = Room::where('room_number', $roomInfo['room_number'])->first();
                    if (!$existingRoom) {
                        Room::create([
                            'room_number' => $roomInfo['room_number'],
                            'room_type_id' => $roomType->room_type_id,
                            'floor' => $roomInfo['floor'],
                            'room_status' => 'available'
                        ]);
                    }
                }
            }
        }

        $this->command->info('Sample rooms seeded successfully!');
        $this->command->info('Total rooms: ' . Room::count());
    }
}