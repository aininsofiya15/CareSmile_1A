<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\User;

function navigationUser(Role $role, string $name): User
{
    return User::factory()->create([
        'name' => $name,
        'role' => $role,
    ]);
}

it('routes dentist appointment sidebar link to the dentist appointment page', function () {
    $dentist = navigationUser(Role::Dentist, 'Dr Navigation');
    $patient = navigationUser(Role::Patient, 'Navigation Patient');

    Appointment::create([
        'patient_id' => $patient->id,
        'doctor_id' => $dentist->id,
        'appointment_date' => now()->addDay()->toDateString(),
        'appointment_time' => '09:00:00',
        'end_time' => '09:30:00',
        'service' => 'Scaling',
        'status' => 'scheduled',
    ]);

    $this->actingAs($dentist)
        ->get(route('dentist.appointments'))
        ->assertOk()
        ->assertSee(route('dentist.appointments'), false)
        ->assertDontSee(route('patient.appointments'), false)
        ->assertSee('My Appointments')
        ->assertSee('Navigation Patient');
});

it('keeps patient appointment sidebar link on the patient appointment page', function () {
    $patient = navigationUser(Role::Patient, 'Patient Navigation');

    $this->actingAs($patient)
        ->get(route('patient.appointments'))
        ->assertOk()
        ->assertSee(route('patient.appointments'), false)
        ->assertDontSee(route('dentist.appointments'), false)
        ->assertSee('My Appointments');
});

it('keeps admin appointment sidebar link on the admin appointment page', function () {
    $admin = navigationUser(Role::Admin, 'Admin Navigation');

    $this->actingAs($admin)
        ->get(route('admin.appointments'))
        ->assertOk()
        ->assertSee(route('admin.appointments'), false)
        ->assertDontSee(route('patient.appointments'), false)
        ->assertSee('Appointments List');
});

it('preserves 403 protection for wrong appointment route access', function () {
    $dentist = navigationUser(Role::Dentist, 'Dr Protected');
    $patient = navigationUser(Role::Patient, 'Patient Protected');

    $this->actingAs($dentist)
        ->get(route('patient.appointments'))
        ->assertForbidden();

    $this->actingAs($patient)
        ->get(route('dentist.appointments'))
        ->assertForbidden();
});
