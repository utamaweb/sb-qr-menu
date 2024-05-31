<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Stock;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\CloseCashier;
use Illuminate\Http\Request;
use App\Models\StockPurchase;
use App\Models\TransactionDetail;
use App\Http\Controllers\Controller;
use App\Models\CloseCashierProductSold;

class CloseCashierController extends Controller
{
    public function index()
    {
        $warehouseId = auth()->user()->warehouse_id;

        // Ambil data close_cashiers yang memiliki warehouse_id sesuai dengan pengguna yang login
        $closeCashiers = CloseCashier::whereHas('shift', function ($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        })->orderBy('id', 'DESC')->get();
        return view('backend.close_cashier.index', compact('closeCashiers'));
    }

    public function show($id) {
        // get detail tutup kasir
        $closeCashier = CloseCashier::find($id);
        // get produk terjual di shift tersebut
        $closeCashierProductSolds = CloseCashierProductSold::where('close_cashier_id', $id)->get();
        // get pengeluaran shift tersebut
        $expenses = Expense::where('shift_id', $closeCashier->shift_id)->get();
        $sumExpense = Expense::where('shift_id', $closeCashier->shift_id)->sum('amount');
        // get penambahan stok shift tersebut
        $stockPurchases = StockPurchase::where('shift_id', $closeCashier->shift_id)->get();
        $sumStockPurchase = StockPurchase::where('shift_id', $closeCashier->shift_id)->sum('total_price');
        // get transaksi lunas shift tersebut
        $transactions = Transaction::where('status', 'Lunas')->where('shift_id', $closeCashier->shift_id)->get();
        // get sisa stok shift tersebut
        $stocksIngredient = []; // Pastikan untuk menginisialisasi array terlebih dahulu

        $stocksIngredient = []; // Pastikan untuk menginisialisasi array terlebih dahulu

        $stocks = Stock::where('shift_id', $closeCashier->shift_id)->get();

        foreach ($stocks as $stock) {
            $ingredientStock = Stock::where('shift_id', $closeCashier->shift_id)
                                    ->where('ingredient_id', $stock['ingredient_id'])
                                    ->first();

            if ($ingredientStock) {
                $stockData = [
                    'ingredient_id' => $stock['ingredient_id'],
                    'ingredient_name' => $ingredientStock->ingredient->name,
                    'first_stock' => $ingredientStock->first_stock,
                    'stock_in' => $ingredientStock->stock_in,
                    'total_stock' => $ingredientStock->stock_in + $ingredientStock->first_stock,
                    'used_stock' => $ingredientStock->stock_used,
                    'last_system_stock' => $ingredientStock->last_stock,
                    'stock_close_input' => $ingredientStock->stock_close_input,
                    'difference_stock' => $ingredientStock->difference_stock,
                ];

                // Konversi $stockData menjadi objek
                $stocksIngredient[] = (object) $stockData;
            }
        }
        return view('backend.close_cashier.show', compact('closeCashier','closeCashierProductSolds', 'expenses', 'stockPurchases','sumExpense','sumStockPurchase','transactions', 'stocks', 'stocksIngredient'));
    }


    // Method to get CloseCashier Transaction Details
    public function transactionDetails(Transaction $transaction) {
        $details = TransactionDetail::where('transaction_id', $transaction->id)->get();

        return response()->json($details);
    }

}
