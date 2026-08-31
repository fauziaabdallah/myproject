<?php

namespace App\Http\Controllers;

use App\Models\Gapco;
use App\Models\Notification;
use App\Models\Station;
use App\Models\SystemUser;
use App\Models\Voucher;
use App\Models\VoucherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SystemUserController extends Controller
{
       public function index(Request $request)
{
    $query = SystemUser::with(['organization', 'station']);

    // ADMIN
    if (Auth::guard('web')->user()->role == "admin") {

        $query->where('role', '!=', 'admin');

    }

    // MANAGER
    elseif (Auth::guard('web')->user()->role == "accountant") {

        $query->where(function ($q) {

            $q->where('role', 'manager')
              ->orWhere('role', 'driver');

        })
        ->where(
            'organization_id',
            Auth::guard('web')->user()->organization_id
        );
    }

    
    if ($request->filled('role')) {

        $query->where('role', $request->role);

    }

    // ORGANIZATION FILTER
    if ($request->filled('organization_id')) {

        $query->where(
            'organization_id',
            $request->organization_id
        );

    }

    // STATION FILTER
    if ($request->filled('station_id')) {

        $query->where(
            'station_id',
            $request->station_id
        );

    }

  
    if (
        !$request->filled('role') &&
        !$request->filled('organization_id') &&
        !$request->filled('station_id')
    ) {

        $users = collect();

    } else {

        $users = $query->latest()->get();

    }

    $organizations = Gapco::orderBy('company_name')->get();

    $stations = Station::orderBy('station_name')->get();
    $noteCount = Notification::where("read_by","admin")->count();
    $notes = Notification::where("read_by","admin")->get();
    return view(
        'organizationManager',
        compact(
            'users',
            'noteCount',
            'notes',
            'organizations',
            'stations'
        )
    );
}

    
    public function store(Request $request)
{
    $request->validate([
        'first_name'       => 'required',
        'last_name'        => 'required',
        'mobile'           => 'required|min:10',
        'email'            => 'required|email|unique:system_users,email',
        'password'         => 'required|min:4',
        'role'             => 'required',
        'organization_id'  => 'required_if:role,admin|nullable|exists:organizations,id',
    ]);

    $organizationId = Auth::guard('web')->user()->role == 'station_manager'
        ? Auth::guard('web')->user()->organization_id
        : $request->organization_id;

    SystemUser::create([
        'first_name'      => $request->first_name,
        'last_name'       => $request->last_name,
        'mobile'          => $request->mobile,
        'email'           => $request->email,
        'password'        => Hash::make($request->password),
        'role'            => $request->role,
        'station_id'      => $request->station_id,
        'organization_id' => $organizationId,
    ]);

    return back()->with('success', 'User created successfully');
}

    
    public function update(Request $request,$id)
{
    $user = SystemUser::findOrFail($id);

    $user->update([
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
        'email'      => $request->email,
        'mobile'      => $request->mobile,
        'role'       => $request->role,
    ]);

    return back()->with('success','User updated successfully');
}
    public function show()
{
    $users = SystemUser::where('role','!=','accountant')->where('organization_id',Auth::guard('web')->user()->organization_id)->get();
    $vouchers = Voucher::with(['request.user.organization'])
    ->whereHas('request.user', function($query){
        $query->where('organization_id', Auth::guard('web')->user()->organization_id);
    })
    ->latest()
    ->get();
    return view('voucher', compact('vouchers','users'));
}

    public function generate(Request $request)
{
    $request->validate([
        'driver_id' => 'required',
        'litres'    => 'required|min:1'
    ]);

    $litres = $request->litres;
    $amount = $litres * 3000; // 1 litre = 3000 TZS

    $voucher = Voucher::whereHas('request.user', function ($query) {
        $query->where(
            'organization_id',
            Auth::guard('web')->user()->organization_id
        );
    })
    ->where('status', 'unused')
    ->latest()
    ->first();

    if (!$voucher || $voucher->amount < $amount) {
        return back()->with('error', 'Your Vouchar has been finished please make another requtes');
    }

    try {

        DB::transaction(function () use ($voucher, $request, $amount, $litres) {

            $reference = 'VCH-' . strtoupper(Str::random(8));

            VoucherAssignment::create([
                'voucher_id'        => $voucher->id,
                'driver_id'         => $request->driver_id,
                'reference_number'  => $reference,
                'qr_code'           => 'QR-' . Str::random(10),
                'amount'            => $amount,
                'litres'            => $litres
            ]);

            $voucher->decrement('amount', $amount);

            if ($voucher->amount - $amount <= 0) {
                $voucher->update([
                    'status' => 'finished'
                ]);
            }
        });

        return back()->with('success', 'Voucher generated successfully');

    } catch (\Exception $e) {

        return back()->with('error', $e->getMessage());

    }
}
    public function generated(){
        $user = Auth::guard('web')->user()->id;
        $voucher = VoucherAssignment::where('status','pending')->where('driver_id',$user)->first();
        return view("generated",compact('voucher'));
    }
    public function expired()
{
    $user = Auth::guard('web')->user();

    if ($user->role == "accountant") {

        $voucher = VoucherAssignment::with([
                'voucher_verify.station',
                'driver.organization'
            ])
            ->where('status', 'expired')
            ->whereHas('driver', function ($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })
            ->latest()
            ->get();

    } else {

        $voucher = VoucherAssignment::with([
                'voucher_verify.station',
                'driver.organization'
            ])
            ->where('status', 'expired')
            ->where('driver_id', $user->id)
            ->latest()
            ->get();
    }

    return view('expired_vochar', compact('voucher'));
}
    public function searchVoucher(Request $request)
{
    $request->validate([
        'reference_number' => 'required|string'
    ]);

    $voucher = VoucherAssignment::with([
        'driver.organization',
        'voucher.request.user.organization'
    ])
    ->where('reference_number', $request->reference_number)
    ->first();

    if (!$voucher) {
        return response()->json([
            'success' => false,
            'message' => 'Voucher not found.'
        ], 404);
    }

    // Check if already used
    if ($voucher->status !== 'pending') {
        return response()->json([
            'success' => false,
            'message' => 'This voucher has already been used.'
        ], 422);
    }

    return response()->json([
        'success' => true,

        'voucher' => [
            'id' => $voucher->id,

            'driver' => $voucher->driver->first_name . ' ' .
                        $voucher->driver->last_name,

            'organization' =>
                $voucher->driver->organization->company_name ?? 'N/A',

            'voucher_code' =>
                $voucher->voucher->voucher_code ?? 'N/A',

            'amount' => number_format($voucher->amount),

            'fuel_litres' =>
                round($voucher->amount / 3000, 2),

            'reference_number' =>
                $voucher->reference_number,

            'status' =>
                strtoupper($voucher->status),

            'qr_code' =>
                QrCode::size(200)->generate($voucher->reference_number)
        ]
    ]);
}


public function verifyVoucher(Request $request)
{
    $request->validate([
        'reference_number' => 'required|string'
    ]);

    $user = Auth::guard('web')->user();

    $voucher = VoucherAssignment::where(
        'reference_number',
        $request->reference_number
    )->first();

    if (!$voucher) {
        return back()->with('error', 'Voucher not found.');
    }

    if ($voucher->status !== 'pending') {
        return back()->with(
            'error',
            'Voucher has already been used.'
        );
    }

    $voucher->update([
        'status' => 'expired',
        'verified_by' => $user->id,
    ]);

    return back()->with(
        'success',
        'Voucher verified successfully.'
    );
}

    public function showverify(){
        $vouchers = VoucherAssignment::with('driver.organization')
                ->where("status","expired")
                ->OrderBy('created_at','desc')
                ->get();
        return view("verify_voucher",compact('vouchers'));
    }


     public function index1()
    {  
        if(Auth::guard('web')->user()->role=="admin"){
        $users = SystemUser::with('organization')->latest()->where('role','!=','admin')->get();
        }elseif(Auth::guard('web')->user()->role=="accountant"){
        $users = SystemUser::with('organization')
    ->where(function($query){
        $query->where('role', 'manager')
              ->orWhere('role', 'driver');
    })
    ->where('organization_id', Auth::guard('web')->user()->organization_id)
    ->get();
        }
        $organizations = Gapco::orderBy('id')->get();
        $stations = Station::all();

        return view('testing', compact('users', 'organizations','stations'));
    }

    public function toggleStatus($id)
{
    $user = SystemUser::findOrFail($id);

    $user->status =
        $user->status == 'Active'
        ? 'blocked'
        : 'Active';

    $user->save();

    return back()->with(
        'success',
        'User status updated successfully'
    );
}
}
