<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

function previewAdminUser(): User
{
    return User::factory()->create(['role' => Role::Admin]);
}

function previewDentistUser(): User
{
    return User::factory()->create(['role' => Role::Dentist]);
}

function slotPreviewPayload(User $dentist, array $overrides = []): array
{
    return array_merge([
        'doctor_id' => $dentist->id,
        'working_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '11:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 30,
    ], $overrides);
}

function slotPreviewRequest(TestCase $test, User $admin, User $dentist, array $overrides = []): TestResponse
{
    return $test->actingAs($admin)->getJson(route('admin.schedules.preview-slots', slotPreviewPayload($dentist, $overrides)));
}

it('generates correct 30 minute slots', function () {
    $admin = previewAdminUser();
    $dentist = previewDentistUser();

    $response = slotPreviewRequest($this, $admin, $dentist);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('summary.total_slots', 4)
        ->assertJsonPath('summary.available_slots', 4)
        ->assertJsonPath('summary.blocked_slots', 0)
        ->assertJsonPath('slots.0.start_time', '09:00')
        ->assertJsonPath('slots.3.end_time', '11:00');
});

it('generates correct 60 minute slots', function () {
    $admin = previewAdminUser();
    $dentist = previewDentistUser();

    $response = slotPreviewRequest($this, $admin, $dentist, [
        'slot_duration' => 60,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('summary.total_slots', 2)
        ->assertJsonPath('summary.available_slots', 2)
        ->assertJsonPath('slots.0.start_time', '09:00')
        ->assertJsonPath('slots.1.end_time', '11:00');
});

it('excludes break time from available slots', function () {
    $admin = previewAdminUser();
    $dentist = previewDentistUser();

    $response = slotPreviewRequest($this, $admin, $dentist, [
        'start_time' => '09:00',
        'end_time' => '17:00',
        'break_start' => '13:00',
        'break_end' => '14:00',
        'slot_duration' => 30,
    ]);

    $response->assertOk()
        ->assertJsonPath('summary.total_slots', 14)
        ->assertJsonPath('summary.available_slots', 14)
        ->assertJsonPath('summary.blocked_slots', 0);
});

it('does not generate break slots in preview', function () {
    $admin = previewAdminUser();
    $dentist = previewDentistUser();

    $response = slotPreviewRequest($this, $admin, $dentist, [
        'start_time' => '12:00',
        'end_time' => '14:00',
        'break_start' => '13:00',
        'break_end' => '14:00',
    ]);

    $slots = $response->json('slots');

    expect($slots)->toHaveCount(2)
        ->and($slots[0]['start_time'])->toBe('12:00')
        ->and($slots[1]['end_time'])->toBe('13:00')
        ->and(collect($slots)->pluck('reason')->filter()->values()->all())->toBe([]);
});

it('marks existing appointment slots as blocked', function () {
    $admin = previewAdminUser();
    $dentist = previewDentistUser();
    $patient = User::factory()->create(['role' => Role::Patient]);
    $date = now()->addDay()->toDateString();

    Appointment::create([
        'patient_id' => $patient->id,
        'doctor_id' => $dentist->id,
        'appointment_date' => $date,
        'appointment_time' => '10:00',
        'end_time' => '11:00',
        'service' => 'Scaling',
        'status' => 'scheduled',
    ]);

    $response = slotPreviewRequest($this, $admin, $dentist, [
        'working_date' => $date,
        'start_time' => '09:00',
        'end_time' => '12:00',
        'slot_duration' => 30,
    ]);

    $response->assertOk()
        ->assertJsonPath('summary.total_slots', 6)
        ->assertJsonPath('summary.available_slots', 4)
        ->assertJsonPath('summary.blocked_slots', 2)
        ->assertJsonPath('slots.2.status', 'blocked')
        ->assertJsonPath('slots.2.reason', 'Existing appointment')
        ->assertJsonPath('slots.3.status', 'blocked')
        ->assertJsonPath('slots.3.reason', 'Existing appointment');
});

it('rejects invalid time range', function () {
    $admin = previewAdminUser();
    $dentist = previewDentistUser();

    $response = slotPreviewRequest($this, $admin, $dentist, [
        'start_time' => '11:00',
        'end_time' => '09:00',
    ]);

    $response->assertOk()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Start time must be earlier than end time.');
});

it('rejects invalid break time', function () {
    $admin = previewAdminUser();
    $dentist = previewDentistUser();

    $response = slotPreviewRequest($this, $admin, $dentist, [
        'start_time' => '09:00',
        'end_time' => '17:00',
        'break_start' => '08:00',
        'break_end' => '09:30',
    ]);

    $response->assertOk()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Break time must be within working hours.');
});

it('rejects slot duration longer than working duration', function () {
    $admin = previewAdminUser();
    $dentist = previewDentistUser();

    $response = slotPreviewRequest($this, $admin, $dentist, [
        'start_time' => '09:00',
        'end_time' => '10:00',
        'slot_duration' => 120,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Slot duration must be shorter than or equal to the working duration.');
});

it('works in edit mode with the current schedule id', function () {
    $admin = previewAdminUser();
    $dentist = previewDentistUser();
    $schedule = DoctorSchedule::create([
        'doctor_id' => $dentist->id,
        'working_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '11:00',
        'slot_duration' => 30,
        'is_active' => true,
    ]);

    $response = slotPreviewRequest($this, $admin, $dentist, [
        'schedule_id' => $schedule->id,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('warning', null)
        ->assertJsonPath('summary.total_slots', 4);
});

it('returns total available and blocked counts together', function () {
    $admin = previewAdminUser();
    $dentist = previewDentistUser();
    $patient = User::factory()->create(['role' => Role::Patient]);
    $date = now()->addDay()->toDateString();

    Appointment::create([
        'patient_id' => $patient->id,
        'doctor_id' => $dentist->id,
        'appointment_date' => $date,
        'appointment_time' => '10:30',
        'end_time' => '11:00',
        'service' => 'Filling',
        'status' => 'scheduled',
    ]);

    $response = slotPreviewRequest($this, $admin, $dentist, [
        'working_date' => $date,
        'start_time' => '09:00',
        'end_time' => '11:00',
        'break_start' => '09:30',
        'break_end' => '10:00',
        'slot_duration' => 30,
    ]);

    $response->assertOk()
        ->assertJsonPath('summary.total_slots', 3)
        ->assertJsonPath('summary.available_slots', 2)
        ->assertJsonPath('summary.blocked_slots', 1);
});
