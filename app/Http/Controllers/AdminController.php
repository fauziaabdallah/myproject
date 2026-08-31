<?php

namespace App\Http\Controllers;

use App\Models\FuelManager;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Gapco;
use App\Models\Station;
use Illuminate\Support\Facades\Password;
use App\Models\SystemUser;
use App\Models\UserRequest;
use App\Models\Voucher;
use App\Models\VoucherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
{
    $user = Auth::guard('web')->user();
    $userOrgId = $user->organization_id;

    // -----------------------------------------------------------------
    // DATA ZA ACCOUNTANT (Graph ya Total Requests kwa Mwezi & Recent Requests)
    // -----------------------------------------------------------------
    $accountantGraphLabels = collect();
    $datasets = [];
    $accountantMonthNames = collect();
    $accountantTotals = collect();
    $recentRequests = collect();

    if ($user->role == 'accountant') {
    // 1. Kuchukua siku 7 zilizopita
    $last7Days = collect();
    for ($i = 6; $i >= 0; $i--) {
        $last7Days->push(now()->subDays($i)->format('Y-m-d'));
    }

    // 2. Kuchukua madereva wote wa Organization hii
    $drivers = SystemUser::where('role', 'driver')
        ->where('organization_id', $userOrgId)
        ->get();

    // 3. Kuchukua matumizi ya mafuta kwa LITA (Litres) kwa kila dereva
    $datasets = [];
    $colors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c', '#34495e'];
    $colorIndex = 0;

    foreach ($drivers as $driver) {
        $driverData = [];

        foreach ($last7Days as $date) {
            // Tunachukua jumla ya amount na kuigawa kwa 3000 kupata Lita (Litres)
            $dailyAmount = VoucherAssignment::where('driver_id', $driver->id)
                ->whereDate('created_at', $date)
                ->sum('amount');

            $dailyLitres = round($dailyAmount / 3000, 2); // Inazungusha decimal kuwa tarakimu 2

            $driverData[] = $dailyLitres;
        }

        $datasets[] = [
            'label' => $driver->first_name . ' ' . $driver->last_name,
            'data' => $driverData,
            'backgroundColor' => $colors[$colorIndex % count($colors)],
            'borderColor' => $colors[$colorIndex % count($colors)],
            'borderWidth' => 1
        ];

        $colorIndex++;
    }

    // Tarehe za kuonyeshwa kwenye X-Axis ya Graph
    $accountantGraphLabels = $last7Days->map(function($date) {
        return \Carbon\Carbon::parse($date)->format('d M');
    });

    // 4. Recent Vouchers (5 za Hivi Karibuni)
    $recentRequests = VoucherAssignment::with(['driver', 'voucher'])
        ->whereHas('driver', function ($q) use ($userOrgId) {
            $q->where('organization_id', $userOrgId);
        })
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
}

    // -----------------------------------------------------------------
    // DATA ZA ADMIN (Revenue Line Chart & Today Status Pie Chart)
    // -----------------------------------------------------------------
    $monthNames = collect();
    $totals = collect();
    $pending = 0;
    $approved = 0;

    if ($user->role == 'admin') {
        $monthlyRevenue = DB::table('payments')
            ->selectRaw('MONTH(created_at) as month, SUM(amount_paid) as total')
            ->where('status', 'confirmed')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthNames = $monthlyRevenue->pluck('month')->map(function($m) {
            return date("F", mktime(0, 0, 0, $m, 1));
        });
        $totals = $monthlyRevenue->pluck('total');

        $pending = UserRequest::where('status', 'pending')->whereDate('created_at', today())->count();
        $approved = UserRequest::where('status', 'approved')->whereDate('created_at', today())->count();
    }

    // --- CODE ZAKO ZINGINE ZINABAKI HAPA HAPA ---
    $total_manager = FuelManager::count();
    $total_station = Gapco::where('type','FUEL COMPANY')->count();
    $total_station1 = Station::where('organization_id', $userOrgId)->count();
    $total_customer = SystemUser::where("role", "accountant")->count();

    $query = Payment::where('status', 'confirmed')->whereDate('created_at', today());
    if ($user->role != 'admin') {
        $query->whereHas('request', function ($q) use ($userOrgId) {
            $q->where('organization_id', $userOrgId);
        });
    }
    $totalRevenue = $query->sum('amount_paid');

    $total_request = UserRequest::where("status", "pending")
        ->when($user->role != 'admin', fn($q) => $q->where('organization_id', $userOrgId))
        ->count();

    $data = DB::table('fuel_workers')
        ->join('stations', 'fuel_workers.station_id', '=', 'stations.id')
        ->select('stations.station_name', DB::raw('COUNT(fuel_workers.id) as total'))
        ->groupBy('stations.station_name')
        ->get();

    $stationNames = $data->pluck('station_name');
    $employeeCounts = $data->pluck('total');

    $total_account = SystemUser::where('role','accountant')->where('organization_id', $userOrgId)->count();
    $total_driver = SystemUser::where('role','driver')->where('organization_id', $userOrgId)->count();

    // Inajumuisha amount ya voucher zote za organization husika
$total_voucher_amount = Voucher::whereHas('request.user', function ($query) use ($userOrgId) {
    $query->where('organization_id', $userOrgId);
})->sum('amount');

// Inabadilisha kiasi kuwa Litres (1 Litre = 3000 TZS)
$remaining_litres = round($total_voucher_amount / 3000, 2);

    $tpending = UserRequest::where('status', 'pending')->where('requested_by', $user->id)->count();
    $attendant = SystemUser::where('role','attendant')->where('station_id', $user->station_id)->count();

    $count1 = VoucherAssignment::whereHas('voucher_verify', function ($query) use ($user) {
        $query->where('station_id', $user->station_id)->where('status','expired');
    })->count();

    $count2 = VoucherAssignment::where('status', 'pending')
        ->when($user->role == 'driver', fn($q) => $q->where('driver_id', $user->id))
        ->when($user->role != 'driver', fn($q) => $q->whereHas('driver', fn($sq) => $sq->where('organization_id', $userOrgId)))
        ->count();

    $count3 = VoucherAssignment::where('status', 'expired')
        ->when($user->role == 'driver', fn($q) => $q->where('driver_id', $user->id))
        ->when($user->role != 'driver', fn($q) => $q->whereHas('driver', fn($sq) => $sq->where('organization_id', $userOrgId)))
        ->count();

    $noteCount = Notification::where("read_by", "admin")->count();
    $notes = Notification::where("read_by", "admin")->get();

    return view("dashboard", compact(
        'accountantGraphLabels','datasets',
        'recentRequests', 'accountantMonthNames', 'accountantTotals', // <-- Za Accountant
        'noteCount','total_station1','notes','count3','count2','count1',
        'attendant','remaining_litres','total_account','total_driver','tpending',
        'total_manager','total_station','total_customer','totalRevenue',
        'total_request','pending','approved','stationNames','employeeCounts',
        'monthNames', 'totals'
    ));
}
     public function forgot(){
        return view("forgotpassword");
    }
     public function sendResetLink(Request $request)
{
    $request->validate([
        'email' => 'required|email'
    ]);

    $status = Password::broker('users')->sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with('success', 'Link sent successfully, check your email Inbox')
        : back()->withErrors(['email' => 'Email not found.']);
}
    public function showResetForm(Request $request, $token = null)
    {
        return view('resetpassword1', [
            'token' => $token,
            'email' => $request->query('email') 
        ]);
    }
    public function updatePassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:4|confirmed'
    ]);

    $status = Password::broker('users')->reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($teacher, $password) {
            $teacher->password = Hash::make($password);
            $teacher->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login1')->with('success', 'Password changed success!')
        : back()->withErrors(['email' => __($status)]);
}


    public function delete_all()
{
    Notification::where('read_by', 'admin')->delete();

    return back()->with('success', 'Notifications deleted successfully');
}


    public function show($id)
{
    $voucher = Voucher::with([
        'request.user.organization'
    ])->findOrFail($id);

    return view('vouchershow', compact('voucher'));
}
    
}
