<?php

use App\Http\Controllers\DemoAutoUpdateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['jwt.verify', 'api']], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    // Products CRUD API
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products/{id}', [ProductController::class, 'detail']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);

    Route::get('users', [UserController::class, 'show']);
    Route::get('users/{username}', [UserController::class, 'getUserByUsername']);
    Route::put('users', [UserController::class, 'update']);

    Route::put('wallets', [WalletController::class, 'update']);
    Route::get('wallets', [WalletController::class, 'show']);

    Route::get('transfer_histories', [TransferHistoryController::class, 'index']);

    Route::post('top_ups', [TopUpController::class, 'store']);

    Route::post('transfers', [TransferController::class, 'store']);


    Route::get('transactions', [TransactionController::class, 'index']);

    Route::get('payment_methods', [PaymentMethodController::class, 'index']);

    Route::get('tips', [TipController::class, 'index']);

    Route::get('operator_cards', [OperatorCardController::class, 'index']);
});

Route::controller(DemoAutoUpdateController::class)->group(function () {
    Route::get('fetch-data-general', 'fetchDataGeneral')->name('fetch-data-general');
    Route::get('fetch-data-upgrade', 'fetchDataForAutoUpgrade')->name('data-read');
    Route::get('fetch-data-bugs', 'fetchDataForBugs')->name('fetch-data-bugs');
});

