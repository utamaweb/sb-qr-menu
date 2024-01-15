<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Warehouse;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $data = $request->all();
        $validator = Validator::make($data, [
            'order_type_id' => 'required',
            'payment_method' => 'required',
            'transaction_details' => 'required|array|min:1', // minimal ada satu transaksi_detail
            'transaction_details.*.product_id' => 'required|numeric',
            'transaction_details.*.qty' => 'required|numeric',
            'transaction_details.*.subtotal' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();

        try {
            $total_amount = 0;
            foreach ($request->transaction_details as $detail) {
                $total_amount += $detail['subtotal'];
            }
            $total_qty = 0;
            foreach ($request->transaction_details as $detail) {
                $total_qty += $detail['qty'];
            }
            $change_money = $request->paid_amount - $total_amount;
            $dateNow = Carbon::now()->format('Y-m-d');
            $dateTimeNow = Carbon::now();
            $sequence_number = Transaction::where('date', $dateNow)->orderBy('id', 'DESC')->first()->sequence_number;
            $transaction = Transaction::create([
                'warehouse_id' => auth()->user()->warehouse_id,
                'sequence_number' => $sequence_number + 1,
                'order_type_id' => $request->order_type_id,
                'user_id' => $request->user_id,
                'payment_method' => $request->payment_method,
                'date' => $dateNow,
                'notes' => $request->notes,
                'total_amount' => $total_amount,
                'total_qty' => $total_qty,
                'paid_amount' => $request->paid_amount,
                'change_money' => $change_money,
            ]);

            // Simpan detail transaksi
            $transaction_details = $request->input('transaction_details');
            foreach ($transaction_details as $detail) {
                $transaction->transaction_details()->create($detail);
            }
            $transaction['details'] = $transaction_details;
            $transaction['warehouse'] = Warehouse::where('id', auth()->user()->warehouse_id)->first();
            // $transaction['warehouse']['name'] = $transaction['warehouse']->name;
            // $transaction['warehouse']['address'] = $transaction['warehouse']->address;
            $transaction['datetime'] = $transaction->created_at->isoFormat('D MMM Y H:m');
            $transaction['paid_at'] = $dateTimeNow->isoFormat('D MMM Y H:m');
            $transaction['product_count'] = count($request->transaction_details);

            DB::commit();
            return response()->json($transaction, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
