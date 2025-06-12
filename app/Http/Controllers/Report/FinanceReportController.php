<?php

namespace App\Http\Controllers\Report;

use App\Models\Stock;
use App\Models\Regional;
use App\Models\Warehouse;
use App\Models\CloseCashier;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FinanceReportController extends Controller
{
    public function financeReport(Request $request){
        // Kondisi untuk filter tanggal
        if ($request->input('start_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
        } else {
            $start_date = date("Y-m-d");
            $end_date = date("Y-m-d");
        }

        $warehouses = Warehouse::where('business_id', auth()->user()->business_id)->get();
        $regionals = Regional::where('business_id', auth()->user()->business_id)->get();
        $warehouse_ids = Warehouse::where('business_id', auth()->user()->business_id)->pluck('id');

        if($request->warehouse_id != 'all' && $request->warehouse_id != null){
            $warehouse_id = [(int) $request->warehouse_id];
        } else {
            $warehouse_id = $warehouse_ids;
        }

        $warehouse_request = $request->get('warehouse_id');
        $regional_request = $request->get('regional_id');

        if($regional_request){
            $warehouses = Warehouse::where('business_id', auth()->user()->business_id)
                ->where('regional_id', $regional_request)
                ->get();
        }

        // Get finance data with appropriate relationships and filters
        $finance = CloseCashier::with([
                'shift.warehouse.regional',
                'shift.transactions' => function($query) {
                    $query->where('status', 'Batal');
                }
            ])
            ->whereHas('shift', function($query) use ($warehouse_id) {
                // Only include records where the related shift's warehouse_id is in the selected warehouse(s)
                $query->whereIn('warehouse_id', $warehouse_id);
            });

        // Apply regional filter if specified
        if ($regional_request != 'all' && $regional_request != null) {
            $finance->whereHas('shift.warehouse', function($query) use ($regional_request) {
                $query->where('regional_id', $regional_request);
            });
        }

        // Apply date range and get results
        // Now ordering by warehouse name, then by shift number, then by created_at date
        $finance = $finance->join('shifts', 'close_cashiers.shift_id', '=', 'shifts.id')
            ->join('warehouses', 'shifts.warehouse_id', '=', 'warehouses.id')
            ->select('close_cashiers.*')
            ->whereBetween('close_cashiers.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->orderBy('warehouses.name')
            ->orderBy('shifts.shift_number')
            ->orderBy('close_cashiers.created_at', 'desc')
            ->get();

        // Process canceled transactions data
        $finance->each(function($item) {
            // Count of canceled transactions
            $item->count_transaction_canceled = $item->shift->transactions->count();

            // Total amount of canceled transactions
            $item->total_transaction_canceled = $item->shift->transactions->sum('total_amount');
        });

        return view('backend.report.finance_report', compact(
            'start_date',
            'end_date',
            'finance',
            'warehouse_id',
            'warehouses',
            'warehouse_request',
            'regionals',
            'regional_request'
        ));
    }

    public function getWarehousesByRegional($regional_id)
    {
        if($regional_id == 'all') {
            $warehouses = Warehouse::where('business_id', auth()->user()->business_id)->get();
        } else {
            $warehouses = Warehouse::where('business_id', auth()->user()->business_id)
                        ->where('regional_id', $regional_id)
                        ->get();
        }

        return response()->json($warehouses);
    }
}
