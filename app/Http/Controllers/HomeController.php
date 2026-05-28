<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = DB::table('property')
            ->whereNotIn('propertyno', function($subquery) {
                $subquery->select('propertyno')
                         ->from('lease_agreement')
                         ->where('enddate', '>=', now()->toDateString());
            });

        if ($request->filled('min_price')) {
            $query->where('monthly_rate', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('monthly_rate', '<=', $request->max_price);
        }
        if ($request->filled('rooms')) {
            $query->where('no_of_rooms', '>=', $request->rooms);
        }

       $properties = $query->get();
        $featured   = $properties->first();
        $rest       = $properties->skip(1)->values();

        $activeLeaseCount = 0;
        $viewingsCount    = 0;

        if ($user && $user->renterno) {
            $activeLeaseCount = DB::table('lease_agreement')
                ->where('renterno', $user->renterno)
                ->where('enddate', '>=', now()->toDateString())
                ->count();
            $viewingsCount = DB::table('viewing')->where('renterno', $user->renterno)->count();
        }

        $availableCount = $properties->count();

        return view('home', compact('properties', 'featured', 'rest', 'activeLeaseCount', 'viewingsCount', 'availableCount'));
    }
}