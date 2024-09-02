@extends('backend.layout.main') 
@section('content')

<section class="forms">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Laporan Sisa Stok</h3>
            </div>
            {!! Form::open(['route' => 'report.remainingStockReport', 'method' => 'get']) !!}
            <div class="row product-report-filter d-flex justify-content-center align-items-center">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="date-range" class="form-label"><strong>Pilih Tahun</strong></label>
                        <div class="input-group">
                            <select name="year" id="year" class="form-control">
                                <option value="">---Pilih Tahun---</option>
                                @php
                                    $tahun = date('Y');
                                    $minYear = $tahun - 10;
                                    $maxYear = $tahun;
                                @endphp

                                @for ($i = $maxYear; $i >= $minYear; $i--)
                                    <option value="{{ $i }}" {{$year == $i ? 'selected' : ''}}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 ">
                    <div class="form-group">
                        <label for="date-range" class="form-label"><strong>Pilih Bulan</strong></label>
                        <div class="input-group">
                            <select name="month" id="month" class="form-control">
                                <option value="">---Pilih Bulan---</option>
                                @php
                                    $currentMonth = date('m');
                                    for ($m = 1; $m <= 12; $m++) {
                                        $monthValue = str_pad($m, 2, '0', STR_PAD_LEFT);
                                        $monthLabel = Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F');
                                @endphp
                                        <option value="{{ $monthValue }}" {{ $month == $monthValue ? 'selected' : '' }}>
                                            {{ $monthLabel }}
                                        </option>
                                @php
                                    }
                                @endphp
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mt-4">
                    <div class="form-group text-center">
                        <button class="btn btn-primary" type="submit">{{trans('file.submit')}}</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>

<div class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <a href="{{ route('report.differenceStockReport') }}" class="btn btn-info">
                            <i class="dripicons-arrow-thin-left"></i> Kembali
                        </a>
                        <a href="{{ route('report.remainingStockReportPrint') }}?month={{$month}}&year={{$year}}" class="btn btn-success">
                            <i class="dripicons-print"></i> Cetak PDF
                        </a>
                    </div>
                    <div class="card-body">
                        <h4>Laporan Selisih Stok</h4>
                        <br>
                        <div class="row">
                            @foreach($formattedStocks as $warehouse)
                            <div class="col-md-6  align-items-stretch"> <!-- Flexbox added here -->
                                <div class="card mb-4 flex-fill"> <!-- Flexbox fill added here -->
                                    <div class="card-header">
                                        <h4>{{ $warehouse['warehouse_name'] }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Bahan Baku</th>
                                                    <th>Satuan</th>
                                                    <th>Total Sisa Stok</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($warehouse['stocks'] as $stock)
                                                <tr>
                                                    <td>{{ $stock['ingredient_name'] }}</td>
                                                    <td>{{ $stock['unit_name'] }}</td>
                                                    <td>{{ $stock['total_last_stock'] }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div> <!-- End row -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script type="text/javascript">
$('.selectpicker').selectpicker();
$("ul#report").siblings('a').attr('aria-expanded','true');
$("ul#report").addClass("show");
$("ul#report #laporan-sisa").addClass("active");

$('.selectpicker').selectpicker('refresh');

$(".daterangepicker-field").daterangepicker({
  callback: function(startDate, endDate, period){
    var start_date = startDate.format('YYYY-MM-DD');
    var end_date = endDate.format('YYYY-MM-DD');
    var title = start_date + ' s/d ' + end_date;
    $(this).val(title);
    $(".product-report-filter input[name=start_date]").val(start_date);
    $(".product-report-filter input[name=end_date]").val(end_date);
  }
});

$('#ingredient-table').DataTable( {
    "order": [],
    'language': {
        'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
        "info": '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
        "search": 'Cari',
        'paginate': {
                'previous': '<i class="dripicons-chevron-left"></i>',
                'next': '<i class="dripicons-chevron-right"></i>'
        }
    },
    'columnDefs': [
        // Add specific column definitions if needed
    ],
    'select': { 
        style: 'multi',  
        selector: 'td:first-child'
    },
    'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
    dom: '<"row"lfB>rtip',
    buttons: [
        {
            extend: 'pdf',
            text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
            exportOptions: {
                columns: ':visible:Not(.not-exported)',
                rows: ':visible'
            },
        },
        {
            extend: 'excel',
            text: '<i title="export to excel" class="dripicons-document-new"></i>',
            exportOptions: {
                columns: ':visible:Not(.not-exported)',
                rows: ':visible'
            },
        },
        {
            extend: 'csv',
            text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
            exportOptions: {
                columns: ':visible:Not(.not-exported)',
                rows: ':visible'
            },
        },
        {
            extend: 'print',
            text: '<i title="print" class="fa fa-print"></i>',
            exportOptions: {
                columns: ':visible:Not(.not-exported)',
                rows: ':visible'
            },
        },
        {
            extend: 'colvis',
            text: '<i title="column visibility" class="fa fa-eye"></i>',
            columns: ':gt(0)'
        },
    ],
});
</script>
@endpush
