@extends('layouts.app')

@section('title', 'Pending Bookings - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-clock text-warning"></i> Pending Bookings</h2>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            @if($bookings->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Guest</th>
                                        <th>Room</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Guests</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->id }}</td>
                                            <td>
                                                <strong>{{ $booking->user->name }}</strong><br>
                                                <small class="text-muted">{{ $booking->user->email }}</small>
                                            </td>
                                            <td>
                                                <strong>Room {{ $booking->room->room_number }}</strong><br>
                                                <small class="text-muted">{{ $booking->room->room_type }}</small>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($booking->checkin_date)->format('M d, Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($booking->checkout_date)->format('M d, Y') }}</td>
                                            <td>{{ $booking->guests }}</td>
                                            <td>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" 
                                                                onclick="return confirm('Confirm this booking?')">
                                                            <i class="fas fa-check"></i> Confirm
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('Cancel this booking?')">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                    </form>
                                                    
                                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $bookings->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h4>No Pending Bookings</h4>
                        <p class="text-muted">All bookings have been processed.</p>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> View All Bookings
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
