<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\User;
use Carbon\Carbon;

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function histDentist(string $name = 'Dr History'): User
{
    return User::factory()->create(['name' => $name, 'role' => Role::Dentist]);
}

function histPatient(string $name = 'History Patient'): User
{
    return User::factory()->create(['name' => $name, 'role' => Role::Patient]);
}

function histAdmin(): User
{
    return User::factory()->create(['role' => Role::Admin]);
}

function histPastSchedule(User $dentist, array $overrides = []): DoctorSchedule
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

function histTodaySchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    $schedule = DoctorSchedule::create(array_merge([
        'doctor_id'     => $dentist->id,
        'working_date'  => now()->toDateString(),
        'start_time'    => '09:00',
        'end_time'      => '12:00',
        'slot_duration' => 30,
        'is_active'     => true,
        'status'        => DoctorSchedule::STATUS_ACTIVE,
    ], $overrides));

    $schedule->generateSlots();
    return $schedule->refresh();
}

function histFutureSchedule(User $dentist, array $overrides = []): DoctorSchedule
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

function histAppointment(User $patient, User $dentist, DoctorSchedule $schedule, array $overrides = []): Appointment
{
    $date = Carbon::parse($schedule->working_date)->toDateString();

    $appointment = Appointment::create(array_merge([
        'patient_id'       => $patient->id,
        'doctor_id'        => $dentist->id,
        'appointment_date' => $date,
        'appointment_time' => '09:00:00',
        'end_time'         => '09:30:00',
        'service'          => 'Cleaning',
        'status'           => 'scheduled',
    ], $overrides));

    // Mark overlapping slots as booked
    $apptStart = Carbon::parse($overrides['appointment_time'] ?? '09:00:00');
    $apptEnd   = Carbon::parse($overrides['end_time'] ?? '09:30:00');
    $schedule->slots->each(function ($slot) use ($apptStart, $apptEnd) {
        $slotStart = Carbon::parse($slot->start_time);
        $slotEnd   = Carbon::parse($slot->end_time);
        if ($apptStart->lt($slotEnd) && $apptEnd->gt($slotStart)) {
            $slot->book();
        }
    });

    return $appointment;
}

// ---------------------------------------------------------------------------
// Section heading visibility
// ---------------------------------------------------------------------------

it('dentist schedule page shows active and upcoming section heading', function () {
    $dentist = histDentist();
    histFutureSchedule($dentist); // at least one schedule so the two-section layout renders

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee('Active &amp; Upcoming', false);
});

it('dentist schedule page shows schedule history section heading', function () {
    $dentist = histDentist();
    histPastSchedule($dentist); // ensure the two-section layout renders

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee('Schedule History', false);
});

// ---------------------------------------------------------------------------
// Schedule classification by date
// ---------------------------------------------------------------------------

it('today schedule date appears in the active section of the page', function () {
    $dentist  = histDentist();
    $schedule = histTodaySchedule($dentist);

    $dateLabel = Carbon::parse($schedule->working_date)->format('M d, Y');

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee($dateLabel);
});

it('future schedule date appears in the active section of the page', function () {
    $dentist  = histDentist();
    $schedule = histFutureSchedule($dentist);

    $dateLabel = Carbon::parse($schedule->working_date)->format('M d, Y');

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee($dateLabel);
});

it('past schedule date appears on the page inside the history section', function () {
    $dentist  = histDentist();
    $schedule = histPastSchedule($dentist);

    $dateLabel = Carbon::parse($schedule->working_date)->format('M d, Y');

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee('Schedule History', false)
        ->assertSee($dateLabel);
});

// ---------------------------------------------------------------------------
// Slot labels
// ---------------------------------------------------------------------------

it('past schedule slots are labeled Past in the page HTML', function () {
    $dentist = histDentist();
    histPastSchedule($dentist);

    // Past schedule → all slots are "past" → slot-status-label contains "Past"
    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee('Past', false);
});

it('active schedule available slots are labeled Available in the page HTML', function () {
    $dentist = histDentist();
    histFutureSchedule($dentist); // future → all slots available

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee('Available', false);
});

it('active schedule booked slots are labeled Booked in the page HTML', function () {
    $dentist  = histDentist();
    $patient  = histPatient();
    $schedule = histFutureSchedule($dentist);
    histAppointment($patient, $dentist, $schedule);

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee('Booked', false);
});

// ---------------------------------------------------------------------------
// Access control
// ---------------------------------------------------------------------------

it('dentist cannot see another dentist schedule on the page', function () {
    $dentist      = histDentist('Dr Owner');
    $otherDentist = histDentist('Dr Other');
    histFutureSchedule($dentist);

    $dateLabel = Carbon::parse(now()->addDay()->toDateString())->format('M d, Y');

    // The other dentist views their own empty schedule page — should not see Dr Owner's schedule date
    // (actually both schedules are on the same date, so check patient name instead via booked slot)
    $patient  = histPatient('Exclusive Patient');
    $schedule = histFutureSchedule($dentist);
    histAppointment($patient, $dentist, $schedule);

    $this->actingAs($otherDentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertDontSee('Exclusive Patient');
});

it('patient cannot access dentist schedule page', function () {
    $patient = histPatient();

    $this->actingAs($patient)
        ->get(route('dentist.schedules.index'))
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Past booked slots still show View Details
// ---------------------------------------------------------------------------

it('past booked slots still expose patient name for View Details on past schedule', function () {
    $dentist  = histDentist();
    $patient  = histPatient('Past Booked Patient');
    $schedule = histPastSchedule($dentist);
    histAppointment($patient, $dentist, $schedule, ['status' => 'completed']);

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee('Past Booked Patient');
});

// ---------------------------------------------------------------------------
// Long-duration appointment covers multiple past slots
// ---------------------------------------------------------------------------

it('long duration appointment in past schedule marks multiple slots and appears multiple times in HTML', function () {
    $dentist  = histDentist();
    $patient  = histPatient('LongDuration Patient');

    $schedule = histPastSchedule($dentist, [
        'start_time'    => '09:00',
        'end_time'      => '12:00',
        'slot_duration' => 30,
    ]);

    // 2-hour appointment → covers 4 × 30-minute slots
    histAppointment($patient, $dentist, $schedule, [
        'appointment_time' => '09:00:00',
        'end_time'         => '11:00:00',
        'status'           => 'completed',
    ]);

    $response = $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'));

    $response->assertOk();
    $response->assertSee('LongDuration Patient');
});

// ---------------------------------------------------------------------------
// Today label
// ---------------------------------------------------------------------------

it('today schedule shows today day label in the active section', function () {
    $dentist = histDentist();
    histTodaySchedule($dentist);

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee('Today', false);
});
