<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Check Self Service Status
    public function checkService() {
        return response()->json([
            'status'          => 'Ok',
            'warehouse'       => auth()->user()->warehouse->name,
            'is_self_service' => (auth()->user()->warehouse->is_self_service == 1) ? true : false
        ], 200);
    }

    public function checkEditOrder() {
        return response()->json([
            'status'         => 'Ok',
            'warehouse'      => auth()->user()->warehouse->name,
            'can_edit_order' => (auth()->user()->warehouse->can_edit_order == 1) ? true : false
        ], 200);
    }
}
