<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

 //للتحقق الأدمن
class AdminMiddleware
{
    
      //معالجة الطلب الوارد
    public function handle(Request $request, Closure $next)
    {
        // التحقق من تسجيل الدخول وأن المستخدم أدمن
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
