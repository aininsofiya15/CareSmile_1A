<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is logged in
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Convert string role (e.g., "admin") to Enum
        try {
            $roleEnum = Role::from($role);
        } catch (\ValueError $e) {
            abort(403, 'Invalid role.');
        }

        // Check if user has the required role
        if (! $user->hasRole($roleEnum)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}