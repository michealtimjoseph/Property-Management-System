<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ClientViewingsController extends Controller
{
public function index()
{
    $user = auth()->user();
    // Fetch viewings with property and staff details
    $viewings = DB::table('viewing as v')
        ->join('property as p', 'v.propertyno', '=', 'p.propertyno')
        ->leftJoin('staff as s', 'v.staffno', '=', 's.staffno')
        ->where('v.renterno', $user->renterno)
        ->select('v.*', 'p.street', 'p.city', 's.firstname as staff_fname', 's.lastname as staff_lname')
        ->orderBy('v.view_date', 'desc')
        ->get();

    return view('viewings', compact('viewings'));
}
public function store(Request $request)
{
    $request->validate([
        'propertyno'     => 'required|exists:property,propertyno',
        'view_date'      => 'required|date|after:today',
        'comment'        => 'nullable|string',
        'contact_no'     => 'required|string',
        'preferred_time' => 'nullable|string',
    ]);

    $user = Auth::user();
    $renterNo = $user->renterno;

    // 1. Handle Guest to Renter Conversion
    if (!$renterNo) {
        $validBranch = DB::table('branch')->select('branchno')->first();
        $branchId = $validBranch ? $validBranch->branchno : 'B001';

        $lastRenter = DB::table('renter')->latest('renterno')->first();
        $nextRNum = $lastRenter ? (int) filter_var($lastRenter->renterno, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
        $renterNo = 'R' . str_pad($nextRNum, 3, '0', STR_PAD_LEFT);

        DB::table('renter')->insert([
            'renterno'                => $renterNo,
            'firstname'               => explode(' ', $user->name)[0],
            'lastname'                => explode(' ', $user->name)[1] ?? '',
            'address'                 => 'Not Provided',
            'phone'                   => $request->contact_no,
            'preferred_property_type' => 'Flat',
            'max_rent'                => 0,
            'comment'                 => 'Auto-generated from viewing request',
            'branchno'                => $branchId,
        ]);

        DB::table('users')->where('id', $user->id)->update(['renterno' => $renterNo]);
    }

    // 2. Generate Viewing ID — MUST be here, after renter block, before insert
    $lastViewing = DB::table('viewing')->orderBy('viewingid', 'desc')->first();
    $nextVNum = $lastViewing ? (int) filter_var($lastViewing->viewingid, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
    $viewingId = 'V' . str_pad($nextVNum, 3, '0', STR_PAD_LEFT);

    // 3. Insert Viewing
    DB::table('viewing')->insert([
        'viewingid'  => $viewingId,
        'renterno'   => $renterNo,
        'propertyno' => $request->propertyno,
        'view_date'  => $request->view_date,
        'comment'    => trim(($request->preferred_time ? 'Preferred time: ' . $request->preferred_time . '. ' : '') . ($request->comment ?? '')),
        'staffno'    => null,
        'status'     => 'Pending',
    ]);

    return redirect()->route('home')->with('success', 'Viewing request sent!');
}
}