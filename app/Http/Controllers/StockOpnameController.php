<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\Ingredient;
use App\Models\Stock;
use App\Models\Shift;
use App\Models\Warehouse;
use Illuminate\Validation\Rule;
use Keygen;
use Auth;
use DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        if(auth()->user()->hasRole('Superadmin')){
            $stockOpnames = StockOpname::with('warehouse')->get();
        } else {
            $stockOpnames = StockOpname::with('warehouse')->where('warehouse_id', auth()->user()->warehouse_id)->get();
        }
        $stockOpnameDetails = StockOpnameDetail::get();

        return view('backend.stock_opname.index', compact('stockOpnames','stockOpnameDetails'));
    }

    public function create() {
        $warehouses = Warehouse::get();
        $warehouse = Warehouse::find(auth()->user()->warehouse_id);
        $ingredients = Ingredient::where('business_id', $warehouse->business_id)->get();
        $roleName = auth()->user()->getRoleNames()[0];
        return view('backend.stock_opname.create', compact('ingredients', 'roleName', 'warehouses'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);
        if($request->ingredient_id < 1){
            return redirect()->route('stock-opname.create')->with('not_permitted', 'Bahan Baku Harus Diisi Minimal 1');
        }
        $latestShift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->orderBy('id', 'DESC')->first();
        // Store Table StockOpname
        $stockOpname = StockOpname::create([
            'name' => $request->name,
            'notes' => $request->notes,
            'warehouse_id' => $request->warehouse_id,
        ]);
        // Store Table Stock Opname Detail
        foreach ($request->qty as $item => $v) {
            $data = array(
                'stock_opname_id' => $stockOpname->id,
                'ingredient_id' => $request->ingredient_id[$item],
                'qty' => $request->qty[$item],
            );
            StockOpnameDetail::create($data);
            $checkStock = Stock::where('shift_id', $latestShift->id)->where('ingredient_id', $request->ingredient_id[$item])->where('warehouse_id', $request->warehouse_id)->count();
            if($checkStock < 1){
                Stock::create([
                    'warehouse_id' => $request->warehouse_id,
                    'shift_id' => $latestShift->id,
                    'ingredient_id' => $request->ingredient_id[$item],
                    'first_stock' => $request->qty[$item],
                    'stock_in' => $request->qty[$item],
                    'last_stock' => $request->qty[$item],
                ]);
            } else {
                $stock = Stock::where('ingredient_id', $request->ingredient_id[$item])->where('warehouse_id', $request->warehouse_id)->first();
                Stock::where('shift_id', $latestShift->id)->where('ingredient_id', $request->ingredient_id[$item])->where('warehouse_id', $request->warehouse_id)->update([
                    // 'stock_in' => $stock->stock_in + $request->qty[$item],
                    'last_stock' => $request->qty[$item],
                ]);
            }
        }
        return redirect()->route('stock-opname.index')->with('message', 'Data berhasil ditambahkan');
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
        return 'Data berhasil dihapus!';
    }

    public function destroy($id)
    {
        $stockOpname = StockOpname::find($id);
        $stockOpnameDetails = StockOpnameDetail::whereStockOpnameId($id)->delete();
        $stockOpname->delete();
        return redirect()->route('stock-opname.index')->with('not_permitted', 'Data berhasil dihapus');
    }
}
