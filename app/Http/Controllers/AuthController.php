<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


  // كنترولر المصادقة
class AuthController extends Controller
{
    
      // عرض نموذج تسجيل الدخول
    public function showLoginForm()
    {
        return view('auth.login');// إرجاع صفحة تسجيل الدخول
    }

     //معالجة طلب تسجيل الدخول

    public function login(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', 
            'password' => 'required', 
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // الحصول على بيانات الاعتماد البريد الإلكتروني وكلمة المرور
        $credentials = $request->only('email', 'password');

        // محاولة تسجيل الدخول
        if (Auth::attempt($credentials)) {
            // تجديد الجلسة لأمان إضافي
            $request->session()->regenerate();

            // التحقق من نوع المستخدم وتوجيهه
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard'); 
            }

            return redirect()->route('user.dashboard'); 
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    
     // عرض نموذج التسجيل     
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    
      //معالجة طلب التسجيل     
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255', 
            'username' => 'required|string|max:255|unique:users', 
            'email' => 'required|string|email|max:255|unique:users', 
            'password' => 'required|string|min:8|confirmed', 
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // إنشاء مستخدم جديد
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // تشفير كلمة المرور
            'role' => 'user', 
        ]);

        Auth::login($user); // تسجيل دخول المستخدم الجديد تلقائياً

        // توجيه المستخدم للوحة المستخدم
        return redirect()->route('user.dashboard');
    }

    
     //معالجة طلب تسجيل الخروج
    public function logout(Request $request)
    {
        Auth::logout(); 

        $request->session()->invalidate();// إلغاء الجلسة الحالية
        
        $request->session()->regenerateToken();// تجديد رمز الجلسة لأمان إضافي

        return redirect('/');
    }
}
