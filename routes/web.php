<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\ItemTypeController;
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
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\PublicDocumentController;


// Root
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::post('/chatbot', [App\Http\Controllers\ChatbotController::class, 'chat'])->name('chatbot.chat');

// Guest only routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,1');

    // Password Reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Email Verification (auth, no active.company needed)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function (\Illuminate\Http\Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
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
    Route::post('/settings/password', [CompanyController::class, 'updatePassword'])->name('password.update.profile');

    Route::post('/invoices/{invoice}/recurring/pause',  [InvoiceController::class, 'pauseRecurring'])->name('invoices.recurring.pause');
    Route::post('/invoices/{invoice}/recurring/resume', [InvoiceController::class, 'resumeRecurring'])->name('invoices.recurring.resume');
    Route::post('/invoices/{invoice}/recurring/cancel', [InvoiceController::class, 'cancelRecurring'])->name('invoices.recurring.cancel');

    Route::post('/settings/item-types',              [ItemTypeController::class, 'store'])->name('item-types.store');
    Route::put('/settings/item-types/{itemType}',    [ItemTypeController::class, 'update'])->name('item-types.update');
    Route::post('/settings/item-types/{itemType}/toggle', [ItemTypeController::class, 'toggle'])->name('item-types.toggle');
    Route::delete('/settings/item-types/{itemType}', [ItemTypeController::class, 'destroy'])->name('item-types.destroy');
});

// Protected routes

Route::middleware(['auth'])->group(function () {
    Route::get('/company/setup', [CompanyController::class, 'setup'])->name('company.setup');
    Route::post('/company/setup', [CompanyController::class, 'storeSetup']);
});
Route::middleware(['auth', 'active.company'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Company
    // Route::get('/company/setup', [CompanyController::class, 'setup'])->name('company.setup');
    // Route::post('/company/setup', [CompanyController::class, 'storeSetup']);
    Route::get('/company/settings', [CompanyController::class, 'settings'])->name('company.settings');
    Route::put('/company/settings', [CompanyController::class, 'updateSettings']);

    // Clients
    Route::resource('clients', ClientController::class);
    Route::get('clients/{client}/statement/download', [ClientController::class, 'downloadStatement'])->name('clients.statement.download');
    Route::post('clients/{client}/statement/email', [ClientController::class, 'emailStatement'])->name('clients.statement.email');

    // Catalog
    Route::resource('catalog/categories', ServiceCategoryController::class)->names('categories');
    Route::resource('catalog/items', CatalogItemController::class)->names('catalog-items');

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'recordPayment'])->name('invoices.payments.store');
    Route::delete('invoices/{invoice}/payments/{payment}', [InvoiceController::class, 'deletePayment'])->name('invoices.payments.destroy');
    Route::get('invoices/{invoice}/receipt/{payment?}', [InvoiceController::class, 'downloadReceipt'])->name('invoices.receipt.download');
    Route::post('invoices/{invoice}/receipt/{payment?}/email', [InvoiceController::class, 'emailReceipt'])->name('invoices.receipt.email');
    Route::get('invoices/{invoice}/delivery-note', [InvoiceController::class, 'downloadDeliveryNote'])->name('invoices.delivery-note');
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::post('invoices/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoices.duplicate');

    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/send', [QuotationController::class, 'send'])->name('quotations.send');
    Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convertToInvoice'])->name('quotations.convert');
    Route::get('quotations/{quotation}/download', [QuotationController::class, 'download'])->name('quotations.download');
    Route::get('quotations/{quotation}/delivery-note', [QuotationController::class, 'downloadDeliveryNote'])->name('quotations.delivery-note');
    Route::post('quotations/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->name('quotations.duplicate');

    // Expenses
    Route::resource('expenses', ExpenseController::class);

    // Reports & KRA Tax Summary
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    // Subscription
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/pay', [SubscriptionController::class, 'initiatePay'])->name('subscription.pay');
    Route::post('/subscription/status', [SubscriptionController::class, 'checkStatus'])->name('subscription.status');
    Route::post('/subscription/bypass', [SubscriptionController::class, 'activateBypass'])->name('subscription.bypass');

    // Staff management (owner only)
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::post('/staff/invite', [StaffController::class, 'invite'])->name('staff.invite');
    Route::post('/staff/invitations/{invitation}/resend', [StaffController::class, 'resendInvitation'])->name('staff.invitations.resend');
    Route::delete('/staff/invitations/{invitation}', [StaffController::class, 'revokeInvitation'])->name('staff.invitations.revoke');
    Route::post('/staff/{user}/deactivate', [StaffController::class, 'deactivate'])->name('staff.deactivate');
    Route::delete('/staff/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');

    // Invoice reminders
    Route::post('invoices/{invoice}/remind', [InvoiceController::class, 'sendReminder'])->name('invoices.remind');
    Route::get('invoices/{invoice}/whatsapp', [InvoiceController::class, 'whatsappReminder'])->name('invoices.whatsapp');
    
    // Quotation reminders
    Route::post('quotations/{quotation}/remind', [QuotationController::class, 'sendReminder'])->name('quotations.remind');
    Route::get('quotations/{quotation}/whatsapp', [QuotationController::class, 'whatsappReminder'])->name('quotations.whatsapp');

});

// Public Shareable Client Portal (no auth required)
Route::prefix('p')->name('public.')->group(function () {
    Route::get('/invoice/{token}', [PublicDocumentController::class, 'showInvoice'])->name('invoice.show');
    Route::get('/invoice/{token}/pdf', [PublicDocumentController::class, 'downloadInvoicePdf'])->name('invoice.pdf');
    Route::get('/invoice/{token}/receipt', [PublicDocumentController::class, 'downloadInvoiceReceipt'])->name('invoice.receipt');
    Route::post('/invoice/{token}/notify-payment', [PublicDocumentController::class, 'notifyInvoicePayment'])->middleware('throttle:10,1')->name('invoice.notify-payment');

    Route::get('/quotation/{token}', [PublicDocumentController::class, 'showQuotation'])->name('quotation.show');
    Route::get('/quotation/{token}/pdf', [PublicDocumentController::class, 'downloadQuotationPdf'])->name('quotation.pdf');
    Route::post('/quotation/{token}/respond', [PublicDocumentController::class, 'respondQuotation'])->middleware('throttle:10,1')->name('quotation.respond');
});

// M-Pesa Callback (no auth — called by Safaricom)
Route::post('/api/mpesa/callback', [SubscriptionController::class, 'callback']);
// Staff invitation (public - no auth needed)
Route::get('/invite/{token}', [StaffController::class, 'showAcceptForm'])->name('staff.accept');
Route::post('/invite/{token}', [StaffController::class, 'acceptInvitation'])->name('staff.accept.submit');
