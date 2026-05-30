@extends('layouts.app')

@section('title', 'Login - Travelx Hotel')

@section('content')
<div class="container">
    <div class="row justify-content-center" style="margin-top: 100px;">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4><i class="fas fa-sign-in-alt"></i> Login to Travelx</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email Address
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus
                                   placeholder="Enter your email">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="Enter your password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-0">Don't have an account? 
                            <a href="{{ route('register') }}" class="text-decoration-none">
                                <strong>Register here</strong>
                            </a>
                        </p>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-2"><i class="fas fa-info-circle"></i> Demo Accounts:</h6>
                        <small class="text-muted">
                            <strong>Admin:</strong> admin@gmail.com / admin123<br>
                            <strong>User:</strong> test@example.com / password
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
