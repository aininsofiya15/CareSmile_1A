<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::where('patient_id', Auth::id())->get();

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        // 从数据库读取 active services
        $services = \App\Models\Service::where('is_active', true)->get();

        // 读取有 available slots 的日期（未来日期）
        $availableSchedules = \App\Models\DoctorSchedule::with(['slots' => function ($q) {
            $q->where('is_available', true);
        }, 'doctor'])
            ->where('working_date', '>=', now()->toDateString())
            ->where('is_active', true)
            ->get();

        return view('appointments.create', compact('services', 'availableSchedules'));
    }

    // public function store(Request $request)
    // {
    //     // Validation
    //     $validated = $request->validate([
    //         'date' => 'required|date|after_or_equal:today',
    //         'time' => 'required',
    //         'service' => 'required|string',
    //     ]);

    //     // 🚫 double booking check
    //     $exists = Appointment::where('appointment_date', $validated['date'])
    //         ->where('appointment_time', $validated['time'])
    //         ->where('status', 'scheduled')
    //         ->exists();

    //     if ($exists) {
    //         return back()->withErrors([
    //             'time' => 'This time slot is already booked. Please choose another time.',
    //         ])->withInput();
    //     }

    //     Appointment::create([
    //         'patient_id' => Auth::id(),
    //         'appointment_date' => $validated['date'],
    //         'appointment_time' => $validated['time'],
    //         'service' => $validated['service'],
    //     ]);

    //     return redirect()->route('patient.appointments')
    //         ->with('success', 'Appointment booked!');
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slot_id' => 'required|exists:schedule_slots,id',
            'service' => 'required|exists:services,name',
        ]);

        // 找到这个 slot
        $slot = \App\Models\ScheduleSlot::with('schedule')->findOrFail($validated['slot_id']);

        // 检查 slot 是否还 available
        if (!$slot->is_available) {
            return back()->withErrors([
                'slot_id' => 'This time slot is already booked. Please choose another time.',
            ])->withInput();
        }

        $service = \App\Models\Service::where('name', $validated['service'])->first();
        $slot = \App\Models\ScheduleSlot::with('schedule')->findOrFail($validated['slot_id']);

        // 计算 slot duration（分钟）
        $slotDuration = \Carbon\Carbon::parse($slot->start_time)
            ->diffInMinutes(\Carbon\Carbon::parse($slot->end_time));

        if ($service->duration_minutes > $slotDuration) {
            return back()->withErrors([
                'slot_id' => 'Selected time slot is too short for this service.',
            ])->withInput();
        }

        // 创建 appointment
        Appointment::create([
            'patient_id' => Auth::id(),
            'doctor_id' => $slot->schedule->doctor_id,
            'appointment_date' => $slot->schedule->working_date,
            'appointment_time' => $slot->start_time,
            'service' => $validated['service'],
        ]);

        // 把 slot 标记为 unavailable
        $slot->book();

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment booked!');
    }

    // public function showReschedule($id)
    // {
    //     $appointment = Appointment::where('id', $id)
    //         ->where('patient_id', Auth::id())
    //         ->firstOrFail();

    //     return view('appointments.reschedule', compact('appointment'));
    // }

    public function showReschedule($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        // 读取有 available slots 的 schedules（同样逻辑）
        $availableSchedules = \App\Models\DoctorSchedule::with(['slots' => function ($q) {
            $q->where('is_available', true);
        }, 'doctor'])
            ->where('working_date', '>=', now()->toDateString())
            ->where('is_active', true)
            ->get();

        return view('appointments.reschedule', compact('appointment', 'availableSchedules'));
    }

    // public function submitReschedule(Request $request, $id)
    // {
    //     $appointment = Appointment::where('id', $id)
    //         ->where('patient_id', Auth::id())
    //         ->firstOrFail();

    //     $appointment->appointment_date = $request->date;
    //     $appointment->appointment_time = $request->time;
    //     $appointment->save();

    //     return redirect()->route('patient.appointments')
    //         ->with('success', 'Appointment updated!');
    // }

    public function submitReschedule(Request $request, $id)
    {
        $request->validate([
            'slot_id' => 'required|exists:schedule_slots,id',
        ]);

        $appointment = Appointment::where('id', $id)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        // Step 1: 释放旧的 slot
        $oldSlot = \App\Models\ScheduleSlot::where('start_time', $appointment->appointment_time)
            ->whereHas('schedule', function ($q) use ($appointment) {
                $q->where('doctor_id', $appointment->doctor_id)
                ->where('working_date', $appointment->appointment_date);
            })
            ->first();

        if ($oldSlot) {
            $oldSlot->release(); // is_available = true
        }

        // Step 2: 占用新的 slot
        $newSlot = \App\Models\ScheduleSlot::with('schedule')->findOrFail($request->slot_id);

        if (!$newSlot->is_available) {
            return back()->withErrors([
                'slot_id' => 'This time slot is already booked. Please choose another.',
            ])->withInput();
        }

        // Step 3: 更新 appointment
        $appointment->appointment_date = $newSlot->schedule->working_date;
        $appointment->appointment_time = $newSlot->start_time;
        $appointment->doctor_id = $newSlot->schedule->doctor_id;
        $appointment->save();

        // Step 4: 标记新 slot 为 unavailable
        $newSlot->book();

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment rescheduled successfully!');
    }

    // public function cancel($id)
    // {
    //     $appointment = Appointment::where('id', $id)
    //         ->where('patient_id', Auth::id())
    //         ->firstOrFail();

    //     $appointment->status = 'cancelled';
    //     $appointment->save();

    //     return redirect()->route('patient.appointments')
    //         ->with('success', 'Appointment cancelled!');
    // }

    public function cancel($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        $appointment->status = 'cancelled';
        $appointment->save();

        // 释放 slot，让其他人可以预约
        $slot = \App\Models\ScheduleSlot::where('schedule_id', function ($q) use ($appointment) {
            $q->select('id')
            ->from('doctor_schedules')
            ->where('doctor_id', $appointment->doctor_id)
            ->where('working_date', $appointment->appointment_date);
        })
            ->where('start_time', $appointment->appointment_time)
            ->first();

        if ($slot) {
            $slot->release();
        }

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment cancelled!');
    }

    // ADMIN
    public function adminIndex(Request $request)
    {
        $query = Appointment::with('patient');

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        } elseif (!$request->status) {
            // default
            $query->where('status', 'scheduled');
        }

        $appointments = $query->get();

        return view('appointments.admin', compact('appointments'));
    }

    public function markCompleted($id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->status = 'completed';
        $appointment->save();

        return back()->with('success', 'Marked as completed');
    }

    public function markNoShow($id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->status = 'no_show';
        $appointment->save();

        return back()->with('success', 'Marked as no-show');
    }
}
