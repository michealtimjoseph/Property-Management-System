<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffProfileController extends Controller

{
    public function index(Request $request)
        {
            $branches = DB::table('branch')->select('branchno', 'city')->get();

            $branchDetails = DB::table('branch')
            ->where('branchno', $request->branchno)
            ->first();

            $selectedBranch = $request->input('branchno');
            $search = $request->input('search'); 

            $branchSupervisor = null;

            if ($request->filled('branchno')) {
            $branchSupervisor = DB::table('staff')
                ->where('branchno', $request->branchno)
                ->where('position', 'Supervisor') // Ensure this matches exactly how it's spelled in your DB
                ->first();
        }

            $staffs = DB::table(DB::raw("get_staff_by_branch(
                    CAST(:branch AS TEXT), 
                    CAST(:search AS TEXT)
                ) as staff_data"))
                ->setBindings([
                    'branch' => $selectedBranch ?: null,
                    'search' => $search ?: null
                ])
                ->paginate(10)
                ->withQueryString();

            return view('staff.staff', compact('staffs', 'branches', 'branchSupervisor', 'branchDetails'));
        }

    public function show($id)
        {
            $staff = DB::table(DB::raw("get_staff_details(CAST(:id AS TEXT))"))
                ->setBindings(['id' => $id])
                ->first();

            if (!$staff) {
                abort(404, 'Staff member not found.');
            }

            return view('staff.show', compact('staff'));
        }

public function edit($id = null)
{
    // If no ID is passed in the URL, use the authenticated staff's ID
    $staffId = $id ?: auth()->guard('staff')->id();

    $staff = DB::table(DB::raw("get_staff_details(CAST(:id AS TEXT))"))
        ->setBindings(['id' => $staffId])
        ->first();

    $branches = DB::table('branch')->select('branchno', 'city')->get();

    if (!$staff) {
        abort(404, 'Staff member not found.');
    }

    // Pass the staff data as 'user' to match your edit.blade.php variables
    return view('staff.edit', [
        'user' => $staff,
        'branches' => $branches,
        'staff' => $staff
    ]);
}
public function update(Request $request, $id)
{
    $request->validate([
        'firstname'     => 'required|string',
        'lastname'      => 'required|string',
        'address'       => 'required|string',
        'telephoneno'   => 'required|string',
        'sex'           => 'required|in:M,F',
        'date_of_birth' => 'required|date',
        'nin'           => 'required|string',
        'position'      => 'required|in:Secretary,Manager,Regular,Supervisor', 
        'salary'        => 'required|numeric',
        'branchno'      => 'required|string',
        'email'         => 'required|email',
    ]);

    if ($request->position === 'Supervisor') {
            $exists = DB::table('staff')
                ->where('branchno', $request->branchno)
                ->where('position', 'Supervisor')
                ->where('staffno', '!=', $id) // Exclude the current staff member
                ->exists();

            if ($exists) {
                return back()->withErrors(['position' => 'This branch already has a supervisor assigned.']);
            }
        }

    DB::statement("CALL update_staff_member(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
        $id,
        $request->firstname,
        $request->lastname,
        $request->address,
        $request->telephoneno,
        $request->sex,
        $request->date_of_birth,
        $request->nin,
        $request->position,
        $request->salary,
        $request->branchno,
        $request->email
    ]);

    return redirect()->route('staff.staff')->with('success', 'Staff updated successfully!');
}

    public function create()
    {
        $branches = DB::table('branch')->select('branchno', 'city')->get();

        $lastStaff = DB::table('staff')->latest('staffno')->first();
        $nextId = $lastStaff ? (int) filter_var($lastStaff->staffno, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
        $autoStaffNo = 'SL234' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('staff.create', compact('branches', 'autoStaffNo'));
    }

    public function store(Request $request)
        {
            $validated = $request->validate([
                'staffno'       => 'required',
                'firstname'     => 'required|string',
                'lastname'      => 'required|string',
                'address'       => 'required|string',
                'telephoneno'   => 'required|string',
                'sex'           => 'required|in:M,F',
                'date_of_birth' => 'required|date',
                'nin'           => 'required|string',
                'position'      => 'required|in:Secretary,Manager,Regular,Supervisor', 
                'salary'        => 'required|numeric',
                'branchno'      => 'required|string',
                'email'         => 'required|email',
                'password'      => 'required|min:8',
            ]);

            $hashedPassword = bcrypt($request->password);

            if (strcasecmp($request->position, 'Supervisor') === 0) {
            $existingSupervisor = DB::table('staff')
                ->where('branchno', $request->branchno)
                ->where('position', 'Supervisor')
                ->exists();

            if ($existingSupervisor) {
                return back()
                    ->withInput()
                    ->withErrors(['position' => 'This branch already has an assigned Supervisor. You must reassign or remove the current supervisor first.']);
            }
        }

            DB::statement("CALL insert_staff_member(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $request->staffno,
                $request->firstname,
                $request->lastname,
                $request->address,
                $request->telephoneno,
                $request->sex,
                $request->date_of_birth,
                $request->nin,
                $request->position,
                $request->salary,
                $request->branchno,
                $hashedPassword,
                $request->email
            ]);

            return redirect()->route('staff.staff')->with('success', 'Staff added via Stored Procedure!');
        }
    
        // In App\Http\Controllers\StaffProfileController.php

public function createBranch()
{
    // Generate a new Branch ID (e.g., B005)
    $lastBranch = DB::table('branch')->latest('branchno')->first();
    $nextNum = $lastBranch ? (int) filter_var($lastBranch->branchno, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
    $autoBranchNo = 'B' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

    return view('staff.branches.create', compact('autoBranchNo'));
}

public function storeBranch(Request $request)
{
    $request->validate([
        'branchno' => 'required|string|unique:branch,branchno',
        'street'   => 'required|string',
        'area'     => 'required|string',
        'city'     => 'required|string',
        'postcode' => 'required|string',
        'phone'    => 'required|string',
        'faxno'    => 'nullable|string',
    ]);

    DB::table('branch')->insert([
        'branchno' => $request->branchno,
        'street'   => $request->street,
        'area'     => $request->area,
        'city'     => $request->city,
        'postcode' => $request->postcode,
        'phone'    => $request->phone,
        'faxno'    => $request->faxno,
    ]);

    return redirect()->route('staff.staff')->with('success', 'New branch added successfully!');
}
}
