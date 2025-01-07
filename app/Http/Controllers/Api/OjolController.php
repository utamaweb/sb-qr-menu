<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ojol;
use App\Models\Warehouse;
use App\Models\OrderType;
use Illuminate\Http\Request;
use App\Models\OjolWarehouse;

class OjolController extends Controller
{
    public function index()
    {
        $ojols = Ojol::where('business_id', '=', auth()->user()->warehouse->business_id)->where('deleted_at', '=', NULL)->get();

        foreach ($ojols as $ojol) {
            $ojolWarehouse = OjolWarehouse::where('warehouse_id', '=', auth()->user()->warehouse_id)->where('ojol_id', '=', $ojol->id)->first();

            if ($ojolWarehouse != null) {
                $ojol->percent     = $ojolWarehouse->percent;
                $ojol->extra_price = $ojolWarehouse->extra_price;
            } else {
                continue;
            }

            $ojol['type'] = "online";
        }

        // Check outlet service type
        $serviceType = auth()->user()->warehouse->is_self_service;
        if($serviceType == 0) {
            // Get order types
            $orderTypes = OrderType::all();

            // Loop to push order types to ojols
            foreach($orderTypes as $item) {
                $item['type'] = "offline";
                $ojols->push($item);
            }
        }

        return response()->json($ojols, 200);
    }
}
