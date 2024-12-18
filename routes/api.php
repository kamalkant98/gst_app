<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PayUMoneyController;
use App\Http\Controllers\BusinessRegistrationController;
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

Route::Post('/api/generate_otp',[UserController::class, 'generate_otp']);
Route::Post('/api/verifyOtp',[UserController::class, 'verifyOtp']);


// Route::post('/api/user/store', [UserController::class, 'store']);
// Route::get('pay-u-money-view',[PayUMoneyController::class,'payUMoneyView']);
// Route::get('pay-u-response',[PayUMoneyController::class,'payUResponse'])->name('pay.u.response');

// Route::get('pay-u-cancel',[PayUMoneyController::class,'payUCancel'])->name('pay.u.cancel');
// Route::get('/payu-payment', [PayUMoneyController::class, 'initiatePayment'])->name('payu.initiate');


Route::post('/api/payu-payment', [PayUMoneyController::class, 'initiatePayment'])->name('payu.initiate');
Route::post('/api/payu-callback', [PayUMoneyController::class, 'handleCallback'])->name('payu.callback');

Route::Post('/api/user/store',[UserController::class, 'store']);

Route::post('/api/business-registration/store', [BusinessRegistrationController::class, 'businessStore'])->name('business-registration-store');