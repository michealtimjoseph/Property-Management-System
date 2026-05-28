<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureStaffRole
{
    public function handle(Request $request, Closure $next, ...$roles)
{
    $user = Auth::guard('staff')->user();
    if (!$user || !in_array(strtolower($user->position), array_map('strtolower', $roles))) {
        return redirect()->route('staff.dashboard')->with('error', 'Unauthorized access.');
    }
    return $next($request);
}
}