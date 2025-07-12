@extends('layouts.app')

@section('title', 'Dashboard - Travelx Hotel')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-tachometer-alt"></i> Welcome back, {{ auth()->user()->name }}!
            </h1>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">{{ $totalBookings }}</div>
                <div>Total Bookings</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">{{ $confirmedBookings }}</div>
                <div>Confirmed</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">{{ $pendingBookings }}</div>
                <div>Pending</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-number">{{ \App\Models\Room::where('availability', true)->count() }}</div>
                <div>Available Rooms</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Bookings -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Recent Bookings</h5>
                    <a href="{{ route('user.bookings') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @forelse($recentBookings as $booking)
                    <div class="d-flex align-items-center mb-3 p-3 border rounded">
                        <img src="{{ asset('images/rooms/' . ($booking->room->image ?? 'default.jpg')) }}" 
                             class="rounded me-3" 
                             style="width: 80px; height: 60px; object-fit: cover;"
                             alt="{{ $booking->room->room_type }}">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $booking->room->room_type }}</h6>
                            <p class="mb-1 text-muted">
                                <i class="fas fa-calendar"></i> 
                                {{ $booking->checkin_date->format('M d, Y') }} - 
                                {{ $booking->checkout_date->format('M d, Y') }}
                            </p>
                            <p class="mb-0">
                                <span class="badge 
                                    @if($booking->status == 'confirmed') bg-success
                                    @elseif($booking->status == 'pending') bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="h6 mb-1">${{ number_format($booking->getTotalPrice(), 2) }}</div>
                            <small class="text-muted">{{ $booking->getTotalNights() }} nights</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No bookings yet</h5>
                        <p class="text-muted">Start exploring our amazing rooms!</p>
                        <a href="{{ route('rooms.index') }}" class="btn btn-primary">
                            <i class="fas fa-bed"></i> Browse Rooms
                        </a>
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
                        <a href="{{ route('rooms.index') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Find Rooms
                        </a>
                        <a href="{{ route('user.bookings') }}" class="btn btn-outline-primary">
                            <i class="fas fa-calendar-check"></i> My Bookings
                        </a>
                        <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user-edit"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Account Info</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Name:</strong> {{ auth()->user()->name }}
                    </p>
                    <p class="mb-2">
                        <strong>Username:</strong> {{ auth()->user()->username }}
                    </p>
                    <p class="mb-2">
                        <strong>Email:</strong> {{ auth()->user()->email }}
                    </p>
                    <p class="mb-0">
                        <strong>Member since:</strong> {{ auth()->user()->created_at->format('M Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Rooms -->
    @if($featuredRooms->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4"><i class="fas fa-star"></i> Featured Rooms</h3>
        </div>
    </div>
    <div class="row g-4">
        @foreach($featuredRooms as $room)
        <div class="col-lg-4 col-md-6">
            <div class="card room-card h-100">
                <div class="position-relative">
                    <img src="{{ asset('images/rooms/' . ($room->image ?? 'default.jpg')) }}" 
                         class="card-img-top room-image" 
                         alt="{{ $room->room_type }}">
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge badge-custom">
                            <i class="fas fa-star"></i> {{ $room->room_stars }}
                        </span>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $room->room_type }}</h5>
                    <p class="card-text">
                        <i class="fas fa-eye"></i> {{ $room->room_view }}
                    </p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5 mb-0 text-primary">
                                ${{ number_format($room->price_per_night, 2) }}
                                <small class="text-muted">/night</small>
                            </span>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('rooms.show', $room) }}" class="btn btn-outline-primary">
                                <i class="fas fa-info-circle"></i> View Details
                            </a>
                            <a href="{{ route('bookings.create', $room) }}" class="btn btn-success">
                                <i class="fas fa-calendar-plus"></i> Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
