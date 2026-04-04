<?php

namespace App\Http\Controllers;

class PatientController extends Controller
{
    public function dashboard()
    {
        return view('patient.dashboard');
    }

    public function profile()
    {
    
        return view('patient.patientprofile'); 
    }
}
