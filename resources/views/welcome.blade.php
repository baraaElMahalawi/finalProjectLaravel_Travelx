@extends('layouts.app')

@section('title', 'Welcome to Travelx Hotel')

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <h1 class="display-4 mb-4">Welcome to Travelx Hotel</h1>
        <p class="lead mb-4">Experience luxury and comfort at its finest. Book your perfect stay with us.</p>
        <a href="{{ route('rooms.index') }}" class="btn btn-light btn-lg">
            <i class="fas fa-bed"></i> Explore Our Rooms
        </a>
    </div>
</div>

<!-- Features Section -->
<div class="container my-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-concierge-bell fa-3x mb-3 text-primary"></i>
                    <h4>Luxury Service</h4>
                    <p>Experience world-class service and hospitality from our dedicated staff.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-swimming-pool fa-3x mb-3 text-primary"></i>
                    <h4>Premium Amenities</h4>
                    <p>Enjoy our premium facilities including pools, spa, and fitness center.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-map-marked-alt fa-3x mb-3 text-primary"></i>
                    <h4>Prime Location</h4>
                    <p>Located in the heart of the city with easy access to major attractions.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Rooms Section -->
<div class="container my-5">
    <h2 class="text-center mb-4">Featured Rooms</h2>
    <div class="row g-4">
        @foreach(\App\Models\Room::where('room_stars', '>=', 4)->take(3)->get() as $room)
        <div class="col-md-4">
            <div class="card room-card h-100">
                <img src="{{ asset('images/rooms/' . ($room->image ?? 'default.jpg')) }}" 
                     class="card-img-top room-image" 
                     alt="{{ $room->room_type }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $room->room_type }}</h5>
                    <p class="card-text">
                        <i class="fas fa-star text-warning"></i> 
                        {{ $room->room_stars }} Stars
                    </p>
                    <p class="card-text">
                        <i class="fas fa-eye"></i> {{ $room->room_view }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 mb-0">${{ number_format($room->price_per_night, 2) }}/night</span>
                        <a href="{{ route('rooms.show', $room) }}" class="btn btn-primary">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Why Choose Us Section -->
<div class="container my-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2 class="mb-4">Why Choose Travelx Hotel?</h2>
            <div class="mb-3">
                <h5><i class="fas fa-check-circle text-success"></i> Best Price Guarantee</h5>
                <p>We offer competitive rates and best price guarantees for all our rooms.</p>
            </div>
            <div class="mb-3">
                <h5><i class="fas fa-check-circle text-success"></i> Free Cancellation</h5>
                <p>Flexible booking options with free cancellation on most rooms.</p>
            </div>
            <div class="mb-3">
                <h5><i class="fas fa-check-circle text-success"></i> 24/7 Customer Support</h5>
                <p>Our dedicated support team is available round the clock to assist you.</p>
            </div>
            <div class="mb-3">
                <h5><i class="fas fa-check-circle text-success"></i> Safe & Secure</h5>
                <p>We prioritize your safety and security throughout your stay.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-4">
                    <h4 class="text-center mb-4">Quick Booking</h4>
                    <p class="text-center">
                        Ready to experience luxury? Book your stay now and get exclusive offers!
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('rooms.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-search"></i> Find Rooms
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-user-plus"></i> Create Account
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="container my-5">
    <h2 class="text-center mb-4">What Our Guests Say</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="card-text">"Amazing experience! The staff was very friendly and the rooms were luxurious. Will definitely come back!"</p>
                    <footer class="blockquote-footer mt-3">John Doe</footer>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="card-text">"Perfect location, excellent amenities, and outstanding service. Couldn't ask for more!"</p>
                    <footer class="blockquote-footer mt-3">Jane Smith</footer>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="card-text">"The best hotel experience I've ever had. The attention to detail is remarkable!"</p>
                    <footer class="blockquote-footer mt-3">Mike Johnson</footer>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
