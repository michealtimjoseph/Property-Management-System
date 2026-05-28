<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaseApplicationController extends Controller
{
    // ───────────────────────────────────────────
    //  RENTER SIDE
    // ───────────────────────────────────────────

    /**
     * Show the renter's own applications list.
     */
    public function index()
    {
        $user = Auth::user();

        $applications = DB::table('lease_application as la')
            ->join('property as p', 'la.propertyno', '=', 'p.propertyno')
            ->leftJoin('viewing as v', 'la.viewingid', '=', 'v.viewingid')
            ->leftJoin('staff as s', 'la.reviewed_by', '=', 's.staffno')
            ->where('la.renterno', $user->renterno)
            ->select(
                'la.*',
                'p.street', 'p.city', 'p.property_type', 'p.monthly_rate', 'p.main_image',
                'v.view_date',
                DB::raw("s.firstname || ' ' || s.lastname as reviewed_by_name")
            )
            ->orderByDesc('la.created_at')
            ->get();

        return view('applications', compact('applications'));
    }

    /**
     * Store a new lease application from a renter.
     */
    public function store(Request $request)
    {
        $request->validate([
            'propertyno'           => 'required|exists:property,propertyno',
            'preferred_start_date' => 'required|date|after:today',
            'message'              => 'nullable|string|max:500',
            'contact_no'           => 'required|string',
            'viewingid'            => 'nullable|exists:viewing,viewingid',
        ]);

        $user     = Auth::user();
        $renterNo = $user->renterno;

        // Auto-create renter record if this is a first-time user (same logic as ClientViewingsController)
        if (!$renterNo) {
            $validBranch = DB::table('branch')->select('branchno')->first();
            $branchId    = $validBranch ? $validBranch->branchno : 'B001';

            $lastRenter = DB::table('renter')->orderByDesc('renterno')->first();
            $nextRNum   = $lastRenter ? (int) filter_var($lastRenter->renterno, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
            $renterNo   = 'R' . str_pad($nextRNum, 3, '0', STR_PAD_LEFT);

            DB::table('renter')->insert([
                'renterno'                => $renterNo,
                'firstname'               => explode(' ', $user->name)[0],
                'lastname'                => explode(' ', $user->name)[1] ?? '',
                'address'                 => 'Not Provided',
                'phone'                   => $request->contact_no,
                'preferred_property_type' => 'Flat',
                'max_rent'                => 0,
                'comment'                 => 'Auto-generated from lease application',
                'branchno'                => $branchId,
            ]);

            DB::table('users')->where('id', $user->id)->update(['renterno' => $renterNo]);
        }

        // Prevent duplicate pending application for the same property
        $existing = DB::table('lease_application')
            ->where('renterno', $renterNo)
            ->where('propertyno', $request->propertyno)
            ->where('status', 'Pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have a pending application for this property.');
        }

        // Generate application ID
        $last   = DB::table('lease_application')->orderByDesc('applicationid')->first();
        $nextId = $last ? (int) filter_var($last->applicationid, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
        $appId  = 'LA' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        DB::table('lease_application')->insert([
            'applicationid'        => $appId,
            'renterno'             => $renterNo,
            'propertyno'           => $request->propertyno,
            'viewingid'            => $request->viewingid ?: null,
            'preferred_start_date' => $request->preferred_start_date,
            'message'              => $request->message,
            'status'               => 'Pending',
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        return redirect()->route('applications')->with('success', 'Your lease application has been submitted! We will review it shortly.');
    }

    // ───────────────────────────────────────────
    //  STAFF SIDE
    // ───────────────────────────────────────────

    /**
     * Staff: list all applications with filters.
     */
    public function staffIndex(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        // 1. Initialize the query builder properly
        $query = DB::table('lease_application as la')
            ->join('property as p', 'la.propertyno', '=', 'p.propertyno')
            ->leftJoin('renter as r', 'la.renterno', '=', 'r.renterno')
            ->leftJoin('staff as s', 'la.reviewed_by', '=', 's.staffno') // 👈 JOIN THE STAFF TABLE
            ->where('p.branchno', $staff->branchno); // Filter by staff branch

        // 2. Dynamic Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('la.applicationid', 'ILIKE', "%{$search}%")
                  ->orWhere('r.firstname', 'ILIKE', "%{$search}%")
                  ->orWhere('r.lastname', 'ILIKE', "%{$search}%")
                  ->orWhere('p.street', 'ILIKE', "%{$search}%");
            });
        }

        // 3. Dynamic Status Filter
        if ($request->filled('status')) {
            $query->where('la.status', $request->status);
        }

        // 4. Select everything your Blade view requires (including monthly_rate & reviewed_by_name)
        $applications = $query->select(
                'la.*',
                'p.street', 
                'p.city', 
                'p.property_type',
                'p.monthly_rate', // 👈 Grabs the property monthly rate
                DB::raw("r.firstname || ' ' || r.lastname as renter_name"),
                'r.phone as renter_phone',
                DB::raw("s.firstname || ' ' || s.lastname as reviewed_by_name") // 👈 Combines staff name
            )
            ->orderByDesc('la.created_at')
            ->get();
            
        // 5. Get application counts for indicators
        $counts = [
            'pending'  => DB::table('lease_application')->where('status', 'Pending')->count(),
            'approved' => DB::table('lease_application')->where('status', 'Approved')->count(),
            'rejected' => DB::table('lease_application')->where('status', 'Rejected')->count(),
        ];

        return view('staff.applications', compact('applications', 'counts'));
    }

    /**
     * Staff: Proceed to lease creation (Deferred Approval).
     */
    public function approve($id)
    {
        $app = DB::table('lease_application as la')
            ->join('property as p', 'la.propertyno', '=', 'p.propertyno')
            ->where('la.applicationid', $id)
            ->select('la.*', 'p.monthly_rate')
            ->first();

        if (!$app || $app->status !== 'Pending') {
            return back()->with('error', 'Application not found or already reviewed.');
        }

        // Redirect to lease creation WITHOUT approving the status in the DB yet
        return redirect()->route('staff.leases.create', [
            'renterno'   => $app->renterno,
            'propertyno' => $app->propertyno,
            'startdate'  => $app->preferred_start_date,
            'monthly_rent' => $app->monthly_rate, 
            'from_app'   => $id,
        ])->with('success', 'Application approved. Please complete the lease agreement below.');
    }

    /**
     * Staff: reject an application.
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'nullable|string|max:300']);

        DB::table('lease_application')->where('applicationid', $id)->update([
            'status'      => 'Rejected',
            'reviewed_by' => Auth::guard('staff')->user()->staffno,
            'reviewed_at' => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'Application has been rejected.');
    }
}