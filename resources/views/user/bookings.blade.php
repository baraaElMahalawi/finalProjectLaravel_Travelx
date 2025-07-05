@extends('layouts.app')

@section('title', 'My Bookings - Travelx Hotel')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-calendar-check"></i> My Bookings
            </h1>
        </div>
    </div>

    <div class="row">
        @forelse($bookings as $booking)
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ asset('images/rooms/' . ($booking->room->image ?? 'default.jpg')) }}" 
                                 class="img-fluid rounded" 
                                 alt="{{ $booking->room->room_type }}">
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $booking->room->room_type }}</h5>
                                <span class="badge 
                                    @if($booking->status == 'confirmed') bg-success
                                    @elseif($booking->status == 'pending') bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                            
                            <p class="card-text mb-1">
                                <i class="fas fa-door-open"></i> Room {{ $booking->room->room_number }}
                            </p>
                            
                            <p class="card-text mb-1">
                                <i class="fas fa-calendar-check"></i> 
                                {{ $booking->checkin_date->format('M d, Y') }}
                            </p>
                            
                            <p class="card-text mb-1">
                                <i class="fas fa-calendar-times"></i> 
                                {{ $booking->checkout_date->format('M d, Y') }}
                            </p>
                            
                            <p class="card-text mb-1">
                                <i class="fas fa-users"></i> 
                                {{ $booking->guests }} {{ $booking->guests == 1 ? 'Guest' : 'Guests' }}
                            </p>
                            
                            <p class="card-text mb-2">
                                <i class="fas fa-moon"></i> 
                                {{ $booking->getTotalNights() }} {{ $booking->getTotalNights() == 1 ? 'Night' : 'Nights' }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-primary mb-0">
                                        Total: ${{ number_format($booking->getTotalPrice(), 2) }}
                                    </h6>
                                    <small class="text-muted">
                                        ${{ number_format($booking->room->price_per_night, 2) }}/night
                                    </small>
                                </div>
                                
                                <div>
                                    @if($booking->status == 'pending' || $booking->status == 'confirmed')
                                        <form method="POST" 
                                              action="{{ route('bookings.cancel', $booking) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('rooms.show', $booking->room) }}" 
                                       class="btn btn-sm btn-outline-primary ms-1">
                                        <i class="fas fa-eye"></i> View Room
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        <i class="fas fa-clock"></i> 
                        Booked on {{ $booking->created_at->format('M d, Y \a\t H:i') }}
                    </small>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                <h3 class="text-muted">No bookings yet</h3>
                <p class="text-muted mb-4">You haven't made any bookings yet. Start exploring our amazing rooms!</p>
                <a href="{{ route('rooms.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-bed"></i> Browse Rooms
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
    @endif

    <!-- Booking Statistics -->
    @if($bookings->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4"><i class="fas fa-chart-bar"></i> Booking Statistics</h3>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-primary">{{ $bookings->total() }}</h4>
                    <p class="mb-0">Total Bookings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-success">{{ $bookings->where('status', 'confirmed')->count() }}</h4>
                    <p class="mb-0">Confirmed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-warning">{{ $bookings->where('status', 'pending')->count() }}</h4>
                    <p class="mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h4 class="text-info">
                        ${{ number_format($bookings->where('status', 'confirmed')->sum(function($booking) { return $booking->getTotalPrice(); }), 2) }}
                    </h4>
                    <p class="mb-0">Total Spent</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
