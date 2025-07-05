@extends('layouts.app')

@section('title', 'Our Rooms - Travelx Hotel')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-5">
                <i class="fas fa-bed"></i> Our Luxury Rooms
            </h1>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('rooms.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="room_type" class="form-label">Room Type</label>
                                <select class="form-select" id="room_type" name="room_type">
                                    <option value="">All Types</option>
                                    <option value="Standard Single" {{ request('room_type') == 'Standard Single' ? 'selected' : '' }}>Standard Single</option>
                                    <option value="Standard Double" {{ request('room_type') == 'Standard Double' ? 'selected' : '' }}>Standard Double</option>
                                    <option value="Deluxe Double" {{ request('room_type') == 'Deluxe Double' ? 'selected' : '' }}>Deluxe Double</option>
                                    <option value="Suite" {{ request('room_type') == 'Suite' ? 'selected' : '' }}>Suite</option>
                                    <option value="Presidential Suite" {{ request('room_type') == 'Presidential Suite' ? 'selected' : '' }}>Presidential Suite</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="min_price" class="form-label">Min Price</label>
                                <input type="number" class="form-control" id="min_price" name="min_price" 
                                       value="{{ request('min_price') }}" placeholder="0">
                            </div>
                            <div class="col-md-3">
                                <label for="max_price" class="form-label">Max Price</label>
                                <input type="number" class="form-control" id="max_price" name="max_price" 
                                       value="{{ request('max_price') }}" placeholder="1000">
                            </div>
                            <div class="col-md-3">
                                <label for="stars" class="form-label">Stars</label>
                                <select class="form-select" id="stars" name="stars">
                                    <option value="">All Stars</option>
                                    <option value="5" {{ request('stars') == '5' ? 'selected' : '' }}>5 Stars</option>
                                    <option value="4" {{ request('stars') == '4' ? 'selected' : '' }}>4 Stars</option>
                                    <option value="3" {{ request('stars') == '3' ? 'selected' : '' }}>3 Stars</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms Grid -->
    <div class="row g-4">
        @forelse($rooms as $room)
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
                    @if(!$room->availability)
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-danger">Not Available</span>
                        </div>
                    @endif
                </div>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $room->room_type }}</h5>
                    <p class="card-text">
                        <i class="fas fa-door-open"></i> Room {{ $room->room_number }}
                    </p>
                    <p class="card-text">
                        <i class="fas fa-eye"></i> {{ $room->room_view }}
                    </p>
                    
                    <!-- Amenities -->
                    <div class="mb-3">
                        @foreach($room->getAmenities() as $amenity)
                            <span class="amenity-badge">{{ $amenity }}</span>
                        @endforeach
                    </div>
                    
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h4 mb-0 text-primary">
                                ${{ number_format($room->price_per_night, 2) }}
                                <small class="text-muted">/night</small>
                            </span>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('rooms.show', $room) }}" class="btn btn-outline-primary">
                                <i class="fas fa-info-circle"></i> View Details
                            </a>
                            @if($room->availability)
                                @auth
                                    <a href="{{ route('bookings.create', $room) }}" class="btn btn-success">
                                        <i class="fas fa-calendar-plus"></i> Book Now
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-success">
                                        <i class="fas fa-sign-in-alt"></i> Login to Book
                                    </a>
                                @endauth
                            @else
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-ban"></i> Not Available
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-bed fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">No rooms found</h3>
                <p class="text-muted">Try adjusting your filters or check back later.</p>
                <a href="{{ route('rooms.index') }}" class="btn btn-primary">
                    <i class="fas fa-refresh"></i> View All Rooms
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($rooms->hasPages())
    <div class="row mt-5">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $rooms->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
