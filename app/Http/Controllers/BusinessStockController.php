<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class BusinessStockController extends Controller
{
    // Index
    public function index() {

        if(request()->outlet) {
            $warehouseId = request()->outlet;
        } else {
            $warehouseId = Warehouse::where('business_id', auth()->user()->business_id)->first()->id;
        }

        // Get warehouses by user business
        $warehouses = Warehouse::where('business_id', auth()->user()->business_id)->get();

        // Get stocks by warehouse
        $stocks = Stock::where('warehouse_id', $warehouseId)->orderBy('id', 'DESC')->groupBy('ingredient_id')->get();

        return view('backend.stok.business', compact('warehouses', 'stocks'));
    }
}
