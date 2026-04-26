<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Support\Facades\Auth; 

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
        /** @var \App\Models\User $user */
            $user = Auth::user(); 
                if (!$user || !$user->isAdmin()) {
                    abort(403, 'Unauthorized access. Admin only.');
                }
                return $next($request);
        });
    }

    public function index()
    {
        $services = Service::paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'description' => 'required|string|min:10',
            'price' => 'required|numeric|min:0|max:9999.99',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'is_active' => 'sometimes|boolean'
        ]);

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Dental service added successfully!');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'description' => 'required|string|min:10',
            'price' => 'required|numeric|min:0|max:9999.99',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'is_active' => 'sometimes|boolean'
        ]);

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Dental service updated successfully!');
    }

    public function destroy(Service $service)
    {
        // REMOVE the appointment check since Appointment model doesn't exist yet
        // Just delete the service directly
        
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Dental service deleted successfully!');
    }
}