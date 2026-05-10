<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

function createDentistSchedule(User $dentist, array $overrides = []): DoctorSchedule
{
    $schedule = DoctorSchedule::create(array_merge([
        'doctor_id' => $dentist->id,
        'working_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '17:00',
        'break_start' => null,
        'break_end' => null,
        'slot_duration' => 30,
        'is_active' => true,
    ], $overrides));

    $schedule->generateSlots();

    return $schedule->refresh();
}

function createDentalService(int $durationMinutes): Service
{
    return Service::create([
        'name' => "Service {$durationMinutes}",
        'description' => "Service {$durationMinutes} minutes",
        'price' => 100,
        'duration_minutes' => $durationMinutes,
        'is_active' => true,
    ]);
}

function bookAppointment(TestCase $test, User $patient, Service $service, int $slotId): TestResponse
{
    return $test->actingAs($patient)->post(route('patient.appointments.store'), [
        'service_id' => $service->id,
        'slot_id' => $slotId,
    ]);
}

it('books a 30 minute service into one slot', function () {
    $patient = User::factory()->create(['role' => Role::Patient]);
    $dentist = User::factory()->create(['role' => Role::Dentist]);
    $service = createDentalService(30);
    $schedule = createDentistSchedule($dentist);
    $slot = $schedule->slots()->where('start_time', '09:00:00')->firstOrFail();

    bookAppointment($this, $patient, $service, $slot->id)
        ->assertRedirect(route('patient.appointments'));

    $this->assertDatabaseHas('appointments', [
        'patient_id' => $patient->id,
        'doctor_id' => $dentist->id,
        'appointment_time' => '09:00:00',
        'end_time' => '09:30:00',
        'service' => $service->name,
        'status' => 'scheduled',
    ]);
});

it('books a 60 minute service across two 30 minute slots', function () {
    $patient = User::factory()->create(['role' => Role::Patient]);
    $dentist = User::factory()->create(['role' => Role::Dentist]);
    $service = createDentalService(60);
    $schedule = createDentistSchedule($dentist);
    $slot = $schedule->slots()->where('start_time', '09:00:00')->firstOrFail();

    bookAppointment($this, $patient, $service, $slot->id)
        ->assertRedirect(route('patient.appointments'));

    $this->assertDatabaseHas('appointments', [
        'appointment_time' => '09:00:00',
        'end_time' => '10:00:00',
        'service' => $service->name,
    ]);
});

it('books a 120 minute service across multiple generated slots', function () {
    $patient = User::factory()->create(['role' => Role::Patient]);
    $dentist = User::factory()->create(['role' => Role::Dentist]);
    $service = createDentalService(120);
    $schedule = createDentistSchedule($dentist);
    $slot = $schedule->slots()->where('start_time', '09:00:00')->firstOrFail();

    bookAppointment($this, $patient, $service, $slot->id)
        ->assertRedirect(route('patient.appointments'));

    $this->assertDatabaseHas('appointments', [
        'appointment_time' => '09:00:00',
        'end_time' => '11:00:00',
        'service' => $service->name,
    ]);

    expect($schedule->slots()->whereBetween('start_time', ['09:00:00', '10:30:00'])->where('is_available', false)->count())
        ->toBe(4);
});

it('rejects a service range that overlaps an existing appointment', function () {
    $patient = User::factory()->create(['role' => Role::Patient]);
    $otherPatient = User::factory()->create(['role' => Role::Patient]);
    $dentist = User::factory()->create(['role' => Role::Dentist]);
    $service = createDentalService(120);
    $schedule = createDentistSchedule($dentist);
    $slot = $schedule->slots()->where('start_time', '09:00:00')->firstOrFail();

    Appointment::create([
        'patient_id' => $otherPatient->id,
        'doctor_id' => $dentist->id,
        'appointment_date' => $schedule->working_date,
        'appointment_time' => '09:30:00',
        'end_time' => '10:30:00',
        'service' => $service->name,
        'status' => 'scheduled',
    ]);

    bookAppointment($this, $patient, $service, $slot->id)
        ->assertSessionHasErrors([
            'time_slot' => 'This time slot conflicts with another appointment.',
        ]);

    expect(Appointment::where('patient_id', $patient->id)->count())->toBe(0);
});

it('rejects a service range that overlaps break time', function () {
    $patient = User::factory()->create(['role' => Role::Patient]);
    $dentist = User::factory()->create(['role' => Role::Dentist]);
    $service = createDentalService(120);
    $schedule = createDentistSchedule($dentist, [
        'break_start' => '13:00',
        'break_end' => '14:00',
    ]);
    $slot = $schedule->slots()->where('start_time', '12:30:00')->firstOrFail();

    bookAppointment($this, $patient, $service, $slot->id)
        ->assertSessionHasErrors([
            'time_slot' => 'Selected service overlaps with doctor break time.',
        ]);
});

it('rejects a service range that exceeds the schedule end time', function () {
    $patient = User::factory()->create(['role' => Role::Patient]);
    $dentist = User::factory()->create(['role' => Role::Dentist]);
    $service = createDentalService(120);
    $schedule = createDentistSchedule($dentist);
    $slot = $schedule->slots()->where('start_time', '16:00:00')->firstOrFail();

    bookAppointment($this, $patient, $service, $slot->id)
        ->assertSessionHasErrors([
            'time_slot' => 'Selected service does not fit within the doctor schedule.',
        ]);
});

it('only returns starts that can fit the selected service duration', function () {
    $patient = User::factory()->create(['role' => Role::Patient]);
    $dentist = User::factory()->create(['role' => Role::Dentist]);
    $service = createDentalService(120);
    $schedule = createDentistSchedule($dentist, [
        'end_time' => '11:00',
    ]);

    $response = $this->actingAs($patient)->getJson('/get-slots/'.$schedule->working_date->toDateString().'?service_id='.$service->id);

    $response->assertOk();
    $slots = $response->json('0.slots');

    expect($slots)->toHaveCount(1)
        ->and($slots[0]['start_time'])->toBe('09:00')
        ->and($slots[0]['appointment_end_time'])->toBe('11:00:00');
});
