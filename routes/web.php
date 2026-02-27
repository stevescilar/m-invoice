<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Catalog\ServiceCategoryController;
use App\Http\Controllers\Catalog\CatalogItemController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\Quotation\QuotationController;
use App\Http\Controllers\Expense\ExpenseController;
use App\Http\Controllers\Subscription\SubscriptionController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    // Company setup (no active.company middleware here)
    Route::get('/company/setup', [CompanyController::class, 'setup'])->name('company.setup');
    Route::post('/company/setup', [CompanyController::class, 'storeSetup']);

    // All other routes require active company
    Route::middleware(['active.company'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Company settings
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

        // Expenses
        Route::resource('expenses', ExpenseController::class);

        // Subscriptions
        Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});
