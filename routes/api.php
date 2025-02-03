<?php

use App\Http\Controllers\MpesaController;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

// M-Pesa callback routes
Route::group(['prefix' => '/payments'], function () {
    Route::post('/callback', [MpesaController::class, 'callback'])->name('payments.callback');
    Route::post('/confirmation', [MpesaController::class, 'confirmation'])->name('payments.confirmation');
    Route::post('/validation', [MpesaController::class, 'validation'])->name('payments.validation');
    Route::post('/timeout', [MpesaController::class, 'timeout'])->name('payments.timeout');
    Route::post('/result', [MpesaController::class, 'result'])->name('payments.result');
});

Route::group(['prefix' => '/fetch-data'], function () {
    Route::get('/constituency/{county_id}', [ApiController::class, 'constituency']);
    Route::get('/ward/{constituency_id}', [ApiController::class, 'ward']);
    Route::get('/location/{ward_id}', [ApiController::class, 'location']);
    Route::get('/product/{barcode}', [ApiController::class, 'product']);
    Route::get('/payment-methods', [ApiController::class, 'paymentMethods']);
    Route::get('/mpesa-payments', [ApiController::class, 'mpesaPayments']);
});
