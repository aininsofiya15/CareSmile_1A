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
        return view('appointments.create');
    }

    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'service' => 'required|string',
        ]);

        // 🚫 double booking check
        $exists = Appointment::where('appointment_date', $validated['date'])
            ->where('appointment_time', $validated['time'])
            ->where('status', 'scheduled')
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'time' => 'This time slot is already booked. Please choose another time.',
            ])->withInput();
        }

        Appointment::create([
            'patient_id' => Auth::id(),
            'appointment_date' => $validated['date'],
            'appointment_time' => $validated['time'],
            'service' => $validated['service'],
        ]);

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment booked!');
    }

    public function showReschedule($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        return view('appointments.reschedule', compact('appointment'));
    }

    public function submitReschedule(Request $request, $id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        $appointment->appointment_date = $request->date;
        $appointment->appointment_time = $request->time;
        $appointment->save();

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment updated!');
    }

    public function cancel($id)
    {
        $appointment = Appointment::where('id', $id)
            ->where('patient_id', Auth::id())
            ->firstOrFail();

        $appointment->status = 'cancelled';
        $appointment->save();

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
