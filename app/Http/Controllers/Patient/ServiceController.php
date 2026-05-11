<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        // Only authenticated patients can access
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    /**
     * Display a listing of available dental services for patients.
     */
    public function index(Request $request)
    {
        // Get only active services for patients (is_active = true)
        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->paginate(12);
        
        return view('patient.services.index', compact('services'));
    }
}