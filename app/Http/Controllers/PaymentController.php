<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Gapco;
use App\Models\Payment;
use App\Models\SystemUser;
use App\Models\VoucherAssignment;
use App\Models\Station;
use App\Models\UserRequest;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['request.user', 'verifier'])->latest()->get();
        $requests = UserRequest::all();
        $users = SystemUser::all();
        $noteCount = Notification::where("read_by","admin")->count();
        $notes = Notification::where("read_by","admin")->get();

        return view('paymentverify', compact('payments', 'requests', 'users','noteCount','notes'));
    }

    public function index2(Request $request)
{
    $user = Auth::guard('web')->user();

    // Aina ya Ripoti Iliyochaguliwa (Default ni payments)
    $reportType = $request->input('report_type', 'payments');

    // Variable za matokeo
    $payments = collect();
    $organizations = collect();
    $stations = collect();
    $fuelAssignments = collect();

    $totalRevenue = 0;
    $totalTransactions = 0;
    $filtered = false;

    // Filter za tarehe
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // 1. RIPOTI YA PAYMENTS
    if ($reportType == 'payments') {
        $query = Payment::with([
            'request.user',
            'request.fuel_request',
            'verifier'
        ]);

        if ($startDate && $endDate) {
            $filtered = true;
            $query->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        }

        if ($user->role != 'admin') {
            $query->whereHas('request', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->get();
        $totalRevenue = $payments->where('status', 'confirmed')->sum('amount_paid');
        $totalTransactions = $payments->count();
    } 

    // 2. RIPOTI YA ORGANIZATIONS (MAKAMPUNI)
    elseif ($reportType == 'organizations') {
        $query = Gapco::query();

        if ($startDate && $endDate) {
            $filtered = true;
            $query->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        }

        $organizations = $query->orderBy('created_at', 'desc')->get();
    }

    // 3. RIPOTI YA STATIONS (VITUO VYA MAFUTA)
    elseif ($reportType == 'stations') {
        $query = Station::with('organization');

        if ($startDate && $endDate) {
            $filtered = true;
            $query->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        }

        if ($user->role != 'admin') {
            $query->where('organization_id', $user->organization_id);
        }

        $stations = $query->orderBy('created_at', 'desc')->get();
    }

    // 4. RIPOTI YA FUEL VOUCHERS (MADEREVA WALIOWEKA/WALIOPEWA MAFUTA)
    elseif ($reportType == 'driver_fuel') {
        $query = VoucherAssignment::with(['driver', 'voucher', 'voucher_verify']);

        if ($startDate && $endDate) {
            $filtered = true;
            $query->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        }

        if ($user->role != 'admin') {
            $query->whereHas('driver', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            });
        }

        $fuelAssignments = $query->orderBy('created_at', 'desc')->get();
        $totalRevenue = $fuelAssignments->sum('amount'); // Jumla ya kiasi cha mafuta yaliyogawiwa
        $totalTransactions = $fuelAssignments->count();
    }

    // Taarifa za Notification
    $noteCount = Notification::where("read_by", "admin")->count();
    $notes = Notification::where("read_by", "admin")->get();

    return view('report', compact(
        'reportType',
        'payments',
        'organizations',
        'stations',
        'fuelAssignments',
        'totalRevenue',
        'totalTransactions',
        'filtered',
        'noteCount',
        'notes'
    ));
}
    public function verify(Request $request, $id)
{
    $payment = Payment::findOrFail($id);

    $request->validate([
        'referrence_number' => 'required',
        'amount_paid'       => 'required',
    ]);

    $expectedAmount = $payment->request->request_amount;

    if ($request->amount_paid != $expectedAmount) {
        return back()->with('error', 'Amount does not match request amount!');
    }

    $payment->update([
        'referrence_number' => $request->referrence_number,
        'amount_paid'       => $request->amount_paid,
        'status'            => 'confirmed',
        'verified_by'       => Auth::guard('web')->user()->id,
    ]);
    $user = SystemUser::find($payment->request->requested_by);

    if ($user && $user->email) {

        Mail::raw(
            "Hello {$user->first_name} {$user->last_name},

Your payment has been verified successfully.

Reference Number: {$payment->referrence_number}
Amount Paid: " . number_format($payment->amount_paid) . " TZS

Your fuel Vouchar is now generated and ready for the next process.

Thank you.",
            function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Payment Verification Successful');
            }
        );
    }

    
    Voucher::create([
        'request_id'   => $payment->request_id,
        'voucher_code' => strtoupper(Str::random(10)),
        'qr_code'      => 'QR-' . Str::random(10), 
        'amount'       => $request->amount_paid,
        'status'       => 'unused',
        'expiry_date'  => now()->addDays(30),
    ]);

    return back()->with('success', 'Payment verified & voucher created');
}

    public function store(Request $request)
{
    $request->validate([
        'request_id' => 'required|exists:user_requests,id',
        'referrence_number' => 'required',
        'amount_paid' => 'required|numeric',
    ]);

    Payment::create([
        'request_id'        => $request->request_id,
        'referrence_number' => $request->referrence_number,
        'amount_paid'       => $request->amount_paid,
        'status'            => 'pending',
        'verified_by'       => Auth::guard('web')->user()->id, 
    ]);
    Notification::create([
        "title" => "Payment alert",
        "action" => "Customer ".Auth::guard('web')->user()->first_name." ".Auth::guard('web')->user()->last_name." has made payment",
        "read_by" => "admin"
    ]);

    return back()->with('success', 'Payment submitted successfully');
}
}
