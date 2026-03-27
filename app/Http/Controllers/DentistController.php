<?php

namespace App\Http\Controllers;

class DentistController extends Controller
{
    public function dashboard()
    {
        return view('dentist.dashboard');
    }
}
