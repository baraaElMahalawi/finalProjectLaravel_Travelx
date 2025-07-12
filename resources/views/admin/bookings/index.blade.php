@extends('layouts.app')

@section('title', 'Manage Bookings - Admin')

@section('content')
<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            <div class="sidebar">
                <nav class="nav flex-column">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="{{ route('admin.rooms.index') }}">
                        <i class="fas fa-bed"></i> Rooms
                    </a>
                    <a class="nav-link active" href="{{ route('admin.bookings.index') }}">
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
                <h1><i class="fas fa-calendar-check"></i> All Bookings</h1>
                <a href="{{ route('admin.bookings.pending') }}" class="btn btn-warning">
                    <i class="fas fa-clock"></i> Pending Bookings
                </a>
            </div>

            <!-- Bookings Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Guest</th>
                                    <th>Room</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Guests</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                <tr>
                                    <td>
                                        <strong>#{{ $booking->id }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $booking->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $booking->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $booking->room->room_type }}</strong>
                                            <br>
                                            <small class="text-muted">Room {{ $booking->room->room_number }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $booking->checkin_date->format('M d, Y') }}</td>
                                    <td>{{ $booking->checkout_date->format('M d, Y') }}</td>
                                    <td>{{ $booking->guests }}</td>
                                    <td>
                                        <strong class="text-success">
                                            ${{ number_format($booking->getTotalPrice(), 2) }}
                                        </strong>
                                        <br>
                                        <small class="text-muted">{{ $booking->getTotalNights() }} nights</small>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($booking->status == 'confirmed') bg-success
                                            @elseif($booking->status == 'pending') bg-warning
                                            @else bg-danger
                                            @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($booking->status == 'pending')
                                            <div class="btn-group" role="group">
                                                <form method="POST" 
                                                      action="{{ route('admin.bookings.confirm', $booking) }}" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-success"
                                                            title="Confirm Booking">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" 
                                                      action="{{ route('admin.bookings.cancel', $booking) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger"
                                                            title="Cancel Booking">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif($booking->status == 'confirmed')
                                            <form method="POST" 
                                                  action="{{ route('admin.bookings.cancel', $booking) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to cancel this confirmed booking?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Cancel Booking">
                                                    <i class="fas fa-ban"></i> Cancel
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No bookings found</h5>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($bookings->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $bookings->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Booking Statistics -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary">{{ $bookings->total() }}</h3>
                            <p class="mb-0">Total Bookings</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-success">{{ $bookings->where('status', 'confirmed')->count() }}</h3>
                            <p class="mb-0">Confirmed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-warning">{{ $bookings->where('status', 'pending')->count() }}</h3>
                            <p class="mb-0">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-info">
                                ${{ number_format($bookings->where('status', 'confirmed')->sum(function($booking) { return $booking->getTotalPrice(); }), 2) }}
                            </h3>
                            <p class="mb-0">Total Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
