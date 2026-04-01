<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    /**
     * Require authentication for all methods.
     */
    public function __construct()
    {
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

            $dentists = User::where('role', Role::Dentist)->get();

            return view('admin.schedules.index', compact('schedules', 'dentists'));
        }

        // Dentist sees only their own schedules (filtered by doctor_id)
        if ($user->isDentist()) {
            $schedules = DoctorSchedule::where('doctor_id', $user->id)
                ->orderBy('working_date', 'desc')
                ->orderBy('start_time')
                ->paginate(10);

            return view('dentist.schedules.index', compact('schedules'));
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

        return view('admin.schedules.create', compact('dentists'));
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
        // Step 1: Validate form input
        $validated = $request->validate($this->validationRules());

        // Step 2: Create schedule object with validated data
        $schedule = new DoctorSchedule($validated);

        // Step 3: Check for overlapping schedules (same doctor, same date)
        if ($schedule->hasOverlap()) {
            return back()->withErrors([
                'working_date' => 'This doctor already has a schedule for the selected date.',
            ])->withInput();
        }

        // Step 4: Validate break time is within working hours
        if (! $this->isBreakTimeValid($schedule)) {
            return back()->withErrors([
                'break_start' => 'Break time must be within working hours.',
                'break_end' => 'Break end must be after break start and within working hours.',
            ])->withInput();
        }

        // Step 5: Validate working hours are reasonable
        if (! $this->isWorkingHoursValid($schedule)) {
            return back()->withErrors([
                'end_time' => 'Working hours must be at least '.config('schedules.min_work_duration', 1).' hour(s).',
            ])->withInput();
        }

        // Step 6: Save schedule and generate time slots
        $schedule->save();
        $schedule->generateSlots();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule created successfully. Time slots have been generated.');
    }

    /**
     * Display the specified schedule details.
     */
    public function show(DoctorSchedule $schedule): View
    {
        $schedule->load('doctor', 'slots');

        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit(DoctorSchedule $schedule): View
    {
        $dentists = User::where('role', Role::Dentist)->get();

        return view('admin.schedules.edit', compact('schedule', 'dentists'));
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
        // Step 1: Validate form input
        $validated = $request->validate($this->validationRules());

        // Step 2: Fill model with validated data
        $schedule->fill($validated);

        // Step 3: Check for overlapping schedules (excluding current schedule)
        if ($schedule->hasOverlap()) {
            return back()->withErrors([
                'working_date' => 'This doctor already has a schedule for the selected date.',
            ])->withInput();
        }

        // Step 4: Validate break time is within working hours
        if (! $this->isBreakTimeValid($schedule)) {
            return back()->withErrors([
                'break_start' => 'Break time must be within working hours.',
                'break_end' => 'Break end must be after break start and within working hours.',
            ])->withInput();
        }

        // Step 5: Validate working hours are reasonable
        if (! $this->isWorkingHoursValid($schedule)) {
            return back()->withErrors([
                'end_time' => 'Working hours must be at least '.config('schedules.min_work_duration', 1).' hour(s).',
            ])->withInput();
        }

        // Step 6: Save and regenerate time slots
        $schedule->save();
        $schedule->generateSlots();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule updated successfully. Time slots have been regenerated.');
    }

    /**
     * Remove the specified schedule from storage.
     * Also deletes associated time slots (cascade).
     */
    public function destroy(DoctorSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule deleted successfully.');
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
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
            'slot_duration' => 'required|integer|min:10|max:120',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Validate that break time is within working hours.
     *
     * Conditions:
     * - If no break times provided, always valid
     * - Break start must be >= work start
     * - Break end must be <= work end
     */
    private function isBreakTimeValid(DoctorSchedule $schedule): bool
    {
        // No break time = always valid
        if (empty($schedule->break_start) || empty($schedule->break_end)) {
            return true;
        }

        $workStart = strtotime($schedule->start_time);
        $workEnd = strtotime($schedule->end_time);
        $breakStart = strtotime($schedule->break_start);
        $breakEnd = strtotime($schedule->break_end);

        // Break must start after work starts
        if ($breakStart < $workStart) {
            return false;
        }

        // Break must end before work ends
        if ($breakEnd > $workEnd) {
            return false;
        }

        // Break must be at least 15 minutes
        if (($breakEnd - $breakStart) < 900) { // 900 seconds = 15 minutes
            return false;
        }

        return true;
    }

    /**
     * Validate that working hours meet minimum duration.
     *
     * Minimum: 1 hour (configurable)
     */
    private function isWorkingHoursValid(DoctorSchedule $schedule): bool
    {
        $workStart = strtotime($schedule->start_time);
        $workEnd = strtotime($schedule->end_time);
        $minDuration = (config('schedules.min_work_duration', 1)) * 3600; // Convert hours to seconds

        return ($workEnd - $workStart) >= $minDuration;
    }
}
