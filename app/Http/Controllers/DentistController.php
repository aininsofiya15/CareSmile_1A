<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Services\ScheduleUtilizationService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // Added back for updates
use Illuminate\View\View;
use App\Models\User;

class DentistController extends Controller
{
    public function __construct(private ScheduleUtilizationService $scheduleUtilizationService) {}

    public function dashboard(): View
    {
        /** @var \App\Models\User $dentist */
        $dentist = Auth::user();

        abort_unless($dentist?->isDentist(), 403, 'Unauthorized access.');

        $today = Carbon::today();
        $now = Carbon::now();
        $weekStart = $today->copy()->startOfWeek(CarbonInterface::MONDAY);
        $weekEnd = $today->copy()->endOfWeek(CarbonInterface::SUNDAY);

        // Get Weekly Schedules
        $weeklySchedules = DoctorSchedule::with([
            'slots' => fn ($query) => $query->orderBy('start_time'),
        ])
            ->where('doctor_id', $dentist->id)
            ->whereBetween('working_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('working_date')
            ->orderBy('start_time')
            ->get();

        // Get Weekly Appointments
        $weeklyAppointments = Appointment::with('patient')
            ->where('doctor_id', $dentist->id)
            ->where('status', 'scheduled')
            ->whereBetween('appointment_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        // Get Upcoming Appointments
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
            $weeklyAppointments->merge($upcomingAppointments)->pluck('service')->filter()->unique()->values()->all()
        );

        $schedulesByDate = $weeklySchedules->groupBy(fn ($s) => $this->scheduleDate($s));
        $appointmentsByDate = $weeklyAppointments->groupBy(fn ($a) => $this->appointmentDate($a));

        $weekDays = collect();
        for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
            $date = $weekStart->copy()->addDays($dayOffset);
            $dateString = $date->toDateString();
            $daySchedules = $schedulesByDate->get($dateString, collect())->values();
            $dayAppointments = $appointmentsByDate->get($dateString, collect())->values();
            
            $scheduleCards = $daySchedules
                ->map(fn ($s) => $this->formatScheduleCard($s, $dayAppointments, $serviceDurations))
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
            'today_working_hours' => $todaySchedule && $todaySchedule['has_schedule'] ? collect($todaySchedule['schedules'])->pluck('working_hours')->implode(' / ') : 'No schedule',
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

        $upcomingAppointmentCards = $upcomingAppointments->map(fn ($a) => $this->formatAppointmentCard($a, $serviceDurations))->values();

        return view('dentist.dashboard', [
            'dashboardStats' => $dashboardStats,
            'todaySchedule' => $todaySchedule,
            'weekDays' => $weekDays,
            'upcomingAppointments' => $upcomingAppointmentCards,
            'totalPatients' => Appointment::where('doctor_id', $dentist->id)->distinct('patient_id')->count(),
            'todayCount' => $todaySchedule['appointments_count'] ?? 0,
            'weekCount' => $weeklyAppointments->count(),
        ]);
    }

    public function profile(): View
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
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);
        return back()->with('success', 'Security credentials updated successfully!');
    }

    public function appointments(): View
    {
        /** @var User $dentist */
        $dentist = Auth::user();
        abort_unless($dentist?->isDentist(), 403, 'Unauthorized access.');

        $appointments = Appointment::with('patient')
            ->where('doctor_id', $dentist->id)
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return view('dentist.appointments.index', compact('appointments'));
    }

    private function formatScheduleCard(DoctorSchedule $schedule, Collection $appointmentsForDate, array $serviceDurations): array
    {
        $date = $this->scheduleDate($schedule);
        $scheduleStart = Carbon::parse($date.' '.$this->timeString($schedule->start_time));
        $scheduleEnd = Carbon::parse($date.' '.$this->timeString($schedule->end_time));
        $utilizationSummary = $this->scheduleUtilizationService->getScheduleUtilizationSummary($schedule);

        $scheduleAppointments = $appointmentsForDate
            ->filter(function ($a) use ($scheduleStart, $scheduleEnd, $serviceDurations) {
                [$start, $end] = $this->appointmentRange($a, $serviceDurations);
                return $this->timeRangesOverlap($start, $end, $scheduleStart, $scheduleEnd);
            })->values();

        return [
            'id' => $schedule->id,
            'working_date' => $date,
            'working_hours' => $scheduleStart->format('h:i A').' - '.$scheduleEnd->format('h:i A'),
            'total_slots' => $utilizationSummary['total_slots'],
            'booked_slots' => $utilizationSummary['booked_slots'],
            'available_slots' => $schedule->isBookable() ? $utilizationSummary['remaining_slots'] : 0,
            'utilization_percentage' => $utilizationSummary['utilization_percentage'],
            'utilization_label' => $utilizationSummary['utilization_label'],
            'utilization_class' => $utilizationSummary['utilization_class'],
        ];
    }

    private function formatAppointmentCard(Appointment $appointment, array $serviceDurations): array
    {
        [$start, $end] = $this->appointmentRange($appointment, $serviceDurations);
        return [
            'patient_name' => $appointment->patient?->name ?? 'N/A',
            'appointment_date' => $start->format('M d, Y'),
            'appointment_start_time' => $start->format('h:i A'),
            'service' => $appointment->service ?: 'N/A',
            'status' => ucfirst(str_replace('_', ' ', $appointment->status ?: 'scheduled')),
        ];
    }

    private function appointmentRange(Appointment $appointment, array $serviceDurations): array
    {
        $start = Carbon::parse($this->appointmentDate($appointment).' '.$this->timeString($appointment->appointment_time));
        $end = $appointment->end_time
            ? Carbon::parse($this->appointmentDate($appointment).' '.$this->timeString($appointment->end_time))
            : $start->copy()->addMinutes($serviceDurations[$appointment->service] ?? 1);
        return [$start, $end];
    }

    private function timeRangesOverlap(Carbon $firstStart, Carbon $firstEnd, Carbon $secondStart, Carbon $secondEnd): bool
    {
        return $firstStart->lt($secondEnd) && $firstEnd->gt($secondStart);
    }

    private function serviceDurationsFor(array $serviceNames): array
    {
        return Service::whereIn('name', $serviceNames)->pluck('duration_minutes', 'name')->map(fn ($d) => (int) $d)->all();
    }

    private function scheduleDate(DoctorSchedule $schedule): string
    {
        return $schedule->working_date instanceof CarbonInterface ? $schedule->working_date->toDateString() : Carbon::parse($schedule->working_date)->toDateString();
    }

    private function appointmentDate(Appointment $appointment): string
    {
        return $appointment->appointment_date instanceof CarbonInterface ? $appointment->appointment_date->toDateString() : Carbon::parse($appointment->appointment_date)->toDateString();
    }

    private function timeString(mixed $time): string
    {
        return $time instanceof CarbonInterface ? $time->format('H:i:s') : Carbon::parse((string) $time)->format('H:i:s');
    }
}