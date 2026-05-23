<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;

// الصفحة الرئيسية
Route::get('/', function () {
    return view('welcome');
})->name('home');

// مسارات المصادقة (للمستخدمين غير المسجلين فقط)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// تسجيل الخروج للمستخدمين المسجلين فقط
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

//  متاحة للجميع
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

// مسارات المستخدمين المسجلين فقط
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    
   // Route::get('/my-bookings', [UserController::class, 'bookings'])->name('user.bookings');
    
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/rooms/{room}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');// يقوم بإنشاء مسار جديد لتخزين بيانات الحجز الى بوست عند ارسال طلب بوكينج
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    
});

// مسارات الأدمن (للمستخدمين من نوع أدمن فقط)
//تحقق مما إذا كان المستخدم مسجلاً للدخول واذا لم يسجل دخول يرجع على واجهة تسجيل الدخول 
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    
    Route::get('/rooms', [AdminController::class, 'rooms'])->name('rooms.index');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');// يقوم بإنشاء مسار جديد لتخزين بيانات الغرفة الجديدة الى بوست عند ارسال طلب بوكينج
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings.index');
    Route::get('/bookings/pending', [AdminController::class, 'pendingBookings'])->name('bookings.pending');
    Route::patch('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    //هذا يحدد أن المسار سيستجيب لطلبات التي تُستخدم في تعديلات جزئية على مورد موجود و في هذه الحالة أنت تقوم بتعديل حالة الحجز إلى مؤكد
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'adminCancel'])->name('bookings.cancel');//نفس السابق لكن مخصص لإلغاء الحجز
    
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
});
