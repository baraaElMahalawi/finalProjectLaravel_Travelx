<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    
      //إنشاء نسخة جديدة من الكنترولر.
    public function __construct()
    {
        // Middleware يتم التعامل معه في ملف routes
    }

    
     // عرض لوحة تحكم الأدمن مع إحصائيات عامة.
    public function dashboard()
    {
        // حساب إجمالي عدد الغرف
        $totalRooms = Room::count();
        // حساب عدد الغرف المتاحة 
        $availableRooms = Room::where('availability', true)->count();
        // حساب إجمالي الحجوزات
        $totalBookings = Booking::count();
        // حساب الحجوزات المعلقة
        $pendingBookings = Booking::where('status', 'pending')->count();
        // حساب الحجوزات المؤكدة
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        // حساب عدد المستخدمين العاديين
        $totalUsers = User::where('role', 'user')->count();

        // جلب أحدث 5 حجوزات مع بيانات المستخدم والغرفة المرتبطة
        $recentBookings = Booking::with(['user', 'room'])
            ->latest()
            ->take(5)
            ->get();

        // إرجاع صفحة لوحة التحكم مع البيانات المحسوبة
        return view('admin.dashboard', compact(
            'totalRooms',
            'availableRooms',
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'totalUsers',
            'recentBookings'
        ));
    }

     // عرض جميع الغرف في لوحة تحكم الأدمن مع التصفح.
    public function rooms()
    {
        $rooms = Room::latest()->paginate(10);
        return view('admin.rooms.index', compact('rooms'));
    }

    
     //عرض جميع الحجوزات في لوحة تحكم الأدمن مع التصفح.
    public function bookings()
    {
        $bookings = Booking::with(['user', 'room'])
            ->latest()
            ->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

     // عرض الحجوزات المعلقة  في لوحة تحكم الأدمن.
    public function pendingBookings()
    {
        $bookings = Booking::with(['user', 'room'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);
        return view('admin.bookings.pending', compact('bookings'));
    }

     //عرض جميع المستخدمين العاديين في لوحة تحكم الأدمن مع التصفح.
    public function users()
    {
        $users = User::where('role', 'user')
            ->latest()
            ->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    
      // عرض صفحة ملف الأدمن الشخصي.
    public function profile()
    {
        return view('admin.profile');
    }

    
     // تحديث بيانات ملف الأدمن .
    public function updateProfile(Request $request)
    {
        // التحقق من صحة البيانات 
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // جلب المستخدم الحالي
        $user = Auth::user();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;

        // تحديث كلمة المرور إذا تم إدخالها
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('admin.profile')
            ->with('success', 'تم تحديث الملف الشخصي بنجاح.');
    }
}
