<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Shift;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function category() {
        $expense_category = ExpenseCategory::get();
        return response()->json($expense_category, 200);
    }

    public function getExpense() {
        $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                ->where('user_id', auth()->user()->id)
                ->where('is_closed', 0)
                ->first();
        $expenses = Expense::where('shift_id', $shift->id)->get()->map(function ($item){
            $item->warehouse_name = $item->warehouse->name;
            $item->expense_category_name = $item->expenseCategory->name;
            $item->created_by = $item->user->name;
            return $item;
        });
        return response()->json($expenses, 200);
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
                ->where('user_id', auth()->user()->id)
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
            return response()->json($shift, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
