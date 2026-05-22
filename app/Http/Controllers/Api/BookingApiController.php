<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingApiController extends Controller
{
    
     // جلب حجوزات المستخدم
    public function index(Request $request)
    {
        // جلب الحجوزات مع بيانات الغرفة المرتبطة
        $query = $request->user()->bookings()->with('room');

        //الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('from_date')) {
            $query->where('checkin_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('checkout_date', '<=', $request->to_date);
        }

        // عدد النتائج في الصفحة افتراضي 
        $perPage = $request->get('per_page', 10);
        $bookings = $query->latest()->paginate($perPage);

        // إرجاع النتائج بصيغة JSON
        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

     // جلب جميع الحجوزات للمسؤول فقط
    public function adminIndex(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح. يتطلب صلاحيات المسؤول.'
            ], 403);
        }

        $query = Booking::with(['user', 'room']);

        // فلترة حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->has('from_date')) {
            $query->where('checkin_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('checkout_date', '<=', $request->to_date);
        }

        // عدد النتائج في الصفحة افتراضي 15
        $perPage = $request->get('per_page', 15);
        $bookings = $query->latest()->paginate($perPage);

        // إرجاع النتائج بصيغة JSON
        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    
     // جلب تفاصيل حجز معين
    public function show(Request $request, $id)
    {
        $booking = Booking::with(['user', 'room'])->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'الحجز غير موجود'
            ], 404);
        }

        // التحقق من ملكية الحجز أو صلاحية المسؤول
        if ($booking->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح'
            ], 403);
        }

        // إرجاع بيانات الحجز
        return response()->json([
            'success' => true,
            'data' => $booking
        ]);
    }

    
      //إنشاء حجز جديد
    public function store(Request $request)
    {
        // التحقق من صحة البيانات 
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'checkin_date' => 'required|date|after_or_equal:today',
            'checkout_date' => 'required|date|after:checkin_date',
            'guests' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'أخطاء في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $room = Room::find($request->room_id);

        // التحقق من توفر الغرفة
        if (!$room->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'الغرفة غير متوفرة للحجز'
            ], 400);
        }

        // التحقق من وجود حجوزات متعارضة
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

        if ($conflictingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'الغرفة محجوزة بالفعل في التواريخ المحددة'
            ], 400);
        }

        // إنشاء الحجز الجديد بحالة معلق
        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'room_id' => $request->room_id,
            'checkin_date' => $request->checkin_date,
            'checkout_date' => $request->checkout_date,
            'guests' => $request->guests,
            'status' => 'pending',
        ]);

        $booking->load(['user', 'room']);

        // إرجاع استجابة نجاح مع بيانات الحجز
        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحجز بنجاح. في انتظار تأكيد المسؤول.',
            'data' => $booking
        ], 201);
    }

    
     // إلغاء حجز 
    public function cancel(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'الحجز غير موجود'
            ], 404);
        }

        // التحقق من ملكية الحجز
        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح'
            ], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'الحجز ملغي بالفعل'
            ], 400);
        }

        // تحديث حالة الحجز إلى ملغي
        $booking->update(['status' => 'cancelled']);

        // إرجاع استجابة نجاح مع بيانات الحجز
        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح',
            'data' => $booking
        ]);
    }

    
     //تأكيد حجز للمسؤول 
    public function confirm(Request $request, $id)
    {
        // التحقق من صلاحية المسؤول
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح. يتطلب صلاحيات المسؤول.'
            ], 403);
        }

        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'الحجز غير موجود'
            ], 404);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'يمكن تأكيد الحجوزات المعلقة فقط'
            ], 400);
        }

        // تحديث حالة الحجز إلى مؤكد
        $booking->update(['status' => 'confirmed']);
        $booking->load(['user', 'room']);

        // إرجاع استجابة نجاح مع بيانات الحجز
        return response()->json([
            'success' => true,
            'message' => 'تم تأكيد الحجز بنجاح',
            'data' => $booking
        ]);
    }

    
     // إلغاء حجز من المسؤول
    public function adminCancel(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح. يتطلب صلاحيات المسؤول.'
            ], 403);
        }

        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'الحجز غير موجود'
            ], 404);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'الحجز ملغي بالفعل'
            ], 400);
        }

        // تحديث حالة الحجز إلى ملغي
        $booking->update(['status' => 'cancelled']);
        $booking->load(['user', 'room']);

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح',
            'data' => $booking
        ]);
    }

    
     //جلب الحجوزات المعلقة للمسؤول 
    public function pending(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح. يتطلب صلاحيات المسؤول.'
            ], 403);
        }

        $perPage = $request->get('per_page', 15);
        $bookings = Booking::with(['user', 'room'])
            ->where('status', 'pending')
            ->latest()
            ->paginate($perPage);

        // إرجاع النتائج بصيغة JSON
        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    
      //جلب إحصائيات الحجوزات للمسؤول 
    public function statistics(Request $request)
    {
        // التحقق من صلاحية المسؤول
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح. يتطلب صلاحيات المسؤول.'
            ], 403);
        }

        // حساب إحصائيات الحجوزات المختلفة
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        $todayBookings = Booking::whereDate('created_at', today())->count();
        $thisMonthBookings = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // إرجاع الإحصائيات بصيغة JSON
        return response()->json([
            'success' => true,
            'data' => [
                'total_bookings' => $totalBookings,
                'pending_bookings' => $pendingBookings,
                'confirmed_bookings' => $confirmedBookings,
                'cancelled_bookings' => $cancelledBookings,
                'today_bookings' => $todayBookings,
                'this_month_bookings' => $thisMonthBookings,
            ]
        ]);
    }
}
                                                                                                                                                                                                                                                                    