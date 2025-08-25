<li id="list-transaction">
    <a href="{{ route('report.listTransaction') }}">
        <i class="dripicons-document-remove"></i>
        <span>List Transaksi</span>
    </a>
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

        <li id="finance-report">
            <a href="{{ route('financeReport') }}?warehouse_id=all&regional_id=all">Laporan Finance</a>
        </li>
        <li id="laporan-tutup-kasir">
            <a href="{{ route('close-cashier.index') }}">Laporan Tutup Kasir</a>
        </li>
        <li id="laporan-transaksi-produk">
            <a href="{{ route('report.product') }}">Laporan Transaksi Produk</a>
        </li>
        <li id="product-omzet-by-month">
            <a href="{{ route('report.productsOmzetByMonth') }}">Laporan Omset Produk</a>
        </li>
        <li id="profit-loss-report-menu">
            {!! Form::open(['route' => 'report.profitLoss', 'method' => 'post', 'id' => 'profitLoss-report-form']) !!}
            <input type="hidden" name="start_date" value="{{ date('Y-m') . '-' . '01' }}" />
            <input type="hidden" name="end_date" value="{{ date('Y-m-d') }}" />
            {!! Form::close() !!}
        </li>
        {{-- <li id="best-seller-report-menu">
            <a href="{{ url('admin/report/best_seller') }}">{{ trans('file.Best Seller') }}</a>
        </li> --}}
        <li id="daily-sale-outlet">
            <a href="{{ route('admin.report.daily_sale_outlet', ['year' => date('Y'), 'month' => date('m')]) }}">Laporan Sales</a>
        </li>
        <li id="daily-sale-report-menu">
            <a href="{{ url('admin/report/daily_sale/' . date('Y') . '/' . date('m')) }}">Transaksi Harian</a>
        </li>
        <li id="monthly-sale-report-menu">
            <a href="{{ url('admin/report/monthly_sale/' . date('Y')) }}">Transaksi Bulanan</a>
        </li>
        <li id="daily-purchase-report-menu">
            <a href="{{ url('admin/report/daily_purchase/' . date('Y') . '/' . date('m')) }}">Pembelian Harian</a>
        </li>
        <li id="monthly-purchase-report-menu">
            <a href="{{ url('admin/report/monthly_purchase/' . date('Y')) }}">Pembelian Bulanan</a>
        </li>
        <li id="payment-report-menu">
            {!! Form::open(['route' => 'report.paymentByDate', 'method' => 'post', 'id' => 'payment-report-form']) !!}
            <input type="hidden" name="start_date" value="{{ date('Y-m') . '-' . '01' }}" />
            <input type="hidden" name="end_date" value="{{ date('Y-m-d') }}" />
            <a id="payment-report-link" href="">Laporan Pembayaran</a>
            {!! Form::close() !!}
        </li>
    </ul>
</li>
<li>
    <a href="#expense" aria-expanded="false" data-toggle="collapse">
        <i class="dripicons-wallet"></i>
        <span>Biaya Pengeluaran</span>
    </a>
    <ul id="expense" class="collapse list-unstyled" style="border-radius: 10px;">
        <li id="exp-list-menu">
            <a href="{{ route('pengeluaran.index') }}">Daftar Pengeluaran</a>
        </li>
    </ul>
</li>
