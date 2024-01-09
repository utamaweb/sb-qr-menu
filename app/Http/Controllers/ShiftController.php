<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Warehouse;
use Illuminate\Validation\Rule;
use Keygen;
use Auth;
use DB;
use App\Traits\CacheForget;

class ShiftController extends Controller
{
    use CacheForget;
    public function index()
    {
        $shifts = Shift::get();
        $warehouses = Warehouse::get();
        return view('backend.shift.create', compact('shifts', 'warehouses'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'shift_name' => 'required|max:255',
            'shift_hour' => 'required',
        ]);
        Shift::create([
            'shift_name' => $request->shift_name,
            'shift_hour' => $request->shift_hour,
            'warehouse_id' => $request->warehouse_id,
            'initial_shift_money' => $request->initial_shift_money,
        ]);
        // $this->cacheForget('ingredient_list');
        return redirect()->route('shift.index')->with('message', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'shift_name' => 'required|max:255',
            'shift_hour' => 'required',
        ]);
        $shift = Shift::find($id);
        $shift->update([
            'shift_name' => $request->shift_name,
            'shift_hour' => $request->shift_hour,
            'warehouse_id' => $request->warehouse_id,
            'initial_shift_money' => $request->initial_shift_money,
        ]);
        // $this->cacheForget('ingredient_list');
        return redirect()->route('shift.index')->with('message', 'Data berhasil diedit');
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
        $shift = Shift::find($id);
        $shift->delete();
        // $this->cacheForget('ingredient_list');
        return redirect()->route('shift.index')->with('not_permitted', 'Data berhasil dihapus');
    }
}
