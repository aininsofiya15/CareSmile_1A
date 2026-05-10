<?php

use App\Enums\Role;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\User;

function operatingHoursAdmin(): User
{
    return User::factory()->create(['role' => Role::Admin]);
}

function operatingHoursDentist(): User
{
    return User::factory()->create(['role' => Role::Dentist]);
}

function operatingHoursPayload(User $dentist, array $overrides = []): array
{
    return array_merge([
        'doctor_id' => $dentist->id,
        'working_date' => now()->addDay()->toDateString(),
        'start_time' => '08:00',
        'end_time' => '18:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 30,
        'notes' => null,
        'is_active' => 1,
    ], $overrides);
}

it('allows admin to create a schedule across configured clinic hours', function () {
    $admin = operatingHoursAdmin();
    $dentist = operatingHoursDentist();

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), operatingHoursPayload($dentist));

    $response->assertRedirect(route('admin.schedules.index'));
    $this->assertDatabaseHas('doctor_schedules', [
        'doctor_id' => $dentist->id,
        'start_time' => '08:00',
        'end_time' => '18:00',
    ]);
});

it('rejects schedules before clinic opening time', function () {
    $admin = operatingHoursAdmin();
    $dentist = operatingHoursDentist();

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), operatingHoursPayload($dentist, [
        'start_time' => '07:30',
        'end_time' => '17:00',
    ]));

    $response->assertSessionHasErrors([
        'start_time' => 'Schedule time must be within clinic operating hours. Clinic operating hours are 08:00 AM to 06:00 PM.',
    ]);
});

it('rejects schedules after clinic closing time', function () {
    $admin = operatingHoursAdmin();
    $dentist = operatingHoursDentist();

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), operatingHoursPayload($dentist, [
        'start_time' => '08:00',
        'end_time' => '18:30',
    ]));

    $response->assertSessionHasErrors([
        'start_time' => 'Schedule time must be within clinic operating hours. Clinic operating hours are 08:00 AM to 06:00 PM.',
    ]);
});

it('rejects schedule times that are not on the configured interval', function () {
    $admin = operatingHoursAdmin();
    $dentist = operatingHoursDentist();

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), operatingHoursPayload($dentist, [
        'start_time' => '08:15',
        'end_time' => '17:00',
    ]));

    $response->assertSessionHasErrors([
        'start_time' => 'Please select time in 30-minute intervals.',
    ]);
});

it('allows schedule times on the 30 minute interval', function () {
    $admin = operatingHoursAdmin();
    $dentist = operatingHoursDentist();

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), operatingHoursPayload($dentist, [
        'start_time' => '08:30',
        'end_time' => '17:30',
    ]));

    $response->assertRedirect(route('admin.schedules.index'));
    $this->assertDatabaseHas('doctor_schedules', [
        'doctor_id' => $dentist->id,
        'start_time' => '08:30',
        'end_time' => '17:30',
    ]);
});

it('rejects break time outside working hours', function () {
    $admin = operatingHoursAdmin();
    $dentist = operatingHoursDentist();

    $response = $this->actingAs($admin)->post(route('admin.schedules.store'), operatingHoursPayload($dentist, [
        'start_time' => '09:00',
        'end_time' => '17:00',
        'break_start' => '08:30',
        'break_end' => '09:30',
    ]));

    $response->assertSessionHasErrors([
        'break_start' => 'Break time must be within working hours.',
    ]);
});

it('keeps available slot preview working with operating hour dropdown values', function () {
    $admin = operatingHoursAdmin();
    $dentist = operatingHoursDentist();

    $response = $this->actingAs($admin)->getJson(route('admin.schedules.preview-slots', operatingHoursPayload($dentist, [
        'start_time' => '08:00',
        'end_time' => '10:00',
    ])));

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('summary.total_slots', 4)
        ->assertJsonPath('summary.available_slots', 4);
});

it('keeps real time conflict detection working with operating hour validation', function () {
    $admin = operatingHoursAdmin();
    $dentist = operatingHoursDentist();

    DoctorSchedule::create(operatingHoursPayload($dentist, [
        'start_time' => '08:00',
        'end_time' => '11:00',
    ]));

    $response = $this->actingAs($admin)->getJson(route('admin.schedules.check-conflict', operatingHoursPayload($dentist, [
        'start_time' => '10:30',
        'end_time' => '13:00',
    ])));

    $response->assertOk()
        ->assertJsonPath('hasConflict', true)
        ->assertJsonPath('type', 'overlap');
});

it('keeps appointment booking availability working for valid generated slots', function () {
    $patient = User::factory()->create(['role' => Role::Patient]);
    $dentist = operatingHoursDentist();
    $service = Service::create([
        'name' => 'Operating Hour Service',
        'description' => 'Test service',
        'price' => 100,
        'duration_minutes' => 30,
        'is_active' => true,
    ]);
    $schedule = DoctorSchedule::create(operatingHoursPayload($dentist, [
        'start_time' => '08:00',
        'end_time' => '10:00',
    ]));
    $schedule->generateSlots();

    $response = $this->actingAs($patient)->getJson('/get-slots/'.$schedule->working_date->toDateString().'?service_id='.$service->id);

    $response->assertOk()
        ->assertJsonPath('0.slots.0.start_time', '08:00')
        ->assertJsonPath('0.slots.0.appointment_end_time', '08:30:00');
});
