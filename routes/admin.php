<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminTransactionController;

// Admin guest routes
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
});

// Admin authenticated routes
Route::middleware(['admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('companies', AdminCompanyController::class)->names('admin.companies');
    Route::resource('users', AdminUserController::class)->names('admin.users');
    Route::get('transactions', [AdminTransactionController::class, 'index'])->name('admin.transactions');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});
