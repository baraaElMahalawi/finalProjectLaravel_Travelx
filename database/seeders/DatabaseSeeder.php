<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        $faker = Faker::create();

        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => $faker->name,
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]);
        }

        $roomTypes = ['Standard Single', 'Standard Double', 'Deluxe Single', 'Deluxe Double', 'Suite'];
        $roomViews = ['City View', 'Sea View', 'Garden View', 'Mountain View'];
        $poolTypes = ['Indoor Pool', 'Outdoor Pool', 'Infinity Pool', 'No Pool'];

        for ($i = 101; $i <= 120; $i++) {
            Room::create([
                'room_number' => (string)$i,
                'room_type' => $faker->randomElement($roomTypes),
                'price_per_night' => $faker->randomFloat(2, 80, 500), 
                'availability' => $faker->boolean(80), 
                'image' => 'default.jpg',
                'room_view' => $faker->randomElement($roomViews),
                'pool_type' => $faker->randomElement($poolTypes),
                'room_stars' => $faker->numberBetween(1, 5),
                'has_parking' => $faker->boolean(70), 
                'has_airport_transfer' => $faker->boolean(50),
                'has_wifi' => $faker->boolean(95), 
                'has_coffee_maker' => $faker->boolean(60), 
                'has_bar' => $faker->boolean(40), 
                'has_breakfast' => $faker->boolean(80), 
            ]);
        }

        $users = User::where('role', 'user')->get();
        $rooms = Room::all();

        for ($i = 1; $i <= 15; $i++) {
            $checkinDate = $faker->dateTimeBetween('now', '+30 days');
            $checkoutDate = $faker->dateTimeBetween($checkinDate, $checkinDate->format('Y-m-d') . ' +7 days');
            
            Booking::create([
                'user_id' => $faker->randomElement($users)->id,
                'room_id' => $faker->randomElement($rooms)->id,
                'checkin_date' => $checkinDate,
                'checkout_date' => $checkoutDate,
                'guests' => $faker->numberBetween(1, 4),
                'status' => $faker->randomElement(['pending', 'confirmed', 'cancelled']),
            ]);
        }
    }
}
