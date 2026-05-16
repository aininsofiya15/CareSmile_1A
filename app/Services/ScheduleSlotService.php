<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\ScheduleSlot;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScheduleSlotService
{
    /**
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    public function calculateSlotRanges(DoctorSchedule $schedule): array
    {
        return $this->calculateSlotRangesFromValues(
            $this->scheduleDate($schedule),
            $this->timeString($schedule->start_time),
            $this->timeString($schedule->end_time),
            (int) $schedule->slot_duration,
            $schedule->break_start ? $this->timeString($schedule->break_start) : null,
            $schedule->break_end ? $this->timeString($schedule->break_end) : null
        );
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    public function calculateSlotRangesFromValues(
        string $date,
        string $startTime,
        string $endTime,
        int $slotDuration,
        ?string $breakStart = null,
        ?string $breakEnd = null
    ): array {
        if ($slotDuration <= 0) {
            return [];
        }

        $workStart = Carbon::parse($date.' '.$this->timeString($startTime));
        $workEnd = Carbon::parse($date.' '.$this->timeString($endTime));

        if ($workStart->gte($workEnd)) {
            return [];
        }

        $breakStartAt = $breakStart ? Carbon::parse($date.' '.$this->timeString($breakStart)) : null;
        $breakEndAt = $breakEnd ? Carbon::parse($date.' '.$this->timeString($breakEnd)) : null;
        $ranges = [];
        $seen = [];

        for ($slotStart = $workStart->copy(); $slotStart->lt($workEnd); $slotStart->addMinutes($slotDuration)) {
            $slotEnd = $slotStart->copy()->addMinutes($slotDuration);

            if ($slotEnd->gt($workEnd)) {
                break;
            }

            if ($breakStartAt && $breakEndAt && $this->timeRangesOverlap($slotStart, $slotEnd, $breakStartAt, $breakEndAt)) {
                continue;
            }

            $key = $slotStart->format('H:i:s').'|'.$slotEnd->format('H:i:s');

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $ranges[] = [
                'start' => $slotStart->copy(),
                'end' => $slotEnd,
            ];
        }

        return $ranges;
    }

    public function generateSlots(DoctorSchedule $schedule): void
    {
        $ranges = $this->calculateSlotRanges($schedule);

        DB::transaction(function () use ($schedule, $ranges): void {
            ScheduleSlot::where('schedule_id', $schedule->id)->delete();

            foreach ($ranges as $range) {
                ScheduleSlot::create([
                    'schedule_id' => $schedule->id,
                    'start_time' => $range['start']->format('H:i:s'),
                    'end_time' => $range['end']->format('H:i:s'),
                    'is_available' => true,
                ]);
            }
        });

        $schedule->unsetRelation('slots');
    }

    public function isSlotWithinBreak(Carbon $slotStart, Carbon $slotEnd, DoctorSchedule $schedule): bool
    {
        if (!$schedule->break_start || !$schedule->break_end) {
            return false;
        }

        $date = $this->scheduleDate($schedule);
        $breakStart = Carbon::parse($date.' '.$this->timeString($schedule->break_start));
        $breakEnd = Carbon::parse($date.' '.$this->timeString($schedule->break_end));

        return $this->timeRangesOverlap($slotStart, $slotEnd, $breakStart, $breakEnd);
    }

    public function isSlotBooked(
        DoctorSchedule $schedule,
        Carbon $slotStart,
        Carbon $slotEnd,
        ?int $ignoreAppointmentId = null
    ): bool {
        return $this->blockingAppointmentRanges($schedule, $ignoreAppointmentId)
            ->contains(
                fn (array $appointmentRange): bool => $this->timeRangesOverlap(
                    $slotStart,
                    $slotEnd,
                    $appointmentRange['start'],
                    $appointmentRange['end']
                )
            );
    }

    /**
     * @return EloquentCollection<int, ScheduleSlot>
     */
    public function getAvailableSlots(DoctorSchedule $schedule, ?Service $service = null, ?int $ignoreAppointmentId = null): EloquentCollection
    {
        $schedule->loadMissing(['slots' => fn ($query) => $query->orderBy('start_time')]);

        return $schedule->slots
            ->filter(function (ScheduleSlot $slot) use ($schedule, $service, $ignoreAppointmentId): bool {
                $slot->setRelation('schedule', $schedule);

                if ($service) {
                    [$startTime, $endTime] = $this->appointmentRangeForSlot($slot, $service);

                    return $this->validateAppointmentRange($schedule, $startTime, $endTime, $ignoreAppointmentId) === null;
                }

                return (bool) $slot->is_available;
            })
            ->values();
    }

    /**
     * @return EloquentCollection<int, ScheduleSlot>
     */
    public function getBookedSlots(DoctorSchedule $schedule): EloquentCollection
    {
        $schedule->loadMissing(['slots' => fn ($query) => $query->orderBy('start_time')]);

        return $schedule->slots->where('is_available', false)->values();
    }

    /**
     * @return EloquentCollection<int, ScheduleSlot>
     */
    public function getRemainingSlots(DoctorSchedule $schedule): EloquentCollection
    {
        return $this->getAvailableSlots($schedule);
    }

    /**
     * @return array<int, string>
     */
    public function validateSlotGeneration(DoctorSchedule $schedule): array
    {
        $errors = [];
        $date = $this->scheduleDate($schedule);
        $workStart = Carbon::parse($date.' '.$this->timeString($schedule->start_time));
        $workEnd = Carbon::parse($date.' '.$this->timeString($schedule->end_time));
        $slotDuration = (int) $schedule->slot_duration;

        if ($workStart->gte($workEnd)) {
            $errors[] = 'End time must be after start time.';
        }

        if ($slotDuration <= 0) {
            $errors[] = 'Slot duration must be a positive number.';
        }

        if ($schedule->break_start && $schedule->break_end) {
            $breakStart = Carbon::parse($date.' '.$this->timeString($schedule->break_start));
            $breakEnd = Carbon::parse($date.' '.$this->timeString($schedule->break_end));

            if ($breakStart->gte($breakEnd) || $breakStart->lt($workStart) || $breakEnd->gt($workEnd)) {
                $errors[] = 'Break time must remain within working hours.';
            }
        }

        if ($errors === [] && $this->calculateSlotRanges($schedule) === []) {
            $errors[] = 'Slot duration is too large for the selected schedule.';
        }

        return $errors;
    }

    public function syncSlotAvailability(DoctorSchedule $schedule): void
    {
        $schedule->loadMissing(['slots' => fn ($query) => $query->orderBy('start_time')]);

        foreach ($schedule->slots as $slot) {
            $slotStart = Carbon::parse($this->scheduleDate($schedule).' '.$this->timeString($slot->start_time));
            $slotEnd = Carbon::parse($this->scheduleDate($schedule).' '.$this->timeString($slot->end_time));

            $slot->update([
                'is_available' => !$this->isSlotBooked($schedule, $slotStart, $slotEnd),
            ]);
        }

        $schedule->unsetRelation('slots');
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public function appointmentRangeForSlot(ScheduleSlot $slot, Service $service): array
    {
        $date = $this->scheduleDate($slot->schedule);
        $startTime = Carbon::parse($date.' '.$this->timeString($slot->start_time));
        $endTime = $startTime->copy()->addMinutes((int) $service->duration_minutes);

        return [$startTime, $endTime];
    }

    /**
     * @return array<string, string>|null
     */
    public function validateAppointmentRange(
        DoctorSchedule $schedule,
        Carbon $startTime,
        Carbon $endTime,
        ?int $ignoreAppointmentId = null
    ): ?array {
        if (!$schedule->isBookable()) {
            return ['time_slot' => 'This schedule is currently unavailable for booking. Please select another available slot.'];
        }

        $date = $this->scheduleDate($schedule);
        $scheduleStart = Carbon::parse($date.' '.$this->timeString($schedule->start_time));
        $scheduleEnd = Carbon::parse($date.' '.$this->timeString($schedule->end_time));

        if ($startTime->lt($scheduleStart) || $endTime->gt($scheduleEnd)) {
            return ['time_slot' => 'Selected service does not fit within the doctor schedule.'];
        }

        if ($this->isSlotWithinBreak($startTime, $endTime, $schedule)) {
            return ['time_slot' => 'Selected service overlaps with doctor break time.'];
        }

        if ($this->isSlotBooked($schedule, $startTime, $endTime, $ignoreAppointmentId)) {
            return ['time_slot' => 'This time slot conflicts with another appointment.'];
        }

        return null;
    }

    /**
     * @return Collection<int, array{start: Carbon, end: Carbon}>
     */
    public function blockingAppointmentRanges(DoctorSchedule $schedule, ?int $ignoreAppointmentId = null): Collection
    {
        $appointments = Appointment::where('doctor_id', $schedule->doctor_id)
            ->whereDate('appointment_date', $this->scheduleDate($schedule))
            // ->where('status', 'scheduled')
            ->whereIn('status', ['scheduled', 'completed', 'no_show'])
            ->when($ignoreAppointmentId, fn ($query) => $query->where('id', '!=', $ignoreAppointmentId))
            ->get();

        $serviceDurations = $this->serviceDurationsFor($appointments->pluck('service')->filter()->unique()->all());

        return $appointments->map(function (Appointment $appointment) use ($serviceDurations): array {
            $appointmentStart = Carbon::parse($this->appointmentDate($appointment).' '.$this->timeString($appointment->appointment_time));

            return [
                'start' => $appointmentStart,
                'end' => $this->appointmentEndTime($appointment, $appointmentStart, $serviceDurations),
            ];
        });
    }

    /**
     * @param array<int, string> $serviceNames
     *
     * @return array<string, int>
     */
    private function serviceDurationsFor(array $serviceNames): array
    {
        return Service::whereIn('name', $serviceNames)
            ->pluck('duration_minutes', 'name')
            ->map(fn ($duration): int => (int) $duration)
            ->all();
    }

    /**
     * @param array<string, int> $serviceDurations
     */
    private function appointmentEndTime(Appointment $appointment, Carbon $appointmentStart, array $serviceDurations): Carbon
    {
        if ($appointment->end_time) {
            return Carbon::parse($this->appointmentDate($appointment).' '.$this->timeString($appointment->end_time));
        }

        $duration = $serviceDurations[$appointment->service] ?? null;

        if ($duration) {
            return $appointmentStart->copy()->addMinutes($duration);
        }

        return $appointmentStart->copy()->addMinute();
    }

    public function timeRangesOverlap(Carbon $firstStart, Carbon $firstEnd, Carbon $secondStart, Carbon $secondEnd): bool
    {
        return $firstStart->lt($secondEnd) && $firstEnd->gt($secondStart);
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

    public function timeString(mixed $time): string
    {
        if ($time instanceof CarbonInterface) {
            return $time->format('H:i:s');
        }

        return Carbon::parse((string) $time)->format('H:i:s');
    }
}
