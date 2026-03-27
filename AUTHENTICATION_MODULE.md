# CareSmile Dental Clinic - Authentication Module

## Overview
This module implements authentication and role-based access control for the dental clinic appointment system.

## Features Implemented

### 1. User Authentication
- User registration with role selection (Patient/Dentist)
- User login with email and password
- Password reset functionality
- Session management
- Rate limiting for login attempts (5 attempts per minute)

### 2. Role-Based Access Control
Three user roles implemented:
- **Admin**: Full system access for managing the clinic
- **Patient**: Can book appointments and view their records
- **Dentist**: Can manage schedules and view patient appointments

### 3. Role Middleware
Custom middleware (`RoleMiddleware`) protects routes based on user roles.

## Files Created/Modified

### Models
- `app/Models/User.php` - Updated with role field and helper methods

### Enums
- `app/Enums/Role.php` - Role enum with Admin, Patient, and Dentist values

### Middleware
- `app/Http/Middleware/RoleMiddleware.php` - Role-based access control

### Form Requests
- `app/Http/Requests/Auth/LoginRequest.php` - Login validation
- `app/Http/Requests/RegisterRequest.php` - Registration validation

### Controllers
- `app/Http/Controllers/AdminController.php` - Admin dashboard
- `app/Http/Controllers/PatientController.php` - Patient dashboard
- `app/Http/Controllers/DentistController.php` - Dentist dashboard
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` - Updated login
- `app/Http/Controllers/Auth/RegisteredUserController.php` - Updated registration

### Views (Bootstrap-styled)
- `resources/views/auth/login.blade.php` - Login form
- `resources/views/auth/register.blade.php` - Registration form with role selection
- `resources/views/layouts/app.blade.php` - Main layout
- `resources/views/layouts/guest.blade.php` - Guest layout for auth pages
- `resources/views/layouts/navigation.blade.php` - Navigation bar
- `resources/views/admin/dashboard.blade.php` - Admin dashboard
- `resources/views/patient/dashboard.blade.php` - Patient dashboard
- `resources/views/dentist/dashboard.blade.php` - Dentist dashboard
- `resources/views/welcome.blade.php` - Landing page

### Database
- `database/migrations/2026_03_27_185423_add_role_to_users_table.php` - Adds role column
- `database/seeders/DatabaseSeeder.php` - Creates default admin user

### Configuration
- `bootstrap/app.php` - Registered role middleware alias
- `resources/css/app.css` - Bootstrap CSS
- `resources/js/bootstrap.js` - Bootstrap JS

### Routes
- `routes/web.php` - Updated with role-specific dashboard routes

## Default Admin Account
- **Email**: admin@caresmile.com
- **Password**: password

## How to Run

### 1. Start the development server
```bash
php artisan serve
```

### 2. Access the application
- Visit: http://localhost:8000
- Home page: http://localhost:8000/
- Login: http://localhost:8000/login
- Register: http://localhost:8000/register

### 3. Test the authentication
1. Register a new patient account
2. Register a new dentist account
3. Login as admin (admin@caresmile.com / password)
4. Test role-based route protection

## Route Protection
Routes are protected using middleware:
```php
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware('role:admin')
    ->name('admin.dashboard');
```

Users without the required role will see a 403 Forbidden error.

## Form Validation
- All forms use Form Request classes for validation
- Error messages are displayed using Bootstrap error styling
- CSRF protection is enabled on all forms

## Next Modules to Build
1. Shared Layout and Theme (with dark/light mode)
2. Public Pages
3. Patient Management
4. Dentist Management
5. Dental Services Management
6. Doctor Schedule Management
7. Appointment Booking
8. Consultation Records
9. Medical Document Management
10. Dashboards and Reporting

## Security Features
- Password hashing using bcrypt
- CSRF token verification
- Rate limiting on login attempts
- Role-based access control
- Session regeneration after login

## Notes
- Bootstrap 5 is used for styling
- No email verification required (can be added later)
- Admin accounts must be created via database seeding or directly in the database
- Patients and Dentists can self-register
