<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FuelManagerController;
use App\Http\Controllers\GapcoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\UserRequestController;
use App\Models\SystemUser;
use Illuminate\Support\Facades\Route;

Route::get("/",[LoginController::class,"showLogin"])->name('login1');
Route::post("/login",[LoginController::class,"login"])->name("login");
Route::get("/dashboard",[AdminController::class,"dashboard"])->name("dashboard");
Route::resource('stations', StationController::class);
Route::resource('fuelManagers', FuelManagerController::class);
Route::resource('gapcos', GapcoController::class);
Route::resource('users', SystemUserController::class);
Route::resource('userRequest', UserRequestController::class);
Route::resource('payments', PaymentController::class);
Route::put('/payments/verify/{id}', [PaymentController::class, 'verify'])->name('payments.verify');
Route::get('/vouchers', [SystemUserController::class, 'index'])->name('vouchers.index');
Route::get('/vouchers', [SystemUserController::class, 'show'])->name('vouchers.show');
Route::get('/users_all',[SystemUserController::class,"index1"])->name("index1");
Route::post('/vouchers/generate', [SystemUserController::class, 'generate'])
    ->name('vouchers.generate');
Route::get('/generated',[SystemUserController::class,"generated"])->name('generated');
Route::get('/verify',[SystemUserController::class,"showverify"])->name('verify');
Route::post('/voucher/verify', [SystemUserController::class, 'verifyVoucher'])->name('voucher.verify');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/expired',[SystemUserController::class,"expired"])->name("expired");
Route::put('/request/toggle-status/{id}', [UserRequestController::class, 'toggleStatus'])
    ->name('request.toggleStatus');
Route::get("/forgot",[AdminController::class,"forgot"])->name("forgot");
Route::post('/forgot-password', [AdminController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AdminController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AdminController::class, 'updatePassword'])->name('password.update');
Route::get('/users/{id}/toggle-status',
    [SystemUserController::class,'toggleStatus'])
    ->name('users.toggle-status');
Route::delete("/delete_notes",[AdminController::class,"delete_all"])->name("delete_all");
Route::get('/vouchers/{id}', [AdminController::class, 'show'])
    ->name('vouchers.show1');


Route::get('/reports/payments', [PaymentController::class, 'index2'])
    ->name('reports.payments');
Route::get('/reports/fuel-report',[UserRequestController::class,'fuelReport'])
    ->name('reports.fuel');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    
    // Update Profile Information
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
    Route::post('/voucher/search', [SystemUserController::class, 'searchVoucher'])
    ->name('voucher.search');
    // Update Password
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('password.update1');
