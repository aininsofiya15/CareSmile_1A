<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User; 
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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => Role::Patient, // ✅ Force every public registration to be a Patient
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect them to the Patient Dashboard
        return redirect(route('patient.dashboard', absolute: false));
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