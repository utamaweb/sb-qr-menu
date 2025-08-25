<li id="ojol">
    <a href="{{ route('ojol.index') }}">
        <i class="dripicons-location"></i>
        <span>Ojek Online</span>
    </a>
</li>
<li id="kategori">
    <a href="{{ route('kategori.index') }}">
        <i class="dripicons-user-id"></i>
        <span>Kategori</span>
    </a>
</li>
<li id="regional">
    <a href="{{ route('regional.index') }}">
        <i class="dripicons-location"></i>
        <span>Regional</span>
    </a>
</li>
<li id="produk">
    <a href="{{ route('produk.index') }}">
        <i class="dripicons-user-id"></i>
        <span>Produk</span>
    </a>
</li>
<li id="bahan-baku">
    <a href="{{ route('bahan-baku.index') }}">
        <i class="dripicons-user-id"></i>
        <span>Bahan Baku</span>
    </a>
</li>
<li id="business-stock">
    <a href="{{ route('business-stock.index') }}">
        <i class="dripicons-user-id"></i>
        <span>Daftar Stok</span>
    </a>
</li>
<li id="user">
    <a href="{{ route('user.index') }}">
        <i class="dripicons-user"></i>
        <span>User</span>
    </a>
</li>
<li id="outlet">
    <a href="{{ route('outlet.index') }}">
        <i class="dripicons-store"></i>
        <span>Outlet</span>
    </a>
</li>
<li>
    <a href="#expense" aria-expanded="false" data-toggle="collapse">
        <i class="dripicons-wallet"></i>
        <span>Biaya Pengeluaran</span>
    </a>
    <ul id="expense" class="collapse list-unstyled" style="border-radius: 10px;">
        <li id="exp-cat-menu">
            <a href="{{ route('nama-pengeluaran.index') }}">Nama Pengeluaran</a>
        </li>
        <li id="exp-list-menu">
            <a href="{{ route('pengeluaran.index') }}">Daftar Pengeluaran</a>
        </li>
    </ul>
</li>
<li>
    <a href="#report" aria-expanded="false" data-toggle="collapse">
        <i class="dripicons-document-remove"></i>
        <span>Laporan</span>
    </a>
    <ul id="report" class="collapse list-unstyled" style="border-radius: 10px;">
        <li id="laporan-selisih">
            <a href="{{ route('report.differenceStockReport') }}?warehouse_id=all">Laporan Selisih Stok</a>
        </li>
        <li id="laporan-sisa">
            <a href="{{ route('report.remainingStockReport') }}">Laporan Sisa Stok</a>
        </li>
        <li id="product-omzet-by-month">
            <a href="{{ route('report.productsOmzetByMonth') }}">Laporan Omset Produk</a>
        </li>
        <li id="daily-sale-outlet">
            <a href="{{ route('admin.report.daily_sale_outlet', ['year' => date('Y'), 'month' => date('m')]) }}">Laporan Sales</a>
        </li>
        <li id="finance-report">
            <a href="{{ route('financeReport') }}?warehouse_id=all&regional_id=all">Laporan Finance</a>
        </li>
    </ul>
</li>
