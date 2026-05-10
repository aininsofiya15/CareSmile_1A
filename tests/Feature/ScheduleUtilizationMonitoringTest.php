<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\User;
use App\Services\ScheduleUtilizationService;
use Carbon\Carbon;

beforeEach(function (): void {
    Carbon::setTestNow(Carbon::parse('2026-05-11 08:00:00'));
});

afterEach(function (): void {
    Carbon::setTestNow();
});

function utilizationMonitoringUser(Role $role, string $name): User
{
    return User::factory()->create([
        'name' => $name,
        'role' => $role,
    ]);
}

function utilizationMonitoringSchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    return DoctorSchedule::create(array_merge([
        'doctor_id' => $dentist->id,
        'working_date' => Carbon::today()->toDateString(),
        'start_time' => '08:00',
        'end_time' => '13:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 30,
        'is_active' => true,
        'status' => DoctorSchedule::STATUS_ACTIVE,
        'status_updated_automatically' => false,
    ], $overrides));
}

function utilizationMonitoringService(string $name = 'Dental Checkup', int $durationMinutes = 30): Service
{
    return Service::create([
        'name' => $name,
        'description' => $name,
        'price' => 100,
        'duration_minutes' => $durationMinutes,
        'is_active' => true,
    ]);
}

function utilizationMonitoringAppointment(User $patient, User $dentist, DoctorSchedule $schedule, string $startTime, string $endTime, string $status = 'scheduled'): Appointment
{
    return Appointment::create([
        'patient_id' => $patient->id,
        'doctor_id' => $dentist->id,
        'appointment_date' => $schedule->working_date,
        'appointment_time' => $startTime,
        'end_time' => $endTime,
        'service' => 'Dental Checkup',
        'status' => $status,
    ]);
}

it('reports no bookings when a schedule has generated slots and no appointments', function () {
    $service = app(ScheduleUtilizationService::class);
    $dentist = utilizationMonitoringUser(Role::Dentist, 'Dr No Bookings');
    $schedule = utilizationMonitoringSchedule($dentist);

    $summary = $service->getScheduleUtilizationSummary($schedule);

    expect($summary['total_slots'])->toBe(10)
        ->and($summary['booked_slots'])->toBe(0)
        ->and($summary['remaining_slots'])->toBe(10)
        ->and($summary['utilization_percentage'])->toBe(0)
        ->and($summary['utilization_label'])->toBe('No Bookings');
});

it('reports moderate utilization for a partially booked schedule', function () {
    $service = app(ScheduleUtilizationService::class);
    $dentist = utilizationMonitoringUser(Role::Dentist, 'Dr Moderate');
    $patient = utilizationMonitoringUser(Role::Patient, 'Patient Moderate');
    $schedule = utilizationMonitoringSchedule($dentist);

    utilizationMonitoringService();

    foreach (['08:00:00', '08:30:00', '09:00:00', '09:30:00'] as $startTime) {
        utilizationMonitoringAppointment(
            $patient,
            $dentist,
            $schedule,
            $startTime,
            Carbon::parse($startTime)->addMinutes(30)->format('H:i:s')
        );
    }

    $summary = $service->getScheduleUtilizationSummary($schedule);

    expect($summary['total_slots'])->toBe(10)
        ->and($summary['booked_slots'])->toBe(4)
        ->and($summary['remaining_slots'])->toBe(6)
        ->and($summary['utilization_percentage'])->toBe(40)
        ->and($summary['utilization_label'])->toBe('Moderate Utilization');
});

it('reports high utilization for a nearly full schedule', function () {
    $service = app(ScheduleUtilizationService::class);
    $dentist = utilizationMonitoringUser(Role::Dentist, 'Dr High');
    $patient = utilizationMonitoringUser(Role::Patient, 'Patient High');
    $schedule = utilizationMonitoringSchedule($dentist);

    utilizationMonitoringService();

    foreach (range(0, 7) as $index) {
        $startTime = Carbon::parse('08:00')->addMinutes($index * 30);

        utilizationMonitoringAppointment(
            $patient,
            $dentist,
            $schedule,
            $startTime->format('H:i:s'),
            $startTime->copy()->addMinutes(30)->format('H:i:s')
        );
    }

    $summary = $service->getScheduleUtilizationSummary($schedule);

    expect($summary['total_slots'])->toBe(10)
        ->and($summary['booked_slots'])->toBe(8)
        ->and($summary['remaining_slots'])->toBe(2)
        ->and($summary['utilization_percentage'])->toBe(80)
        ->and($summary['utilization_label'])->toBe('High Utilization');
});

