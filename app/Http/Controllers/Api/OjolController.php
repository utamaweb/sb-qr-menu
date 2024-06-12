<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ojol;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\OjolWarehouse;

class OjolController extends Controller
{
    public function index()
    {
        $business_id = Warehouse::find(auth()->user()->warehouse_id)->business_id;
        $ojols = Ojol::where('business_id', $business_id)->get();
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
        return response()->json($ojols, 200);
    }
}
