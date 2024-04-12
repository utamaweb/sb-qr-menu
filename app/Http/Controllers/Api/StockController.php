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
}
