<?php

namespace App\Http\Controllers;

use App\Models\Gapco;
use App\Models\Notification;
use App\Models\SystemUser;
use App\Models\Station;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Voucher;
use App\Models\VoucherAssignment;
use Illuminate\Support\Facades\Mail;

class UserRequestController extends Controller
{
    public function index()
{
    if(Auth::guard('web')->user()->role == "accountant"){

        $requests = UserRequest::with([
            'user.organization',
            'payment'
        ])
        ->where('requested_by', Auth::guard('web')->user()->id)
        ->latest()
        ->get();

    }
    elseif(Auth::guard('web')->user()->role == "admin"){
        $requests = UserRequest::with([
        'user.organization',
        'fuel_request',
        'payment'
    ])
    ->latest()
    ->get();

    }
    else{

        $requests = UserRequest::with([
        'user.organization',
        'fuel_request',
        'payment'
    ])
    ->where('organization_id', Auth::guard('web')->user()->organization_id)
    ->latest()
    ->get();
    }
    $organizations = Gapco::where("type","FUEL COMPANY")->get();

    $users = SystemUser::all();
    $noteCount = Notification::where("read_by","admin")->count();
    $notes = Notification::where("read_by","admin")->get();

    return view('userRequest', compact(
        'requests',
        'users',
        'noteCount',
        'notes',
        'organizations'
    ));
}

    
    public function store(Request $request)
    {
        
        $request->validate([
            'request_amount' => 'required',
            'number_of_litre' => 'required'
            
        ]);

        UserRequest::create([
            'request_amount' => $request->request_amount,
            'number_of_litre' => $request->number_of_litre,
            'requested_by'   => Auth::guard('web')->user()->id,
            'organization_id' => $request->organization_id,
            'status'         => 'pending',
        ]);
        Notification::create([
            "title" => "Customer request",
            "action" => "Customer ".Auth::guard('web')->user()->first_name."  ".Auth::guard('web')->user()->last_name." has made a request",
            "read_by" => "admin"
        ]);

        return back()->with('success', 'Request created successfully');
    }

   
    public function update(Request $request, $id)
    {
        $req = UserRequest::findOrFail($id);

        $req->update([
            'request_amount' => $request->request_amount,
            'status'         => $request->status,
        ]);

        return back()->with('success', 'Request updated successfully');
    }
    public function toggleStatus($id)
{
    $req = UserRequest::findOrFail($id);

    if ($req->status == 'approved') {

        $req->status = 'pending';

    } else {

        $req->status = 'approved';
        $user = SystemUser::find($req->requested_by);

        if ($user && $user->email) {

            Mail::raw(
                "Hello {$user->first_name}  {$user->last_name},

Your fuel request has been approved successfully.

Amount: " . number_format($req->request_amount) . " TZS
Number of Litres: {$req->number_of_litre}
Please make payment to the following number to get Vouchar:
Number: 0676434424
Name: Fauzia 
Thank you.",
                function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Fuel Request Approved');
                }
            );
        }
    }

    $req->save();

    return back()->with('success', 'Status updated successfully');
}


public function fuelReport(Request $request)
{
    // ============================================================
    // USER ALIYE-LOGIN
    // ============================================================
    $user = Auth::guard('web')->user();

    // ============================================================
    // DATES
    // ============================================================
    $startDate = $request->start_date;
    $endDate   = $request->end_date;


    // ============================================================
    // QUERY YA STATIONS
    // ============================================================
    $query = Station::with('organization');


    // ============================================================
    // USER ACCESS
    // ============================================================

    // ADMIN
    // Anaona stations zote
    if ($user->role == 'admin') {

        // Hakuna filter
    }

    // STATION MANAGER
    // Anaona stations zote za organization yake
    elseif ($user->role == 'station_manager') {

        $query->where(
            'organization_id',
            $user->organization_id
        );
    }

    // USERS WENGINE
    // Anaona stations za organization yake
    else {

        $query->where(
            'organization_id',
            $user->organization_id
        );
    }


    // ============================================================
    // DATE FILTER
    // ============================================================

    if ($startDate && $endDate) {

        $query->whereDate(
            'created_at',
            '>=',
            $startDate
        )->whereDate(
            'created_at',
            '<=',
            $endDate
        );

    } elseif ($startDate) {

        $query->whereDate(
            'created_at',
            '>=',
            $startDate
        );

    } elseif ($endDate) {

        $query->whereDate(
            'created_at',
            '<=',
            $endDate
        );
    }


    // ============================================================
    // GET STATIONS
    // ============================================================

    $stations = $query
        ->orderBy('created_at', 'desc')
        ->get();

    return view('report2', compact(
        'stations',
        'startDate',
        'endDate'
    ));
}











}
