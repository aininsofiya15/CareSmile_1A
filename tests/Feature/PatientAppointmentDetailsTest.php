<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\User;

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function detailsPatientUser(string $name = 'Test Patient'): User
{
    return User::factory()->create(['name' => $name, 'role' => Role::Patient]);
}

function detailsDentistUser(string $name = 'Dr Test'): User
{
    return User::factory()->create(['name' => $name, 'role' => Role::Dentist]);
}

function detailsAdminUser(): User
{
    return User::factory()->create(['role' => Role::Admin]);
}

function makeAppointment(User $patient, User $dentist, array $overrides = []): Appointment
{
    return Appointment::create(array_merge([
        'patient_id'       => $patient->id,
        'doctor_id'        => $dentist->id,
        'appointment_date' => now()->addDay()->toDateString(),
        'appointment_time' => '09:00:00',
        'end_time'         => '11:00:00',
        'service'          => 'Teeth Whitening',
        'status'           => 'scheduled',
    ], $overrides));
}

// ---------------------------------------------------------------------------
// Access Control
// ---------------------------------------------------------------------------

it('patient can view their own appointment details', function () {
    $patient = detailsPatientUser();
    $dentist = detailsDentistUser();
    $appointment = makeAppointment($patient, $dentist);

    $this->actingAs($patient)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertOk();
});

it('patient cannot view another patient appointment and receives 403', function () {
    $owner   = detailsPatientUser('Owner');
    $other   = detailsPatientUser('Other');
    $dentist = detailsDentistUser();
    $appointment = makeAppointment($owner, $dentist);

    $this->actingAs($other)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertForbidden();
});

it('dentist cannot access patient appointment details route and receives 403', function () {
    $patient = detailsPatientUser();
    $dentist = detailsDentistUser();
    $appointment = makeAppointment($patient, $dentist);

    $this->actingAs($dentist)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertForbidden();
});

it('admin cannot access patient appointment details route and receives 403', function () {
    $patient = detailsPatientUser();
    $dentist = detailsDentistUser();
    $admin   = detailsAdminUser();
    $appointment = makeAppointment($patient, $dentist);

    $this->actingAs($admin)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Content: Service, Dentist, Duration
// ---------------------------------------------------------------------------

it('appointment details page shows the correct service name', function () {
    $patient = detailsPatientUser();
    $dentist = detailsDentistUser();
    $appointment = makeAppointment($patient, $dentist, ['service' => 'Dental Scaling']);

    $this->actingAs($patient)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertOk()
        ->assertSee('Dental Scaling');
});

it('appointment details page shows the correct dentist name', function () {
    $patient = detailsPatientUser();
    $dentist = detailsDentistUser('Dr Adeeb Irfaan');
    $appointment = makeAppointment($patient, $dentist);

    $this->actingAs($patient)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertOk()
        ->assertSee('Dr Adeeb Irfaan');
});

it('appointment details page shows correct start and end time for long duration appointment', function () {
    $patient = detailsPatientUser();
    $dentist = detailsDentistUser();
    $appointment = makeAppointment($patient, $dentist, [
        'appointment_time' => '09:00:00',
        'end_time'         => '11:00:00',
    ]);

    $this->actingAs($patient)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertOk()
        ->assertSee('09:00 AM')
        ->assertSee('11:00 AM');
});

// ---------------------------------------------------------------------------
// Reminder Notice
// ---------------------------------------------------------------------------

it('reminder message appears on appointment details page', function () {
    $patient = detailsPatientUser();
    $dentist = detailsDentistUser();
    $appointment = makeAppointment($patient, $dentist);

    $this->actingAs($patient)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertOk()
        ->assertSee('15 minutes before', false)
        ->assertSee('20 minutes', false);
});

// ---------------------------------------------------------------------------
// Edge Cases
// ---------------------------------------------------------------------------

it('appointment without end_time still displays safely without crashing', function () {
    $patient = detailsPatientUser();
    $dentist = detailsDentistUser();
    $appointment = makeAppointment($patient, $dentist, ['end_time' => null]);

    $this->actingAs($patient)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertOk()
        ->assertSee('09:00 AM');
});

it('cancelled appointment shows cancelled status on details page', function () {
    $patient = detailsPatientUser();
    $dentist = detailsDentistUser();
    $appointment = makeAppointment($patient, $dentist, ['status' => 'cancelled']);

    $this->actingAs($patient)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertOk()
        ->assertSee('Cancelled');
});

it('no_show appointment shows No Show status on details page', function () {
    $patient = detailsPatientUser();
    $dentist = detailsDentistUser();
    $appointment = makeAppointment($patient, $dentist, ['status' => 'no_show']);

    $this->actingAs($patient)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertOk()
        ->assertSee('No Show');
});

it('completed appointment shows completed status on details page', function () {
    $patient = detailsPatientUser();
    $dentist = detailsDentistUser();
    $appointment = makeAppointment($patient, $dentist, ['status' => 'completed']);

    $this->actingAs($patient)
        ->get(route('patient.appointments.show', $appointment->id))
        ->assertOk()
        ->assertSee('Completed');
});
