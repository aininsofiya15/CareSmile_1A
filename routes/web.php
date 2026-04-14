<?php

use App\Enums\Role;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DentistController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
        ->middleware('role:'.Role::Admin->value)
        ->name('admin.dashboard');

    Route::get('/admin/patients', function () {
        return view('admin.patients');
    })->middleware('role:'.Role::Admin->value)->name('admin.patients');

    Route::get('/patient/dashboard', [PatientController::class, 'dashboard'])
        ->middleware('role:'.Role::Patient->value)
        ->name('patient.dashboard');

    Route::get('/dentist/dashboard', [DentistController::class, 'dashboard'])
        ->middleware('role:'.Role::Dentist->value)
        ->name('dentist.dashboard');

    Route::middleware(['auth', 'role:'.Role::Patient->value])->group(function () {
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments/store', [AppointmentController::class, 'store']);
    });

    // Route::get('/appointments', [AppointmentController::class, 'index'])
    //     ->name('appointments');
    // Route::get('/appointments/create', [AppointmentController::class, 'create'])
    //     ->name('appointments.create');

    // Route::post('/appointments/store', [AppointmentController::class, 'store']);

    Route::get('/reschedule', function () {
        return view('appointments/reschedule');
    });

    Route::get('/management', function () {
        return view('appointments/admin');
    });

    Route::get('/appointments/{id}/reschedule', [AppointmentController::class, 'showReschedule'])->name('appointments.reschedule');

    Route::post('/appointments/{id}/reschedule', [AppointmentController::class, 'submitReschedule'])->name('appointments.reschedule.submit');

    Route::post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
});

require __DIR__.'/auth.php';
