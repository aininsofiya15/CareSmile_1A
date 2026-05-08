<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DentistController extends Controller
{
    public function dashboard()
    {
        $dentistId = \Illuminate\Support\Facades\Auth::id();

        // Set to 0 for now so the dashboard doesn't crash
        $todayCount = 0;
        $totalPatients = 0;
        $weekCount = 0;

        return view('dentist.dashboard', compact('todayCount', 'totalPatients', 'weekCount'));
    }
    public function profile()
    {
        $user = Auth::user();
        return view('dentist.dentist_profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string'],
            'specialization' => ['required', 'string', 'in:General Dentistry,Orthodontics,Periodontics,Pediatric Dentistry,Oral Surgery'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Professional profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        // 1. Validate the input
        $request->validate([
            'current_password' => ['required', 'current_password'], // Built-in Laravel check
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' looks for password_confirmation
        ]);

        // 2. Update the password in the database
        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        // 3. Return with success message
        return back()->with('success', 'Security credentials updated successfully!');
    }
}