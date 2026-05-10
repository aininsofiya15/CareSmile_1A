<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\User;
use App\Services\ScheduleImpactService;
use App\Services\ScheduleSlotService;
use App\Services\ScheduleStatusService;
use App\Services\ScheduleUtilizationService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    /**
     * Require authentication for all methods.
     */
    public function __construct(
        private ScheduleStatusService $scheduleStatusService,
        private ScheduleImpactService $scheduleImpactService,
        private ScheduleSlotService $scheduleSlotService,
        private ScheduleUtilizationService $scheduleUtilizationService
    ) {
        $this->middleware('auth');
    }

    /**
     * Display schedules based on user role.
     *
     * - Admin: sees all schedules from all dentists
     * - Dentist: sees only their own schedules
     * - Others: denied
     */
    public function index(): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        // Admin sees all schedules with dentist relationship
        if ($user->isAdmin()) {
            $schedules = DoctorSchedule::with('doctor')
                ->orderBy('working_date', 'desc')
                ->orderBy('start_time')
                ->paginate(10);

            $scheduleImpactSummaries = $schedules->getCollection()
                ->mapWithKeys(fn (DoctorSchedule $schedule): array => [
                    $schedule->id => $this->scheduleImpactService->getImpactSummary($schedule),
                ]);
            $scheduleUtilizationSummaries = $schedules->getCollection()
                ->mapWithKeys(fn (DoctorSchedule $schedule): array => [
                    $schedule->id => $this->scheduleUtilizationService->getScheduleUtilizationSummary($schedule),
                ]);
            $adminUtilizationSummary = $this->scheduleUtilizationService->getAggregateSummary($schedules->getCollection());
            $dentists = User::where('role', Role::Dentist)->get();

            return view('admin.schedules.index', compact('schedules', 'dentists', 'scheduleImpactSummaries', 'scheduleUtilizationSummaries', 'adminUtilizationSummary'));
        }

        // Dentist sees only their own schedules (filtered by doctor_id)
        if ($user->isDentist()) {
            $schedules = DoctorSchedule::with([
                'slots' => fn ($query) => $query->orderBy('start_time'),
            ])
                ->where('doctor_id', $user->id)
                ->orderBy('working_date', 'desc')
                ->orderBy('start_time')
                ->paginate(10);
            $scheduleUtilizationSummaries = $schedules->getCollection()
                ->mapWithKeys(fn (DoctorSchedule $schedule): array => [
                    $schedule->id => $this->scheduleUtilizationService->getScheduleUtilizationSummary($schedule),
                ]);

            return view('dentist.schedules.index', compact('schedules', 'scheduleUtilizationSummaries'));
        }

        abort(403, 'Unauthorized access');
    }

    /**
     * Show the form for creating a new schedule.
     * Only accessible by admin.
     */
    public function create(): View
    {
        $dentists = User::where('role', Role::Dentist)->get();
        $timeOptions = $this->clinicTimeOptions();
        $statusOptions = DoctorSchedule::statusOptions();

        return view('admin.schedules.create', compact('dentists', 'timeOptions', 'statusOptions'));
    }

    /**
     * Store a newly created schedule in storage.
     *
     * Validation:
     * - All required fields must be present
     * - Doctor must exist in users table
     * - Date must be today or future
     * - End time must be after start time
     * - Break times must be valid if provided
     *
     * Business Rules:
     * - Same doctor cannot have two schedules on same date (overlap check)
     * - Break time must be within working hours
     * - Time slots are automatically generated after save
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules(), $this->validationMessages());
        $validated = $this->withScheduleStatusDefaults($validated);

        $schedule = new DoctorSchedule($validated);
        $conflict = $this->detectScheduleConflict($validated);

        if ($conflict['hasConflict']) {
            return back()->withErrors([
                $conflict['field'] => $conflict['message'],
            ])->withInput();
        }

        $schedule->save();
        $schedule->generateSlots();
        $this->scheduleStatusService->refreshScheduleAvailability($schedule);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule created successfully. Time slots have been generated.');
    }

    /**
     * Display the specified schedule details.
     */
    public function show(DoctorSchedule $schedule): View
    {
        $schedule->load([
            'doctor',
            'slots' => fn ($query) => $query->orderBy('start_time'),
        ]);
        $utilizationSummary = $this->scheduleUtilizationService->getScheduleUtilizationSummary($schedule);

        return view('admin.schedules.show', compact('schedule', 'utilizationSummary'));
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit(DoctorSchedule $schedule): View
    {
        $dentists = User::where('role', Role::Dentist)->get();
        $timeOptions = $this->clinicTimeOptions();
        $statusOptions = DoctorSchedule::statusOptions();
        $impactSummary = $this->scheduleImpactService->getImpactSummary($schedule);

        return view('admin.schedules.edit', compact('schedule', 'dentists', 'timeOptions', 'statusOptions', 'impactSummary'));
    }

    /**
     * Update the specified schedule in storage.
     *
     * Similar validation as store, plus:
     * - Overlap check excludes current schedule (id != current)
     * - Time slots are regenerated after update
     */
    public function update(Request $request, DoctorSchedule $schedule): RedirectResponse
    {
        $validated = $request->validate($this->validationRules(), $this->validationMessages());
        $validated = $this->withScheduleStatusDefaults($validated);
        $conflict = $this->detectScheduleConflict($validated, $schedule->id);

        if ($conflict['hasConflict']) {
            return back()->withErrors([
                $conflict['field'] => $conflict['message'],
            ])->withInput();
        }

        if (
            $this->scheduleImpactService->hasAffectedAppointments($schedule) &&
            $this->scheduleImpactService->isMajorScheduleChange($schedule, $validated) &&
            $request->input('impact_confirmed') !== '1'
        ) {
            return back()
                ->with('warning', 'This schedule has existing appointments. Changing the date, time, break time, duration, dentist, or status may affect patient bookings.')
                ->withInput();
        }

        $schedule->fill($validated);
        $schedule->save();
        $schedule->generateSlots();
        $this->scheduleStatusService->refreshScheduleAvailability($schedule);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule updated successfully. Time slots have been regenerated.');
    }

    /**
     * Remove the specified schedule from storage.
     * Also deletes associated time slots (cascade).
     */
    public function destroy(DoctorSchedule $schedule): RedirectResponse
    {
        $impactSummary = $this->scheduleImpactService->getImpactSummary($schedule);

        if ($impactSummary['count'] > 0) {
            return redirect()->route('admin.schedules.index')
                ->with('error', 'This schedule cannot be deleted because it has existing patient appointments. Please cancel or reschedule the appointments first.');
        }

        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule deleted successfully.');
    }

    public function checkConflict(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'working_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'slot_duration' => 'required|integer|min:1|max:120',
            'schedule_id' => 'nullable|integer|exists:doctor_schedules,id',
        ], $this->validationMessages());

        if ($validator->fails()) {
            return response()->json([
                'hasConflict' => true,
                'type' => 'invalid_time',
                'message' => $validator->errors()->first() ?: 'Please complete all required schedule fields before checking conflicts.',
                'conflicts' => [],
            ]);
        }

        return response()->json(
            $this->publicConflictResponse(
                $this->detectScheduleConflict($validator->validated(), $request->integer('schedule_id') ?: null)
            )
        );
    }

    public function previewSlots(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'working_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'slot_duration' => 'required|integer|min:1|max:120',
            'schedule_id' => 'nullable|integer|exists:doctor_schedules,id',
        ], $this->previewValidationMessages());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?: 'Please complete all required schedule fields before previewing slots.',
                'warning' => null,
                'summary' => null,
                'slots' => [],
            ]);
        }

        $validated = $validator->validated();
        $conflict = $this->detectScheduleConflict($validated, $request->integer('schedule_id') ?: null);

        if ($conflict['hasConflict'] && in_array($conflict['type'], ['invalid_time', 'break_time', 'invalid_slot_duration'], true)) {
            return response()->json([
                'success' => false,
                'message' => $conflict['message'],
                'warning' => null,
                'summary' => null,
                'slots' => [],
            ]);
        }

        $preview = $this->buildSlotPreview($validated, $conflict);

        return response()->json([
            'success' => true,
            'message' => 'Slot preview generated successfully.',
            'warning' => $conflict['hasConflict'] ? $conflict['message'] : null,
            'summary' => $preview['summary'],
            'slots' => $preview['slots'],
        ]);
    }

    /**
     * Define validation rules for schedule form.
     *
     * Rules:
     * - doctor_id: Required, must exist in users table
     * - working_date: Required, must be today or future date
     * - start_time/end_time: Required, end must be after start
     * - break_start/break_end: Optional, end must be after start if both provided
     * - slot_duration: Required, must be between 10-120 minutes
     * - notes: Optional, max 1000 characters
     * - is_active: Optional, defaults to false if not checked
     */
    private function validationRules(): array
    {
        return [
            'doctor_id' => 'required|exists:users,id',
            'working_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start' => 'nullable|required_with:break_end|date_format:H:i',
            'break_end' => 'nullable|required_with:break_start|date_format:H:i|after:break_start',
            'slot_duration' => 'required|integer|min:1|max:120',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'status' => ['nullable', Rule::in(DoctorSchedule::statuses())],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function withScheduleStatusDefaults(array $validated): array
    {
        $validated['status'] = $validated['status'] ?? DoctorSchedule::STATUS_ACTIVE;
        $validated['is_active'] = $validated['status'] === DoctorSchedule::STATUS_ACTIVE;
        $validated['status_updated_automatically'] = false;

        return $validated;
    }

    /**
     * @return array<string, string>
     */
    private function validationMessages(): array
    {
        return [
            'doctor_id.required' => 'Please complete all required schedule fields before checking conflicts.',
            'doctor_id.exists' => 'Please select a valid doctor.',
            'working_date.required' => 'Please complete all required schedule fields before checking conflicts.',
            'working_date.after_or_equal' => 'Working date must be today or a future date.',
            'start_time.required' => 'Please complete all required schedule fields before checking conflicts.',
            'end_time.required' => 'Please complete all required schedule fields before checking conflicts.',
            'end_time.after' => 'Start time must be earlier than end time.',
            'break_start.required_with' => 'Break start and break end time must both be provided.',
            'break_end.required_with' => 'Break start and break end time must both be provided.',
            'break_end.after' => 'Break start time must be earlier than break end time.',
            'slot_duration.required' => 'Please complete all required schedule fields before checking conflicts.',
            'slot_duration.integer' => 'Slot duration must be a positive number.',
            'slot_duration.min' => 'Slot duration must be a positive number.',
            'status.in' => 'Please select a valid schedule status.',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function previewValidationMessages(): array
    {
        return array_merge($this->validationMessages(), [
            'doctor_id.required' => 'Please complete all required schedule fields before previewing slots.',
            'working_date.required' => 'Please complete all required schedule fields before previewing slots.',
            'start_time.required' => 'Please complete all required schedule fields before previewing slots.',
            'end_time.required' => 'Please complete all required schedule fields before previewing slots.',
            'slot_duration.required' => 'Please complete all required schedule fields before previewing slots.',
        ]);
    }

    /**
     * @param  array<string, mixed>  $input
     * @param  array{hasConflict: bool, type: ?string, field: string, message: string, conflicts: array<int, array<string, string>>}  $conflict
     * @return array{summary: array<string, mixed>, slots: array<int, array<string, ?string>>}
     */
    private function buildSlotPreview(array $input, array $conflict): array
    {
        $date = Carbon::parse((string) $input['working_date'])->toDateString();
        $workStart = Carbon::parse($date.' '.$this->normalizeTime($input['start_time']));
        $workEnd = Carbon::parse($date.' '.$this->normalizeTime($input['end_time']));
        $slotDuration = (int) $input['slot_duration'];
        $breakStart = ! empty($input['break_start']) ? Carbon::parse($date.' '.$this->normalizeTime($input['break_start'])) : null;
        $breakEnd = ! empty($input['break_end']) ? Carbon::parse($date.' '.$this->normalizeTime($input['break_end'])) : null;
        $appointments = $this->blockingAppointments((int) $input['doctor_id'], $date);
        $scheduleConflictRanges = $this->scheduleConflictRanges($date, $conflict['conflicts']);
        $slots = [];

        foreach ($this->scheduleSlotService->calculateSlotRangesFromValues(
            $date,
            $workStart->format('H:i:s'),
            $workEnd->format('H:i:s'),
            $slotDuration,
            $breakStart?->format('H:i:s'),
            $breakEnd?->format('H:i:s')
        ) as $slotRange) {
            $slotStart = $slotRange['start'];
            $slotEnd = $slotRange['end'];
            [$status, $reason] = $this->slotPreviewStatus($slotStart, $slotEnd, $breakStart, $breakEnd, $appointments, $scheduleConflictRanges);

            $slots[] = [
                'start_time' => $slotStart->format('H:i'),
                'end_time' => $slotEnd->format('H:i'),
                'display_start_time' => $slotStart->format('h:i A'),
                'display_end_time' => $slotEnd->format('h:i A'),
                'status' => $status,
                'reason' => $reason,
            ];
        }

        $blockedSlots = collect($slots)->where('status', 'blocked')->count();

        return [
            'summary' => [
                'working_date' => $date,
                'working_start' => $workStart->format('H:i'),
                'working_end' => $workEnd->format('H:i'),
                'display_working_date' => $workStart->format('d M Y'),
                'display_working_start' => $workStart->format('h:i A'),
                'display_working_end' => $workEnd->format('h:i A'),
                'break_start' => $breakStart?->format('H:i'),
                'break_end' => $breakEnd?->format('H:i'),
                'display_break_start' => $breakStart?->format('h:i A'),
                'display_break_end' => $breakEnd?->format('h:i A'),
                'slot_duration' => $slotDuration,
                'total_slots' => count($slots),
                'available_slots' => count($slots) - $blockedSlots,
                'blocked_slots' => $blockedSlots,
            ],
            'slots' => $slots,
        ];
    }

    /**
     * @param  array<int, array{start: Carbon, end: Carbon}>  $appointments
     * @param  array<int, array{start: Carbon, end: Carbon}>  $scheduleConflictRanges
     * @return array{0: string, 1: ?string}
     */
    private function slotPreviewStatus(
        Carbon $slotStart,
        Carbon $slotEnd,
        ?Carbon $breakStart,
        ?Carbon $breakEnd,
        array $appointments,
        array $scheduleConflictRanges
    ): array {
        if ($breakStart && $breakEnd && $this->timeRangesOverlap($slotStart, $slotEnd, $breakStart, $breakEnd)) {
            return ['blocked', 'Break time'];
        }

        foreach ($appointments as $appointment) {
            if ($this->timeRangesOverlap($slotStart, $slotEnd, $appointment['start'], $appointment['end'])) {
                return ['blocked', 'Existing appointment'];
            }
        }

        foreach ($scheduleConflictRanges as $conflictRange) {
            if ($this->timeRangesOverlap($slotStart, $slotEnd, $conflictRange['start'], $conflictRange['end'])) {
                return ['blocked', 'Schedule conflict'];
            }
        }

        return ['available', null];
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    private function blockingAppointments(int $doctorId, string $date): array
    {
        $appointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->get();

        $serviceDurations = Service::whereIn('name', $appointments->pluck('service')->unique()->all())
            ->pluck('duration_minutes', 'name')
            ->map(fn ($duration): int => (int) $duration)
            ->all();

        return $appointments->map(function (Appointment $appointment) use ($serviceDurations): array {
            $appointmentDate = $this->appointmentDate($appointment);
            $appointmentStart = Carbon::parse($appointmentDate.' '.$this->normalizeTime($appointment->appointment_time));
            $appointmentEnd = $appointment->end_time
                ? Carbon::parse($appointmentDate.' '.$this->normalizeTime($appointment->end_time))
                : $appointmentStart->copy()->addMinutes($serviceDurations[$appointment->service] ?? 1);

            return [
                'start' => $appointmentStart,
                'end' => $appointmentEnd,
            ];
        })->all();
    }

    /**
     * @param  array<int, array<string, string>>  $conflicts
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    private function scheduleConflictRanges(string $date, array $conflicts): array
    {
        return collect($conflicts)
            ->map(fn (array $conflict): array => [
                'start' => Carbon::parse($date.' '.$this->normalizeTime($conflict['start_time'])),
                'end' => Carbon::parse($date.' '.$this->normalizeTime($conflict['end_time'])),
            ])
            ->all();
    }

    private function timeRangesOverlap(Carbon $firstStart, Carbon $firstEnd, Carbon $secondStart, Carbon $secondEnd): bool
    {
        return $firstStart->lt($secondEnd) && $firstEnd->gt($secondStart);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{hasConflict: bool, type: ?string, field: string, message: string, conflicts: array<int, array<string, string>>}
     */
    private function detectScheduleConflict(array $input, ?int $ignoreScheduleId = null): array
    {
        $startMinutes = $this->minutesFromTime($input['start_time'] ?? null);
        $endMinutes = $this->minutesFromTime($input['end_time'] ?? null);

        if ($startMinutes === null || $endMinutes === null || $startMinutes >= $endMinutes) {
            return $this->conflict('invalid_time', 'end_time', 'Start time must be earlier than end time.');
        }

        $workingDuration = $endMinutes - $startMinutes;
        $minimumWorkingMinutes = (int) config('schedules.min_work_duration', 1) * 60;
        $clinicOpenMinutes = $this->clinicOpeningMinutes();
        $clinicCloseMinutes = $this->clinicClosingMinutes();
        $timeIntervalMinutes = $this->clinicTimeIntervalMinutes();

        if ($startMinutes < $clinicOpenMinutes || $endMinutes > $clinicCloseMinutes) {
            return $this->conflict(
                'invalid_time',
                'start_time',
                'Schedule time must be within clinic operating hours. Clinic operating hours are '.$this->clinicOperatingHoursLabel().'.'
            );
        }

        if (($startMinutes - $clinicOpenMinutes) % $timeIntervalMinutes !== 0 || ($endMinutes - $clinicOpenMinutes) % $timeIntervalMinutes !== 0) {
            return $this->conflict('invalid_time', 'start_time', 'Please select time in '.$timeIntervalMinutes.'-minute intervals.');
        }

        if ($workingDuration < $minimumWorkingMinutes) {
            return $this->conflict('invalid_time', 'end_time', 'Working hours must be at least '.config('schedules.min_work_duration', 1).' hour(s).');
        }

        $slotDuration = (int) ($input['slot_duration'] ?? 0);

        if ($slotDuration <= 0) {
            return $this->conflict('invalid_slot_duration', 'slot_duration', 'Slot duration must be a positive number.');
        }

        if ($slotDuration > $workingDuration) {
            return $this->conflict('invalid_slot_duration', 'slot_duration', 'Slot duration must be shorter than or equal to the working duration.');
        }

        $breakStartMinutes = $this->minutesFromTime($input['break_start'] ?? null);
        $breakEndMinutes = $this->minutesFromTime($input['break_end'] ?? null);

        if (($breakStartMinutes !== null && $breakEndMinutes === null) || ($breakStartMinutes === null && $breakEndMinutes !== null)) {
            return $this->conflict('break_time', 'break_start', 'Break start and break end time must both be provided.');
        }

        if ($breakStartMinutes !== null && $breakEndMinutes !== null) {
            if ($breakStartMinutes >= $breakEndMinutes) {
                return $this->conflict('break_time', 'break_start', 'Break start time must be earlier than break end time.');
            }

            if (($breakStartMinutes - $clinicOpenMinutes) % $timeIntervalMinutes !== 0 || ($breakEndMinutes - $clinicOpenMinutes) % $timeIntervalMinutes !== 0) {
                return $this->conflict('break_time', 'break_start', 'Please select time in '.$timeIntervalMinutes.'-minute intervals.');
            }

            if ($breakStartMinutes < $startMinutes || $breakEndMinutes > $endMinutes) {
                return $this->conflict('break_time', 'break_start', 'Break time must be within working hours.');
            }
        }

        $generatedSlots = $this->scheduleSlotService->calculateSlotRangesFromValues(
            Carbon::parse((string) $input['working_date'])->toDateString(),
            $this->normalizeTime($input['start_time']),
            $this->normalizeTime($input['end_time']),
            $slotDuration,
            ! empty($input['break_start']) ? $this->normalizeTime($input['break_start']) : null,
            ! empty($input['break_end']) ? $this->normalizeTime($input['break_end']) : null
        );

        if ($generatedSlots === []) {
            return $this->conflict('invalid_slot_duration', 'slot_duration', 'Slot duration is too large for the selected schedule.');
        }

        $baseQuery = DoctorSchedule::where('doctor_id', $input['doctor_id'])
            ->whereDate('working_date', Carbon::parse((string) $input['working_date'])->toDateString())
            ->when($ignoreScheduleId, fn ($query) => $query->where('id', '!=', $ignoreScheduleId));

        $duplicate = (clone $baseQuery)
            ->whereTime('start_time', $this->normalizeTime($input['start_time']))
            ->whereTime('end_time', $this->normalizeTime($input['end_time']))
            ->first();

        if ($duplicate) {
            return $this->conflict(
                'duplicate',
                'working_date',
                'This doctor already has a schedule for the same date and time.',
                [$this->formatConflictSchedule($duplicate)]
            );
        }

        $overlaps = (clone $baseQuery)
            ->whereTime('start_time', '<', $this->normalizeTime($input['end_time']))
            ->whereTime('end_time', '>', $this->normalizeTime($input['start_time']))
            ->orderBy('start_time')
            ->get();

        if ($overlaps->isNotEmpty()) {
            return $this->conflict(
                'overlap',
                'working_date',
                'This doctor already has a schedule that overlaps with the selected time.',
                $overlaps->map(fn (DoctorSchedule $schedule): array => $this->formatConflictSchedule($schedule))->all()
            );
        }

        return [
            'hasConflict' => false,
            'type' => null,
            'field' => 'working_date',
            'message' => 'No schedule conflict detected.',
            'conflicts' => [],
        ];
    }

    /**
     * @param  array<int, array<string, string>>  $conflicts
     * @return array{hasConflict: bool, type: string, field: string, message: string, conflicts: array<int, array<string, string>>}
     */
    private function conflict(string $type, string $field, string $message, array $conflicts = []): array
    {
        return [
            'hasConflict' => true,
            'type' => $type,
            'field' => $field,
            'message' => $message,
            'conflicts' => $conflicts,
        ];
    }

    /**
     * @param  array{hasConflict: bool, type: ?string, field: string, message: string, conflicts: array<int, array<string, string>>}  $conflict
     * @return array{hasConflict: bool, type: ?string, message: string, conflicts: array<int, array<string, string>>}
     */
    private function publicConflictResponse(array $conflict): array
    {
        return [
            'hasConflict' => $conflict['hasConflict'],
            'type' => $conflict['type'],
            'message' => $conflict['message'],
            'conflicts' => $conflict['conflicts'],
        ];
    }

    /**
     * @return array{date: string, start_time: string, end_time: string, display_start_time: string, display_end_time: string}
     */
    private function formatConflictSchedule(DoctorSchedule $schedule): array
    {
        return [
            'date' => $this->scheduleDate($schedule),
            'start_time' => $this->normalizeTime($schedule->start_time),
            'end_time' => $this->normalizeTime($schedule->end_time),
            'display_start_time' => Carbon::parse($schedule->start_time)->format('h:i A'),
            'display_end_time' => Carbon::parse($schedule->end_time)->format('h:i A'),
        ];
    }

    private function scheduleDate(DoctorSchedule $schedule): string
    {
        if ($schedule->working_date instanceof CarbonInterface) {
            return $schedule->working_date->toDateString();
        }

        return Carbon::parse($schedule->working_date)->toDateString();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function clinicTimeOptions(): array
    {
        $openingTime = Carbon::parse($this->clinicOpeningTime());
        $closingTime = Carbon::parse($this->clinicClosingTime());
        $intervalMinutes = $this->clinicTimeIntervalMinutes();
        $options = [];

        for ($time = $openingTime->copy(); $time->lte($closingTime); $time->addMinutes($intervalMinutes)) {
            $options[] = [
                'value' => $time->format('H:i'),
                'label' => $time->format('h:i A'),
            ];
        }

        return $options;
    }

    private function clinicOpeningTime(): string
    {
        return (string) config('clinic.opening_time', '08:00');
    }

    private function clinicClosingTime(): string
    {
        return (string) config('clinic.closing_time', '18:00');
    }

    private function clinicOpeningMinutes(): int
    {
        return $this->minutesFromTime($this->clinicOpeningTime()) ?? 0;
    }

    private function clinicClosingMinutes(): int
    {
        return $this->minutesFromTime($this->clinicClosingTime()) ?? 0;
    }

    private function clinicTimeIntervalMinutes(): int
    {
        return (int) config('clinic.time_interval_minutes', 30);
    }

    private function clinicOperatingHoursLabel(): string
    {
        return Carbon::parse($this->clinicOpeningTime())->format('h:i A').' to '.Carbon::parse($this->clinicClosingTime())->format('h:i A');
    }

    private function appointmentDate(Appointment $appointment): string
    {
        if ($appointment->appointment_date instanceof CarbonInterface) {
            return $appointment->appointment_date->toDateString();
        }

        return Carbon::parse($appointment->appointment_date)->toDateString();
    }

    private function minutesFromTime(mixed $time): ?int
    {
        if ($time === null || $time === '') {
            return null;
        }

        $parsed = Carbon::parse((string) $time);

        return ($parsed->hour * 60) + $parsed->minute;
    }

    private function normalizeTime(mixed $time): string
    {
        if ($time instanceof CarbonInterface) {
            return $time->format('H:i:s');
        }

        return Carbon::parse((string) $time)->format('H:i:s');
    }
}
