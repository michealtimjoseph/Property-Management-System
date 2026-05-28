<?php


namespace App\Http\Controllers;


use App\Models\Inspection;
use App\Models\Properties;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function index()
    {
        // Fetch data for the modal dropdowns
        $properties = Properties::select('propertyno', 'street', 'city')->get();
        $staffMembers = Staff::select('staffno', 'firstname', 'lastname')->get();
        
        // Fetch existing inspections
        $inspections = Inspection::with(['property', 'staff'])
    ->orderByDesc('inspection_date')
    ->get();
        // CHANGED: Pointing to 'inspection' instead of 'inspections.index'
        return view('staff.inspections', compact('properties', 'staffMembers', 'inspections'));
    }

    public function store(Request $request)
{
    // 1. Corrected Server-side validation
    $request->validate([
        // exists:table_name,column_name
        'property_id'    => 'required|exists:property,propertyno', 
        'staff_id'       => 'required|exists:staff,staffno',
        'scheduled_date' => 'required|date|after_or_equal:today',
    ]);

    // 2. Generate a unique ID (e.g., INSP-4829)
    // You can adjust this format if your company uses a different ID style
    $newInspectionId = 'INSP-' . rand(1000, 9999);

    // 3. Save to database using your exact column names
    Inspection::create([
        'inspectionid'    => $newInspectionId,
        'propertyno'      => $request->property_id,
        'staffno'         => $request->staff_id,
        'inspection_date' => $request->scheduled_date,
        'evaluation'      => 'Pending review.', // Default text until they actually do the review
    ]);

    return back()->with('success', 'Review scheduled successfully!');
}
}