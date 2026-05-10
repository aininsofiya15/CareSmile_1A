<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class ScheduleUtilizationService
{
    public function __construct(private ScheduleSlotService $scheduleSlotService) {}

    /**
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    public function calculateSlotRanges(DoctorSchedule $schedule): array
    {
        return $this->scheduleSlotService->calculateSlotRanges($schedule);
    }

    public function calculateTotalSlots(DoctorSchedule $schedule): int
    {
        return count($this->calculateSlotRanges($schedule));
    }

    public function calculateBookedSlots(DoctorSchedule $schedule): int
    {
        $slotRanges = $this->calculateSlotRanges($schedule);

        if ($slotRanges === []) {
            return 0;
        }

        $appointments = $this->scheduleSlotService->blockingAppointmentRanges($schedule);

        return collect($slotRanges)
            ->filter(function (array $slotRange) use ($appointments): bool {
                return $appointments->contains(
                    fn (array $appointmentRange): bool => $this->scheduleSlotService->timeRangesOverlap(
                        $slotRange['start'],
                        $slotRange['end'],
                        $appointmentRange['start'],
                        $appointmentRange['end']
                    )
                );
            })
            ->count();
    }

    public function calculateRemainingSlots(DoctorSchedule $schedule): int
    {
        return max(0, $this->calculateTotalSlots($schedule) - $this->calculateBookedSlots($schedule));
    }

    public function calculateUtilizationPercentage(DoctorSchedule $schedule): int
    {
        $totalSlots = $this->calculateTotalSlots($schedule);

        if ($totalSlots <= 0) {
            return 0;
        }

        return (int) round(($this->calculateBookedSlots($schedule) / $totalSlots) * 100);
    }

    public function getUtilizationLabel(int $percentage): string
    {
        return match (true) {
            $percentage <= 0 => 'No Bookings',
            $percentage < 40 => 'Low Utilization',
            $percentage < 80 => 'Moderate Utilization',
            $percentage < 100 => 'High Utilization',
            default => 'Fully Booked',
        };
    }

    public function getUtilizationClass(int $percentage): string
    {
        return match (true) {
            $percentage <= 0 => 'utilization-none',
            $percentage < 40 => 'utilization-low',
            $percentage < 80 => 'utilization-moderate',
            $percentage < 100 => 'utilization-high',
            default => 'utilization-full',
        };
    }

    /**
     * @return array{total_slots: int, booked_slots: int, remaining_slots: int, utilization_percentage: int, utilization_label: string, utilization_class: string}
     */
    public function getScheduleUtilizationSummary(DoctorSchedule $schedule): array
    {
        $totalSlots = $this->calculateTotalSlots($schedule);
        $bookedSlots = $this->calculateBookedSlots($schedule);
        $remainingSlots = max(0, $totalSlots - $bookedSlots);
        $utilizationPercentage = $totalSlots > 0 ? (int) round(($bookedSlots / $totalSlots) * 100) : 0;

        return [
            'total_slots' => $totalSlots,
            'booked_slots' => $bookedSlots,
            'remaining_slots' => $remainingSlots,
            'utilization_percentage' => $utilizationPercentage,
            'utilization_label' => $this->getUtilizationLabel($utilizationPercentage),
            'utilization_class' => $this->getUtilizationClass($utilizationPercentage),
        ];
    }

    /**
     * @param  iterable<int, DoctorSchedule>  $schedules
     * @return array{total_schedules: int, total_slots: int, booked_slots: int, remaining_slots: int, average_utilization_percentage: int, utilization_label: string, utilization_class: string, fully_booked_schedules: int}
     */
    public function getAggregateSummary(iterable $schedules): array
    {
        $totalSchedules = 0;
        $totalSlots = 0;
        $bookedSlots = 0;
        $fullyBookedSchedules = 0;

        foreach ($schedules as $schedule) {
            $summary = $this->getScheduleUtilizationSummary($schedule);

            $totalSchedules++;
            $totalSlots += $summary['total_slots'];
            $bookedSlots += $summary['booked_slots'];

            if ($summary['total_slots'] > 0 && $summary['remaining_slots'] === 0) {
                $fullyBookedSchedules++;
            }
        }

        $averageUtilization = $totalSlots > 0 ? (int) round(($bookedSlots / $totalSlots) * 100) : 0;

        return [
            'total_schedules' => $totalSchedules,
            'total_slots' => $totalSlots,
            'booked_slots' => $bookedSlots,
            'remaining_slots' => max(0, $totalSlots - $bookedSlots),
            'average_utilization_percentage' => $averageUtilization,
            'utilization_label' => $this->getUtilizationLabel($averageUtilization),
            'utilization_class' => $this->getUtilizationClass($averageUtilization),
            'fully_booked_schedules' => $fullyBookedSchedules,
        ];
    }

    /**
     * @return array{dentist_id: int, dentist_name: string, total_schedules: int, total_appointments: int, total_generated_slots: int, total_booked_slots: int, total_remaining_slots: int, average_utilization_percentage: int, utilization_label: string, utilization_class: string, fully_booked_schedules: int}
     */
    public function getDentistWorkloadSummary(int $dentistId, ?CarbonInterface $startDate = null, ?CarbonInterface $endDate = null): array
    {
        $dentist = User::find($dentistId);
        $schedules = DoctorSchedule::where('doctor_id', $dentistId)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate): void {
                $query->whereBetween('working_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->orderBy('working_date')
            ->get();

        $aggregateSummary = $this->getAggregateSummary($schedules);
        $appointmentCount = Appointment::where('doctor_id', $dentistId)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate): void {
                $query->whereBetween('appointment_date', [$startDate->toDateString(), $endDate->toDateString()]);
            })
            ->count();

        return [
            'dentist_id' => $dentistId,
            'dentist_name' => $dentist?->name ?? 'Dentist',
            'total_schedules' => $aggregateSummary['total_schedules'],
            'total_appointments' => $appointmentCount,
            'total_generated_slots' => $aggregateSummary['total_slots'],
            'total_booked_slots' => $aggregateSummary['booked_slots'],
            'total_remaining_slots' => $aggregateSummary['remaining_slots'],
            'average_utilization_percentage' => $aggregateSummary['average_utilization_percentage'],
            'utilization_label' => $aggregateSummary['utilization_label'],
            'utilization_class' => $aggregateSummary['utilization_class'],
            'fully_booked_schedules' => $aggregateSummary['fully_booked_schedules'],
        ];
    }

    /**
     * @return Collection<int, array{dentist_id: int, dentist_name: string, total_schedules: int, total_appointments: int, total_generated_slots: int, total_booked_slots: int, total_remaining_slots: int, average_utilization_percentage: int, utilization_label: string, utilization_class: string, fully_booked_schedules: int}>
     */
    public function getAllDentistWorkloadSummaries(): Collection
    {
        return User::where('role', Role::Dentist)
            ->orderBy('name')
            ->get()
            ->map(fn (User $dentist): array => $this->getDentistWorkloadSummary($dentist->id));
    }
}
