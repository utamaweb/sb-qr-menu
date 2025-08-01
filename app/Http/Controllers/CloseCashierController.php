<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Ojol;
use App\Models\Stock;
use App\Models\Expense;
use App\Models\Regional;
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
    public function index(Request $request)
    {
        $warehouse_request = $request->get('warehouse_id', 'all');
        $regional_request = $request->get('regional_id', 'all');
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        $query = CloseCashier::with(['shift.warehouse.regional', 'shift.user']);

        if (auth()->user()->hasRole(['Admin Bisnis', 'Report'])) {
            $query->whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date);

            $regionals = Regional::where('business_id', auth()->user()->business_id)->get();

            if ($regional_request != 'all' && $regional_request != null) {
                $warehouses = Warehouse::where('business_id', auth()->user()->business_id)
                    ->where('regional_id', $regional_request)
                    ->get();

                $query->whereHas('shift.warehouse', function($q) use ($regional_request) {
                    $q->where('regional_id', $regional_request);
                });
            } else {
                $warehouses = Warehouse::where('business_id', auth()->user()->business_id)->get();
            }

            if ($warehouse_request != 'all' && $warehouse_request != null) {
                $query->whereHas('shift', function($q) use ($warehouse_request) {
                    $q->where('warehouse_id', $warehouse_request);
                });
            }
        } else {
            $warehouse_id = auth()->user()->warehouse_id;
            $query->whereHas('shift', function($q) use ($warehouse_id) {
                $q->where('warehouse_id', $warehouse_id);
            });

            $warehouses = collect();
            $regionals = collect();
        }

        $closeCashiers = $query->orderBy('created_at', 'desc')->get();

        return view('backend.close_cashier.index', compact(
            'closeCashiers',
            'warehouses',
            'regionals',
            'warehouse_request',
            'regional_request',
            'start_date',
            'end_date'
        ));
    }

    public function getWarehousesByRegional($regional_id)
    {
        if ($regional_id == 'all') {
            $warehouses = Warehouse::where('business_id', auth()->user()->business_id)->get();
        } else {
            $warehouses = Warehouse::where('business_id', auth()->user()->business_id)
                ->where('regional_id', $regional_id)
                ->get();
        }

        return response()->json($warehouses);
    }

    public function show($id) {
        $closeCashier = CloseCashier::with('shift', 'shift.warehouse', 'shift.user', 'closeCashierProductSold')->find($id);
        $business_id = auth()->user()->warehouse->business_id ?? auth()->user()->business_id;
        $ojols = Ojol::where('business_id', $business_id)->pluck('name')->toArray();
        $ojolName = array_merge(['Tunai', 'Transfer'], $ojols);

        $paymentTransactions = Transaction::with('transaction_details')
            ->whereIn('payment_method', $ojolName)
            ->where('shift_id', $closeCashier->shift_id)
            ->get();

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

        foreach ($transactionDetails as $paymentMethod => $products) {
            $transactionDetails[$paymentMethod] = array_map(function($productName, $qty) {
                return ['product_name' => $productName, 'qty' => $qty];
            }, array_keys($products), $products);
        }

        $closeCashierProductSolds = $closeCashier->closeCashierProductSold;
        $expenses = Expense::with('expenseCategory')->where('shift_id', $closeCashier->shift_id)->get();
        $sumExpense = $expenses->sum('amount');
        $stockPurchases = StockPurchase::where('shift_id', $closeCashier->shift_id)->get();
        $sumStockPurchase = $stockPurchases->sum('total_price');
        $transactionals = Transaction::where('shift_id', $closeCashier->shift_id)->get();

        $stocks = Stock::where('shift_id', $closeCashier->shift_id)->get();
        $ingredientIds = $stocks->pluck('ingredient_id');
        $ingredientStocks = Stock::where('shift_id', $closeCashier->shift_id)
                               ->whereIn('ingredient_id', $ingredientIds)
                               ->get();

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
                'broken_stock' => $ingredientStock->broken_stock,
                'difference_stock' => $ingredientStock->difference_stock,
            ];

            $stocksIngredient[] = (object) $stockData;
        }

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

    public function transactionDetails(Transaction $transaction) {
        $details = TransactionDetail::with('product')->where('transaction_id', $transaction->id)->get();
        return response()->json($details);
    }
}
