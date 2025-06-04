<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\StockPurchase;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Business;
use App\Models\Warehouse;
use App\Models\Account;
use App\Models\User;
use App\Models\Product_Warehouse;
use App\Models\Unit;
use Cache;
use DB;
use Auth;
use Storage;
use Illuminate\Support\Str;
use Printing;
use Rawilk\Printing\Contracts\Printer;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
/*use vendor\autoload;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;*/

class HomeController extends Controller
{
    public function home()
    {
        return view('backend.home');
    }

    public function index()
    {
        return redirect('dashboard');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $now = Carbon::now();
        $startOfMonth = $now->startOfMonth()->toDateString();
        $endOfMonth = $now->endOfMonth()->toDateString();
        $startOfLastMonth = $now->copy()->subMonthNoOverflow()->startOfMonth()->toDateString();
        $endOfLastMonth = $now->copy()->subMonthNoOverflow()->endOfMonth()->toDateString();

        // Elemen Paling Atas di dashboard
        $countBusiness = Business::count();
        $countAdminBisnis = User::where('role_id', 2)->count();

        $countWarehouse = 0;
        $countAdminOutlet = 0;
        $countProduct = 0;
        $countIngredient = 0;
        $totalIncomeThisMonth = 0;
        $totalIncomePreviousMonth = 0;
        $countTransactionThisMonth = 0;
        $countTransactionPreviousMonth = 0;

        if ($user->hasRole('Superadmin')) {
            $countAdminOutlet = User::where('role_id', 3)->count();
            $countWarehouse = Warehouse::count();
            $warehouses = Warehouse::pluck('id');

            $data = compact('countBusiness', 'countWarehouse', 'countAdminBisnis', 'countAdminOutlet');
        } elseif ($user->hasRole(['Admin Bisnis', 'Report'])) {
            $business_id = $user->business_id;
            $warehouses = Warehouse::where('business_id', $business_id)->pluck('id');
            $countAdminOutlet = User::where('role_id', 3)->whereIn('warehouse_id', $warehouses)->count();
            $countWarehouse = Warehouse::where('business_id', $business_id)->count();
            $countProduct = Product::where('business_id', $business_id)->count();
            $countIngredient = Ingredient::where('business_id', $business_id)->count();

            $totalIncomeThisMonth = $this->getTransactionSum($warehouses, $startOfMonth, $endOfMonth);
            $totalIncomePreviousMonth = $this->getTransactionSum($warehouses, $startOfLastMonth, $endOfLastMonth);
            $countTransactionThisMonth = $this->getTransactionCount($warehouses, $startOfMonth, $endOfMonth);
            $countTransactionPreviousMonth = $this->getTransactionCount($warehouses, $startOfLastMonth, $endOfLastMonth);

            $data = compact('countBusiness', 'countWarehouse', 'countAdminBisnis', 'countAdminOutlet', 'totalIncomeThisMonth', 'totalIncomePreviousMonth', 'countTransactionThisMonth', 'countTransactionPreviousMonth', 'countProduct', 'countIngredient');
        } else {
            $revenue = $this->getTransactionSum([$user->warehouse_id], $startOfMonth, $endOfMonth);
            $purchase_return = $this->getTransactionQtySum([$user->warehouse_id], $startOfMonth, $endOfMonth);
            $expense = $this->getExpenseSum([$user->warehouse_id], $startOfMonth, $endOfMonth);
            $stockPurchase = $this->getStockPurchaseSum([$user->warehouse_id], $startOfMonth, $endOfMonth);
            $expense += $stockPurchase;
            $profit = $revenue - $expense;

            $data = compact(
                'purchase_return', 'revenue', 'expense', 'profit', 'countBusiness', 'countWarehouse', 'countAdminBisnis', 'countAdminOutlet',
                'countProduct', 'countIngredient', 'totalIncomePreviousMonth', 'totalIncomeThisMonth', 'countTransactionPreviousMonth', 'countTransactionThisMonth'
            );
        }

        return view('backend.index', $data);
    }

    private function getTransactionSum($warehouses, $startDate, $endDate)
    {
        return Transaction::whereIn('warehouse_id', $warehouses)
            ->where('status', 'Lunas')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('total_amount');
    }

    private function getTransactionQtySum($warehouses, $startDate, $endDate)
    {
        return Transaction::whereIn('warehouse_id', $warehouses)
            ->where('status', 'Lunas')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('total_qty');
    }

    private function getExpenseSum($warehouses, $startDate, $endDate)
    {
        return Expense::whereIn('warehouse_id', $warehouses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
    }

    private function getStockPurchaseSum($warehouses, $startDate, $endDate)
    {
        return StockPurchase::whereIn('warehouse_id', $warehouses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_price');
    }

    private function getTransactionCount($warehouses, $startDate, $endDate)
    {
        return Transaction::whereIn('warehouse_id', $warehouses)
            ->where('status', 'Lunas')
            ->whereBetween('date', [$startDate, $endDate])
            ->count();
    }


    public function uploadApk(Request $request)
    {
        $dateNow = Carbon::now()->format('Y-m-d');
        $apk = $request->apk;
        if ($apk) {
            Storage::deleteDirectory('public/apk');
            $apkName = 'sb-pos.' . $apk->getClientOriginalExtension();
            // $apkName = 'sb-pos' . '-' . $dateNow . '.' . $apk->getClientOriginalExtension();
            $uploadApk = $apk->storeAs('public/apk', $apkName);
        }
        return redirect()->back()->with('message', 'APK Berhasil Diupload');
    }

}
