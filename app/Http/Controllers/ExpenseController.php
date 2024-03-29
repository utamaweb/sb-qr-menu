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
        // $role = Role::find(Auth::user()->role_id);
        // if($role->hasPermissionTo('expenses-index')){
            // $permissions = Role::findByName($role->name)->permissions;
            // foreach ($permissions as $permission)
            //     $all_permission[] = $permission->name;
            // if(empty($all_permission))
            //     $all_permission[] = 'dummy text';

            if($request->starting_date) {
                $starting_date = $request->starting_date;
                $ending_date = $request->ending_date;
            }
            else {
                $starting_date = date('Y-m-01', strtotime('-1 year', strtotime(date('Y-m-d'))));
                $ending_date = date("Y-m-d");
            }

            if($request->input('warehouse_id'))
                $warehouse_id = $request->input('warehouse_id');
            else
                $warehouse_id = 0;
            $lims_expense_category_list = ExpenseCategory::get();
            $expenses = Expense::get();
            $lims_warehouse_list = Warehouse::select('name', 'id')->where('is_active', true)->get();
            // $lims_account_list = Account::where('is_active', true)->get();
            return view('backend.expense.index', compact('expenses','lims_expense_category_list', 'lims_warehouse_list', 'starting_date', 'ending_date', 'warehouse_id'));
        // }
        // else
        //     return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
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
            'warehouse_id' => $request->warehouse_id,
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
