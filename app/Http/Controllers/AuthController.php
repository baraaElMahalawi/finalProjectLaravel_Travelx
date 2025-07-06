<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * كنترولر المصادقة - يتعامل مع تسجيل الدخول والخروج والتسجيل
 * Authentication Controller - handles login, logout, and registration
 */
class AuthController extends Controller
{
    /**
     * عرض نموذج تسجيل الدخول
     * Show login form
     */
    public function showLoginForm()
    {
        // إرجاع صفحة تسجيل الدخول
        return view('auth.login');
    }

    /**
     * معالجة طلب تسجيل الدخول
     * Handle login request
     */
    public function login(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', // البريد الإلكتروني مطلوب ويجب أن يكون صحيح
            'password' => 'required', // كلمة المرور مطلوبة
        ]);

        // إذا فشل التحقق، إرجاع الأخطاء
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // الحصول على بيانات الاعتماد (البريد الإلكتروني وكلمة المرور)
        $credentials = $request->only('email', 'password');

        // محاولة تسجيل الدخول
        if (Auth::attempt($credentials)) {
            // تجديد الجلسة لأمان إضافي
            $request->session()->regenerate();

            // التحقق من نوع المستخدم وتوجيهه للصفحة المناسبة
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard'); // توجيه الأدمن للوحة الإدارة
            }

            return redirect()->route('user.dashboard'); // توجيه المستخدم العادي للوحة المستخدم
        }

        // في حالة فشل تسجيل الدخول، إرجاع رسالة خطأ
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    /**
     * عرض نموذج التسجيل
     * Show registration form
     */
    public function showRegistrationForm()
    {
        // إرجاع صفحة التسجيل
        return view('auth.register');
    }

    /**
     * معالجة طلب التسجيل
     * Handle registration request
     */
    public function register(Request $request)
    {
        // التحقق من صحة البيانات المدخلة للتسجيل
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255', // الاسم مطلوب ونص بحد أقصى 255 حرف
            'username' => 'required|string|max:255|unique:users', // اسم المستخدم مطلوب وفريد
            'email' => 'required|string|email|max:255|unique:users', // البريد الإلكتروني مطلوب وفريد
            'password' => 'required|string|min:8|confirmed', // كلمة المرور مطلوبة بحد أدنى 8 أحرف ومؤكدة
        ]);

        // إذا فشل التحقق، إرجاع الأخطاء
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
            'role' => 'user', // تعيين دور المستخدم كمستخدم عادي
        ]);

        // تسجيل دخول المستخدم الجديد تلقائياً
        Auth::login($user);

        // توجيه المستخدم للوحة المستخدم
        return redirect()->route('user.dashboard');
    }

    /**
     * معالجة طلب تسجيل الخروج
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // تسجيل خروج المستخدم
        Auth::logout();

        // إلغاء الجلسة الحالية
        $request->session()->invalidate();
        
        // تجديد رمز الجلسة لأمان إضافي
        $request->session()->regenerateToken();

        // توجيه المستخدم للصفحة الرئيسية
        return redirect('/');
    }
}
