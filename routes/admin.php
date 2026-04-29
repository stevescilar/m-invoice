<?php

use App\Http\Controllers\Admin\AdminAccessController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminTransactionController;
use Illuminate\Support\Facades\Route;

// ── Secret gate — visit this URL first to unlock the login page ──
// URL: /admin/access/msl-admin-9x7k2p  (change the token in AdminAccessController)
Route::get('/access/{token}', [AdminAccessController::class, 'gate'])->name('admin.gate');

// ── Admin Guest Routes (protected by gate middleware) ──
Route::middleware(['guest:admin', 'admin.gate'])->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    // OTP verification
    Route::get('/verify', [AdminAuthController::class, 'showOtp'])->name('admin.otp');
    Route::post('/verify', [AdminAuthController::class, 'verifyOtp'])->name('admin.otp.verify');
    Route::post('/verify/resend', [AdminAuthController::class, 'resendOtp'])->name('admin.otp.resend');
});

// ── Admin Auth Routes ──
Route::middleware('auth:admin')->prefix('/')->name('admin.')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [AdminAuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AdminAuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AdminAuthController::class, 'updatePassword'])->name('profile.password');

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Companies
    Route::get('/companies', [AdminCompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/{company}', [AdminCompanyController::class, 'show'])->name('companies.show');
    Route::post('/companies/{company}/bypass', [AdminCompanyController::class, 'toggleBypass'])->name('companies.bypass');
    Route::post('/companies/{company}/extend-trial', [AdminCompanyController::class, 'extendTrial'])->name('companies.extend-trial');
    Route::delete('/companies/{company}', [AdminCompanyController::class, 'destroy'])->name('companies.destroy');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle', [AdminUserController::class, 'toggleActive'])->name('users.toggle');

    // Transactions
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
});