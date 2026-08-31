<?php

namespace App\Http\Controllers;

use App\Models\FuelManager;
use App\Models\Staff;
use App\Models\Station;
use App\Models\SystemUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class FuelManagerController extends Controller
{
    public function index()
    {
        if(Auth::guard('web')->check() && Auth::guard('web')->user()->role=="station_manager"){
        $staff = SystemUser::with('station')->latest()->where('role','attendant')->where('station_id',Auth::guard('web')->user()->station_id)->get();
        }
        elseif(Auth::guard('web')->check() && Auth::guard('web')->user()->role=="subadmin"){
        $staff = SystemUser::with('station')->latest()->where('role','station_manager')->where('organization_id',Auth::guard('web')->user()->organization_id)->get();

        }
        else{
        $staff = SystemUser::with('station')->latest()->where('role','station_manager')->get();
        }
        $stations = Station::all(); 
        return view('fuelmanagers', compact('staff', 'stations'));
    }
    
public function store(Request $request)
{
    $request->validate([
        'firstname' => 'required',
        'lastname'  => 'required',
        'mobile'    => 'required',
        'role'      => 'required',
        'email'     => 'required|email|unique:system_users,email',
        'password'  => 'required|min:4',
        'station_id' => 'nullable|exists:stations,id',
    ]);

    $user = Auth::guard('web')->user();

    /*
    |--------------------------------------------------------------------------
    | ORGANIZATION & STATION LOGIC
    |--------------------------------------------------------------------------
    */

    // Kama aliye-login ni SUBADMIN
    if ($user->role === 'subadmin') {

        $organizationId = $user->organization_id;
        $stationId      = $request->station_id;

    }

    // Kama aliye-login ni STATION MANAGER
    elseif ($user->role === 'station_manager') {

        $organizationId = $user->organization_id;
        $stationId      = $user->station_id;

    }

    // Kwa roles nyingine
    else {

        $organizationId = $request->organization_id;
        $stationId      = $request->station_id;

    }


    /*
    |--------------------------------------------------------------------------
    | CREATE USER
    |--------------------------------------------------------------------------
    */

    SystemUser::create([
        'first_name'      => $request->firstname,
        'last_name'       => $request->lastname,
        'mobile'          => $request->mobile,
        'email'           => $request->email,
        'role'            => $request->role,

        'organization_id' => $organizationId,
        'station_id'      => $stationId,

        'password'        => Hash::make($request->password),
    ]);


    return back()->with(
        'success',
        'Staff added successfully'
    );
}



   
    public function update(Request $request, $id)
    {
        $staff = FuelManager::findOrFail($id);

        $request->validate([
            'firstname'  => 'required',
            'lastname'   => 'required',
            'mobile'     => 'required',
            'email'      => 'required|email|unique:staff,email,' . $id,
            'station_id' => 'required|exists:stations,id',
        ]);

        $data = [
            'firstname'  => $request->firstname,
            'lastname'   => $request->lastname,
            'mobile'     => $request->mobile,
            'email'      => $request->email,
            'station_id' => $request->station_id,
        ];

       
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        return back()->with('success', 'Staff updated successfully');
    }
}