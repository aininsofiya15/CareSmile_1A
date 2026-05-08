<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Patient Dashboard Logic
     */
    public function dashboard()
    {
        $userId = Auth::id();

        // 1. Upcoming appointments (List)
        $upcomingAppointments = Appointment::where('patient_id', $userId)
            ->where('appointment_date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        // 2. Next immediate appointment
        $nextAppointment = $upcomingAppointments->first();

        // 3. Total completed visits
        $totalVisits = Appointment::where('patient_id', $userId)
            ->where('status', 'completed')
            ->count();

        return view('patient.dashboard', compact(
            'upcomingAppointments',
            'nextAppointment',
            'totalVisits'
        ));
    }
}