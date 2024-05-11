<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CloseCashier;
use App\Models\Expense;
use App\Models\StockPurchase;
use App\Models\Transaction;
use App\Models\CloseCashierProductSold;
use Auth;
use DB;
use App\Traits\CacheForget;

class CloseCashierController extends Controller
{
    use CacheForget;
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
        $closeCashier = CloseCashier::find($id);
        $closeCashierProductSolds = CloseCashierProductSold::where('close_cashier_id', $id)->get();
        $expenses = Expense::where('shift_id', $closeCashier->shift_id)->get();
        $sumExpense = Expense::where('shift_id', $closeCashier->shift_id)->sum('amount');
        $stockPurchases = StockPurchase::where('shift_id', $closeCashier->shift_id)->get();
        $sumStockPurchase = StockPurchase::where('shift_id', $closeCashier->shift_id)->sum('total_price');
        $transactions = Transaction::where('status', 'Lunas')->where('shift_id', $closeCashier->shift_id)->get();
        return view('backend.close_cashier.show', compact('closeCashier','closeCashierProductSolds', 'expenses', 'stockPurchases','sumExpense','sumStockPurchase','transactions'));
    }

}
