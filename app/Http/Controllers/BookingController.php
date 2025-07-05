<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Display user's bookings
     */
    public function index()
    {
        $bookings = Auth::user()->bookings()->with('room')->latest()->paginate(10);
        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking
     */
    public function create(Room $room)
    {
        if (!$room->isAvailable()) {
            return redirect()->route('rooms.index')
                ->with('error', 'This room is not available for booking.');
        }

        return view('bookings.create', compact('room'));
    }

    /**
     * Store a newly created booking
     */
    public function store(Request $request)
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

        $room = Room::findOrFail($request->room_id);

        if (!$room->isAvailable()) {
            return redirect()->route('rooms.index')
                ->with('error', 'This room is not available for booking.');
        }

        // Check for conflicting bookings
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

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        // Check if user owns this booking or is admin
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('bookings.show', compact('booking'));
    }

    /**
     * Cancel a booking
     */
    public function cancel(Booking $booking)
    {
        // Check if user owns this booking
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

    /**
     * Confirm a booking (Admin only)
     */
    public function confirm(Booking $booking)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $booking->update(['status' => 'confirmed']);

        return redirect()->back()
            ->with('success', 'Booking confirmed successfully.');
    }

    /**
     * Cancel a booking (Admin only)
     */
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
