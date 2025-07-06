<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * كنترولر الحجوزات - يتعامل مع عرض وإدارة الحجوزات
 * Booking Controller - handles booking display and management
 */
class BookingController extends Controller
{
    /**
     * عرض حجوزات المستخدم
     * Display user's bookings
     */
    public function index()
    {
        // جلب الحجوزات الخاصة بالمستخدم مع بيانات الغرفة، مرتبة حسب الأحدث، مع تقسيم الصفحات
        $bookings = Auth::user()->bookings()->with('room')->latest()->paginate(10);
        
        // إرجاع صفحة عرض الحجوزات
        return view('bookings.index', compact('bookings'));
    }

    /**
     * عرض نموذج إنشاء حجز جديد
     * Show the form for creating a new booking
     */
    public function create(Room $room)
    {
        // التحقق من توفر الغرفة
        if (!$room->isAvailable()) {
            return redirect()->route('rooms.index')
                ->with('error', 'This room is not available for booking.');
        }

        // إرجاع صفحة إنشاء الحجز مع بيانات الغرفة
        return view('bookings.create', compact('room'));
    }

    /**
     * حفظ حجز جديد
     * Store a newly created booking
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id', // يجب أن تكون الغرفة موجودة
            'checkin_date' => 'required|date|after_or_equal:today', // تاريخ الوصول يجب أن يكون اليوم أو بعده
            'checkout_date' => 'required|date|after:checkin_date', // تاريخ المغادرة يجب أن يكون بعد الوصول
            'guests' => 'required|integer|min:1|max:10', // عدد الضيوف بين 1 و 10
        ]);

        // إذا فشل التحقق، إرجاع الأخطاء
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // جلب بيانات الغرفة
        $room = Room::findOrFail($request->room_id);

        // التحقق من توفر الغرفة مجدداً
        if (!$room->isAvailable()) {
            return redirect()->route('rooms.index')
                ->with('error', 'This room is not available for booking.');
        }

        // التحقق من وجود حجوزات متعارضة في نفس الفترة
        $conflictingBooking = Booking::where('room_id', $request->room_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('checkin_date', [$request->checkin_date, $request->checkout_date])
                    ->orWhereBetween('checkout_date', [$request->checkin_date, $request->checkout_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('checkin_date', '<=', $request->checkin_date)
                          ->where('checkout_date', '>=', $request->checkout_date);
                    });
            })
            ->exists();

        // إذا وجدت حجوزات متعارضة، إرجاع رسالة خطأ
        if ($conflictingBooking) {
            return redirect()->back()
                ->with('error', 'Room is already booked for the selected dates.')
                ->withInput();
        }

        // إنشاء الحجز الجديد بحالة "معلق"
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'checkin_date' => $request->checkin_date,
            'checkout_date' => $request->checkout_date,
            'guests' => $request->guests,
            'status' => 'pending',
        ]);

        // إعادة التوجيه لعرض تفاصيل الحجز مع رسالة نجاح
        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully! Waiting for admin confirmation.');
    }

    /**
     * عرض تفاصيل حجز محدد
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        // التحقق من أن المستخدم يملك الحجز أو هو أدمن
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // إرجاع صفحة تفاصيل الحجز
        return view('bookings.show', compact('booking'));
    }

    /**
     * إلغاء حجز (للمستخدم فقط)
     * Cancel a booking
     */
    public function cancel(Booking $booking)
    {
        // التحقق من أن المستخدم يملك الحجز
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // التحقق من أن الحجز لم يتم إلغاؤه مسبقاً
        if ($booking->status === 'cancelled') {
            return redirect()->back()
                ->with('error', 'Booking is already cancelled.');
        }

        // تحديث حالة الحجز إلى ملغي
        $booking->update(['status' => 'cancelled']);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }

    /**
     * تأكيد حجز (للأدمن فقط)
     * Confirm a booking (Admin only)
     */
    public function confirm(Booking $booking)
    {
        // التحقق من أن المستخدم أدمن
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        // تحديث حالة الحجز إلى مؤكد
        $booking->update(['status' => 'confirmed']);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->back()
            ->with('success', 'Booking confirmed successfully.');
    }

    /**
     * إلغاء حجز (للأدمن فقط)
     * Cancel a booking (Admin only)
     */
    public function adminCancel(Booking $booking)
    {
        // التحقق من أن المستخدم أدمن
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        // تحديث حالة الحجز إلى ملغي
        $booking->update(['status' => 'cancelled']);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->back()
            ->with('success', 'Booking cancelled successfully.');
    }
}
