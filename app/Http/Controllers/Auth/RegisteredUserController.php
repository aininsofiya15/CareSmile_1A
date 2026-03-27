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
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => Role::from($request->role),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended($this->redirectPath($user));
    }

    protected function redirectPath(User $user): string
    {
        return match ($user->role) {
            Role::Admin => route('admin.dashboard'),
            Role::Dentist => route('dentist.dashboard'),
            Role::Patient => route('patient.dashboard'),
        };
    }
}
