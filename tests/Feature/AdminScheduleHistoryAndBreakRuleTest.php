<?php

use App\Enums\Role;
use App\Models\DoctorSchedule;
use App\Models\User;
use Carbon\Carbon;

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function adminHistAdmin(): User
{
    return User::factory()->create(['role' => Role::Admin]);
}

function adminHistDentist(string $name = 'Dr AdminHist'): User
{
    return User::factory()->create(['name' => $name, 'role' => Role::Dentist]);
}

function adminHistPatient(): User
{
    return User::factory()->create(['role' => Role::Patient]);
}

function adminHistFutureSchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    $schedule = DoctorSchedule::create(array_merge([
        'doctor_id'     => $dentist->id,
        'working_date'  => now()->addDay()->toDateString(),
        'start_time'    => '09:00',
        'end_time'      => '12:00',
        'slot_duration' => 30,
        'is_active'     => true,
        'status'        => DoctorSchedule::STATUS_ACTIVE,
    ], $overrides));

    $schedule->generateSlots();
    return $schedule->refresh();
}

function adminHistPastSchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    $schedule = DoctorSchedule::create(array_merge([
        'doctor_id'     => $dentist->id,
        'working_date'  => now()->subDay()->toDateString(),
        'start_time'    => '09:00',
        'end_time'      => '12:00',
        'slot_duration' => 30,
        'is_active'     => true,
        'status'        => DoctorSchedule::STATUS_ACTIVE,
    ], $overrides));

    $schedule->generateSlots();
    return $schedule->refresh();
}

// ---------------------------------------------------------------------------
// Admin index: two-section layout
// ---------------------------------------------------------------------------

it('admin schedules page shows active and upcoming section heading', function () {
    $admin   = adminHistAdmin();
    $dentist = adminHistDentist();
    adminHistFutureSchedule($dentist);

    $this->actingAs($admin)
        ->get(route('admin.schedules.index'))
        ->assertOk()
        ->assertSee('Active &amp; Upcoming Schedules', false);
});

it('admin schedules page shows schedule history section heading when past schedules exist', function () {
    $admin   = adminHistAdmin();
    $dentist = adminHistDentist();
    adminHistPastSchedule($dentist);

    $this->actingAs($admin)
        ->get(route('admin.schedules.index'))
        ->assertOk()
        ->assertSee('Schedule History', false);
});

it('admin schedules page does not show history heading when no past schedules exist', function () {
    $admin   = adminHistAdmin();
    $dentist = adminHistDentist();
    adminHistFutureSchedule($dentist);

    $this->actingAs($admin)
        ->get(route('admin.schedules.index'))
        ->assertOk()
        ->assertDontSee('Schedule History', false);
});

it('future schedule date appears in the active section of the admin page', function () {
    $admin   = adminHistAdmin();
    $dentist = adminHistDentist();
    $schedule = adminHistFutureSchedule($dentist);

    $dateLabel = Carbon::parse($schedule->working_date)->format('M d, Y');

    $this->actingAs($admin)
        ->get(route('admin.schedules.index'))
        ->assertOk()
        ->assertSee($dateLabel);
});

it('past schedule date appears on the admin page inside the history section', function () {
    $admin    = adminHistAdmin();
    $dentist  = adminHistDentist();
    $schedule = adminHistPastSchedule($dentist);

    $dateLabel = Carbon::parse($schedule->working_date)->format('M d, Y');

    $this->actingAs($admin)
        ->get(route('admin.schedules.index'))
        ->assertOk()
        ->assertSee('Schedule History', false)
        ->assertSee($dateLabel);
});

it('admin page active section uses active and upcoming schedules count for utilization summary', function () {
    $admin   = adminHistAdmin();
    $dentist = adminHistDentist();
    adminHistFutureSchedule($dentist);
    adminHistPastSchedule($dentist);

    $this->actingAs($admin)
        ->get(route('admin.schedules.index'))
        ->assertOk()
        ->assertSee('Active / Upcoming Schedules', false);
});

// ---------------------------------------------------------------------------
// Break time validation: backend
// ---------------------------------------------------------------------------

