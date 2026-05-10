<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\User;

function impactAdmin(): User
{
    return User::factory()->create(['role' => Role::Admin]);
}

function impactDentist(): User
{
    return User::factory()->create(['role' => Role::Dentist]);
}

function impactPatient(): User
{
    return User::factory()->create(['role' => Role::Patient]);
}

function impactService(): Service
{
    return Service::create([
        'name' => 'Impact Cleaning',
        'description' => 'Impact Cleaning',
        'price' => 90,
        'duration_minutes' => 30,
        'is_active' => true,
    ]);
}

function impactSchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    $schedule = DoctorSchedule::create(array_merge([
        'doctor_id' => $dentist->id,
        'working_date' => now()->addDays(5)->toDateString(),
        'start_time' => '09:00',
        'end_time' => '11:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 30,
        'notes' => null,
        'is_active' => true,
        'status' => DoctorSchedule::STATUS_ACTIVE,
        'status_updated_automatically' => false,
    ], $overrides));

    $schedule->generateSlots();

    return $schedule->refresh();
}

function impactSchedulePayload(DoctorSchedule $schedule, array $overrides = []): array
{
    return array_merge([
        'doctor_id' => $schedule->doctor_id,
        'working_date' => $schedule->working_date->toDateString(),
        'start_time' => '09:00',
        'end_time' => '11:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 30,
        'status' => DoctorSchedule::STATUS_ACTIVE,
        'notes' => null,
    ], $overrides);
}

function impactAppointment(DoctorSchedule $schedule, User $patient, Service $service, array $overrides = []): Appointment
{
    return Appointment::create(array_merge([
        'patient_id' => $patient->id,
        'doctor_id' => $schedule->doctor_id,
        'appointment_date' => $schedule->working_date->toDateString(),
        'appointment_time' => '09:00:00',
        'end_time' => '09:30:00',
        'service' => $service->name,
        'status' => 'scheduled',
    ], $overrides));
}

it('deletes a schedule with no affected appointments', function () {
    $admin = impactAdmin();
    $dentist = impactDentist();
    $schedule = impactSchedule($dentist);

    $this->actingAs($admin)
        ->delete(route('admin.schedules.destroy', $schedule))
        ->assertRedirect(route('admin.schedules.index'))
        ->assertSessionHas('success', 'Schedule deleted successfully.');

    $this->assertDatabaseMissing('doctor_schedules', ['id' => $schedule->id]);
});

it('prevents deleting a schedule with existing patient appointments', function () {
    $admin = impactAdmin();
    $dentist = impactDentist();
    $patient = impactPatient();
    $service = impactService();
    $schedule = impactSchedule($dentist);
    $appointment = impactAppointment($schedule, $patient, $service);

    $this->actingAs($admin)
        ->delete(route('admin.schedules.destroy', $schedule))
        ->assertRedirect(route('admin.schedules.index'))
        ->assertSessionHas('error', 'This schedule cannot be deleted because it has existing patient appointments. Please cancel or reschedule the appointments first.');

    $this->assertDatabaseHas('doctor_schedules', ['id' => $schedule->id]);
    $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
});

it('updates a schedule normally when there are no affected appointments', function () {
    $admin = impactAdmin();
    $dentist = impactDentist();
    $schedule = impactSchedule($dentist);

    $this->actingAs($admin)
        ->put(route('admin.schedules.update', $schedule), impactSchedulePayload($schedule, [
            'end_time' => '12:00',
        ]))
        ->assertRedirect(route('admin.schedules.index'));

    $this->assertDatabaseHas('doctor_schedules', [
        'id' => $schedule->id,
        'end_time' => '12:00',
    ]);
});

it('requires confirmation before major updates to schedules with appointments', function () {
    $admin = impactAdmin();
    $dentist = impactDentist();
    $patient = impactPatient();
    $service = impactService();
    $schedule = impactSchedule($dentist);
    impactAppointment($schedule, $patient, $service);

    $this->actingAs($admin)
        ->put(route('admin.schedules.update', $schedule), impactSchedulePayload($schedule, [
            'end_time' => '12:00',
        ]))
        ->assertRedirect()
        ->assertSessionHas('warning', 'This schedule has existing appointments. Changing the date, time, break time, duration, dentist, or status may affect patient bookings.');

    $this->assertDatabaseHas('doctor_schedules', [
        'id' => $schedule->id,
        'end_time' => '11:00',
    ]);
});

it('allows confirmed status changes with appointments while preserving appointments', function () {
    $admin = impactAdmin();
    $dentist = impactDentist();
    $patient = impactPatient();
    $service = impactService();
    $schedule = impactSchedule($dentist);
    $appointment = impactAppointment($schedule, $patient, $service);

    $this->actingAs($admin)
        ->put(route('admin.schedules.update', $schedule), impactSchedulePayload($schedule, [
            'status' => DoctorSchedule::STATUS_UNAVAILABLE,
            'impact_confirmed' => '1',
        ]))
        ->assertRedirect(route('admin.schedules.index'));

    $this->assertDatabaseHas('doctor_schedules', [
        'id' => $schedule->id,
        'status' => DoctorSchedule::STATUS_UNAVAILABLE,
        'is_active' => false,
    ]);
    $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
});

it('shows affected appointment warnings on schedule index and edit screens', function () {
    $admin = impactAdmin();
    $dentist = impactDentist();
    $patient = impactPatient();
    $service = impactService();
    $schedule = impactSchedule($dentist);
    impactAppointment($schedule, $patient, $service);

    $this->actingAs($admin)
        ->get(route('admin.schedules.index'))
        ->assertOk()
        ->assertSee('1 appointment')
        ->assertSee('Cannot Delete - Appointments Exist');

    $this->actingAs($admin)
        ->get(route('admin.schedules.edit', $schedule))
        ->assertOk()
        ->assertSee('This schedule has 1 existing appointment')
        ->assertSee($patient->name);
});
