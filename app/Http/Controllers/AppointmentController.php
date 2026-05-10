<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\ScheduleSlot;
use App\Models\Service;
use App\Services\ScheduleSlotService;
use App\Services\ScheduleStatusService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        private ScheduleStatusService $scheduleStatusService,
        private ScheduleSlotService $scheduleSlotService
    ) {}

    public function index(): View
    {
        $appointments = Appointment::where('patient_id', Auth::id())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    public function create(): View
    {
        $services = Service::active()
            ->orderBy('name')
            ->get();

        $availableSchedules = DoctorSchedule::with('doctor')
            ->whereDate('working_date', '>=', now()->toDateString())
            ->where('status', DoctorSchedule::STATUS_ACTIVE)
            ->where('is_active', true)
            ->orderBy('working_date')
            ->orderBy('start_time')
            ->get();

        return view('appointments.create', compact('services', 'availableSchedules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_id' => [
                'required',
                Rule::exists('services', 'id')->where('is_active', true),
            ],
            'slot_id' => 'required|exists:schedule_slots,id',
        ], $this->bookingValidationMessages());

        $service = Service::active()->find($validated['service_id']);
        $slot = ScheduleSlot::with('schedule')->findOrFail($validated['slot_id']);

        if (! $service || ! $slot->schedule) {
            return back()->withErrors([
                'time_slot' => 'Please select a valid service, date, and time slot.',
            ])->withInput();
        }

        [$startTime, $endTime] = $this->scheduleSlotService->appointmentRangeForSlot($slot, $service);
        $rangeError = $this->scheduleSlotService->validateAppointmentRange($slot->schedule, $startTime, $endTime);

        if ($rangeError) {
            return back()->withErrors($rangeError)->withInput();
        }

        Appointment::create([
            'patient_id' => Auth::id(),
            'doctor_id' => $slot->schedule->doctor_id,
            'appointment_date' => $this->scheduleDate($slot->schedule),
            'appointment_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
            'service' => $service->name,
            'status' => 'scheduled',
        ]);

        $this->scheduleStatusService->updateScheduleStatusAfterBooking($slot->schedule);

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment booked!');
    }

    public function showReschedule(int $id): View
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        $service = Service::where('name', $appointment->service)->first();

        $availableSchedules = DoctorSchedule::with('doctor')
            ->whereDate('working_date', '>=', now()->toDateString())
            ->where('status', DoctorSchedule::STATUS_ACTIVE)
            ->where('is_active', true)
            ->orderBy('working_date')
            ->orderBy('start_time')
            ->get();

        return view('appointments.reschedule', compact('appointment', 'availableSchedules', 'service'));
    }

    public function submitReschedule(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'slot_id' => 'required|exists:schedule_slots,id',
        ], $this->bookingValidationMessages());

        $appointment = Appointment::where('id', $id)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        $service = Service::where('name', $appointment->service)->first();
        $newSlot = ScheduleSlot::with('schedule')->findOrFail($validated['slot_id']);

        if (! $service || ! $newSlot->schedule) {
            return back()->withErrors([
                'time_slot' => 'Please select a valid service, date, and time slot.',
            ])->withInput();
        }

        [$startTime, $endTime] = $this->scheduleSlotService->appointmentRangeForSlot($newSlot, $service);
        $rangeError = $this->scheduleSlotService->validateAppointmentRange($newSlot->schedule, $startTime, $endTime, $appointment->id);

        if ($rangeError) {
            return back()->withErrors($rangeError)->withInput();
        }

        $oldSchedule = DoctorSchedule::where('doctor_id', $appointment->doctor_id)
            ->whereDate('working_date', $this->appointmentDate($appointment))
            ->first();

        $appointment->update([
            'appointment_date' => $this->scheduleDate($newSlot->schedule),
            'appointment_time' => $startTime->format('H:i:s'),
            'end_time' => $endTime->format('H:i:s'),
            'doctor_id' => $newSlot->schedule->doctor_id,
        ]);

        if ($oldSchedule) {
            $this->scheduleStatusService->refreshScheduleAvailability($oldSchedule);
        }

        if (! $oldSchedule || $oldSchedule->id !== $newSlot->schedule->id) {
            $this->scheduleStatusService->updateScheduleStatusAfterBooking($newSlot->schedule);
        }

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment rescheduled successfully!');
    }

    public function cancel(int $id): RedirectResponse
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        $appointment->update([
            'status' => 'cancelled',
        ]);

        $schedule = DoctorSchedule::where('doctor_id', $appointment->doctor_id)
            ->whereDate('working_date', $this->appointmentDate($appointment))
            ->first();

        if ($schedule) {
            $this->scheduleStatusService->refreshScheduleAvailability($schedule);
        }

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment cancelled!');
    }

    public function adminIndex(Request $request): View
    {
        $query = Appointment::with('patient');

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        } elseif (! $request->status) {
            $query->where('status', 'scheduled');
        }

        $appointments = $query
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        return view('appointments.admin', compact('appointments'));
    }

    public function markCompleted(int $id): RedirectResponse
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->update([
            'status' => 'completed',
        ]);

        $schedule = DoctorSchedule::where('doctor_id', $appointment->doctor_id)
            ->whereDate('working_date', $this->appointmentDate($appointment))
            ->first();

        if ($schedule) {
            $this->scheduleStatusService->refreshScheduleAvailability($schedule);
        }

        return back()->with('success', 'Marked as completed');
    }

    public function markNoShow(int $id): RedirectResponse
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->update([
            'status' => 'no_show',
        ]);

        $schedule = DoctorSchedule::where('doctor_id', $appointment->doctor_id)
            ->whereDate('working_date', $this->appointmentDate($appointment))
            ->first();

        if ($schedule) {
            $this->scheduleStatusService->refreshScheduleAvailability($schedule);
        }

        return back()->with('success', 'Marked as no-show');
    }

    public function availableSlots(Request $request, string $date): JsonResponse
    {
        $service = Service::active()->find($request->query('service_id'));

        if (! $service) {
            return response()->json([]);
        }

        $ignoreAppointmentId = null;

        if ($request->query('appointment_id')) {
            $appointment = Appointment::where('id', $request->query('appointment_id'))
                ->where('patient_id', Auth::id())
                ->first();

            $ignoreAppointmentId = $appointment?->id;
        }

        $schedules = DoctorSchedule::with([
            'doctor',
            'slots' => fn ($query) => $query->orderBy('start_time'),
        ])
            ->whereDate('working_date', $date)
            ->where('status', DoctorSchedule::STATUS_ACTIVE)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get()
            ->filter(function (DoctorSchedule $schedule) use ($service, $ignoreAppointmentId): bool {
                $availableSlots = $this->scheduleSlotService->getAvailableSlots($schedule, $service, $ignoreAppointmentId)
                    ->map(function (ScheduleSlot $slot) use ($service): ScheduleSlot {
                        [$startTime, $endTime] = $this->scheduleSlotService->appointmentRangeForSlot($slot, $service);
                        $slot->setAttribute('start_time', $this->timeString($startTime));
                        $slot->setAttribute('end_time', $this->timeString($slot->end_time));
                        $slot->setAttribute('appointment_end_time', $endTime->format('H:i:s'));

                        return $slot;
                    });

                $schedule->setRelation('slots', $availableSlots);

                return $availableSlots->isNotEmpty();
            })
            ->values();

        return response()->json($schedules);
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

    /**
     * @return array<string, string>
     */
    private function bookingValidationMessages(): array
    {
        return [
            'service_id.required' => 'Please select a valid service, date, and time slot.',
            'service_id.exists' => 'Please select a valid service, date, and time slot.',
            'slot_id.required' => 'Please select a valid service, date, and time slot.',
            'slot_id.exists' => 'Please select a valid service, date, and time slot.',
        ];
    }
}
