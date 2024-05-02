<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Account;
use App\Models\Warehouse;
use App\Models\CashRegister;
use App\Models\Shift;
use App\Models\ExpenseCategory;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        if(auth()->user()->hasRole('Superadmin')){
            $lims_expense_category_list = ExpenseCategory::get();
            $expenses = Expense::get();
        } elseif(auth()->user()->hasRole('Admin Bisnis')){
            $lims_expense_category_list = ExpenseCategory::where('business_id', auth()->user()->business_id)->get();
            $warehouse_id = Warehouse::where('business_id', auth()->user()->business_id)->pluck('id');
            $expenses = Expense::whereIn('warehouse_id', $warehouse_id)->get();
        } else{
            $warehouse = Warehouse::where('id', auth()->user()->warehouse_id)->first();
            $business_id = $warehouse->business_id;
            $lims_expense_category_list = ExpenseCategory::where('business_id', $business_id)->get();
            $expenses = Expense::where('warehouse_id', auth()->user()->warehouse_id)->get();
        }
        $lims_warehouse_list = Warehouse::select('name', 'id')->where('is_active', true)->get();
        return view('backend.expense.index', compact('expenses','lims_expense_category_list', 'lims_warehouse_list'));

    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $dateNow = Carbon::now()->format('Y-m-d');
        $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)->where('date', $dateNow)->where('user_id', auth()->user()->id)->where('is_closed', 0)->first();
        if($shift == NULL){
            return redirect()->back()->with('not_permitted', 'Belum ada kasir yang dibuka');
        }
        Expense::create([
            // 'name' => $request->name,
            'qty' => $request->qty,
            'shift_id' => $shift->id,
            'expense_category_id' => $request->expense_category_id,
            'warehouse_id' => Auth::user()->warehouse_id,
            'amount' => $request->amount,
            'note' => $request->note,
            'user_id' => Auth::id()
        ]);
        return redirect()->back()->with('message', 'Data Berhasil Ditambahkan');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
            $lims_expense_data = Expense::find($id);
            $lims_expense_data->date = date('d-m-Y', strtotime($lims_expense_data->created_at->toDateString()));
            return $lims_expense_data;
    }

    public function update(Request $request, $id)
    {
        $lims_expense_data = Expense::find($id);
        $lims_expense_data->update([
            'name' => $request->name,
            'qty' => $request->qty,
            'expense_category_id' => $request->expense_category_id,
            // 'warehouse_id' => $request->warehouse_id,
            'amount' => $request->amount,
            'note' => $request->note,
        ]);
        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $expense_id = $request['expenseIdArray'];
        foreach ($expense_id as $id) {
            $lims_expense_data = Expense::find($id);
            $lims_expense_data->delete();
        }
        return 'Expense deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_expense_data = Expense::find($id);
        $lims_expense_data->delete();
        return redirect()->back()->with('not_permitted', 'Data deleted successfully');
    }
}
