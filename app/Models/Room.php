<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_number',
        'room_type',
        'price_per_night',
        'availability',
        'image',
        'room_view',
        'pool_type',
        'room_stars',
        'has_parking',
        'has_airport_transfer',
        'has_wifi',
        'has_coffee_maker',
        'has_bar',
        'has_breakfast',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'availability' => 'boolean',
        'has_parking' => 'boolean',
        'has_airport_transfer' => 'boolean',
        'has_wifi' => 'boolean',
        'has_coffee_maker' => 'boolean',
        'has_bar' => 'boolean',
        'has_breakfast' => 'boolean',
        'price_per_night' => 'decimal:2',
    ];

    /**
     * Get the bookings for the room.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the personal room assignments for the room.
     */
    public function personalRooms()
    {
        return $this->hasMany(UserPersonalRoom::class);
    }

    /**
     * Check if room is available for booking
     */
    public function isAvailable()
    {
        return $this->availability;
    }

    /**
     * Get room amenities as array
     */
    public function getAmenities()
    {
        $amenities = [];
        
        if ($this->has_wifi) $amenities[] = 'WiFi';
        if ($this->has_parking) $amenities[] = 'Parking';
        if ($this->has_airport_transfer) $amenities[] = 'Airport Transfer';
        if ($this->has_coffee_maker) $amenities[] = 'Coffee Maker';
        if ($this->has_bar) $amenities[] = 'Bar';
        if ($this->has_breakfast) $amenities[] = 'Breakfast';
        
        return $amenities;
    }
}
