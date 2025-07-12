@extends('layouts.app')

@section('title', 'Add New Room - Admin')

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
                    <a class="nav-link active" href="{{ route('admin.rooms.index') }}">
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
                <h1><i class="fas fa-plus"></i> Add New Room</h1>
                <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Rooms
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.rooms.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-info-circle"></i> Basic Information</h5>
                                
                                <div class="mb-3">
                                    <label for="room_number" class="form-label">Room Number *</label>
                                    <input type="text" 
                                           class="form-control @error('room_number') is-invalid @enderror" 
                                           id="room_number" 
                                           name="room_number" 
                                           value="{{ old('room_number') }}" 
                                           required
                                           placeholder="e.g., 101, 202A">
                                    @error('room_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="room_type" class="form-label">Room Type *</label>
                                    <select class="form-select @error('room_type') is-invalid @enderror" 
                                            id="room_type" 
                                            name="room_type" 
                                            required>
                                        <option value="">Select Room Type</option>
                                        <option value="Standard Single" {{ old('room_type') == 'Standard Single' ? 'selected' : '' }}>Standard Single</option>
                                        <option value="Standard Double" {{ old('room_type') == 'Standard Double' ? 'selected' : '' }}>Standard Double</option>
                                        <option value="Deluxe Double" {{ old('room_type') == 'Deluxe Double' ? 'selected' : '' }}>Deluxe Double</option>
                                        <option value="Suite" {{ old('room_type') == 'Suite' ? 'selected' : '' }}>Suite</option>
                                        <option value="Presidential Suite" {{ old('room_type') == 'Presidential Suite' ? 'selected' : '' }}>Presidential Suite</option>
                                    </select>
                                    @error('room_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="price_per_night" class="form-label">Price per Night ($) *</label>
                                    <input type="number" 
                                           class="form-control @error('price_per_night') is-invalid @enderror" 
                                           id="price_per_night" 
                                           name="price_per_night" 
                                           value="{{ old('price_per_night') }}" 
                                           step="0.01"
                                           min="0"
                                           required
                                           placeholder="150.00">
                                    @error('price_per_night')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="room_view" class="form-label">Room View</label>
                                    <input type="text" 
                                           class="form-control @error('room_view') is-invalid @enderror" 
                                           id="room_view" 
                                           name="room_view" 
                                           value="{{ old('room_view') }}"
                                           placeholder="e.g., City View, Sea View">
                                    @error('room_view')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="pool_type" class="form-label">Pool Type</label>
                                    <select class="form-select @error('pool_type') is-invalid @enderror" 
                                            id="pool_type" 
                                            name="pool_type">
                                        <option value="">No Pool Access</option>
                                        <option value="Outdoor Pool" {{ old('pool_type') == 'Outdoor Pool' ? 'selected' : '' }}>Outdoor Pool</option>
                                        <option value="Indoor Pool" {{ old('pool_type') == 'Indoor Pool' ? 'selected' : '' }}>Indoor Pool</option>
                                        <option value="Private Pool" {{ old('pool_type') == 'Private Pool' ? 'selected' : '' }}>Private Pool</option>
                                    </select>
                                    @error('pool_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="room_stars" class="form-label">Star Rating *</label>
                                    <select class="form-select @error('room_stars') is-invalid @enderror" 
                                            id="room_stars" 
                                            name="room_stars" 
                                            required>
                                        <option value="">Select Stars</option>
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ old('room_stars') == $i ? 'selected' : '' }}>
                                                {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('room_stars')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Amenities and Image -->
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-concierge-bell"></i> Amenities & Image</h5>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Room Image</label>
                                    <input type="file" 
                                           class="form-control @error('image') is-invalid @enderror" 
                                           id="image" 
                                           name="image"
                                           accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Upload a high-quality image of the room (JPG, PNG, GIF)</small>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="availability" 
                                               name="availability"
                                               {{ old('availability') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="availability">
                                            <i class="fas fa-check-circle text-success"></i> Available for Booking
                                        </label>
                                    </div>
                                </div>

                                <h6 class="mb-2">Room Amenities</h6>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="has_wifi" 
                                                   name="has_wifi"
                                                   {{ old('has_wifi') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="has_wifi">
                                                <i class="fas fa-wifi"></i> WiFi
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="has_parking" 
                                                   name="has_parking"
                                                   {{ old('has_parking') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="has_parking">
                                                <i class="fas fa-car"></i> Parking
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="has_airport_transfer" 
                                                   name="has_airport_transfer"
                                                   {{ old('has_airport_transfer') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="has_airport_transfer">
                                                <i class="fas fa-plane"></i> Airport Transfer
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="has_coffee_maker" 
                                                   name="has_coffee_maker"
                                                   {{ old('has_coffee_maker') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="has_coffee_maker">
                                                <i class="fas fa-coffee"></i> Coffee Maker
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="has_bar" 
                                                   name="has_bar"
                                                   {{ old('has_bar') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="has_bar">
                                                <i class="fas fa-glass-martini"></i> Mini Bar
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="has_breakfast" 
                                                   name="has_breakfast"
                                                   {{ old('has_breakfast') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="has_breakfast">
                                                <i class="fas fa-utensils"></i> Breakfast
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Create Room
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
