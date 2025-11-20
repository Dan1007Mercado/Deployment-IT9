<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Hotel Admin',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Reception Staff', 
            'email' => 'reception@hotel.com',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
        ]);

        User::create([
            'name' => 'Hotel Staff',
            'email' => 'staff@hotel.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        $this->command->info('Users seeded successfully!');
    }
}