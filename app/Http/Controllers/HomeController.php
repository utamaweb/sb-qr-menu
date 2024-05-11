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
    use \App\Traits\AutoUpdateTrait;

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
        $start_date = date("Y").'-'.date("m").'-'.'01';
        $end_date = date("Y").'-'.date("m").'-'.date('t', mktime(0, 0, 0, date("m"), 1, date("Y")));
        $yearly_sale_amount = [];
        // Elemen Paling Atas di dashboard
        $countBusiness = Business::count();
        $countAdminBisnis = User::where('role_id', 2)->count();
        if(auth()->user()->hasRole('Superadmin')){
            $countAdminOutlet = User::where('role_id', 3)->count();
            $countWarehouse = Warehouse::count();
        } elseif(auth()->user()->hasRole('Admin Bisnis')){
            $business_id = auth()->user()->business_id;
            $warehouses = Warehouse::where('business_id', $business_id)->pluck('id');
            $countAdminOutlet = User::where('role_id', 3)->whereIn('warehouse_id', $warehouses)->count();
            $countWarehouse = Warehouse::where('business_id', $business_id)->count();
            $countProduct = Product::where('business_id', $business_id)->count();
            $countIngredient = Ingredient::where('business_id', $business_id)->count();
        }
        if(auth()->user()->warehouse_id == NULL){
            $revenue = Transaction::where('status', 'Lunas')->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum(DB::raw('total_amount'));
            $purchase_return = Transaction::where('status', 'Lunas')->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('total_qty');
            $expense = Expense::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('amount');
            $stockPurchase = StockPurchase::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('total_price');
            $expense = $expense + $stockPurchase;
            $profit = $revenue - $expense;
        } else {
            $revenue = Transaction::where('status', 'Lunas')->whereDate('created_at', '>=' , $start_date)->where('warehouse_id', auth()->user()->warehouse_id)->whereDate('created_at', '<=' , $end_date)->sum(DB::raw('total_amount'));
            $purchase_return = Transaction::where('status', 'Lunas')->whereDate('created_at', '>=' , $start_date)->where('warehouse_id', auth()->user()->warehouse_id)->whereDate('created_at', '<=' , $end_date)->sum('total_qty');
            $expense = Expense::whereDate('created_at', '>=' , $start_date)->where('warehouse_id', auth()->user()->warehouse_id)->whereDate('created_at', '<=' , $end_date)->sum('amount');
            $stockPurchase = StockPurchase::whereDate('created_at', '>=' , $start_date)->where('warehouse_id', auth()->user()->warehouse_id)->whereDate('created_at', '<=' , $end_date)->sum('total_price');
            $expense = $expense + $stockPurchase;
            $profit = $revenue - $expense;
        }
        // End Elemen Paling Atas

        //cash flow of last 6 months
        $start = strtotime(date('Y-m-01', strtotime('-6 month', strtotime(date('Y-m-d') ))));
        $end = strtotime(date('Y-m-'.date('t', mktime(0, 0, 0, date("m"), 1, date("Y")))));

        // arus uang
        while($start < $end)
        {
            if(auth()->user()->warehouse_id == NULL){
                $start_date = date("Y-m", $start).'-'.'01';
                $end_date = date("Y-m", $start).'-'.date('t', mktime(0, 0, 0, date("m", $start), 1, date("Y", $start)));

                $recieved_amount = DB::table('transactions')->where('status', 'Lunas')->whereNotNull('shift_id')->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('total_amount');
                $sent_amount = DB::table('expenses')->whereNotNull('shift_id')->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('amount');
                $stockPurchase = StockPurchase::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('total_price');
                $expense_amount = Expense::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('amount');
                $sent_amount = $sent_amount + $expense_amount + $stockPurchase;
                $payment_recieved[] = number_format((float)($recieved_amount), config('decimal'), '.', '');
                $payment_sent[] = number_format((float)$sent_amount, config('decimal'), '.', '');
                $month[] = date("F", strtotime($start_date));
                $start = strtotime("+1 month", $start);
            } else {
                $start_date = date("Y-m", $start).'-'.'01';
                $end_date = date("Y-m", $start).'-'.date('t', mktime(0, 0, 0, date("m", $start), 1, date("Y", $start)));
                $recieved_amount = DB::table('transactions')->where('status', 'Lunas')->whereNotNull('shift_id')->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('warehouse_id', auth()->user()->warehouse_id)->sum('total_amount');
                $sent_amount = DB::table('expenses')->where('warehouse_id', auth()->user()->warehouse_id)->whereNotNull('shift_id')->whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('amount');
                $stockPurchase = StockPurchase::whereDate('created_at', '>=' , $start_date)->where('warehouse_id', auth()->user()->warehouse_id)->whereDate('created_at', '<=' , $end_date)->sum('total_price');
                $expense_amount = Expense::whereDate('created_at', '>=' , $start_date)->where('warehouse_id', auth()->user()->warehouse_id)->whereDate('created_at', '<=' , $end_date)->sum('amount');
                $sent_amount = $sent_amount + $expense_amount + $stockPurchase;
                $payment_recieved[] = number_format((float)($recieved_amount), config('decimal'), '.', '');
                $payment_sent[] = number_format((float)$sent_amount, config('decimal'), '.', '');
                $month[] = date("F", strtotime($start_date));
                $start = strtotime("+1 month", $start);
            }
        }
        // end arus uang
        if(auth()->user()->hasRole('Admin Bisnis')){
            return view('backend.index', compact('purchase_return','revenue', 'expense', 'profit', 'payment_recieved', 'payment_sent', 'month', 'countBusiness', 'countWarehouse','countAdminBisnis', 'countAdminOutlet', 'countProduct', 'countIngredient'));
        } elseif(auth()->user()->hasRole('Superadmin')){
            return view('backend.index', compact('purchase_return','revenue', 'expense', 'profit', 'payment_recieved', 'payment_sent', 'month', 'countBusiness', 'countWarehouse','countAdminBisnis', 'countAdminOutlet'));
        } else {
            return view('backend.index', compact('purchase_return','revenue', 'expense', 'profit', 'payment_recieved', 'payment_sent', 'month'));
        }
    }

    public function yearlyBestSellingPrice()
    {
        //making strict mode false for this query
        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();
        $yearly_best_selling_price = Product_Sale::join('products', 'products.id', '=', 'product_sales.product_id')
        ->select(DB::raw('products.name as product_name, products.code as product_code, products.image as product_images, sum(total) as total_price'))
        ->whereDate('product_sales.created_at', '>=' , date("Y").'-01-01')
        ->whereDate('product_sales.created_at', '<=' , date("Y").'-12-31')
        ->groupBy('products.code')
        ->orderBy('total_price', 'desc')
        ->take(5)
        ->get();

        return response()->json($yearly_best_selling_price);
    }

    public function yearlyBestSellingQty()
    {
        //making strict mode false for this query
        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();
        $yearly_best_selling_qty = Product_Sale::join('products', 'products.id', '=', 'product_sales.product_id')
        ->select(DB::raw('products.name as product_name, products.code as product_code, products.image as product_images, sum(product_sales.qty) as sold_qty'))
        ->whereDate('product_sales.created_at', '>=' , date("Y").'-01-01')
        ->whereDate('product_sales.created_at', '<=' , date("Y").'-12-31')
        ->groupBy('products.code')
        ->orderBy('sold_qty', 'desc')
        ->take(5)
        ->get();

        return response()->json($yearly_best_selling_qty);
    }

    public function monthlyBestSellingQty()
    {
        //making strict mode false for this query
        config()->set('database.connections.mysql.strict', false);
        DB::reconnect();
        $start_date = date("Y").'-'.date("m").'-'.'01';
        $end_date = date("Y").'-'.date("m").'-'.date('t', mktime(0, 0, 0, date("m"), 1, date("Y")));
        $best_selling_qty = Product_Sale::join('products', 'products.id', '=', 'product_sales.product_id')
        ->select(DB::raw('products.name as product_name, products.code as product_code, products.image as product_images, sum(product_sales.qty) as sold_qty'))
        ->whereDate('product_sales.created_at', '>=' , $start_date)
        ->whereDate('product_sales.created_at', '<=' , $end_date)
        ->groupBy('products.code')
        ->orderBy('sold_qty', 'desc')
        ->take(5)
        ->get();

        return response()->json($best_selling_qty);
    }

    public function recentSale()
    {
        if(Auth::user()->role_id > 2 && cache()->get('general_setting')->staff_access == 'own')
        {
            $recent_sale = Sale::join('customers', 'customers.id', '=', 'sales.customer_id')->select('sales.id','sales.reference_no','sales.sale_status','sales.created_at','sales.grand_total','sales.user_id','customers.name')->orderBy('id', 'desc')->where('sales.user_id', Auth::id())->take(5)->get();
            return response()->json($recent_sale);
        }
        else
        {
            $recent_sale = Sale::join('customers', 'customers.id', '=', 'sales.customer_id')->select('sales.id','sales.reference_no','sales.sale_status','sales.created_at','sales.grand_total','customers.name')->orderBy('id', 'desc')->take(5)->get();
            return response()->json($recent_sale);
        }
    }

    public function recentPurchase()
    {
        if(Auth::user()->role_id > 2 && cache()->get('general_setting')->staff_access == 'own')
        {
            $recent_purchase = Purchase::join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select('purchases.id','purchases.reference_no','purchases.payment_status','purchases.created_at','purchases.grand_total','purchases.user_id','suppliers.name')->orderBy('id', 'desc')->where('purchases.user_id', Auth::id())->take(5)->get();
            return response()->json($recent_purchase);
        }
        else
        {
            $recent_purchase = Purchase::join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')->select('purchases.id','purchases.reference_no','purchases.payment_status','purchases.created_at','purchases.grand_total','suppliers.name')->orderBy('id', 'desc')->take(5)->get();
            return response()->json($recent_purchase);
        }
    }

    public function recentQuotation()
    {
        if(Auth::user()->role_id > 2 && cache()->get('general_setting')->staff_access == 'own')
        {
            $recent_quotation = Quotation::join('customers', 'customers.id', '=', 'quotations.customer_id')->select('quotations.id','quotations.reference_no','quotations.quotation_status','quotations.created_at','quotations.grand_total','quotations.user_id','customers.name')->orderBy('id', 'desc')->where('quotations.user_id', Auth::id())->take(5)->get();
            return response()->json($recent_quotation);
        }
        else
        {
            $recent_quotation = Quotation::join('customers', 'customers.id', '=', 'quotations.customer_id')->select('quotations.id','quotations.reference_no','quotations.quotation_status','quotations.created_at','quotations.grand_total','customers.name')->orderBy('id', 'desc')->take(5)->get();
            return response()->json($recent_quotation);
        }
    }

    public function recentPayment()
    {
        if(Auth::user()->role_id > 2 && cache()->get('general_setting')->staff_access == 'own')
        {
            $recent_payment = Payment::select('id','payment_reference','amount','paying_method','created_at','user_id')->orderBy('id', 'desc')->where('user_id', Auth::id())->take(5)->get();
            return response()->json($recent_payment);
        }
        else
        {
            $recent_payment = Payment::select('id','payment_reference','amount','paying_method','created_at')->orderBy('id', 'desc')->take(5)->get();
            return response()->json($recent_payment);
        }
    }

    public function dashboardFilter($start_date, $end_date)
    {
        if(Auth::user()->role_id > 2 && cache()->get('general_setting')->staff_access == 'own') {
            config()->set('database.connections.mysql.strict', false);
            DB::reconnect();
            $product_sale_data = Sale::join('product_sales', 'sales.id','=', 'product_sales.sale_id')
                ->select(DB::raw('product_sales.product_id, product_sales.product_batch_id, sale_unit_id, sum(product_sales.qty) as sold_qty, sum(product_sales.total) as sold_amount'))
                ->where('sales.user_id', Auth::id())
                ->whereDate('sales.created_at', '>=' , $start_date)
                ->whereDate('sales.created_at', '<=' , $end_date)
                ->groupBy('product_sales.product_id', 'product_sales.product_batch_id')
                ->get();
            config()->set('database.connections.mysql.strict', true);
            DB::reconnect();
            $product_cost = $this->calculateAverageCOGS($product_sale_data);
            $revenue = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('user_id', Auth::id())->sum(DB::raw('grand_total - shipping_cost'));
            $return = Returns::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('user_id', Auth::id())->sum('grand_total');
            $purchase_return = ReturnPurchase::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->where('user_id', Auth::id())->sum('grand_total');
            $revenue -= $return;
            $profit = $revenue + $purchase_return - $product_cost;

            $data[0] = $revenue;
            $data[1] = $return;
            $data[2] = $profit;
            $data[3] = $purchase_return;
        }
        else{
            config()->set('database.connections.mysql.strict', false);
            DB::reconnect();
            $product_sale_data = Product_Sale::join('sales', 'product_sales.sale_id', '=', 'sales.id')
                                ->select(DB::raw('product_sales.product_id, product_sales.product_batch_id, product_sales.sale_unit_id, sum(product_sales.qty) as sold_qty, sum(product_sales.total) as sold_amount'))
                                ->whereDate('sales.created_at', '>=' , $start_date)
                                ->whereDate('sales.created_at', '<=' , $end_date)
                                ->groupBy('product_sales.product_id', 'product_sales.product_batch_id')
                                ->get();
            config()->set('database.connections.mysql.strict', true);
            DB::reconnect();
            $product_cost = $this->calculateAverageCOGS($product_sale_data);
            $revenue = Sale::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum(DB::raw('grand_total - shipping_cost'));
            $return = Returns::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('grand_total');
            $purchase_return = ReturnPurchase::whereDate('created_at', '>=' , $start_date)->whereDate('created_at', '<=' , $end_date)->sum('grand_total');
            $revenue -= $return;
            $profit = $revenue + $purchase_return - $product_cost;

            $data[0] = $revenue;
            $data[1] = $return;
            $data[2] = $profit;
            $data[3] = $purchase_return;
        }
        return $data;
    }

    public function calculateAverageCOGS($product_sale_data)
    {
        $product_cost = 0;
        foreach ($product_sale_data as $key => $product_sale) {
            $product_data = Product::select('type', 'product_list', 'variant_list', 'qty_list')->find($product_sale->product_id);
            if($product_data && $product_data->type == 'combo') {
                $product_list = explode(",", $product_data->product_list);
                if($product_data->variant_list)
                    $variant_list = explode(",", $product_data->variant_list);
                else
                    $variant_list = [];
                $qty_list = explode(",", $product_data->qty_list);

                foreach ($product_list as $index => $product_id) {
                    if(count($variant_list) && $variant_list[$index]) {
                        $product_purchase_data = ProductPurchase::where([
                            ['product_id', $product_id],
                            ['variant_id', $variant_list[$index] ]
                        ])
                        ->select('recieved', 'purchase_unit_id', 'total')
                        ->get();
                    }
                    else {
                        $product_purchase_data = ProductPurchase::where('product_id', $product_id)
                        ->select('recieved', 'purchase_unit_id', 'total')
                        ->get();
                    }
                    $total_received_qty = 0;
                    $total_purchased_amount = 0;
                    $sold_qty = ($product_sale->sold_qty - $product_sale->return_qty) * $qty_list[$index];
                    $units = Unit::select('id', 'operator', 'operation_value')->get();
                    foreach ($product_purchase_data as $key => $product_purchase) {
                        $purchase_unit_data = $units->where('id',$product_purchase->purchase_unit_id)->first();
                        if($purchase_unit_data->operator == '*')
                            $total_received_qty += $product_purchase->recieved * $purchase_unit_data->operation_value;
                        else
                            $total_received_qty += $product_purchase->recieved / $purchase_unit_data->operation_value;
                        $total_purchased_amount += $product_purchase->total;
                    }
                    if($total_received_qty)
                        $averageCost = $total_purchased_amount / $total_received_qty;
                    else
                        $averageCost = 0;
                    $product_cost += $sold_qty * $averageCost;
                }
            }
            else {
                if($product_sale->product_batch_id) {
                    $product_purchase_data = ProductPurchase::where([
                        ['product_id', $product_sale->product_id],
                        ['product_batch_id', $product_sale->product_batch_id]
                    ])
                    ->select('recieved', 'purchase_unit_id', 'total')
                    ->get();
                }
                elseif($product_sale->variant_id) {
                    $product_purchase_data = ProductPurchase::where([
                        ['product_id', $product_sale->product_id],
                        ['variant_id', $product_sale->variant_id]
                    ])
                    ->select('recieved', 'purchase_unit_id', 'total')
                    ->get();
                }
                else {
                    $product_purchase_data = ProductPurchase::where('product_id', $product_sale->product_id)
                    ->select('recieved', 'purchase_unit_id', 'total')
                    ->get();
                }
                $total_received_qty = 0;
                $total_purchased_amount = 0;
                $units = Unit::select('id', 'operator', 'operation_value')->get();
                if($product_sale->sale_unit_id) {
                    $sale_unit_data = $units->where('id', $product_sale->sale_unit_id)->first();
                    if($sale_unit_data->operator == '*')
                        $sold_qty = ($product_sale->sold_qty - $product_sale->return_qty) * $sale_unit_data->operation_value;
                    else
                        $sold_qty = ($product_sale->sold_qty - $product_sale->return_qty) / $sale_unit_data->operation_value;
                }
                else {
                    $sold_qty = ($product_sale->sold_qty - $product_sale->return_qty);
                }
                foreach ($product_purchase_data as $key => $product_purchase) {
                    $purchase_unit_data = $units->where('id', $product_purchase->purchase_unit_id)->first();
                    if($purchase_unit_data->operator == '*')
                        $total_received_qty += $product_purchase->recieved * $purchase_unit_data->operation_value;
                    else
                        $total_received_qty += $product_purchase->recieved / $purchase_unit_data->operation_value;
                    $total_purchased_amount += $product_purchase->total;
                }
                if($total_received_qty)
                    $averageCost = $total_purchased_amount / $total_received_qty;
                else
                    $averageCost = 0;
                $product_cost += $sold_qty * $averageCost;
            }
        }
        return $product_cost;
    }

    public function myTransaction($year, $month)
    {
        $start = 1;
        $number_of_day = date('t', mktime(0, 0, 0, $month, 1, $year));
        while($start <= $number_of_day)
        {
            if($start < 10)
                $date = $year.'-'.$month.'-0'.$start;
            else
                $date = $year.'-'.$month.'-'.$start;
            $sale_generated[$start] = Sale::whereDate('created_at', $date)->where('user_id', Auth::id())->count();
            $sale_grand_total[$start] = Sale::whereDate('created_at', $date)->where('user_id', Auth::id())->sum('grand_total');
            $purchase_generated[$start] = Purchase::whereDate('created_at', $date)->where('user_id', Auth::id())->count();
            $purchase_grand_total[$start] = Purchase::whereDate('created_at', $date)->where('user_id', Auth::id())->sum('grand_total');
            $quotation_generated[$start] = Quotation::whereDate('created_at', $date)->where('user_id', Auth::id())->count();
            $quotation_grand_total[$start] = Quotation::whereDate('created_at', $date)->where('user_id', Auth::id())->sum('grand_total');
            $start++;
        }
        $start_day = date('w', strtotime($year.'-'.$month.'-01')) + 1;
        $prev_year = date('Y', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
        $prev_month = date('m', strtotime('-1 month', strtotime($year.'-'.$month.'-01')));
        $next_year = date('Y', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
        $next_month = date('m', strtotime('+1 month', strtotime($year.'-'.$month.'-01')));
        return view('backend.user.my_transaction', compact('start_day', 'year', 'month', 'number_of_day', 'prev_year', 'prev_month', 'next_year', 'next_month', 'sale_generated', 'sale_grand_total','purchase_generated', 'purchase_grand_total','quotation_generated', 'quotation_grand_total'));
    }

    public function switchTheme($theme)
    {
        setcookie('theme', $theme, time() + (86400 * 365), "/");
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
