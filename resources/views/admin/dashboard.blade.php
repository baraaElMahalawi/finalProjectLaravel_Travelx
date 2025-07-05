@extends('layouts.app')

@section('title', 'Admin Dashboard - Travelx Hotel')

@section('content')
<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            <div class="sidebar">
                <nav class="nav flex-column">
                    <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="{{ route('admin.rooms.index') }}">
                        <i class="fas fa-bed"></i> Rooms
                    </a>
                    <a class="nav-link" href="{{ route('admin.bookings.index') }}">
                        <i class="fas fa-calendar-check"></i> Bookings
                    </a>
                    <a class="nav-link" href="{{ route('admin.bookings.pending') }}">
                        <i class="fas fa-clock"></i> Pending Bookings
                    </a>
                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a class="nav-link" href="{{ route('admin.profile') }}">
                        <i class="fas fa-user-cog"></i> Profile
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
                <div class="text-muted">
                    Welcome back, {{ auth()->user()->name }}
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-number">{{ $totalRooms }}</div>
                        <div><i class="fas fa-bed"></i> Total Rooms</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-number">{{ $availableRooms }}</div>
                        <div><i class="fas fa-check-circle"></i> Available</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-number">{{ $totalBookings }}</div>
                        <div><i class="fas fa-calendar"></i> Total Bookings</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-number">{{ $pendingBookings }}</div>
                        <div><i class="fas fa-clock"></i> Pending</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-number">{{ $confirmedBookings }}</div>
                        <div><i class="fas fa-check"></i> Confirmed</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stats-card">
                        <div class="stats-number">{{ $totalUsers }}</div>
                        <div><i class="fas fa-users"></i> Users</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Bookings -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Recent Bookings</h5>
                            <div>
                                <a href="{{ route('admin.bookings.pending') }}" class="btn btn-sm btn-warning me-2">
                                    <i class="fas fa-clock"></i> Pending ({{ $pendingBookings }})
                                </a>
                                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @forelse($recentBookings as $booking)
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('images/rooms/' . ($booking->room->image ?? 'default.jpg')) }}" 
                                         class="rounded me-3" 
                                         style="width: 60px; height: 45px; object-fit: cover;"
                                         alt="{{ $booking->room->room_type }}">
                                    <div>
                                        <h6 class="mb-1">{{ $booking->user->name }}</h6>
                                        <p class="mb-1 text-muted">
                                            {{ $booking->room->room_type }} - Room {{ $booking->room->room_number }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> 
                                            {{ $booking->checkin_date->format('M d') }} - 
                                            {{ $booking->checkout_date->format('M d, Y') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge 
                                        @if($booking->status == 'confirmed') bg-success
                                        @elseif($booking->status == 'pending') bg-warning
                                        @else bg-danger
                                        @endif mb-2">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    <div class="h6 mb-0">${{ number_format($booking->getTotalPrice(), 2) }}</div>
                                </div>
                                @if($booking->status == 'pending')
                                <div class="ms-3">
                                    <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No recent bookings</h5>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Room
                                </a>
                                <a href="{{ route('admin.bookings.pending') }}" class="btn btn-warning">
                                    <i class="fas fa-clock"></i> Review Pending Bookings
                                </a>
                                <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-bed"></i> Manage Rooms
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-users"></i> View Users
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> System Info</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Total Revenue:</strong> 
                                ${{ number_format(\App\Models\Booking::where('status', 'confirmed')->get()->sum(function($booking) { return $booking->getTotalPrice(); }), 2) }}
                            </p>
                            <p class="mb-2">
                                <strong>Occupancy Rate:</strong> 
                                {{ $totalRooms > 0 ? round((($totalRooms - $availableRooms) / $totalRooms) * 100, 1) : 0 }}%
                            </p>
                            <p class="mb-2">
                                <strong>Average Room Price:</strong> 
                                ${{ number_format(\App\Models\Room::avg('price_per_night'), 2) }}
                            </p>
                            <p class="mb-0">
                                <strong>Last Updated:</strong> 
                                {{ now()->format('M d, Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Status Overview -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Room Status Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach(\App\Models\Room::all()->groupBy('room_type') as $type => $rooms)
                                <div class="col-md-3 mb-3">
                                    <div class="border rounded p-3">
                                        <h6>{{ $type }}</h6>
                                        <p class="mb-1">
                                            <span class="text-success">{{ $rooms->where('availability', true)->count() }} Available</span>
                                        </p>
                                        <p class="mb-0">
                                            <span class="text-danger">{{ $rooms->where('availability', false)->count() }} Occupied</span>
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
