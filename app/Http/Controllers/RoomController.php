<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * كنترولر الغرف - يتعامل مع عرض وإدارة الغرف
 * Room Controller - handles room display and management
 */
class RoomController extends Controller
{
    /**
     * عرض قائمة الغرف المتاحة
     * Display a listing of available rooms
     */
    public function index()
    {
        // جلب الغرف المتاحة فقط مع تقسيم الصفحات (12 غرفة في كل صفحة)
        $rooms = Room::where('availability', true)->paginate(12);
        
        // إرجاع صفحة عرض الغرف مع البيانات
        return view('rooms.index', compact('rooms'));
    }

    /**
     * عرض تفاصيل غرفة محددة
     * Display the specified room
     */
    public function show(Room $room)
    {
        // إرجاع صفحة تفاصيل الغرفة
        return view('rooms.show', compact('room'));
    }

    /**
     * عرض نموذج إنشاء غرفة جديدة (للأدمن فقط)
     * Show the form for creating a new room (Admin only)
     */
    public function create()
    {
        // إرجاع صفحة إنشاء غرفة جديدة
        return view('admin.rooms.create');
    }

    /**
     * حفظ غرفة جديدة (للأدمن فقط)
     * Store a newly created room (Admin only)
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|string|unique:rooms', // رقم الغرفة مطلوب وفريد
            'room_type' => 'required|string', // نوع الغرفة مطلوب
            'price_per_night' => 'required|numeric|min:0', // سعر الليلة مطلوب ورقم موجب
            'room_view' => 'nullable|string', // إطلالة الغرفة اختيارية
            'pool_type' => 'nullable|string', // نوع المسبح اختياري
            'room_stars' => 'required|integer|min:1|max:5', // تقييم الغرفة من 1 إلى 5 نجوم
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // صورة اختيارية بحجم أقصى 2MB
        ]);

        // إذا فشل التحقق، إرجاع الأخطاء
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // الحصول على جميع البيانات
        $data = $request->all();
        
        // معالجة رفع الصورة
        if ($request->hasFile('image')) {
            // إنشاء اسم فريد للصورة باستخدام الوقت الحالي
            $imageName = time().'.'.$request->image->extension();
            // نقل الصورة إلى مجلد الصور
            $request->image->move(public_path('images/rooms'), $imageName);
            $data['image'] = $imageName;
        }

        // معالجة الحقول المنطقية (checkbox)
        $data['availability'] = $request->has('availability'); // متاحة أم لا
        $data['has_parking'] = $request->has('has_parking'); // يوجد موقف سيارات
        $data['has_airport_transfer'] = $request->has('has_airport_transfer'); // يوجد نقل من المطار
        $data['has_wifi'] = $request->has('has_wifi'); // يوجد واي فاي
        $data['has_coffee_maker'] = $request->has('has_coffee_maker'); // يوجد صانع قهوة
        $data['has_bar'] = $request->has('has_bar'); // يوجد بار
        $data['has_breakfast'] = $request->has('has_breakfast'); // يوجد إفطار

        // إنشاء الغرفة الجديدة
        Room::create($data);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    /**
     * عرض نموذج تعديل غرفة محددة (للأدمن فقط)
     * Show the form for editing the specified room (Admin only)
     */
    public function edit(Room $room)
    {
        // إرجاع صفحة تعديل الغرفة
        return view('admin.rooms.edit', compact('room'));
    }

    /**
     * تحديث غرفة محددة (للأدمن فقط)
     * Update the specified room (Admin only)
     */
    public function update(Request $request, Room $room)
    {
        // التحقق من صحة البيانات مع استثناء الغرفة الحالية من فحص التفرد
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|string|unique:rooms,room_number,'.$room->id,
            'room_type' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'room_view' => 'nullable|string',
            'pool_type' => 'nullable|string',
            'room_stars' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // إذا فشل التحقق، إرجاع الأخطاء
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // الحصول على جميع البيانات
        $data = $request->all();
        
        // معالجة رفع الصورة الجديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($room->image && file_exists(public_path('images/rooms/'.$room->image))) {
                unlink(public_path('images/rooms/'.$room->image));
            }
            
            // رفع الصورة الجديدة
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/rooms'), $imageName);
            $data['image'] = $imageName;
        }

        // معالجة الحقول المنطقية
        $data['availability'] = $request->has('availability');
        $data['has_parking'] = $request->has('has_parking');
        $data['has_airport_transfer'] = $request->has('has_airport_transfer');
        $data['has_wifi'] = $request->has('has_wifi');
        $data['has_coffee_maker'] = $request->has('has_coffee_maker');
        $data['has_bar'] = $request->has('has_bar');
        $data['has_breakfast'] = $request->has('has_breakfast');

        // تحديث بيانات الغرفة
        $room->update($data);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    /**
     * حذف غرفة محددة (للأدمن فقط)
     * Remove the specified room (Admin only)
     */
    public function destroy(Room $room)
    {
        // حذف صورة الغرفة إذا كانت موجودة
        if ($room->image && file_exists(public_path('images/rooms/'.$room->image))) {
            unlink(public_path('images/rooms/'.$room->image));
        }

        // حذف الغرفة من قاعدة البيانات
        $room->delete();

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}
