<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;

class BookingPolicy
{

     // تحديد ما إذا كان المستخدم يمكنه عرض قائمة الحجوزات
    public function viewAny(User $user)
    {
        // السماح فقط للمستخدمين الذين لديهم صلاحية الأدمن
        return $user->isAdmin();
    }

    
      //تحديد ما إذا كان المستخدم يمكنه عرض حجز معين
    public function view(User $user, Booking $booking)
    {
        // السماح فقط للمستخدمين الذين لديهم صلاحية الأدمن أو صاحب الحجز
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

    
     // تحديد ما إذا كان المستخدم يمكنه إنشاء حجز جديد
    public function create(User $user)
    {
        return $user !== null;
    }

    
     // تحديد ما إذا كان المستخدم يمكنه تحديث حجز معين
    public function update(User $user, Booking $booking)
    {
        return $user->isAdmin() || $user->id === $booking->user_id;
    }

     // تحديد ما إذا كان المستخدم يمكنه حذف حجز معين
    public function delete(User $user, Booking $booking)
    {
        return $user->isAdmin() || $user->id === $booking->user_id;
    }
}
