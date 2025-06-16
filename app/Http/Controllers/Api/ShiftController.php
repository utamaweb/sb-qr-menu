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
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function open(Request $request)
    {

        $checkStocks = Stock::where('warehouse_id', auth()->user()->warehouse_id)->exists();

        if ($checkStocks) {
            $data = $request->all();
            $validator = Validator::make($data, [
                'stocks' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages()], 400);
            }
        }


        DB::beginTransaction();

        try {
            $dateNow = Carbon::now()->format('Y-m-d');
            $checkShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->orderBy('id', 'DESC')->first();
            $roleName = auth()->user()->getRoleNames()[0];
            if ($roleName != 'Kasir') {
                return response()->json(['status' => "gagal", 'message' => "Buka kasir harus dilakukan dengan role kasir."], 200);
            }

            if($request->shift_number) {
                $shiftNumber = $request->shift_number;
            } else {
                // untuk nomor shift (1,2,3)
                $shiftNumber = 1;
                if ($checkShift && $checkShift->shift_number < auth()->user()->warehouse->max_shift_count) {
                    $shiftNumber = $checkShift->shift_number + 1;
                }
            }

            // check kasir sudah buka atau belum
            $checkUserShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                ->where('user_id', auth()->user()->id)
                ->where('is_closed', 0)
                ->orderBy('id', 'DESC')
                ->first();
            if ($checkUserShift) {
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
            if ($request->stocks) {
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
            \Log::emergency("File:" . $th->getFile() . " Line:" . $th->getLine() . " Message:" . $th->getMessage());
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    // closeUpdate()
    public function close(Request $request)
    {
        DB::beginTransaction();

        try {
            $dateNow = Carbon::now()->format('Y-m-d');

            // Cari shift yang open sesuai dengan kasir login
            $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                ->where('user_id', auth()->user()->id)
                ->where('is_closed', 0)
                ->with('user')
                ->first();

            if (is_null($shift)) {
                return response()->json(['message' => "Belum Ada Kasir Buka"], 500);
            }

            // Cek apakah kasir sudah tutup
            if (CloseCashier::where('shift_id', $shift->id)->where('is_closed', 1)->exists()) {
                return response()->json(['message' => 'Cashier Already Closed Before This'], 500);
            }

            // Cek jika ada orderan belum selesai
            if (Transaction::where('status', 'Pending')->where('shift_id', $shift->id)->exists()) {
                return response()->json(['status' => 'gagal', 'message' => "Selesaikan orderan terlebih dahulu untuk tutup kasir"], 500);
            }

            // Ambil transaksi dan expenses sesuai shift
            $transactions = Transaction::where('status', 'Lunas')->where('shift_id', $shift->id)->with('transaction_details')->get();
            $expenses = Expense::where('shift_id', $shift->id)->with('expenseCategory')->get();

            $totalExpense = $expenses->sum('amount');
            $totalCash = $transactions->where('payment_method', 'Tunai')->sum('total_amount');
            $totalNonCash = $transactions->where('payment_method', '!=', 'Tunai')->sum('total_amount');
            $totalProductSales = $transactions->sum('total_qty');
            $totalMoney = $totalCash + $totalNonCash;

            foreach ($expenses as $expense) {
                // $totalExpense += $expense['total_price'];
                // $totalExpense += $expense['amount'];
                if ($expense->qty == 0) {
                    continue;
                } else {
                    $expense['price'] = round($expense->amount / $expense->qty);
                }
            }

            // Update Shift Setelah tutup kasir
            $shift->update([
                'end_time' => Carbon::now()->format('Y-m-d H:i:s'),
                'closing_balance' => $request->cash_in_drawer,
                'total_transaction' => $totalMoney,
                'is_closed' => 1
            ]);

            // Total omset per tipe pembayaran
            $paymentTypes = ['GOFOOD', 'GRABFOOD', 'SHOPEEFOOD', 'QRIS', 'Transfer'];
            $omzet = [];
            foreach ($paymentTypes as $type) {
                $omzet[strtolower($type) . '_omzet'] = $transactions->where('payment_method', $type)->sum('total_amount');
            }

            // Buat CloseCashier
            $closeCashier = CloseCashier::create(array_merge([
                'shift_id' => $shift->id,
                'date' => $shift->date,
                'open_time' => $shift->start_time,
                'close_time' => Carbon::now()->format('Y-m-d H:i:s'),
                'initial_balance' => $shift->opening_balance,
                'is_closed' => 1,
                'total_cash' => $totalCash,
                'total_non_cash' => $totalNonCash,
                'total_income' => $totalMoney,
                'total_product_sales' => $totalProductSales,
                'total_expense' => $totalExpense,
                'auto_balance' => $shift->opening_balance + $totalMoney - $totalExpense,
                'cash_in_drawer' => $request->cash_in_drawer,
                'difference' => $request->cash_in_drawer - ($totalCash - $totalExpense),
            ], $omzet));

            // Data produk terjual
            $totalQtyPerProduct = [];
            foreach ($transactions as $transaction) {
                foreach ($transaction->transaction_details as $item) {
                    $totalQtyPerProduct[$item->product_id] = ($totalQtyPerProduct[$item->product_id] ?? 0) + $item->qty;
                }
            }

            // Insert CloseCashierProductSold
            $structuredData = [];
            foreach ($totalQtyPerProduct as $productId => $totalQty) {
                $productName = Product::find($productId)->name;
                $structuredData[] = ['product_name' => $productName, 'qty' => $totalQty];
                CloseCashierProductSold::create([
                    'close_cashier_id' => $closeCashier->id,
                    'product_name' => $productName,
                    'qty' => $totalQty,
                ]);
            }

            // OjolCloseCashier data input
            $ojols = Ojol::where('business_id', Warehouse::where('id', auth()->user()->warehouse_id)->value('business_id'))->get();
            foreach ($ojols as $ojol) {
                OjolCloseCashier::create([
                    'ojol_id' => $ojol->id,
                    'close_cashier_id' => $closeCashier->id,
                    'omzet' => $transactions->where('payment_method', $ojol->name)->sum('total_amount'),
                ]);
            }

            // Update atau buat stok
            $stocks = [];
            foreach ($request->stocks as $stock) {
                $ingredientStock = Stock::firstOrCreate(
                    ['shift_id' => $shift->id, 'ingredient_id' => $stock['ingredient_id'], 'warehouse_id' => auth()->user()->warehouse_id],
                    ['first_stock' => Stock::where('warehouse_id', auth()->user()->warehouse_id)->where('ingredient_id', $stock['ingredient_id'])->latest()->value('last_stock')]
                );
                $brokenStock = $stock['broken_stock'] ?? 0;
                $stockData = [
                    'ingredient_id' => $stock['ingredient_id'],
                    'ingredient_name' => $ingredientStock->ingredient->name,
                    'first_stock' => $ingredientStock->first_stock,
                    'used_stock' => $ingredientStock->stock_used,
                    'stock_in' => $ingredientStock->stock_in,
                    'stock_real' => $ingredientStock->last_stock,
                    'stock_close_input' => $stock['stock'],
                    'broken_stock' => $brokenStock,
                    'difference_stock' => $stock['stock'] + $brokenStock - $ingredientStock->last_stock,
                ];

                $stocks[] = $stockData;
                $ingredientStock->update([
                    'stock_close_input' => $stock['stock'],
                    'broken_stock' => $brokenStock,
                    'difference_stock' => $stockData['difference_stock'],
                ]);
            }

            // Menyusun response
            $closeCashier->load('shift.user');
            $response = $closeCashier->toArray();
            $response['product_sold'] = $structuredData;
            $response['expenses'] = $expenses;
            $response['stocks'] = $stocks;
            $response['ojols'] = $ojols;
            $response['ojol_omzet'] = OjolCloseCashier::where('close_cashier_id', $closeCashier->id)->get();
            $response['warehouse_name'] = $shift->warehouse->name;
            $response['result'] = $totalCash - $totalNonCash - $totalExpense;
            $response['cash_in_drawer_without_opening_balance'] = $request->cash_in_drawer;

            DB::commit();
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::emergency("File:" . $th->getFile() . " Line:" . $th->getLine() . " Message:" . $th->getMessage());
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }


    public function checkCashier()
    {
        $checkShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->where('is_closed', 0)->first();
        if ($checkShift) {
            return response()->json(['status' => True, 'message' => "Kasir di outlet " . auth()->user()->warehouse->name . " telah buka"], 200);
        } else {
            return response()->json(['status' => False, 'message' => "Tidak ada kasir buka di outlet " . auth()->user()->warehouse->name], 200);
        }
    }

    public function closable()
    {
        $checkShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->where('is_closed', 0)->first();
        $transactions = Transaction::where('status', 'Pending')->where('shift_id', $checkShift->id)->whereNull('payment_method')->whereNull('paid_amount')->count();

        if ($transactions > 0) {
            return response()->json(['status' => False, 'message' => "Tidak bisa tutup kasir, terdapat orderan yang masih tersedia."], 200);
        } else {
            return response()->json(['status' => True, 'message' => "Kasir bisa ditutup."], 200);
        }
    }

    public function latest()
    {
        $latestShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->orderBy('id', 'DESC')->first();
        if (!$latestShift) {
            $latestShift = [];
            $latestShift['stocks'] = [];
            return response()->json($latestShift, 200);
        }

        // Eager load the ingredient relationship
        $ingredientStock = Stock::where('shift_id', $latestShift->id)->get();
        // $ingredientStock = Stock::with('ingredient')
        //     ->where('warehouse_id', auth()->user()->warehouse_id)
        //     ->orderBy('id', 'DESC')
        //     ->get()
        //     ->unique('ingredient_id');

        $stocks = $ingredientStock->map(function ($stock) {
            return [
                'ingredient_id' => $stock->ingredient_id,
                'ingredient_name' => $stock->ingredient->name,
                'first_stock' => $stock->first_stock,
                'used_stock' => $stock->stock_used,
                'stock_in' => $stock->stock_in,
                'stock' => $stock->stock_close_input,
            ];
        });

        $latestShift['stocks'] = $stocks;
        return response()->json($latestShift, 200);
    }
}
