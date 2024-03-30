<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Unit;
use App\Models\Stock;
use Illuminate\Validation\Rule;
use Keygen;
use Auth;
use DB;
use App\Traits\CacheForget;

class IngredientController extends Controller
{
    use CacheForget;
    public function index()
    {
        // $lims_ingredient_all = Ingredient::get();
        $lims_ingredient_all = Stock::get();
        $roleName = auth()->user()->getRoleNames()[0];
        if($roleName == 'Kasir'){
            $lims_ingredient_all = Stock::where('warehouse_id', auth()->user()->warehouse_id)->get();
        }
        $units = Unit::get();
        return view('backend.ingredient.create', compact('lims_ingredient_all', 'units'));
    }

    public function ingredient()
    {
        $lims_ingredient_all = Ingredient::get();
        // $lims_ingredient_all = Stock::get();
        $units = Unit::get();
        return view('backend.bahan_baku.create', compact('lims_ingredient_all', 'units'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'max:255',
        ]);
        Ingredient::create([
            'name' => $request->name,
            // 'max_stock' => $request->max_stock,
            // 'first_stock' => $request->first_stock,
            // 'stock_in' => $request->first_stock,
            'unit_id' => $request->unit_id,
        ]);
        $this->cacheForget('ingredient_list');
        return redirect()->back()->with('message', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $lims_order_type_data = Ingredient::findOrFail($id);
        return $lims_order_type_data;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'max:255',
        ]);
        $ingredient = Ingredient::find($id);
        $ingredient->update([
            'name' => $request->name,
            // 'first_stock' => $request->first_stock,
            // 'stock_in' => $request->first_stock,
            // 'max_stock' => $request->max_stock,
            'unit_id' => $request->unit_id,
        ]);
        $this->cacheForget('ingredient_list');
        return redirect()->back()->with('message', 'Data berhasil diedit');
    }

    public function deleteBySelection(Request $request)
    {
        $order_type_id = $request['order_typeIdArray'];
        foreach ($order_type_id as $id) {
            $lims_order_type_data = Ingredient::find($id);
            $lims_order_type_data->is_active = false;
            $lims_order_type_data->save();
        }
        $this->cacheForget('ingredient_list');
        return 'Ingredient deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_order_type_data = Ingredient::find($id);
        $lims_order_type_data->delete();
        $this->cacheForget('ingredient_list');
        return redirect()->back()->with('not_permitted', 'Data berhasil dihapus');
    }
}
