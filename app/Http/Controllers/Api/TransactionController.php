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
use App\Models\Product_Warehouse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Events\TransactionNotPaid;
use App\Events\TransactionPaid;
use App\Events\TransactionCancelled;
use App\Events\RefreshTransactions;
use App\Services\WhatsappService;
use GuzzleHttp\Client;


class TransactionController extends Controller
{
    protected $whatsapp;

    public function __construct()
    {
        $this->whatsapp = new WhatsappService();
    }

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

        $transactions = Transaction::with(['order_type:id,name', 'transaction_details' => function ($query) {
            $query->select('transaction_id', 'product_id', 'product_price', 'qty', 'subtotal')
                ->with('product:id,name');
        }])
            ->where('shift_id', $shift->id)
            ->where('warehouse_id', $warehouseId)
            ->where('category_order', 'ONLINE')
            ->orderByDesc('id')
            ->get();

        $formattedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'sequence_number' => $transaction->sequence_number,
                'order_type' => $transaction->order_type->name,
                'category_order' => $transaction->category_order,
                'payment_method' => $transaction->payment_method,
                'total_amount' => $transaction->total_amount,
                'paid_time' => $transaction->created_at->format('Y-m-d H:i:s'),
                'status' => $transaction->status,
                'items' => $transaction->transaction_details->map(function ($detail) {
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

        $transactions = Transaction::with(['order_type:id,name', 'transaction_details' => function ($query) {
            $query->select('transaction_id', 'product_id', 'product_price', 'qty', 'subtotal')
                ->with('product:id,name');
        }])
            ->where('shift_id', $shift->id)
            ->where('warehouse_id', $warehouseId)
            ->where('category_order', 'OFFLINE')
            ->orderByDesc('id')
            ->get();

        $formattedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'sequence_number' => $transaction->sequence_number,
                'order_type' => $transaction->order_type->name,
                'category_order' => $transaction->category_order,
                'payment_method' => $transaction->payment_method,
                'total_amount' => $transaction->total_amount,
                'paid_time' => $transaction->created_at->format('Y-m-d H:i:s'),
                'status' => $transaction->status,
                'items' => $transaction->transaction_details->map(function ($detail) {
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

        $transactions = Transaction::with(['order_type:id,name', 'transaction_details' => function ($query) {
            $query->select('transaction_id', 'product_id', 'product_price', 'qty', 'subtotal')
                ->with('product:id,name');
        }])
            ->where('warehouse_id', auth()->user()->warehouse_id)
            ->where('shift_id', $shift->id)
            ->where('status', 'Pending')
            ->whereNull('payment_method')
            ->orderByDesc('id')
            ->get();

        $formattedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'sequence_number' => $transaction->sequence_number,
                'order_type' => $transaction->order_type->name,
                'payment_method' => $transaction->payment_method,
                'total_amount' => $transaction->total_amount,
                'paid_time' => $transaction->created_at->format('Y-m-d H:i:s'),
                'status' => $transaction->status,
                'items' => $transaction->transaction_details->map(function ($detail) {
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
            if ($request->category_order) {
                $data = $request->all();
                $validator = Validator::make($data, [
                    'transaction_details' => 'required',
                    'order_type_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => 'Pilihan Menu & Jenis Pesanan Tidak Boleh Kosong'], 500);
                }
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
                if ($checkShiftOpen == NULL) {
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

            } else { // Step 2. Payment Transaction
                // Get data from request
                $data = $request->all();

                // Validation
                $validator = Validator::make($data, [
                    'payment_details' => 'required|array',
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => 'Pilihan Menu Tidak Boleh Kosong'], 500);
                }

                // Get shift
                $shift = DB::select("SELECT * FROM shifts
                    WHERE warehouse_id = " . auth()->user()->warehouse_id .
                    " AND is_closed = 0
                    ORDER BY id DESC
                    LIMIT 1");

                // Shift check
                if (collect($shift)->count() > 0) {
                    $shift = collect($shift)->first();
                } else {
                    return response()->json(['message' => 'Kasir belum buka'], 500);
                }

                // Get stocks
                $stocks = Stock::where('shift_id', $shift->id)->get();

                // Get transaction
                $transaction = Transaction::with('transaction_details', 'transaction_details.product', 'transaction_details.product.ingredient')
                    ->findOrFail($request->transaction_id);

                // Payment details from request
                $payment_details = collect($request->payment_details);

                // Sync transaction details with payment details
                foreach ($transaction->transaction_details as $detail) {
                    $product_id = $detail->product_id;
                    $updated_detail = $payment_details->firstWhere('product_id', $product_id);

                    if (!$updated_detail) {
                        // If the product is removed from the order, delete it from transaction_details
                        $detail->delete();
                    } else {
                        // Update qty and subtotal if the product exists in the updated order
                        $detail->update([
                            'qty' => $updated_detail['qty'],
                            'subtotal' => $updated_detail['subtotal'],
                        ]);
                    }
                }

                // Add new items to transaction_details if they don't exist yet
                foreach ($payment_details as $payment_detail) {
                    $product_id = $payment_detail['product_id'];
                    $existing_detail = $transaction->transaction_details->firstWhere('product_id', $product_id);

                    if (!$existing_detail) {
                        // Add new item to transaction_details
                        $transaction->transaction_details()->create([
                            'product_id' => $product_id,
                            'qty' => $payment_detail['qty'],
                            'subtotal' => $payment_detail['subtotal'],
                        ]);
                    }
                }

                // Refresh transaction_details after synchronization
                $transaction->refresh();
                $transaction->load('transaction_details', 'transaction_details.product', 'transaction_details.product.ingredient');

                // Recalculate stock based on updated transaction_details
                $outOfStockIngredients = [];
                foreach ($transaction->transaction_details as $detail) {
                    $product = $detail->product;
                    $ingredients = $product->ingredient;

                    foreach ($ingredients as $ingredient) {
                        $stock = $stocks->where('ingredient_id', $ingredient->id)->first();
                        $productIngredientQty = IngredientProducts::where('product_id', $product->id)
                            ->where('ingredient_id', $ingredient->id)
                            ->value('qty');

                        if (!$stock) {
                            // Handle if stock doesn't exist yet
                            $getLastStock = Stock::where('warehouse_id', auth()->user()->warehouse_id)
                                ->where('ingredient_id', $ingredient->id)
                                ->orderBy('id', 'DESC')
                                ->first();

                            $stock = Stock::create([
                                'warehouse_id'  => auth()->user()->warehouse_id,
                                'ingredient_id' => $ingredient->id,
                                'shift_id'      => $shift->id,
                                'first_stock'   => $getLastStock ? $getLastStock->last_stock : 0,
                                'last_stock'    => $getLastStock ? $getLastStock->last_stock : 0,
                            ]);
                        }

                        if ($stock->last_stock < ($productIngredientQty * $detail->qty)) {
                            // If stock is insufficient, add to out-of-stock list
                            if (!in_array($ingredient->name, $outOfStockIngredients)) {
                                $outOfStockIngredients[] = $ingredient->name;
                            }
                        } else {
                            // Deduct stock
                            $stock->last_stock -= ($productIngredientQty * $detail->qty);
                            $stock->stock_used += ($productIngredientQty * $detail->qty);
                            $stock->save();

                            // Record transaction in/out
                            TransactionInOut::create([
                                'warehouse_id'     => auth()->user()->warehouse_id,
                                'ingredient_id'    => $ingredient->id,
                                'transaction_id'   => $transaction->id,
                                'qty'              => $detail->qty,
                                'date'             => Carbon::now()->format('Y-m-d'),
                                'transaction_type' => 'out',
                                'user_id'          => auth()->user()->id,
                            ]);
                        }
                    }
                }

                if (!empty($outOfStockIngredients)) {
                    $ingredientsList = implode(', ', $outOfStockIngredients);
                    return response()->json([
                        'status' => false,
                        'message' => 'Stok bahan baku ' . $ingredientsList . ' berikut tidak mencukupi.'
                    ], 500);
                }

                // Update transaction status and totals
                $total_amount = $payment_details->sum('subtotal');
                $total_qty = $payment_details->sum('qty');

                if ($transaction->status == 'Pending') {
                    $transaction->update([
                        'payment_method' => $request->payment_method,
                        'paid_amount' => $request->paid_amount,
                        'change_money' => $request->change_money,
                        'status' => 'Lunas',
                        'total_amount' => $total_amount,
                        'total_qty' => $total_qty,
                    ]);
                    DB::commit();
                } else {
                    return response()->json(['message' => 'Transaksi ini telah dibayar lunas'], 500);
                }

                $transaction['details'] = $transaction->transaction_details;
                return response()->json($transaction, 200);

            }
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::emergency("File:" . $th->getFile() . " Line:" . $th->getLine() . " Message:" . $th->getMessage());
            return response()->json(['message' => "Pembayaran pesanan gagal!"], 500);
            // return response()->json(['message' => $th->getMessage(), 'line' => $th->getLine()], 500);
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
            return response()->json(['errors' => $validator->messages()], 500);
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
            if ($checkShift == NULL) {
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
                        return response()->json(['message' => 'Stok bahan baku ' . $ingredient->name . ' tidak mencukupi.'], 500);
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
            \Log::emergency("File:" . $th->getFile() . " Line:" . $th->getLine() . " Message:" . $th->getMessage());
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }


    // Store offline transactions for cashier only outlet type
    public function storeOffline(Request $request) {
        $requestData = $request->all();
        $validate = Validator::make($requestData, [
            'order_type_id' => ['required', 'exists:order_types,id'],
            'payment_method' => ['required','string'],
            'paid_amount' => ['required'],
            'transaction_details' => ['required'],
            'notes' => ['nullable','string'],
        ]);

        // Validations check
        if($validate->fails()) {
            return response()->json([
                'status' => 'Error',
                'errors' => $validate->errors(),
            ], 500);
        }

        // Get Latest Shift data
        $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->latest()->first();

        // Check if there is no shift
        if(empty($shift) || ($shift->is_closed == 1)) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Shift belum dibuka',
            ], 500);
        }

        // Sequence number
        $countTransactionInShift = Transaction::where('shift_id', $shift->id)->orderBy('id', 'DESC')->count();
        $lastTransaction = Transaction::where('shift_id', $shift->id)->orderBy('id', 'DESC')->first();
        if ($countTransactionInShift > 0) {
            $sequence_number = $lastTransaction->sequence_number + 1;
        } else {
            $sequence_number = 1;
        }

        // Get total amount and total quantity
        $total_amount = 0;
        $total_qty = 0;
        foreach($request->transaction_details as $detail) {
            $total_amount += $detail['subtotal'];
            $total_qty += $detail['qty'];
        }

        // Get change money amount
        $change_money = $request->paid_amount - $total_amount;

        // Begin offline data transaction
        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'shift_id' => $shift->id,
                'warehouse_id' => auth()->user()->warehouse_id,
                'sequence_number' => $sequence_number,
                'order_type_id' => $request->order_type_id,
                'category_order' => "OFFLINE",
                'user_id' => auth()->user()->id,
                'payment_method' => $request->payment_method,
                'date' => date('Y-m-d'),
                'notes' => $request->notes,
                'total_amount' => $total_amount,
                'total_qty' => $total_qty,
                'paid_amount' => $request->paid_amount,
                'change_money' => $change_money,
                'status' => 'Lunas'
            ]);

            // Process if the transaction succeeded.
            if($transaction) {
                // Create transaction details
                foreach($request->transaction_details as $detail) {
                    // Create transaction detail
                    $transaction_detail = TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $detail['product_id'],
                        'product_name' => $detail['product_name'],
                        'product_price' => $detail['product_price'],
                        'qty' => $detail['qty'],
                        'subtotal' => $detail['subtotal'],
                    ]);

                    // Process if transaction detail created
                    if($transaction_detail) {
                        // Get product ingredients
                        $product_ingredients = IngredientProducts::where('product_id', $transaction_detail->product_id)->get();

                        // Get ingredients stock
                        foreach($product_ingredients as $ingredient) {
                            $stock = Stock::where('warehouse_id', auth()->user()->warehouse_id)->where('shift_id', $shift->id)->where('ingredient_id', $ingredient['ingredient_id'])->first();

                            // Process if there are stock
                            if($stock->last_stock >= ($ingredient['qty'] * $detail['qty'])) {
                                // Update stock
                                $stock->update([
                                    'stock_used' => $stock->stock_used += ($ingredient['qty'] * $detail['qty']),
                                    'last_used' => $stock->last_used + ($ingredient['qty'] * $detail['qty'])
                                ]);

                                // Create transaction in out
                                TransactionInOut::create([
                                    'warehouse_id' => auth()->user()->warehouse_id,
                                    'ingredient_id' => $ingredient['ingredient_id'],
                                    'qty' => ($ingredient['qty'] * $detail['qty']),
                                    'date' => date('Y-m-d'),
                                    'transaction_type' => 'out',
                                    'user_id' => auth()->user()->id,
                                    'transaction_id' => $transaction->id,
                                ]);

                                // Commit the transaction
                                DB::commit();
                            } else { // Response if stock not enough
                                DB::rollBack();
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'Stok bahan baku tidak mencukupi.'
                                ], 500);
                            }
                        }
                    } else { // Response if fail to create transaction details
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Gagal membuat detail transaksi.'
                        ], 500);
                    }
                }

                // Get transaction details
                $transaction['details'] = TransactionDetail::where('transaction_id', $transaction->id)->get();

                // Get warehouse
                $transaction['warehouse'] = Warehouse::where('id', $transaction->warehouse_id)->first();

                // Get datetime
                $transaction['datetime'] = $transaction->created_at->isoFormat('D MMM Y H:m');

                // Get product count
                $transaction['product_count'] = TransactionDetail::where('transaction_id', $transaction->id)->count();

                // Get order type
                $transaction['order_type'] = OrderType::where('id', $transaction->order_type_id)->first();

                // Get order type name
                $transaction['order_type_name'] = $transaction['order_type']['name'];

                // Response if transaction created successfully
                return response()->json($transaction, 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::emergency("File:" . $th->getFile() . " Line:" . $th->getLine() . " Message:" . $th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => throw $th
            ], 500);
        }
    }

    /**
     * Generate OTP Code 6-digit
     */
    public function generateOtp()
    {
        $otp = rand(100000, 999999);
        return $otp;
    }

    /**
     * Check warehouse whatsapp number
     */
    public function checkWhatsappNumber()
    {
        $warehouse = Warehouse::find(auth()->user()->warehouse_id);

        if(!empty($warehouse->whatsapp) && ($warehouse->is_whatsapp_active == 1)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check whatsapp connection
     */
    public function checkWhatsappConnection()
    {
        $whatsapp = $this->whatsapp->getSessionDetail();
        $error = $whatsapp->getData()->error;

        if($error) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Transaction Cancellation request
     * @param Request $request
     */
    public function transactionCancellationRequest(Request $request)
    {
        $otp = $this->generateOtp();

        // Get transaction
        $transaction = Transaction::with('shift', 'transaction_details')->find($request->id);

        if(!$transaction){
            return response()->json([
                'status' => 'error',
                'message' => 'Data transaksi tidak ditemukan.'
            ], 500);
        }

        if(auth()->user()->warehouse->is_whatsapp_active == 1) {
            if($this->checkWhatsappConnection()) {
                $transaction->cancelation_otp = $otp;
                $transaction->cancelation_reason = $request->reason;
                $transaction->save();

                $message = "*Permintaan Pembatalan Pesanan*";
                $message .= "\n\nKode pembatalan : *" . $otp . "*";
                $message .= "\nAlasan : " . $request->reason;
                $message .= "\n\nOutlet : " . auth()->user()->warehouse->name;
                $message .= "\nShift : " . $transaction->shift->shift_number;
                $message .= "\nNomor Antrian : " . $transaction->sequence_number;
                $message .= "\n\nProduk : ";

                foreach($transaction->transaction_details as $detail) {
                    $message .= "\n- " . $detail->product_name . " (" . $detail->qty . ")";
                }

                $message .= "\n\nJumlah : Rp. " . number_format($transaction->total_amount, 0, ',', '.');

                $message .= "\n\nPesan ini dikirim pada " . Carbon::now()->translatedFormat('l, j F Y H:i:s');

                $this->whatsapp->sendMessage('62' . auth()->user()->warehouse->whatsapp, $message);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'OTP berhasil dikirim!'
                ], 200);
            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Tidak ada akun Whatsapp terhubung!'
                ], 201);
            }
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Outlet ini tidak memiliki akun Whatsapp terhubung!'
            ], 201);
        }

    }


    /**
     * Cancel transaction
     * @param Request $request
     * @param $id
     */
    public function cancel(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::find($id);
            if ($transaction) {
                // Check if cancelation otp is not empty and equal to request otp
                if(!empty($transaction->cancelation_otp) && ($transaction->cancelation_otp == $request->otp)) {
                    // Update status transaksi menjadi batal
                    $transaction->update([
                        'status' => 'Batal',
                    ]);

                    DB::commit();

                    // Send whatsapp message
                    $message = "Pembatalan pesanan dengan kode pembatalan *" . $request->otp . "* berhasil!";
                    $this->whatsapp->sendMessage('62' . auth()->user()->warehouse->whatsapp, $message);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Transaksi Berhasil Dibatalkan'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'OTP transaksi tidak valid'
                    ], 201);
                }

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data transaksi tidak ada'
                ], 201);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::emergency("File:" . $th->getFile() . " Line:" . $th->getLine() . " Message:" . $th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Old cancel method
     */
    public function cancelOld($id)
    {
        try {
            DB::beginTransaction();

            // Get transaction by id
            $transaction = Transaction::findOrFail($id);

            // If transaction exists change status to batal
            if($transaction) {
                $transaction->status = 'Batal';
                $transaction->save();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaksi berhasil dibatalkan'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data transaksi tidak ada'
                ], 201);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::emergency("File:" . $th->getFile() . " Line:" . $th->getLine() . " Message:" . $th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
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

        // Ambil bahan baku terkait melalui model Ingredient
        $qty = [];
        $ingredients = $product->ingredient;
        foreach ($ingredients as $ingredient) {
            $stock = Stock::where('shift_id', $shift->id)->where('ingredient_id', $ingredient->id)->where('warehouse_id', auth()->user()->warehouse_id)->first();
            $productIngredientQty = IngredientProducts::where('product_id', '=', $product->id)->where('ingredient_id', '=', $ingredient->id)->pluck('qty')->min();
            $qty[] = floor($stock->last_stock / $productIngredientQty);
        }
        // Get smallest quantity
        $qty = min($qty);
        if ($qty < $request->qty) {
            return response()->json(['status' => false, 'message' => "Stok Bahan Baku Tidak Mencukupi!"], 500);
        } else {
            return response()->json(['status' => true, 'message' => "Stok Tersedia"], 200);
        }
    }

    // Delete transaction products
    public function deleteTransactionProducts($id) {
        $detail = TransactionDetail::where('id', $id)->first();
        $delete = $detail->delete();

        if($delete) {
            return response()->json([
                "status" => "ok",
                "message" => "Produk berhasil dihapus"
            ], 200);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "Gagal menghapus produk"
            ], 500);
        }
    }
}
