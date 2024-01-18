<ul id="side-main-menu" class="side-menu list-unstyled">
   {{-- DASHBOARD --}}
   <li><a href="{{url('/dashboard')}}"> <i
      class="dripicons-meter"></i><span>Beranda</span></a></li>

   {{-- PENGELUARAN --}}
   <?php
      $index_permission_active = $role_has_permissions_list->where('name', 'expenses-index')->first();
      ?>
   @if($index_permission_active)
   <li>
      <a href="#expense" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-wallet"></i><span>Biaya Pengeluaran</span></a>
      <ul id="expense" class="collapse list-unstyled " style="border-radius: 10px;">
        @if(auth()->user()->hasRole('Superadmin'))
         <li id="exp-cat-menu"><a href="{{route('expense_categories.index')}}">Nama Pengeluaran</a>
         </li>
         @endif
         <li id="exp-list-menu"><a href="{{route('expenses.index')}}">Daftar Pengeluaran</a></li>
         {{-- <?php
            $add_permission_active = $role_has_permissions_list->where('name', 'expenses-add')->first();
            ?>
         @if($add_permission_active)
         <li><a id="add-expense" href=""> {{trans('file.Add Expense')}}</a>
         </li>
         @endif --}}
      </ul>
   </li>
   @endif

   {{-- LAPORAN --}}
   <?php
      $profit_loss_active = $role_has_permissions_list->where('name', 'profit-loss')->first();

      $best_seller_active = $role_has_permissions_list->where('name', 'best-seller')->first();

      $warehouse_report_active = $role_has_permissions_list->where('name', 'warehouse-report')->first();

      $warehouse_stock_report_active = $role_has_permissions_list->where('name', 'warehouse-stock-report')->first();

      $product_report_active = $role_has_permissions_list->where('name', 'product-report')->first();

      $daily_sale_active = $role_has_permissions_list->where('name', 'daily-sale')->first();

      $monthly_sale_active = $role_has_permissions_list->where('name', 'monthly-sale')->first();

      $daily_purchase_active = $role_has_permissions_list->where('name', 'daily-purchase')->first();

      $monthly_purchase_active = $role_has_permissions_list->where('name', 'monthly-purchase')->first();

      $purchase_report_active = $role_has_permissions_list->where('name', 'purchase-report')->first();

      $sale_report_active = $role_has_permissions_list->where('name', 'sale-report')->first();

      $sale_report_chart_active = $role_has_permissions_list->where('name', 'sale-report-chart')->first();

      $payment_report_active = $role_has_permissions_list->where('name', 'payment-report')->first();

      $product_expiry_report_active = $role_has_permissions_list->where('name', 'product-expiry-report')->first();

      $product_qty_alert_active = $role_has_permissions_list->where('name', 'product-qty-alert')->first();

      $dso_report_active = $role_has_permissions_list->where('name', 'dso-report')->first();

      $user_report_active = $role_has_permissions_list->where('name', 'user-report')->first();

      $customer_report_active = $role_has_permissions_list->where('name', 'customer-report')->first();

      $supplier_report_active = $role_has_permissions_list->where('name', 'supplier-report')->first();

      $due_report_active = $role_has_permissions_list->where('name', 'due-report')->first();

      $supplier_due_report_active = $role_has_permissions_list->where('name', 'supplier-due-report')->first();

      ?>
   @if($profit_loss_active || $best_seller_active || $warehouse_report_active || $warehouse_stock_report_active ||
   $product_report_active || $daily_sale_active || $monthly_sale_active || $daily_purchase_active ||
   $monthly_purchase_active || $purchase_report_active || $sale_report_active || $sale_report_chart_active ||
   $payment_report_active || $product_expiry_report_active || $product_qty_alert_active || $dso_report_active ||
   $user_report_active || $customer_report_active || $supplier_report_active || $due_report_active ||
   $supplier_due_report_active)
   <li>
      <a href="#report" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-document-remove"></i><span>{{trans('file.Reports')}}</span></a>
      <ul id="report" class="collapse list-unstyled " style="border-radius: 10px;">
        <li id="stock-opname"><a  href="{{route('close-cashier.index')}}">Laporan Tutup Kasir</a></li>
         @if($profit_loss_active)
         <li id="profit-loss-report-menu">
            {!! Form::open(['route' => 'report.profitLoss', 'method' => 'post', 'id' =>
            'profitLoss-report-form']) !!}
            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <a id="profitLoss-link" href="">{{trans('file.Summary Report')}}</a>
            {!! Form::close() !!}
         </li>
         @endif

         @if($best_seller_active)
         <li id="best-seller-report-menu">
            <a href="{{url('report/best_seller')}}">{{trans('file.Best Seller')}}</a>
         </li>
         @endif
         @if($product_report_active)
         <li id="product-report-menu">
            {!! Form::open(['route' => 'report.product', 'method' => 'get', 'id' => 'product-report-form']) !!}
            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <input type="hidden" name="warehouse_id" value="0" />
            <a id="report-link" href="">{{trans('file.Product Report')}}</a>
            {!! Form::close() !!}
         </li>
         @endif
         @if($daily_sale_active)
         <li id="daily-sale-report-menu">
            <a href="{{url('report/daily_sale/'.date('Y').'/'.date('m'))}}">{{trans('file.Daily Sale')}}</a>
         </li>
         @endif
         @if($monthly_sale_active)
         <li id="monthly-sale-report-menu">
            <a href="{{url('report/monthly_sale/'.date('Y'))}}">{{trans('file.Monthly Sale')}}</a>
         </li>
         @endif
         @if($daily_purchase_active)
         <li id="daily-purchase-report-menu">
            <a
               href="{{url('report/daily_purchase/'.date('Y').'/'.date('m'))}}">{{trans('file.Daily Purchase')}}</a>
         </li>
         @endif
         @if($monthly_purchase_active)
         <li id="monthly-purchase-report-menu">
            <a href="{{url('report/monthly_purchase/'.date('Y'))}}">{{trans('file.Monthly Purchase')}}</a>
         </li>
         @endif
         @if($sale_report_active)
         <li id="sale-report-menu">
            {!! Form::open(['route' => 'report.sale', 'method' => 'post', 'id' => 'sale-report-form']) !!}
            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <input type="hidden" name="warehouse_id" value="0" />
            <a id="sale-report-link" href="">{{trans('file.Sale Report')}}</a>
            {!! Form::close() !!}
         </li>
         @endif
         @if($sale_report_chart_active)
         <li id="sale-report-chart-menu">
            {!! Form::open(['route' => 'report.saleChart', 'method' => 'post', 'id' =>
            'sale-report-chart-form']) !!}
            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <input type="hidden" name="warehouse_id" value="0" />
            <input type="hidden" name="time_period" value="weekly" />
            <a id="sale-report-chart-link" href="">{{trans('file.Sale Report Chart')}}</a>
            {!! Form::close() !!}
         </li>
         @endif
         @if($payment_report_active)
         <li id="payment-report-menu">
            {!! Form::open(['route' => 'report.paymentByDate', 'method' => 'post', 'id' =>
            'payment-report-form']) !!}
            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <a id="payment-report-link" href="">{{trans('file.Payment Report')}}</a>
            {!! Form::close() !!}
         </li>
         @endif
         @if($purchase_report_active)
         <li id="purchase-report-menu">
            {!! Form::open(['route' => 'report.purchase', 'method' => 'post', 'id' => 'purchase-report-form'])
            !!}
            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <input type="hidden" name="warehouse_id" value="0" />
            <a id="purchase-report-link" href="">{{trans('file.Purchase Report')}}</a>
            {!! Form::close() !!}
         </li>
         @endif
         @if($customer_report_active)
         <li id="customer-report-menu">
            <a id="customer-report-link" href="">{{trans('file.Customer Report')}}</a>
         </li>
         @endif
         @if($customer_report_active)
         <li id="customer-report-menu">
            <a id="customer-group-report-link" href="">{{trans('file.Customer Group Report')}}</a>
         </li>
         @endif
         @if($due_report_active)
         <li id="due-report-menu">
            {!! Form::open(['route' => 'report.customerDueByDate', 'method' => 'post', 'id' =>
            'customer-due-report-form']) !!}
            <input type="hidden" name="start_date" value="{{date('Y-m-d', strtotime('-1 year'))}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <a id="due-report-link" href="">{{trans('file.Customer Due Report')}}</a>
            {!! Form::close() !!}
         </li>
         @endif
         @if($supplier_report_active)
         <li id="supplier-report-menu">
            <a id="supplier-report-link" href="">{{trans('file.Supplier Report')}}</a>
         </li>
         @endif
         @if($supplier_due_report_active)
         <li id="supplier-due-report-menu">
            {!! Form::open(['route' => 'report.supplierDueByDate', 'method' => 'post', 'id' =>
            'supplier-due-report-form']) !!}
            <input type="hidden" name="start_date" value="{{date('Y-m-d', strtotime('-1 year'))}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <a id="supplier-due-report-link" href="">{{trans('file.Supplier Due Report')}}</a>
            {!! Form::close() !!}
         </li>
         @endif
         @if($warehouse_report_active)
         <li id="warehouse-report-menu">
            <a id="warehouse-report-link" href="">Laporan Cabang</a>
         </li>
         @endif
         @if($warehouse_stock_report_active)
         <li id="warehouse-stock-report-menu">
            <a href="{{route('report.warehouseStock')}}">Chart Persediaan Cabang</a>
         </li>
         @endif
         @if($product_expiry_report_active)
         <li id="productExpiry-report-menu">
            <a href="{{route('report.productExpiry')}}">{{trans('file.Product Expiry Report')}}</a>
         </li>
         @endif
         @if($product_qty_alert_active)
         <li id="qtyAlert-report-menu">
            <a href="{{route('report.qtyAlert')}}">{{trans('file.Product Quantity Alert')}}</a>
         </li>
         @endif
         @if($dso_report_active)
         <li id="daily-sale-objective-menu">
            <a href="{{route('report.dailySaleObjective')}}">{{trans('file.Daily Sale Objective Report')}}</a>
         </li>
         @endif
         @if($user_report_active)
         <li id="user-report-menu">
            <a id="user-report-link" href="">{{trans('file.User Report')}}</a>
         </li>
         @endif
      </ul>
   </li>
   @endif

   <hr>

   {{-- PRODUK --}}
   <li>
    <a ><h3>Kelola Produk</h3></a>
   </li>
   <?php
      $index_permission_active = $role_has_permissions_list->where('name', 'products-index')->first();

      $category_permission_active = $role_has_permissions_list->where('name', 'category')->first();

      $print_barcode_active = $role_has_permissions_list->where('name', 'print_barcode')->first();

      $coupon_permission_active = $role_has_permissions_list->where('name', 'coupon')->first();

      $unit_permission_active = $role_has_permissions_list->where('name', 'unit')->first();

      $stock_count_active = $role_has_permissions_list->where('name', 'stock_count')->first();

      $adjustment_active = $role_has_permissions_list->where('name', 'adjustment')->first();
      ?>
   @if($index_permission_active || $category_permission_active || $print_barcode_active)
   <li>
      <a href="#product" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-list"></i><span>Produk</span><span></a>
      <ul id="product" class="collapse list-unstyled " style="border-radius: 10px;">
         @if($category_permission_active)
         <li id="category-menu"><a href="{{route('category.index')}}">Kategori</a></li>
         @endif
         @if($index_permission_active)
         <li id="product-list-menu"><a href="{{route('products.index')}}">Daftar Produk</a>
         </li>
         @endif
         {{-- @if($print_barcode_active)
         <li id="printBarcode-menu"><a href="{{route('product.printBarcode')}}">{{__('file.print_barcode')}}</a></li>
         @endif --}}
         @if($unit_permission_active)
         <li id="unit-menu"><a href="{{route('unit.index')}}">{{trans('file.Unit')}}</a></li>
         @endif
      </ul>
   </li>
   @endif
   {{-- @if($stock_count_active || $adjustment_active || $coupon_permission_active)
   <li>
      <a href="#stok" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-folder-open"></i><span>Stok</span><span></a>
      <ul id="stok" class="collapse list-unstyled " style="border-radius: 10px;">
         @if($adjustment_active)
         <li id="adjustment-list-menu"><a
            href="{{route('qty_adjustment.index')}}">{{trans('file.Adjustment List')}}</a></li>
         @endif
         @if($stock_count_active)
         <li id="stock-count-menu"><a href="{{route('stock-count.index')}}">{{trans('file.Stock Count')}}</a></li>
         @endif
      </ul>
   </li>
   @endif --}}
    <li id="order-type"><a href="{{route('order_type.index')}}"> <i class="dripicons-view-thumb"></i><span>Tipe Pesanan</span></a></li>
    <li id="ingredient"><a  href="{{route('ingredient.index')}}"> <i class="dripicons-view-thumb"></i><span>Bahan Baku</span></a></li>
    <li id="stock-opname"><a  href="{{route('stock-opname.index')}}"> <i class="dripicons-view-thumb"></i><span>Stok Opname</span></a></li>
    <li id="stock-opname"><a  href="{{route('stock-purchase.index')}}"> <i class="dripicons-view-thumb"></i><span>Pembelian Stok</span></a></li>
    {{-- <li>
        <a href="#bahan-baku" aria-expanded="false" data-toggle="collapse"> <i
                class="dripicons-list"></i><span>Bahan Baku</span><span></a>
        <ul id="bahan-baku" class="collapse list-unstyled " style="border-radius: 10px;">
            <li id="bahan-baku"><a href="{{route('bahan_baku.index')}}">Bahan Baku</a></li>
            <li id="kategori-bahan-baku"><a href="{{route('category.index')}}">Kategori Bahan Baku</a></li>
        </ul>
    </li> --}}

   <hr>

   {{-- KELOLA TOKO --}}
    <li>
        <a>
            <h3>Kelola Toko</h3>
        </a>
    </li>


   <?php
      $role_index_permission_active = $role_has_permissions_list->where('name', 'roles-index')->first();

      $user_index_permission_active = $role_has_permissions_list->where('name', 'users-index')->first();

      $customer_index_permission_active = $role_has_permissions_list->where('name', 'customers-index')->first();

      $warehouse_permission_active = $role_has_permissions_list->where('name', 'warehouse')->first();

      $customer_group_permission_active = $role_has_permissions_list->where('name', 'customer_group')->first();

      $biller_index_permission_active = $role_has_permissions_list->where('name', 'billers-index')->first();

      $supplier_index_permission_active = $role_has_permissions_list->where('name', 'suppliers-index')->first();

      $tax_permission_active = $role_has_permissions_list->where('name', 'tax')->first();

      ?>
   @if($tax_permission_active)
   {{-- <li>
      <a href="#pembayaran" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-wallet"></i><span>Pembayaran</span></a>
      <ul id="pembayaran" class="collapse list-unstyled " style="border-radius: 10px;">
         @if($tax_permission_active)
        <li id="tax-menu"><a href="{{route('tax.index')}}">{{trans('file.Tax')}}</a></li>
        @endif
      </ul>
   </li> --}}

   @endif
   @if($role_index_permission_active || $user_index_permission_active || $customer_index_permission_active || $biller_index_permission_active ||
   $supplier_index_permission_active || $warehouse_permission_active || $customer_group_permission_active)
   <li>
      <a href="#outlet" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-home"></i><span>Outlet</span></a>
      <ul id="outlet" class="collapse list-unstyled " style="border-radius: 10px;">
         {{-- @if($role_index_permission_active) --}}
         <li id="role-menu"><a href="{{route('role.index')}}">Role</a></li>
         {{-- @endif --}}
         @if($user_index_permission_active)
         <li id="user-list-menu"><a href="{{route('user.index')}}">Karyawan</a></li>
         @endif
         @if($warehouse_permission_active)
        <li id="warehouse-menu"><a href="{{route('warehouse.index')}}">Cabang</a></li>
        @endif
        <li id="shift-menu"><a href="{{route('shift.index')}}">Shift</a></li>
        {{-- <li id="table-menu"><a href="{{route('tables.index')}}">Meja</a></li>
         @if($customer_index_permission_active)
         <li id="customer-list-menu"><a href="{{route('customer.index')}}">Customer</a></li>
         @endif
        @if($customer_group_permission_active)
        <li id="customer-group-menu"><a href="{{route('customer_group.index')}}">Group Customer</a></li>
        @endif --}}
         {{-- @if($supplier_index_permission_active)
         <li id="supplier-list-menu"><a href="{{route('supplier.index')}}">Supplier</a></li>
         @endif --}}
      </ul>
   </li>

   @endif


   {{-- <?php
      $index_permission_active = $role_has_permissions_list->where('name', 'purchases-index')->first();
      ?>
   @if($index_permission_active)
   <li>
      <a href="#purchase" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-card"></i><span>{{trans('file.Purchase')}}</span></a>
      <ul id="purchase" class="collapse list-unstyled " style="border-radius: 10px;">
         <li id="purchase-list-menu"><a href="{{route('purchases.index')}}">{{trans('file.Purchase List')}}</a>
         </li>
         <?php
            $add_permission_active = $role_has_permissions_list->where('name', 'purchases-add')->first();
            ?>
         @if($add_permission_active)
         <li id="purchase-create-menu"><a href="{{route('purchases.create')}}">{{trans('file.Add Purchase')}}</a>
         </li>
         <li id="purchase-import-menu"><a
            href="{{url('purchases/purchase_by_csv')}}">{{trans('file.Import Purchase By CSV')}}</a></li>
         @endif
      </ul>
   </li>
   @endif --}}
   <?php
      $sale_index_permission_active = $role_has_permissions_list->where('name', 'sales-index')->first();

      $gift_card_permission_active = $role_has_permissions_list->where('name', 'gift_card')->first();

      $coupon_permission_active = $role_has_permissions_list->where('name', 'coupon')->first();

      $delivery_permission_active = $role_has_permissions_list->where('name', 'delivery')->first();

      $sale_add_permission_active = $role_has_permissions_list->where('name', 'sales-add')->first();
      ?>
   @if($sale_index_permission_active || $gift_card_permission_active || $coupon_permission_active ||
   $delivery_permission_active)
   {{-- <li>
      <a href="#sale" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-cart"></i><span>{{trans('file.Sale')}}</span></a>
      <ul id="sale" class="collapse list-unstyled " style="border-radius: 10px;">
         @if($sale_add_permission_active)
         <li id="sale-list-menu"><a href="{{route('sales.index')}}">{{trans('file.Sale List')}}</a></li>
         <li><a href="{{route('sale.pos')}}">POS</a></li>
         <li id="sale-create-menu"><a href="{{route('sales.create')}}">{{trans('file.Add Sale')}}</a></li>
         <li id="sale-import-menu"><a
            href="{{url('sales/sale_by_csv')}}">{{trans('file.Import Sale By CSV')}}</a></li>
         @endif --}}
         {{-- @if($gift_card_permission_active)
         <li id="gift-card-menu"><a href="{{route('gift_cards.index')}}">{{trans('file.Gift Card List')}}</a>
         </li>
         @endif
         @if($coupon_permission_active)
         <li id="coupon-menu"><a href="{{route('coupons.index')}}">{{trans('file.Coupon List')}}</a> </li>
         @endif
         <li id="courier-menu"><a href="{{route('couriers.index')}}">{{trans('file.Courier List')}}</a> </li>
         @if($delivery_permission_active)
         <li id="delivery-menu"><a href="{{route('delivery.index')}}">{{trans('file.Delivery List')}}</a></li>
         @endif --}}
      {{-- </ul>
   </li> --}}
   @endif

   <li>
      <a href="#setting" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-gear"></i><span>{{trans('file.settings')}}</span></a>
      <ul id="setting" class="collapse list-unstyled " style="border-radius: 10px;">
         <?php
            $all_notification_permission_active = $role_has_permissions_list->where('name', 'all_notification')->first();

            $send_notification_permission_active = $role_has_permissions_list->where('name', 'send_notification')->first();

            $warehouse_permission_active = $role_has_permissions_list->where('name', 'warehouse')->first();

            $customer_group_permission_active = $role_has_permissions_list->where('name', 'customer_group')->first();

            $brand_permission_active = $role_has_permissions_list->where('name', 'brand')->first();

            $unit_permission_active = $role_has_permissions_list->where('name', 'unit')->first();

            $currency_permission_active = $role_has_permissions_list->where('name', 'currency')->first();

            $tax_permission_active = $role_has_permissions_list->where('name', 'tax')->first();

            $general_setting_permission_active = $role_has_permissions_list->where('name', 'general_setting')->first();

            $backup_database_permission_active = $role_has_permissions_list->where('name', 'backup_database')->first();

            $mail_setting_permission_active = $role_has_permissions_list->where('name', 'mail_setting')->first();

            $sms_setting_permission_active = $role_has_permissions_list->where('name', 'sms_setting')->first();

            $create_sms_permission_active = $role_has_permissions_list->where('name', 'create_sms')->first();

            $pos_setting_permission_active = $role_has_permissions_list->where('name', 'pos_setting')->first();

            $hrm_setting_permission_active = $role_has_permissions_list->where('name', 'hrm_setting')->first();

            $reward_point_setting_permission_active = $role_has_permissions_list->where('name', 'reward_point_setting')->first();

            $discount_plan_permission_active = $role_has_permissions_list->where('name', 'discount_plan')->first();

            $discount_permission_active = $role_has_permissions_list->where('name', 'discount')->first();

            $custom_field_permission_active = $role_has_permissions_list->where('name', 'custom_field')->first();
            ?>
         {{-- @if($role->id <= 2)
         <li id="role-menu"><a
            href="{{route('role.index')}}">{{trans('file.Role Permission')}}</a></li>
         @endif --}}
         {{-- @if($discount_plan_permission_active)
         <li id="discount-plan-list-menu"><a href="{{route('discount-plans.index')}}">{{trans('file.Discount Plan')}}</a></li>
         @endif
         @if($discount_permission_active)
         <li id="discount-list-menu"><a href="{{route('discounts.index')}}">{{trans('file.Discount')}}</a></li>
         @endif --}}
         {{-- @if($all_notification_permission_active)
         <li id="notification-list-menu">
            <a href="{{route('notifications.index')}}">{{trans('file.All Notification')}}</a>
         </li>
         @endif --}}
         {{-- @if($send_notification_permission_active)
         <li id="notification-menu">
            <a href="" id="send-notification">{{trans('file.Send Notification')}}</a>
         </li>
         @endif --}}
         {{-- @if($customer_group_permission_active)
         <li id="customer-group-menu"><a href="{{route('customer_group.index')}}">{{trans('file.Customer Group')}}</a></li>
         @endif --}}
         {{-- @if($brand_permission_active)
         <li id="brand-menu"><a href="{{route('brand.index')}}">{{trans('file.Brand')}}</a></li>
         @endif --}}
         {{-- @if($currency_permission_active)
         <li id="currency-menu"><a href="{{route('currency.index')}}">{{trans('file.Currency')}}</a></li>
         @endif --}}
         {{-- @if($tax_permission_active)
         <li id="tax-menu"><a href="{{route('tax.index')}}">{{trans('file.Tax')}}</a></li>
         @endif
         <li id="user-menu"><a href="{{route('user.profile', ['id' => Auth::id()])}}">{{trans('file.User Profile')}}</a></li>
         @if($create_sms_permission_active)
         <li id="create-sms-menu"><a href="{{route('setting.createSms')}}">{{trans('file.Create SMS')}}</a></li>
         @endif
         @if($backup_database_permission_active)
         <li><a href="{{route('setting.backup')}}">{{trans('file.Backup Database')}}</a></li>
         @endif --}}
         @if($general_setting_permission_active)
         <li id="general-setting-menu"><a href="{{route('setting.general')}}">{{trans('file.General Setting')}}</a></li>
         @endif
         {{-- @if($mail_setting_permission_active)
         <li id="mail-setting-menu"><a href="{{route('setting.mail')}}">{{trans('file.Mail Setting')}}</a></li>
         @endif
         @if($reward_point_setting_permission_active)
         <li id="reward-point-setting-menu"><a
            href="{{route('setting.rewardPoint')}}">{{trans('file.Reward Point Setting')}}</a></li>
         @endif
         @if($sms_setting_permission_active)
         <li id="sms-setting-menu"><a href="{{route('setting.sms')}}">{{trans('file.SMS Setting')}}</a></li>
         @endif --}}
         @if($pos_setting_permission_active)
         <li id="pos-setting-menu"><a href="{{route('setting.pos')}}">POS {{trans('file.settings')}}</a></li>
         @endif
         {{-- @if($hrm_setting_permission_active)
         <li id="hrm-setting-menu"><a href="{{route('setting.hrm')}}"> {{trans('file.HRM Setting')}}</a></li>
         @endif --}}
      </ul>
   </li>
</ul>
