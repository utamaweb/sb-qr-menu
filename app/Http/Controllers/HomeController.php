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

        if ($user->hasRole('Superadmin')) {
            $countAdminOutlet = User::where('role_id', 3)->count();
            $countWarehouse = Warehouse::count();
            $warehouses = Warehouse::pluck('id');
        } elseif ($user->hasRole('Admin Bisnis')) {
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
        }

        $revenue = $this->getTransactionSum([$user->warehouse_id], $startOfMonth, $endOfMonth);
        $purchase_return = $this->getTransactionQtySum([$user->warehouse_id], $startOfMonth, $endOfMonth);
        $expense = $this->getExpenseSum([$user->warehouse_id], $startOfMonth, $endOfMonth);
        $stockPurchase = $this->getStockPurchaseSum([$user->warehouse_id], $startOfMonth, $endOfMonth);
        $expense += $stockPurchase;
        $profit = $revenue - $expense;

        // Cash flow of last 6 months
        $cashFlow = $this->getCashFlow($user->warehouse_id);

        $data = compact('purchase_return', 'revenue', 'expense', 'profit', 'countBusiness', 'countWarehouse', 'countAdminBisnis', 'countAdminOutlet');

        if ($user->hasRole('Admin Bisnis')) {
            $data = array_merge($data, compact('countProduct', 'countIngredient', 'totalIncomePreviousMonth', 'totalIncomeThisMonth', 'countTransactionPreviousMonth', 'countTransactionThisMonth'));
        }

        $data = array_merge($data, $cashFlow);

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

    private function getCashFlow($warehouseId)
    {
        $cashFlow = [];
        $now = Carbon::now();
        $start = $now->copy()->subMonths(6)->startOfMonth();

        while ($start <= $now) {
            $startDate = $start->copy()->startOfMonth()->toDateString();
            $endDate = $start->copy()->endOfMonth()->toDateString();

            $recieved_amount = Transaction::where('warehouse_id', $warehouseId)
                ->where('status', 'Lunas')
                ->whereNotNull('shift_id')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_amount');

            $sent_amount = Expense::where('warehouse_id', $warehouseId)
                ->whereNotNull('shift_id')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            $stockPurchase = StockPurchase::where('warehouse_id', $warehouseId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_price');

            $sent_amount += $stockPurchase;

            $cashFlow['payment_recieved'][] = number_format((float)$recieved_amount, config('decimal'), '.', '');
            $cashFlow['payment_sent'][] = number_format((float)$sent_amount, config('decimal'), '.', '');
            $cashFlow['month'][] = $start->format('F');

            $start->addMonth();
        }

        return $cashFlow;
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
