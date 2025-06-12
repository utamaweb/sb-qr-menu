@extends('backend.layout.main') @section('content')

<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Laporan Transaksi Produk</h3>
                        <h4 class="text-center mt-3">Tanggal: {{ \Carbon\Carbon::parse($start_date)->translatedFormat('j M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->translatedFormat('j M Y') }}</h4>
                        @if(isset($warehouseId) && $warehouseId != 'all' && $warehouse)
                            <h5 class="text-center mt-2">Outlet: {{ $warehouse->name }}</h5>
                        @elseif($warehouseId == 'all')
                            <h5 class="text-center mt-2">Outlet: Semua Outlet</h5>
                        @endif
                        @if(isset($regional_request) && $regional_request != 'all')
                            @php
                                $regional = \App\Models\Regional::find($regional_request);
                            @endphp
                            @if($regional)
                                <h5 class="text-center mt-2">Regional: {{ $regional->name }}</h5>
                            @endif
                        @elseif(isset($regional_request) && $regional_request == 'all' && auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                            <h5 class="text-center mt-2">Regional: Semua Regional</h5>
                        @endif
                    </div>

                    <div class="card-body">
                        {!! Form::open(['route' => 'report.product', 'method' => 'get']) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for=""><strong>Pilih Tanggal</strong></label>
                                        <div class="input-group">
                                            <input type="text" id="start_date" name="start_date" class="form-control date" required value="{{ $start_date }}">
                                            <input type="text" id="end_date" name="end_date" class="form-control date" required value="{{ $end_date }}">
                                        </div>
                                    </div>
                                </div>
                                @if(auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                                <div class="col-md-3">
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
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="warehouse_id"><strong>Pilih Outlet</strong></label>
                                        <select name="warehouse_id" id="warehouse-select" class="form-control selectpicker" data-live-search="true" required>
                                            <option value="all" {{ $warehouseId == 'all' ? 'selected' : ''}}>Semua Outlet</option>
                                            @foreach($warehouses as $w)
                                                <option value="{{ $w->id }}" {{ ($warehouseId == $w->id) ? 'selected' : '' }}>
                                                    {{ $w->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-2">
                                    <div class="form-group" style="margin-top: 28px;">
                                        <button class="btn btn-primary btn-block" type="submit">Filter</button>
                                    </div>
                                </div>
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-right mb-3">
                                    <small class="text-muted">
                                        Total Produk: {{ count($products) }} |
                                        Total Penjualan: Rp. {{ number_format(array_sum($products->pluck('subtotal')->toArray()), 0, ',', '.') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="ingredient-table" class="table table-hover" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th>Jumlah Terjual (Rupiah)</th>
                                        <th>Jumlah Terjual (Kuantitas)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->category->name }}</td>
                                            <td>Rp. {{ number_format($product->subtotal, 0, ',', '.') }}</td>
                                            <td>{{ number_format($product->qty, 0, ',', '.') }}</td>
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
</section>
@endsection

@push('scripts')
<script>
    // Fungsi untuk memeriksa tanggal
    function checkDates() {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);

        // Memeriksa apakah tanggal mulai lebih besar dari tanggal selesai
        if (startDate > endDate) {
            alert("Tanggal mulai tidak boleh lebih besar dari tanggal selesai.");
        }
    }

    // Menambahkan event listener untuk input tanggal
    document.getElementById('start_date').addEventListener('change', checkDates);
    document.getElementById('end_date').addEventListener('change', checkDates);
</script>
<script type="text/javascript">
    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #laporan-transaksi-produk").addClass("active");

    var ingredient_id = [];
    var user_verified = {{ json_encode(env('USER_VERIFIED')) }};

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.selectpicker').selectpicker('refresh');

    $('#ingredient-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
            "info"      : '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search"    : 'Cari',
            'paginate'  : {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next'    : '<i class="dripicons-chevron-right"></i>'
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

                    // Add "Semua Outlet" option
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
</script>
@endpush
