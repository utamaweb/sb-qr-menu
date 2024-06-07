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
use App\Models\OrderType;
use App\Models\Warehouse;
use App\Models\TransactionDetail;
use App\Models\TransactionInOut;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Events\TransactionNotPaid;
use App\Events\TransactionPaid;
use App\Events\TransactionCancelled;
use App\Events\RefreshTransactions;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // get history online transaction
    public function online()
    {
        // $dateNow = Carbon::now()->format('Y-m-d');
        $userId = auth()->user()->id;
        $warehouseId = auth()->user()->warehouse_id;

        $shift = Shift::where('warehouse_id', $warehouseId)
            ->where('user_id', $userId)
            ->where('is_closed', 0)
            ->first();

        if (!$shift) {
            return response()->json([], 200);
        }

        $transactions = Transaction::with(['order_type:id,name', 'transaction_details' => function($query) {
            $query->select('transaction_id', 'product_id', 'product_price', 'qty', 'subtotal')
                ->with('product:id,name');
        }])
        ->where('shift_id', $shift->id)
        ->where('warehouse_id', $warehouseId)
        ->where('category_order', 'ONLINE')
        ->orderByDesc('id')
        ->get();

        $formattedTransactions = $transactions->map(function($transaction) {
            return [
                'id' => $transaction->id,
                'sequence_number' => $transaction->sequence_number,
                'order_type' => $transaction->order_type->name,
                'category_order' => $transaction->category_order,
                'payment_method' => $transaction->payment_method,
                'total_amount' => $transaction->total_amount,
                'paid_time' => $transaction->created_at->format('Y-m-d H:i:s'),
                'status' => $transaction->status,
                'items' => $transaction->transaction_details->map(function($detail) {
                    return [
                        'transaction_id' => $detail->transaction_id,
                        'product_id' => $detail->product_id,
                        'product_name' => $detail->product->name,
                        'price' => $detail->product_price,
                        'qty' => $detail->qty,
                        'subtotal' => $detail->subtotal,
                    ];
                })->toArray(),
            ];
        });

        return response()->json($formattedTransactions, 200);
    }


    // get history offline transaciton
    public function offline()
    {
        // $dateNow = Carbon::now()->format('Y-m-d');
        $userId = auth()->user()->id;
        $warehouseId = auth()->user()->warehouse_id;

        $shift = Shift::where('warehouse_id', $warehouseId)
            ->where('user_id', $userId)
            ->where('is_closed', 0)
            ->first();

        if (!$shift) {
            return response()->json([], 200);
        }

        $transactions = Transaction::with(['order_type:id,name', 'transaction_details' => function($query) {
            $query->select('transaction_id', 'product_id', 'product_price', 'qty', 'subtotal')
                ->with('product:id,name');
        }])
        ->where('shift_id', $shift->id)
        ->where('warehouse_id', $warehouseId)
        ->where('category_order', 'OFFLINE')
        ->orderByDesc('id')
        ->get();

        $formattedTransactions = $transactions->map(function($transaction) {
            return [
                'id' => $transaction->id,
                'sequence_number' => $transaction->sequence_number,
                'order_type' => $transaction->order_type->name,
                'category_order' => $transaction->category_order,
                'payment_method' => $transaction->payment_method,
                'total_amount' => $transaction->total_amount,
                'paid_time' => $transaction->created_at->format('Y-m-d H:i:s'),
                'status' => $transaction->status,
                'items' => $transaction->transaction_details->map(function($detail) {
                    return [
                        'transaction_id' => $detail->transaction_id,
                        'product_id' => $detail->product_id,
                        'product_name' => $detail->product->name,
                        'price' => $detail->product_price,
                        'qty' => $detail->qty,
                        'subtotal' => $detail->subtotal,
                    ];
                })->toArray(),
            ];
        });

        return response()->json($formattedTransactions, 200);
    }


    public function notPaid()
    {
        // $dateNow = Carbon::now()->format('Y-m-d');
        $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
            ->where('is_closed', 0)
            ->first();

        if (!$shift) {
            return response()->json([], 200);
        }

        $transactions = Transaction::with(['order_type:id,name', 'transaction_details' => function($query) {
            $query->select('transaction_id', 'product_id', 'product_price', 'qty', 'subtotal')
                ->with('product:id,name');
        }])
        ->where('warehouse_id', auth()->user()->warehouse_id)
        ->where('shift_id', $shift->id)
        ->where('status', 'Pending')
        ->whereNull('payment_method')
        ->orderByDesc('id')
        ->get();

        $formattedTransactions = $transactions->map(function($transaction) {
            return [
                'id' => $transaction->id,
                'sequence_number' => $transaction->sequence_number,
                'order_type' => $transaction->order_type->name,
                'payment_method' => $transaction->payment_method,
                'total_amount' => $transaction->total_amount,
                'paid_time' => $transaction->created_at->format('Y-m-d H:i:s'),
                'status' => $transaction->status,
                'items' => $transaction->transaction_details->map(function($detail) {
                    return [
                        'transaction_id' => $detail->transaction_id,
                        'product_id' => $detail->product_id,
                        'product_name' => $detail->product->name,
                        'price' => $detail->product_price,
                        'qty' => $detail->qty,
                        'subtotal' => $detail->subtotal,
                    ];
                })->toArray(),
            ];
        });
        // event(new RefreshTransactions($formattedTransactions));

        return response()->json($formattedTransactions, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Step 1. Create Transaction
            if($request->transaction_details){
                $total_amount = 0;
                foreach ($request->transaction_details as $detail) {
                    $total_amount += $detail['subtotal'];
                }
                $total_qty = 0;
                foreach ($request->transaction_details as $detail) {
                    $total_qty += $detail['qty'];
                }
                // $change_money = $request->paid_amount - $total_amount;
                $dateNow = Carbon::now()->format('Y-m-d');
                $dateTimeNow = Carbon::now();
                // $transactionCheck = Transaction::where('date', $dateNow)->orderBy('id', "DESC")->count();
                $checkShiftOpen = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                // ->where('date', $dateNow)
                // ->where('user_id', auth()->user()->id)
                ->where('is_closed', 0)
                ->first();
                if($checkShiftOpen == NULL){
                    return response()->json(['message' => 'Belum Ada Kasir Buka'], 500);
                }
                $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                ->where('is_closed', 0)
                // ->orderBy('id', 'desc')
                ->first();

                $countTransactionInShift = Transaction::where('shift_id', $shift->id)->orderBy('id', 'DESC')->count();
                $lastTransaction = Transaction::where('shift_id', $shift->id)->orderBy('id', 'DESC')->first();
                if ($countTransactionInShift > 0) {
                    $sequence_number = $lastTransaction->sequence_number + 1;
                } else {
                    $sequence_number = 1;
                }
                // return $sequence_number;

                // Insert ke table transaction (step 1 : buat transaksi)
                $transaction = Transaction::create([
                    'warehouse_id' => auth()->user()->warehouse_id,
                    'shift_id' => $checkShiftOpen->id,
                    'sequence_number' => $sequence_number,
                    'order_type_id' => $request->order_type_id,
                    'category_order' => $request->category_order,
                    'user_id' => auth()->user()->id,
                    // 'payment_method' => $request->payment_method,
                    'date' => $dateNow,
                    'notes' => $request->notes,
                    'total_amount' => $total_amount,
                    'total_qty' => $total_qty,
                    'status' => 'Pending',
                    // 'paid_amount' => $request->paid_amount,
                    // 'change_money' => $change_money,
                ]);

                // Simpan detail transaksi
                $transaction_details = $request->input('transaction_details');
                $transactionDetailsWithProducts = [];
                foreach ($transaction_details as $detail) {
                    $productDetail = [
                        'transaction_id' => $transaction->id,
                        'product_id' => $detail['product_id'],
                        'qty' => $detail['qty'],
                        'subtotal' => $detail['subtotal'],
                        // 'product_name' => \App\Models\Product::find($detail['product_id'])->name,
                        'product_name' => $detail['product_name'],
                        'product_price' => $detail['product_price'],
                    ];

                    // Menambahkan data detail produk ke array
                    $transactionDetailsWithProducts[] = $productDetail;

                    $transaction->transaction_details()->create($detail);
                }
                $transaction['details'] = $transactionDetailsWithProducts;
                $transaction['warehouse'] = Warehouse::where('id', auth()->user()->warehouse_id)->first();
                $transaction['datetime'] = $transaction->created_at->isoFormat('D MMM Y H:m');
                // $transaction['paid_at'] = $dateTimeNow->isoFormat('D MMM Y H:m');
                $transaction['product_count'] = count($request->transaction_details);

                $transaction['order_type'] = $transaction->order_type;
                $transaction['order_type_name'] = $transaction['order_type']['name'];
                DB::commit();
                // event(new TransactionNotPaid([
                //     'id' => $transaction->id,
                //     'sequence_number' => $transaction->sequence_number,
                //     'order_type' => $transaction->order_type->name,
                //     'payment_method' => $transaction->payment_method,
                //     'total_amount' => $transaction->total_amount,
                //     'paid_time' => $transaction->created_at->format('Y-m-d H:i:s'),
                //     'status' => $transaction->status,
                //     'items' => $transaction->transaction_details->map(function($detail) {
                //         return [
                //             'transaction_id' => $detail->transaction_id,
                //             'product_id' => $detail->product_id,
                //             'product_name' => $detail->product->name,
                //             'price' => $detail->product_price,
                //             'qty' => $detail->qty,
                //             'subtotal' => $detail->subtotal,
                //         ];
                //     })->toArray(),
                // ]));

                return response()->json($transaction, 200);
            // Step 2. Payment Transaction
            } else {
                $dateNow = Carbon::now()->format('Y-m-d');
                $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->where('is_closed', 0)->first();
                $transaction = Transaction::findOrFail($request->transaction_id);
                // Simpan detail transaksi
                $transaction_details = $request->input('payment_details');
                $transactionDetailsWithProducts = [];
                $product_ids = [];
                foreach ($transaction_details as $detail) {
                    array_push($product_ids, $detail['product_id']);
                }
                $products = Product::whereIn('id', $product_ids)->get();
                foreach ($transaction_details as $detail) {
                    // get deal with stock
                    $product_id = $detail['product_id'];
                    $product_qty = $detail['qty'];
                    // Ambil produk terkait
                    $product = $products->where('id', $product_id)->first();

                    // Ambil bahan baku terkait melalui model Ingredient
                    $ingredients = $product->ingredient;

                    foreach ($ingredients as $ingredient) {
                        $stock = Stock::where('shift_id', $shift->id)->where('ingredient_id', $ingredient->id)->where('warehouse_id', auth()->user()->warehouse_id)->first();
                        $productIngredientQty = IngredientProducts::where('product_id', '=', $product->id)->where('ingredient_id', '=', $ingredient->id)->pluck('qty')->min();
                        if (!$stock) {
                            // Handle jika stok belum ada
                            // continue;

                            $getLastStock = Stock::where('warehouse_id', auth()->user()->warehouse_id)->where('ingredient_id', '=', $ingredient->id)->orderBy('id', 'DESC')->first();

                            $stock = Stock::create([
                                'warehouse_id' => auth()->user()->warehouse_id,
                                'ingredient_id' => $ingredient->id,
                                'shift_id' => $shift->id,
                                'first_stock' => $getLastStock->last_stock,
                                'last_stock' => $getLastStock->last_stock,
                            ]);
                        }

                        if ($stock->last_stock < ($productIngredientQty * $product_qty)) {
                            // Jika stok kurang dari qty, return peringatan
                            DB::rollback();
                            return response()->json(['message' => 'Stok bahan baku ' . $ingredient->name . ' tidak mencukupi.'], 200);
                        }

                        $stock->last_stock -= ($productIngredientQty * $product_qty);
                        $stock->stock_used += ($productIngredientQty * $product_qty);
                        $stock->save();
                        // Insert ke table transaction in out
                        TransactionInOut::create([
                            'warehouse_id' => auth()->user()->warehouse_id,
                            'ingredient_id' => $ingredient->id,
                            'transaction_id' => $transaction->id,
                            'qty' => $product_qty,
                            'date' => $dateNow,
                            'transaction_type' => 'out',
                            'user_id' => auth()->user()->id,
                        ]);
                    }
                    // end get deal with stock
                    $productDetail = [
                        'transaction_id' => $transaction->id,
                        'product_id' => $detail['product_id'],
                        'qty' => $detail['qty'],
                        'subtotal' => $detail['subtotal'],
                    ];

                    // Menambahkan data detail produk ke array
                    $transactionDetailsWithProducts[] = $productDetail;
                    $detail_transaction = TransactionDetail::where('product_id', $detail['product_id'])->where('transaction_id', $transaction->id)->update([
                        'qty' => $detail['qty'],
                        'subtotal' => $detail['subtotal']
                    ]);
                }
                $total_amount = 0;
                foreach ($request->payment_details as $detail) {
                    $total_amount += $detail['subtotal'];
                }
                $total_qty = 0;
                foreach ($request->payment_details as $detail) {
                    $total_qty += $detail['qty'];
                }
                $transaction->update([
                    'total_amount' => $total_amount,
                    'total_qty' => $total_qty,
                    // 'status' => 'Lunas'
                ]);

                // $change_money = $request->paid_amount - $transaction->total_amount;
                // if($transaction->paid_amount < $transaction->total_amount){

                // update transaction status, etc lunas
                if($transaction->status == 'Pending'){

                    $transaction->update([
                        'payment_method' => $request->payment_method,
                        'paid_amount' => $request->paid_amount,
                        'change_money' => $request->change_money,
                        'status' => 'Lunas',
                    ]);
                    DB::commit();
                    // event(new TransactionPaid($transaction->id));
                } else {
                    return response()->json(['message' => 'Transaksi ini telah dibayar lunas'], 200);
                }
                $transaction['details'] = $transactionDetailsWithProducts;
                return response()->json($transaction, 200);
            }


        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }


    public function storeOnline(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            // 'order_type_id' => 'required',
            // 'payment_method' => 'required',
            // 'transaction_details' => 'required|array|min:1', // minimal ada satu transaksi_detail
            // 'transaction_details.*.product_id' => 'required|numeric',
            // 'transaction_details.*.qty' => 'required|numeric',
            // 'transaction_details.*.subtotal' => 'required|numeric',
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
            // $change_money = $request->paid_amount - $total_amount;
            $dateNow = Carbon::now()->format('Y-m-d');
            // $dateTimeNow = Carbon::now();
            $checkShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
            // ->where('date', $dateNow)
            // ->where('user_id', auth()->user()->id)
            ->where('is_closed', 0)
            ->first();
            if($checkShift == NULL){
                return response()->json(['message' => 'Belum Ada Kasir Buka'], 200);
            }

            $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                ->where('is_closed', 0)
                // ->orderBy('id', 'desc')
                ->first();

            $countTransactionInShift = Transaction::where('shift_id', $shift->id)->orderBy('id', 'DESC')->count();
            $lastTransaction = Transaction::where('shift_id', $shift->id)->orderBy('id', 'DESC')->first();
            if ($countTransactionInShift > 0) {
                $sequence_number = $lastTransaction->sequence_number + 1;
            } else {
                $sequence_number = 1;
            }

            // $change_money = $request->paid_amount - $total_amount;
            // Insert ke table transaction (step 1 : buat transaksi)
            $transaction = Transaction::create([
                'warehouse_id' => auth()->user()->warehouse_id,
                'shift_id' => $shift->id,
                'sequence_number' => $sequence_number,
                'order_type_id' => $request->order_type_id,
                'category_order' => 'ONLINE',
                'user_id' => auth()->user()->id,
                'payment_method' => $request->payment_method,
                'date' => $dateNow,
                'notes' => $request->notes,
                'total_amount' => $total_amount,
                'total_qty' => $total_qty,
                // 'paid_amount' => $request->paid_amount,
                'paid_amount' => $total_amount,
                'status' => 'Lunas',
                // 'change_money' => $change_money,
            ]);


            // Simpan detail transaksi
            $transaction_details = $request->input('transaction_details');
            $transactionDetailsWithProducts = [];
            foreach ($transaction_details as $detail) {
                $productDetail = [
                    'transaction_id' => $transaction->id,
                    'product_id' => $detail['product_id'],
                    'qty' => $detail['qty'],
                    'subtotal' => $detail['subtotal'],
                    // 'product_name' => \App\Models\Product::find($detail['product_id'])->name,
                    'product_name' => $detail['product_name'],
                    'product_price' => $detail['product_price'],
                ];

                // Menambahkan data detail produk ke array
                $transactionDetailsWithProducts[] = $productDetail;

                $transaction->transaction_details()->create($detail);
            }
            $transaction['details'] = $transactionDetailsWithProducts;
            $transaction['warehouse'] = Warehouse::where('id', auth()->user()->warehouse_id)->first();
            $transaction['datetime'] = $transaction->created_at->isoFormat('D MMM Y H:m');
            // $transaction['paid_at'] = $dateTimeNow->isoFormat('D MMM Y H:m');
            $transaction['product_count'] = count($request->transaction_details);

            $product_ids = [];
            foreach ($request->transaction_details as $detail) {
                array_push($product_ids, $detail['product_id']);
            }
            $products = Product::whereIn('id', $product_ids)->get();
            $ingredient_product_ids = IngredientProducts::whereIn('product_id', $product_ids)->get()->pluck('ingredient_id');
            // $ingredient_product = IngredientProducts::whereIn('product_id', $product_ids)->get();
            // $ingredients = Ingredient::whereIn('id', $ingredient_product_ids)->get();
            $ingredients = Stock::whereIn('id', $ingredient_product_ids)->get();
            foreach ($request->transaction_details as $detail) {
                $product_id = $detail['product_id'];
                $qty = $detail['qty'];
                // Ambil produk terkait
                $product = $products->where('id', $product_id)->first();

                // Ambil bahan baku terkait melalui model Ingredient
                $ingredients = $product->ingredient;

                foreach ($ingredients as $ingredient) {
                    $stock = Stock::where('shift_id', $shift->id)->where('ingredient_id', $ingredient->id)->where('warehouse_id', auth()->user()->warehouse_id)->first();
                    $productIngredientQty = IngredientProducts::where('product_id', '=', $product->id)->where('ingredient_id', '=', $ingredient->id)->pluck('qty')->min();
                    if (!$stock) {
                        // Handle jika stok belum ada
                        // continue;

                        $getLastStock = Stock::where('warehouse_id', auth()->user()->warehouse_id)->where('ingredient_id', '=', $ingredient->id)->orderBy('id', 'DESC')->first();

                        $stock = Stock::create([
                            'warehouse_id' => auth()->user()->warehouse_id,
                            'ingredient_id' => $ingredient->id,
                            'shift_id' => $shift->id,
                            'first_stock' => $getLastStock->last_stock,
                            'last_stock' => $getLastStock->last_stock,
                        ]);
                    }

                    if ($stock->last_stock < ($productIngredientQty * $qty)) {
                        // Jika stok kurang dari qty, return peringatan
                        DB::rollback();
                        return response()->json(['message' => 'Stok bahan baku ' . $ingredient->name . ' tidak mencukupi.'], 200);
                    }

                    $stock->last_stock -= ($productIngredientQty * $qty);
                    $stock->stock_used += ($productIngredientQty * $qty);
                    $stock->save();
                    // Insert ke table transaction in out
                    TransactionInOut::create([
                        'warehouse_id' => auth()->user()->warehouse_id,
                        'ingredient_id' => $ingredient->id,
                        'transaction_id' => $transaction->id,
                        'qty' => $qty,
                        'date' => $dateNow,
                        'transaction_type' => 'out',
                        'user_id' => auth()->user()->id,
                    ]);
                }
                // end get deal with stock
            }
            $transaction['order_type'] = $transaction->order_type;
            $transaction['order_type_name'] = $transaction['order_type']['name'];
            DB::commit();
            return response()->json($transaction, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }


    public function cancel(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::find($id);
            if($transaction){

                // $shift = Shift::find($transaction->shift_id);
                // $details = TransactionDetail::where('transaction_id', $id)->get();
                // Update stock sesuai stok sebelum cancel
                // foreach($details as $detail){
                //     $product_id = $detail->product_id;
                //     $ingredientProducts = IngredientProducts::where('product_id', $product_id)->get();
                //     foreach($ingredientProducts as $ingredientProduct){
                //         $stock = Stock::where('ingredient_id', $ingredientProduct->ingredient_id)->where('warehouse_id', $shift->warehouse_id)->where('shift_id', $shift->id)->first();
                //         $stock->update([
                //             'stock_used' => $stock->stock_used - $detail->qty,
                //             'last_stock' => $stock->last_stock + $detail->qty
                //         ]);
                //     }
                // }
                // Update status transaksi menjadi batal
                $transaction->update([
                    'status' => 'Batal',
                ]);
                DB::commit();
                // event(new TransactionCancelled($transaction->id));
                return response()->json(['message' => 'Transaksi Berhasil Dibatalkan'], 200);
            } else {
                return response()->json(['message' => 'Data transaksi tidak ada'], 200);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function orderType()
    {
        $orderTypes = OrderType::get();
        return response()->json($orderTypes, 200);
    }

    public function checkQty(Request $request)
    {
        $warehouseId = auth()->user()->warehouse_id;
        $productId = $request->product_id;

        // Query untuk mendapatkan produk dan harga dari warehouse dengan eager loading untuk bahan baku
        $product = Product::with('ingredient')->find($productId);

        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Produk tersebut tidak berada di outlet ini'], 200);
        }

        // Check shift
        $shift = Shift::where('warehouse_id', $warehouseId)
            ->where('is_closed', 0)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$shift) {
            return response()->json(['message' => 'Kasir belum buka'], 200);
        }

        // Ambil stok terakhir untuk setiap bahan baku di gudang tertentu
        $ingredientStocks = $product->ingredient->mapWithKeys(function ($ingredient) use ($shift, $warehouseId, $productId) {
            $productIngredientQty = IngredientProducts::where('product_id', '=', $productId)->where('ingredient_id', '=', $ingredient->id)->pluck('qty')->toArray();
            $lastStock = Stock::where('ingredient_id', $ingredient->id)
                ->where('shift_id', $shift->id)
                ->where('warehouse_id', $warehouseId)
                ->first();

            return [$ingredient->id => $lastStock ? $lastStock->last_stock / min($productIngredientQty) : 0];
        });

        // Ambil stok terkecil dari semua bahan baku
        $smallestStock = $ingredientStocks->isNotEmpty() ? floor($ingredientStocks->min()) : 0;

        // Tambahkan qty terkecil ke dalam produk
        $product->qty = $smallestStock;

        if ($smallestStock < $request->qty) {
            return response()->json(['status' => false, 'message' => "Stok yang tersedia hanya " . $smallestStock], 200);
        } else {
            return response()->json(['status' => true, 'message' => "Stok Tersedia"], 200);
        }
    }

}
