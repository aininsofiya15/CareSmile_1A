<?php

namespace App\Services;

use App\Models\DoctorSchedule;

class ScheduleStatusService
{
    public function __construct(private ScheduleSlotService $scheduleSlotService) {}

    public function isScheduleBookable(DoctorSchedule $schedule): bool
    {
        return $schedule->isBookable();
    }

    public function updateScheduleStatusAfterBooking(DoctorSchedule $schedule): void
    {
        $this->syncSlotAvailability($schedule);
        $this->markFullyBookedIfNeeded($schedule->refresh());
    }

    public function refreshScheduleAvailability(DoctorSchedule $schedule): void
    {
        $this->syncSlotAvailability($schedule);

        $schedule->refresh();

        if ($schedule->isFullyBooked()) {
            $this->restoreActiveIfSlotAvailable($schedule);

            return;
        }

        $this->markFullyBookedIfNeeded($schedule);
    }

    public function markFullyBookedIfNeeded(DoctorSchedule $schedule): void
    {
        if (! $schedule->isBookable()) {
            return;
        }

        if ($this->calculateGeneratedSlotCount($schedule) === 0) {
            return;
        }

        if ($this->calculateAvailableSlotCount($schedule) > 0) {
            return;
        }

        $schedule->update([
            'status' => DoctorSchedule::STATUS_FULLY_BOOKED,
            'is_active' => false,
            'status_updated_automatically' => true,
        ]);
    }

    public function restoreActiveIfSlotAvailable(DoctorSchedule $schedule): void
    {
        if (! $schedule->isFullyBooked() || ! $schedule->status_updated_automatically) {
            return;
        }

        if ($this->calculateAvailableSlotCount($schedule) === 0) {
            return;
        }

        $schedule->update([
            'status' => DoctorSchedule::STATUS_ACTIVE,
            'is_active' => true,
            'status_updated_automatically' => false,
        ]);
    }

    public function syncSlotAvailability(DoctorSchedule $schedule): void
    {
        $this->scheduleSlotService->syncSlotAvailability($schedule);
    }

    public function calculateGeneratedSlotCount(DoctorSchedule $schedule): int
    {
        if ($schedule->relationLoaded('slots')) {
            return $schedule->slots->count();
        }

        return $schedule->slots()->count();
    }

    public function calculateBookedSlotCount(DoctorSchedule $schedule): int
    {
        if ($schedule->relationLoaded('slots')) {
            return $schedule->slots->where('is_available', false)->count();
        }

        return $schedule->slots()->where('is_available', false)->count();
    }

    public function calculateAvailableSlotCount(DoctorSchedule $schedule): int
    {
        if ($schedule->relationLoaded('slots')) {
            return $schedule->slots->where('is_available', true)->count();
        }

        return $schedule->slots()->where('is_available', true)->count();
    }
}
