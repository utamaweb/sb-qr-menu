<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CloseCashier;
use App\Models\Expense;
use App\Models\StockPurchase;
use App\Models\CloseCashierProductSold;
use Auth;
use DB;
use App\Traits\CacheForget;

class CloseCashierController extends Controller
{
    use CacheForget;
    public function index()
    {
        $closeCashiers = CloseCashier::get();
        return view('backend.close_cashier.index', compact('closeCashiers'));
    }

    public function show($id) {
        $closeCashier = CloseCashier::find($id);
        $closeCashierProductSolds = CloseCashierProductSold::where('close_cashier_id', $id)->get();
        $expenses = Expense::where('shift_id', $closeCashier->shift_id)->get();
        $sumExpense = Expense::where('shift_id', $closeCashier->shift_id)->sum('amount');
        $stockPurchases = StockPurchase::where('shift_id', $closeCashier->shift_id)->get();
        $sumStockPurchase = StockPurchase::where('shift_id', $closeCashier->shift_id)->sum('total_price');
        return view('backend.close_cashier.show', compact('closeCashier','closeCashierProductSolds', 'expenses', 'stockPurchases','sumExpense','sumStockPurchase'));
    }

}
