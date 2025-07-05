<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Show the user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $recentBookings = $user->bookings()->with('room')->latest()->take(3)->get();
        $totalBookings = $user->bookings()->count();
        $confirmedBookings = $user->bookings()->where('status', 'confirmed')->count();
        $pendingBookings = $user->bookings()->where('status', 'pending')->count();
        
        $featuredRooms = Room::where('availability', true)
            ->where('room_stars', '>=', 4)
            ->take(6)
            ->get();

        return view('user.dashboard', compact(
            'recentBookings',
            'totalBookings',
            'confirmedBookings',
            'pendingBookings',
            'featuredRooms'
        ));
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        return view('user.profile');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show user bookings
     */
    public function bookings()
    {
        $bookings = Auth::user()->bookings()->with('room')->latest()->paginate(10);
        return view('user.bookings', compact('bookings'));
    }
}
