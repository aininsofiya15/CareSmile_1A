<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\PatientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();

        // 1️⃣ Upcoming appointments (list)
        $upcomingAppointments = Appointment::where('patient_id', $userId)
            ->where('appointment_date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        // 2️⃣ Next appointment (single)
        $nextAppointment = Appointment::where('patient_id', $userId)
            ->where('appointment_date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->first();

        // 3️⃣ Total visits (completed only)
        $totalVisits = Appointment::where('patient_id', $userId)
            ->where('status', 'completed')
            ->count();

        return view('patient.dashboard', compact(
            'upcomingAppointments',
            'nextAppointment',
            'totalVisits'
        ));
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

        $user->patientProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    // app/Http/Controllers/PatientController.php

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}
