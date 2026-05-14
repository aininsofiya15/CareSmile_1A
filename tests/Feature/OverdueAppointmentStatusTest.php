<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\User;
use Carbon\Carbon;

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function overdueAdmin(): User
{
    return User::factory()->create(['role' => Role::Admin]);
}

function overdueDentist(): User
{
    return User::factory()->create(['role' => Role::Dentist]);
}

function overduePatient(): User
{
    return User::factory()->create(['role' => Role::Patient]);
}

function pastScheduledAppointment(User $patient, User $dentist, array $overrides = []): Appointment
{
    return Appointment::create(array_merge([
        'patient_id'       => $patient->id,
        'doctor_id'        => $dentist->id,
        'appointment_date' => now()->subDay()->toDateString(),
        'appointment_time' => '09:00:00',
        'end_time'         => '10:00:00',
        'service'          => 'Cleaning',
        'status'           => 'scheduled',
    ], $overrides));
}

function futureScheduledAppointment(User $patient, User $dentist, array $overrides = []): Appointment
{
    return Appointment::create(array_merge([
        'patient_id'       => $patient->id,
        'doctor_id'        => $dentist->id,
        'appointment_date' => now()->addDay()->toDateString(),
        'appointment_time' => '09:00:00',
        'end_time'         => '10:00:00',
        'service'          => 'Checkup',
        'status'           => 'scheduled',
    ], $overrides));
}

// ---------------------------------------------------------------------------
// Overdue detection on admin appointments page
// ---------------------------------------------------------------------------

it('admin appointments page shows overdue warning when past scheduled appointments exist', function () {
    $admin   = overdueAdmin();
    $dentist = overdueDentist();
    $patient = overduePatient();
    pastScheduledAppointment($patient, $dentist);

    $this->actingAs($admin)
        ->get(route('admin.appointments', ['status' => 'all']))
        ->assertOk()
        ->assertSee('overdue')
        ->assertSee('need status review', false);
});

it('admin appointments page shows no overdue warning when no overdue appointments exist', function () {
    $admin   = overdueAdmin();
    $dentist = overdueDentist();
    $patient = overduePatient();
    futureScheduledAppointment($patient, $dentist);

    $this->actingAs($admin)
        ->get(route('admin.appointments', ['status' => 'all']))
        ->assertOk()
        ->assertSee('Reminder:', false)
        ->assertDontSee('need status review', false);
});

it('overdue filter on admin appointments page returns only overdue appointments', function () {
    $admin   = overdueAdmin();
    $dentist = overdueDentist();
    $patient = overduePatient();

    $past   = pastScheduledAppointment($patient, $dentist);
    $future = futureScheduledAppointment($patient, $dentist);

    $response = $this->actingAs($admin)
        ->get(route('admin.appointments', ['status' => 'overdue']));

    $response->assertOk();
    $response->assertSee($past->service);
    $response->assertDontSee($future->service);
});

// ---------------------------------------------------------------------------
// Future appointments are not affected
// ---------------------------------------------------------------------------

it('future scheduled appointment is not shown as overdue', function () {
    $admin   = overdueAdmin();
    $dentist = overdueDentist();
    $patient = overduePatient();
    futureScheduledAppointment($patient, $dentist);

    $this->actingAs($admin)
        ->get(route('admin.appointments', ['status' => 'scheduled']))
        ->assertOk()
        ->assertDontSee('<span class="overdue-badge">', false);
});

// ---------------------------------------------------------------------------
// Bulk mark-overdue action
// ---------------------------------------------------------------------------

it('admin can bulk mark overdue appointments as no show via mark overdue route', function () {
    $admin   = overdueAdmin();
    $dentist = overdueDentist();
    $patient = overduePatient();
    $appointment = pastScheduledAppointment($patient, $dentist);

    $this->actingAs($admin)
        ->post(route('admin.appointments.mark_overdue'))
        ->assertRedirect(route('admin.appointments'));

    $this->assertDatabaseHas('appointments', [
        'id'     => $appointment->id,
        'status' => 'no_show',
    ]);
});

it('future appointment is not marked no show by bulk overdue action', function () {
    $admin   = overdueAdmin();
    $dentist = overdueDentist();
    $patient = overduePatient();
    $future  = futureScheduledAppointment($patient, $dentist);

    $this->actingAs($admin)
        ->post(route('admin.appointments.mark_overdue'));

    $this->assertDatabaseHas('appointments', [
        'id'     => $future->id,
        'status' => 'scheduled',
    ]);
});

it('admin can still manually override overdue appointment status to completed via complete route', function () {
    $admin   = overdueAdmin();
    $dentist = overdueDentist();
    $patient = overduePatient();
    $appointment = pastScheduledAppointment($patient, $dentist);

    $this->actingAs($admin)
        ->post(route('admin.appointments.complete', $appointment->id));

    $this->assertDatabaseHas('appointments', [
        'id'     => $appointment->id,
        'status' => 'completed',
    ]);
});

it('appointment without end time does not crash admin appointments page', function () {
    $admin   = overdueAdmin();
    $dentist = overdueDentist();
    $patient = overduePatient();

    Appointment::create([
        'patient_id'       => $patient->id,
        'doctor_id'        => $dentist->id,
        'appointment_date' => now()->subDay()->toDateString(),
        'appointment_time' => '10:00:00',
        'end_time'         => null,
        'service'          => 'Whitening',
        'status'           => 'scheduled',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.appointments', ['status' => 'all']))
        ->assertOk();
});

it('patient cannot access mark overdue route', function () {
    $patient = overduePatient();

    $this->actingAs($patient)
        ->post(route('admin.appointments.mark_overdue'))
        ->assertForbidden();
});
