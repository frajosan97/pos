<?php

use App\Http\Controllers\MpesaController;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('/fetch-data')->group(function () {
    Route::get('/constituency/{county_id}', [ApiController::class, 'constituency'])->name('constituency');
    Route::get('/ward/{constituency_id}', [ApiController::class, 'ward'])->name('ward');
    Route::get('/location/{ward_id}', [ApiController::class, 'location'])->name('location');
    Route::get('/product/{barcode}', [ApiController::class, 'product'])->name('product');
    Route::get('/payment-methods', [ApiController::class, 'paymentMethods'])->name('payment-methods');
    Route::get('/mpesa-payments', [ApiController::class, 'mpesaPayments'])->name('mpesa-payments');
});

// M-Pesa callback routes
Route::prefix('/payments')->group(function () {
    Route::post('/callback', [MpesaController::class, 'callback'])->name('payments.callback');
    Route::post('/confirmation', [MpesaController::class, 'confirmation'])->name('payments.confirmation');
    Route::post('/validation', [MpesaController::class, 'validation'])->name('payments.validation');
    Route::post('/timeout', [MpesaController::class, 'timeout'])->name('payments.timeout');
    Route::post('/result', [MpesaController::class, 'result'])->name('payments.result');
});
