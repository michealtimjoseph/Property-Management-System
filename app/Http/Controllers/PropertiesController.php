<?php

namespace App\Http\Controllers;

use App\Models\Properties;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $branches = DB::table('branch')->select('branchno', 'city')->get();

        $selectedBranch = $request->input('branchno');
        $search = $request->input('search'); 

        $properties = DB::table(DB::raw("get_properties_by_branch(
                CAST(:branch AS TEXT), 
                CAST(:search AS TEXT)
            ) as property_data"))
            ->setBindings([
                'branch' => $selectedBranch ?: null,
                'search' => $search ?: null
            ])
            ->paginate(10)
            ->withQueryString();

         return view('staff.properties.properties', compact('properties', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = DB::table('branch')->select('branchno', 'city')->get();
        $staffs = DB::table('staff')->select('staffno', 'firstname', 'lastname')->get();
        $owners = DB::table('property_owner')->select('ownerid', 'firstname', 'lastname')->get();

        $lastProperty = DB::table('property')->latest('propertyno')->first();
        $nextId = $lastProperty ? (int) filter_var($lastProperty->propertyno, FILTER_SANITIZE_NUMBER_INT) + 1 : 1;
        $autoPropertyNo = 'PG' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('staff.properties.create', compact('branches', 'autoPropertyNo', 'staffs', 'owners'));
    }

    /**
     * Store a newly created resource in storage.
     */
        public function store(Request $request)
        {
            $request->validate([
                'propertyno'    => 'required|string|unique:property,propertyno',
                'branchno'      => 'required|string',
                'staffno'       => 'required|string',
                'main_image'    => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', 
                'street'        => 'required|string',
                'area'          => 'required|string',
                'city'          => 'required|string',
                'property_type' => 'required|string',
                'no_of_rooms'   => 'required|integer|min:1',
                'monthly_rate'  => 'required|numeric',
                'postcode'      => 'required|string',
                'ownerno'       => 'required|string',
            ]);

            $imagePath = null;
            if ($request->hasFile('main_image')) {
                $imagePath = $request->file('main_image')->store('images', 'public');
            }

            DB::statement("CALL insert_property(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $request->propertyno,
                $request->street,
                $request->area,
                $request->city,
                $request->postcode,
                $request->property_type,
                (int)$request->no_of_rooms,
                (float)$request->monthly_rate,
                $request->staffno,
                $request->ownerno,
                $request->branchno,
                $imagePath 
            ]);

            return redirect()->route('staff.properties.properties')
                            ->with('success', 'Property ' . $request->propertyno . ' has been listed successfully!');
        }
    /**
     * Display the specified resource.
     */
    public function showProperty($id)
        {
            $property = DB::table(DB::raw("get_property_details(CAST(:id AS TEXT))"))
                ->setBindings(['id' => $id])
                ->first();

            if (!$property) {
                abort(404, 'Property not found.');
            }

            if (isset($property->angle_images)) {
                $property->angle_images = str_getcsv(trim($property->angle_images, '{}'));
            } else {
                $property->angle_images = [];
            }

            return view('staff.properties.show', compact('property'));
        }

    /**
     * Show the form for editing the specified resource.
     */
    public function editProperty($id)
        {
            $property = DB::table('property')->where('propertyno', $id)->first();

             if (!$property) {
                 abort(404, 'Property not found.');
             }

             

            $branches = DB::table('branch')->get();
            $staffs = DB::table('staff')->get();
            $owners = DB::table('property_owner')->get();

            if (!$property) abort(404);

            return view('staff.properties.edit', compact('property', 'branches', 'staffs', 'owners'));
        }

        public function update(Request $request, $id)
        {
            $request->validate([
                'street'        => 'required|string',
                'area'          => 'required|string',
                'city'          => 'required|string',
                'postcode'      => 'required|string', // Added
                'property_type' => 'required|string',
                'no_of_rooms'   => 'required|integer',
                'monthly_rate'  => 'required|numeric',
                'staffno'       => 'required|string', // Added
                'ownerno'       => 'required|string', // Added
                'branchno'      => 'required|string', // Added
                'main_image'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $imagePath = $request->old_image;

            if ($request->hasFile('main_image')) {
                $imagePath = $request->file('main_image')->store('images', 'public');
            }

            DB::statement("CALL update_property(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $id,
                $request->street,
                $request->area,
                $request->city,
                $request->postcode,
                $request->property_type,
                (int)$request->no_of_rooms,
                (float)$request->monthly_rate,
                $request->staffno,
                $request->ownerno,
                $request->branchno,
                $imagePath
            ]);

            return redirect()->route('staff.properties.properties')->with('success', 'Property updated!');
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Properties $properties)
    {
        //
    }
}
