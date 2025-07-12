# Travelx Hotel API Documentation

## Overview
This is a comprehensive REST API for the Travelx Hotel booking system built with Laravel. The API provides endpoints for user authentication, room management, and booking operations.

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication
The API uses Laravel Sanctum for authentication. After login, you'll receive a Bearer token that must be included in the Authorization header for protected endpoints.

```
Authorization: Bearer {your-token-here}
```

## Response Format
All API responses follow this format:

### Success Response
```json
{
    "success": true,
    "data": {...},
    "message": "Optional success message"
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {...} // Optional validation errors
}
```

## Endpoints

### Authentication

#### Register User
```http
POST /api/v1/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "username": "johndoe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "username": "johndoe",
            "email": "john@example.com",
            "role": "user"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

#### Login
```http
POST /api/v1/login
```

**Request Body:**
```json
{
    "email": "admin@gmail.com",
    "password": "admin123"
}
```

#### Logout
```http
POST /api/v1/logout
```
*Requires authentication*

#### Get Profile
```http
GET /api/v1/profile
```
*Requires authentication*

#### Update Profile
```http
PUT /api/v1/profile
```
*Requires authentication*

**Request Body:**
```json
{
    "name": "Updated Name",
    "username": "newusername",
    "email": "newemail@example.com",
    "password": "newpassword",
    "password_confirmation": "newpassword"
}
```

### Rooms

#### Get All Rooms
```http
GET /api/v1/rooms
```

**Query Parameters:**
- `available` (boolean): Filter by availability
- `room_type` (string): Filter by room type
- `min_price` (number): Minimum price filter
- `max_price` (number): Maximum price filter
- `has_wifi` (boolean): Filter by WiFi availability
- `has_parking` (boolean): Filter by parking availability
- `has_breakfast` (boolean): Filter by breakfast availability
- `sort_by` (string): Sort by field (price_per_night, room_stars, created_at)
- `sort_order` (string): Sort order (asc, desc)
- `per_page` (number): Items per page (default: 10)

**Example:**
```http
GET /api/v1/rooms?available=true&min_price=100&max_price=300&sort_by=price_per_night&sort_order=asc
```

#### Get Single Room
```http
GET /api/v1/rooms/{id}
```

#### Check Room Availability
```http
POST /api/v1/rooms/available
```

**Request Body:**
```json
{
    "checkin_date": "2024-02-01",
    "checkout_date": "2024-02-05"
}
```

#### Create Room (Admin Only)
```http
POST /api/v1/admin/rooms
```
*Requires admin authentication*

**Request Body:**
```json
{
    "room_number": "301",
    "room_type": "Deluxe Suite",
    "price_per_night": 250.00,
    "availability": true,
    "image": "room301.jpg",
    "room_view": "Sea View",
    "pool_type": "Private Pool",
    "room_stars": 5,
    "has_parking": true,
    "has_airport_transfer": true,
    "has_wifi": true,
    "has_coffee_maker": true,
    "has_bar": true,
    "has_breakfast": true
}
```

#### Update Room (Admin Only)
```http
PUT /api/v1/admin/rooms/{id}
```
*Requires admin authentication*

#### Delete Room (Admin Only)
```http
DELETE /api/v1/admin/rooms/{id}
```
*Requires admin authentication*

### Bookings

#### Get User Bookings
```http
GET /api/v1/bookings
```
*Requires authentication*

**Query Parameters:**
- `status` (string): Filter by status (pending, confirmed, cancelled)
- `from_date` (date): Filter from check-in date
- `to_date` (date): Filter to checkout date
- `per_page` (number): Items per page (default: 10)

#### Create Booking
```http
POST /api/v1/bookings
```
*Requires authentication*

**Request Body:**
```json
{
    "room_id": 1,
    "checkin_date": "2024-02-01",
    "checkout_date": "2024-02-05",
    "guests": 2
}
```

#### Get Single Booking
```http
GET /api/v1/bookings/{id}
```
*Requires authentication*

#### Cancel Booking
```http
PATCH /api/v1/bookings/{id}/cancel
```
*Requires authentication*

### Admin Booking Management

#### Get All Bookings (Admin)
```http
GET /api/v1/admin/bookings
```
*Requires admin authentication*

**Query Parameters:**
- `status` (string): Filter by status
- `user_id` (number): Filter by user
- `room_id` (number): Filter by room
- `from_date` (date): Filter from date
- `to_date` (date): Filter to date
- `per_page` (number): Items per page (default: 15)

#### Get Pending Bookings (Admin)
```http
GET /api/v1/admin/bookings/pending
```
*Requires admin authentication*

#### Get Booking Statistics (Admin)
```http
GET /api/v1/admin/bookings/statistics
```
*Requires admin authentication*

**Response:**
```json
{
    "success": true,
    "data": {
        "total_bookings": 150,
        "pending_bookings": 12,
        "confirmed_bookings": 120,
        "cancelled_bookings": 18,
        "today_bookings": 5,
        "this_month_bookings": 45
    }
}
```

#### Confirm Booking (Admin)
```http
PATCH /api/v1/admin/bookings/{id}/confirm
```
*Requires admin authentication*

#### Cancel Booking (Admin)
```http
PATCH /api/v1/admin/bookings/{id}/cancel
```
*Requires admin authentication*

## Testing the API

### Using cURL

#### Register a new user:
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "username": "testuser",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### Login:
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@gmail.com",
    "password": "admin123"
  }'
```

#### Get rooms with authentication:
```bash
curl -X GET http://localhost:8000/api/v1/rooms \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

#### Create a booking:
```bash
curl -X POST http://localhost:8000/api/v1/bookings \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "room_id": 1,
    "checkin_date": "2024-02-01",
    "checkout_date": "2024-02-05",
    "guests": 2
  }'
```

### Using Postman

1. Import the API endpoints into Postman
2. Set up environment variables:
   - `base_url`: http://localhost:8000/api/v1
   - `token`: Your authentication token
3. Use `{{base_url}}` and `{{token}}` in your requests

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## Rate Limiting

The API includes rate limiting to prevent abuse. Default limits:
- 60 requests per minute for authenticated users
- 30 requests per minute for guest users

## Demo Accounts

### Admin Account
- Email: `admin@gmail.com`
- Password: `admin123`

### Test User Account
- Email: `test@example.com`
- Password: `password`

## Installation & Setup

1. Install Laravel Sanctum (if not already installed):
```bash
composer require laravel/sanctum
```

2. Publish Sanctum configuration:
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

3. Run migrations:
```bash
php artisan migrate
```

4. Add Sanctum middleware to `app/Http/Kernel.php`:
```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

## Support

For API support or questions, please contact the development team or refer to the Laravel Sanctum documentation.
