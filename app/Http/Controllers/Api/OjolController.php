<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ojol;
use App\Models\Warehouse;

class OjolController extends Controller
{
    public function index() {
        $business_id = Warehouse::find(auth()->user()->warehouse_id)->business_id;
        $ojols = Ojol::where('business_id', $business_id)->get();
        return response()->json($ojols, 200);
    }
}
