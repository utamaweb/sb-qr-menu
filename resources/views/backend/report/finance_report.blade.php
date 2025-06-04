@extends('backend.layout.main')
@section('content')

<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Laporan Finance</h3>
                        <h4 class="text-center mt-3">Tanggal: {{ \Carbon\Carbon::parse($start_date)->translatedFormat('j M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->translatedFormat('j M Y') }}</h4>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'financeReport', 'method' => 'get']) !!}
                            <div class="form-group">
                                <label for=""><strong>Pilih Tanggal</strong></label>
                                <div class="input-group">
                                    <input type="text" name="start_date" class="form-control date" required value="{{ $start_date }}">
                                    <input type="text" name="end_date" class="form-control date" required value="{{ $end_date }}">
                                </div>
                            </div>

                            @if(auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                                <div class="form-group">
                                    <label><strong>Pilih Regional</strong></label>
                                    <select id="regional-select" name="regional_id" class="form-control selectpicker" data-live-search="true" data-live-search-style="begins"
                                    title="Pilih regional">
                                        <option value="all" {{ $regional_request == 'all' ? 'selected' : ''}}>Semua Regional</option>
                                        @foreach($regionals as $regional)
                                        <option value="{{$regional->id}}" {{$regional->id == $regional_request ? 'selected' : ''}}>{{$regional->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><strong>Pilih Outlet</strong></label>
                                    <select id="warehouse-select" name="warehouse_id" class="form-control selectpicker" data-live-search="true" data-live-search-style="begins"
                                    title="Pilih outlet">
                                        <option value="all" {{ $warehouse_request == 'all' ? 'selected' : ''}}>Semua Outlet</option>
                                        @foreach($warehouses as $warehouse)
                                        <option value="{{$warehouse->id}}" {{$warehouse->id == $warehouse_request ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif


                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ingredient-table" class="table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Outlet</th>
                                        <th>Tgl, Jam</th>
                                        <th>Shift</th>
                                        <th>Nama Kasir</th>
                                        <th>Jumlah Uang Tunai</th>
                                        <th>Jumlah Non Tunai</th>
                                        <th>Jumlah Pengeluaran</th>
                                        <th>Jumlah Uang di Laci</th>
                                        <th>Cash Modal Awal</th>
                                        <th>Cash Modal Akhir</th>
                                        <th>Jmlh Transaksi Refund (QTY)</th>
                                        <th>Jmlh Transaksi Refund (Rupiah)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($finance as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><b>{{ $item->shift->warehouse->name ?? 'N/A' }}</b></td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                                        <td><b>{{ $item->shift->shift_number ?? 'N/A' }}</b></td>
                                        <td>{{ $item->shift->user->name ?? 'N/A' }}</td>
                                        <td>@currency($item->total_cash)</td>
                                        <td>@currency($item->total_non_cash)</td>
                                        <td>@currency($item->total_expense)</td>
                                        <td>@currency($item->total_cash - $item->total_expense)</td>
                                        <td>@currency($item->initial_balance)</td>
                                        <td>@currency($item->cash_in_drawer)</td>
                                        <td>{{ $item->count_transaction_canceled ?? 0 }}</td>
                                        <td>@currency($item->total_transaction_canceled)</td>
                                    </tr>
                                    @endforeach

                                    @if(count($finance) == 0)
                                    <tr>
                                        <td colspan="12" class="text-center">Tidak ada data yang tersedia</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
$('.selectpicker').selectpicker();
$("ul#report").siblings('a').attr('aria-expanded','true');
$("ul#report").addClass("show");
$("ul#report #finance-report").addClass("active");

// Add CSS for the loading indicator
$('head').append(`
    <style>
        .loader-container {
            display: none;
            position: relative;
            width: 100%;
            height: 30px;
            text-align: center;
            margin-top: 5px;
        }
        .loader {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0,0,0,0.1);
            border-radius: 50%;
            border-top-color: #3498db;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
`);

// Add loading indicator element after warehouse select
$('#warehouse-select').after('<div class="loader-container"><div class="loader"></div><small class="ml-2">Loading outlets...</small></div>');

// Handle Regional-Warehouse dependency
$(document).ready(function() {
    // On regional select change
    $('#regional-select').change(function() {
        var regionalId = $(this).val();

        // Show loading indicator
        $('.loader-container').show();

        // Disable warehouse select while loading
        $('#warehouse-select').prop('disabled', true).selectpicker('refresh');

        // Make AJAX request
        $.ajax({
            url: '{{ route("getWarehousesByRegional", "") }}/' + regionalId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Clear current options
                $('#warehouse-select').empty();

                // Add "All Outlets" option
                $('#warehouse-select').append('<option value="all">Semua Outlet</option>');

                // Add warehouses from response
                $.each(data, function(index, warehouse) {
                    $('#warehouse-select').append('<option value="' + warehouse.id + '">' + warehouse.name + '</option>');
                });

                // Enable warehouse select and refresh
                $('#warehouse-select').prop('disabled', false).selectpicker('refresh');

                // Hide loading indicator
                $('.loader-container').hide();
            },
            error: function(xhr, status, error) {
                console.error("Error fetching warehouses: " + error);

                // Hide loading indicator even on error
                $('.loader-container').hide();

                // Re-enable warehouse select
                $('#warehouse-select').prop('disabled', false).selectpicker('refresh');

                // Show error message
                alert("Error loading outlets. Please try again.");
            }
        });
    });
});

$('.selectpicker').selectpicker('refresh');

$('#ingredient-table').DataTable({
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
            title: 'Laporan Finance - {{ \Carbon\Carbon::parse($start_date)->format("d-m-Y") }} s/d {{ \Carbon\Carbon::parse($end_date)->format("d-m-Y") }}',
            filename: 'Finance_Report_{{ \Carbon\Carbon::parse($start_date)->format("d-m-Y") }}_{{ \Carbon\Carbon::parse($end_date)->format("d-m-Y") }}',
            exportOptions: {
                columns: ':visible:Not(.not-exported)',
                rows: ':visible'
            },
            customize: function(xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];

                // Bold headers
                $('row:first c', sheet).attr('s', '2');

                // Format currency columns
                $('row:not(:first) c[r^="F"], row:not(:first) c[r^="G"], row:not(:first) c[r^="H"], row:not(:first) c[r^="I"], row:not(:first) c[r^="J"], row:not(:first) c[r^="K"], row:not(:first) c[r^="M"]', sheet).each(function() {
                    // Apply currency format (Indonesian Rupiah)
                    $(this).attr('s', '61');
                });
            },
            sheetName: 'Laporan Finance',
            messageTop: function() {
                var regional = $('#regional-select option:selected').text();
                var warehouse = $('#warehouse-select option:selected').text();

                var filterInfo = 'Regional: ' + regional;
                if (warehouse) {
                    filterInfo += ' | Outlet: ' + warehouse;
                }

                return filterInfo;
            }
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

// Date validation
$(document).ready(function() {
    function validateDates() {
        var startDate = new Date($("input[name='start_date']").val());
        var endDate = new Date($("input[name='end_date']").val());

        // Check if end date is before start date
        if (startDate > endDate) {
            alert("Tanggal Mulai tidak boleh lebih besar dari Tanggal Selesai.");
            $("input[name='start_date']").val('');
            $("input[name='end_date']").val('');
            return false;
        }

        // Calculate the difference in days
        var timeDiff = Math.abs(endDate.getTime() - startDate.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

        // Check if date range exceeds 30 days
        if (diffDays > 30) {
            alert("Rentang tanggal tidak boleh lebih dari 30 hari.");
            $("input[name='end_date']").val($("input[name='start_date']").val());
            return false;
        }

        return true;
    }

    // Add validation on date change
    $("input[name='start_date'], input[name='end_date']").on("change", function() {
        validateDates();
    });

    // Add validation on form submit
    $('form').on('submit', function(e) {
        if (!validateDates()) {
            e.preventDefault();
            return false;
        }
        return true;
    });
});
</script>
@endpush
