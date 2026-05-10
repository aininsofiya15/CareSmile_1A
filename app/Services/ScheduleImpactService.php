<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class ScheduleImpactService
{
    /**
     * @return Collection<int, Appointment>
     */
    public function getAffectedAppointments(DoctorSchedule $schedule): Collection
    {
        return Appointment::with('patient')
            ->where('doctor_id', $schedule->doctor_id)
            ->whereDate('appointment_date', $this->scheduleDate($schedule))
            ->whereTime('appointment_time', '>=', $this->timeString($schedule->start_time))
            ->whereTime('appointment_time', '<', $this->timeString($schedule->end_time))
            ->where('status', '!=', 'cancelled')
            ->orderBy('appointment_time')
            ->get();
    }

    public function countAffectedAppointments(DoctorSchedule $schedule): int
    {
        return $this->getAffectedAppointments($schedule)->count();
    }

    public function hasAffectedAppointments(DoctorSchedule $schedule): bool
    {
        return $this->countAffectedAppointments($schedule) > 0;
    }

    /**
     * @param  array<string, mixed>  $newData
     */
    public function isMajorScheduleChange(DoctorSchedule $schedule, array $newData): bool
    {
        $comparisons = [
            'doctor_id' => [(string) $schedule->doctor_id, (string) ($newData['doctor_id'] ?? $schedule->doctor_id)],
            'working_date' => [$this->scheduleDate($schedule), Carbon::parse((string) ($newData['working_date'] ?? $this->scheduleDate($schedule)))->toDateString()],
            'start_time' => [$this->minuteTimeString($schedule->start_time), $this->minuteTimeString($newData['start_time'] ?? $schedule->start_time)],
            'end_time' => [$this->minuteTimeString($schedule->end_time), $this->minuteTimeString($newData['end_time'] ?? $schedule->end_time)],
            'break_start' => [$this->nullableMinuteTimeString($schedule->break_start), $this->nullableMinuteTimeString($newData['break_start'] ?? $schedule->break_start)],
            'break_end' => [$this->nullableMinuteTimeString($schedule->break_end), $this->nullableMinuteTimeString($newData['break_end'] ?? $schedule->break_end)],
            'slot_duration' => [(string) $schedule->slot_duration, (string) ($newData['slot_duration'] ?? $schedule->slot_duration)],
            'status' => [(string) $schedule->status, (string) ($newData['status'] ?? $schedule->status)],
        ];

        foreach ($comparisons as [$currentValue, $newValue]) {
            if ($currentValue !== $newValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{count: int, appointments: array<int, array{patient_name: string, appointment_date: string, appointment_time: string, status: string}>}
     */
    public function getImpactSummary(DoctorSchedule $schedule): array
    {
        $appointments = $this->getAffectedAppointments($schedule);

        return [
            'count' => $appointments->count(),
            'appointments' => $appointments
                ->map(fn (Appointment $appointment): array => [
                    'patient_name' => $appointment->patient?->name ?? 'Patient',
                    'appointment_date' => $this->appointmentDate($appointment),
                    'appointment_time' => Carbon::parse($appointment->appointment_time)->format('h:i A'),
                    'status' => ucfirst(str_replace('_', ' ', (string) $appointment->status)),
                ])
                ->values()
                ->all(),
        ];
    }

    private function scheduleDate(DoctorSchedule $schedule): string
    {
        if ($schedule->working_date instanceof CarbonInterface) {
            return $schedule->working_date->toDateString();
        }

        return Carbon::parse($schedule->working_date)->toDateString();
    }

    private function appointmentDate(Appointment $appointment): string
    {
        if ($appointment->appointment_date instanceof CarbonInterface) {
            return $appointment->appointment_date->toDateString();
        }

        return Carbon::parse($appointment->appointment_date)->toDateString();
    }

    private function nullableMinuteTimeString(mixed $time): string
    {
        if ($time === null || $time === '') {
            return '';
        }

        return $this->minuteTimeString($time);
    }

    private function minuteTimeString(mixed $time): string
    {
        if ($time instanceof CarbonInterface) {
            return $time->format('H:i');
        }

        return Carbon::parse((string) $time)->format('H:i');
    }

    private function timeString(mixed $time): string
    {
        if ($time instanceof CarbonInterface) {
            return $time->format('H:i:s');
        }

        return Carbon::parse((string) $time)->format('H:i:s');
    }
}
