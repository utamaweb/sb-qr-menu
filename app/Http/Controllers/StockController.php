<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(auth()->user()->hasRole('Superadmin')){
            $lims_ingredient_all = Stock::get();
        } elseif(auth()->user()->hasRole('Admin Bisnis')) {
            $warehouse_id = Warehouse::where('business_id', auth()->user()->business_id)->pluck('id');
            $lims_ingredient_all = Stock::whereIn('warehouse_id', $warehouse_id)->get();
        } else{
            $lims_ingredient_all = Stock::where('warehouse_id', auth()->user()->warehouse_id)->get();
        }
        return view('backend.stok.create', compact('lims_ingredient_all'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stock $stock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $lims_order_type_data = Stock::find($id)->delete();
        return redirect()->back()->with('not_permitted', 'Data berhasil dihapus');
    }
}
