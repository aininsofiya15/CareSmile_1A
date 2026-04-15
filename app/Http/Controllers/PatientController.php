<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function dashboard()
    {
        $upcomingAppointments = Appointment::where('patient_id', Auth::id())
            ->where('appointment_date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->get();

        return view('patient.dashboard', compact('upcomingAppointments'));
    }
}
