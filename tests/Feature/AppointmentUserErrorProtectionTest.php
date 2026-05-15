<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createPatient(): User
{
    return User::factory()->create([
        'role' => Role::Patient,
    ]);
}

function createDentist(): User
{
    return User::factory()->create([
        'role' => Role::Dentist,
    ]);
}

function createService(): Service
{
    return Service::create([
        'name' => 'Scaling',
        'description' => 'Dental scaling service',
        'price' => 100,
        'duration_minutes' => 30,
        'is_active' => true,
    ]);
}

function createSchedule(User $dentist): DoctorSchedule
{
    $schedule = DoctorSchedule::create([
        'doctor_id' => $dentist->id,
        'working_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '12:00',
        'slot_duration' => 30,
        'is_active' => true,
    ]);

    $schedule->generateSlots();

    return $schedule->refresh();
}

it('rejects booking when service is not selected', function () {
    $patient = createPatient();

    $response = $this->actingAs($patient)
        ->post(route('patient.appointments.store'), [
            'slot_id' => 1,
        ]);

    $response->assertSessionHasErrors('service_id');
});

it('rejects booking when slot is not selected', function () {
    $patient = createPatient();

    $service = createService();

    $response = $this->actingAs($patient)
        ->post(route('patient.appointments.store'), [
            'service_id' => $service->id,
        ]);

    $response->assertSessionHasErrors('slot_id');
});

it('rejects booking using invalid slot id', function () {
    $patient = createPatient();

    $service = createService();

    $response = $this->actingAs($patient)
        ->post(route('patient.appointments.store'), [
            'service_id' => $service->id,
            'slot_id' => 999999,
        ]);

    $response->assertSessionHasErrors('slot_id');
});

it('rejects booking using inactive service', function () {
    $patient = createPatient();

    $service = Service::create([
        'name' => 'Inactive Service',
        'description' => 'Inactive service',
        'price' => 100,
        'duration_minutes' => 30,
        'is_active' => false,
    ]);

    $dentist = createDentist();

    $schedule = createSchedule($dentist);

    $slot = $schedule->slots()->first();

    $response = $this->actingAs($patient)
        ->post(route('patient.appointments.store'), [
            'service_id' => $service->id,
            'slot_id' => $slot->id,
        ]);

    $response->assertSessionHasErrors('service_id');
});

it('prevents double booking on the same slot', function () {
    $patient1 = createPatient();

    $patient2 = createPatient();

    $dentist = createDentist();

    $service = createService();

    $schedule = createSchedule($dentist);

    $slot = $schedule->slots()->first();

    Appointment::create([
        'patient_id' => $patient1->id,
        'doctor_id' => $dentist->id,
        'appointment_date' => $schedule->working_date,
        'appointment_time' => $slot->start_time,
        'end_time' => '09:30:00',
        'service' => $service->name,
        'status' => 'scheduled',
    ]);

    $slot->update([
        'is_available' => false,
    ]);

    $response = $this->actingAs($patient2)
        ->post(route('patient.appointments.store'), [
            'service_id' => $service->id,
            'slot_id' => $slot->id,
        ]);

    $response->assertSessionHasErrors('slot_id');
});

it('rejects booking for unavailable slot', function () {
    $patient = createPatient();

    $dentist = createDentist();

    $service = createService();

    $schedule = createSchedule($dentist);

    $slot = $schedule->slots()->first();

    $slot->update([
        'is_available' => false,
    ]);

    $response = $this->actingAs($patient)
        ->post(route('patient.appointments.store'), [
            'service_id' => $service->id,
            'slot_id' => $slot->id,
        ]);

    $response->assertSessionHasErrors('slot_id');
});

it('rejects reschedule without slot selection', function () {
    $patient = createPatient();

    $dentist = createDentist();

    $service = createService();

    $appointment = Appointment::create([
        'patient_id' => $patient->id,
        'doctor_id' => $dentist->id,
        'appointment_date' => now()->addDay()->toDateString(),
        'appointment_time' => '09:00:00',
        'end_time' => '09:30:00',
        'service' => $service->name,
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($patient)
        ->post(route('patient.appointments.reschedule.submit', $appointment->id), []);

    $response->assertSessionHasErrors('slot_id');
});
