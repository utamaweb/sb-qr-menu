@extends('backend.layout.main')
@section('content')
<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <h4 class="text-center mb-4">{{ trans('file.Monthly Sale Report') }}</h4>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered" style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                        <thead>
                            <tr>
                                <th><a href="{{ url('admin/report/monthly_sale/'.($year-1)) }}"><i class="fa fa-arrow-left"></i> {{ trans('file.Previous') }}</a></th>
                                <th colspan="10" class="text-center">{{ $year }}</th>
                                <th><a href="{{ url('admin/report/monthly_sale/'.($year+1)) }}">{{ trans('file.Next') }} <i class="fa fa-arrow-right"></i></a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $month)
                                    <td><strong>{{ $month }}</strong></td>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($total_qty as $key => $qty)
                                    <td>
                                        @if($qty > 0)
                                            <strong>Total Produk Sold (Qty)</strong><br>
                                            <span>{{ number_format($qty, 0, '', '.') }}</span><br><br>
                                        @endif

                                        @if($total_transaction[$key] > 0)
                                            <strong>Jumlah Transaksi Lunas</strong><br>
                                            <span>{{ number_format($total_transaction[$key], 0, '', '.') }}</span><br><br>
                                        @endif

                                        @if($total_paid[$key] > 0)
                                            <strong>Total Penerimaan Uang</strong><br>
                                            <span>Rp. {{ number_format($total_paid[$key], 0, '', '.') }}</span><br><br>
                                        @endif

                                        @if($total_amount[$key] > 0)
                                            <strong>Total Pembayaran</strong><br>
                                            <span>Rp. {{ number_format($total_amount[$key], 0, '', '.') }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #monthly-sale-report-menu").addClass("active");
</script>
@endpush
