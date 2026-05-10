<?php

use App\Enums\Role;
use App\Models\DoctorSchedule;
use App\Models\User;

function timePickerAdmin(): User
{
    return User::factory()->create(['role' => Role::Admin]);
}

function timePickerDentist(): User
{
    return User::factory()->create(['role' => Role::Dentist]);
}

function timePickerSchedule(User $dentist): DoctorSchedule
{
    $schedule = DoctorSchedule::create([
        'doctor_id' => $dentist->id,
        'working_date' => now()->addDays(4)->toDateString(),
        'start_time' => '08:00',
        'end_time' => '17:00',
        'break_start' => '12:00',
        'break_end' => '13:00',
        'slot_duration' => 30,
        'notes' => null,
        'is_active' => true,
        'status' => DoctorSchedule::STATUS_ACTIVE,
        'status_updated_automatically' => false,
    ]);

    $schedule->generateSlots();

    return $schedule->refresh();
}

it('shows card based time pickers on the create schedule page', function () {
    $admin = timePickerAdmin();
    timePickerDentist();

    $this->actingAs($admin)
        ->get(route('admin.schedules.create'))
        ->assertOk()
        ->assertSee('data-time-picker', false)
        ->assertSee('data-field="start_time"', false)
        ->assertSee('data-field="end_time"', false)
        ->assertSee('data-field="break_start"', false)
        ->assertSee('data-field="break_end"', false)
        ->assertSee('<input type="hidden" name="start_time" id="start_time"', false)
        ->assertSee('<input type="hidden" name="end_time" id="end_time"', false)
        ->assertSee('No break time')
        ->assertDontSee('<select name="start_time"', false)
        ->assertDontSee('<select name="end_time"', false)
        ->assertDontSee('<select name="break_start"', false)
        ->assertDontSee('<select name="break_end"', false);
});

it('shows existing schedule times selected on the edit schedule page', function () {
    $admin = timePickerAdmin();
    $dentist = timePickerDentist();
    $schedule = timePickerSchedule($dentist);

    $this->actingAs($admin)
        ->get(route('admin.schedules.edit', $schedule))
        ->assertOk()
        ->assertSee('08:00 AM')
        ->assertSee('05:00 PM')
        ->assertSee('12:00 PM')
        ->assertSee('01:00 PM')
        ->assertSee('value="08:00"', false)
        ->assertSee('value="17:00"', false)
        ->assertSee('value="12:00"', false)
        ->assertSee('value="13:00"', false)
        ->assertDontSee('<select name="start_time"', false)
        ->assertDontSee('<select name="end_time"', false)
        ->assertDontSee('<select name="break_start"', false)
        ->assertDontSee('<select name="break_end"', false);
});
