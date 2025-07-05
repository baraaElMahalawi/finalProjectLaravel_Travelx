@extends('layouts.app')

@section('title', 'Booking Details - Travelx Hotel')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-calendar-check"></i> Booking #{{ $booking->id }}
                    </h4>
                    <span class="badge 
                        @if($booking->status == 'confirmed') bg-success
                        @elseif($booking->status == 'pending') bg-warning
                        @else bg-danger
                        @endif badge-lg">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ asset('images/rooms/' . ($booking->room->image ?? 'default.jpg')) }}" 
                                 class="img-fluid rounded" 
                                 alt="{{ $booking->room->room_type }}">
                        </div>
                        <div class="col-md-8">
                            <h5>{{ $booking->room->room_type }}</h5>
                            <p class="mb-2">
                                <i class="fas fa-door-open"></i> Room {{ $booking->room->room_number }}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-eye"></i> {{ $booking->room->room_view }}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-star text-warning"></i> {{ $booking->room->room_stars }} Stars
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-dollar-sign"></i> ${{ number_format($booking->room->price_per_night, 2) }} per night
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle"></i> Booking Details</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Guest Name:</strong></td>
                                    <td>{{ $booking->user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $booking->user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Check-in:</strong></td>
                                    <td>{{ $booking->checkin_date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Check-out:</strong></td>
                                    <td>{{ $booking->checkout_date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Guests:</strong></td>
                                    <td>{{ $booking->guests }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Nights:</strong></td>
                                    <td>{{ $booking->getTotalNights() }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-calculator"></i> Price Breakdown</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td>Room Rate:</td>
                                    <td>${{ number_format($booking->room->price_per_night, 2) }} / night</td>
                                </tr>
                                <tr>
                                    <td>Number of Nights:</td>
                                    <td>{{ $booking->getTotalNights() }}</td>
                                </tr>
                                <tr>
                                    <td>Subtotal:</td>
                                    <td>${{ number_format($booking->getTotalPrice(), 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Taxes & Fees:</td>
                                    <td>$0.00</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Total Amount:</strong></td>
                                    <td><strong class="text-success">${{ number_format($booking->getTotalPrice(), 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($booking->status == 'pending')
                        <div class="alert alert-warning">
                            <i class="fas fa-clock"></i> Your booking is pending confirmation. We'll notify you once it's confirmed.
                        </div>
                    @elseif($booking->status == 'confirmed')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Your booking is confirmed! We look forward to welcoming you.
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> This booking has been cancelled.
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Bookings
                                </a>
                            @else
                                <a href="{{ route('user.bookings') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to My Bookings
                                </a>
                            @endif
                        </div>
                        
                        <div>
                            @if($booking->status == 'pending' || $booking->status == 'confirmed')
                                @if(auth()->user()->isAdmin())
                                    @if($booking->status == 'pending')
                                        <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" class="d-inline me-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check"></i> Confirm Booking
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" 
                                          action="{{ route('admin.bookings.cancel', $booking) }}" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-ban"></i> Cancel Booking
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" 
                                          action="{{ route('bookings.cancel', $booking) }}" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fas fa-times"></i> Cancel Booking
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        <i class="fas fa-clock"></i> 
                        Booking created on {{ $booking->created_at->format('F d, Y \a\t H:i A') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
