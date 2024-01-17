<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CloseCashier;
use App\Models\CloseCashierProductSold;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use DB;

class CloseCashierController extends Controller
{
    public function open() {
        DB::beginTransaction();

        try {
            $openCashier = CloseCashier::create([
                'date' => Carbon::now()->format('Y-m-d'),
                'open_time' => Carbon::now()->format('Y-m-d H:i:s'),
                'user_id' => auth()->user()->id,
                'warehouse_id' => auth()->user()->warehouse_id,
                'initial_balance' => 0,
            ]);
            session(['current_close_cashier_id' => $openCashier->id]);
            $openCashier['session_close'] = session('current_close_cashier_id');
            DB::commit();
            return response()->json($openCashier, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function close() {
        DB::beginTransaction();

        try {
            // $transaction_details = TransactionDetail::where('transaction_id', [1,2])->get();
            // return $transaction_details;

            $dateNow = Carbon::now()->format('Y-m-d');
            $openCashier = CloseCashier::where('warehouse_id', auth()->user()->warehouse_id)
                ->where('date', $dateNow)
                ->where('user_id', auth()->user()->id)
                ->where('is_closed', 0)
                ->first();

            $transactions = Transaction::where('close_cashier_id', $openCashier->id)->get();

            $totalCash = 0;
            $totalNonCash = 0;
            $totalProductSales = 0;
            $totalQtyPerProduct = [];

            foreach ($transactions as $transaction) {
                if ($transaction['payment_method'] === 'Tunai') {
                    $totalCash += $transaction['paid_amount'];
                }

                if ($transaction['payment_method'] === 'QRIS') {
                    $totalNonCash += $transaction['paid_amount'];
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
                    'close_cashier_id' => $openCashier->id,
                    'product_name' => $productName,
                    'qty' => $totalQty,
                ]);
            }

            // Menghitung total money (gabungan total pendapatan tunai & QRIS)
            $totalMoney = $totalCash + $totalNonCash;
            $closeCashier = CloseCashier::find($openCashier->id);
            $closeCashier->update([
                'close_time' => Carbon::now()->format('Y-m-d H:i:s'),
                'is_closed' => 1,
                'total_cash' => $totalCash,
                'total_non_cash' => $totalNonCash,
                'total_money' => $totalMoney,
                'total_product_sales' => $totalProductSales,
            ]);
            $closeCashier['product_sold'] = $structuredData;


            DB::commit();
            return response()->json($closeCashier, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
