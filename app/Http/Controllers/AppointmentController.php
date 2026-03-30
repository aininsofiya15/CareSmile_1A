<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function create()
    {
        return view('appointments.create');
    }

    public function store(Request $request)
    {
        Appointment::create([
            'patient_id' => 1, // 暂时先 hardcode
            'appointment_date' => $request->date,
            'appointment_time' => $request->time,
            'service' => $request->service,
        ]);

        return redirect()->back()->with('success', 'Appointment booked!');
    }

    public function index()
    {
        $appointments = Appointment::all();

        return view('appointments.index', compact('appointments'));
    }
}
