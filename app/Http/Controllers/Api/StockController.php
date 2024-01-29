<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Ingredient;

class StockController extends Controller
{
    public function getAllIngredients() {
        $ingredients = Ingredient::with('unit')->get();
        return response()->json($ingredients, 200);
    }

    public function getAllStocks() {
        $stocks = Stock::with(['warehouse', 'ingredient'])->get();
        return response()->json($stocks, 200);
    }

    public function getStockByWarehouse() {
        $roleName = auth()->user()->getRoleNames()[0];
        $stockByWarehouse = Stock::with(['warehouse', 'ingredient'])->get();
        if($roleName == 'Kasir'){
            $stockByWarehouse = Stock::with(['warehouse', 'ingredient'])->where('warehouse_id', auth()->user()->warehouse_id)->get();
        }
        return response()->json($stockByWarehouse, 200);
    }
}
