<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Warehouse;
use Illuminate\Support\Str;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Yajra\DataTables\Facades\DataTables;


class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        // Kondisi untuk filter tanggal
        if ($request->input('start_date')) {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
        } else {
            $start_date = date("Y-m-d");
            $end_date = date("Y-m-d");
        }

        // get warehouse data and expense data
        $warehouses = Warehouse::where('business_id', auth()->user()->business_id)->where('is_active', true)->get();

        // condition for user access
        if(auth()->user()->hasRole(['Admin Bisnis', 'Report'])) {
            $warehouseId = $request->get('warehouse_id') ?? null;
        } else {
            $warehouseId = auth()->user()->warehouse_id;
        }

        $warehouse = Warehouse::where('id', $warehouseId)->first();
        $expenses = Expense::with('expenseCategory', 'warehouse', 'user')
            ->where('warehouse_id', $warehouseId)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->orderBy('id', 'desc')
            ->get();

        return view('backend.expense.index', compact('start_date', 'end_date', 'expenses', 'warehouseId', 'warehouse', 'warehouses'));
    }

    public function getExpenses(Request $request)
    {
        $query = Expense::with('expenseCategory', 'warehouse', 'user');

        $warehouse_id = Warehouse::where('business_id', auth()->user()->business_id)->pluck('id');
        $query->whereIn('warehouse_id', $warehouse_id);

        return DataTables::of($query)
            ->addColumn('DT_RowIndex', function($expense) use ($request) {
                static $index = 0;
                return $request->input('start') + ++$index; // Nomor urut otomatis
            })
            ->editColumn('created_at', function($expense) {
                return $expense->created_at->format('d-m-Y H:i:s');
            })
            ->rawColumns(['DT_RowIndex'])
            ->make(true);
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

    public function export($warehouseId){
        $start_date = request()->start_date;
        $end_date = request()->end_date;
        $query = Expense::with('expenseCategory', 'warehouse', 'user')
            ->where('warehouse_id', $warehouseId)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->orderBy('id', 'desc');

        $totalCost = $query->sum('amount');
        $warehouse = Warehouse::findOrFail($warehouseId);

        $data      = $query->distinct()->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Laporan Pengeluaran Outlet ' . $warehouse->name . ': ' . $start_date . ' s/d ' . $end_date);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['No.', 'Pengeluaran', 'Keterangan', 'Kuantitas', 'Total', 'Outlet', 'Dibuat | Waktu'];
        $sheet->fromArray($headers, NULL, 'A3');

        $headerRange = 'A3:G3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach ($data as $index => $item) {
            $row = [
                $index + 1,
                $item->expenseCategory->name,
                $item->note,
                $item->qty,
                'Rp. ' . number_format($item->amount, 0, ',', '.'),
                $item->warehouse->name,
                $item->created_at,
            ];
            $sheet->fromArray($row, NULL, 'A' . ($index + 4));
        }

        $sheet->setCellValue('D' . (count($data) + 5), 'Total Keseluruhan:');
        $sheet->setCellValue('E' . (count($data) + 5), 'Rp. ' . number_format($totalCost, 0, ',', '.'));

        $lastRow = count($data) + 6;
        $sheet->getStyle('A3:G' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $sheet->setAutoFilter('A3:G' . (count($data) + 3));

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'laporan_pengeluaran_' . Str::slug($warehouse->name) . '_' . $start_date . '_' . $end_date . '.xlsx';

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
