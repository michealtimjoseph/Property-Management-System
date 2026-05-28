<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropertyListingRequestController extends Controller
{
    // ─────────────────────────────────────────
    //  CLIENT SIDE
    // ─────────────────────────────────────────

    /**
     * Show the client's own listing requests.
     */
    public function index()
    {
        $user     = Auth::user();
        $renterNo = $user->renterno;

        $requests = $renterNo
            ? DB::table('property_listing_request as plr')
                ->leftJoin('staff as s', 'plr.reviewed_by', '=', 's.staffno')
                ->where('plr.renterno', $renterNo)
                ->select(
                    'plr.*',
                    DB::raw("s.firstname || ' ' || s.lastname as reviewed_by_name")
                )
                ->orderByDesc('plr.created_at')
                ->get()
            : collect();

        return view('listing-requests', compact('requests'));
    }

    /**
     * Store a new property listing request from a client.
     */
    public function store(Request $request)
    {
        $request->validate([
            'street'        => 'required|string',
            'area'          => 'required|string',
            'city'          => 'required|string',
            'postcode'      => 'required|string',
            'property_type' => 'required|in:Flat,House',
            'no_of_rooms'   => 'required|integer|min:1|max:20',
            'monthly_rate'  => 'required|numeric|min:1',
            'main_image'    => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'message'       => 'nullable|string|max:500',
        ]);

        $user     = Auth::user();
        $renterNo = $user->renterno;

        // Auto-create renter record if missing (same pattern as other controllers)
        if (!$renterNo) {
            $validBranch = DB::table('branch')->select('branchno')->first();
            $branchId    = $validBranch ? $validBranch->branchno : 'B001';

            $lastRenter = DB::table('renter')->orderByDesc('renterno')->first();
            $nextNum    = $lastRenter
                ? (int) filter_var($lastRenter->renterno, FILTER_SANITIZE_NUMBER_INT) + 1
                : 1;
            $renterNo   = 'R' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

            DB::table('renter')->insert([
                'renterno'                => $renterNo,
                'firstname'               => explode(' ', $user->name)[0],
                'lastname'                => explode(' ', $user->name)[1] ?? '',
                'address'                 => 'Not Provided',
                'phone'                   => '',
                'preferred_property_type' => $request->property_type,
                'max_rent'                => 0,
                'comment'                 => 'Auto-generated from listing request',
                'branchno'                => $branchId,
            ]);

            DB::table('users')->where('id', $user->id)->update(['renterno' => $renterNo]);
        }

        // Generate request ID
        $last   = DB::table('property_listing_request')->orderByDesc('requestid')->first();
        $nextId = $last
            ? (int) filter_var($last->requestid, FILTER_SANITIZE_NUMBER_INT) + 1
            : 1;
        $reqId  = 'PLR' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        // Store image
        $imagePath = $request->file('main_image')->store('images', 'public');

        DB::table('property_listing_request')->insert([
            'requestid'     => $reqId,
            'renterno'      => $renterNo,
            'street'        => $request->street,
            'area'          => $request->area,
            'city'          => $request->city,
            'postcode'      => $request->postcode,
            'property_type' => $request->property_type,
            'no_of_rooms'   => (int) $request->no_of_rooms,
            'monthly_rate'  => (float) $request->monthly_rate,
            'main_image'    => $imagePath,
            'message'       => $request->message,
            'status'        => 'Pending',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('listing-requests')
            ->with('success', 'Your property listing request has been submitted! Our team will review it shortly.');
    }

    // ─────────────────────────────────────────
    //  STAFF SIDE
    // ─────────────────────────────────────────

    /**
     * Staff: list all property listing requests.
     */
    public function staffIndex(Request $request)
    {
        $query = DB::table('property_listing_request as plr')
            ->join('renter as r', 'plr.renterno', '=', 'r.renterno')
            ->leftJoin('staff as s', 'plr.reviewed_by', '=', 's.staffno');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plr.requestid', 'ILIKE', "%{$search}%")
                  ->orWhere('r.firstname',  'ILIKE', "%{$search}%")
                  ->orWhere('r.lastname',   'ILIKE', "%{$search}%")
                  ->orWhere('plr.street',   'ILIKE', "%{$search}%")
                  ->orWhere('plr.city',     'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('plr.status', $request->status);
        }

        $requests = $query->select(
                'plr.*',
                DB::raw("r.firstname || ' ' || r.lastname as renter_name"),
                'r.phone as renter_phone',
                DB::raw("s.firstname || ' ' || s.lastname as reviewed_by_name")
            )
            ->orderByDesc('plr.created_at')
            ->get();

        $counts = [
            'pending'  => DB::table('property_listing_request')->where('status', 'Pending')->count(),
            'approved' => DB::table('property_listing_request')->where('status', 'Approved')->count(),
            'rejected' => DB::table('property_listing_request')->where('status', 'Rejected')->count(),
        ];

        return view('staff.listing-requests.index', compact('requests', 'counts'));
    }

    /**
     * Staff: approve — mark approved and redirect to property create pre-filled.
     */
    public function approve($id)
    {
        $req = DB::table('property_listing_request')->where('requestid', $id)->first();

        if (!$req || $req->status !== 'Pending') {
            return back()->with('error', 'Request not found or already reviewed.');
        }

        $staffNo = Auth::guard('staff')->user()->staffno;

        DB::table('property_listing_request')->where('requestid', $id)->update([
            'status'      => 'Approved',
            'reviewed_by' => $staffNo,
            'reviewed_at' => now(),
            'updated_at'  => now(),
        ]);

        // Redirect to property creation pre-filled with the request data
        return redirect()->route('staff.properties.create', [
            'from_request' => $id,
            'street'       => $req->street,
            'area'         => $req->area,
            'city'         => $req->city,
            'postcode'     => $req->postcode,
            'property_type'=> $req->property_type,
            'no_of_rooms'  => $req->no_of_rooms,
            'monthly_rate' => $req->monthly_rate,
        ])->with('success', 'Request approved! Please complete the property details below.');
    }

    /**
     * Staff: reject a listing request.
     */
    public function reject(Request $request, $id)
    {
        $req = DB::table('property_listing_request')->where('requestid', $id)->first();

        if (!$req || $req->status !== 'Pending') {
            return back()->with('error', 'Request not found or already reviewed.');
        }

        DB::table('property_listing_request')->where('requestid', $id)->update([
            'status'      => 'Rejected',
            'reviewed_by' => Auth::guard('staff')->user()->staffno,
            'reviewed_at' => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'Listing request has been rejected.');
    }
}