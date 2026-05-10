<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Services\ScheduleUtilizationService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DentistController extends Controller
{
    public function __construct(private ScheduleUtilizationService $scheduleUtilizationService) {}

    public function dashboard(): View
    {
        $dentistId = \Illuminate\Support\Facades\Auth::id();

        // Set to 0 for now so the dashboard doesn't crash
        $todayCount = 0;
        $totalPatients = 0;
        $weekCount = 0;

        return view('dentist.dashboard', compact('todayCount', 'totalPatients', 'weekCount'));
    }
    public function profile()
    {
        $user = Auth::user();
        return view('dentist.dentist_profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string'],
            'specialization' => ['required', 'string', 'in:General Dentistry,Orthodontics,Periodontics,Pediatric Dentistry,Oral Surgery'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Professional profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        // 1. Validate the input
        $request->validate([
            'current_password' => ['required', 'current_password'], // Built-in Laravel check
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' looks for password_confirmation
        ]);

        // 2. Update the password in the database
        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        // 3. Return with success message
        return back()->with('success', 'Security credentials updated successfully!');
    }
}
        $dentist = Auth::user();

        abort_unless($dentist?->isDentist(), 403, 'Unauthorized access.');

        $today = Carbon::today();
        $now = Carbon::now();
        $weekStart = $today->copy()->startOfWeek(CarbonInterface::MONDAY);
        $weekEnd = $today->copy()->endOfWeek(CarbonInterface::SUNDAY);

        $weeklySchedules = DoctorSchedule::with([
            'slots' => fn ($query) => $query->orderBy('start_time'),
        ])
            ->where('doctor_id', $dentist->id)
            ->whereBetween('working_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('working_date')
            ->orderBy('start_time')
            ->get();

        $weeklyAppointments = Appointment::with('patient')
            ->where('doctor_id', $dentist->id)
            ->where('status', 'scheduled')
            ->whereBetween('appointment_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $upcomingAppointments = Appointment::with('patient')
            ->where('doctor_id', $dentist->id)
            ->where('status', 'scheduled')
            ->where(function ($query) use ($today, $now): void {
                $query->whereDate('appointment_date', '>', $today->toDateString())
                    ->orWhere(function ($query) use ($today, $now): void {
                        $query->whereDate('appointment_date', $today->toDateString())
                            ->whereTime('appointment_time', '>=', $now->format('H:i:s'));
                    });
            })
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(10)
            ->get();

        $serviceDurations = $this->serviceDurationsFor(
            $weeklyAppointments
                ->merge($upcomingAppointments)
                ->pluck('service')
                ->filter()
                ->unique()
                ->values()
                ->all()
        );

        $schedulesByDate = $weeklySchedules->groupBy(
            fn (DoctorSchedule $schedule): string => $this->scheduleDate($schedule)
        );

        $appointmentsByDate = $weeklyAppointments->groupBy(
            fn (Appointment $appointment): string => $this->appointmentDate($appointment)
        );

        $weekDays = collect();

        for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
            $date = $weekStart->copy()->addDays($dayOffset);
            $dateString = $date->toDateString();
            $daySchedules = $schedulesByDate->get($dateString, collect())->values();
            $dayAppointments = $appointmentsByDate->get($dateString, collect())->values();
            $scheduleCards = $daySchedules
                ->map(fn (DoctorSchedule $schedule): array => $this->formatScheduleCard($schedule, $dayAppointments, $serviceDurations))
                ->values();
            $dayTotalSlots = $scheduleCards->sum('total_slots');
            $dayBookedSlots = $scheduleCards->sum('booked_slots');
            $dayUtilizationPercentage = $dayTotalSlots > 0 ? (int) round(($dayBookedSlots / $dayTotalSlots) * 100) : 0;

            $weekDays->push([
                'date' => $dateString,
                'date_label' => $date->format('M d'),
                'full_date_label' => $date->format('l, M d, Y'),
                'day_name' => $date->format('l'),
                'is_today' => $date->isSameDay($today),
                'has_schedule' => $scheduleCards->isNotEmpty(),
                'schedules' => $scheduleCards,
                'appointments_count' => $dayAppointments->count(),
                'total_slots' => $dayTotalSlots,
                'booked_slots' => $dayBookedSlots,
                'available_slots' => $scheduleCards->sum('available_slots'),
                'utilization_percentage' => $dayUtilizationPercentage,
                'utilization_label' => $this->scheduleUtilizationService->getUtilizationLabel($dayUtilizationPercentage),
                'utilization_class' => $this->scheduleUtilizationService->getUtilizationClass($dayUtilizationPercentage),
            ]);
        }

        $todaySchedule = $weekDays->firstWhere('date', $today->toDateString());
        $todayWorkloadSummary = $this->scheduleUtilizationService->getDentistWorkloadSummary($dentist->id, $today, $today);
        $weeklyWorkloadSummary = $this->scheduleUtilizationService->getDentistWorkloadSummary($dentist->id, $weekStart, $weekEnd);
        $overallWorkloadSummary = $this->scheduleUtilizationService->getDentistWorkloadSummary($dentist->id);

        $dashboardStats = [
            'today_working_hours' => $todaySchedule && $todaySchedule['has_schedule']
                ? collect($todaySchedule['schedules'])->pluck('working_hours')->implode(' / ')
                : 'No schedule',
            'today_appointments' => $todaySchedule['appointments_count'] ?? 0,
            'today_available_slots' => $todaySchedule['available_slots'] ?? 0,
            'weekly_schedules' => $weeklySchedules->count(),
            'total_booked_appointments' => $weeklyAppointments->count(),
            'weekly_available_slots' => $weekDays->sum('available_slots'),
            'today_total_slots' => $todayWorkloadSummary['total_generated_slots'],
            'today_booked_slots' => $todayWorkloadSummary['total_booked_slots'],
            'today_remaining_slots' => $todayWorkloadSummary['total_remaining_slots'],
            'today_utilization_percentage' => $todayWorkloadSummary['average_utilization_percentage'],
            'today_utilization_label' => $todayWorkloadSummary['utilization_label'],
            'today_utilization_class' => $todayWorkloadSummary['utilization_class'],
            'weekly_total_slots' => $weeklyWorkloadSummary['total_generated_slots'],
            'weekly_booked_slots' => $weeklyWorkloadSummary['total_booked_slots'],
            'weekly_remaining_slots' => $weeklyWorkloadSummary['total_remaining_slots'],
            'weekly_utilization_percentage' => $weeklyWorkloadSummary['average_utilization_percentage'],
            'weekly_utilization_label' => $weeklyWorkloadSummary['utilization_label'],
            'weekly_utilization_class' => $weeklyWorkloadSummary['utilization_class'],
            'workload_total_schedules' => $overallWorkloadSummary['total_schedules'],
            'workload_total_appointments' => $overallWorkloadSummary['total_appointments'],
            'workload_total_slots' => $overallWorkloadSummary['total_generated_slots'],
            'workload_average_utilization' => $overallWorkloadSummary['average_utilization_percentage'],
            'workload_utilization_label' => $overallWorkloadSummary['utilization_label'],
            'workload_utilization_class' => $overallWorkloadSummary['utilization_class'],
            'workload_fully_booked_schedules' => $overallWorkloadSummary['fully_booked_schedules'],
        ];

        $upcomingAppointmentCards = $upcomingAppointments
            ->map(fn (Appointment $appointment): array => $this->formatAppointmentCard($appointment, $serviceDurations))
            ->values();

        return view('dentist.dashboard', [
            'dashboardStats' => $dashboardStats,
            'todaySchedule' => $todaySchedule,
            'weekDays' => $weekDays,
            'upcomingAppointments' => $upcomingAppointmentCards,
        ]);
    }

    public function profile(): View
    {
        $user = Auth::user();

        return view('dentist.profile', compact('user'));
    }

    public function appointments(): View
    {
        $dentist = Auth::user();

        abort_unless($dentist?->isDentist(), 403, 'Unauthorized access.');

        $appointments = Appointment::with('patient')
            ->where('doctor_id', $dentist->id)
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return view('dentist.appointments.index', compact('appointments'));
    }

    /**
     * @param  Collection<int, Appointment>  $appointmentsForDate
     * @param  array<string, int>  $serviceDurations
     * @return array<string, mixed>
     */
    private function formatScheduleCard(DoctorSchedule $schedule, Collection $appointmentsForDate, array $serviceDurations): array
    {
        $date = $this->scheduleDate($schedule);
        $scheduleStart = Carbon::parse($date.' '.$this->timeString($schedule->start_time));
        $scheduleEnd = Carbon::parse($date.' '.$this->timeString($schedule->end_time));
        $utilizationSummary = $this->scheduleUtilizationService->getScheduleUtilizationSummary($schedule);

        $scheduleAppointments = $appointmentsForDate
            ->filter(function (Appointment $appointment) use ($scheduleStart, $scheduleEnd, $serviceDurations): bool {
                [$appointmentStart, $appointmentEnd] = $this->appointmentRange($appointment, $serviceDurations);

                return $this->timeRangesOverlap($appointmentStart, $appointmentEnd, $scheduleStart, $scheduleEnd);
            })
            ->values();

        $isBookable = $schedule->isBookable();

        return [
            'id' => $schedule->id,
            'working_date' => $date,
            'working_hours' => $scheduleStart->format('h:i A').' - '.$scheduleEnd->format('h:i A'),
            'start_time' => $scheduleStart->format('h:i A'),
            'end_time' => $scheduleEnd->format('h:i A'),
            'break_time' => $schedule->break_start && $schedule->break_end
                ? Carbon::parse($date.' '.$this->timeString($schedule->break_start))->format('h:i A').' - '.Carbon::parse($date.' '.$this->timeString($schedule->break_end))->format('h:i A')
                : 'No break',
            'slot_duration' => (int) $schedule->slot_duration,
            'status' => $schedule->statusLabel(),
            'status_class' => $schedule->statusBadgeClass(),
            'is_bookable' => $isBookable,
            'total_slots' => $utilizationSummary['total_slots'],
            'booked_slots' => $utilizationSummary['booked_slots'],
            'available_slots' => $isBookable ? $utilizationSummary['remaining_slots'] : 0,
            'booked_appointments' => $scheduleAppointments->count(),
            'utilization_percentage' => $utilizationSummary['utilization_percentage'],
            'utilization_label' => $utilizationSummary['utilization_label'],
            'utilization_class' => $utilizationSummary['utilization_class'],
        ];
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    private function slotRangesForSchedule(DoctorSchedule $schedule): array
    {
        $date = $this->scheduleDate($schedule);

        if ($schedule->relationLoaded('slots') && $schedule->slots->isNotEmpty()) {
            return $schedule->slots
                ->map(fn ($slot): array => [
                    'start' => Carbon::parse($date.' '.$this->timeString($slot->start_time)),
                    'end' => Carbon::parse($date.' '.$this->timeString($slot->end_time)),
                ])
                ->all();
        }

        $slotRanges = [];
        $slotDuration = (int) $schedule->slot_duration;
        $scheduleEnd = Carbon::parse($date.' '.$this->timeString($schedule->end_time));
        $breakStart = $schedule->break_start ? Carbon::parse($date.' '.$this->timeString($schedule->break_start)) : null;
        $breakEnd = $schedule->break_end ? Carbon::parse($date.' '.$this->timeString($schedule->break_end)) : null;

        for ($slotStart = Carbon::parse($date.' '.$this->timeString($schedule->start_time)); $slotStart->lt($scheduleEnd); $slotStart->addMinutes($slotDuration)) {
            $slotEnd = $slotStart->copy()->addMinutes($slotDuration);

            if ($slotEnd->gt($scheduleEnd)) {
                break;
            }

            if ($breakStart && $breakEnd && $this->timeRangesOverlap($slotStart, $slotEnd, $breakStart, $breakEnd)) {
                continue;
            }

            $slotRanges[] = [
                'start' => $slotStart->copy(),
                'end' => $slotEnd,
            ];
        }

        return $slotRanges;
    }

    /**
     * @param  array<string, int>  $serviceDurations
     * @return array<string, string>
     */
    private function formatAppointmentCard(Appointment $appointment, array $serviceDurations): array
    {
        [$appointmentStart, $appointmentEnd] = $this->appointmentRange($appointment, $serviceDurations);

        return [
            'patient_name' => $appointment->patient?->name ?? 'N/A',
            'appointment_date' => $appointmentStart->format('M d, Y'),
            'appointment_day' => $appointmentStart->format('l'),
            'appointment_start_time' => $appointmentStart->format('h:i A'),
            'appointment_end_time' => $appointmentEnd->format('h:i A'),
            'service' => $appointment->service ?: 'N/A',
            'status' => ucfirst(str_replace('_', ' ', $appointment->status ?: 'scheduled')),
        ];
    }

    /**
     * @param  array<string, int>  $serviceDurations
     * @return array{0: Carbon, 1: Carbon}
     */
    private function appointmentRange(Appointment $appointment, array $serviceDurations): array
    {
        $appointmentStart = Carbon::parse($this->appointmentDate($appointment).' '.$this->timeString($appointment->appointment_time));
        $appointmentEnd = $appointment->end_time
            ? Carbon::parse($this->appointmentDate($appointment).' '.$this->timeString($appointment->end_time))
            : $appointmentStart->copy()->addMinutes($serviceDurations[$appointment->service] ?? 1);

        return [$appointmentStart, $appointmentEnd];
    }

    private function timeRangesOverlap(Carbon $firstStart, Carbon $firstEnd, Carbon $secondStart, Carbon $secondEnd): bool
    {
        return $firstStart->lt($secondEnd) && $firstEnd->gt($secondStart);
    }

    /**
     * @param  array<int, string>  $serviceNames
     * @return array<string, int>
     */
    private function serviceDurationsFor(array $serviceNames): array
    {
        return Service::whereIn('name', $serviceNames)
            ->pluck('duration_minutes', 'name')
            ->map(fn ($duration): int => (int) $duration)
            ->all();
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

    private function timeString(mixed $time): string
    {
        if ($time instanceof CarbonInterface) {
            return $time->format('H:i:s');
        }

        return Carbon::parse((string) $time)->format('H:i:s');
    }
}
