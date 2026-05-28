<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RenterController extends Controller
{
    public function index(Request $request)
{
    $viewType = $request->input('type', 'renters'); // Defaults to 'renters'
    $branches = DB::table('branch')->select('branchno', 'city')->get();
    $selectedBranch = $request->input('branchno');
    $search = $request->input('search');

    if ($viewType === 'owners') {
        $data = DB::table('property_owner')
            ->when($search, function ($query, $search) {
                $query->where('firstname', 'ILIKE', "%{$search}%")
                      ->orWhere('lastname', 'ILIKE', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();
    } else {
        // Original renter logic
        $data = DB::table(DB::raw("get_renters_by_branch(CAST(:branch AS TEXT), CAST(:search AS TEXT)) as renter_data"))
            ->setBindings(['branch' => $selectedBranch ?: null, 'search' => $search ?: null])
            ->paginate(10)
            ->withQueryString();
    }

    return view('staff.renters.index', compact('data', 'branches', 'selectedBranch', 'search', 'viewType'));
}

    public function create()
    {
        $branches = DB::table('branch')->select('branchno', 'city')->get();
        $staffs = DB::table('staff')->select('staffno', 'firstname', 'lastname')->get();

        $lastRenter = DB::table('renter')->latest('renterno')->first();
        $nextId = $lastRenter ? (int) filter_var($lastRenter->renterno, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
        $autoRenterNo = 'CR' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('staff.renters.create', compact('branches', 'staffs', 'autoRenterNo'));
    }

    public function show($id)
    {
        $renter = DB::table(DB::raw("get_renters_details(CAST(:id AS TEXT))"))
            ->setBindings(['id' => $id])
            ->first();

        if (!$renter) {
            abort(404, 'Renter not found.');
        }

        return view('staff.renters.show', compact('renter'));
    }

    public function store(Request $request)
        {
            $request->validate([
                'renterno'                => 'required|string|unique:renter,renterno',
                'firstname'               => 'required|string',
                'lastname'                => 'required|string',
                'address'                 => 'required|string',
                'phone'                   => 'required|string',
                'preferred_property_type' => 'required|string',
                'max_rent'                => 'required|numeric',
                'branchno'                => 'required|string',
                'witness_staffno'         => 'required|string',
            ]);

            DB::statement("CALL insert_renter(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $request->renterno,
                $request->firstname,
                $request->lastname,
                $request->address,
                $request->phone,
                $request->preferred_property_type,
                (float)$request->max_rent,
                $request->comment,
                $request->branchno,
                $request->witness_staffno
            ]);

            return redirect()->route('staff.renters.index')
                            ->with('success', 'Renter ' . $request->firstname . ' registered successfully!');
        }

    public function edit($id)
        {
            $renter = DB::table('renter')->where('renterno', $id)->first();

             if (!$renter) {
                 abort(404, 'Renter not found.');
             }

             

            $branches = DB::table('branch')->get();
            $staffs = DB::table('staff')->get();

            if (!$renter) abort(404);

            return view('staff.renters.edit', compact('renter', 'branches', 'staffs'));
        }
    
     public function update(Request $request, $id)
        {
            $request->validate([
                'firstname'        => 'required|string',
                'lastname'         => 'required|string',
                'address'          => 'required|string',
                'phone'            => 'required|string',
                'sex'              => 'required|string',
                'preferred_property_type' => 'required|string',
                'max_rent'         => 'required|numeric',
                'comment'          => 'nullable|string',
                'witness_staffno'       => 'required|string',
                'branchno'      => 'required|string', // Added
            ]);

            DB::statement("CALL update_renter(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $id,
                $request->firstname,
                $request->lastname,
                $request->address,
                $request->phone,
                $request->sex,
                $request->preferred_property_type,
                (float)$request->max_rent,
                $request->comment,
                $request->branchno,
                $request->witness_staffno
            ]);

            return redirect()->route('staff.renters.index')->with('success', 'Renter updated!');
        }
        public function history($id)
            {
                $renter = DB::table('renter')->where('renterno', $id)->first();

                if (!$renter) {
                    abort(404, 'Renter not found.');
                }

                $leases = DB::table(DB::raw("get_lease_history(CAST(:id AS TEXT)) as lease_data"))
                    ->setBindings(['id' => $id])
                    ->get();

                return view('staff.renters.leases', compact('renter', 'leases'));
        }
    

// Add these methods to App\Http\Controllers\RenterController.php

public function createOwner()
{
    $lastOwner = DB::table('property_owner')->latest('ownerid')->first();
    $nextId = $lastOwner ? (int) filter_var($lastOwner->ownerid, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
    $autoOwnerId = 'O' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

    return view('staff.owners.create', compact('autoOwnerId'));
}

public function storeOwner(Request $request)
{
    $request->validate([
        'ownerid'   => 'required|string|unique:property_owner,ownerid',
        'firstname' => 'required|string',
        'lastname'  => 'required|string',
        'address'   => 'required|string',
        'contact'   => 'required|string', // Changed from telno to contact
    ]);

    DB::table('property_owner')->insert([
        'ownerid'   => $request->ownerid,
        'firstname' => $request->firstname,
        'lastname'  => $request->lastname,
        'address'   => $request->address,
        'contact'   => $request->contact, // Changed from telno to contact
    ]);

    return redirect()->route('staff.renters.index', ['type' => 'owners'])
                     ->with('success', 'Owner registered successfully!');
}
public function editOwner($id)
{
    $owner = DB::table('property_owner')->where('ownerid', $id)->first();
    if (!$owner) abort(404);
    return view('staff.owners.edit', compact('owner'));
}

public function updateOwner(Request $request, $id)
{
    $request->validate([
        'firstname' => 'required|string',
        'lastname'  => 'required|string',
        'address'   => 'required|string',
        'contact'   => 'required|string', // Changed from telno to contact
    ]);

    DB::table('property_owner')->where('ownerid', $id)->update([
        'firstname' => $request->firstname,
        'lastname'  => $request->lastname,
        'address'   => $request->address,
        'contact'   => $request->contact, // Changed from telno to contact
    ]);

    return redirect()->route('staff.renters.index', ['type' => 'owners'])
                     ->with('success', 'Owner updated successfully!');
}
}