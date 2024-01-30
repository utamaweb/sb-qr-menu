<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Ingredient;
use App\Models\TransactionIngredient;
use App\Models\TransactionInOut;
use Carbon\Carbon;

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
}
