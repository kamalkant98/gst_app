<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayUMoneyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('invoice_template');
});




// Route::get('/payu-payment', [PayUMoneyController::class, 'initiatePayment'])->name('payu.initiate');
// Route::post('/payu-callback', [PayUMoneyController::class, 'handleCallback'])->name('payu.callback');

