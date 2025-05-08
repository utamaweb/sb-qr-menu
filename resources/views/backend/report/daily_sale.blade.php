@extends('backend.layout.main')
@section('content')
<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <h4 class="text-center mb-4">Laporan Penjualan Harian</h4>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered text-center" style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                        <thead>
                            <tr>
                                <th><a href="{{ url("admin/report/daily_sale/$prev_year/$prev_month") }}"><i class="fa fa-arrow-left"></i> Previous</a></th>
                                <th colspan="5" class="text-center">{{ date("F Y", strtotime("$year-$month-01")) }}</th>
                                <th><a href="{{ url("admin/report/daily_sale/$next_year/$next_month") }}">Next <i class="fa fa-arrow-right"></i></a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Minggu</strong></td>
                                <td><strong>Senin</strong></td>
                                <td><strong>Selasa</strong></td>
                                <td><strong>Rabu</strong></td>
                                <td><strong>Kamis</strong></td>
                                <td><strong>Jum'at</strong></td>
                                <td><strong>Sabtu</strong></td>
                            </tr>

                            @php
                                $currentDay = 1;
                                $flag = false;
                            @endphp

                            @while ($currentDay <= $number_of_day)
                                <tr>
                                    @for ($weekDay = 1; $weekDay <= 7; $weekDay++)
                                        @if ($currentDay > $number_of_day)
                                            <td></td>
                                            @continue
                                        @endif

                                        @if (!$flag && $weekDay != $start_day)
                                            <td></td>
                                            @continue
                                        @else
                                            @php $flag = true; @endphp
                                        @endif

                                        <td class="{{ ($year.'-'.$month.'-'.$currentDay == date('Y-m-d')) ? 'bg-success text-white' : '' }}">
                                            <p><strong>{{ $currentDay }}</strong></p>

                                            @if ($dailyData[$currentDay]['qty'] > 0)
                                                <strong>Total Produk Terjual</strong><br>
                                                <span>{{ number_format($dailyData[$currentDay]['qty'], 0, '', '.') }}</span><br><br>
                                            @endif

                                            @if ($dailyData[$currentDay]['transaction'] > 0)
                                                <strong>Jumlah Transaksi Lunas</strong><br>
                                                <span>{{ number_format($dailyData[$currentDay]['transaction'], 0, '', '.') }}</span><br><br>
                                            @endif

                                            @if ($dailyData[$currentDay]['amount'] > 0)
                                                <strong>Total Pendapatan</strong><br>
                                                <span>Rp. {{ number_format($dailyData[$currentDay]['amount'], 0, '', '.') }}</span><br><br>
                                            @endif
                                        </td>
                                        @php $currentDay++; @endphp
                                    @endfor
                                </tr>
                            @endwhile
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
    $("ul#report #daily-sale-report-menu").addClass("active");
</script>
@endpush
