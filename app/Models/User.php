<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Booking;
use App\Models\UserPersonalRoom;



 // موديل المستخدم - يمثل بيانات المستخدم في النظام
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    
     // الحقول التي يمكن تعيينها بشكل جماعي
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    
      //الحقول التي يجب إخفاؤها عند التسلسل JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

     // الحقول التي يجب تحويلها  
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
     // التحقق من المستخدم أدمن
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    
     // جلب الحجوزات الخاصة بالمستخدم
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    
     // جلب الغرف الشخصية للمستخدم
    public function personalRooms()
    {
        return $this->hasMany(UserPersonalRoom::class);
    }
}
