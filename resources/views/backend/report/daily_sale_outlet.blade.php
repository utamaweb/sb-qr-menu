@extends('backend.layout.main')
@section('content')
<section>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <h4 class="text-center mb-4">Laporan Sales</h4>

                <!-- Date and Warehouse filters - Only for Admin Bisnis and Report roles -->
                @if(auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                <div class="row mb-4">
                    <div class="col-md-6 offset-md-3 text-center">
                        <form action="{{ route('admin.report.daily_sale_outlet') }}" method="GET">
                            <!-- Month and Year filters -->
                            <div class="form-row mb-3">
                                <div class="col-md-6 mb-2">
                                    <select name="month" id="month" class="form-control selectpicker">
                                        @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                                {{ date("F", mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <select name="year" id="year" class="form-control selectpicker">
                                        @foreach(range(date('Y')-5, date('Y')+1) as $y)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Warehouse filter -->
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

                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary px-4 me-2">Filter</button>
                                @if(isset($warehouse) && $warehouse)
                                    @php
                                        $params = [
                                            'year' => $year,
                                            'month' => $month,
                                            'warehouse_id' => $warehouse_id
                                        ];
                                        $hashParams = encrypt(json_encode($params));
                                    @endphp
                                    <a href="{{ url('admin/report/daily_sale_outlet_pdf') }}?hash={{ $hashParams }}" class="btn btn-danger px-4">
                                        <i class="fa fa-file-pdf"></i> Export PDF
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Show current warehouse information -->
                @if(isset($warehouse) && $warehouse)
                    <h5 class="text-center mb-3">Outlet: {{ $warehouse->name }}
                        @if(!auth()->user()->hasRole(['Sales', 'Admin Bisnis', 'Report']))
                            <div class="mt-2">
                                @php
                                    $params = [
                                        'year' => date('Y'),
                                        'month' => date('m'),
                                        'warehouse_id' => auth()->user()->warehouse_id
                                    ];
                                    $hashParams = encrypt(json_encode($params));
                                @endphp
                                <a href="{{ url('admin/report/daily_sale_outlet_pdf') }}?hash={{ $hashParams }}" class="btn btn-sm btn-danger px-4">
                                    <i class="fa fa-file-pdf"></i> Export PDF
                                </a>
                            </div>
                        @endif
                    </h5>
                @elseif($warehouse_id == '' && auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                    <h5 class="text-center mb-3">Outlet Belum Dipilih</h5>
                @elseif(!auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                    <h5 class="text-center mb-3">
                        Outlet: {{ auth()->user()->warehouse->name ?? 'Tidak Ditemukan' }}
                        <div class="mt-2">
                            @php
                                $params = [
                                    'year' => date('Y'),
                                    'month' => date('m'),
                                    'warehouse_id' => auth()->user()->warehouse_id
                                ];
                                $hashParams = encrypt(json_encode($params));
                            @endphp
                            <a href="{{ url('admin/report/daily_sale_outlet_pdf') }}?hash={{ $hashParams }}" class="btn btn-sm btn-danger px-4">
                                <i class="fa fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </h5>
                @endif

                @php
                    // Initialize totals
                    $totalMonthlyAmount = 0;
                    $totalMonthlyRealAmount = 0;

                    // Calculate totals from daily data
                    for ($i = 1; $i <= $number_of_day; $i++) {
                        $totalMonthlyAmount += $dailyData[$i]['amount'];
                        $totalMonthlyRealAmount += $dailyData[$i]['real_amount'] ?? $dailyData[$i]['amount'];
                    }
                @endphp

                <!-- Monthly Summary -->
                <div class="mb-4">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5 class="mb-2">Total Bulan {{ date("F Y", strtotime("$year-$month-01")) }}:</h5>
                            <h4>Rp. {{ number_format($totalMonthlyAmount, 0, '', '.') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered text-center" style="border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                        <thead>
                            <tr>
                                <th colspan="7" class="text-center">{{ date("F Y", strtotime("$year-$month-01")) }}</th>
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

                                            {{-- @if ($dailyData[$currentDay]['qty'] > 0)
                                                <strong>Total Produk Terjual</strong><br>
                                                <span>{{ number_format($dailyData[$currentDay]['qty'], 0, '', '.') }}</span><br><br>
                                            @endif --}}

                                            {{-- @if ($dailyData[$currentDay]['transaction'] > 0)
                                                <strong>Jumlah Transaksi Lunas</strong><br>
                                                <span>{{ number_format($dailyData[$currentDay]['transaction'], 0, '', '.') }}</span><br><br>
                                            @endif --}}

                                            @if ($dailyData[$currentDay]['amount'] > 0)
                                                {{-- <strong>Total Sales</strong><br> --}}
                                                <strong>Rp. {{ number_format($dailyData[$currentDay]['amount'], 0, '', '.') }}</strong><br>
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
    $("ul#report #daily-sale-outlet").addClass("active");

    // Initialize selectpicker if you're using Bootstrap Select
    $('.selectpicker').selectpicker({
        style: 'btn-link',
    });

    // Hide export PDF button when any filter changes (warehouse, month, year)
    $(document).ready(function() {
        // Store the initial values
        let initialWarehouseId = $('#warehouse_id').val();
        let initialMonth = $('#month').val();
        let initialYear = $('#year').val();
        let exportBtn = $('.btn-danger');

        // Function to check if any filter has changed
        function hasFilterChanged() {
            return $('#warehouse_id').val() !== initialWarehouseId ||
                   $('#month').val() !== initialMonth ||
                   $('#year').val() !== initialYear;
        }

        // Add event listeners to all filter elements
        $('#warehouse_id, #month, #year').on('change', function() {
            // Hide the export button if any filter has changed
            if (hasFilterChanged()) {
                exportBtn.hide();
            }
        });
    });
</script>
@endpush
