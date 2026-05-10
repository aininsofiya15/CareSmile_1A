<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\User;
use App\Services\ScheduleStatusService;

function statusAdmin(): User
{
    return User::factory()->create(['role' => Role::Admin]);
}

function statusDentist(): User
{
    return User::factory()->create(['role' => Role::Dentist]);
}

function statusPatient(): User
{
    return User::factory()->create(['role' => Role::Patient]);
}

function statusService(string $name = 'Status Service', int $durationMinutes = 30): Service
{
    return Service::create([
        'name' => $name,
        'description' => $name,
        'price' => 100,
        'duration_minutes' => $durationMinutes,
        'is_active' => true,
    ]);
}

function statusSchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    $status = $overrides['status'] ?? DoctorSchedule::STATUS_ACTIVE;

    $schedule = DoctorSchedule::create(array_merge([
        'doctor_id' => $dentist->id,
        'working_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 30,
        'is_active' => $status === DoctorSchedule::STATUS_ACTIVE,
        'status' => $status,
        'status_updated_automatically' => false,
    ], $overrides));

    $schedule->generateSlots();

    return $schedule->refresh();
}

function statusSchedulePayload(User $dentist, array $overrides = []): array
{
    return array_merge([
        'doctor_id' => $dentist->id,
        'working_date' => now()->addDays(3)->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 30,
        'status' => DoctorSchedule::STATUS_ACTIVE,
        'notes' => null,
    ], $overrides);
}

it('creates schedules with active inactive and unavailable statuses', function () {
    $admin = statusAdmin();
    $dentist = statusDentist();

    foreach ([DoctorSchedule::STATUS_ACTIVE, DoctorSchedule::STATUS_INACTIVE, DoctorSchedule::STATUS_UNAVAILABLE] as $index => $status) {
        $this->actingAs($admin)
            ->post(route('admin.schedules.store'), statusSchedulePayload($dentist, [
                'working_date' => now()->addDays($index + 3)->toDateString(),
                'status' => $status,
            ]))
            ->assertRedirect(route('admin.schedules.index'));

        $this->assertDatabaseHas('doctor_schedules', [
            'doctor_id' => $dentist->id,
            'status' => $status,
            'is_active' => $status === DoctorSchedule::STATUS_ACTIVE,
        ]);
    }
});

it('rejects invalid schedule status values', function () {
    $admin = statusAdmin();
    $dentist = statusDentist();

    $this->actingAs($admin)
        ->post(route('admin.schedules.store'), statusSchedulePayload($dentist, [
            'status' => 'paused',
        ]))
        ->assertSessionHasErrors('status');
});

it('hides non bookable schedules from patient slot lookup and blocks direct booking attempts', function () {
    $patient = statusPatient();
    $dentist = statusDentist();
    $service = statusService();

    foreach ([DoctorSchedule::STATUS_INACTIVE, DoctorSchedule::STATUS_UNAVAILABLE, DoctorSchedule::STATUS_FULLY_BOOKED] as $status) {
        $schedule = statusSchedule($dentist, [
            'status' => $status,
            'working_date' => now()->addDays(match ($status) {
                DoctorSchedule::STATUS_INACTIVE => 1,
                DoctorSchedule::STATUS_UNAVAILABLE => 2,
                default => 3,
            })->toDateString(),
        ]);
        $slot = $schedule->slots()->firstOrFail();

        $this->actingAs($patient)
            ->getJson('/get-slots/'.$schedule->working_date->toDateString().'?service_id='.$service->id)
            ->assertSuccessful()
            ->assertExactJson([]);

        $this->actingAs($patient)
            ->post(route('patient.appointments.store'), [
                'service_id' => $service->id,
                'slot_id' => $slot->id,
            ])
            ->assertSessionHasErrors([
                'time_slot' => 'This schedule is currently unavailable for booking. Please select another available slot.',
            ]);
    }
});

it('loads the patient appointment create page with active schedules only', function () {
    $patient = statusPatient();
    $dentist = statusDentist();
    statusService();

    statusSchedule($dentist, ['status' => DoctorSchedule::STATUS_ACTIVE]);
    statusSchedule($dentist, [
        'status' => DoctorSchedule::STATUS_INACTIVE,
        'working_date' => now()->addDays(2)->toDateString(),
    ]);

    $this->actingAs($patient)
        ->get(route('patient.appointments.create'))
        ->assertOk()
        ->assertSee('Book Appointment');
});

it('auto marks an active schedule fully booked after all valid slots are booked', function () {
    $patient = statusPatient();
    $dentist = statusDentist();
    $service = statusService('Long Status Service', 60);
    $schedule = statusSchedule($dentist);
    $slot = $schedule->slots()->where('start_time', '09:00:00')->firstOrFail();

    $this->actingAs($patient)
        ->post(route('patient.appointments.store'), [
            'service_id' => $service->id,
            'slot_id' => $slot->id,
        ])
        ->assertRedirect(route('patient.appointments'));

    $schedule->refresh();

    expect($schedule->status)->toBe(DoctorSchedule::STATUS_FULLY_BOOKED)
        ->and($schedule->is_active)->toBeFalse()
        ->and($schedule->status_updated_automatically)->toBeTrue()
        ->and($schedule->slots()->where('is_available', false)->count())->toBe(2);
});

it('restores automatically fully booked schedules to active when a booking is cancelled', function () {
    $patient = statusPatient();
    $dentist = statusDentist();
    $service = statusService('Cancellable Status Service', 60);
    $schedule = statusSchedule($dentist);
    $slot = $schedule->slots()->where('start_time', '09:00:00')->firstOrFail();

    $this->actingAs($patient)
        ->post(route('patient.appointments.store'), [
            'service_id' => $service->id,
            'slot_id' => $slot->id,
        ])
        ->assertRedirect(route('patient.appointments'));

    $appointment = Appointment::where('patient_id', $patient->id)->firstOrFail();

    $this->actingAs($patient)
        ->post(route('patient.appointments.cancel', $appointment->id))
        ->assertRedirect(route('patient.appointments'));

    $schedule->refresh();

    expect($schedule->status)->toBe(DoctorSchedule::STATUS_ACTIVE)
        ->and($schedule->is_active)->toBeTrue()
        ->and($schedule->status_updated_automatically)->toBeFalse();
});

it('does not restore manually fully booked schedules automatically', function () {
    $dentist = statusDentist();
    $schedule = statusSchedule($dentist, [
        'status' => DoctorSchedule::STATUS_FULLY_BOOKED,
        'is_active' => false,
        'status_updated_automatically' => false,
    ]);

    app(ScheduleStatusService::class)->refreshScheduleAvailability($schedule);

    $schedule->refresh();

    expect($schedule->status)->toBe(DoctorSchedule::STATUS_FULLY_BOOKED)
        ->and($schedule->is_active)->toBeFalse()
        ->and($schedule->status_updated_automatically)->toBeFalse();
});
