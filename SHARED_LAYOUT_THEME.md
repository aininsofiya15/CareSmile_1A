# CareSmile Dental Clinic - Shared Layout and Theme Module

## Overview
This module provides a reusable, theme-aware layout system with dark/light mode support for the entire application.

## Features Implemented

### 1. Layout System
- **Authenticated Layout** (`layouts/app.blade.php`): For logged-in users with sidebar
- **Public Layout** (`layouts/public.blade.php`): For public pages without sidebar

### 2. Reusable Components
All components are located in `resources/views/components/`:

- **navbar.blade.php**: Top navigation with logo, links, and theme toggle
- **sidebar.blade.php**: Left sidebar with role-specific navigation
- **footer.blade.php**: Simple footer
- **flash-message.blade.php**: Success/error/warning/info messages
- **validation-errors.blade.php**: Form validation errors

### 3. Dark/Light Mode Toggle
- Toggle button in navbar (🌙/☀️ icons)
- Saves preference to localStorage
- Applies theme on page load
- Smooth transitions between themes

### 4. Theme Helper Functions
Located in `app/Helpers/theme_helper.php`:

- `getTheme()`: Returns current theme ('light' or 'dark')
- `theme_navbar()`: Returns navbar background class
- `theme_sidebar()`: Returns sidebar background class
- `theme_footer()`: Returns footer background class
- `isDarkMode()`: Returns boolean

## Files Created/Modified

### Layouts
- `resources/views/layouts/app.blade.php` - Authenticated layout with sidebar
- `resources/views/layouts/public.blade.php` - Public layout without sidebar

### Components
- `resources/views/components/navbar.blade.php` - Navigation bar
- `resources/views/components/sidebar.blade.php` - Sidebar menu
- `resources/views/components/footer.blade.php` - Footer
- `resources/views/components/flash-message.blade.php` - Flash messages
- `resources/views/components/validation-errors.blade.php` - Validation errors

### Styling & JavaScript
- `resources/css/app.css` - Dark mode CSS styles
- `public/js/theme.js` - Theme toggle logic

### Helpers
- `app/Helpers/theme_helper.php` - Theme helper functions
- `composer.json` - Updated autoload for helpers

### Pages Updated
- `resources/views/welcome.blade.php` - Uses public layout
- `resources/views/admin/dashboard.blade.php` - Uses app layout
- `resources/views/patient/dashboard.blade.php` - Uses app layout
- `resources/views/dentist/dashboard.blade.php` - Uses app layout

## How to Use Layouts

### Public Pages (Home, About, etc.)
```blade
@extends('layouts.public')

@section('content')
    <h1>Welcome!</h1>
    <p>Your content here...</p>
@endsection
```

### Authenticated Pages (Dashboards, CRUD pages, etc.)
```blade
@extends('layouts.app')

@section('content')
    <h1>Dashboard</h1>
    <p>Your content here...</p>
@endsection
```

## How Layouts Work

### Authenticated Layout (`app.blade.php`)
```
┌─────────────────────────────────┬────────────────────────────────┐
│                                 │  Navbar + Theme Toggle         │
│         Sidebar                 ├────────────────────────────────┤
│                                 │                                │
│  - Logo                         │  Flash Messages                │
│  - Dashboard Link               │  Validation Errors             │
│  - Placeholder Links            │  Page Content                  │
│  - User Info                    │  Footer                        │
│                                 │                                │
└─────────────────────────────────┴────────────────────────────────┘
```

### Public Layout (`public.blade.php`)
```
┌────────────────────────────────────────────────────────────────┐
│              Navbar + Theme Toggle                              │
├────────────────────────────────────────────────────────────────┤
│                                                                │
│              Flash Messages                                     │
│              Validation Errors                                  │
│              Page Content                                       │
│                                                                │
├────────────────────────────────────────────────────────────────┤
│                         Footer                                  │
└────────────────────────────────────────────────────────────────┘
```

## How to Test Dark/Light Mode

### 1. Start the server
```bash
php artisan serve
```

### 2. Visit any page
- Home page: http://localhost:8000/
- Login page: http://localhost:8000/login
- Dashboard: http://localhost:8000/admin/dashboard (after login)

### 3. Test the theme toggle
1. Click the 🌙 button in the navbar
2. Page should switch to dark mode
3. Background, navbar, sidebar, cards should all change to dark colors
4. Click ☀️ button to switch back to light mode
5. Theme preference is saved to localStorage
6. Refresh the page - theme should persist

### 4. Test on different pages
- Visit home page (public layout)
- Visit dashboard (authenticated layout)
- Toggle theme on each page
- Theme should persist across all pages

## Theme Color Scheme

### Light Theme
- Background: `#f8f9fa`
- Body Text: `#212529`
- Navbar: White with shadow
- Sidebar: `#f8f9fa`
- Cards: White with border

### Dark Theme
- Background: `#1a1a2e`
- Body Text: `#e9ecef`
- Navbar: Dark blue (`#16213e`)
- Sidebar: Dark blue (`#16213e`)
- Cards: Dark blue with darker border

## Component Usage Examples

### Including Flash Messages
Flash messages are automatically included in both layouts. Use them in controllers:

```php
return redirect()->route('home')->with('success', 'Operation successful!');
return redirect()->back()->with('error', 'Something went wrong!');
```

### Validation Errors
Validation errors are automatically displayed. In your form requests:

```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email'],
    ];
}
```

### Sidebar Navigation
Sidebar automatically shows appropriate links based on user role:

```blade
@if(Auth::user()->isAdmin())
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
@elseif(Auth::user()->isDentist())
    <a href="{{ route('dentist.dashboard') }}">Dashboard</a>
@else
    <a href="{{ route('patient.dashboard') }}">Dashboard</a>
@endif
```

## Adding New Pages

### Public Page Example
Create `resources/views/pages/about.blade.php`:
```blade
@extends('layouts.public')

@section('content')
<div class="container">
    <h1>About Us</h1>
    <p>Learn more about CareSmile Dental Clinic...</p>
</div>
@endsection
```

### Authenticated Page Example
Create `resources/views/admin/patients.blade.php`:
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Patients</h1>
    <p>Patient management coming soon...</p>
</div>
@endsection
```

## Customizing Theme Colors

Edit `resources/css/app.css` to modify theme colors:

```css
.theme-dark {
    --bs-body-bg: #1a1a2e;
    --bs-body-color: #e9ecef;
}
```

## Notes

1. **Theme Persistence**: Theme is stored in localStorage under key 'theme'
2. **Default Theme**: Light mode is the default
3. **No Server-side Storage**: Theme preference is client-side only
4. **Responsive**: Sidebar is hidden on mobile (< 992px)
5. **Bootstrap 5**: Uses Bootstrap's built-in dark mode utilities
6. **CSS Variables**: Theme uses CSS custom properties for easy customization

## Security

- No authentication logic modified
- CSRF tokens included in all forms
- Session management unchanged
- Role-based access control preserved

## Next Steps

This layout module is ready for other modules:
- Public Pages (About, Services, Contact)
- Patient Management
- Dentist Management
- Appointment Booking
- etc.

Each new page should extend the appropriate layout based on whether it requires authentication.
