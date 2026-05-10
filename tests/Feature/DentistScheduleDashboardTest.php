<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function (): void {
    Carbon::setTestNow(Carbon::parse('2026-05-11 07:30:00'));
});

afterEach(function (): void {
    Carbon::setTestNow();
});

function dashboardDentistUser(string $name = 'Dr Adeeb'): User
{
    return User::factory()->create([
        'name' => $name,
        'role' => Role::Dentist,
    ]);
}

function dashboardPatientUser(string $name = 'Patient One'): User
{
    return User::factory()->create([
        'name' => $name,
        'role' => Role::Patient,
    ]);
}

function dashboardService(string $name = 'Dental Checkup', int $durationMinutes = 60): Service
{
    return Service::create([
        'name' => $name,
        'description' => $name,
        'price' => 100,
        'duration_minutes' => $durationMinutes,
        'is_active' => true,
    ]);
}

function dashboardSchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    $schedule = DoctorSchedule::create(array_merge([
        'doctor_id' => $dentist->id,
        'working_date' => Carbon::today()->toDateString(),
        'start_time' => '08:00',
        'end_time' => '10:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 30,
        'is_active' => true,
    ], $overrides));

    $schedule->generateSlots();

    return $schedule->refresh();
}

it('allows a dentist to access the schedule dashboard', function () {
    $dentist = dashboardDentistUser();

    $this->actingAs($dentist)
        ->get(route('dentist.dashboard'))
        ->assertOk()
        ->assertSee('My Schedule Dashboard');
});

it('blocks patient and admin users from the dentist schedule dashboard', function () {
    $patient = dashboardPatientUser();
    $admin = User::factory()->create(['role' => Role::Admin]);

    $this->actingAs($patient)
        ->get(route('dentist.dashboard'))
        ->assertForbidden();

    $this->actingAs($admin)
        ->get(route('dentist.dashboard'))
        ->assertForbidden();
});

it('shows today schedule weekly schedule upcoming appointments and calculated slot counts for the authenticated dentist only', function () {
    $dentist = dashboardDentistUser('Dr Adeeb');
    $otherDentist = dashboardDentistUser('Dr Other');
    $patient = dashboardPatientUser('Aina Patient');
    $hiddenPatient = dashboardPatientUser('Hidden Patient');
    $service = dashboardService('Scaling', 60);
    $schedule = dashboardSchedule($dentist);

    dashboardSchedule($otherDentist, [
        'start_time' => '14:00',
        'end_time' => '16:00',
    ]);

    Appointment::create([
        'patient_id' => $patient->id,
        'doctor_id' => $dentist->id,
        'appointment_date' => $schedule->working_date,
        'appointment_time' => '08:00:00',
        'end_time' => null,
        'service' => $service->name,
        'status' => 'scheduled',
    ]);

    Appointment::create([
        'patient_id' => $hiddenPatient->id,
        'doctor_id' => $otherDentist->id,
        'appointment_date' => $schedule->working_date,
        'appointment_time' => '14:00:00',
        'end_time' => '15:00:00',
        'service' => $service->name,
        'status' => 'scheduled',
    ]);

    $this->actingAs($dentist)
        ->get(route('dentist.dashboard'))
        ->assertOk()
        ->assertSee('Today\'s Schedule', false)
        ->assertSee('Weekly Schedule')
        ->assertSee('Upcoming Appointments')
        ->assertSee('08:00 AM - 10:00 AM')
        ->assertSee('Booked Appointments: 1')
        ->assertSee('Total Slots')
        ->assertSee('Booked Slots')
        ->assertSee('Available Slots')
        ->assertSee('2')
        ->assertSee('Aina Patient')
        ->assertSee('Scaling')
        ->assertSee('08:00 AM - 09:00 AM')
        ->assertDontSee('Hidden Patient')
        ->assertDontSee('02:00 PM - 04:00 PM');
});

it('handles a dentist with no schedule or upcoming appointment gracefully', function () {
    $dentist = dashboardDentistUser();

    $this->actingAs($dentist)
        ->get(route('dentist.dashboard'))
        ->assertOk()
        ->assertSee('No schedule assigned for today.')
        ->assertSee('No schedules assigned this week.')
        ->assertSee('No upcoming appointments.');
});
