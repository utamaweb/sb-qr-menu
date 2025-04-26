// Get data from request
                $data = $request->all();

                // Validation
                $validator = Validator::make($data, [
                    'payment_details' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => 'Pilihan Menu Tidak Boleh Kosong'], 500);
                }
                // End of validation

                // Get shift
                $shift = DB::select("SELECT * FROM shifts
                    WHERE warehouse_id = " . auth()->user()->warehouse_id .
                    " AND is_closed = 0
                    ORDER BY id DESC
                    LIMIT 1");

                // Shift check
                if(collect($shift)->count() > 0) {
                    $shift = collect($shift)->first();
                } else {
                    return response()->json(['message' => 'Kasir belum buka'], 500);
                }
                // End of shift check

                // Get stocks
                $stocks = Stock::where('shift_id', $shift->id)->get();

                // Delete old transaction details
                DB::delete("DELETE FROM transaction_details WHERE transaction_id = " . $request->transaction_id);

                // Get product ids
                $product_ids = collect($request->payment_details)->pluck('product_id')->toArray();

                // Get products
                $warehouseProductData = DB::select("SELECT p.name AS product_name, pw.* FROM product_warehouse AS pw LEFT JOIN products AS p ON pw.product_id = p.id WHERE pw.warehouse_id = " . auth()->user()->warehouse_id . " AND pw.product_id IN (" . implode(',', $product_ids) . ") AND pw.deleted_at IS NULL");

                // Create new transaction details
                $transactionDetailsData = [];

                foreach($request->payment_details as $detail) {
                    $product = collect($warehouseProductData)->where('product_id', $detail['product_id'])->first();
                    $transactionDetailsData[] = [
                        'transaction_id' => $request->transaction_id,
                        'product_id'     => $detail['product_id'],
                        'qty'            => $detail['qty'],
                        'subtotal'       => $detail['subtotal'],
                        'product_name'   => $product->product_name,
                        'product_price'  => $product->price,
                    ];
                }

                // Insert new transaction details
                DB::table('transaction_details')->insert($transactionDetailsData);

                // Get transaction
                $transaction = Transaction::with('transaction_details', 'transaction_details.product', 'transaction_details.product.ingredient')->findOrFail($request->transaction_id);

                // Get transaction details
                $transaction_details = $transaction->transaction_details;
                $products = $transaction_details->pluck('product');

                // Get product ingredients
                $productIngredients = IngredientProducts::whereIn('product_id', $products->pluck('id'))->get();

                // Variables
                $transactionDetailsWithProducts = [];
                $outOfStockIngredients = []; // Array untuk menyimpan bahan baku yang habis

                foreach ($transaction_details as $detail) {
                    // get deal with stock
                    $product_id = $detail['product_id'];
                    $product_qty = $detail['qty'];

                    // Ambil produk terkait
                    $product = $products->where('id', $product_id)->first();

                    // Ambil bahan baku terkait melalui model Ingredient
                    $ingredients = $product->ingredient;

                    foreach ($ingredients as $ingredient) {
                        $stock = $stocks->where('ingredient_id', $ingredient->id)->first();
                        $productIngredientQty = $productIngredients->where('product_id', '=', $product->id)->where('ingredient_id', '=', $ingredient->id)->pluck('qty')->min();

                        if (!$stock) {
                            // Handle jika stok belum ada
                            $getLastStock = $stocks->where('ingredient_id', $ingredient->id)->first();

                            $stock = Stock::create([
                                'warehouse_id'  => auth()->user()->warehouse_id,
                                'ingredient_id' => $ingredient->id,
                                'shift_id'      => $shift->id,
                                'first_stock'   => $getLastStock->last_stock,
                                'last_stock'    => $getLastStock->last_stock,
                            ]);
                        }

                        if ($stock->last_stock < ($productIngredientQty * $product_qty)) {
                            // Jika stok kurang dari qty, tambahkan bahan baku ke array
                            if (!in_array($ingredient->name, $outOfStockIngredients)) {
                                $outOfStockIngredients[] = $ingredient->name;
                            }
                        } else {
                            $stock->last_stock -= ($productIngredientQty * $product_qty);
                            $stock->stock_used += ($productIngredientQty * $product_qty);
                            $stock->save();
                            // Insert ke table transaction in out
                            TransactionInOut::create([
                                'warehouse_id'     => auth()->user()->warehouse_id,
                                'ingredient_id'    => $ingredient->id,
                                'transaction_id'   => $transaction->id,
                                'qty'              => $product_qty,
                                'date'             => Carbon::now()->format('Y-m-d'),
                                'transaction_type' => 'out',
                                'user_id'          => auth()->user()->id,
                            ]);
                        }
                    }

                    // end get deal with stock
                    $productDetail = [
                        'transaction_id' => $transaction->id,
                        'product_id'     => $detail['product_id'],
                        'qty'            => $detail['qty'],
                        'subtotal'       => $detail['subtotal'],
                    ];

                    // Menambahkan data detail produk ke array
                    $transactionDetailsWithProducts[] = $productDetail;
                }

                if (!empty($outOfStockIngredients)) {
                    // Jika ada bahan baku yang habis, tampilkan pesan peringatan
                    $ingredientsList = implode(', ', $outOfStockIngredients);
                    return response()->json([
                        'status' => false,
                        'message' => 'Stok bahan baku '. $ingredientsList . ' berikut tidak mencukupi.'
                    ], 500);
                }

                $total_amount = collect($request->payment_details)->sum('subtotal');
                $total_qty = collect($request->payment_details)->sum('qty');

                // update transaction status, etc lunas
                if ($transaction->status == 'Pending') {
                    $transaction->update([
                        'payment_method' => $request->payment_method,
                        'paid_amount' => $request->paid_amount,
                        'change_money' => $request->change_money,
                        'status' => 'Lunas',
                    ]);
                    DB::commit();
                } else {
                    return response()->json(['message' => 'Transaksi ini telah dibayar lunas'], 500);
                }

                $transaction['details'] = $transactionDetailsWithProducts;
                return response()->json($transaction, 200);

            }
