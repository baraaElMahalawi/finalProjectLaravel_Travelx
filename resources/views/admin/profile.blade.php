@extends('layouts.app')

@section('title', 'Admin Profile - Travelx Hotel')

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
                    <a class="nav-link" href="{{ route('admin.bookings.index') }}">
                        <i class="fas fa-calendar-check"></i> Bookings
                    </a>
                    <a class="nav-link" href="{{ route('admin.bookings.pending') }}">
                        <i class="fas fa-clock"></i> Pending Bookings
                    </a>
                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a class="nav-link active" href="{{ route('admin.profile') }}">
                        <i class="fas fa-user-cog"></i> Profile
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0"><i class="fas fa-user-cog"></i> Admin Profile</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">
                                                <i class="fas fa-user"></i> Full Name
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name', auth()->user()->name) }}" 
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">
                                                <i class="fas fa-at"></i> Username
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('username') is-invalid @enderror" 
                                                   id="username" 
                                                   name="username" 
                                                   value="{{ old('username', auth()->user()->username) }}" 
                                                   required>
                                            @error('username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope"></i> Email Address
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', auth()->user()->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4">

                                <h5><i class="fas fa-lock"></i> Change Password</h5>
                                <p class="text-muted">Leave blank if you don't want to change your password</p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Admin Information -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Admin Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Role:</strong> {{ ucfirst(auth()->user()->role) }}</p>
                                    <p><strong>Admin Since:</strong> {{ auth()->user()->created_at->format('F d, Y') }}</p>
                                    <p><strong>Last Login:</strong> {{ now()->format('F d, Y H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Rooms:</strong> {{ \App\Models\Room::count() }}</p>
                                    <p><strong>Total Bookings:</strong> {{ \App\Models\Booking::count() }}</p>
                                    <p><strong>Total Users:</strong> {{ \App\Models\User::where('role', 'user')->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-server"></i> System Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Available Rooms:</span>
                                        <span class="badge bg-success">{{ \App\Models\Room::where('availability', true)->count() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Pending Bookings:</span>
                                        <span class="badge bg-warning">{{ \App\Models\Booking::where('status', 'pending')->count() }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Confirmed Bookings:</span>
                                        <span class="badge bg-success">{{ \App\Models\Booking::where('status', 'confirmed')->count() }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>System Status:</span>
                                        <span class="badge bg-success">Online</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
