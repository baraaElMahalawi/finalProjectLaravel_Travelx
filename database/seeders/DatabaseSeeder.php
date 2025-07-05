<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create sample rooms
        $rooms = [
            [
                'room_number' => '101',
                'room_type' => 'Standard Single',
                'price_per_night' => 150.00,
                'availability' => true,
                'image' => 'room1.jpg',
                'room_view' => 'City View',
                'pool_type' => 'Outdoor Pool',
                'room_stars' => 3,
                'has_parking' => true,
                'has_airport_transfer' => false,
                'has_wifi' => true,
                'has_coffee_maker' => true,
                'has_bar' => false,
                'has_breakfast' => true,
            ],
            [
                'room_number' => '102',
                'room_type' => 'Deluxe Double',
                'price_per_night' => 250.00,
                'availability' => true,
                'image' => 'room2.jpg',
                'room_view' => 'Sea View',
                'pool_type' => 'Indoor Pool',
                'room_stars' => 4,
                'has_parking' => true,
                'has_airport_transfer' => true,
                'has_wifi' => true,
                'has_coffee_maker' => true,
                'has_bar' => true,
                'has_breakfast' => true,
            ],
            [
                'room_number' => '201',
                'room_type' => 'Suite',
                'price_per_night' => 400.00,
                'availability' => true,
                'image' => 'room3.jpg',
                'room_view' => 'Mountain View',
                'pool_type' => 'Private Pool',
                'room_stars' => 5,
                'has_parking' => true,
                'has_airport_transfer' => true,
                'has_wifi' => true,
                'has_coffee_maker' => true,
                'has_bar' => true,
                'has_breakfast' => true,
            ],
            [
                'room_number' => '202',
                'room_type' => 'Standard Double',
                'price_per_night' => 200.00,
                'availability' => true,
                'image' => 'room4.jpg',
                'room_view' => 'Garden View',
                'pool_type' => 'Outdoor Pool',
                'room_stars' => 3,
                'has_parking' => false,
                'has_airport_transfer' => false,
                'has_wifi' => true,
                'has_coffee_maker' => false,
                'has_bar' => false,
                'has_breakfast' => true,
            ],
            [
                'room_number' => '301',
                'room_type' => 'Presidential Suite',
                'price_per_night' => 800.00,
                'availability' => true,
                'image' => 'room5.jpg',
                'room_view' => 'Panoramic View',
                'pool_type' => 'Private Pool',
                'room_stars' => 5,
                'has_parking' => true,
                'has_airport_transfer' => true,
                'has_wifi' => true,
                'has_coffee_maker' => true,
                'has_bar' => true,
                'has_breakfast' => true,
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }

        // Create a test user
        User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}
