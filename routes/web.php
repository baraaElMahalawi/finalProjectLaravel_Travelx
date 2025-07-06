<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| هذا الملف يحتوي على جميع مسارات الويب الخاصة بالتطبيق
| This file contains all web routes for the application
|
*/

// الصفحة الرئيسية
Route::get('/', function () {
    return view('welcome');
})->name('home');

// مسارات المصادقة (للمستخدمين غير المسجلين فقط)
Route::middleware('guest')->group(function () {
    // عرض نموذج تسجيل الدخول
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    // معالجة طلب تسجيل الدخول
    Route::post('/login', [AuthController::class, 'login']);
    // عرض نموذج التسجيل
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    // معالجة طلب التسجيل
    Route::post('/register', [AuthController::class, 'register']);
});

// تسجيل الخروج (للمستخدمين المسجلين فقط)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// مسارات الغرف العامة (متاحة للجميع)
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

// مسارات المستخدمين (للمستخدمين المسجلين فقط)
Route::middleware(['auth'])->group(function () {
    // لوحة تحكم المستخدم
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    
    // صفحة تعديل الملف الشخصي
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    
    // عرض حجوزات المستخدم
    Route::get('/my-bookings', [UserController::class, 'bookings'])->name('user.bookings');
    
    // مسارات الحجوزات
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/rooms/{room}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// مسارات الأدمن (للمستخدمين من نوع أدمن فقط)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // لوحة تحكم الأدمن
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // صفحة تعديل ملف الأدمن الشخصي
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    
    // إدارة الغرف
    Route::get('/rooms', [AdminController::class, 'rooms'])->name('rooms.index');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    
    // إدارة الحجوزات
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings.index');
    Route::get('/bookings/pending', [AdminController::class, 'pendingBookings'])->name('bookings.pending');
    Route::patch('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'adminCancel'])->name('bookings.cancel');
    
    // إدارة المستخدمين
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
});
