<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ViewingsController extends Controller
{
    /**
     * Display separated viewings: Active Schedule vs Completion History.
     */
public function index(Request $request)
    {
        DB::table('viewing')
        ->where('view_date', '<', Carbon::now()->toDateString())
        ->where('status', '!=', 'Closed')
        ->update(['status' => 'Closed']);

        $search = $request->input('search');

        // 1. ACTIVE VIEWINGS QUERY
        $activeQuery = DB::table('viewing as v')
            ->join('property as p', 'v.propertyno', '=', 'p.propertyno')
            ->join('renter as r', 'v.renterno', '=', 'r.renterno')
            ->leftJoin('staff as s', 'v.staffno', '=', 's.staffno')
            ->whereNotNull('v.staffno') 
            ->where('v.status', '!=', 'Completed');

        // Apply Search Filter
        if ($search) {
            $activeQuery->where(function($q) use ($search) {
                $q->where('p.street', 'ILIKE', "%{$search}%")
                  ->orWhere('p.city', 'ILIKE', "%{$search}%")
                  ->orWhere('r.firstname', 'ILIKE', "%{$search}%")
                  ->orWhere('r.lastname', 'ILIKE', "%{$search}%")
                  ->orWhere('s.firstname', 'ILIKE', "%{$search}%");
            });
        }

        $activeViewings = $activeQuery->select(
            'v.*', 'v.viewingid as id', 'v.view_date as date',
            'p.street as title', DB::raw("CONCAT(p.street, ', ', p.city) as addr"),
            's.firstname as staff_name', 'r.firstname as r_fname', 'r.lastname as r_lname'
        )->orderBy('v.view_date', 'asc')->get();


        // 2. COMPLETED VIEWINGS QUERY
        $completedQuery = DB::table('viewing as v')
            ->join('property as p', 'v.propertyno', '=', 'p.propertyno')
            ->join('renter as r', 'v.renterno', '=', 'r.renterno')
            ->leftJoin('staff as s', 'v.staffno', '=', 's.staffno')
            ->where('v.status', '=', 'Completed');

        if ($search) {
            $completedQuery->where(function($q) use ($search) {
                $q->where('p.street', 'ILIKE', "%{$search}%")
                  ->orWhere('p.city', 'ILIKE', "%{$search}%")
                  ->orWhere('r.firstname', 'ILIKE', "%{$search}%")
                  ->orWhere('r.lastname', 'ILIKE', "%{$search}%")
                  ->orWhere('s.firstname', 'ILIKE', "%{$search}%");
            });
        }

        $completedViewings = $completedQuery->select(
            'v.*', 'v.viewingid as id', 'v.view_date as date',
            'p.street as title', DB::raw("CONCAT(p.street, ', ', p.city) as addr"),
            's.firstname as staff_name', 'r.firstname as r_fname', 'r.lastname as r_lname'
        )->orderBy('v.view_date', 'desc')->get();


        // 3. SIDE PANEL: Pending Requests
        $requests = DB::table('viewing as v')
            ->join('property as p', 'v.propertyno', '=', 'p.propertyno')
            ->join('renter as r', 'v.renterno', '=', 'r.renterno')
            ->whereNull('v.staffno')
            ->select('v.viewingid as id', 'p.street as title', 'r.firstname', 'r.lastname', 'v.view_date')
            ->get();

        $timeline = $activeViewings->groupBy(function($item) {
            return Carbon::parse($item->date)->format('M d, Y');
        });

        return view('staff.viewings', compact('activeViewings', 'completedViewings', 'requests', 'timeline'));
    }    /**
     * Mark viewing as Completed with staff feedback.
     */
    public function updateFeedback(Request $request)
    {
        $request->validate([
            'viewingid' => 'required|exists:viewing,viewingid',
            'comment'   => 'required|string|max:1000',
        ]);

        DB::table('viewing')
            ->where('viewingid', $request->viewingid)
            ->update([
                'comment' => $request->comment,
                'status'  => 'Completed'
            ]);

        return back()->with('success', 'Viewing feedback recorded and marked as Completed.');
    }

    /**
     * Create viewing form (Prefilled for requests).
     */
public function create(Request $request)
    {
        $properties = DB::table('property')->get();
        $renters = DB::table('renter')->get();
        $staffList = DB::table('staff')->get();
        $users = DB::table('users')->get();

        $prefilledRequest = null;
        if ($request->has('request_id')) {
            $prefilledRequest = DB::table('viewing')
                ->where('viewingid', $request->request_id)
                ->first();
        }

        if (!$prefilledRequest) {
            $lastViewing = DB::table('viewing')->orderBy('viewingid', 'desc')->first();
            $nextId = $lastViewing ? (int) filter_var($lastViewing->viewingid, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
            $autoViewingId = 'V' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        } else {
            $autoViewingId = trim($prefilledRequest->viewingid);
        }

        return view('staff.viewings.create', compact('properties', 'renters', 'staffList', 'autoViewingId', 'prefilledRequest', 'users'));
    }
    /**
     * Save or finalize viewing record.
     */
public function store(Request $request)
    {
        $request->validate([
            'viewingid'  => 'required|string',
            'propertyno' => 'required|string|exists:property,propertyno',
            'renterno'   => 'required|string',
            'view_date'  => 'required|date',
            'staffno'    => 'nullable|string|exists:staff,staffno'
        ]);

        DB::table('viewing')->updateOrInsert(
            ['viewingid' => trim($request->viewingid)],
            [
                'propertyno' => trim($request->propertyno),
                'renterno'   => trim($request->renterno),
                'staffno'    => $request->staffno ? trim($request->staffno) : null,
                'view_date'  => $request->view_date,
                'comment'    => $request->comment,
                'status'     => $request->staffno ? 'Confirmed' : 'Pending' 
            ]
        );

        return redirect()->route('staff.viewings')->with('success', 'Viewing finalized successfully!');
    }
}