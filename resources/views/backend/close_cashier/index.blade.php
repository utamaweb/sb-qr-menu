@extends('backend.layout.main') @section('content')
    <section>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Laporan Tutup Kasir</span>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="ingredient-table" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Waktu Buka / Tutup Kasir</th>
                                    <th>Kasir</th>
                                    <th>Outlet</th>
                                    <th>Shift</th>
                                    <th>Modal Awal</th>
                                    <th>Total Tunai</th>
                                    <th>Total Non Tunai</th>
                                    <th>Uang Laci</th>
                                    <th>Selisih</th>
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
                                        <td>{{ $closeCashier->shift->user->name }}</td>
                                        <td>{{ $closeCashier->shift->warehouse->name }}</td>
                                        <td>{{ $closeCashier->shift->shift_number }}</td>
                                        <td>@currency($closeCashier->initial_balance)</td>
                                        <td>@currency($closeCashier->total_cash)</td>
                                        <td>@currency($closeCashier->total_non_cash)</td>
                                        <td>@currency($closeCashier->cash_in_drawer)</td>
                                        <td>@currency($closeCashier->difference)</td>
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

        var ingredient_id = [];
        var user_verified = <?php echo json_encode(env('USER_VERIFIED')); ?>;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#select_all").on("change", function() {
                if ($(this).is(':checked')) {
                    $("tbody input[type='checkbox']").prop('checked', true);
                } else {
                    $("tbody input[type='checkbox']").prop('checked', false);
                }
            });

            $("#export").on("click", function(e) {
                e.preventDefault();
                var unit = [];
                $(':checkbox:checked').each(function(i) {
                    unit[i] = $(this).val();
                });
                $.ajax({
                    type: 'POST',
                    url: '/exportunit',
                    data: {

                        unitArray: unit
                    },
                    success: function(data) {
                        alert('Exported to CSV file successfully! Click Ok to download file');
                        window.location.href = data;
                    }
                });
            });

            $('.open-CreateUnitDialog').on('click', function() {
                $(".operator").hide();
                $(".operation_value").hide();

            });

            $('#base_unit_create').on('change', function() {
                if ($(this).val()) {
                    $("#createModal .operator").show();
                    $("#createModal .operation_value").show();
                } else {
                    $("#createModal .operator").hide();
                    $("#createModal .operation_value").hide();
                }
            });

            $('#base_unit_edit').on('change', function() {
                if ($(this).val()) {
                    $("#editModal .operator").show();
                    $("#editModal .operation_value").show();
                } else {
                    $("#editModal .operator").hide();
                    $("#editModal .operation_value").hide();
                }
            });
        });

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
