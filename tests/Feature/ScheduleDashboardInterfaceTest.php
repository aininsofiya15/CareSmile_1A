<?php

use App\Enums\Role;
use App\Models\DoctorSchedule;
use App\Models\User;
use Carbon\Carbon;

function scheduleInterfaceUser(Role $role, string $name): User
{
    return User::factory()->create([
        'name' => $name,
        'role' => $role,
    ]);
}

function scheduleInterfaceSchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    $schedule = DoctorSchedule::create(array_merge([
        'doctor_id' => $dentist->id,
        'working_date' => Carbon::parse('2026-05-12')->toDateString(),
        'start_time' => '08:00',
        'end_time' => '10:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 30,
        'is_active' => true,
        'status' => DoctorSchedule::STATUS_ACTIVE,
        'status_updated_automatically' => false,
    ], $overrides));

    $schedule->generateSlots();

    return $schedule->refresh();
}

it('renders the dentist schedule view as dashboard cards with collapsed slots', function () {
    $dentist = scheduleInterfaceUser(Role::Dentist, 'Dr Schedule UI');
    scheduleInterfaceSchedule($dentist, [
        'break_start' => '09:00',
        'break_end' => '09:30',
    ]);

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee('schedule-card')
        ->assertSee('View Slots')
        ->assertSee('slot-panel')
        ->assertSee('Break Time')
        ->assertSee('Utilized')
        ->assertSee('Available Slots');
});

it('renders the admin schedule detail view with utilization and collapsible generated slots', function () {
    $admin = scheduleInterfaceUser(Role::Admin, 'Admin Schedule UI');
    $dentist = scheduleInterfaceUser(Role::Dentist, 'Dr Admin Schedule UI');
    $schedule = scheduleInterfaceSchedule($dentist);

    $this->actingAs($admin)
        ->get(route('admin.schedules.show', $schedule))
        ->assertOk()
        ->assertSee('Schedule Details')
        ->assertSee('Generated Slots')
        ->assertSee('View Slots')
        ->assertSee('slot-panel')
        ->assertSee('Total Slots')
        ->assertSee('Utilized');
});

it('does not render an empty break section when no break exists', function () {
    $dentist = scheduleInterfaceUser(Role::Dentist, 'Dr No Break UI');
    scheduleInterfaceSchedule($dentist);

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertDontSee('Break Time');
});
