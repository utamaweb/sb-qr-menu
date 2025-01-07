<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Ojol;
use App\Models\Stock;
use App\Models\Expense;
use App\Models\Warehouse;
use App\Models\Transaction;
use App\Models\CloseCashier;
use Illuminate\Http\Request;
use App\Models\OjolWarehouse;
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
        $closeCashiers = CloseCashier::with('warehouse', 'user', 'shift', 'shift.warehouse', 'shift.user')->whereHas('shift', function ($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        })->orderBy('id', 'DESC')->get();
        return view('backend.close_cashier.index', compact('closeCashiers'));
    }

    // function show old version (took so long time to show)
    public function showOld($id) {
        // get detail tutup kasir
        $closeCashier = CloseCashier::find($id);

        // adjust ojols
        $business_id = Warehouse::find(auth()->user()->warehouse_id)->business_id;
        $ojols = Ojol::where('business_id', $business_id)->get();
        $ojolName = ['Tunai','Transfer'];
        foreach ($ojols as $ojol) {
            $ojolName[] = $ojol->name;
        }

        // get produk terjual di shift tersebut
        $paymentTransactions = Transaction::whereIn('payment_method', $ojolName)
            ->where('shift_id', $closeCashier->shift_id)
            ->get();

        // init transactionDetails dynamically
        $transactionDetails = [];
        foreach ($ojolName as $paymentMethod) {
            $transactionDetails[$paymentMethod] = [];
        }

        // count products
        foreach ($paymentTransactions as $transaction) {
            $details = TransactionDetail::where('transaction_id', $transaction->id)->get();
            foreach ($details as $detail) {
                $productName = $detail->product_name;
                $paymentMethod = $transaction->payment_method;

                // add qty to product
                if (isset($transactionDetails[$paymentMethod][$productName])) {
                    $transactionDetails[$paymentMethod][$productName] += $detail->qty;
                } else {
                    $transactionDetails[$paymentMethod][$productName] = $detail->qty;
                }
            }
        }

        // formatting array
        foreach ($transactionDetails as $paymentMethod => $products) {
            $transactionDetails[$paymentMethod] = array_map(function($productName, $qty) {
                return ['product_name' => $productName, 'qty' => $qty];
            }, array_keys($products), $products);
        }

        // return $transactionDetails;


        $closeCashierProductSolds = CloseCashierProductSold::where('close_cashier_id', $id)->get();
        // get pengeluaran shift tersebut
        $expenses = Expense::where('shift_id', $closeCashier->shift_id)->get();
        $sumExpense = Expense::where('shift_id', $closeCashier->shift_id)->sum('amount');
        // get penambahan stok shift tersebut
        $stockPurchases = StockPurchase::where('shift_id', $closeCashier->shift_id)->get();
        $sumStockPurchase = StockPurchase::where('shift_id', $closeCashier->shift_id)->sum('total_price');
        // get transaksi lunas shift tersebut
        $transactionals = Transaction::where('status', 'Lunas')->where('shift_id', $closeCashier->shift_id)->get();
        // get sisa stok shift tersebut
        $stocksIngredient = [];

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
        return view('backend.close_cashier.show', compact('closeCashier','closeCashierProductSolds', 'expenses', 'stockPurchases','sumExpense','sumStockPurchase','transactionals', 'stocks', 'stocksIngredient', 'transactionDetails'));
    }


    // update the function to new version (optimized)
    public function show($id) {
        // Ambil detail tutup kasir
        $closeCashier = CloseCashier::with('shift', 'shift.warehouse', 'shift.user', 'closeCashierProductSold')->find($id);

        // dd($closeCashier->closeCashierProductSold);

        // Ambil business_id berdasarkan warehouse user
        $business_id = auth()->user()->warehouse->business_id;

        // Ambil nama ojol yang tersedia
        $ojols = Ojol::where('business_id', $business_id)->pluck('name')->toArray();
        $ojolName = array_merge(['Tunai', 'Transfer'], $ojols);

        // Gunakan eager loading untuk mengurangi jumlah query
        $paymentTransactions = Transaction::with('transaction_details')
            ->whereIn('payment_method', $ojolName)
            ->where('shift_id', $closeCashier->shift_id)
            ->get();

        // Inisialisasi array transaksi
        $transactionDetails = [];
        foreach ($paymentTransactions as $transaction) {
            foreach ($transaction->transaction_details as $detail) {
                $productName = $detail->product_name;
                $paymentMethod = $transaction->payment_method;

                if (!isset($transactionDetails[$paymentMethod])) {
                    $transactionDetails[$paymentMethod] = [];
                }

                if (isset($transactionDetails[$paymentMethod][$productName])) {
                    $transactionDetails[$paymentMethod][$productName] += $detail->qty;
                } else {
                    $transactionDetails[$paymentMethod][$productName] = $detail->qty;
                }
            }
        }

        // Formatting array
        foreach ($transactionDetails as $paymentMethod => $products) {
            $transactionDetails[$paymentMethod] = array_map(function($productName, $qty) {
                return ['product_name' => $productName, 'qty' => $qty];
            }, array_keys($products), $products);
        }


        // Ambil data produk yang terjual
        // $closeCashierProductSolds = CloseCashierProductSold::where('close_cashier_id', $id)->get();
        $closeCashierProductSolds = $closeCashier->closeCashierProductSold;

        // Ambil data pengeluaran di shift tersebut
        $expenses = Expense::with('expenseCategory')->where('shift_id', $closeCashier->shift_id)->get();
        $sumExpense = $expenses->sum('amount'); // Optimasi dengan sum di PHP, bukan query

        // Ambil penambahan stok shift tersebut
        $stockPurchases = StockPurchase::where('shift_id', $closeCashier->shift_id)->get();
        $sumStockPurchase = $stockPurchases->sum('total_price'); // Optimasi dengan sum di PHP

        // Ambil transaksi lunas shift tersebut
        $transactionals = Transaction::where('status', 'Lunas')
            ->where('shift_id', $closeCashier->shift_id)
            ->get();

        // Ambil stok sisa di shift tersebut
        $stocks = Stock::where('shift_id', $closeCashier->shift_id)->get();
        $ingredientIds = $stocks->pluck('ingredient_id');

        // Ambil stok bahan dalam satu query berdasarkan ingredient_id
        $ingredientStocks = Stock::where('shift_id', $closeCashier->shift_id)
                                 ->whereIn('ingredient_id', $ingredientIds)
                                 ->get();

        // Olah stok menjadi array yang dapat digunakan di view
        $stocksIngredient = [];
        foreach ($ingredientStocks as $ingredientStock) {
            $stockData = [
                'ingredient_id' => $ingredientStock->ingredient_id,
                'ingredient_name' => $ingredientStock->ingredient->name,
                'first_stock' => $ingredientStock->first_stock,
                'stock_in' => $ingredientStock->stock_in,
                'total_stock' => $ingredientStock->first_stock + $ingredientStock->stock_in,
                'used_stock' => $ingredientStock->stock_used,
                'last_system_stock' => $ingredientStock->last_stock,
                'stock_close_input' => $ingredientStock->stock_close_input,
                'difference_stock' => $ingredientStock->difference_stock,
            ];

            // Konversi $stockData menjadi objek
            $stocksIngredient[] = (object) $stockData;
        }

        // Kirim data ke view
        return view('backend.close_cashier.show', compact(
            'closeCashier',
            'closeCashierProductSolds',
            'expenses',
            'stockPurchases',
            'sumExpense',
            'sumStockPurchase',
            'transactionals',
            'stocks',
            'stocksIngredient',
            'transactionDetails'
        ));
    }



    // Method to get CloseCashier Transaction Details
    public function transactionDetails(Transaction $transaction) {
        $details = TransactionDetail::where('transaction_id', $transaction->id)->get();

        return response()->json($details);
    }

}
