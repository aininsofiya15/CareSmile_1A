<?php

namespace App\Http\Controllers;

// THESE ARE THE "MISSING LINKS" - MAKE SURE ALL 4 ARE HERE!
use App\Enums\Role;
use App\Models\PatientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function dashboard()
    {
        return view('patient.dashboard');
    }

    public function profile()
    {
        $user = Auth::user();

        // SQA Tip: We use the value of the Enum to match the database string
        if ($user->role->value === Role::Patient->value) {
            $profile = $user->patientProfile ?? new PatientProfile();
            return view('patient.patientprofile', compact('user', 'profile'));
        }

        // Fallback if they aren't a patient
        return abort(403, 'Unauthorized action.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'phone_number' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'address' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
        ]);

        // This is the magic line that saves the data
        $user->patientProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}