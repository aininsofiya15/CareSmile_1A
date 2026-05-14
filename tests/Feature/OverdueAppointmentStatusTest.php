<?php

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OverdueAppointmentStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_appointments_page_shows_overdue_warning_when_past_scheduled_appointments_exist(): void
    {
        $admin = $this->overdueAdmin();
        $dentist = $this->overdueDentist();
        $patient = $this->overduePatient();
        $this->pastScheduledAppointment($patient, $dentist);

        $this->actingAs($admin)
            ->get(route('admin.appointments', ['status' => 'all']))
            ->assertOk()
            ->assertSee('overdue')
            ->assertSee('need status review', false);
    }

    public function test_admin_appointments_page_shows_no_overdue_warning_when_no_overdue_appointments_exist(): void
    {
        $admin = $this->overdueAdmin();
        $dentist = $this->overdueDentist();
        $patient = $this->overduePatient();
        $this->futureScheduledAppointment($patient, $dentist);

        $this->actingAs($admin)
            ->get(route('admin.appointments', ['status' => 'all']))
            ->assertOk()
            ->assertSee('Reminder:', false)
            ->assertDontSee('need status review', false);
    }

    public function test_overdue_filter_on_admin_appointments_page_returns_only_overdue_appointments(): void
    {
        $admin = $this->overdueAdmin();
        $dentist = $this->overdueDentist();
        $patient = $this->overduePatient();

        $past = $this->pastScheduledAppointment($patient, $dentist);
        $future = $this->futureScheduledAppointment($patient, $dentist);

        $response = $this->actingAs($admin)
            ->get(route('admin.appointments', ['status' => 'overdue']));

        $response->assertOk();
        $response->assertSee($past->service);
        $response->assertDontSee($future->service);
    }

    public function test_future_scheduled_appointment_is_not_shown_as_overdue(): void
    {
        $admin = $this->overdueAdmin();
        $dentist = $this->overdueDentist();
        $patient = $this->overduePatient();
        $this->futureScheduledAppointment($patient, $dentist);

        $this->actingAs($admin)
            ->get(route('admin.appointments', ['status' => 'scheduled']))
            ->assertOk()
            ->assertDontSee('<span class="overdue-badge">', false);
    }

    public function test_admin_can_bulk_mark_overdue_appointments_as_no_show_via_mark_overdue_route(): void
    {
        $admin = $this->overdueAdmin();
        $dentist = $this->overdueDentist();
        $patient = $this->overduePatient();
        $appointment = $this->pastScheduledAppointment($patient, $dentist);

        $this->actingAs($admin)
            ->post(route('admin.appointments.mark_overdue'))
            ->assertRedirect(route('admin.appointments'));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'no_show',
        ]);
    }

    public function test_future_appointment_is_not_marked_no_show_by_bulk_overdue_action(): void
    {
        $admin = $this->overdueAdmin();
        $dentist = $this->overdueDentist();
        $patient = $this->overduePatient();
        $future = $this->futureScheduledAppointment($patient, $dentist);

        $this->actingAs($admin)
            ->post(route('admin.appointments.mark_overdue'));

        $this->assertDatabaseHas('appointments', [
            'id' => $future->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_admin_can_still_manually_override_overdue_appointment_status_to_completed_via_complete_route(): void
    {
        $admin = $this->overdueAdmin();
        $dentist = $this->overdueDentist();
        $patient = $this->overduePatient();
        $appointment = $this->pastScheduledAppointment($patient, $dentist);

        $this->actingAs($admin)
            ->post(route('admin.appointments.complete', $appointment->id));

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);
    }

    public function test_appointment_without_end_time_does_not_crash_admin_appointments_page(): void
    {
        $admin = $this->overdueAdmin();
        $dentist = $this->overdueDentist();
        $patient = $this->overduePatient();

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $dentist->id,
            'appointment_date' => now()->subDay()->toDateString(),
            'appointment_time' => '10:00:00',
            'end_time' => null,
            'service' => 'Whitening',
            'status' => 'scheduled',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.appointments', ['status' => 'all']))
            ->assertOk();
    }

    public function test_patient_cannot_access_mark_overdue_route(): void
    {
        $patient = $this->overduePatient();

        $this->actingAs($patient)
            ->post(route('admin.appointments.mark_overdue'))
            ->assertForbidden();
    }

    private function overdueAdmin(): User
    {
        return User::factory()->create(['role' => Role::Admin]);
    }

    private function overdueDentist(): User
    {
        return User::factory()->create(['role' => Role::Dentist]);
    }

    private function overduePatient(): User
    {
        return User::factory()->create(['role' => Role::Patient]);
    }

    private function pastScheduledAppointment(User $patient, User $dentist, array $overrides = []): Appointment
    {
        return Appointment::create(array_merge([
            'patient_id' => $patient->id,
            'doctor_id' => $dentist->id,
            'appointment_date' => now()->subDay()->toDateString(),
            'appointment_time' => '09:00:00',
            'end_time' => '10:00:00',
            'service' => 'Cleaning',
            'status' => 'scheduled',
        ], $overrides));
    }

    private function futureScheduledAppointment(User $patient, User $dentist, array $overrides = []): Appointment
    {
        return Appointment::create(array_merge([
            'patient_id' => $patient->id,
            'doctor_id' => $dentist->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '09:00:00',
            'end_time' => '10:00:00',
            'service' => 'Checkup',
            'status' => 'scheduled',
        ], $overrides));
    }
}
