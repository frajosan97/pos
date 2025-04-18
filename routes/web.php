<?php

use App\Exports\PaymentsExport;
use App\Exports\SalesExport;
use App\Exports\ProductsExport;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Auth::routes();

/*
|--------------------------------------------------------------------------
| OTP & Account Verification Routes
|--------------------------------------------------------------------------
*/
Route::prefix('verify')->group(function () {
    Route::get('/send-otp-form', function () {
        $verifyMethods = session('verify_methods', []);
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

/*
|--------------------------------------------------------------------------
| Excel Generator
|--------------------------------------------------------------------------
*/
Route::prefix('excel')->group(function () {
    Route::get('/users', function () {
        return Excel::download(new UsersExport, 'users.xlsx');
    });
    Route::get('/inventory', function () {
        return Excel::download(new ProductsExport, 'inventory.xlsx');
    });
    Route::get('/sales', function () {
        return Excel::download(new SalesExport, 'sales.xlsx');
    });
    Route::get('/payments', function () {
        return Excel::download(new PaymentsExport, 'payments.xlsx');
    });
});

/*
|--------------------------------------------------------------------------
| PDF Generator
|--------------------------------------------------------------------------
*/
Route::prefix('pdf')->group(function () {
    Route::get('/branch', [PdfController::class, 'branch'])->name('branch.pdf');
    Route::get('/employee', [PdfController::class, 'employee'])->name('employee.pdf');
    Route::get('/contract_letter/{id}', [PdfController::class, 'contractLetter'])->name('contract_letter.pdf');
    Route::get('/inventory', [PdfController::class, 'inventory'])->name('inventory.pdf');
    Route::get('/sales', [PdfController::class, 'sales'])->name('sales.pdf');
    Route::get('/receipt/{id}', [PdfController::class, 'receipt'])->name('receipt.pdf');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Auth + Permission Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'permission'])->group(function () {
    /*
    |-------------------------------------------------------------------------
    | Management
    |-------------------------------------------------------------------------
    */
    Route::prefix('manage')->group(function () {
        Route::get('/self', [DashboardController::class, 'manager_self'])->name('manager.self');
        Route::get('/branch', [DashboardController::class, 'manager_branch'])->name('manager.branch');
        Route::get('/general', [DashboardController::class, 'manager_general'])->name('manager.general');
        Route::get('/product', [SaleController::class, 'catalogue'])->name('manager.product');
        Route::get('/catalogue', [SaleController::class, 'product'])->name('manager.catalogue');
        Route::get('/category-product-sales', [SaleController::class, 'catProFetch'])->name('sale.cat_pro_fetch');
    });

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/', [SaleController::class, 'create'])->name('/');
    Route::get('/analytics', [DashboardController::class, 'index'])->name('analytics.index');

    /*
    |--------------------------------------------------------------------------
    | Resourceful Routes
    |--------------------------------------------------------------------------
    */
    Route::resources([
        'employee'  => EmployeeController::class,
        'catalogue' => CatalogueController::class,
        'product'   => ProductController::class,
        'payment'   => PaymentController::class,
        'sale'      => SaleController::class,
        'customer'  => CustomerController::class,
    ]);

    Route::post('/product-verify/{id}', [ProductController::class, 'verify'])->name('product.verify');

    /*
    |--------------------------------------------------------------------------
    | Chat Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('chat')->group(function () {
        Route::get('/box', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/load-chats', [ChatController::class, 'loadChats'])->name('chat.load-chat');
        Route::get('/messages/{conversationId}', [ChatController::class, 'getMessages'])->name('message.get');
        Route::get('/get-user/{id}', [ChatController::class, 'getUser'])->name('chat.get');
        Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('message.send');
    });

    /*
    |--------------------------------------------------------------------------
    | M-Pesa Integration
    |--------------------------------------------------------------------------
    */
    Route::prefix('mpesa')->group(function () {
        Route::post('/registerUrl', [MpesaController::class, 'registerUrl'])->name('mpesa.registerUrl');
        Route::post('/simulate', [MpesaController::class, 'simulate'])->name('mpesa.simulate');
        Route::post('/stkpush', [MpesaController::class, 'stkpush'])->name('mpesa.stkpush');

        Route::view('/stkpush-form', 'portal.mpesa.stk_push')->name('mpesa.stkpush.form');
        Route::view('/simulate-form', 'portal.mpesa.simulate')->name('mpesa.simulate.form');
    });

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->group(function () {
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

    /*
    |--------------------------------------------------------------------------
    | KYC & Employee Signature
    |--------------------------------------------------------------------------
    */
    Route::post('/kyc/{id}/handle', [EmployeeController::class, 'handleKyc'])->name('kyc.handle');
    Route::get('/contract-letter/{id}', [EmployeeController::class, 'contractLetter'])->name('contract_letter.show');
    Route::post('/save-signature/{id}', [EmployeeController::class, 'saveSignature'])->name('save_signature.save');

    /*
    |--------------------------------------------------------------------------
    | Cache & Optimization
    |--------------------------------------------------------------------------
    */
    Route::view('/clear-cache', 'portal.setting.clear-cache')->name('clear-cache.form');
    Route::view('/optimize-system', 'portal.setting.optimize-system')->name('optimize-system.form');
    Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('cache.clear');
    Route::post('/optimize', [SettingController::class, 'optimize'])->name('cache.optimize');
});
