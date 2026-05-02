<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            // Ensure we pass the profile data to the view
            'profile' => $request->user()->patientProfile ?? (object)[] 
        ]);
    }

    /**
     * UPDATE PATIENT PROFILE (Scenario 3: Medical Data Integrity)
     */
    public function updateProfile(Request $request)
        {
            $user = $request->user();

            // TACTIC: RESTRICT OPTIONS (Data Integrity)
            $validated = $request->validate([
                // Enforces the format: 01X-XXXXXXX
                'phone_number' => ['required', 'regex:/^01[0-9]-[0-9]{7,8}$/'],
                'dob' => ['required', 'date', 'before:today'],
                'address' => ['required', 'string', 'max:500'],
                
                // Matches your 100-character scenario exactly
                'allergies' => ['nullable', 'string', 'max:100'],
                
                'medications' => ['nullable', 'string', 'max:1000'],
                'emergency_contact_name' => ['required', 'string', 'max:255'],
                
                // Also enforces dash for emergency contact
                'emergency_contact_phone' => ['required', 'regex:/^01[0-9]-[0-9]{7,8}$/'],
            ], [
                // Custom Error Messages for your demo
                'phone_number.regex' => 'The phone number must include a dash (e.g., 012-3456789).',
                'emergency_contact_phone.regex' => 'The emergency contact phone must include a dash.',
                'allergies.max' => 'Allergy information is restricted to 100 characters for data integrity.'
            ]);

            // Logic to update the profile table
            // Assumes your User model has a hasOne relationship called 'patientProfile'
            $user->patientProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $validated
            );

            return back()->with('success', 'Profile updated successfully!');
        }

    /**
     * Update the user's password (Security Tactic)
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Delete the user's account (Tactic: Revoke Access)
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();

        // TACTIC: TERMINATE SESSION
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}