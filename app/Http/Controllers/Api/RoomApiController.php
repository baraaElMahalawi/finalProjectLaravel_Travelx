<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomApiController extends Controller
{
    
     // جلب جميع الغرف مع إمكانية الفلترة والفرز.
    public function index(Request $request)
    {
        $query = Room::query();

        // فلترة حسب التوفر
        if ($request->has('available')) {
            $query->where('availability', $request->boolean('available'));
        }

        if ($request->has('room_type')) {
            $query->where('room_type', 'like', '%' . $request->room_type . '%');
        }

        if ($request->has('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        if ($request->has('has_wifi')) {
            $query->where('has_wifi', $request->boolean('has_wifi'));
        }

        if ($request->has('has_parking')) {
            $query->where('has_parking', $request->boolean('has_parking'));
        }

        if ($request->has('has_breakfast')) {
            $query->where('has_breakfast', $request->boolean('has_breakfast'));
        }

        // الفرز حسب السعر أو النجوم أو تاريخ الإنشاء
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['price_per_night', 'room_stars', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            $query->latest();
        }

        // عدد النتائج في الصفحةافتراضي 
        $perPage = $request->get('per_page', 10);
        $rooms = $query->paginate($perPage);

        // إرجاع النتائج بصيغة JSON
        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    
     // جلب تفاصيل غرفة واحدة
    public function show($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'الغرفة غير موجودة'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $room
        ]);
    }

    
      //إنشاء غرفة جديدة للمسؤول 
    public function store(Request $request)
    {
        // التحقق من صلاحية المسؤول
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح. يتطلب صلاحيات المسؤول.'
            ], 403);
        }

        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|string|unique:rooms',
            'room_type' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'availability' => 'boolean',
            'image' => 'nullable|string',
            'room_view' => 'nullable|string',
            'pool_type' => 'nullable|string',
            'room_stars' => 'integer|min:1|max:5',
            'has_parking' => 'boolean',
            'has_airport_transfer' => 'boolean',
            'has_wifi' => 'boolean',
            'has_coffee_maker' => 'boolean',
            'has_bar' => 'boolean',
            'has_breakfast' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'أخطاء في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        // إنشاء الغرفة
        $room = Room::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الغرفة بنجاح',
            'data' => $room
        ], 201);
    }

    
     //تحديث غرفةللمسؤول 
    public function update(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح. يتطلب صلاحيات المسؤول.'
            ], 403);
        }

        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'الغرفة غير موجودة'
            ], 404);
        }

        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|string|unique:rooms,room_number,' . $id,
            'room_type' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'availability' => 'boolean',
            'image' => 'nullable|string',
            'room_view' => 'nullable|string',
            'pool_type' => 'nullable|string',
            'room_stars' => 'integer|min:1|max:5',
            'has_parking' => 'boolean',
            'has_airport_transfer' => 'boolean',
            'has_wifi' => 'boolean',
            'has_coffee_maker' => 'boolean',
            'has_bar' => 'boolean',
            'has_breakfast' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'أخطاء في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحديث بيانات الغرفة
        $room->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الغرفة بنجاح',
            'data' => $room
        ]);
    }

    
      //حذف غرفةللمسؤول 
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح. يتطلب صلاحيات المسؤول.'
            ], 403);
        }

        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'الغرفة غير موجودة'
            ], 404);
        }

        // التحقق من وجود حجوزات نشطة على الغرفة
        $activeBookings = $room->bookings()->whereIn('status', ['pending', 'confirmed'])->count();

        if ($activeBookings > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف غرفة تحتوي على حجوزات نشطة'
            ], 400);
        }

        // حذف الغرفة
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الغرفة بنجاح'
        ]);
    }


      //جلب الغرف المتاحة لتواريخ محددة
    public function available(Request $request)
    {
        // التحقق من صحة التواريخ المدخلة
        $validator = Validator::make($request->all(), [
            'checkin_date' => 'required|date|after_or_equal:today',
            'checkout_date' => 'required|date|after:checkin_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'أخطاء في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $checkinDate = $request->checkin_date;
        $checkoutDate = $request->checkout_date;

        // جلب الغرف المتاحة التي لا تحتوي على حجوزات متعارضة
        $availableRooms = Room::where('availability', true)
            ->whereDoesntHave('bookings', function ($query) use ($checkinDate, $checkoutDate) {
                $query->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($checkinDate, $checkoutDate) {
                        $q->whereBetween('checkin_date', [$checkinDate, $checkoutDate])
                          ->orWhereBetween('checkout_date', [$checkinDate, $checkoutDate])
                          ->orWhere(function ($subQ) use ($checkinDate, $checkoutDate) {
                              $subQ->where('checkin_date', '<=', $checkinDate)
                                   ->where('checkout_date', '>=', $checkoutDate);
                          });
                    });
            })
            ->get();

        // إرجاع الغرف المتاحة مع بيانات التواريخ وعدد الغرف
        return response()->json([
            'success' => true,
            'data' => $availableRooms,
            'meta' => [
                'checkin_date' => $checkinDate,
                'checkout_date' => $checkoutDate,
                'total_available' => $availableRooms->count()
            ]
        ]);
    }
}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       