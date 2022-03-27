<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'vnpay'], function () {
    Route::get('/', [PaymentController::class, 'showVnpay']);
    Route::get('/return', [PaymentController::class, 'returnVnpay']);
    Route::post('/', [PaymentController::class, 'storeVnpay'])->name('storeVnpay');
});

Route::group(['prefix' => 'momo'], function () {
    Route::get('/', [PaymentController::class, 'getListMomo']);
    Route::get('/create', [PaymentController::class, 'showMomo'])->name('createMomo');
    Route::post('/confirm', [PaymentController::class, 'paymentMomoConfirm'])->name('momoConfirm');
    Route::post('/', [PaymentController::class, 'paymentMomo'])->name('storeMomo');
});
