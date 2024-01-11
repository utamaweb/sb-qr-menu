<?php

namespace App\Http\Controllers;

use App\Models\StockPurchase;
use App\Models\StockPurchaseIngredient;
use App\Models\Warehouse;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StockPurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stockPurchases = StockPurchase::get();
        $ingredients = Ingredient::get();
        return view('backend.stock_purchase.index', compact('stockPurchases', 'ingredients'));
    }

    public function create() {
        $dateNow = Carbon::now()->format('Y-m-d');
        $ingredients = Ingredient::get();
        $roleName = auth()->user()->getRoleNames()[0];
        $warehouses = Warehouse::get();
        return view('backend.stock_purchase.create', compact('dateNow','ingredients', 'roleName', 'warehouses'));
    }

    public function store(Request $request)
    {
        $qtyInt = array_map('intval', $request->qty);
        $totalQty = array_sum($qtyInt);
        // return $totalQty;
        $this->validate($request, [
            'warehouse_id' => 'required',
            'qty' => 'required',
            'ingredient_id' => 'required',
        ]);
        $stockPurchase = StockPurchase::create([
            'warehouse_id' => $request->warehouse_id,
            'user_id' => auth()->user()->id,
            'date' => $request->date,
            'total_qty' => $totalQty
        ]);
        foreach ($request->qty as $item => $v) {
            $data = array(
                'stock_purchase_id' => $stockPurchase->id,
                'ingredient_id' => $request->ingredient_id[$item],
                'qty' => $request->qty[$item],
                'notes' => $request->notes[$item],
            );
            $ingredient = Ingredient::find($request->ingredient_id[$item]);
            $data2 = array(
                'last_stock' => $ingredient->qty + $request->qty[$item],
            );
            StockPurchaseIngredient::create($data);
            Ingredient::find($request->ingredient_id[$item])->update($data2);
        }
        return redirect()->route('stock-purchase.index')->with('message', 'Data berhasil ditambahkan');
    }

    public function show($id) {
        $stockOpname = StockOpname::find($id);
        $stockOpnameDetails = StockOpnameDetail::whereStockOpnameId($id)->get();
        return view('backend.stock_opname.show', compact('stockOpname','stockOpnameDetails'));
    }

    public function updateDetail(Request $request, $id)
    {
        $this->validate($request, [
            'qty' => 'required',
        ]);
        StockOpnameDetail::find($id)->update([
            'qty' => $request->qty,
        ]);
        return redirect()->back()->with('message', 'Data berhasil diubah');
    }

    public function deleteBySelection(Request $request)
    {
        $shift_id = $request['shiftIdArray'];
        foreach ($shift_id as $id) {
            $lims_order_type_data = Shift::find($id);
            $lims_order_type_data->delete();
        }
        // $this->cacheForget('ingredient_list');
        return 'Data berhasil dihapus!';
    }

    public function destroy($id)
    {
        $stockOpname = StockOpname::find($id);
        $stockOpnameDetails = StockOpnameDetail::whereStockOpnameId($id)->delete();
        $stockOpname->delete();
        // $this->cacheForget('ingredient_list');
        return redirect()->route('stock-opname.index')->with('not_permitted', 'Data berhasil dihapus');
    }
}
