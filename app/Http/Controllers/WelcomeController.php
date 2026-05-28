<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $category = $request->input('category');

        $properties = DB::table(DB::raw("get_properties_by_branch(
            CAST(:branch AS TEXT),
            CAST(:search AS TEXT)
        ) as property_data"))
        ->setBindings([
            'branch' => null,
            'search' => null,
        ])
        ->get();

        // Filter by category client-side since function doesn't support type filter
        if ($category) {
            $properties = $properties->filter(fn($p) => $p->property_type === $category)->values();
        }

        return view('welcome', compact('properties', 'category'));
    }
}