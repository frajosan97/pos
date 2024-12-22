<?php

use App\Http\Controllers\MpesaApiHandleController;
use Illuminate\Support\Facades\Route;

// M-Pesa callback routes
Route::group(['prefix' => '/transactions'], function () {
    Route::post('/callback', [MpesaApiHandleController::class, 'callback'])->name('transactions.callback');
    Route::post('/confirmation', [MpesaApiHandleController::class, 'confirmation'])->name('transactions.confirmation');
    Route::post('/validation', [MpesaApiHandleController::class, 'validation'])->name('transactions.validation');
    Route::post('/timeout', [MpesaApiHandleController::class, 'timeout'])->name('transactions.timeout');
    Route::post('/result', [MpesaApiHandleController::class, 'result'])->name('transactions.result');
});
