<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


   //كنترولر الغرف 
class RoomController extends Controller
{
    public function index()
    {
        // جلب الغرف المتاحة  مع تقسيم الصفحات الى ١٢ في كا صفحة
        $rooms = Room::where('availability', true)->paginate(12);
        
        return view('rooms.index', compact('rooms'));
    }

     // عرض تفاصيل غرفة محددة
    public function show(Room $room)
    {
        return view('rooms.show', compact('room'));
    }

      //عرض نموذج إنشاء غرفة جديدة للأدمن 
    public function create()
    {
        return view('admin.rooms.create');
    }

      //حفظ غرفة جديدة للأدمن 
    public function store(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        //nullable اختياري/ unique فريد
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|string|unique:rooms',
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
        
        // معالجة رفع الصورة
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension(); // إنشاء اسم فريد للصورة باستخدام الوقت الحالي
            $request->image->move(public_path('images/rooms'), $imageName);// نقل الصورة إلى مجلد الصور
            $data['image'] = $imageName;
        }

        // معالجة checkbox
        $data['availability'] = $request->has('availability'); 
        $data['has_parking'] = $request->has('has_parking'); 
        $data['has_airport_transfer'] = $request->has('has_airport_transfer'); 
        $data['has_wifi'] = $request->has('has_wifi'); 
        $data['has_coffee_maker'] = $request->has('has_coffee_maker');
        $data['has_bar'] = $request->has('has_bar'); 
        $data['has_breakfast'] = $request->has('has_breakfast'); 

        Room::create($data);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room created successfully.');
    }

     //عرض نموذج تعديل غرفة  للأدمن 
    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));// إرجاع تعديل الغرفة
    }


      //تحديث غرفة محددة للأدمن 
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

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // معالجة رفع الصورة الجديدة
        if ($request->hasFile('image')) {
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

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

        //حذف غرفة محددة للأدمن فقط
    public function destroy(Room $room)
    {
        // حذف صورة الغرفة إذا كانت موجودة
        if ($room->image && file_exists(public_path('images/rooms/'.$room->image))) {
            unlink(public_path('images/rooms/'.$room->image));
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}