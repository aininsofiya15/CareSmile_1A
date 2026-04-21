<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

class DentistController extends Controller
{
    public function dashboard()
    {
        return view('dentist.dashboard');
    }

    
    public function profile()
    {
        $user = Auth::user(); // <--- Capital 'A' Auth
        return view('dentist.profile', compact('user'));
    }
}
