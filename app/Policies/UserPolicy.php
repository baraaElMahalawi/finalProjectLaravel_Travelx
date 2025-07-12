<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    
     // تحديد ما إذا كان المستخدم يمكنه عرض قائمة المستخدمين
    public function viewAny(User $user)
    {
        // السماح فقط للأدمن
        return $user->isAdmin();
    }

    
     // تحديد ما إذا كان المستخدم يمكنه عرض مستخدم معين
    public function view(User $user, User $model)
    {
        //السماح فقط للأدمن أو المستخدم نفسه
        return $user->isAdmin() || $user->id === $model->id;
    }

    
     // تحديد ما إذا كان المستخدم يمكنه تحديث مستخدم معين
    public function update(User $user, User $model)
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    
     // تحديد ما إذا كان المستخدم يمكنه حذف مستخدم معين
    public function delete(User $user, User $model)
    {
        return $user->isAdmin();
    }
}