it('reports fully booked utilization when all generated slots are occupied', function () {
    $service = app(ScheduleUtilizationService::class);
    $dentist = utilizationMonitoringUser(Role::Dentist, 'Dr Full');
    $patient = utilizationMonitoringUser(Role::Patient, 'Patient Full');
    $schedule = utilizationMonitoringSchedule($dentist);

    utilizationMonitoringService();

    foreach (range(0, 9) as $index) {
        $startTime = Carbon::parse('08:00')->addMinutes($index * 30);

        utilizationMonitoringAppointment(
            $patient,
            $dentist,
            $schedule,
            $startTime->format('H:i:s'),
            $startTime->copy()->addMinutes(30)->format('H:i:s')
        );
    }

    $summary = $service->getScheduleUtilizationSummary($schedule);

    expect($summary['total_slots'])->toBe(10)
        ->and($summary['booked_slots'])->toBe(10)
        ->and($summary['remaining_slots'])->toBe(0)
        ->and($summary['utilization_percentage'])->toBe(100)
        ->and($summary['utilization_label'])->toBe('Fully Booked');
});

it('excludes break time from total generated slots', function () {
    $service = app(ScheduleUtilizationService::class);
    $dentist = utilizationMonitoringUser(Role::Dentist, 'Dr Break');
    $schedule = utilizationMonitoringSchedule($dentist, [
        'start_time' => '08:00',
        'end_time' => '17:00',
        'break_start' => '12:00',
        'break_end' => '13:00',
    ]);

    $summary = $service->getScheduleUtilizationSummary($schedule);

    expect($summary['total_slots'])->toBe(16)
        ->and($summary['booked_slots'])->toBe(0)
        ->and($summary['remaining_slots'])->toBe(16);
});

it('handles invalid slot duration without crashing', function () {
    $service = app(ScheduleUtilizationService::class);
    $dentist = utilizationMonitoringUser(Role::Dentist, 'Dr Invalid');
    $patient = utilizationMonitoringUser(Role::Patient, 'Patient Invalid');
    $schedule = utilizationMonitoringSchedule($dentist, [
        'slot_duration' => 0,
    ]);

    utilizationMonitoringService();
    utilizationMonitoringAppointment($patient, $dentist, $schedule, '08:00:00', '08:30:00');

    $summary = $service->getScheduleUtilizationSummary($schedule);

    expect($summary['total_slots'])->toBe(0)
        ->and($summary['booked_slots'])->toBe(0)
        ->and($summary['remaining_slots'])->toBe(0)
        ->and($summary['utilization_percentage'])->toBe(0)
        ->and($summary['utilization_label'])->toBe('No Bookings');
});

it('does not count cancelled appointments as booked slots', function () {
    $service = app(ScheduleUtilizationService::class);
    $dentist = utilizationMonitoringUser(Role::Dentist, 'Dr Cancelled');
    $patient = utilizationMonitoringUser(Role::Patient, 'Patient Cancelled');
    $schedule = utilizationMonitoringSchedule($dentist);

    utilizationMonitoringService();
    utilizationMonitoringAppointment($patient, $dentist, $schedule, '08:00:00', '08:30:00', 'cancelled');

    $summary = $service->getScheduleUtilizationSummary($schedule);

    expect($summary['booked_slots'])->toBe(0)
        ->and($summary['remaining_slots'])->toBe(10);
});

it('shows utilization monitoring on the admin schedule list and dentist dashboard', function () {
    $admin = utilizationMonitoringUser(Role::Admin, 'Admin Manager');
    $dentist = utilizationMonitoringUser(Role::Dentist, 'Dr Dashboard');

    utilizationMonitoringSchedule($dentist);

    $this->actingAs($admin)
        ->get(route('admin.schedules.index'))
        ->assertOk()
        ->assertSee('Generated Slots')
        ->assertSee('Average Utilization')
        ->assertSee('No Bookings');

    $this->actingAs($dentist)
        ->get(route('dentist.dashboard'))
        ->assertOk()
        ->assertSee('Today Utilization')
        ->assertSee('Weekly Utilization')
        ->assertSee('Workload Summary');
});