it('admin can create a schedule with 30-minute break time', function () {
    $admin   = adminHistAdmin();
    $dentist = adminHistDentist();

    $this->actingAs($admin)
        ->post(route('admin.schedules.store'), [
            'doctor_id'     => $dentist->id,
            'working_date'  => now()->addDays(3)->toDateString(),
            'start_time'    => '09:00',
            'end_time'      => '17:00',
            'break_start'   => '12:00',
            'break_end'     => '12:30',
            'slot_duration' => 30,
            'status'        => DoctorSchedule::STATUS_ACTIVE,
        ])
        ->assertRedirect(route('admin.schedules.index'));

    $this->assertDatabaseHas('doctor_schedules', [
        'doctor_id' => $dentist->id,
    ]);
    $this->assertTrue(
        \App\Models\DoctorSchedule::where('doctor_id', $dentist->id)->whereNotNull('break_start')->whereNotNull('break_end')->exists()
    );
});

it('admin can create a schedule with 60-minute break time', function () {
    $admin   = adminHistAdmin();
    $dentist = adminHistDentist();

    $this->actingAs($admin)
        ->post(route('admin.schedules.store'), [
            'doctor_id'     => $dentist->id,
            'working_date'  => now()->addDays(4)->toDateString(),
            'start_time'    => '09:00',
            'end_time'      => '17:00',
            'break_start'   => '12:00',
            'break_end'     => '13:00',
            'slot_duration' => 30,
            'status'        => DoctorSchedule::STATUS_ACTIVE,
        ])
        ->assertRedirect(route('admin.schedules.index'));

    $this->assertDatabaseHas('doctor_schedules', [
        'doctor_id' => $dentist->id,
    ]);
});

it('admin cannot create a schedule with break time exceeding 1 hour', function () {
    $admin   = adminHistAdmin();
    $dentist = adminHistDentist();

    $this->actingAs($admin)
        ->post(route('admin.schedules.store'), [
            'doctor_id'     => $dentist->id,
            'working_date'  => now()->addDays(5)->toDateString(),
            'start_time'    => '09:00',
            'end_time'      => '17:00',
            'break_start'   => '12:00',
            'break_end'     => '13:30',
            'slot_duration' => 30,
            'status'        => DoctorSchedule::STATUS_ACTIVE,
        ])
        ->assertSessionHasErrors('break_end');
});

it('admin cannot update a schedule with break time exceeding 1 hour', function () {
    $admin    = adminHistAdmin();
    $dentist  = adminHistDentist();
    $schedule = adminHistFutureSchedule($dentist);

    $this->actingAs($admin)
        ->put(route('admin.schedules.update', $schedule->id), [
            'doctor_id'     => $dentist->id,
            'working_date'  => $schedule->working_date->toDateString(),
            'start_time'    => '09:00',
            'end_time'      => '17:00',
            'break_start'   => '12:00',
            'break_end'     => '14:00',
            'slot_duration' => 30,
            'status'        => DoctorSchedule::STATUS_ACTIVE,
        ])
        ->assertSessionHasErrors('break_end');
});

it('conflict check endpoint rejects break time exceeding 1 hour', function () {
    $admin   = adminHistAdmin();
    $dentist = adminHistDentist();

    // break_start 12:00, break_end 13:30 = 90 min > 60, both on 30-min clinic intervals
    $this->actingAs($admin)
        ->getJson(route('admin.schedules.check-conflict', [
            'doctor_id'     => $dentist->id,
            'working_date'  => now()->addDays(6)->toDateString(),
            'start_time'    => '09:00',
            'end_time'      => '17:00',
            'break_start'   => '12:00',
            'break_end'     => '13:30',
            'slot_duration' => 30,
        ]))
        ->assertOk()
        ->assertJson(['hasConflict' => true])
        ->assertJsonFragment(['message' => 'Break time cannot exceed 1 hour.']);
});

it('slot preview endpoint rejects break time exceeding 1 hour', function () {
    $admin   = adminHistAdmin();
    $dentist = adminHistDentist();

    $this->actingAs($admin)
        ->getJson(route('admin.schedules.preview-slots', [
            'doctor_id'     => $dentist->id,
            'working_date'  => now()->addDays(7)->toDateString(),
            'start_time'    => '09:00',
            'end_time'      => '17:00',
            'break_start'   => '12:00',
            'break_end'     => '14:00',
            'slot_duration' => 30,
        ]))
        ->assertOk()
        ->assertJson(['success' => false])
        ->assertJsonFragment(['message' => 'Break time cannot exceed 1 hour.']);
});

// ---------------------------------------------------------------------------
// Other role pages not broken
// ---------------------------------------------------------------------------

it('dentist my schedule page still loads after admin changes', function () {
    $dentist = adminHistDentist();
    adminHistFutureSchedule($dentist);

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk();
});

it('patient cannot access admin schedule index', function () {
    $patient = adminHistPatient();

    $this->actingAs($patient)
        ->get(route('admin.schedules.index'))
        ->assertForbidden();
});
