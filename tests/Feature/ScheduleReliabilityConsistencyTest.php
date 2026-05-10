<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\User;
use App\Services\ScheduleSlotService;
use App\Services\ScheduleUtilizationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

beforeEach(function (): void {
    Carbon::setTestNow(Carbon::parse('2026-05-11 08:00:00'));
});

afterEach(function (): void {
    Carbon::setTestNow();
});

function reliabilityUser(Role $role, string $name): User
{
    return User::factory()->create([
        'name' => $name,
        'role' => $role,
    ]);
}

function reliabilityService(string $name = 'Reliability Service', int $durationMinutes = 30): Service
{
    return Service::create([
        'name' => $name,
        'description' => $name,
        'price' => 100,
        'duration_minutes' => $durationMinutes,
        'is_active' => true,
    ]);
}

function reliabilitySchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    $schedule = DoctorSchedule::create(array_merge([
        'doctor_id' => $dentist->id,
        'working_date' => now()->addDay()->toDateString(),
        'start_time' => '08:00',
        'end_time' => '17:00',
        'break_start' => '12:00',
        'break_end' => '13:00',
        'slot_duration' => 30,
        'is_active' => true,
        'status' => DoctorSchedule::STATUS_ACTIVE,
        'status_updated_automatically' => false,
    ], $overrides));

    $schedule->generateSlots();

    return $schedule->refresh();
}

function reliabilityBook(TestCase $test, User $patient, Service $service, int $slotId): TestResponse
{
    return $test->actingAs($patient)->post(route('patient.appointments.store'), [
        'service_id' => $service->id,
        'slot_id' => $slotId,
    ]);
}

it('keeps slot generation consistent across admin preview patient booking and dentist dashboard', function () {
    $admin = reliabilityUser(Role::Admin, 'Admin Reliability');
    $patient = reliabilityUser(Role::Patient, 'Patient Reliability');
    $dentist = reliabilityUser(Role::Dentist, 'Dr Reliability');
    $service = reliabilityService();
    $schedule = reliabilitySchedule($dentist);

    $adminPreview = $this->actingAs($admin)->getJson(route('admin.schedules.preview-slots', [
        'doctor_id' => $dentist->id,
        'working_date' => $schedule->working_date->toDateString(),
        'start_time' => '08:00',
        'end_time' => '17:00',
        'break_start' => '12:00',
        'break_end' => '13:00',
        'slot_duration' => 30,
    ]));

    $patientSlots = $this->actingAs($patient)
        ->getJson('/get-slots/'.$schedule->working_date->toDateString().'?service_id='.$service->id);

    $adminPreview->assertOk()->assertJsonPath('summary.total_slots', 16);
    $patientSlots->assertOk();

    expect($patientSlots->json('0.slots'))->toHaveCount(16);

    $this->actingAs($dentist)
        ->get(route('dentist.dashboard'))
        ->assertOk()
        ->assertSee('Slots: 16 total, 0 booked');
});

it('excludes break time and prevents overlapping generated slots', function () {
    $dentist = reliabilityUser(Role::Dentist, 'Dr Break Reliability');
    $schedule = reliabilitySchedule($dentist);
    $ranges = app(ScheduleSlotService::class)->calculateSlotRanges($schedule);

    $keys = collect($ranges)->map(fn (array $range): string => $range['start']->format('H:i').'|'.$range['end']->format('H:i'));

    expect($ranges)->toHaveCount(16)
        ->and($keys->contains('12:00|12:30'))->toBeFalse()
        ->and($keys->contains('12:30|13:00'))->toBeFalse();

    collect($ranges)->sliding(2)->each(function (Collection $pair): void {
        if ($pair->count() === 2) {
            expect($pair->first()['end']->lte($pair->last()['start']))->toBeTrue();
        }
    });
});

it('does not create duplicate generated slots', function () {
    $dentist = reliabilityUser(Role::Dentist, 'Dr Duplicate Reliability');
    $schedule = reliabilitySchedule($dentist);

    $schedule->generateSlots();
    $schedule->generateSlots();

    $keys = $schedule->slots()
        ->get()
        ->map(fn ($slot): string => $slot->start_time->format('H:i:s').'|'.$slot->end_time->format('H:i:s'));

    expect($schedule->slots()->count())->toBe(16)
        ->and($keys->unique()->count())->toBe(16);
});

it('rejects invalid schedule time ranges', function () {
    $admin = reliabilityUser(Role::Admin, 'Admin Invalid Range');
    $dentist = reliabilityUser(Role::Dentist, 'Dr Invalid Range');

    $this->actingAs($admin)
        ->post(route('admin.schedules.store'), [
            'doctor_id' => $dentist->id,
            'working_date' => now()->addDay()->toDateString(),
            'start_time' => '17:00',
            'end_time' => '08:00',
            'break_start' => null,
            'break_end' => null,
            'slot_duration' => 30,
            'status' => DoctorSchedule::STATUS_ACTIVE,
        ])
        ->assertSessionHasErrors('end_time');
});

it('rejects invalid break time outside working hours', function () {
    $admin = reliabilityUser(Role::Admin, 'Admin Invalid Break');
    $dentist = reliabilityUser(Role::Dentist, 'Dr Invalid Break');

    $this->actingAs($admin)
        ->post(route('admin.schedules.store'), [
            'doctor_id' => $dentist->id,
            'working_date' => now()->addDay()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '17:00',
            'break_start' => '07:30',
            'break_end' => '08:30',
            'slot_duration' => 30,
            'status' => DoctorSchedule::STATUS_ACTIVE,
        ])
        ->assertSessionHasErrors('break_start');
});

