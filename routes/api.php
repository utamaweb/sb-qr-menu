<?php

use App\Http\Controllers\DemoAutoUpdateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PrinterController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\CloseCashierController;

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
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'detail']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);

    // Printer CRUD API
    Route::post('printer', [PrinterController::class, 'store']);
    Route::get('printer', [PrinterController::class, 'index']);
    Route::get('printer/{id}', [PrinterController::class, 'detail']);
    Route::put('printer/{id}', [PrinterController::class, 'update']);
    Route::delete('printer/{id}', [PrinterController::class, 'destroy']);

    Route::get('users', [UserController::class, 'show']);
    Route::get('users/{username}', [UserController::class, 'getUserByUsername']);
    Route::put('users', [UserController::class, 'update']);

    Route::post('transaction', [TransactionController::class, 'store']);


    Route::post('close_cashier/open', [CloseCashierController::class, 'open']);
    Route::post('close_cashier/close', [CloseCashierController::class, 'close']);


    Route::put('wallets', [WalletController::class, 'update']);
    Route::get('wallets', [WalletController::class, 'show']);

    Route::get('transfer_histories', [TransferHistoryController::class, 'index']);

    Route::post('top_ups', [TopUpController::class, 'store']);

    Route::post('transfers', [TransferController::class, 'store']);


});

Route::controller(DemoAutoUpdateController::class)->group(function () {
    Route::get('fetch-data-general', 'fetchDataGeneral')->name('fetch-data-general');
    Route::get('fetch-data-upgrade', 'fetchDataForAutoUpgrade')->name('data-read');
    Route::get('fetch-data-bugs', 'fetchDataForBugs')->name('fetch-data-bugs');
});

