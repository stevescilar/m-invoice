<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Catalog\ServiceCategoryController;
use App\Http\Controllers\Catalog\CatalogItemController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Quotation\QuotationController;
use App\Http\Controllers\Expense\ExpenseController;
use App\Http\Controllers\Subscription\SubscriptionController;

// Root
Route::get('/', function () {
    return redirect()->route('login');
});

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Guest only routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Password Reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Email Verification (auth, no active.company needed)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email verified successfully!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent to ' . $request->user()->email);
    })->middleware('throttle:6,1')->name('verification.send');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Protected routes
Route::middleware(['auth', 'active.company'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Company
    Route::get('/company/setup', [CompanyController::class, 'setup'])->name('company.setup');
    Route::post('/company/setup', [CompanyController::class, 'storeSetup']);
    Route::get('/company/settings', [CompanyController::class, 'settings'])->name('company.settings');
    Route::put('/company/settings', [CompanyController::class, 'updateSettings']);

    // Clients
    Route::resource('clients', ClientController::class);

    // Catalog
    Route::resource('catalog/categories', ServiceCategoryController::class)->names('categories');
    Route::resource('catalog/items', CatalogItemController::class)->names('catalog-items');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::post('invoices/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoices.duplicate');

    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/send', [QuotationController::class, 'send'])->name('quotations.send');
    Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convertToInvoice'])->name('quotations.convert');
    Route::get('quotations/{quotation}/download', [QuotationController::class, 'download'])->name('quotations.download');

    // Expenses
    Route::resource('expenses', ExpenseController::class);

    // Subscription
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/pay', [SubscriptionController::class, 'initiatePay'])->name('subscription.pay');
    Route::post('/subscription/status', [SubscriptionController::class, 'checkStatus'])->name('subscription.status');
    Route::post('/subscription/bypass', [SubscriptionController::class, 'activateBypass'])->name('subscription.bypass');
});

// M-Pesa Callback (no auth — called by Safaricom)
Route::post('/api/mpesa/callback', [SubscriptionController::class, 'callback']);