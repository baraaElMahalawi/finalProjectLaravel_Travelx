<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware is handled in routes
    }

    /**
     * Show the admin dashboard
     */
    public function dashboard()
    {
        $totalRooms = Room::count();
        $availableRooms = Room::where('availability', true)->count();
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $totalUsers = User::where('role', 'user')->count();

        $recentBookings = Booking::with(['user', 'room'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRooms',
            'availableRooms',
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'totalUsers',
            'recentBookings'
        ));
    }

    /**
     * Display all rooms for admin
     */
    public function rooms()
    {
        $rooms = Room::latest()->paginate(10);
        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Display all bookings for admin
     */
    public function bookings()
    {
        $bookings = Booking::with(['user', 'room'])
            ->latest()
            ->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Display pending bookings for admin
     */
    public function pendingBookings()
    {
        $bookings = Booking::with(['user', 'room'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);
        return view('admin.bookings.pending', compact('bookings'));
    }

    /**
     * Display all users for admin
     */
    public function users()
    {
        $users = User::where('role', 'user')
            ->latest()
            ->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show admin profile
     */
    public function profile()
    {
        return view('admin.profile');
    }

    /**
     * Update admin profile
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
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('admin.profile')
            ->with('success', 'Profile updated successfully.');
    }
}
