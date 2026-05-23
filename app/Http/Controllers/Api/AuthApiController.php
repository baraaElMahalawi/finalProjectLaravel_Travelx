<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    
     // تسجيل مستخدم جديد
    public function register(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'أخطاء في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // إنشاء توكن وصول للمستخدم الجديد
        $token = $user->createToken('auth_token')->plainTextToken;

        // إرجاع استجابة نجاح مع بيانات المستخدم والتوكن
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل المستخدم بنجاح',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    
     //تسجيل دخول المستخدم
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'أخطاء في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        // محاولة تسجيل الدخول باستخدام البريد وكلمة المرور
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات اعتماد غير صحيحة'
            ], 401);
        }

        // جلب المستخدم الحالي
        $user = Auth::user();
        // إنشاء توكن وصول جديد
        $token = $user->createToken('auth_token')->plainTextToken;

        // إرجاع استجابة نجاح مع بيانات المستخدم والتوكن
        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    
      //تسجيل خروج المستخدم
    public function logout(Request $request)
    {
        // حذف التوكن الحالي للمستخدم
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    
     // جلب بيانات الملف الشخصي للمستخدم
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    
      //تحديث بيانات الملف الشخصي للمستخدم
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $request->user()->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'أخطاء في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحديث بيانات المستخدم
        $user = $request->user();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;

        // تحديث كلمة المرور إذا تم إدخالها
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // حفظ التغييرات
        $user->save();

        // إرجاع  نجاح مع بيانات المستخدم المحدثة
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'data' => $user
        ]);
    }
}
