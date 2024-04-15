@extends('backend.layout.main')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <a href="{{route('close-cashier.index')}}" class="btn btn-info"><i class="dripicons-arrow-thin-left"></i> Kembali </a>
                    </div>
                    <div class="card-body">
                        <h4>Detail Tutup Kasir - {{$closeCashier->shift->user->name}}</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                Waktu Buka Kasir <br>
                                {{$closeCashier->open_time}}
                            </div>
                            <div class="col-md-6 text-right">
                                Waktu Tutup Kasir <br>
                                {{$closeCashier->close_time}}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h2>@currency($closeCashier->total_income)</h2>
                                <p>Total Penerimaan</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4>Detail Form Tutup Kasir</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        Nama <br><br>
                                        Shift <br><br>
                                        Hari / Tanggal <br><br>
                                        Modal <br><br>
                                        Pembayaran Tunai <br>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        {{$closeCashier->shift->user->name}} <br><br>
                                        {{$closeCashier->shift->shift_number}} <br><br>
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $closeCashier->date)->isoFormat('DD MMMM YYYY') }} <br><br>
                                        @currency($closeCashier->initial_balance) <br><br>
                                        @currency($closeCashier->total_cash) <br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4>Non Tunai</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        Omset GOFOOD <br><br>
                                        Omset GRABFOOD <br><br>
                                        Omset SHOPEEFOOD <br><br>
                                        Omset QRIS <br><br>
                                        Omset TRANSFER <br><br>
                                        Total Non Tunai <br>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        @currency($closeCashier->gofood_omzet) <br><br>
                                        @currency($closeCashier->grabfood_omzet) <br><br>
                                        @currency($closeCashier->shopeefood_omzet) <br><br>
                                        @currency($closeCashier->qris_omzet) <br><br>
                                        @currency($closeCashier->transfer_omzet) <br><br>
                                        @currency($closeCashier->total_non_cash) <br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4>Cash Omset</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        Pembayaran Tunai (Omset Aplikasi) - Total Non Tunai <br>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        @currency($closeCashier->total_cash - $closeCashier->total_non_cash) <br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4>Pengeluaran</h4>
                                <hr>
                                <div class="row">
                                    @foreach($expenses as $expense)
                                    <div class="col-md-6">
                                        {{$expense->expenseCategory->name}} <br>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        @currency($expense->amount) <br>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        ------------------------------------------------------------------------------
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        Total Pengeluaran <br><br>
                                        Total Pembelian Stok <br><br>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        @currency($sumExpense)<br><br>
                                        @currency($sumStockPurchase)<br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4>Hasil Akhir</h4>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        Cash Omset - Total Pengeluaran <br><br>
                                        Uang Tunai Di Laci <br><br>
                                        Selisih <br>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        @currency($closeCashier->total_cash - $sumExpense - $sumStockPurchase) <br><br>
                                        @currency($closeCashier->cash_in_drawer) <br><br>
                                        @currency($closeCashier->different) <br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4>Produk Terjual</h4>
                                <hr>
                                <div class="row">
                                    @foreach($closeCashierProductSolds as $product)
                                    <div class="col-md-6">
                                        {{$product->product_name}} <br><br>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        {{$product->qty}} <br><br>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #laporan-tutup-kasir").addClass("active");
</script>
@endpush
