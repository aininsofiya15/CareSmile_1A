<?php

use App\Enums\Role;
use App\Models\DoctorSchedule;
use App\Models\User;

function createScheduleAdmin(): User
{
    return User::factory()->create(['role' => Role::Admin]);
}

function createScheduleDentist(): User
{
    return User::factory()->create(['role' => Role::Dentist]);
}

function schedulePayload(User $dentist, array $overrides = []): array
{
    return array_merge([
        'doctor_id' => $dentist->id,
        'working_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '13:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 30,
        'notes' => null,
        'is_active' => 1,
    ], $overrides);
}

function createExistingSchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    $schedule = DoctorSchedule::create(schedulePayload($dentist, $overrides));
    $schedule->generateSlots();

    return $schedule->refresh();
}

it('allows admin to create a valid schedule without conflict', function () {
    $admin = createScheduleAdmin();
    $dentist = createScheduleDentist();

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), schedulePayload($dentist));

    $response->assertRedirect(route('admin.schedules.index'));
    $this->assertDatabaseHas('doctor_schedules', [
        'doctor_id' => $dentist->id,
        'start_time' => '09:00',
        'end_time' => '13:00',
    ]);

    expect(DoctorSchedule::first()->slots()->count())->toBe(8);
});

it('rejects a duplicate schedule for the same doctor date and time', function () {
    $admin = createScheduleAdmin();
    $dentist = createScheduleDentist();
    createExistingSchedule($dentist);

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), schedulePayload($dentist));

    $response->assertSessionHasErrors([
        'working_date' => 'This doctor already has a schedule for the same date and time.',
    ]);
});

it('rejects an overlapping schedule for the same doctor and date', function () {
    $admin = createScheduleAdmin();
    $dentist = createScheduleDentist();
    createExistingSchedule($dentist, [
        'start_time' => '09:00',
        'end_time' => '13:00',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), schedulePayload($dentist, [
        'start_time' => '12:00',
        'end_time' => '16:00',
    ]));

    $response->assertSessionHasErrors([
        'working_date' => 'This doctor already has a schedule that overlaps with the selected time.',
    ]);
});

it('allows a non-overlapping schedule for the same doctor and date', function () {
    $admin = createScheduleAdmin();
    $dentist = createScheduleDentist();
    createExistingSchedule($dentist, [
        'start_time' => '09:00',
        'end_time' => '13:00',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), schedulePayload($dentist, [
        'start_time' => '14:00',
        'end_time' => '17:00',
    ]));

    $response->assertRedirect(route('admin.schedules.index'));
    expect(DoctorSchedule::where('doctor_id', $dentist->id)->count())->toBe(2);
});

it('rejects break time outside working hours', function () {
    $admin = createScheduleAdmin();
    $dentist = createScheduleDentist();

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), schedulePayload($dentist, [
        'start_time' => '09:00',
        'end_time' => '17:00',
        'break_start' => '08:00',
        'break_end' => '09:30',
    ]));

    $response->assertSessionHasErrors([
        'break_start' => 'Break time must be within working hours.',
    ]);
});

it('rejects break start after break end', function () {
    $admin = createScheduleAdmin();
    $dentist = createScheduleDentist();

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), schedulePayload($dentist, [
        'start_time' => '09:00',
        'end_time' => '17:00',
        'break_start' => '14:00',
        'break_end' => '13:00',
    ]));

    $response->assertSessionHasErrors([
        'break_end' => 'Break start time must be earlier than break end time.',
    ]);
});

it('rejects start time after end time', function () {
    $admin = createScheduleAdmin();
    $dentist = createScheduleDentist();

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), schedulePayload($dentist, [
        'start_time' => '17:00',
        'end_time' => '09:00',
    ]));

    $response->assertSessionHasErrors([
        'end_time' => 'Start time must be earlier than end time.',
    ]);
});

it('rejects slot duration longer than working duration', function () {
    $admin = createScheduleAdmin();
    $dentist = createScheduleDentist();

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), schedulePayload($dentist, [
        'start_time' => '09:00',
        'end_time' => '10:00',
        'slot_duration' => 120,
    ]));

    $response->assertSessionHasErrors([
        'slot_duration' => 'Slot duration must be shorter than or equal to the working duration.',
    ]);
});

it('returns conflict details from the real-time endpoint for overlaps', function () {
    $admin = createScheduleAdmin();
    $dentist = createScheduleDentist();
    createExistingSchedule($dentist, [
        'start_time' => '09:00',
        'end_time' => '13:00',
    ]);

    $response = $this->actingAs($admin)->getJson(route('admin.schedules.check-conflict', schedulePayload($dentist, [
        'start_time' => '12:00',
        'end_time' => '16:00',
    ])));

    $response->assertOk()
        ->assertJsonPath('hasConflict', true)
        ->assertJsonPath('type', 'overlap')
        ->assertJsonPath('conflicts.0.start_time', '09:00:00')
        ->assertJsonPath('conflicts.0.end_time', '13:00:00');
});

it('returns no conflict from the real-time endpoint for a valid schedule', function () {
    $admin = createScheduleAdmin();
    $dentist = createScheduleDentist();

    $response = $this->actingAs($admin)->getJson(route('admin.schedules.check-conflict', schedulePayload($dentist)));

    $response->assertOk()
        ->assertJsonPath('hasConflict', false)
        ->assertJsonPath('type', null)
        ->assertJsonPath('message', 'No schedule conflict detected.');
});

it('ignores the current schedule during edit conflict checks', function () {
    $admin = createScheduleAdmin();
    $dentist = createScheduleDentist();
    $schedule = createExistingSchedule($dentist, [
        'start_time' => '09:00',
        'end_time' => '13:00',
    ]);

    $response = $this->actingAs($admin)->getJson(route('admin.schedules.check-conflict', array_merge(
        schedulePayload($dentist),
        ['schedule_id' => $schedule->id]
    )));

    $response->assertOk()
        ->assertJsonPath('hasConflict', false)
        ->assertJsonPath('type', null);
});
