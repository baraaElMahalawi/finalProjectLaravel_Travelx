<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Room;


 // موديل الحجز - يمثل بيانات الحجز في النظام
class Booking extends Model
{
    use HasFactory;

    
      //الحقول التي يمكن تعيينها بشكل جماعي   
    protected $fillable = [
        'user_id',
        'room_id',
        'checkin_date',
        'checkout_date',
        'guests',
        'status',
    ];

    
     //الحقول التي يجب تحويلها لأنواع معينة   
    protected $casts = [
        'checkin_date' => 'date',
        'checkout_date' => 'date',
    ];

    
     // جلب المستخدم الذي يملك الحجز
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     // جلب الغرفة المحجوزة
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

     //حساب عدد الليالي في الحجز
    public function getTotalNights()
    {
        return $this->checkin_date->diffInDays($this->checkout_date);
    }

    
     // حساب السعر الإجمالي للحجز
    public function getTotalPrice()
    {
        return $this->getTotalNights() * $this->room->price_per_night;
    }

     // التحقق  من الحجز مؤكد
    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

     // التحقق من الحجز معلق
    public function isPending()
    {
        return $this->status === 'pending';
    }

     // التحقق  من الحجز ملغي
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}
