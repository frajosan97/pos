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
use App\Http\Controllers\PaymentController;
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
    Route::get('/send-otp-form', function () {
        $verifyMethods = session('verify_methods', []); // Get session data
        return view('auth.otp.send-otp', compact('verifyMethods'));
    })->name('otp.send-otp');

    Route::post('/send-otp', [LoginController::class, 'sendOtp'])->name('otp.send');
    Route::view('/verify-otp-form', 'auth.otp.verify-otp')->name('otp.verify-otp');
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp'])->name('otp.verify');

    Route::view('/resend-activation-link', 'auth.verify')->name('verify.resend-activation-link');
    Route::post('/verify-link', [LoginController::class, 'sendVerificationLink'])->name('verify-link.send');

    Route::get('/activate/{email}', function ($email) {
        return view('auth.activate', compact('email'));
    })->name('verify.activate');
    Route::post('/activate-account/{email}', [LoginController::class, 'activateAccount'])->name('account.activate');
});

/**
 * Group routes requiring user authentication.
 */
Route::middleware('auth')->group(function () {
    /**
     * Dashboard routes.
     */
    Route::get('/', [SaleController::class, 'create'])->name('/');
    Route::get('/analytics', [DashboardController::class, 'index'])->name('analytics.index');

    /**
     * Resource routes for employees, catalogue, products, payments, and customers.
     */
    Route::resources([
        'employee'  => EmployeeController::class,
        'catalogue' => CatalogueController::class,
        'product'   => ProductController::class,
        'payment'   => PaymentController::class,
        'sale'      => SaleController::class,
        'customer'  => CustomerController::class,
    ]);

    /**
     * Sale-specific routes.
     */
    Route::get('/brand-sales', [SaleController::class, 'catalogue'])->name('sale.catalogue');
    Route::get('/product-sales', [SaleController::class, 'product'])->name('sale.product');
    Route::get('/category-product-sales', [SaleController::class, 'catProFetch'])->name('sale.cat_pro_fetch');

    /**
     * API routes for data fetching and external integrations.
     */
    Route::prefix('/api/fetch-data')->group(function () {
        Route::get('/constituency/{county_id}', [ApiController::class, 'constituency']);
        Route::get('/ward/{constituency_id}', [ApiController::class, 'ward']);
        Route::get('/location/{ward_id}', [ApiController::class, 'location']);
        Route::get('/product/{barcode}', [ApiController::class, 'product']);
        Route::get('/payment-methods', [ApiController::class, 'paymentMethods']);
        Route::get('/mpesa-payments', [ApiController::class, 'mpesaPayments']);
    });

    /**
     * PDF Generator routes.
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
     * Payment-related routes for M-Pesa integration.
     */
    Route::prefix('/mpesa')->group(function () {
        Route::post('/registerUrl', [MpesaController::class, 'registerUrl'])->name('mpesa.registerUrl');
        Route::post('/simulate', [MpesaController::class, 'simulate'])->name('mpesa.simulate');
        Route::post('/stkpush', [MpesaController::class, 'stkpush'])->name('mpesa.stkpush');

        Route::view('/stkpush-form', 'portal.mpesa.stk_push')->name('mpesa.stkpush.form');
        Route::view('/simulate-form', 'portal.mpesa.simulate')->name('mpesa.simulate.form');
    });

    /**
     * Settings routes.
     */
    Route::prefix('/settings')->group(function () {
        $entities = ['county', 'constituency', 'ward', 'location', 'branch', 'role', 'company'];
        foreach ($entities as $entity) {
            Route::get("/$entity", [SettingController::class, $entity])->name("setting.$entity");
            Route::post("/$entity/store", [SettingController::class, 'store' . ucfirst($entity)])->name("setting.$entity.store");
            Route::put("/$entity/update/{id}", [SettingController::class, 'update' . ucfirst($entity)])->name("setting.$entity.update");
            Route::post("/$entity/destroy", [SettingController::class, 'destroy' . ucfirst($entity)])->name("setting.$entity.destroy");
        }

        Route::put('/role/update-permission/{id}', [SettingController::class, 'updatePermission'])->name('setting.roles.updatePermissions');
        Route::put('/company/update/{id}', [SettingController::class, 'updateCompany'])->name('setting.company.update');
    });

    Route::post('/kyc/{id}/handle', [EmployeeController::class, 'handleKyc'])->name('kyc.handle');

    /**
     * Cache-related routes.
     */
    Route::view('/clear-cache', 'portal.setting.clear-cache')->name('clear-cache.form');
    Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('cache.clear');
    Route::post('/optimize', [SettingController::class, 'optimize'])->name('cache.optimize');
});