it('rejects slot duration that cannot fit any valid working session', function () {
    $admin = reliabilityUser(Role::Admin, 'Admin Invalid Duration');
    $dentist = reliabilityUser(Role::Dentist, 'Dr Invalid Duration');

    $this->actingAs($admin)
        ->post(route('admin.schedules.store'), [
            'doctor_id' => $dentist->id,
            'working_date' => now()->addDay()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'break_start' => '09:00',
            'break_end' => '09:30',
            'slot_duration' => 90,
            'status' => DoctorSchedule::STATUS_ACTIVE,
        ])
        ->assertSessionHasErrors('slot_duration');
});

it('handles slot duration larger than working hours without crashing', function () {
    $dentist = reliabilityUser(Role::Dentist, 'Dr Large Duration');
    $schedule = reliabilitySchedule($dentist, [
        'start_time' => '08:00',
        'end_time' => '09:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 120,
    ]);

    expect($schedule->slots()->count())->toBe(0)
        ->and(app(ScheduleSlotService::class)->validateSlotGeneration($schedule))->toContain('Slot duration is too large for the selected schedule.');
});

it('returns empty generated slots safely for zero or negative duration', function () {
    $slotService = app(ScheduleSlotService::class);

    expect($slotService->calculateSlotRangesFromValues('2026-05-12', '08:00', '10:00', 0))->toBe([])
        ->and($slotService->calculateSlotRangesFromValues('2026-05-12', '08:00', '10:00', -30))->toBe([]);
});

it('hides long service starts that cannot fit around break time', function () {
    $patient = reliabilityUser(Role::Patient, 'Patient Long Break');
    $dentist = reliabilityUser(Role::Dentist, 'Dr Long Break');
    $service = reliabilityService('Long Break Service', 90);
    $schedule = reliabilitySchedule($dentist, [
        'start_time' => '08:00',
        'end_time' => '10:00',
        'break_start' => '09:00',
        'break_end' => '09:30',
        'slot_duration' => 30,
    ]);

    $this->actingAs($patient)
        ->getJson('/get-slots/'.$schedule->working_date->toDateString().'?service_id='.$service->id)
        ->assertOk()
        ->assertExactJson([]);
});

it('synchronizes available slots after booking and cancellation', function () {
    $patient = reliabilityUser(Role::Patient, 'Patient Sync');
    $dentist = reliabilityUser(Role::Dentist, 'Dr Sync');
    $service = reliabilityService('Sync Service', 30);
    $schedule = reliabilitySchedule($dentist, [
        'start_time' => '09:00',
        'end_time' => '10:00',
        'break_start' => null,
        'break_end' => null,
    ]);
    $slot = $schedule->slots()->where('start_time', '09:00:00')->firstOrFail();

    reliabilityBook($this, $patient, $service, $slot->id)->assertRedirect(route('patient.appointments'));
    expect($schedule->refresh()->slots()->where('is_available', true)->count())->toBe(1);

    $appointment = Appointment::where('patient_id', $patient->id)->firstOrFail();

    $this->actingAs($patient)
        ->post(route('patient.appointments.cancel', $appointment->id))
        ->assertRedirect(route('patient.appointments'));

    expect($schedule->refresh()->slots()->where('is_available', true)->count())->toBe(2);
});

it('marks the schedule fully booked when the final slot is booked', function () {
    $patient = reliabilityUser(Role::Patient, 'Patient Full Sync');
    $dentist = reliabilityUser(Role::Dentist, 'Dr Full Sync');
    $service = reliabilityService('Full Sync Service', 60);
    $schedule = reliabilitySchedule($dentist, [
        'start_time' => '09:00',
        'end_time' => '10:00',
        'break_start' => null,
        'break_end' => null,
    ]);
    $slot = $schedule->slots()->where('start_time', '09:00:00')->firstOrFail();

    reliabilityBook($this, $patient, $service, $slot->id)->assertRedirect(route('patient.appointments'));

    expect($schedule->refresh()->status)->toBe(DoctorSchedule::STATUS_FULLY_BOOKED)
        ->and($schedule->is_active)->toBeFalse()
        ->and($schedule->slots()->where('is_available', false)->count())->toBe(2);
});

it('recalculates utilization after booking', function () {
    $patient = reliabilityUser(Role::Patient, 'Patient Utilization Sync');
    $dentist = reliabilityUser(Role::Dentist, 'Dr Utilization Sync');
    $service = reliabilityService('Utilization Sync Service', 30);
    $schedule = reliabilitySchedule($dentist, [
        'start_time' => '09:00',
        'end_time' => '10:00',
        'break_start' => null,
        'break_end' => null,
    ]);
    $slot = $schedule->slots()->where('start_time', '09:00:00')->firstOrFail();
    $utilization = app(ScheduleUtilizationService::class);

    expect($utilization->getScheduleUtilizationSummary($schedule)['utilization_percentage'])->toBe(0);

    reliabilityBook($this, $patient, $service, $slot->id)->assertRedirect(route('patient.appointments'));

    $summary = $utilization->getScheduleUtilizationSummary($schedule->refresh());

    expect($summary['booked_slots'])->toBe(1)
        ->and($summary['remaining_slots'])->toBe(1)
        ->and($summary['utilization_percentage'])->toBe(50);
});
