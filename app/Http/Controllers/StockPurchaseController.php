<?php

namespace App\Http\Controllers;

use App\Models\StockPurchase;
use App\Models\StockPurchaseIngredient;
use App\Models\Warehouse;
use App\Models\TransactionInOut;
use App\Models\Stock;
use App\Models\Ingredient;
use App\Models\CloseCashier;
use App\Models\Shift;
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
        if($request->notes < 1){
            return redirect()->route('pembelian-stok.create')->with('not_permitted', 'Bahan Baku Harus Diisi Minimal 1');
        }
        $dateNow = Carbon::now()->format('Y-m-d');
        $roleName = auth()->user()->getRoleNames()[0];
        $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->where('user_id', auth()->user()->id)->where('is_closed', 0)->first();
        if($roleName == 'Superadmin'){
            $shift = Shift::where('date', $dateNow)->where('is_closed', 0)->first();
        }
        if($shift == NULL){
            return redirect()->route('pembelian-stok.index')->with('not_permitted', 'Belum ada kasir yang dibuka');
        }
        $qtyInt = array_map('intval', $request->qty);
        $totalQty = array_sum($qtyInt);
        $subtotalInt = array_map('intval', $request->subtotal);
        $totalSubtotal = array_sum($subtotalInt);
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
            'total_qty' => $totalQty,
            'total_price' => $totalSubtotal,
            'shift_id' => $shift->id,
        ]);
        foreach ($request->qty as $item => $v) {
            $data = array(
                'stock_purchase_id' => $stockPurchase->id,
                'ingredient_id' => $request->ingredient_id[$item],
                'qty' => $request->qty[$item],
                'subtotal' => $request->subtotal[$item],
                'notes' => $request->notes[$item],
            );
            StockPurchaseIngredient::create($data);
            $qtyToInt = (int)$request->qty[$item];
            $checkStock = Stock::where('ingredient_id', $request->ingredient_id[$item])->where('warehouse_id', $request->warehouse_id)->count();
            if($checkStock < 1){
                Stock::create([
                    'warehouse_id' => $request->warehouse_id,
                    'ingredient_id' => $request->ingredient_id[$item],
                    'first_stock' => $request->qty[$item],
                    'stock_in' => $request->qty[$item],
                    'last_stock' => $request->qty[$item],
                ]);
            } else {
                $stock = Stock::where('ingredient_id', $request->ingredient_id[$item])->where('warehouse_id', $request->warehouse_id)->first();
                Stock::where('ingredient_id', $request->ingredient_id[$item])->where('warehouse_id', $request->warehouse_id)->update([
                    'stock_in' => $stock->stock_in + $request->qty[$item],
                    'last_stock' => $stock->last_stock + $request->qty[$item],
                ]);
            }
            TransactionInOut::create([
                'warehouse_id' => $request->warehouse_id,
                'ingredient_id' => $request->ingredient_id[$item],
                'qty' => $request->qty[$item],
                'transaction_type' => 'in',
                'date' => $dateNow,
                'user_id' => auth()->user()->id,
            ]);
            // $ingredient = Ingredient::find($request->ingredient_id[$item]);
            // $data2 = array(
            //     'last_stock' => $ingredient->last_stock + $qtyToInt,
            // );
            // Ingredient::find($request->ingredient_id[$item])->update($data2);
        }
        return redirect()->route('pembelian-stok.index')->with('message', 'Data berhasil ditambahkan');
    }


    public function edit($id) {
        $stockPurchase = StockPurchase::find($id);
        $stockPurchaseIngredients = StockPurchaseIngredient::where('stock_purchase_id', $id)->get();
        $dateNow = Carbon::now()->format('Y-m-d');
        $ingredients = Ingredient::get();
        $roleName = auth()->user()->getRoleNames()[0];
        $warehouses = Warehouse::get();
        return view('backend.stock_purchase.edit', compact('stockPurchase','stockPurchaseIngredients', 'dateNow','ingredients', 'roleName', 'warehouses'));
    }

    public function update(Request $request, $id)
    {
        if(count($request->notes) < 1){
            return redirect()->route('pembelian-stok.create')->with('not_permitted', 'Bahan Baku Harus Diisi Minimal 1');
        }
        $dateNow = Carbon::now()->format('Y-m-d');
        $roleName = auth()->user()->getRoleNames()[0];
        $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->where('user_id', auth()->user()->id)->where('is_closed', 0)->first();
        if($roleName == 'Superadmin'){
            $shift = Shift::where('date', $dateNow)->where('is_closed', 0)->first();
        }
        $totalQty = 0;
        $totalSubtotal = 0;
        $qtyInt = array_map('intval', $request->qty);
        $totalQty = array_sum($qtyInt);
        $subtotalInt = array_map('intval', $request->subtotal);
        $totalSubtotal = array_sum($subtotalInt);

        StockPurchase::find($id)->update(['total_price' => $totalSubtotal]);

        if (count($request->stock_purchase_ingredient_id) > 0) {
            foreach ($request->stock_purchase_ingredient_id as $item => $v) {
                $data = array(
                    'stock_purchase_id' => $id,
                    'ingredient_id' => $request->ingredient_id[$item],
                    // 'stock_purchase_ingredient_id' => $request->stock_purchase_ingredient_id[$item],
                    'qty' => $request->qty[$item],
                    'subtotal' => $request->subtotal[$item],
                    'notes' => $request->notes[$item],
                );
                StockPurchaseIngredient::where('id', $request->stock_purchase_ingredient_id[$item])->update($data);
            }
        }
        return redirect()->route('pembelian-stok.index')->with('message', 'Data berhasil diubah');
    }


    public function show($id) {
        $warehouses = Warehouse::get();
        $dateNow = Carbon::now()->format('Y-m-d');
        $roleName = auth()->user()->getRoleNames()[0];
        $ingredients = Ingredient::get();
        $stockPurchase = StockPurchase::find($id);
        $stockPurchaseDetails = StockPurchaseIngredient::whereStockPurchaseId($id)->get();
        return view('backend.stock_purchase.show', compact('ingredients','dateNow','roleName','warehouses','stockPurchase','stockPurchaseDetails'));
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
        return redirect()->back()->with('not_permitted', 'Data berhasil dihapus');
    }
}
