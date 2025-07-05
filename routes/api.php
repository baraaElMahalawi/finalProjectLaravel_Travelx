<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\RoomApiController;
use App\Http\Controllers\Api\BookingApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::prefix('v1')->group(function () {
    
    // Authentication routes
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login']);
    
    // Public room routes
    Route::get('/rooms', [RoomApiController::class, 'index']);
    Route::get('/rooms/{id}', [RoomApiController::class, 'show']);
    Route::post('/rooms/available', [RoomApiController::class, 'available']);
    
});

// Protected API routes (require authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/profile', [AuthApiController::class, 'profile']);
    Route::put('/profile', [AuthApiController::class, 'updateProfile']);
    
    // User booking routes
    Route::get('/bookings', [BookingApiController::class, 'index']);
    Route::post('/bookings', [BookingApiController::class, 'store']);
    Route::get('/bookings/{id}', [BookingApiController::class, 'show']);
    Route::patch('/bookings/{id}/cancel', [BookingApiController::class, 'cancel']);
    
    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        
        // Room management
        Route::post('/rooms', [RoomApiController::class, 'store']);
        Route::put('/rooms/{id}', [RoomApiController::class, 'update']);
        Route::delete('/rooms/{id}', [RoomApiController::class, 'destroy']);
        
        // Booking management
        Route::get('/bookings', [BookingApiController::class, 'adminIndex']);
        Route::get('/bookings/pending', [BookingApiController::class, 'pending']);
        Route::get('/bookings/statistics', [BookingApiController::class, 'statistics']);
        Route::patch('/bookings/{id}/confirm', [BookingApiController::class, 'confirm']);
        Route::patch('/bookings/{id}/cancel', [BookingApiController::class, 'adminCancel']);
        
    });
    
});

// API Documentation route
Route::get('/docs', function () {
    return response()->json([
        'message' => 'Travelx Hotel API Documentation',
        'version' => '1.0',
        'endpoints' => [
            'authentication' => [
                'POST /api/v1/register' => 'Register new user',
                'POST /api/v1/login' => 'Login user',
                'POST /api/v1/logout' => 'Logout user (requires auth)',
                'GET /api/v1/profile' => 'Get user profile (requires auth)',
                'PUT /api/v1/profile' => 'Update user profile (requires auth)',
            ],
            'rooms' => [
                'GET /api/v1/rooms' => 'Get all rooms with filters',
                'GET /api/v1/rooms/{id}' => 'Get single room',
                'POST /api/v1/rooms/available' => 'Check room availability for dates',
                'POST /api/v1/admin/rooms' => 'Create room (admin only)',
                'PUT /api/v1/admin/rooms/{id}' => 'Update room (admin only)',
                'DELETE /api/v1/admin/rooms/{id}' => 'Delete room (admin only)',
            ],
            'bookings' => [
                'GET /api/v1/bookings' => 'Get user bookings (requires auth)',
                'POST /api/v1/bookings' => 'Create booking (requires auth)',
                'GET /api/v1/bookings/{id}' => 'Get single booking (requires auth)',
                'PATCH /api/v1/bookings/{id}/cancel' => 'Cancel booking (requires auth)',
                'GET /api/v1/admin/bookings' => 'Get all bookings (admin only)',
                'GET /api/v1/admin/bookings/pending' => 'Get pending bookings (admin only)',
                'GET /api/v1/admin/bookings/statistics' => 'Get booking statistics (admin only)',
                'PATCH /api/v1/admin/bookings/{id}/confirm' => 'Confirm booking (admin only)',
                'PATCH /api/v1/admin/bookings/{id}/cancel' => 'Cancel booking (admin only)',
            ],
        ],
        'authentication' => [
            'type' => 'Bearer Token',
            'header' => 'Authorization: Bearer {token}',
            'note' => 'Include the token received from login in the Authorization header'
        ],
        'response_format' => [
            'success' => [
                'success' => true,
                'data' => '...',
                'message' => 'Optional success message'
            ],
            'error' => [
                'success' => false,
                'message' => 'Error message',
                'errors' => 'Validation errors (optional)'
            ]
        ]
    ]);
});
