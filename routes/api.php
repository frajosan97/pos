<?php

use App\Http\Controllers\MpesaApiHandleController;
use Illuminate\Support\Facades\Route;

// M-Pesa callback routes
Route::group(['prefix' => '/payments'], function () {
    Route::post('/callback', [MpesaApiHandleController::class, 'callback'])->name('payments.callback');
    Route::post('/confirmation', [MpesaApiHandleController::class, 'confirmation'])->name('payments.confirmation');
    Route::post('/validation', [MpesaApiHandleController::class, 'validation'])->name('payments.validation');
    Route::post('/timeout', [MpesaApiHandleController::class, 'timeout'])->name('payments.timeout');
    Route::post('/result', [MpesaApiHandleController::class, 'result'])->name('payments.result');
});
