<?php

namespace App\Http\Controllers;


use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the patient's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Eager load the relationship
        $user->load('patientProfile');

        return view('patient.patientprofile', [
            'user' => $user,
            'profile' => $user->patientProfile // This fills the $profile variable in Blade
        ]);
    }

    /**
     * Update Patient Medical & Personal Profile
     * SQA Tactic: Data Integrity & Input Restriction
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'phone_number' => ['nullable', 'regex:/^01[0-9]-[0-9]{7,8}$/'],
            'dob' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'allergies' => ['nullable', 'string', 'max:100'],
            'medications' => ['nullable', 'string', 'max:1000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'regex:/^01[0-9]-[0-9]{7,8}$/'],
        ], [
            'phone_number.regex' => 'Please follow the format: 01X-XXXXXXX',
            'emergency_contact_phone.regex' => 'Emergency phone must follow the format: 01X-XXXXXXX',
        ]);

        $user->patientProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update Password (Security Tactic)
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            // Added 'different:current_password' to the end of the array
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed', 'different:current_password'],
        ], [
            // Added the custom error message
            'password.different' => 'Your new password must be different from your current password.'
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
    /**
     * Terminate Session & Delete Account
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        \Illuminate\Support\Facades\Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}