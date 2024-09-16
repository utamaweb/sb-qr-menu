@extends('backend.layout.main') 
@section('content')

<section class="forms">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Laporan Selisih Stok</h3>
            </div>
            {!! Form::open(['route' => 'report.differenceStockReport', 'method' => 'get']) !!}
            <div class="row product-report-filter d-flex justify-content-center align-items-center">
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label for="date-range" class="form-label"><strong>Pilih Tanggal</strong></label>
                        <div class="input-group">
                            <input type="text" id="date-range" class="daterangepicker-field form-control" value="{{$start_date}} s/d {{$end_date}}" required />
                            <input type="hidden" name="start_date" value="{{$start_date}}" />
                            <input type="hidden" name="end_date" value="{{$end_date}}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="shift-select" class="form-label"><strong>Pilih Shift</strong></label>
                        <div class="input-group">
                            <select id="shift-select" name="shift" class="form-control">
                                <option value="all" {{request()->shift == 'all' ? 'selected' : '' }}>Semua</option>
                                <option value="1" {{$shift[0] == 1 && count($shift) == 1 ? 'selected' : ''}}>1</option>
                                <option value="2" {{$shift[0] == 2 && count($shift) == 1 ? 'selected' : ''}}>2</option>
                                <option value="3" {{$shift[0] == 3 && count($shift) == 1 ? 'selected' : ''}}>3</option>
                            </select>
                        </div>
                    </div>
                </div>
                @if(auth()->user()->hasRole('Admin Bisnis'))
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label for="warehouse-select" class="form-label"><strong>Pilih Outlet</strong></label>
                        <div class="input-group">
                            <select id="warehouse-select" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="---Pilih Outlet---">
                                <option value="all" {{ $warehouse_request == 'all' ? 'selected' : ''}}>Semua Outlet</option>
                                @foreach($warehouses as $warehouse)
                                <option value="{{$warehouse->id}}" {{$warehouse->id == $warehouse_request ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-2 mt-3">
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
                    <div class="card-header d-flex align-items-center">
                        <a href="{{ route('report.differenceStockReport') }}" class="btn btn-info"><i class="dripicons-arrow-thin-left"></i> Kembali </a>
                    </div>
                    <div class="card-body">
                        <h4>Laporan Selisih Stok</h4>
                        <br>
                        <div class="table-responsive">
                            <table id="ingredient-table" class="table">
                                <thead>
                                    <tr>
                                        <th>Outlet</th>
                                        <th>Bahan Baku</th>
                                        <th>Selisih</th>
                                        <th>Shift</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stocks as $stock)
                                        <tr>
                                            <td>{{ $stock->warehouse_name }}</td>
                                            <td>{{ $stock->ingredient_name }}</td>
                                            <td>{{ $stock->total_difference_stock }}</td>
                                            <td>{{ $stock->shift_number }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
$("ul#report #laporan-selisih").addClass("active");

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
