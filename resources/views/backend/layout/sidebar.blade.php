<ul id="side-main-menu" class="side-menu list-unstyled">

    <li id="dashboard"><a href="{{route('admin.dashboard')}}"> <i class="dripicons-meter"></i><span>Beranda</span></a></li>
{{-- menu --}}
    {{-- Menu Superadmin --}}
    @if(auth()->user()->hasRole('Superadmin'))
    <li id="role"><a href="{{route('role.index')}}"> <i class="dripicons-user-group"></i><span>Role</span></a></li>
    <li id="user"><a href="{{route('user.index')}}"> <i class="dripicons-user"></i><span>User</span></a></li>
    <li id="bisnis"><a href="{{route('business.index')}}"> <i class="dripicons-user-id"></i><span>Bisnis</span></a></li>
    <li id="outlet"><a href="{{route('outlet.index')}}"> <i class="dripicons-store"></i><span>Outlet</span></a></li>
    <li id="unit"><a href="{{route('unit.index')}}"> <i class="dripicons-user-id"></i><span>Unit</span></a></li>
    <li id="tipe-pesanan"><a href="{{route('tipe-pesanan.index')}}"> <i class="dripicons-user-id"></i><span>Tipe Pesanan</span></a></li>
    <li id="outlet"><a href="#"> <i class="dripicons-tags"></i><span>Harga(comingsoon)</span></a></li>
    <li id="outlet"><a href="#"> <i class="dripicons-shopping-bag"></i><span>Transaksi(comingsoon)</span></a></li>
    <li id="setting"><a href="{{route('setting.general')}}"> <i class="dripicons-gear"></i><span>Pengaturan</span></a></li>

    {{-- Menu Admin Bisnis --}}
    @elseif(auth()->user()->hasRole('Admin Bisnis'))
    <li id="ojol"><a href="{{route('ojol.index')}}"> <i class="dripicons-location"></i><span>Ojek Online</span></a></li>
    <li id="kategori"><a href="{{route('kategori.index')}}"> <i class="dripicons-user-id"></i><span>Kategori</span></a></li>
    <li id="produk"><a href="{{route('produk.index')}}"> <i class="dripicons-user-id"></i><span>Produk</span></a></li>
    <li id="bahan-baku"><a href="{{route('bahan-baku.index')}}"> <i class="dripicons-user-id"></i><span>Bahan Baku</span></a></li>
    <li id="user"><a href="{{route('user.index')}}"> <i class="dripicons-user"></i><span>User</span></a></li>
    <li id="outlet"><a href="{{route('outlet.index')}}"> <i class="dripicons-store"></i><span>Outlet</span></a></li>
    <li>
        <a href="#expense" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-wallet"></i><span>Biaya Pengeluaran</span></a>
        <ul id="expense" class="collapse list-unstyled " style="border-radius: 10px;">
           <li id="exp-cat-menu"><a href="{{route('nama-pengeluaran.index')}}">Nama Pengeluaran</a>
           </li>
           <li id="exp-list-menu"><a href="{{route('pengeluaran.index')}}">Daftar Pengeluaran</a></li>
        </ul>
     </li>

    {{-- Menu Admin Outlet --}}
    @elseif(auth()->user()->hasRole('Admin Outlet'))
    <li id="produk-outlet"><a href="{{route('produk-outlet.index')}}"> <i class="dripicons-user-id"></i><span>Produk Outlet</span></a></li>
    <li id="daftar-stok"><a href="{{route('stok.index')}}"> <i class="dripicons-user-id"></i><span>Daftar Stok</span></a></li>
    <li id="tambah-stok"><a href="{{route('pembelian-stok.index')}}"> <i class="dripicons-user-id"></i><span>Tambah Stok</span></a></li>
    <li id="stock-opname"><a  href="{{route('stock-opname.index')}}"> <i class="dripicons-view-thumb"></i><span>Stok Opname</span></a></li>
    <li id="list-transaction"><a href="{{route('report.listTransaction')}}"> <i class="dripicons-document-remove"></i><span>List Transaksi</span></a></li>
   <li>
      <a href="#report" aria-expanded="false" data-toggle="collapse"> <i
         class="dripicons-document-remove"></i><span>Laporan</span></a>
      <ul id="report" class="collapse list-unstyled " style="border-radius: 10px;">
        <li id="laporan-tutup-kasir"><a  href="{{route('close-cashier.index')}}">Laporan Tutup Kasir</a></li>
        <li id="laporan-transaksi-produk"><a  href="{{route('report.product')}}">Laporan Transaksi Produk</a></li>

         {{-- <li id="product-report-menu"> --}}
             {{-- <a id="report-link" href="{{route('report.product')}}">Laporan Transaksi Produk</a> --}}
            {{-- {!! Form::open(['route' => 'report.product', 'method' => 'get', 'id' => 'product-report-form']) !!}
            <input type="hidden" name="start_date" value="{{date('Y-m').'-'.'01'}}" />
            <input type="hidden" name="end_date" value="{{date('Y-m-d')}}" />
            <input type="hidden" name="warehouse_id" value="0" /> --}}
            {{-- {!! Form::close() !!} --}}
         {{-- </li> --}}

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

         {{-- <li id="warehouse-report-menu">
            <a id="warehouse-report-link" href="{{route('report.warehouse')}}">Laporan Cabang</a>
         </li> --}}

      </ul>
    </li>
    <li>
        <a href="#expense" aria-expanded="false" data-toggle="collapse"> <i class="dripicons-wallet"></i><span>Biaya Pengeluaran</span></a>
        <ul id="expense" class="collapse list-unstyled " style="border-radius: 10px;">
           <li id="exp-list-menu"><a href="{{route('pengeluaran.index')}}">Daftar Pengeluaran</a></li>
        </ul>
     </li>
     <li id="user"><a href="{{route('user.index')}}"> <i class="dripicons-user"></i><span>User</span></a></li>

    {{-- Menu Kasir --}}
    @else


    @endif

</ul>
