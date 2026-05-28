<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffLoginController extends Controller
{
    public function showLoginForm() {
        return view('auth.staff-login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('staff')->attempt($credentials)) {
            $request->session()->regenerate();
            
            $staff = Auth::guard('staff')->user();
            
            // Role-based redirection logic
            if (strtolower($staff->position) === 'regular') {
                return redirect()->route('staff.dashboard');
            }

            return redirect()->intended('/staff/dashboard');
        }

        return back()->withErrors([
            'email'=> 'The provided staff credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/staff/login');
    }
}