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
    /**
     * تشغيل seeders قاعدة البيانات
     */
    public function run(): void
    {
        $faker = Faker::create();

        // إنشاء مستخدم الأدمن
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // إنشاء مستخدمين عاديين باستخدام Faker
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => $faker->name,
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]);
        }

        // أنواع الغرف المختلفة
        $roomTypes = ['Standard Single', 'Standard Double', 'Deluxe Single', 'Deluxe Double', 'Suite'];
        $roomViews = ['City View', 'Sea View', 'Garden View', 'Mountain View'];
        $poolTypes = ['Indoor Pool', 'Outdoor Pool', 'Infinity Pool', 'No Pool'];

        // إنشاء غرف باستخدام Faker
        for ($i = 101; $i <= 120; $i++) {
            Room::create([
                'room_number' => (string)$i,
                'room_type' => $faker->randomElement($roomTypes),
                'price_per_night' => $faker->randomFloat(2, 80, 500), // سعر بين 80 و 500
                'availability' => $faker->boolean(80), // 80% احتمال أن تكون متاحة
                'image' => 'default.jpg',
                'room_view' => $faker->randomElement($roomViews),
                'pool_type' => $faker->randomElement($poolTypes),
                'room_stars' => $faker->numberBetween(1, 5),
                'has_parking' => $faker->boolean(70), // 70% احتمال وجود موقف
                'has_airport_transfer' => $faker->boolean(50), // 50% احتمال وجود نقل مطار
                'has_wifi' => $faker->boolean(95), // 95% احتمال وجود واي فاي
                'has_coffee_maker' => $faker->boolean(60), // 60% احتمال وجود صانع قهوة
                'has_bar' => $faker->boolean(40), // 40% احتمال وجود بار
                'has_breakfast' => $faker->boolean(80), // 80% احتمال وجود إفطار
            ]);
        }

        // إنشاء حجوزات تجريبية باستخدام Faker
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
