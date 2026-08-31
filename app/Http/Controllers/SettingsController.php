<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Notification;

class SettingsController extends Controller
{
    
   
    public function index()
    {
        $noteCount = Notification::where("read_by","admin")->count();
        $notes = Notification::where("read_by","admin")->get();
        return view('settings',compact('notes','noteCount')); 
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('web')->user();

        // Validation ya data zinazoingizwa
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:20',
        ]);

        // Ku-update data za mtumiaji
        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'mobile'      => $request->phone,
        ]);

        return redirect()->route('settings')->with('success', 'User information updated success!');
    }

    /**
     * Kubadilisha Nenosiri (Password)
     */
    public function updatePassword(Request $request)
    {
        // Validation ya Password
        $request->validate([
            'current_password'          => 'required',
            'new_password'              => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'Tafadhali ingiza nenosiri la sasa.',
            'new_password.required'     => 'Tafadhali ingiza nenosiri jipya.',
            'new_password.confirmed'    => 'Nenosiri jipya na la kurudia hayafanani.',
        ]);

        $user = Auth::guard('web')->user();

        // Kuangalia kama Current Password ni sahihi
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('settings')->with('error', 'Nenosiri la sasa siyo sahihi! Jaribu tena.');
        }

        // Ku-update password mpya
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('settings')->with('success', 'Password changes success');
    }
}