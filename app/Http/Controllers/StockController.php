<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Shift;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->hasRole('Superadmin')) {
            $lims_ingredient_all = Stock::get();
        } elseif (auth()->user()->hasRole('Admin Bisnis')) {
            $warehouse_id = Warehouse::where('business_id', auth()->user()->business_id)->pluck('id');
            $lims_ingredient_all = Stock::whereIn('warehouse_id', $warehouse_id)->get();
        } else {

            $stocks = Stock::select('ingredient_id', DB::raw('MAX(id) as max_id'))->where('warehouse_id', auth()->user()->warehouse_id)->groupBy('ingredient_id')->get();
            
            $lims_ingredient_all = Stock::with('warehouse', 'ingredient', 'shift')->whereIn('id', $stocks->pluck('max_id'))->get();

            // Get shift data to check if the latest shift is open
            $shift = $lims_ingredient_all->first()->shift;
            $checkShift = ($shift->is_closed == 0) ? true : false;

        }
        return view('backend.stok.create', compact('lims_ingredient_all', 'checkShift'));
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
        $stock = Stock::find($id);
        $ingredient_id = $stock->ingredient_id;
        $warehouse_id = $stock->warehouse_id;
        // delete this ingredients from this outlet with all stocks
        Stock::where('ingredient_id', $ingredient_id)->where('warehouse_id', $warehouse_id)->delete();
        return redirect()->back()->with('not_permitted', 'Data berhasil dihapus');
    }
}
