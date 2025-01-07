@extends('backend.layout.main') @section('content')

<section>
    <div class="container-fluid">

        @include('includes.alerts')

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Daftar Stok</span>
                {{-- @can('tambah-bahanbaku')
                <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-info"><i class="dripicons-plus"></i> Tambah Bahan Baku</a>&nbsp;
                @endcan --}}
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="ingredient-table" class="table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Nama Bahan Baku</th>
                                <th>Outlet</th>
                                @if ($checkShift == false)
                                    {{-- <th>Stok Awal</th> --}}
                                    <th>Stok Masuk</th>
                                    <th>Stok Terjual</th>
                                    <th>Stok Akhir</th>
                                @else
                                    <th>Pesan</th>
                                @endif
                                <th>Unit</th>
                                <th class="not-exported">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lims_ingredient_all as $key=>$ingredient)
                            <tr data-id="{{$ingredient->id}}">
                                <td class="text-center">{{++$key}}</td>
                                <td>{{ $ingredient->ingredient->name }}</td>
                                <td>{{ $ingredient->warehouse->name }}</td>
                                @if ($checkShift == false)
                                    {{-- <td>{{ $ingredient->first_stock }}</td> --}}
                                    <td>{{ number_format($ingredient->stock_in, 0, '', '.') }}</td>
                                    <td>{{ number_format($ingredient->stock_used, 0, '', '.') }}</td>
                                    <td>{{ number_format($ingredient->last_stock, 0, '', '.') }}</td>
                                @else
                                    <td>Shift kasir sedang berjalan, tutup kasir untuk melihat stok.</td>
                                @endif
                                <td>{{ $ingredient->ingredient->unit->unit_name }}</td>
                                <td>
                                    <div class="row">
                                    @can('hapus-bahanbaku')
                                    {{ Form::open(['route' => ['stok.destroy', $ingredient->id], 'method' => 'DELETE'] ) }}
                                                <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> Hapus</button>
                                            {{ Form::close() }}
                                    @endcan
                                </td>
                            </div>
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
    $("#daftar-stok").addClass("active");

    $('#ingredient-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
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
            // {
            //     text: '<i title="delete" class="dripicons-cross"></i>',
            //     className: 'buttons-delete',
            //     action: function ( e, dt, node, config ) {
            //         if(user_verified == '1') {
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
            //         }
            //         else
            //             alert('This feature is disable for demo!');
            //     }
            // },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    } );
</script>
@endpush
