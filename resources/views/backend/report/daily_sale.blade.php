@extends('backend.layout.main')
@section('content')
<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <h4 class="text-center mb-4">Laporan Penjualan Harian</h4>

                <!-- Add warehouse filter only for Admin Bisnis or Report roles -->
                @if(auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                <div class="row mb-4">
                    <div class="col-md-6 offset-md-3 text-center">
                        <form action="{{ url("admin/report/daily_sale/$year/$month") }}" method="GET">
                            <div class="form-group mb-3">
                                <select name="warehouse_id" id="warehouse_id" class="form-control selectpicker" data-live-search="true">
                                    <option value="">Pilih Outlet</option>
                                    @foreach($lims_warehouse_list as $warehouse_item)
                                        <option value="{{ $warehouse_item->id }}" {{ ($warehouse_id == $warehouse_item->id) ? 'selected' : '' }}>
                                            {{ $warehouse_item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary px-4">Filter</button>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Show current warehouse information -->
                @if(isset($warehouse) && $warehouse)
                    <h5 class="text-center mb-3">Outlet: {{ $warehouse->name }}</h5>
                @elseif($warehouse_id == '' && auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                    <h5 class="text-center mb-3">Outlet Belum Dipilih</h5>
                @elseif(!auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                    <h5 class="text-center mb-3">Outlet: {{ auth()->user()->warehouse->name ?? 'Tidak Ditemukan' }}</h5>
                @endif

                <div class="table-responsive mt-4">
                    <table class="table table-bordered text-center" style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                        <thead>
                            <tr>
                                <th><a href="{{ url("admin/report/daily_sale/$prev_year/$prev_month") }}{{ $warehouse_id ? '?warehouse_id='.$warehouse_id : '' }}"><i class="fa fa-arrow-left"></i> Previous</a></th>
                                <th colspan="5" class="text-center">{{ date("F Y", strtotime("$year-$month-01")) }}</th>
                                <th><a href="{{ url("admin/report/daily_sale/$next_year/$next_month") }}{{ $warehouse_id ? '?warehouse_id='.$warehouse_id : '' }}">Next <i class="fa fa-arrow-right"></i></a></th>
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

    // Initialize selectpicker if you're using Bootstrap Select
    $('.selectpicker').selectpicker({
        style: 'btn-link',
    });
</script>
@endpush
