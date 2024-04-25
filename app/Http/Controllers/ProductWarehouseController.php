<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product_Warehouse;
use App\Models\Warehouse;
use App\Models\Product;

class ProductWarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productWarehouses = Product_Warehouse::where('warehouse_id', auth()->user()->warehouse_id)->get();
        return view('backend.product_warehouse.index', compact('productWarehouses'));
    }

    public function create()
    {
        $warehouse_id = auth()->user()->warehouse_id;
        $warehouse = Warehouse::find($warehouse_id);
        if(auth()->user()->hasRole('Superadmin')){
            $warehouses = Warehouse::get();
            $products = Product::get();
        } else{
            $warehouses = Warehouse::where('id', $warehouse_id)->get();
            $products = Product::where('business_id', $warehouse->business_id)->get();
        }
        return view('backend.product_warehouse.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required',
            'warehouse_id' => 'required',
            'price' => 'required',
        ]);
        Product_Warehouse::create([
            'product_id' => $request->product_id,
            'warehouse_id' => $request->warehouse_id,
            'price' => $request->price,
        ]);
        return redirect()->route('produk-outlet.index')->with('message', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $warehouse_id = auth()->user()->warehouse_id;
        $warehouse = Warehouse::find($warehouse_id);
        $productWarehouse = Product_Warehouse::find($id);
        if(auth()->user()->hasRole('Superadmin')){
            $warehouses = Warehouse::get();
            $products = Product::get();
        } else{
            $warehouses = Warehouse::where('id', $warehouse_id)->get();
            $products = Product::where('business_id', $warehouse->business_id)->get();
        }
        return view('backend.product_warehouse.edit', compact('productWarehouse', 'warehouses', 'products'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'product_id' => 'required',
            'warehouse_id' => 'required',
            'price' => 'required',
        ]);
        Product_Warehouse::find($id)->update([
            'product_id' => $request->product_id,
            'warehouse_id' => $request->warehouse_id,
            'price' => $request->price,
        ]);
        return redirect()->route('produk-outlet.index')->with('message', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Product_Warehouse::find($id)->delete();
        return redirect()->back()->with('not_permitted', 'Data berhasil dihapus');
    }
}
