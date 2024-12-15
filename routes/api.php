<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PayUMoneyController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/api/data', [UserController::class, 'index']);
Route::post('/api/user/store', [UserController::class, 'store']);

Route::post('api/payumoney/payment', [PayUMoneyController::class, 'initiatePayment'])->name('payment');

Route::post('api/payumoney/success', [PayUMoneyController::class, 'paymentSuccess'])->name('payumoney.success');
Route::post('api/payumoney/failure', [PayUMoneyController::class, 'paymentFailure'])->name('payumoney.failure');







Route::get('pay', [PayUMoneyController::class, 'pay'])->name('pay');
Route::post('pay/notify', [PayUMoneyController::class, 'notify'])->name('pay.notify');
Route::get('pay/success', [PayUMoneyController::class, 'success'])->name('pay.success');
Route::get('pay/failure', [PayUMoneyController::class, 'failure'])->name('pay.failure');


