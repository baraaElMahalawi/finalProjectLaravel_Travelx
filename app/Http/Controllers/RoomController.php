<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of available rooms
     */
    public function index()
    {
        $rooms = Room::where('availability', true)->paginate(12);
        return view('rooms.index', compact('rooms'));
    }

    /**
     * Display the specified room
     */
    public function show(Room $room)
    {
        return view('rooms.show', compact('room'));
    }

    /**
     * Show the form for creating a new room (Admin only)
     */
    public function create()
    {
        return view('admin.rooms.create');
    }

    /**
     * Store a newly created room (Admin only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|string|unique:rooms',
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
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/rooms'), $imageName);
            $data['image'] = $imageName;
        }

        // Handle boolean fields
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

    /**
     * Show the form for editing the specified room (Admin only)
     */
    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    /**
     * Update the specified room (Admin only)
     */
    public function update(Request $request, Room $room)
    {
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
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($room->image && file_exists(public_path('images/rooms/'.$room->image))) {
                unlink(public_path('images/rooms/'.$room->image));
            }
            
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images/rooms'), $imageName);
            $data['image'] = $imageName;
        }

        // Handle boolean fields
        $data['availability'] = $request->has('availability');
        $data['has_parking'] = $request->has('has_parking');
        $data['has_airport_transfer'] = $request->has('has_airport_transfer');
        $data['has_wifi'] = $request->has('has_wifi');
        $data['has_coffee_maker'] = $request->has('has_coffee_maker');
        $data['has_bar'] = $request->has('has_bar');
        $data['has_breakfast'] = $request->has('has_breakfast');

        $room->update($data);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified room (Admin only)
     */
    public function destroy(Room $room)
    {
        // Delete image if exists
        if ($room->image && file_exists(public_path('images/rooms/'.$room->image))) {
            unlink(public_path('images/rooms/'.$room->image));
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}
