<?php

use App\Enums\Role;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DentistController;
use App\Http\Controllers\DoctorScheduleController;
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

    Route::get('/admin/schedules', [DoctorScheduleController::class, 'index'])
        ->middleware('role:'.Role::Admin->value)
        ->name('admin.schedules.index');

    Route::get('/admin/schedules/create', [DoctorScheduleController::class, 'create'])
        ->middleware('role:'.Role::Admin->value)
        ->name('admin.schedules.create');

    Route::post('/admin/schedules', [DoctorScheduleController::class, 'store'])
        ->middleware('role:'.Role::Admin->value)
        ->name('admin.schedules.store');

    Route::get('/admin/schedules/{schedule}/edit', [DoctorScheduleController::class, 'edit'])
        ->middleware('role:'.Role::Admin->value)
        ->name('admin.schedules.edit');

    Route::put('/admin/schedules/{schedule}', [DoctorScheduleController::class, 'update'])
        ->middleware('role:'.Role::Admin->value)
        ->name('admin.schedules.update');

    Route::delete('/admin/schedules/{schedule}', [DoctorScheduleController::class, 'destroy'])
        ->middleware('role:'.Role::Admin->value)
        ->name('admin.schedules.destroy');

    Route::get('/admin/schedules/{schedule}', [DoctorScheduleController::class, 'show'])
        ->middleware('role:'.Role::Admin->value)
        ->name('admin.schedules.show');

    Route::get('/patient/dashboard', [PatientController::class, 'dashboard'])
        ->middleware('role:'.Role::Patient->value)
        ->name('patient.dashboard');
    
     Route::get('/patient/profile', [PatientController::class, 'profile'])
        ->middleware('role:'.Role::Patient->value)
        ->name('patient.profile');

    Route::get('/dentist/dashboard', [DentistController::class, 'dashboard'])
        ->middleware('role:'.Role::Dentist->value)
        ->name('dentist.dashboard');

    Route::get('/dentist/schedules', [DoctorScheduleController::class, 'index'])
        ->middleware('role:'.Role::Dentist->value)
        ->name('dentist.schedules.index');
});

require __DIR__.'/auth.php';
