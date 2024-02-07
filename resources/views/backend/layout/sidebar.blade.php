<ul id="side-main-menu" class="side-menu list-unstyled">
   {{-- DASHBOARD --}}
   <li><a href="{{url('/dashboard')}}"> <i
      class="dripicons-meter"></i><span>Beranda</span></a></li>

   {{-- PENGELUARAN --}}
   <li>
      <a href="#expense" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-wallet"></i><span>Biaya Pengeluaran</span></a>
      <ul id="expense" class="collapse list-unstyled " style="border-radius: 10px;">
        @if(auth()->user()->hasRole('Superadmin'))
         <li id="exp-cat-menu"><a href="{{route('expense_categories.index')}}">Nama Pengeluaran</a>
         </li>
         @endif
         <li id="exp-list-menu"><a href="{{route('expenses.index')}}">Daftar Pengeluaran</a></li>
      </ul>
   </li>


   <li>
      <a href="#report" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-document-remove"></i><span>Laporan</span></a>
      <ul id="report" class="collapse list-unstyled " style="border-radius: 10px;">
        <li id="stock-opname"><a  href="{{route('close-cashier.index')}}">Laporan Tutup Kasir</a></li>

         <li id="product-report-menu">
            {!! Form::open(['route' => 'report.product', 'method' => 'get', 'id' => 'product-report-form']) !!}
            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <input type="hidden" name="warehouse_id" value="0" />
            <a id="report-link" href="">Laporan Transaksi Produk</a>
            {!! Form::close() !!}
         </li>

         <li id="profit-loss-report-menu">
            {!! Form::open(['route' => 'report.profitLoss', 'method' => 'post', 'id' =>
            'profitLoss-report-form']) !!}
            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <a id="profitLoss-link" href="">Ringkasan Laporan</a>
            {!! Form::close() !!}
         </li>

         <li id="best-seller-report-menu">
            <a href="{{url('admin/report/best_seller')}}">{{trans('file.Best Seller')}}</a>
         </li>

         <li id="daily-sale-report-menu">
            <a href="{{url('admin/report/daily_sale/'.date('Y').'/'.date('m'))}}">Transaksi Harian</a>
         </li>

         <li id="monthly-sale-report-menu">
            <a href="{{url('admin/report/monthly_sale/'.date('Y'))}}">Transaksi Bulanan</a>
         </li>

         <li id="daily-purchase-report-menu">
            <a
               href="{{url('admin/report/daily_purchase/'.date('Y').'/'.date('m'))}}">Pembelian Harian</a>
         </li>

         <li id="monthly-purchase-report-menu">
            <a href="{{url('admin/report/monthly_purchase/'.date('Y'))}}">Pembelian Bulanan</a>
         </li>

         <li id="payment-report-menu">
            {!! Form::open(['route' => 'report.paymentByDate', 'method' => 'post', 'id' =>
            'payment-report-form']) !!}
            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <a id="payment-report-link" href="">Laporan Pembayaran</a>
            {!! Form::close() !!}
         </li>

         <li id="warehouse-report-menu">
            <a id="warehouse-report-link" href="">Laporan Cabang</a>
         </li>

      </ul>
    </li>

   <hr>

   {{-- PRODUK --}}
   <li>
    <a ><h3>Kelola Produk</h3></a>
   </li>

   <li>
      <a href="#product" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-list"></i><span>Produk</span><span></a>
      <ul id="product" class="collapse list-unstyled " style="border-radius: 10px;">

         <li id="category-menu"><a href="{{route('category.index')}}">Kategori</a></li>

         <li id="product-list-menu"><a href="{{route('products.index')}}">Daftar Produk</a>
         </li>

         {{-- @if($print_barcode_active)
         <li id="printBarcode-menu"><a href="{{route('product.printBarcode')}}">{{__('file.print_barcode')}}</a></li>
         @endif --}}

         <li id="unit-menu"><a href="{{route('unit.index')}}">{{trans('file.Unit')}}</a></li>

      </ul>
   </li>

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
   <li>
      <a href="#outlet" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-home"></i><span>Outlet</span></a>
      <ul id="outlet" class="collapse list-unstyled " style="border-radius: 10px;">
         <li id="role-menu"><a href="{{route('role.index')}}">Role</a></li>
         <li id="user-list-menu"><a href="{{route('user.index')}}">Karyawan</a></li>
        <li id="warehouse-menu"><a href="{{route('warehouse.index')}}">Cabang</a></li>
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

   <li>
      <a href="#setting" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-gear"></i><span>{{trans('file.settings')}}</span></a>
      <ul id="setting" class="collapse list-unstyled " style="border-radius: 10px;">
         <li id="general-setting-menu"><a href="{{route('setting.general')}}">{{trans('file.General Setting')}}</a></li>
         <li id="pos-setting-menu"><a href="{{route('setting.pos')}}">POS {{trans('file.settings')}}</a></li>
      </ul>
   </li>
</ul>
