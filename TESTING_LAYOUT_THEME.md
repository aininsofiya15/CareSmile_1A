# Testing Guide - Shared Layout and Theme Module

## Quick Test Checklist

### 1. Start the Application
```bash
php artisan serve
```
Visit: http://localhost:8000

### 2. Test Public Pages
- [ ] Home page loads correctly
- [ ] About page loads correctly (http://localhost:8000/about)
- [ ] Navigation bar shows logo, links, and theme toggle
- [ ] Theme toggle button works (🌙/☀️ icons)
- [ ] Footer displays correctly

### 3. Test Dark/Light Mode
- [ ] Click theme toggle on home page
- [ ] Page switches to dark mode with dark background
- [ ] Click again to switch back to light mode
- [ ] Theme persists after page refresh (localStorage)
- [ ] Theme toggle works on all pages

### 4. Test Authentication
- [ ] Login page loads correctly (http://localhost:8000/login)
- [ ] Register page loads correctly (http://localhost:8000/register)
- [ ] Login as admin: `admin@caresmile.com` / `password`
- [ ] Redirect to admin dashboard after login

### 5. Test Authenticated Layout (Sidebar)
- [ ] Admin dashboard loads with sidebar
- [ ] Sidebar shows CareSmile logo
- [ ] Dashboard link is highlighted/active
- [ ] User name displays in sidebar
- [ ] Role badge shows "Administrator"

### 6. Test Admin Dashboard Features
- [ ] Quick Actions show "Manage Patients" button
- [ ] Click "Manage Patients" to see patients page
- [ ] Patients page loads with sidebar
- [ ] Theme toggle works in authenticated pages

### 7. Test Role-Based Access
- [ ] Register a new Patient account
- [ ] Verify patient dashboard loads
- [ ] Verify patient cannot access admin pages (403 error)
- [ ] Register a new Dentist account
- [ ] Verify dentist dashboard loads
- [ ] Verify dentist cannot access admin pages (403 error)

### 8. Visual Theme Test
Check that dark mode styles are applied correctly:
- [ ] Body background changes to dark blue (#1a1a2e)
- [ ] Navbar changes to dark blue (#16213e)
- [ ] Sidebar changes to dark blue (#16213e)
- [ ] Cards have dark background and border
- [ ] Text is light colored in dark mode
- [ ] Flash messages (success/error) adapt to theme
- [ ] Tables adapt to theme

## Expected URLs
- Home: http://localhost:8000/
- About: http://localhost:8000/about
- Login: http://localhost:8000/login
- Register: http://localhost:8000/register
- Admin Dashboard: http://localhost:8000/admin/dashboard
- Admin Patients: http://localhost:8000/admin/patients
- Patient Dashboard: http://localhost:8000/patient/dashboard
- Dentist Dashboard: http://localhost:8000/dentist/dashboard

## Test Accounts
- Admin: `admin@caresmile.com` / `password`
- Patient: Register a new account
- Dentist: Register a new account (select "Dentist" as role)

## Common Issues & Solutions

### Issue: Theme doesn't persist after refresh
**Solution**: Check browser console for localStorage errors. Ensure JavaScript is working correctly.

### Issue: Sidebar not showing
**Solution**: Ensure user is logged in. Sidebar only shows for authenticated users.

### Issue: CSS not loading
**Solution**: Run `npm run build` to rebuild assets.

### Issue: CareSmile logo not showing
**Solution**: Verify `public/CareSmile.png` exists. Check browser console for 404 errors.

### Issue: Layout broken on mobile
**Solution**: Check if sidebar is hidden (it's designed to hide on screens < 992px).

## Next Modules
Once this module is tested and working, you can proceed to build:
1. Patient Management
2. Dentist Management
3. Dental Services Management
4. Doctor Schedule Management
5. Appointment Booking
6. Consultation Records
7. Medical Document Management

Each new page should:
- Extend `layouts.public` for public pages
- Extend `layouts.app` for authenticated pages
- Follow the existing code patterns
