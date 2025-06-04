@extends('backend.layout.main') @section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Laporan Pembayaran</h3>
                        @if(isset($start_date) && isset($end_date))
                        <h4 class="text-center mt-3">Tanggal: {{ \Carbon\Carbon::parse($start_date)->translatedFormat('j M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->translatedFormat('j M Y') }}</h4>
                        @endif
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'report.paymentByDate', 'method' => 'post']) !!}
                            <div class="form-group">
                                <label for=""><strong>Pilih Tanggal</strong></label>
                                <div class="input-group mb-3">
                                    <input type="text" name="start_date" class="form-control date" required value="{{ $start_date ?? '' }}">
                                    <input type="text" name="end_date" class="form-control date" required value="{{ $end_date ?? '' }}">
                                </div>
                            </div>

                            @if(auth()->user()->hasRole('Admin Bisnis') || auth()->user()->hasRole('Report'))
                            <div class="form-group">
                                <label for="warehouse_id"><strong>Pilih Outlet</strong></label>
                                <select name="warehouse_id" id="warehouse_id" class="selectpicker form-control" data-live-search="true">
                                    <option value="0">Pilih Outlet</option>
                                    @foreach($warehouses as $warehouse)
                                    <option value="{{$warehouse->id}}" {{ isset($warehouse_id) && $warehouse_id == $warehouse->id ? 'selected' : '' }}>{{$warehouse->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                                <input type="hidden" name="warehouse_id" value="{{ auth()->user()->warehouse_id }}">
                            @endif

                            <div class="form-group mt-3">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>

        @if(isset($lims_payment_data))
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table id="report-table" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Outlet </th>
                                        <th>Tipe Pemesanan</th>
                                        <th>Tipe Pembayaran</th>
                                        <th>Total Pembayaran</th>
                                        <th>Total Pesanan (Qty)</th>
                                        <th>Kasir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lims_payment_data as $payment)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$payment->date}}</td>
                                        <td>{{$payment->warehouse->name}}</td>
                                        <td>{{$payment->order_type->name}}</td>
                                        <td>{{$payment->payment_method}}</td>
                                        <td>{{number_format($payment->total_amount, 0, '', ',')}}</td>
                                        <td>{{number_format($payment->total_qty, 0, '', ',')}}</td>
                                        <td>{{$payment->user->name}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="tfoot active">
                                    <th></th>
                                    <th>{{trans('file.Total')}}:</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>{{number_format(0, $general_setting->decimal, '', ',')}}</th>
                                    <th>{{number_format(0, $general_setting->decimal, '', ',')}}</th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report li#payment-report-menu").addClass("active");

    // Function to format number into number format
    function formatNumber(number) {
        // Remove non-digit characters
        var numericValue = number.toString().replace(/\D/g, "");

        // Add thousand separators
        var formattedNumber = numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        return formattedNumber;
    }

    @if(isset($lims_payment_data))
    $('#report-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  'Cari',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'select': { style: 'multi',  selector: 'td:first-child'},
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
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            }
        ],
        drawCallback: function () {
            var api = this.api();
            datatable_sum(api, false);
        }
    } );

    function datatable_sum(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 5 ).footer() ).html('Rp. ' + formatNumber(dt_selector.cells( rows, 5, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}})));
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.cells( rows, 6, { page: 'current' } ).data().sum().toFixed({{$general_setting->decimal}}));
        }
        else {
            $( dt_selector.column( 5 ).footer() ).html('Rp. ' + formatNumber(dt_selector.column( 5, {page:'current'} ).data().sum().toFixed({{$general_setting->decimal}})));
            $( dt_selector.column( 6 ).footer() ).html(dt_selector.column( 6, {page:'current'} ).data().sum().toFixed({{$general_setting->decimal}}));
        }
    }
    @endif

$(".daterangepicker-field").daterangepicker({
  callback: function(startDate, endDate, period){
    var start_date = startDate.format('YYYY-MM-DD');
    var end_date = endDate.format('YYYY-MM-DD');
    var title = start_date + ' to ' + end_date;
    $(this).val(title);
    $('input[name="start_date"]').val(start_date);
    $('input[name="end_date"]').val(end_date);
  }
});

// Initialize selectpicker if it exists
if($('.selectpicker').length)
    $('.selectpicker').selectpicker();
</script>
@endpush
