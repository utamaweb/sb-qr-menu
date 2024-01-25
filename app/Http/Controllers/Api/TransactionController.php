<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Shift;
use App\Models\IngredientProducts;
use App\Models\Ingredient;
use App\Models\Warehouse;
use App\Models\TransactionDetail;
use App\Models\TransactionInOut;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'order_type_id' => 'required',
            'payment_method' => 'required',
            'transaction_details' => 'required|array|min:1', // minimal ada satu transaksi_detail
            'transaction_details.*.product_id' => 'required|numeric',
            'transaction_details.*.qty' => 'required|numeric',
            'transaction_details.*.subtotal' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();

        try {
            $total_amount = 0;
            foreach ($request->transaction_details as $detail) {
                $total_amount += $detail['subtotal'];
            }
            $total_qty = 0;
            foreach ($request->transaction_details as $detail) {
                $total_qty += $detail['qty'];
            }
            $change_money = $request->paid_amount - $total_amount;
            $dateNow = Carbon::now()->format('Y-m-d');
            $dateTimeNow = Carbon::now();
            $transactionCheck = Transaction::count();
            if ($transactionCheck < 1) {
                $sequence_number = 0;
            } else {
                $sequence_number = Transaction::where('date', $dateNow)->orderBy('id', 'DESC')->first()->sequence_number;
            }
            $shift = Shift::where('warehouse_id', auth()
            ->user()->warehouse_id)
            // ->where('date', $dateNow)
            ->where('user_id', auth()->user()->id)
            ->where('is_closed', 0)
            ->first();
            if($shift == NULL){
                return response()->json(['message' => 'Belum Ada Kasir Buka'], 500);
            }
            $transaction = Transaction::create([
                'warehouse_id' => auth()->user()->warehouse_id,
                'shift_id' => $shift->id,
                'sequence_number' => $sequence_number + 1,
                'order_type_id' => $request->order_type_id,
                'user_id' => auth()->user()->id,
                'payment_method' => $request->payment_method,
                'date' => $dateNow,
                'notes' => $request->notes,
                'total_amount' => $total_amount,
                'total_qty' => $total_qty,
                'paid_amount' => $request->paid_amount,
                'change_money' => $change_money,
            ]);

            // Simpan detail transaksi
            $transaction_details = $request->input('transaction_details');
            $transactionDetailsWithProducts = [];
            foreach ($transaction_details as $detail) {
                $productDetail = [
                    'transaction_id' => $detail['transaction_id'],
                    'product_id' => $detail['product_id'],
                    'qty' => $detail['qty'],
                    'subtotal' => $detail['subtotal'],
                    'product_name' => \App\Models\Product::find($detail['product_id'])->name,
                ];

                // Menambahkan data detail produk ke array
                $transactionDetailsWithProducts[] = $productDetail;

                $transaction->transaction_details()->create($detail);
            }
            $transaction['details'] = $transactionDetailsWithProducts;
            $transaction['warehouse'] = Warehouse::where('id', auth()->user()->warehouse_id)->first();
            // $transaction['warehouse']['name'] = $transaction['warehouse']->name;
            // $transaction['warehouse']['address'] = $transaction['warehouse']->address;
            $transaction['datetime'] = $transaction->created_at->isoFormat('D MMM Y H:m');
            $transaction['paid_at'] = $dateTimeNow->isoFormat('D MMM Y H:m');
            $transaction['product_count'] = count($request->transaction_details);


            $product_ids = [];
            foreach ($request->transaction_details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $products = Product::whereIn('id', $product_ids)->get();
            $ingredient_product_ids = IngredientProducts::whereIn('product_id', $product_ids)->get()->pluck('ingredient_id');
            $ingredient_product = IngredientProducts::whereIn('product_id', $product_ids)->get();
            // $ingredients = Ingredient::whereIn('id', $ingredient_product_ids)->get();
            $ingredients = Stock::whereIn('id', $ingredient_product_ids)->get();
            foreach ($request->transaction_details as $detail) {
                $product_id = $detail['product_id'];
                $qty = $detail['qty'];
                // Ambil produk terkait
                $product = $products->where('id', $product_id)->first();

                // Ambil bahan baku terkait melalui model Ingredient
                $ingredients = $product->ingredient;

                // foreach ($ingredients as $ingredient) {
                //     $ingredient->last_stock -= $qty;
                //     $ingredient->stock_used += $qty;
                //     $ingredient->save();
                // }
                foreach ($ingredients as $ingredient) {
                    // $ingredient->last_stock -= $qty;
                    // $ingredient->stock_used += $qty;
                    // $ingredient->save();
                    $stock = Stock::where('ingredient_id', $ingredient->id)->where('warehouse_id', auth()->user()->warehouse_id)->first();
                    if (!$stock) {
                        // Handle jika stok belum ada
                        continue;
                    }
                    $stock->last_stock -= $qty;
                    $stock->stock_used += $qty;
                    $stock->save();

                    TransactionInOut::create([
                        'warehouse_id' => auth()->user()->warehouse_id,
                        'ingredient_id' => $ingredient->id,
                        'transaction_id' => $transaction->id,
                        'qty' => $qty,
                        'transaction_type' => 'out',
                        'user_id' => auth()->user()->id,
                    ]);
                }
            }
            $transaction['order_type'] = $transaction->orderType;
            $transaction['order_type_name'] = $transaction['order_type']['name'];


            DB::commit();
            return response()->json($transaction, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
