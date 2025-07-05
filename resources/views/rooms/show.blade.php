@extends('layouts.app')

@section('title', $room->room_type . ' - Travelx Hotel')

@section('content')
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}">Rooms</a></li>
            <li class="breadcrumb-item active">{{ $room->room_type }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Room Image -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <img src="{{ asset('images/rooms/' . ($room->image ?? 'default.jpg')) }}" 
                     class="card-img-top" 
                     alt="{{ $room->room_type }}"
                     style="height: 400px; object-fit: cover;">
            </div>

            <!-- Room Description -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-info-circle"></i> Room Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-door-open"></i> Room Information</h6>
                            <ul class="list-unstyled">
                                <li><strong>Room Number:</strong> {{ $room->room_number }}</li>
                                <li><strong>Room Type:</strong> {{ $room->room_type }}</li>
                                <li><strong>View:</strong> {{ $room->room_view }}</li>
                                <li><strong>Pool Access:</strong> {{ $room->pool_type }}</li>
                                <li><strong>Star Rating:</strong> 
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $room->room_stars ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-concierge-bell"></i> Amenities</h6>
                            <div class="row">
                                <div class="col-6">
                                    <ul class="list-unstyled">
                                        <li>
                                            <i class="fas fa-wifi {{ $room->has_wifi ? 'text-success' : 'text-muted' }}"></i>
                                            WiFi
                                        </li>
                                        <li>
                                            <i class="fas fa-car {{ $room->has_parking ? 'text-success' : 'text-muted' }}"></i>
                                            Parking
                                        </li>
                                        <li>
                                            <i class="fas fa-plane {{ $room->has_airport_transfer ? 'text-success' : 'text-muted' }}"></i>
                                            Airport Transfer
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <ul class="list-unstyled">
                                        <li>
                                            <i class="fas fa-coffee {{ $room->has_coffee_maker ? 'text-success' : 'text-muted' }}"></i>
                                            Coffee Maker
                                        </li>
                                        <li>
                                            <i class="fas fa-glass-martini {{ $room->has_bar ? 'text-success' : 'text-muted' }}"></i>
                                            Mini Bar
                                        </li>
                                        <li>
                                            <i class="fas fa-utensils {{ $room->has_breakfast ? 'text-success' : 'text-muted' }}"></i>
                                            Breakfast
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Card -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header text-center">
                    <h4 class="mb-0">
                        <span class="text-primary">${{ number_format($room->price_per_night, 2) }}</span>
                        <small class="text-muted">/night</small>
                    </h4>
                </div>
                <div class="card-body">
                    @if($room->availability)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Available for booking
                        </div>

                        @auth
                            <div class="d-grid gap-2">
                                <a href="{{ route('bookings.create', $room) }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-calendar-plus"></i> Book This Room
                                </a>
                            </div>
                        @else
                            <div class="d-grid gap-2">
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Login to Book
                                </a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-user-plus"></i> Create Account
                                </a>
                            </div>
                        @endauth
                    @else
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> Currently not available
                        </div>
                        <button class="btn btn-secondary btn-lg w-100" disabled>
                            <i class="fas fa-ban"></i> Not Available
                        </button>
                    @endif

                    <hr>

                    <!-- Room Features -->
                    <h6><i class="fas fa-star"></i> Room Highlights</h6>
                    <ul class="list-unstyled">
                        @if($room->room_stars >= 4)
                            <li><i class="fas fa-crown text-warning"></i> Premium Room</li>
                        @endif
                        @if($room->has_wifi)
                            <li><i class="fas fa-wifi text-success"></i> Free WiFi</li>
                        @endif
                        @if($room->has_breakfast)
                            <li><i class="fas fa-utensils text-success"></i> Complimentary Breakfast</li>
                        @endif
                        @if($room->has_parking)
                            <li><i class="fas fa-car text-success"></i> Free Parking</li>
                        @endif
                        @if($room->has_airport_transfer)
                            <li><i class="fas fa-plane text-success"></i> Airport Transfer</li>
                        @endif
                    </ul>

                    <hr>

                    <!-- Contact Info -->
                    <div class="text-center">
                        <h6>Need Help?</h6>
                        <p class="mb-1">
                            <i class="fas fa-phone"></i> +1 (555) 123-4567
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-envelope"></i> reservations@travelx.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Rooms -->
    <div class="row mt-5">
        <div class="col-12">
            <h3><i class="fas fa-bed"></i> Similar Rooms</h3>
            <div class="row g-4">
                @foreach(\App\Models\Room::where('id', '!=', $room->id)->where('availability', true)->take(3)->get() as $similarRoom)
                <div class="col-md-4">
                    <div class="card room-card h-100">
                        <img src="{{ asset('images/rooms/' . ($similarRoom->image ?? 'default.jpg')) }}" 
                             class="card-img-top room-image" 
                             alt="{{ $similarRoom->room_type }}">
                        <div class="card-body">
                            <h6 class="card-title">{{ $similarRoom->room_type }}</h6>
                            <p class="card-text">
                                <i class="fas fa-star text-warning"></i> {{ $similarRoom->room_stars }} Stars
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 mb-0">${{ number_format($similarRoom->price_per_night, 2) }}/night</span>
                                <a href="{{ route('rooms.show', $similarRoom) }}" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
