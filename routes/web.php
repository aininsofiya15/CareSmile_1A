<?php

use App\Enums\Role;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DentistController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

// --- Authenticated Routes ---
Route::middleware('auth')->group(function () {
    // Shared Profile (Breeze Defaults)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // ADMIN ROUTES (Role: admin)
    // ==========================================

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');

        // Add this line inside your admin routes group
        Route::resource('services', ServiceController::class);

        // Patient Management
        Route::get('/patients', [AdminController::class, 'managePatients'])->name('patients');
        Route::get('/patients/create', [AdminController::class, 'createPatient'])->name('patients.create');
        Route::post('/patients/store', [AdminController::class, 'storePatient'])->name('patients.store');
        Route::put('/patients/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('patients.reset-password');
        // --- ADD THESE 4 LINES TO FIX THE CRASH ---
        Route::get('/patients/{patient}', [AdminController::class, 'show'])->name('patients.show');
        Route::get('/patients/{patient}/edit', [AdminController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{patient}', [AdminController::class, 'update'])->name('patients.update');
        Route::delete('/patients/{patient}', [AdminController::class, 'destroy'])->name('patients.destroy');

        // Schedule Management
        Route::resource('schedules', DoctorScheduleController::class);

        // Dentist
        Route::get('/dentists', [AdminController::class, 'manageDentists'])->name('dentists');
        Route::get('/dentists/create', [AdminController::class, 'createDentist'])->name('dentists.create');
        Route::post('/dentists/store', [AdminController::class, 'storeDentist'])->name('dentists.store');
        Route::get('/dentists/{dentist}/edit', [AdminController::class, 'editDentist'])->name('dentists.edit');
        Route::put('/dentists/{dentist}', [AdminController::class, 'updateDentist'])->name('dentists.update');
        Route::delete('/dentists/{dentist}', [AdminController::class, 'destroyDentist'])->name('dentists.destroy');

        // Appointment Management (Admin)
        Route::get('/appointments', [AppointmentController::class, 'adminIndex'])->name('appointments');
        Route::post('/appointments/{id}/complete', [AppointmentController::class, 'markCompleted'])->name('appointments.complete');
        Route::post('/appointments/{id}/no-show', [AppointmentController::class, 'markNoShow'])->name('appointments.no_show');
    });

    // ==========================================
    // PATIENT ROUTES (Role: patient)
    // ==========================================

    Route::middleware('role:patient')->prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password/update', [ProfileController::class, 'updatePassword'])->name('password.update');

        // Appointments
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments/store', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{id}/reschedule', [AppointmentController::class, 'showReschedule'])->name('appointments.reschedule');
        Route::post('/appointments/{id}/reschedule', [AppointmentController::class, 'submitReschedule'])->name('appointments.reschedule.submit');
        Route::post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    });

    // ==========================================
    // DENTIST ROUTES (Role: dentist)
    // ==========================================

    Route::middleware('role:dentist')->prefix('dentist')->name('dentist.')->group(function () {
        Route::get('/dashboard', [DentistController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [DentistController::class, 'profile'])->name('profile');
        Route::get('/schedules', [DoctorScheduleController::class, 'index'])->name('schedules.index');
        Route::put('/profile/update', [DentistController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password/update', [DentistController::class, 'updatePassword'])->name('password.update');

    });
});

// AJAX: get available slots by date
Route::get('/get-slots/{date}', function ($date) {
    $schedules = App\Models\DoctorSchedule::with([
        'slots' => function ($q) {
            $q->where('is_available', true);
        },
        'doctor',
    ])
        ->where('working_date', $date)
        ->where('is_active', true)
        ->get();

    return response()->json($schedules);
})->middleware('auth');

require __DIR__.'/auth.php';
