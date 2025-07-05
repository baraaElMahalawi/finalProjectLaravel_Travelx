<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomApiController extends Controller
{
    /**
     * Get all rooms
     */
    public function index(Request $request)
    {
        $query = Room::query();

        // Filter by availability
        if ($request->has('available')) {
            $query->where('availability', $request->boolean('available'));
        }

        // Filter by room type
        if ($request->has('room_type')) {
            $query->where('room_type', 'like', '%' . $request->room_type . '%');
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        // Filter by amenities
        if ($request->has('has_wifi')) {
            $query->where('has_wifi', $request->boolean('has_wifi'));
        }

        if ($request->has('has_parking')) {
            $query->where('has_parking', $request->boolean('has_parking'));
        }

        if ($request->has('has_breakfast')) {
            $query->where('has_breakfast', $request->boolean('has_breakfast'));
        }

        // Sort by price or stars
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by;
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['price_per_night', 'room_stars', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            $query->latest();
        }

        $perPage = $request->get('per_page', 10);
        $rooms = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $rooms
        ]);
    }

    /**
     * Get single room
     */
    public function show($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $room
        ]);
    }

    /**
     * Create new room (Admin only)
     */
    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

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
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $room = Room::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Room created successfully',
            'data' => $room
        ], 201);
    }

    /**
     * Update room (Admin only)
     */
    public function update(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

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
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $room->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully',
            'data' => $room
        ]);
    }

    /**
     * Delete room (Admin only)
     */
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        // Check if room has active bookings
        $activeBookings = $room->bookings()->whereIn('status', ['pending', 'confirmed'])->count();

        if ($activeBookings > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete room with active bookings'
            ], 400);
        }

        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully'
        ]);
    }

    /**
     * Get available rooms for specific dates
     */
    public function available(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkin_date' => 'required|date|after_or_equal:today',
            'checkout_date' => 'required|date|after:checkin_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $checkinDate = $request->checkin_date;
        $checkoutDate = $request->checkout_date;

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
