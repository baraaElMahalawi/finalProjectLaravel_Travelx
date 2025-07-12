<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Room;

class RoomPolicy
{

      //تحديد ما إذا كان المستخدم يمكنه عرض قائمة الغرف
    public function viewAny(User \$user)
    {
        // السماح فقط للأدمن
        return \$user->isAdmin();
    }

    
     // تحديد ما إذا كان المستخدم يمكنه عرض غرفة معينة
    public function view(User \$user, Room \$room)
    {
        return \$user->isAdmin();
    }

    
     // تحديد ما إذا كان المستخدم يمكنه إنشاء غرفة جديدة
    public function create(User \$user)
    {
        return \$user->isAdmin();
    }

    
     // تحديد ما إذا كان المستخدم يمكنه تحديث غرفة معينة
    public function update(User \$user, Room \$room)
    {
        return \$user->isAdmin();
    }

    
    // تحديد ما إذا كان المستخدم يمكنه حذف غرفة معينة
    public function delete(User \$user, Room \$room)
    {
        return \$user->isAdmin();
    }
}
