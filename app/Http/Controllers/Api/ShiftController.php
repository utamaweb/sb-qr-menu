<?php

namespace App\Http\Controllers\Api;

use DB;
use Carbon\Carbon;
use App\Models\Ojol;
use App\Models\Shift;
use App\Models\Stock;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Transaction;
use App\Models\CloseCashier;
use Illuminate\Http\Request;
use App\Models\StockPurchase;
use App\Models\OjolCloseCashier;
use App\Models\TransactionDetail;
use App\Http\Controllers\Controller;
use App\Models\CloseCashierProductSold;

class ShiftController extends Controller
{
    public function open(Request $request) {

        DB::beginTransaction();

        try {
            $dateNow = Carbon::now()->format('Y-m-d');
            $checkShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->orderBy('id', 'DESC')->first();
            $roleName = auth()->user()->getRoleNames()[0];
            if($roleName != 'Kasir'){
                return response()->json(['status' => "gagal", 'message' => "Buka kasir harus dilakukan dengan role kasir."], 200);
            }
            // untuk nomor shift (1,2,3)
            $shiftNumber = 1;
            if($checkShift && $checkShift->shift_number < 3){
                $shiftNumber = $checkShift->shift_number + 1;
            }
            // check kasir sudah buka atau belum
            $checkUserShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                ->where('user_id', auth()->user()->id)
                ->where('is_closed', 0)
                ->orderBy('id', 'DESC')
                ->first();
            if($checkUserShift){
                return response()->json(['message' => "Kasir dengan user : " . $checkUserShift->user->name . " sudah dibuka sebelumnya."], 200);
            }
            // insert data ke table shift
            $shiftOpen = Shift::create([
                'shift_number' => $shiftNumber,
                'date' => Carbon::now()->format('Y-m-d'),
                'start_time' => Carbon::now()->format('Y-m-d H:i:s'),
                'opening_balance' => $request->opening_balance,
                'user_id' => auth()->user()->id,
                'warehouse_id' => auth()->user()->warehouse_id,
            ]);
            // insert stock ke table stocks per shift
            if($request->stocks){
                foreach ($request->stocks as $stock) {
                    // Stock::where('warehouse_id', auth()->user()->warehouse_id)->where('ingredient_id', $stock['ingredient_id'])->update([
                    //     'last_stock' => $stock['stock']
                    // ]);
                    Stock::create([
                        'shift_id' => $shiftOpen->id,
                        'ingredient_id' => $stock['ingredient_id'],
                        'warehouse_id' => auth()->user()->warehouse_id,
                        'first_stock' => $stock['stock'],
                        'last_stock' => $stock['stock']
                    ]);
                    // $transaction->transaction_details()->create($detail);
                }
            }
            DB::commit();
            return response()->json($shiftOpen, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function close(Request $request) {
        DB::beginTransaction();

        try {
            $dateNow = Carbon::now()->format('Y-m-d');
            // Cari shift yang open sesuai dengan kasir login
            $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                ->where('user_id', auth()->user()->id)
                ->where('is_closed', 0)
                ->with('user')
                ->first();
            if($shift == NULL){
                return response()->json(['message' => "Belum Ada Kasir Buka"], 200);
            }
            // check apakah kasir sudah tutup atau belum
            $closeCashierCheck = CloseCashier::where('shift_id', $shift->id)->where('is_closed', 1)->first();
            if($closeCashierCheck){
                return response()->json(['message' => 'Cashier Already Closed Before This'], 200);
            }
            // check apakah ada orderan belum selesai
            $checkTransactionInShift = Transaction::where('status', 'Pending')->where('shift_id', $shift->id)->whereNull('paid_amount')->whereNull('payment_method')->count();
            if($checkTransactionInShift > 0){
                return response()->json(['status' => 'gagal', 'message' => "Selesaikan orderan terlebih dahulu untuk tutup kasir"], 409);
            }
            // get transaksi sesuai shift
            $transactions = Transaction::where('status', 'Lunas')->where('shift_id', $shift->id)->get();
            $expenses = Expense::where('shift_id', $shift->id)->with('expenseCategory')->get();

            $totalExpense = 0;
            $totalCash = 0;
            $totalNonCash = 0;
            $totalProductSales = 0;
            $totalQtyPerProduct = [];
            // perhitungan total transaksi tunai & non tunai
            foreach ($transactions as $transaction) {
                if ($transaction['payment_method'] === 'Tunai') {
                    $totalCash += $transaction['total_amount'];
                }
                if ($transaction['payment_method'] !== 'Tunai') {
                    $totalNonCash += $transaction['total_amount'];
                }
                $totalProductSales += $transaction['total_qty'];
                $transactionDetails = TransactionDetail::where('transaction_id', $transaction->id)->get();
                foreach ($transactionDetails as $item) {
                    $productId = $item['product_id'];
                    // Menambahkan qty ke total qty per produk
                    if (isset($totalQtyPerProduct[$productId])) {
                        $totalQtyPerProduct[$productId] += $item['qty'];
                    } else {
                        $totalQtyPerProduct[$productId] = $item['qty'];
                    }
                }
            }
            foreach ($expenses as $expense) {
                // $totalExpense += $expense['total_price'];
                $totalExpense += $expense['amount'];
                $expense['price'] = $expense->amount / $expense->qty;
            }
            // Menyiapkan struktur data yang diinginkan
            $structuredData = [];
            foreach ($totalQtyPerProduct as $productId => $totalQty) {
                $productName = Product::find($productId)->name;

                // Menambahkan struktur data yang diinginkan
                $structuredData[] = [
                    'product_name' => $productName,
                    'qty' => $totalQty,
                ];
                CloseCashierProductSold::create([
                    'close_cashier_id' => $shift->id,
                    'product_name' => $productName,
                    'qty' => $totalQty,
                ]);
            }
            // Menghitung total money (gabungan total pendapatan tunai & QRIS)
            $totalMoney = $totalCash + $totalNonCash;
            // Update Shift Setelah tutup kasir
            $shift->update([
                'end_time' => Carbon::now()->format('Y-m-d H:i:s'),
                'closing_balance' => $request->cash_in_drawer,
                'total_transaction' => $totalMoney,
                'is_closed' => 1
            ]);
            // total omset per tipe pembayaran
            $transaction = Transaction::where('status', 'Lunas')->where('shift_id', $shift->id)->get();
            $gofood_omzet = $transaction->where('payment_method', 'GOFOOD')->sum('total_amount');
            $grabfood_omzet = $transaction->where('payment_method', 'GRABFOOD')->sum('total_amount');
            $shopeefood_omzet = $transaction->where('payment_method', 'SHOPEEFOOD')->sum('total_amount');
            $qris_omzet = $transaction->where('payment_method', 'QRIS')->sum('total_amount');
            $transfer_omzet = $transaction->where('payment_method', 'Transfer')->sum('total_amount');

            $closeCashier = CloseCashier::create([
                'shift_id' => $shift->id,
                'date' => $shift->date,
                'open_time' => $shift->start_time,
                'close_time' => Carbon::now()->format('Y-m-d H:i:s'),
                'initial_balance' => $shift->opening_balance,
                'is_closed' => 1,
                'total_cash' => $totalCash,
                'total_non_cash' => $totalNonCash,
                'gofood_omzet' => $gofood_omzet,
                'grabfood_omzet' => $grabfood_omzet,
                'shopeefood_omzet' => $shopeefood_omzet,
                'qris_omzet' => $qris_omzet,
                'transfer_omzet' => $transfer_omzet,
                'total_income' => $totalMoney,
                'total_product_sales' => $totalProductSales,
                'total_expense' => $totalExpense,
                'auto_balance' => $shift->opening_balance + $totalMoney - $totalExpense,
                'cash_in_drawer' => $request->cash_in_drawer,
                'difference' => ($totalCash - $totalExpense) + $request->cash_in_drawer
                // 'difference' => $request->cash_in_drawer - ($totalCash - $totalExpense)
                // 'difference' => $request->cash_in_drawer - (($totalCash - $totalNonCash) - $totalExpense)
                // 'difference' => ($shift->opening_balance + $totalMoney - $totalExpense) - $request->cash_in_drawer,
                // 'difference' => ($shift->opening_balance + $totalCash - $totalExpense) - $request->cash_in_drawer,
                // 'difference' => $request->cash_in_drawer - $totalCash - $totalExpense
                // 'difference' => $request->cash_in_drawer - ($totalCash - $totalExpense)
            ]);
            $closeCashier['product_sold'] = $structuredData;
            $closeCashier['expenses'] = $expenses;
            $closeCashier['shift'] = $shift;
            $closeCashier['result'] = $totalCash - $totalNonCash - $totalExpense;
            $closeCashier['cash_in_drawer_without_opening_balance'] = $request->cash_in_drawer;
            $stocks = [];

            // OjolCloseCashier data input

            $business_id = Warehouse::where('id', '=', auth()->user()->warehouse_id)->first()->business_id;
            $ojols = Ojol::where('business_id', '=', $business_id)->get();
            foreach($ojols as $ojol) {
                OjolCloseCashier::create([
                    'ojol_id' => $ojol->id,
                    'close_cashier_id' => $closeCashier->id,
                    'omzet' => $transaction->where('payment_method', $ojol->name)->sum('total_amount'),
                ]);
            }
            // End of OjolCloseCashier data input

            if ($request->stocks) {
                foreach ($request->stocks as $stock) {
                    // $ingredientStock = Stock::where('ingredient_id', $stock['ingredient_id'])->where('warehouse_id', auth()->user()->warehouse_id)->first();
                    $ingredientStock = Stock::where('shift_id', $shift->id)->where('ingredient_id', $stock['ingredient_id'])->where('warehouse_id', auth()->user()->warehouse_id)->first();

                    $stockData = [
                        'ingredient_id' => $stock['ingredient_id'],
                        'ingredient_name' => $ingredientStock->ingredient->name,
                        'first_stock' => $ingredientStock->first_stock,
                        'used_stock' => $ingredientStock->stock_used,
                        'stock_in' => $ingredientStock->stock_in,
                        'stock_real' => $ingredientStock->last_stock,
                        'stock_input' => $stock['stock'],
                        'difference_stock' => $ingredientStock->last_stock - $stock['stock'],
                    ];

                    $stocks[] = $stockData;
                    Stock::where('shift_id', $shift->id)->where('warehouse_id', auth()->user()->warehouse_id)->where('ingredient_id', $stock['ingredient_id'])->update([
                        'last_stock' => $stock['stock']
                    ]);
                }
            }
            $closeCashier['stocks'] = $stocks;
            $closeCashier['warehouse_name'] = $shift->warehouse->name;

            // Get OjolCloseCashier
            $closeCashier['ojols'] = $ojols;
            $closeCashier['ojol_omzet'] = OjolCloseCashier::where('close_cashier_id', '=', $closeCashier->id)->get();

            DB::commit();
            return response()->json($closeCashier, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function checkCashier() {
        $checkShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->where('is_closed', 0)->first();
        if($checkShift){
            return response()->json(['status' => True,'message' => "Kasir di outlet ". auth()->user()->warehouse->name . " telah buka"], 200);
        } else {
            return response()->json(['status' => False, 'message' => "Tidak ada kasir buka di outlet ". auth()->user()->warehouse->name], 200);
        }
    }

    public function closable() {
        $checkShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->where('is_closed', 0)->first();
        $transactions = Transaction::where('status', 'Pending')->where('shift_id', $checkShift->id)->whereNull('payment_method')->whereNull('paid_amount')->count();

        if($transactions > 0){
            return response()->json(['status' => False,'message' => "Tidak bisa tutup kasir, terdapat orderan yang masih tersedia."], 200);
        } else {
            return response()->json(['status' => True, 'message' => "Kasir bisa ditutup."], 200);
        }
    }

    public function latest() {
        $latestShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->orderBy('id', 'DESC')->first();
        if(!$latestShift){
            $latestShift = [];
            $latestShift['stocks'] = [];
            return response()->json($latestShift,200);
        }
        $stocks = [];
        $ingredientStock = Stock::where('shift_id', $latestShift->id)->where('warehouse_id', auth()->user()->warehouse_id)->get();
        foreach ($ingredientStock as $stock) {
            $ingredientStock = Stock::where('shift_id', $latestShift->id)->where('ingredient_id', $stock['ingredient_id'])->where('warehouse_id', auth()->user()->warehouse_id)->first();
            $ingredientName = str_replace(' ', '_', $ingredientStock->ingredient->name);

            $stockData = [
                'ingredient_id' => $stock['ingredient_id'],
                'ingredient_name' => $ingredientStock->ingredient->name,
                'first_stock' => $ingredientStock->first_stock,
                'used_stock' => $ingredientStock->stock_used,
                'stock_in' => $ingredientStock->stock_in,
                'stock' => $ingredientStock->last_stock,
            ];

            $stocks[] = $stockData;
        }
        $latestShift['stocks'] = $stocks;
        return response()->json($latestShift,200);
    }
}
