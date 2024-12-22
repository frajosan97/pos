<?php

/**
 * Import necessary classes for handling route controllers.
 * These classes manage the logic for authentication, payments, settings, API, and the dashboard.
 */

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Define authentication routes provided by Laravel.
 * This includes routes for login, registration, and password management.
 */
Auth::routes();

/**
 * Routes for handling account verification and OTP-based authentication.
 */
Route::prefix('/verify')->group(function () {
    Route::get('/send-otp-form', [LoginController::class, 'showOtpSendForm'])->name('otp.send-otp');
    Route::post('/send-otp', [LoginController::class, 'sendOtp'])->name('otp.send');
    Route::get('/verify-otp-form', [LoginController::class, 'showOtpVerificationForm'])->name('otp.verify-otp');
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp'])->name('otp.verify');
    Route::get('/resend-activation-link', function () {
        return view('auth.verify');
    })->name('verify.resend-activation-link');
    Route::post('/verify-link', [LoginController::class, 'sendVerificationLink'])->name('verify-link.send');
    Route::get('/activate/{email}', [LoginController::class, 'activateAccount'])->name('verify.activate');
});

/**
 * Group routes requiring user authentication.
 */
Route::group(['middleware' => ['auth']], function () {
    /**
     * Route for the application dashboard.
     */
    Route::get('/', [SaleController::class, 'create'])->name('/');
    Route::get('/analytics', [DashboardController::class, 'index'])->name('analytics.index');

    /**
     * Route for the application employees.
     */
    Route::resource('employee', EmployeeController::class);

    /**
     * Route for the catalogue.
     */
    Route::resource('catalogue', CatalogueController::class);

    /**
     * Route for the products.
     */
    Route::resource('product', ProductController::class);

    /**
     * Route for the sale.
     */
    Route::resource('sale', SaleController::class);

    /**
     * Route for the customers.
     */
    Route::resource('customer', CustomerController::class);

    /**
     * API routes for data fetching and external integrations.
     */
    Route::prefix('/api/fetch-data')->group(function () {
        Route::get('/analytics', [ApiController::class, 'analytics']);
        Route::get('/constituency/{county_id}', [ApiController::class, 'constituency']);
        Route::get('/ward/{constituency_id}', [ApiController::class, 'ward']);
        Route::get('/location/{ward_id}', [ApiController::class, 'location']);
        Route::get('/product/{barcode}', [ApiController::class, 'product']);
        Route::get('/payment-methods', [ApiController::class, 'paymentMethods']);
        Route::get('/mpesa-payments', [ApiController::class, 'mpesaPaymants']);
    });

    /**
     * PDF Generators
     */
    Route::prefix('/pdf')->group(function () {
        Route::get('/inventory', [PdfController::class, 'inventory'])->name('inventory.pdf');
        Route::get('/employee', [PdfController::class, 'employee'])->name('employee.pdf');
        Route::get('/sales', [PdfController::class, 'sales'])->name('sales.pdf');
        Route::get('/invoice/{invoice_id}', [PdfController::class, 'invoice'])->name('invoice.pdf');
        Route::get('/branch', [PdfController::class, 'branch'])->name('branch.pdf');
        Route::get('/company', [PdfController::class, 'company'])->name('company.pdf');
    });

    /**
     * Payment-related routes for handling M-Pesa integrations and forms.
     */
    Route::prefix('/mpesa')->group(function () {
        // Post requests
        Route::post('/registerUrl', [MpesaController::class, 'registerUrl'])->name('mpesa.registerUrl');
        Route::post('/simulate', [MpesaController::class, 'simulate'])->name('mpesa.simulate');
        Route::post('/stkpush', [MpesaController::class, 'stkpush'])->name('mpesa.stkpush');
        // Views display
        Route::view('/stkpush-form', 'portal.mpesa.stk_push')->name('mpesa.stkpush.form');
        Route::view('/simulate-form', 'portal.mpesa.simulate')->name('mpesa.simulate.form');
    });

    /**
     * Routes for application settings.
     */
    Route::prefix('/settings')->group(function () {
        Route::get('/county', [SettingController::class, 'county'])->name('setting.county');
        Route::post('/county/store', [SettingController::class, 'storeCounty'])->name('setting.county.store');
        Route::put('/county/update/{id}', [SettingController::class, 'updateCounty'])->name('setting.county.update');
        Route::post('/county/destroy', [SettingController::class, 'destroyCounty'])->name('setting.county.destroy');

        Route::get('/constituency', [SettingController::class, 'constituency'])->name('setting.constituency');
        Route::post('/constituency/store', [SettingController::class, 'storeConstituency'])->name('setting.constituency.store');
        Route::put('/constituency/update/{id}', [SettingController::class, 'updateConstituency'])->name('setting.constituency.update');
        Route::post('/constituency/destroy', [SettingController::class, 'destroyConstituency'])->name('setting.constituency.destroy');

        Route::get('/ward', [SettingController::class, 'ward'])->name('setting.ward');
        Route::post('/ward/store', [SettingController::class, 'storeWard'])->name('setting.ward.store');
        Route::put('/ward/update/{id}', [SettingController::class, 'updateWard'])->name('setting.ward.update');
        Route::post('/ward/destroy', [SettingController::class, 'destroyWard'])->name('setting.ward.destroy');

        Route::get('/location', [SettingController::class, 'location'])->name('setting.location');
        Route::post('/location/store', [SettingController::class, 'storeLocation'])->name('setting.location.store');
        Route::put('/location/update/{id}', [SettingController::class, 'updateLocation'])->name('setting.location.update');
        Route::post('/location/destroy', [SettingController::class, 'destroyLocation'])->name('setting.location.destroy');

        Route::get('/branch', [SettingController::class, 'branch'])->name('setting.branch');
        Route::post('/branch/store', [SettingController::class, 'storeBranch'])->name('setting.branch.store');
        Route::put('/branch/update/{id}', [SettingController::class, 'updateBranch'])->name('setting.branch.update');
        Route::post('/branch/destroy', [SettingController::class, 'destroyBranch'])->name('setting.branch.destroy');

        Route::get('/role', [SettingController::class, 'role'])->name('setting.role');
        Route::post('/role/store', [SettingController::class, 'storeRole'])->name('setting.role.store');
        Route::put('/role/update/{id}', [SettingController::class, 'updateRole'])->name('setting.role.update');
        Route::post('/role/destroy', [SettingController::class, 'destroyRole'])->name('setting.role.destroy');

        Route::get('/company', [SettingController::class, 'company'])->name('setting.company');
        Route::put('/company/update/{id}', [SettingController::class, 'updateCompany'])->name('setting.company.update');
    });

    // CACHE
    Route::get('/clear-cache', function () {
        return view('portal.setting.clear-cache');
    })->name('clear-cache.form');
    Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('cache.clear');
    Route::post('/optimize', [SettingController::class, 'optimize'])->name('cache.optimize');
});
