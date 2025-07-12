<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


 // كنترولر الحجوزات
class BookingController extends Controller
{
    //index. صفحة عرض الحجوزات


    public function index()//عرض حجوزات المستخدم
    {
        // جلب الحجوزات الخاصة بالمستخدم مع بيانات الغرفة و مرتبة حسب الأحدث، مع تقسيم الصفحات
        $bookings = Auth::user()->bookings()->with('room')->latest()->paginate(10);
        
        return view('bookings.index', compact('bookings'));
    }

    
      // عرض نموذج إنشاء حجز جديد    
    public function create(Room $room)
    {
        if (!$room->isAvailable()) {
            return redirect()->route('rooms.index')
                ->with('error', 'This room is not available for booking.');
        }

        // إرجاع صفحة إنشاء الحجز مع بيانات الغرفة
        return view('bookings.create', compact('room'));
    }


    public function store(Request $request)// حفظ حجز جديد
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id', 
            'checkin_date' => 'required|date|after_or_equal:today',
            'checkout_date' => 'required|date|after:checkin_date',
            'guests' => 'required|integer|min:1|max:10', 
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // جلب بيانات الغرفة
        $room = Room::findOrFail($request->room_id); 
        //findOrFail : sql تُسهل لتعامل مع قاعدة البيانات
        //  باستخدام كائنات بي اتش بي بدلًا من كتابة استعلامات  مباشرة.

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

        if ($conflictingBooking) {
            return redirect()->back()
                ->with('error', 'Room is already booked for the selected dates.')
                ->withInput();
        }

        // إنشاء الحجز الجديد بحالة معلق
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'checkin_date' => $request->checkin_date,
            'checkout_date' => $request->checkout_date,
            'guests' => $request->guests,
            'status' => 'pending',
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully! Waiting for admin confirmation.');
    }

    //عرض تفاصيل حجز محدد
    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);//(ممنوع)
        }

        return view('bookings.show', compact('booking'));
    }

     //إلغاء حجز للمستخدم 
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status === 'cancelled') {
            return redirect()->back()
                ->with('error', 'Booking is already cancelled.');
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }

      //تأكيد حجز للأدمن 
    public function confirm(Booking $booking)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $booking->update(['status' => 'confirmed']);

        return redirect()->back()
            ->with('success', 'Booking confirmed successfully.');
    }

    
      //إلغاء حجز للأدمن 
    public function adminCancel(Booking $booking)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->back()
            ->with('success', 'Booking cancelled successfully.');
    }
}
