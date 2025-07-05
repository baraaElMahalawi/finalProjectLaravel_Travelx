@extends('layouts.app')

@section('title', 'Book Room - Travelx Hotel')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-calendar-plus"></i> Book Your Stay</h4>
                </div>
                <div class="card-body">
                    <!-- Room Summary -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{ asset('images/rooms/' . ($room->image ?? 'default.jpg')) }}" 
                                     class="img-fluid rounded" 
                                     alt="{{ $room->room_type }}">
                            </div>
                            <div class="col-md-8">
                                <h5>{{ $room->room_type }}</h5>
                                <p class="mb-1">
                                    <i class="fas fa-door-open"></i> Room {{ $room->room_number }}
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-eye"></i> {{ $room->room_view }}
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-star text-warning"></i> {{ $room->room_stars }} Stars
                                </p>
                                <h6 class="text-primary mt-2">
                                    ${{ number_format($room->price_per_night, 2) }} <small class="text-muted">/night</small>
                                </h6>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Booking Form -->
                    <form method="POST" action="{{ route('bookings.store') }}">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="checkin_date" class="form-label">
                                        <i class="fas fa-calendar-check"></i> Check-in Date
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('checkin_date') is-invalid @enderror" 
                                           id="checkin_date" 
                                           name="checkin_date"
                                           value="{{ old('checkin_date', date('Y-m-d')) }}"
                                           min="{{ date('Y-m-d') }}"
                                           required>
                                    @error('checkin_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="checkout_date" class="form-label">
                                        <i class="fas fa-calendar-check"></i> Check-out Date
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('checkout_date') is-invalid @enderror" 
                                           id="checkout_date" 
                                           name="checkout_date"
                                           value="{{ old('checkout_date', date('Y-m-d', strtotime('+1 day'))) }}"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           required>
                                    @error('checkout_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="guests" class="form-label">
                                <i class="fas fa-users"></i> Number of Guests
                            </label>
                            <select class="form-select @error('guests') is-invalid @enderror" 
                                    id="guests" 
                                    name="guests" 
                                    required>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('guests') == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}
                                    </option>
                                @endfor
                            </select>
                            @error('guests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Special Requests -->
                        <div class="mb-3">
                            <label for="special_requests" class="form-label">
                                <i class="fas fa-comment"></i> Special Requests (Optional)
                            </label>
                            <textarea class="form-control" 
                                      id="special_requests" 
                                      name="special_requests" 
                                      rows="3" 
                                      placeholder="Any special requests or preferences?">{{ old('special_requests') }}</textarea>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('terms') is-invalid @enderror" 
                                       type="checkbox" 
                                       id="terms" 
                                       name="terms" 
                                       required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a>
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-calendar-check"></i> Confirm Booking
                            </button>
                            <a href="{{ route('rooms.show', $room) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Room Details
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Booking Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Booking Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i> Free cancellation up to 24 hours before check-in
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i> No prepayment needed - pay at the hotel
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i> Confirmation will be sent to your email
                        </li>
                        <li>
                            <i class="fas fa-check text-success"></i> 24/7 customer support available
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Ensure checkout date is after checkin date
    document.getElementById('checkin_date').addEventListener('change', function() {
        const checkinDate = new Date(this.value);
        const checkoutInput = document.getElementById('checkout_date');
        const checkoutDate = new Date(checkoutInput.value);
        
        if (checkoutDate <= checkinDate) {
            const nextDay = new Date(checkinDate);
            nextDay.setDate(nextDay.getDate() + 1);
            checkoutInput.value = nextDay.toISOString().split('T')[0];
        }
        
        checkoutInput.min = new Date(checkinDate.getTime() + 86400000).toISOString().split('T')[0];
    });
</script>
@endpush
@endsection
