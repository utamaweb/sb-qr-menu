<?php

use App\Http\Controllers\DemoAutoUpdateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PrinterController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\ExpenseController;

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

Route::post('refresh-token', [AuthController::class, 'refreshToken']);
Route::group(['middleware' => ['jwt.verify', 'api']], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    // Products CRUD API
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('product-warehouse', [ProductController::class, 'productByWarehouse']);
    Route::get('products/{id}', [ProductController::class, 'detail']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);

    // Stock & Ingredients
    Route::get('ingredients', [StockController::class, 'getAllIngredients']);
    Route::get('stocks', [StockController::class, 'getAllStocks']);
    Route::get('stock-history', [StockController::class, 'getStockHistory']);
    Route::get('stock-history/{id}', [StockController::class, 'getDetailStockHistory']);
    Route::post('stocks/add', [StockController::class, 'add']);
    Route::put('stocks/edit/{id}', [StockController::class, 'edit']);
    Route::get('stock-warehouse', [StockController::class, 'getStockByWarehouse']);
    Route::get('warehouse-ingredients', [StockController::class, 'getWarehouseIngredients']);
    Route::get('ingredient-sold', [StockController::class, 'getIngredientSold']);

    // Expense
    Route::post('expense/add', [ExpenseController::class, 'add']);
    Route::put('expense/edit/{id}', [ExpenseController::class, 'edit']);
    Route::get('expense/show/{id}', [ExpenseController::class, 'show']);
    Route::get('expense/category', [ExpenseController::class, 'category']);
    Route::get('expense/', [ExpenseController::class, 'getExpense']);


    // Printer CRUD API
    Route::post('printer', [PrinterController::class, 'store']);
    Route::get('printer', [PrinterController::class, 'index']);
    Route::get('printer/{id}', [PrinterController::class, 'detail']);
    Route::put('printer/{id}', [PrinterController::class, 'update']);
    Route::delete('printer/{id}', [PrinterController::class, 'destroy']);

    Route::get('users', [UserController::class, 'show']);
    Route::get('users/{username}', [UserController::class, 'getUserByUsername']);
    Route::put('users', [UserController::class, 'update']);

    // Transaction
    Route::get('transaction/history/online', [TransactionController::class, 'online']);
    Route::get('transaction/history/offline', [TransactionController::class, 'offline']);
    Route::post('transaction/online', [TransactionController::class, 'storeOnline']);
    Route::get('transaction/latest', [TransactionController::class, 'latest']);
    Route::get('transaction/all', [TransactionController::class, 'all']);
    Route::get('transaction/not-paid', [TransactionController::class, 'notPaid']);
    Route::get('transaction/order-types', [TransactionController::class, 'orderType']);
    Route::get('transaction/{id}', [TransactionController::class, 'detail']);
    Route::post('transaction', [TransactionController::class, 'store']);

    // Shift / Close Cashier
    Route::post('shift/open', [ShiftController::class, 'open']);
    Route::post('close_cashier/close', [ShiftController::class, 'close']);
    Route::get('shift/check', [ShiftController::class, 'checkCashier']);
    Route::get('shift/latest', [ShiftController::class, 'latest']);
    Route::get('shift/closable', [ShiftController::class, 'closable']);
});


