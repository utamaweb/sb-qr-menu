<?php

namespace App\Http\Controllers;

use App\Models\Ojol;
use App\Models\Business;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\OjolWarehouse;
use App\Http\Controllers\Controller;

class OjolWarehouseController extends Controller
{
    // Index
    public function index()
    {
        $business_id = Warehouse::find(auth()->user()->warehouse_id)->business_id;
        $ojols = Ojol::where('business_id', '=', $business_id)->where('deleted_at', '=', NULL)->get();

        foreach ($ojols as $ojol) {
            $ojolWarehouseCheck = OjolWarehouse::where('warehouse_id', '=', auth()->user()->warehouse_id)->where('ojol_id', '=', $ojol->id)->exists();
            $ojolWarehouse = OjolWarehouse::where('warehouse_id', '=', auth()->user()->warehouse_id)->where('ojol_id', '=', $ojol->id)->first();

            if ($ojolWarehouseCheck) {
                $ojol->percent = $ojolWarehouse->percent;
                $ojol->extra_price = $ojolWarehouse->extra_price;
            } else {
                continue;
            }
        }

        return view('backend.ojol_warehouse.index', compact('ojols'));
    }

    // Form
    public function form(Ojol $ojol)
    {
        $ojolWarehouseCheck = OjolWarehouse::where('warehouse_id', '=', auth()->user()->warehouse_id)->where('ojol_id', '=', $ojol->id)->exists();
        $ojolWarehouse = OjolWarehouse::where('warehouse_id', '=', auth()->user()->warehouse_id)->where('ojol_id', '=', $ojol->id)->first();

        if ($ojolWarehouseCheck) {
            $ojol = $ojolWarehouse;
            $ojol->id = $ojol->ojol->id;
            $ojol->name = $ojolWarehouse->ojol->name;
        }

        return view('backend.ojol_warehouse.form', compact('ojol'));
    }

    // Store
    public function store(Request $request, Ojol $ojol)
    {
        $ojolWarehouseCheck = OjolWarehouse::where('warehouse_id', '=', auth()->user()->warehouse_id)->where('ojol_id', '=', $ojol->id)->exists();
        $ojolWarehouse = OjolWarehouse::where('warehouse_id', '=', auth()->user()->warehouse_id)->where('ojol_id', '=', $ojol->id)->first();

        $this->validate($request, [
            'name' => 'required|max:255',
            'percent' => 'nullable|numeric',
            'extra_price' => 'nullable'
        ]);

        if ($ojolWarehouseCheck) {
            $ojolWarehouse->update([
                'percent' => $request->percent,
                'extra_price' => str_replace(",", "", $request->extra_price)
            ]);
        } else {
            OjolWarehouse::create([
                'warehouse_id' => auth()->user()->warehouse_id,
                'ojol_id' => $ojol->id,
                'percent' => $request->percent,
                'extra_price' => str_replace(",", "", $request->extra_price)
            ]);
        }

        return redirect()->route('ojol-warehouse.index')->with('message', 'Data berhasil disimpan');
    }

    public function destroy(Ojol $ojol)
    {
        $ojolWarehouseCheck = OjolWarehouse::where('warehouse_id', '=', auth()->user()->warehouse_id)->where('ojol_id', '=', $ojol->id)->exists();
        $ojolWarehouse = OjolWarehouse::where('warehouse_id', '=', auth()->user()->warehouse_id)->where('ojol_id', '=', $ojol->id)->first();

        if ($ojolWarehouseCheck) {
            $ojolWarehouse->delete();
            return redirect()->route('ojol-warehouse.index')->with('message', 'Data berhasil diubah ke nilai awal!');
        } else {
            return redirect()->back()->with('message', 'Data ojol belum diubah sebelumnya!');
        }
    }
}
