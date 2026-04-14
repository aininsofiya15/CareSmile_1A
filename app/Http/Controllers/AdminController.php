<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    public function managePatients()
    {
        // Fetching only patients, ordered by newest first
        $patients = User::where('role', Role::Patient)->latest()->get();
        return view('admin.patient.listpatient', compact('patients'));
    }

    public function createPatient()
    {
        return view('admin.patient.createpatient');
    }

    public function storePatient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        // 1. Create the User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('CareSmile2026!'), 
            'role' => Role::Patient,
        ]);

        // 2. Initialize the Patient Profile 
        $user->patientProfile()->create([
            'user_id' => $user->id
        ]);

        event(new Registered($user));

        return redirect()->route('admin.patients')->with('success', 'New patient registered successfully!');
    }

    public function show(User $patient)
    {
        if ($patient->role !== Role::Patient) abort(403);

        $patient->load('patientProfile'); 

        return view('admin.patient.viewprofile', compact('patient'));
    }

    public function edit(User $patient)
    {
        if ($patient->role !== Role::Patient) abort(403);

        $patient->load('patientProfile'); 

        return view('admin.patient.viewprofile', compact('patient'));
    }

    public function update(Request $request, User $patient)
    {
        // 1. Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $patient->id,
            'phone_number' => 'nullable|string|max:20',
            'dob' => 'nullable|string', 
            'address' => 'nullable|string|max:500',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
        ]);

        // 2. Update the User table (Account info)
        $patient->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $patient->patientProfile()->updateOrCreate(
            ['user_id' => $patient->id],
            [
                'phone_number' => $request->phone_number === '-' ? null : $request->phone_number,
                'dob'          => $request->dob === '-' ? null : $request->dob,
                'address'      => $request->address === '-' ? null : $request->address,
                'allergies'    => $request->allergies === '-' ? null : $request->allergies,
                'medications'  => $request->medications === '-' ? null : $request->medications,
                'emergency_contact_name'  => $request->emergency_name === '-' ? null : $request->emergency_name,
                'emergency_contact_phone' => $request->emergency_phone === '-' ? null : $request->emergency_phone,
            ]
        );

        // 4. Redirect back with a success message
        return redirect()->route('admin.patients')->with('success', 'Patient record updated in database.');
    }

    public function destroy(User $patient) 
    { 
        // Security check: only allow deleting patients
        if ($patient->role !== Role::Patient) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Delete the related profile first 
        $patient->patientProfile()->delete();
        
        // Delete the user
        $patient->delete();

        return redirect()->route('admin.patients')->with('success', 'Patient record deleted successfully.'); 
    }

    public function manageDentists()
    {
        $dentists = User::where('role', Role::Dentist)->get();
        return view('admin.dentists.index', compact('dentists'));
    }

    // Show the 'Create' form
    public function createDentist()
    {
        return view('admin.dentists.create');
    }

    // Save the new dentist to database
    public function storeDentist(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => Role::Dentist,
        ]);

        return redirect()->route('admin.dentists')->with('success', 'Dentist account created!');

    }
}