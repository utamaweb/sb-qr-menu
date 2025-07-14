@extends('backend.layout.main') @section('content')
    <section>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Laporan Tutup Kasir</span>
                </div>

                <div class="card-body">
                    @if(auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                    <div class="row mb-4">
                        <div class="col-md-12">
                            {!! Form::open(['route' => 'close-cashier.index', 'method' => 'get']) !!}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><strong>Pilih Regional</strong></label>
                                        <select id="regional-select" name="regional_id" class="form-control selectpicker" data-live-search="true" data-live-search-style="begins" title="Pilih regional">
                                            <option value="all" {{ $regional_request == 'all' ? 'selected' : ''}}>Semua Regional</option>
                                            @foreach($regionals as $regional)
                                            <option value="{{$regional->id}}" {{$regional->id == $regional_request ? 'selected' : ''}}>{{$regional->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><strong>Pilih Outlet</strong></label>
                                        <select id="warehouse-select" name="warehouse_id" class="form-control selectpicker" data-live-search="true" data-live-search-style="begins" title="Pilih outlet">
                                            <option value="all" {{ $warehouse_request == 'all' ? 'selected' : ''}}>Semua Outlet</option>
                                            @foreach($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}" {{$warehouse->id == $warehouse_request ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for=""><strong>Tanggal Awal</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="start_date" class="form-control date" value="{{ $start_date ?? date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for=""><strong>Tanggal Akhir</strong></label>
                                        <div class="input-group">
                                            <input type="text" name="end_date" class="form-control date" value="{{ $end_date ?? date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><strong>&nbsp;</strong></label>
                                        <div class="input-group">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table id="ingredient-table" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Waktu Buka / Tutup Kasir</th>
                                    <th>Outlet</th>
                                    <th>Shift</th>
                                    <th>Modal Awal</th>
                                    <th>Total Tunai</th>
                                    <th>Uang Laci</th>
                                    <th>Selisih</th>
                                    <th>Total Pengeluaran</th>
                                    <th>Total Non Tunai</th>
                                    <th>QRIS / TF</th>
                                    <th>GoFood</th>
                                    <th>GrabFood</th>
                                    <th>ShopeeFood</th>
                                    <th class="not-exported">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($closeCashiers as $key => $closeCashier)
                                    <tr data-id="{{ $closeCashier->id }}">
                                        <td>{{ ++$key }}</td>
                                        <td><b>Buka:</b> {{ $closeCashier->open_time }} <br>
                                            <b>Tutup:</b> {{ $closeCashier->close_time }}
                                        </td>
                                        <td>{{ $closeCashier->shift->warehouse->name }}</td>
                                        <td>{{ $closeCashier->shift->shift_number }}</td>
                                        <td>@currency($closeCashier->initial_balance)</td>
                                        <td>@currency($closeCashier->total_cash)</td>
                                        <td>@currency($closeCashier->cash_in_drawer)</td>
                                        <td>@currency($closeCashier->difference)</td>
                                        <td>@currency($closeCashier->total_expense)</td>
                                        <td>@currency($closeCashier->total_non_cash)</td>
                                        <td>@currency($closeCashier->qris_omzet + $closeCashier->transfer_omzet)</td>
                                        <td>@currency($closeCashier->gofood_omzet)</td>
                                        <td>@currency($closeCashier->grabfood_omzet)</td>
                                        <td>@currency($closeCashier->shopeefood_omzet)</td>
                                        <td>
                                            <div class="row">
                                                <a href="{{ route('close-cashier.show', $closeCashier->id) }}" class="btn btn-link"><i class="dripicons-italic"></i> Detail</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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
        $("ul#report").siblings('a').attr('aria-expanded', 'true');
        $("ul#report").addClass("show");
        $("ul#report #laporan-tutup-kasir").addClass("active");

        // Initialize selectpicker and datepicker
        $('.selectpicker').selectpicker();

        // Initialize date picker
        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });

        // Handle Regional-Warehouse dependency
        $(document).ready(function() {
            // On regional select change
            $('#regional-select').change(function() {
                var regionalId = $(this).val();

                // Disable warehouse select while loading
                $('#warehouse-select').prop('disabled', true).selectpicker('refresh');

                // Make AJAX request
                $.ajax({
                    url: '{{ url("admin/close-cashier/get-warehouses-by-regional") }}/' + regionalId,
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
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching warehouses: " + error);
                    }
                });
            });
        });

        // Initialize DataTable
        $('#ingredient-table').DataTable({
            "order": [],
            'language': {
                'lengthMenu': '_MENU_ {{ trans('file.records per page') }}',
                "info": '<small>{{ trans('file.Showing') }} _START_ - _END_ (_TOTAL_)</small>',
                "search": 'Cari',
                'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
                }
            },
            'columnDefs': [
                // {
                //     "orderable": false,
                //     'targets': [0, 2]
                // },
                // {
                //     'render': function(data, type, row, meta){
                //         if(type === 'display'){
                //             data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                //         }

                //        return data;
                //     },
                //     'checkboxes': {
                //        'selectRow': true,
                //        'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                //     },
                //     'targets': [0]
                // }
            ],
            'select': {
                style: 'multi',
                selector: 'td:first-child'
            },
            'lengthMenu': [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            dom: '<"row"lfB>rtip',
            buttons: [{
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
                // {
                //     text: '<i title="delete" class="dripicons-cross"></i>',
                //     className: 'buttons-delete',
                //     action: function ( e, dt, node, config ) {
                //             ingredient_id.length = 0;
                //             $(':checkbox:checked').each(function(i){
                //                 if(i){
                //                     ingredient_id[i-1] = $(this).closest('tr').data('id');
                //                 }
                //             });
                //             if(ingredient_id.length && confirm("Are you sure want to delete?")) {
                //                 $.ajax({
                //                     type:'POST',
                //                     url:'ingredient/deletebyselection',
                //                     data:{
                //                         unitIdArray: ingredient_id
                //                     },
                //                     success:function(data){
                //                         alert(data);
                //                     }
                //                 });
                //                 dt.rows({ page: 'current', selected: true }).remove().draw(false);
                //             }
                //             else if(!ingredient_id.length)
                //                 alert('No unit is selected!');
                //     }
                // },
                {
                    extend: 'colvis',
                    text: '<i title="column visibility" class="fa fa-eye"></i>',
                    columns: ':gt(0)'
                },
            ],
        });
    </script>
@endpush
