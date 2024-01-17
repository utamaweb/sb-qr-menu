<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CloseCashier;
use App\Models\CloseCashierProductSold;
use Auth;
use DB;
use App\Traits\CacheForget;

class CloseCashierController extends Controller
{
    use CacheForget;
    public function index()
    {
        $closeCashiers = CloseCashier::get();
        return view('backend.close_cashier.index', compact('closeCashiers'));
    }

    public function show($id) {
        $closeCashier = CloseCashier::find($id);
        $closeCashierProductSolds = CloseCashierProductSold::where('close_cashier_id', $id)->get();
        return view('backend.close_cashier.show', compact('closeCashier','closeCashierProductSolds'));
    }

}
