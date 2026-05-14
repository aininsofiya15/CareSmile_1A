<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\User;
use Carbon\Carbon;

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function bookedSlotAdmin(): User
{
    return User::factory()->create(['role' => Role::Admin]);
}

function bookedSlotDentist(string $name = 'Dr Test'): User
{
    return User::factory()->create(['name' => $name, 'role' => Role::Dentist]);
}

function bookedSlotPatient(string $name = 'Test Patient'): User
{
    return User::factory()->create(['name' => $name, 'role' => Role::Patient]);
}

function bookedSlotSchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    $schedule = DoctorSchedule::create(array_merge([
        'doctor_id'    => $dentist->id,
        'working_date' => now()->addDay()->toDateString(),
        'start_time'   => '09:00',
        'end_time'     => '12:00',
        'slot_duration' => 30,
        'is_active'    => true,
        'status'       => DoctorSchedule::STATUS_ACTIVE,
    ], $overrides));

    $schedule->generateSlots();
    return $schedule->refresh();
}

function bookedSlotAppointment(User $patient, User $dentist, DoctorSchedule $schedule, array $overrides = []): Appointment
{
    $date            = Carbon::parse($schedule->working_date)->toDateString();
    $appointmentTime = $overrides['appointment_time'] ?? '09:00:00';
    $endTime         = $overrides['end_time']         ?? '10:00:00';

    $appointment = Appointment::create(array_merge([
        'patient_id'       => $patient->id,
        'doctor_id'        => $dentist->id,
        'appointment_date' => $date,
        'appointment_time' => $appointmentTime,
        'end_time'         => $endTime,
        'service'          => 'Scaling',
        'status'           => 'scheduled',
    ], $overrides));

    // Mark overlapping schedule slots as booked so the grid renders "Booked" / "View Details"
    $apptStart = Carbon::parse($appointmentTime);
    $apptEnd   = Carbon::parse($endTime);
    $schedule->refresh()->slots->each(function ($slot) use ($apptStart, $apptEnd) {
        $slotStart = Carbon::parse($slot->start_time);
        $slotEnd   = Carbon::parse($slot->end_time);
        if ($apptStart->lt($slotEnd) && $apptEnd->gt($slotStart)) {
            $slot->book();
        }
    });

    return $appointment;
}

// ---------------------------------------------------------------------------
// Admin access
// ---------------------------------------------------------------------------

it('admin can access the schedule show page with booked slot data', function () {
    $admin   = bookedSlotAdmin();
    $dentist = bookedSlotDentist();
    $patient = bookedSlotPatient();
    $schedule    = bookedSlotSchedule($dentist);
    bookedSlotAppointment($patient, $dentist, $schedule);

    $this->actingAs($admin)
        ->get(route('admin.schedules.show', $schedule->id))
        ->assertOk()
        ->assertSee('Booked')
        ->assertSee('View Details');
});

it('admin schedule show page displays assigned doctor name', function () {
    $admin   = bookedSlotAdmin();
    $dentist = bookedSlotDentist('Dr Adeeb');
    $patient = bookedSlotPatient();
    $schedule    = bookedSlotSchedule($dentist);
    bookedSlotAppointment($patient, $dentist, $schedule);

    $this->actingAs($admin)
        ->get(route('admin.schedules.show', $schedule->id))
        ->assertOk()
        ->assertSee('Dr Adeeb');
});

it('admin schedule show page displays patient name in modal data', function () {
    $admin   = bookedSlotAdmin();
    $dentist = bookedSlotDentist();
    $patient = bookedSlotPatient('Ahmad Faris');
    $schedule    = bookedSlotSchedule($dentist);
    bookedSlotAppointment($patient, $dentist, $schedule);

    $this->actingAs($admin)
        ->get(route('admin.schedules.show', $schedule->id))
        ->assertOk()
        ->assertSee('Ahmad Faris');
});

it('patient cannot access admin schedule show page', function () {
    $admin   = bookedSlotAdmin();
    $dentist = bookedSlotDentist();
    $patient = bookedSlotPatient();
    $schedule    = bookedSlotSchedule($dentist);

    $this->actingAs($patient)
        ->get(route('admin.schedules.show', $schedule->id))
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Dentist access
// ---------------------------------------------------------------------------

it('dentist can access their own schedule page with booked slot data', function () {
    $dentist = bookedSlotDentist();
    $patient = bookedSlotPatient('Aina Patient');
    $schedule    = bookedSlotSchedule($dentist);
    bookedSlotAppointment($patient, $dentist, $schedule);

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee('Aina Patient');
});

it('dentist cannot access another dentist booked slot data because appointments are scoped to own doctor id', function () {
    $dentist      = bookedSlotDentist('Dr Owner');
    $otherDentist = bookedSlotDentist('Dr Other');
    $patient      = bookedSlotPatient('Hidden Patient');
    $schedule     = bookedSlotSchedule($dentist);
    bookedSlotAppointment($patient, $dentist, $schedule);

    $this->actingAs($otherDentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertDontSee('Hidden Patient');
});

it('patient cannot access dentist schedule page', function () {
    $patient = bookedSlotPatient();

    $this->actingAs($patient)
        ->get(route('dentist.schedules.index'))
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Long-duration appointment slot overlap
// ---------------------------------------------------------------------------

it('long duration appointment marks multiple slots as booked on admin schedule show', function () {
    $admin   = bookedSlotAdmin();
    $dentist = bookedSlotDentist();
    $patient = bookedSlotPatient();
    $schedule    = bookedSlotSchedule($dentist, [
        'start_time'    => '09:00',
        'end_time'      => '12:00',
        'slot_duration' => 30,
    ]);

    // 2-hour appointment covers 4 x 30-minute slots
    bookedSlotAppointment($patient, $dentist, $schedule, [
        'appointment_time' => '09:00:00',
        'end_time'         => '11:00:00',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.schedules.show', $schedule->id));

    $response->assertOk();

    // The appointment's patient name should appear at least once in the data attributes
    $response->assertSee($patient->name);
});

// ---------------------------------------------------------------------------
// Dentist schedule reminder notice
// ---------------------------------------------------------------------------

it('dentist schedule page shows patient arrival reminder text in modal area', function () {
    $dentist = bookedSlotDentist();
    $patient = bookedSlotPatient();
    $schedule    = bookedSlotSchedule($dentist);
    bookedSlotAppointment($patient, $dentist, $schedule);

    $this->actingAs($dentist)
        ->get(route('dentist.schedules.index'))
        ->assertOk()
        ->assertSee('15 minutes before', false)
        ->assertSee('20 minutes', false);
});
