@extends('layouts.app')

@section('title', 'Manage Rooms - Admin')

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
                <h1><i class="fas fa-bed"></i> Manage Rooms</h1>
                <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Room
                </a>
            </div>

            <!-- Rooms Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Room Number</th>
                                    <th>Type</th>
                                    <th>Price/Night</th>
                                    <th>Stars</th>
                                    <th>View</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms as $room)
                                <tr>
                                    <td>
                                        <img src="{{ asset('images/rooms/' . ($room->image ?? 'default.jpg')) }}" 
                                             class="rounded" 
                                             style="width: 60px; height: 45px; object-fit: cover;"
                                             alt="{{ $room->room_type }}">
                                    </td>
                                    <td>
                                        <strong>{{ $room->room_number }}</strong>
                                    </td>
                                    <td>{{ $room->room_type }}</td>
                                    <td>
                                        <span class="text-success">
                                            ${{ number_format($room->price_per_night, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $room->room_stars ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </td>
                                    <td>{{ $room->room_view }}</td>
                                    <td>
                                        @if($room->availability)
                                            <span class="badge bg-success">Available</span>
                                        @else
                                            <span class="badge bg-danger">Not Available</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('rooms.show', $room) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.rooms.edit', $room) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.rooms.destroy', $room) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this room?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-bed fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No rooms found</h5>
                                        <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Add First Room
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($rooms->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $rooms->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Room Statistics -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary">{{ $rooms->total() }}</h3>
                            <p class="mb-0">Total Rooms</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-success">{{ $rooms->where('availability', true)->count() }}</h3>
                            <p class="mb-0">Available</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-danger">{{ $rooms->where('availability', false)->count() }}</h3>
                            <p class="mb-0">Occupied</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-info">${{ number_format($rooms->avg('price_per_night'), 2) }}</h3>
                            <p class="mb-0">Avg. Price</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
