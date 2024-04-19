<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\StockPurchase;
use App\Models\StockPurchaseIngredient;
use App\Models\Shift;
use App\Models\TransactionInOut;
use App\Models\Ingredient;
use App\Models\TransactionIngredient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function getAllIngredients() {
        $ingredients = Ingredient::with('unit')->get();
        return response()->json($ingredients, 200);
    }

    public function getAllStocks() {
        $stockWithIngredients = Stock::with(['warehouse', 'ingredient'])->get();
        return response()->json($stockWithIngredients, 200);
    }

    public function getStockByWarehouse() {
        $roleName = auth()->user()->getRoleNames()[0];
        $stockByWarehouse = Stock::with(['warehouse', 'ingredient'])->get();
        if($roleName == 'Kasir'){
            $stockByWarehouse = Stock::with(['warehouse', 'ingredient'])->where('warehouse_id', auth()->user()->warehouse_id)->get();
        }
        return response()->json($stockByWarehouse, 200);
    }

    // public function getStockHistory() {
    //     $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
    //             ->where('user_id', auth()->user()->id)
    //             ->where('is_closed', 0)
    //             ->first();

    //     // Ambil data dari database
    //     $stocks = StockPurchase::where('shift_id', $shift->id)->get()->map(function ($item){
    //         $filteredData = [
    //             'id' => $item->id,
    //             'outlet' => $item->warehouse->name,
    //             'date' => $item->date,
    //             'total_qty' => (int) $item->total_qty,
    //             'total_price' => (int) $item->total_price,
    //             'created_by' => $item->user->name
    //         ];
    //         return $filteredData;
    //     });

    //     return response()->json($stocks, 200);
    // }

    public function getStockHistory() {
        $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                ->where('user_id', auth()->user()->id)
                ->where('is_closed', 0)
                ->first();

        // Ambil data dari database
        $stocks = StockPurchase::where('shift_id', $shift->id)->get()->map(function ($item){
            $filteredData = [
                'id' => $item->id,
                'outlet' => $item->warehouse->name,
                'date' => $item->date,
                'total_qty' => (int) $item->total_qty,
                'total_price' => (int) $item->total_price,
                'created_by' => $item->user->name,
                'details' => $item->stockPurchaseIngredients->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'ingredient_name' => $detail->ingredient->name,
                        'qty' => $detail->qty,
                        'price' => $detail->subtotal / $detail->qty,
                        'subtotal' => $detail->subtotal,
                        'notes' => $detail->notes
                    ];
                })
            ];
            return $filteredData;
        });

        return response()->json($stocks, 200);
    }


    public function getDetailStockHistory($id) {
        $response = StockPurchase::find($id);
        $response['outlet'] = $response->warehouse->name;
        $response['created_by'] = $response->user->name;
        $response['details'] = StockPurchaseIngredient::where('stock_purchase_id', $response->id)->get()->map(function ($item){
            $item->ingredient_name = $item->ingredient->name;
            unset($item->ingredient);
            return $item;
        });
        unset($response['warehouse'], $response['user']);
        return response()->json($response, 200);
    }


    public function getWarehouseIngredients() {
        $warehouseId = auth()->user()->warehouse_id;
        $warehouseIngredients = Stock::where('warehouse_id', $warehouseId)->with('ingredient')->get();

        // Ambil hanya data yang diperlukan
        $outletName = auth()->user()->warehouse->name;
        $ingredients = $warehouseIngredients->pluck('ingredient');

        $response = [
            'outlet' => $outletName,
            'ingredients_in_outlet' => $ingredients
        ];

        return response()->json($response, 200);
    }

    public function getIngredientSold() {
        $dateNow = Carbon::now()->format('Y-m-d');
        $roleName = auth()->user()->getRoleNames()[0];
        $ingredientSold = TransactionInOut::where('date', $dateNow)->where('warehouse_id', auth()->user()->warehouse_id)->get()->pluck('ingredient_id');
        $ingredients = Ingredient::with('unit')->whereIn('id', $ingredientSold)->get();
        return response()->json($ingredients, 200);
    }


    public function add(Request $request) {
        if($request->ingredients < 1){
            return response()->json(['message' => "Bahan Baku Harus Diisi Minimal 1."], 200);
        }
        $dateNow = Carbon::now()->format('Y-m-d');
        $roleName = auth()->user()->getRoleNames()[0];
        $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->where('user_id', auth()->user()->id)->where('is_closed', 0)->first();
        if($roleName == 'Superadmin'){
            $shift = Shift::where('date', $dateNow)->where('is_closed', 0)->first();
        }
        if($shift == NULL){
            return response()->json(['message' => "Belum ada kasir buka."], 200);
        }
        // Inisialisasi variabel total_qty dan total_price
        $total_qty = 0;
        $total_price = 0;

        // Iterasi setiap elemen dalam array ingredients
        foreach ($request['ingredients'] as $ingredient) {
            // Menambahkan qty ke total_qty
            $total_qty += $ingredient['qty'];

            // Menambahkan subtotal ke total_price
            $total_price += $ingredient['subtotal'];
        }

        DB::beginTransaction();

        try {
            $stockPurchase = StockPurchase::create([
                'warehouse_id' => auth()->user()->warehouse_id,
                'user_id' => auth()->user()->id,
                'date' => $dateNow,
                'total_qty' => $total_qty,
                'total_price' => $total_price,
                'shift_id' => $shift->id,
            ]);
            foreach($request->ingredients as $item){
                StockPurchaseIngredient::create([
                    'stock_purchase_id' => $stockPurchase->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal']
                ]);

                $checkStock = Stock::where('ingredient_id', $item['ingredient_id'])->where('warehouse_id', auth()->user()->warehouse_id)->count();
                if($checkStock < 1){
                    Stock::create([
                        'warehouse_id' => auth()->user()->warehouse_id,
                        'ingredient_id' => $item['ingredient_id'],
                        'stock_in' => $item['qty'],
                        'last_stock' => $item['qty'],
                    ]);
                } else {
                    $stock = Stock::where('ingredient_id', $item['ingredient_id'])->where('warehouse_id', auth()->user()->warehouse_id)->first();
                    Stock::where('ingredient_id', $item['ingredient_id'])->where('warehouse_id', auth()->user()->warehouse_id)->update([
                        'stock_in' => $stock->stock_in + $item['qty'],
                        'last_stock' => $stock->last_stock + $item['qty'],
                    ]);
                }

                TransactionInOut::create([
                    'warehouse_id' => auth()->user()->warehouse_id,
                    'ingredient_id' => $item['ingredient_id'],
                    'qty' => $item['qty'],
                    'transaction_type' => 'in',
                    'date' => $dateNow,
                    'user_id' => auth()->user()->id,
                ]);

            }
            DB::commit();
            return response()->json($stockPurchase, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }


    public function edit(Request $request, $id) {
        if($request->ingredients < 1){
            return response()->json(['message' => "Bahan Baku Harus Diisi Minimal 1."], 200);
        }
        $dateNow = Carbon::now()->format('Y-m-d');
        $roleName = auth()->user()->getRoleNames()[0];
        $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->where('user_id', auth()->user()->id)->where('is_closed', 0)->first();
        if($roleName == 'Superadmin'){
            $shift = Shift::where('date', $dateNow)->where('is_closed', 0)->first();
        }
        if($shift == NULL){
            return response()->json(['message' => "Belum ada kasir buka."], 200);
        }
        $total_qty = 0;
        $total_price = 0;

        foreach ($request['ingredients'] as $ingredient) {
            $total_qty += $ingredient['qty'];
            $total_price += $ingredient['subtotal'];
        }
        // return $total_qty . '&' . $total_price;
        DB::beginTransaction();

        try {
            $stockPurchase = StockPurchase::find($id);
            // update data dengan akses data sebelumnya
            if($request->ingredients > 0){
                $detailStockPurchase = StockPurchaseIngredient::where('stock_purchase_id', $id)->get();
                $totalQtyBefore = $stockPurchase->total_qty;
                foreach($detailStockPurchase as $detail){
                    $stock = Stock::where('ingredient_id', $detail->ingredient_id)->where('warehouse_id', $stockPurchase->warehouse_id)->first();
                    Stock::where('ingredient_id', $detail->ingredient_id)->where('warehouse_id', $stockPurchase->warehouse_id)->update([
                        'stock_in' => $stock->stock_in - $detail->qty,
                        'last_stock' => $stock->last_stock - $detail->qty
                    ]);
                    $detail->delete();
                }
            }
            // Update data stock purchase
            $stockPurchaseUpdate = StockPurchase::find($id)->update([
                'total_qty' => $total_qty,
                'total_price' => $total_price,
            ]);

            // Setelah detail dihapus, maka dibuat ulang
            foreach($request->ingredients as $item){
                StockPurchaseIngredient::create([
                    'stock_purchase_id' => $id,
                    'ingredient_id' => $item['ingredient_id'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal']
                ]);

                // cek stok
                $checkStock = Stock::where('ingredient_id', $item['ingredient_id'])->where('warehouse_id', auth()->user()->warehouse_id)->count();
                // jika tidak ada maka dibuat row baru
                if($checkStock < 1){
                    Stock::create([
                        'warehouse_id' => auth()->user()->warehouse_id,
                        'ingredient_id' => $item['ingredient_id'],
                        'stock_in' => $item['qty'],
                        'last_stock' => $item['qty'],
                    ]);
                // jika ada maka update row tsb
                } else {
                    $stock = Stock::where('ingredient_id', $item['ingredient_id'])->where('warehouse_id', auth()->user()->warehouse_id)->first();
                    Stock::where('ingredient_id', $item['ingredient_id'])->where('warehouse_id', auth()->user()->warehouse_id)->update([
                        'stock_in' => $stock->stock_in + $item['qty'],
                        'last_stock' => $stock->last_stock + $item['qty'],
                    ]);
                }

                // tambah data di table transaction in out
                TransactionInOut::create([
                    'warehouse_id' => auth()->user()->warehouse_id,
                    'ingredient_id' => $item['ingredient_id'],
                    'qty' => $item['qty'],
                    'transaction_type' => 'in',
                    'date' => $dateNow,
                    'user_id' => auth()->user()->id,
                ]);

            }
            DB::commit();
            return response()->json($stockPurchase, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
