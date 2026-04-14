<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function create()
    {
        return view('appointments.create');
    }

    public function store(Request $request)
    {
        Appointment::create([
            'patient_id' => Auth::id(),
            'appointment_date' => $request->date,
            'appointment_time' => $request->time,
            'service' => $request->service,
        ]);

        return redirect()->route('appointments')->with('success', 'Appointment booked!');
    }

    public function index()
    {
        $appointments = Appointment::where('patient_id', Auth::id())->get();

        return view('appointments.index', compact('appointments'));
    }

    public function showReschedule($id)
    {
        $appointment = Appointment::findOrFail($id);

        return view('appointments.reschedule', compact('appointment'));
    }

    public function submitReschedule(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->appointment_date = $request->date;
        $appointment->appointment_time = $request->time;
        $appointment->reschedule_status = 'pending';

        $appointment->save();

        return redirect()->route('appointments')->with('success', 'Reschedule request submitted!');
    }

    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->status = 'cancelled';
        $appointment->save();

        return redirect()->route('appointments')->with('success', 'Appointment cancelled!');
    }
}
