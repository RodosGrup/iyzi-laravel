<?php

use Illuminate\Support\Facades\Route;
use RodosGrup\IyziLaravel\Http\Controllers\PaymentController;

Route::get('gateway', function () {
    return response()->make(session('content'), 200);
})->name('iyzico.laravel.gateway');

Route::post('return', [PaymentController::class, 'return'])->name('iyzico.laravel.return');
// Route::post('iyzi-pay', [PaymentController::class, 'pay'])->name('iyzico.laravel.pay');
