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

   @can('lihat-laporan')
   <li>
      <a href="#report" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-document-remove"></i><span>Laporan</span></a>
      <ul id="report" class="collapse list-unstyled " style="border-radius: 10px;">
        <li id="laporan-tutup-kasir"><a  href="{{route('close-cashier.index')}}">Laporan Tutup Kasir</a></li>

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
            {{-- <a id="profitLoss-link" href="">Ringkasan Laporan</a> --}}
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
    @endcan

   <hr>

   {{-- PRODUK --}}
   <li>
    <a ><h3>Kelola Produk</h3></a>
   </li>

   @canany(['lihat-kategori', 'tambah-kategori', 'ubah-kategori', 'hapus-kategori', 'lihat-produk', 'tambah-produk', 'ubah-produk', 'hapus-produk'])
   <li>
      <a href="#product" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-list"></i><span>Produk</span><span></a>
      <ul id="product" class="collapse list-unstyled " style="border-radius: 10px;">
        @canany(['lihat-kategori', 'tambah-kategori', 'ubah-kategori', 'hapus-kategori'])
         <li id="category-menu"><a href="{{route('kategori.index')}}">Kategori</a></li>
        @endcanany
        @canany(['lihat-produk', 'tambah-produk', 'ubah-produk', 'hapus-produk'])
         <li id="product-list-menu"><a href="{{route('produk.index')}}">Daftar Produk</a>
         </li>
        @endcanany
         @canany(['lihat-unit', 'tambah-unit', 'ubah-unit', 'hapus-unit'])
         <li id="unit-menu"><a href="{{route('unit.index')}}">Unit</a></li>
        @endcanany
      </ul>
   </li>
   @endcanany

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
   @canany(['lihat-tipepesanan', 'tambah-tipepesanan', 'ubah-tipepesanan', 'hapus-tipepesanan'])
    <li id="order-type"><a href="{{route('tipe-pesanan.index')}}"> <i class="dripicons-view-thumb"></i><span>Tipe Pesanan</span></a></li>
    @endcanany
    @canany(['lihat-bahanbaku', 'tambah-bahanbaku', 'ubah-bahanbaku', 'hapus-bahanbaku'])
    <li id="ingredient"><a  href="{{route('bahan-baku.index')}}"> <i class="dripicons-view-thumb"></i><span>Bahan Baku</span></a></li>
    @endcanany
    @canany(['lihat-stokopname', 'tambah-stokopname', 'ubah-stokopname', 'hapus-stokopname'])
    <li id="stock-opname"><a  href="{{route('stock-opname.index')}}"> <i class="dripicons-view-thumb"></i><span>Stok Opname</span></a></li>
    @endcanany
    @canany(['lihat-pembelianstok', 'tambah-pembelianstok', 'ubah-pembelianstok', 'hapus-pembelianstok'])
    <li id="stock-purchase"><a  href="{{route('pembelian-stok.index')}}"> <i class="dripicons-view-thumb"></i><span>Pembelian Stok</span></a></li>
    @endcanany
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
    @canany(['lihat-role', 'tambah-role', 'ubah-role', 'hapus-role','lihat-user', 'tambah-user', 'ubah-user', 'hapus-user','lihat-warehouse', 'tambah-warehouse', 'ubah-warehouse', 'hapus-warehouse'])
   <li>
      <a href="#outlet" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-home"></i><span>Outlet</span></a>
      <ul id="outlet" class="collapse list-unstyled " style="border-radius: 10px;">
        @canany(['lihat-role', 'tambah-role', 'ubah-role', 'hapus-role'])
         <li id="role-menu"><a href="{{route('role.index')}}">Role</a></li>
         @endcanany
         @canany(['lihat-user', 'tambah-user', 'ubah-user', 'hapus-user'])
         <li id="user-list-menu"><a href="{{route('user.index')}}">User</a></li>
         @endcanany
         @canany(['lihat-warehouse', 'tambah-warehouse', 'ubah-warehouse', 'hapus-warehouse'])
        <li id="warehouse-menu"><a href="{{route('outlet.index')}}">Outlet</a></li>
        @endcanany
        {{-- <li id="shift-menu"><a href="{{route('shift.index')}}">Shift</a></li> --}}
        {{-- <li id="table-menu"><a href="{{route('tables.index')}}">Meja</a></li>
         @if($customer_index_permission_active)
         <li id="customer-list-menu"><a href="{{route('customer.index')}}">Customer</a></li>
         @endif
        @if($customer_group_permission_active)
        <li id="customer-group-menu"><a href="{{route('customer_group.index')}}">Group Customer</a></li>
        @endif --}}
      </ul>
   </li>
   @endcanany

   @canany(['lihat-setting', 'tambah-setting', 'ubah-setting', 'hapus-setting'])
   {{-- <li>
      <a href="#setting" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-gear"></i><span>{{trans('file.settings')}}</span></a>
      <ul id="setting" class="collapse list-unstyled " style="border-radius: 10px;">
         <li id="general-setting-menu"><a href="{{route('setting.general')}}">{{trans('file.General Setting')}}</a></li>
         <li id="pos-setting-menu"><a href="{{route('setting.pos')}}">POS {{trans('file.settings')}}</a></li>
      </ul>
   </li> --}}
   <li id="general-setting-menu"><a  href="{{route('setting.general')}}"> <i class="dripicons-gear"></i><span>Pengaturan</span></a></li>
   @endcanany
</ul>
