<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Warehouse;
use App\Models\Business;
use App\Models\Shift;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function category() {
        $warehouse = Warehouse::where('id', auth()->user()->warehouse_id)->first();
        $business_id = $warehouse->business_id;
        $expense_category = ExpenseCategory::where('business_id', $business_id)->get();
        return response()->json($expense_category, 200);
    }

    public function getExpense() {
        $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                // ->where('user_id', auth()->user()->id)
                ->where('is_closed', 0)
                ->first();
        $expenses['data'] = Expense::where('shift_id', $shift->id)->get()->map(function ($item){
            $filteredData = [
                'id' => $item->id,
                'outlet' => $item->warehouse->name,
                'date' => $item->created_at->format('d-m-Y H:i:s'),
                'expense_category_id' => $item->expense_category_id,
                'expense_category' => $item->expenseCategory->name,
                'qty' => (int) $item->qty,
                'total_price' => (int) $item->amount,
                'created_by' => $item->user->name,
                'notes' => $item->note
            ];
            return $filteredData;
        });
        $expenses['total_expense'] = $expenses['data']->sum('total_price');
        return response()->json($expenses, 200);
    }

    public function show($id) {
        // $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
        //         ->where('user_id', auth()->user()->id)
        //         ->where('is_closed', 0)
        //         ->first();
        $expense = Expense::find($id);
        // unset($expense);
        $response['id'] = $expense->id;
        $response['expense_category'] = $expense->expenseCategory->name;
        $response['qty'] = $expense->qty;
        $response['total_price'] = $expense->amount;
        $response['note'] = $expense->note;
        $response['date'] = $expense->created_at->format('d-m-Y');
        return response()->json($response, 200);
    }

    public function add(Request $request) {
        $data = $request->all();
        $validator = Validator::make($data, [
            'expense_category_id' => 'required',
            'qty' => 'required',
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();

        try {
            $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                // ->where('user_id', auth()->user()->id)
                ->where('is_closed', 0)
                ->first();
            $expense = Expense::create([
                'expense_category_id' => $request->expense_category_id,
                'qty' => $request->qty,
                'amount' => $request->amount,
                'note' => $request->note,
                'warehouse_id' => auth()->user()->warehouse_id,
                'user_id' => auth()->user()->id,
                'shift_id' => $shift->id
            ]);
            DB::commit();
            return response()->json($expense, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function edit(Request $request, $id) {
        $data = $request->all();
        $validator = Validator::make($data, [
            'expense_category_id' => 'required',
            'qty' => 'required',
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();

        try {
            $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                ->where('user_id', auth()->user()->id)
                ->where('is_closed', 0)
                ->first();
            $expense = Expense::findOrFail($id)->update([
                'expense_category_id' => $request->expense_category_id,
                'qty' => $request->qty,
                'amount' => $request->amount,
                'note' => $request->note,
                'warehouse_id' => auth()->user()->warehouse_id,
                'user_id' => auth()->user()->id,
                'shift_id' => $shift->id
            ]);
            $expenseDetail = Expense::find($id);
            DB::commit();
            return response()->json($expenseDetail, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
