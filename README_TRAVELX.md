# Travelx Hotel Booking System

A complete Laravel-based hotel booking system with admin panel and user management.

## Features

### User Features
- User registration and authentication
- Browse available rooms with filters
- View room details with amenities
- Book rooms with date selection
- Manage personal bookings
- Update profile information
- Cancel bookings

### Admin Features
- Admin dashboard with statistics
- Manage rooms (CRUD operations)
- View and manage all bookings
- Confirm or cancel bookings
- User management
- Admin profile management

## Database Schema

### Users Table
- id, name, username, email, password, role (admin/user)

### Rooms Table
- id, room_number, room_type, price_per_night, availability
- image, room_view, pool_type, room_stars
- amenities: has_parking, has_airport_transfer, has_wifi, has_coffee_maker, has_bar, has_breakfast

### Bookings Table
- id, user_id, room_id, checkin_date, checkout_date, guests, status

### UserPersonalRooms Table
- id, user_id, room_id (for future personal room assignments)

## Default Accounts

### Admin Account
- Email: admin@gmail.com
- Password: admin123

### Test User Account
- Email: test@example.com
- Password: password

## Installation & Setup

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure database
4. Run `php artisan key:generate`
5. Run `php artisan migrate`
6. Run `php artisan db:seed`
7. Run `php artisan serve`

## Sample Rooms

The system comes pre-populated with 5 sample rooms:
- Standard Single (Room 101) - $150/night
- Deluxe Double (Room 102) - $250/night
- Suite (Room 201) - $400/night
- Standard Double (Room 202) - $200/night
- Presidential Suite (Room 301) - $800/night

## Technology Stack

- Laravel 12
- PHP 8.2+
- MySQL/SQLite
- Bootstrap 5
- Font Awesome Icons
- Responsive Design

## Key Features Implemented

✅ User Authentication (Login/Register/Logout)
✅ Role-based Access Control (Admin/User)
✅ Room Management (CRUD)
✅ Booking System with Date Validation
✅ Admin Dashboard with Statistics
✅ User Dashboard
✅ Profile Management
✅ Responsive Design
✅ Beautiful UI with Gradients and Animations
✅ Form Validation
✅ Database Relationships
✅ Booking Status Management (Pending/Confirmed/Cancelled)

## Routes

### Public Routes
- `/` - Homepage
- `/rooms` - Browse rooms
- `/rooms/{room}` - Room details
- `/login` - Login page
- `/register` - Registration page

### User Routes (Authenticated)
- `/dashboard` - User dashboard
- `/profile` - User profile
- `/my-bookings` - User bookings
- `/rooms/{room}/book` - Book room
- `/bookings` - Booking management

### Admin Routes (Admin only)
- `/admin/dashboard` - Admin dashboard
- `/admin/rooms` - Manage rooms
- `/admin/bookings` - Manage bookings
- `/admin/users` - Manage users
- `/admin/profile` - Admin profile

## Future Enhancements

- Payment integration
- Email notifications
- Room availability calendar
- Reviews and ratings
- Multi-language support
- Advanced reporting
- Room images upload
- Booking history export
