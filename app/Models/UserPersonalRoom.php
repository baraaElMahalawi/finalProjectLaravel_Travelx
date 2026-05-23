<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Room;


class UserPersonalRoom extends Model
{
    use HasFactory;

    
     // الحقول التي يمكن تعيينها بشكل جماعي
    protected $fillable = [
        'user_id',
        'room_id',
    ];

     // جلب المستخدم الذي يملك الغرفة الشخصية
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
     // جلب الغرفة المعينة
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
