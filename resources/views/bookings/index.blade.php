@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>
                        My Bookings
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($bookings->count() > 0)
                        <div class="row">
                            @foreach($bookings as $booking)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        @if($booking->room->image)
                                            <img src="{{ asset('images/rooms/' . $booking->room->image) }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $booking->room->room_type }}"
                                                 style="height: 200px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('images/rooms/default.jpg') }}" 
                                                 class="card-img-top" 
                                                 alt="Room Image"
                                                 style="height: 200px; object-fit: cover;">
                                        @endif
                                        
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title">
                                                Room {{ $booking->room->room_number }}
                                                <span class="badge bg-secondary ms-2">{{ $booking->room->room_type }}</span>
                                            </h5>
                                            
                                            <div class="mb-3">
                                                <span class="badge 
                                                    @if($booking->status === 'confirmed') bg-success
                                                    @elseif($booking->status === 'pending') bg-warning
                                                    @else bg-danger
                                                    @endif">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </div>

                                            <div class="booking-details mb-3">
                                                <p class="mb-2">
                                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                                    <strong>Check-in:</strong> {{ \Carbon\Carbon::parse($booking->checkin_date)->format('M d, Y') }}
                                                </p>
                                                <p class="mb-2">
                                                    <i class="fas fa-calendar-alt text-danger me-2"></i>
                                                    <strong>Check-out:</strong> {{ \Carbon\Carbon::parse($booking->checkout_date)->format('M d, Y') }}
                                                </p>
                                                <p class="mb-2">
                                                    <i class="fas fa-users text-info me-2"></i>
                                                    <strong>Guests:</strong> {{ $booking->guests }}
                                                </p>
                                                <p class="mb-2">
                                                    <i class="fas fa-dollar-sign text-success me-2"></i>
                                                    <strong>Price per night:</strong> ${{ number_format($booking->room->price_per_night, 2) }}
                                                </p>
                                                @php
                                                    $nights = \Carbon\Carbon::parse($booking->checkin_date)->diffInDays(\Carbon\Carbon::parse($booking->checkout_date));
                                                    $totalPrice = $nights * $booking->room->price_per_night;
                                                @endphp
                                                <p class="mb-2">
                                                    <i class="fas fa-moon text-dark me-2"></i>
                                                    <strong>Total nights:</strong> {{ $nights }}
                                                </p>
                                                <p class="mb-0">
                                                    <i class="fas fa-calculator text-warning me-2"></i>
                                                    <strong>Total price:</strong> ${{ number_format($totalPrice, 2) }}
                                                </p>
                                            </div>

                                            <div class="mt-auto">
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('bookings.show', $booking) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye me-1"></i>
                                                        View Details
                                                    </a>
                                                    
                                                    @if($booking->status !== 'cancelled')
                                                        <form action="{{ route('bookings.cancel', $booking) }}" 
                                                              method="POST" 
                                                              onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                                <i class="fas fa-times me-1"></i>
                                                                Cancel Booking
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card-footer bg-light">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Booked on {{ $booking->created_at->format('M d, Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $bookings->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No Bookings Found</h4>
                            <p class="text-muted mb-4">You haven't made any bookings yet.</p>
                            <a href="{{ route('rooms.index') }}" class="btn btn-primary">
                                <i class="fas fa-bed me-2"></i>
                                Browse Rooms
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.booking-details p {
    font-size: 0.9rem;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.75rem;
}

.btn-sm {
    font-size: 0.8rem;
}

.card-footer {
    border-top: 1px solid rgba(0,0,0,.125);
}
</style>
@endsection
