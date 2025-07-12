<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\RoomApiController;
use App\Http\Controllers\Api\BookingApiController;


// مسارات API العامة (لا تتطلب مصادقة)
Route::prefix('v1')->group(function () {
    
    // مسارات المصادقة
    Route::post('/register', [AuthApiController::class, 'register']); 
    Route::post('/login', [AuthApiController::class, 'login']);      
    
    // مسارات الغرف العامة
    Route::get('/rooms', [RoomApiController::class, 'index']);        
    Route::get('/rooms/{id}', [RoomApiController::class, 'show']);    
    Route::post('/rooms/available', [RoomApiController::class, 'available']); 
    
});

// مسارات API المحمية تتطلب مصادقة
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // مسارات المصادقة للمستخدمين المسجلين
    Route::post('/logout', [AuthApiController::class, 'logout']);     
    Route::get('/profile', [AuthApiController::class, 'profile']);    
    Route::put('/profile', [AuthApiController::class, 'updateProfile']); 
    
    // مسارات الحجوزات الخاصة بالمستخدم
    Route::get('/bookings', [BookingApiController::class, 'index']); 
    Route::post('/bookings', [BookingApiController::class, 'store']); 
    Route::get('/bookings/{id}', [BookingApiController::class, 'show']); 
    Route::patch('/bookings/{id}/cancel', [BookingApiController::class, 'cancel']); 
    
    // مسارات الأدمن
    Route::middleware('admin')->prefix('admin')->group(function () {
        
        // إدارة الغرف
        Route::post('/rooms', [RoomApiController::class, 'store']); 
        Route::put('/rooms/{id}', [RoomApiController::class, 'update']); 
        Route::delete('/rooms/{id}', [RoomApiController::class, 'destroy']); 
        
        // إدارة الحجوزات
        Route::get('/bookings', [BookingApiController::class, 'adminIndex']); 
        Route::get('/bookings/pending', [BookingApiController::class, 'pending']); 
        Route::get('/bookings/statistics', [BookingApiController::class, 'statistics']); 
        Route::patch('/bookings/{id}/confirm', [BookingApiController::class, 'confirm']); 
        Route::patch('/bookings/{id}/cancel', [BookingApiController::class, 'adminCancel']); 
        
    });
    
});

// مسار توثيق API
Route::get('/docs', function () {
    return response()->json([
        'message' => 'Travelx Hotel API Documentation',
        'version' => '1.0',
        'endpoints' => [
            'authentication' => [
                'POST /api/v1/register' => 'تسجيل مستخدم جديد',
                'POST /api/v1/login' => 'تسجيل الدخول',
                'POST /api/v1/logout' => 'تسجيل الخروج (يتطلب مصادقة)',
                'GET /api/v1/profile' => 'جلب بيانات الملف الشخصي (يتطلب مصادقة)',
                'PUT /api/v1/profile' => 'تحديث بيانات الملف الشخصي (يتطلب مصادقة)',
            ],
            'rooms' => [
                'GET /api/v1/rooms' => 'جلب جميع الغرف مع الفلاتر',
                'GET /api/v1/rooms/{id}' => 'جلب غرفة واحدة',
                'POST /api/v1/rooms/available' => 'التحقق من توفر الغرف للتواريخ',
                'POST /api/v1/admin/rooms' => 'إنشاء غرفة (للمسؤول فقط)',
                'PUT /api/v1/admin/rooms/{id}' => 'تحديث غرفة (للمسؤول فقط)',
                'DELETE /api/v1/admin/rooms/{id}' => 'حذف غرفة (للمسؤول فقط)',
            ],
            'bookings' => [
                'GET /api/v1/bookings' => 'جلب حجوزات المستخدم (يتطلب مصادقة)',
                'POST /api/v1/bookings' => 'إنشاء حجز (يتطلب مصادقة)',
                'GET /api/v1/bookings/{id}' => 'جلب حجز واحد (يتطلب مصادقة)',
                'PATCH /api/v1/bookings/{id}/cancel' => 'إلغاء حجز (يتطلب مصادقة)',
                'GET /api/v1/admin/bookings' => 'جلب جميع الحجوزات (للمسؤول فقط)',
                'GET /api/v1/admin/bookings/pending' => 'جلب الحجوزات المعلقة (للمسؤول فقط)',
                'GET /api/v1/admin/bookings/statistics' => 'جلب إحصائيات الحجوزات (للمسؤول فقط)',
                'PATCH /api/v1/admin/bookings/{id}/confirm' => 'تأكيد حجز (للمسؤول فقط)',
                'PATCH /api/v1/admin/bookings/{id}/cancel' => 'إلغاء حجز (للمسؤول فقط)',
            ],
        ],
        'authentication' => [
            'type' => 'Bearer Token',
            'header' => 'Authorization: Bearer {token}',
            'note' => 'قم بإضافة التوكن الذي تم استلامه من تسجيل الدخول في رأس الطلب'
        ],
        'response_format' => [
            'success' => [
                'success' => true,
                'data' => '...',
                'message' => 'رسالة نجاح اختيارية'
            ],
            'error' => [
                'success' => false,
                'message' => 'رسالة خطأ',
                'errors' => 'أخطاء التحقق (اختياري)'
            ]
        ]
    ]);
});
