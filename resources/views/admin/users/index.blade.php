@extends('layouts.app')

@section('title', 'Users Management - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-users text-primary"></i> Users Management</h2>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            @if($users->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> All Users 
                            <span class="badge bg-primary">{{ $users->total() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Bookings</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>
                                                <strong>{{ $user->name }}</strong>
                                            </td>
                                            <td>{{ $user->username }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $user->bookings->count() }} bookings
                                                </span>
                                            </td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#userModal{{ $user->id }}">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- User Details Modal -->
                                        <div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-user"></i> User Details: {{ $user->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6><i class="fas fa-info-circle"></i> Basic Information</h6>
                                                                <p><strong>Name:</strong> {{ $user->name }}</p>
                                                                <p><strong>Username:</strong> {{ $user->username }}</p>
                                                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                                                <p><strong>Role:</strong> 
                                                                    <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                                                </p>
                                                                <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y H:i') }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6><i class="fas fa-chart-bar"></i> Statistics</h6>
                                                                <p><strong>Total Bookings:</strong> {{ $user->bookings->count() }}</p>
                                                                <p><strong>Confirmed Bookings:</strong> 
                                                                    {{ $user->bookings->where('status', 'confirmed')->count() }}
                                                                </p>
                                                                <p><strong>Pending Bookings:</strong> 
                                                                    {{ $user->bookings->where('status', 'pending')->count() }}
                                                                </p>
                                                                <p><strong>Cancelled Bookings:</strong> 
                                                                    {{ $user->bookings->where('status', 'cancelled')->count() }}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        @if($user->bookings->count() > 0)
                                                            <hr>
                                                            <h6><i class="fas fa-calendar"></i> Recent Bookings</h6>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Room</th>
                                                                            <th>Check-in</th>
                                                                            <th>Check-out</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($user->bookings->take(5) as $booking)
                                                                            <tr>
                                                                                <td>Room {{ $booking->room->room_number }}</td>
                                                                                <td>{{ \Carbon\Carbon::parse($booking->checkin_date)->format('M d, Y') }}</td>
                                                                                <td>{{ \Carbon\Carbon::parse($booking->checkout_date)->format('M d, Y') }}</td>
                                                                                <td>
                                                                                    @if($booking->status == 'confirmed')
                                                                                        <span class="badge bg-success">Confirmed</span>
                                                                                    @elseif($booking->status == 'pending')
                                                                                        <span class="badge bg-warning">Pending</span>
                                                                                    @else
                                                                                        <span class="badge bg-danger">Cancelled</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h4>No Users Found</h4>
                        <p class="text-muted">No users have registered yet.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
