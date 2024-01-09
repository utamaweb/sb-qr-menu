<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\Ingredient;
use Illuminate\Validation\Rule;
use Keygen;
use Auth;
use DB;
use App\Traits\CacheForget;

class StockOpnameController extends Controller
{
    use CacheForget;
    public function index()
    {
        $stockOpnames = StockOpname::get();
        $stockOpnameDetails = StockOpnameDetail::get();
        $ingredients = Ingredient::get();
        return view('backend.stock_opname.create', compact('stockOpnames', 'ingredients','stockOpnameDetails'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);
        $stockOpname = StockOpname::create([
            'name' => $request->name,
        ]);
        foreach ($request->qty as $item => $v) {
            $data = array(
                'stock_opname_id' => $stockOpname->id,
                'ingredient_id' => $request->ingredient_id[$item],
                'qty' => $request->qty[$item],
            );
            $data2 = array(
                'last_stock' => $request->qty[$item]
            );
            StockOpnameDetail::create($data);
            Ingredient::find($request->ingredient_id[$item])->update($data2);
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
