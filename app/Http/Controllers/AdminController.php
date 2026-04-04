<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function profile()
    {
        $user = Auth::user(); // <--- Capital 'A' Auth
        return view('admin.profile', compact('user'));
    }
}
