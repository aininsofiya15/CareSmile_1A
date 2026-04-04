<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\PatientProfile; 
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        // 1. Save the role as a simple string, not an Enum object
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, 
        ]);

        // 2. Compare it using the Enum's string value (->value)
        if ($user->role === Role::Patient->value) {
            PatientProfile::create([
                'user_id' => $user->id,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended($this->redirectPath($user));
    }

    protected function redirectPath(User $user): string
    {
        // 3. Match the string against the Enum string values
        return match ($user->role) {
            Role::Admin->value => route('admin.dashboard'),
            Role::Dentist->value => route('dentist.dashboard'),
            Role::Patient->value => route('patient.dashboard'),
            default => route('home'), // Always good SQA practice to have a safe fallback!
        };
    }
}