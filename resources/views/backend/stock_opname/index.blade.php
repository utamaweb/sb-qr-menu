@extends('backend.layout.main') @section('content')

<section>
    <div class="container-fluid">

        @include('includes.alerts')

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Stok Opname</span>
                @can('tambah-stokopname')
                <a href="{{route('stock-opname.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> Tambah Stok Opname</a>
                @endcan
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="ingredient-table" class="table">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Nama Stok Opname</th>
                                <th>Tanggal</th>
                                <th>Outlet</th>
                                <th class="not-exported">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockOpnames as $key=>$stockOpname)
                            <tr data-id="{{$stockOpname->id}}">
                                <td class="text-center">{{++$key}}</td>
                                <td>{{ $stockOpname->name }}</td>
                                <td>{{ $stockOpname->created_at}}</td>
                                <td>{{ $stockOpname->warehouse->name}}</td>
                                <td>
                                    <div class="row">
                                    <a href="{{route('stock-opname.show', $stockOpname->id)}}" class="btn btn-link"><i class="dripicons-italic"></i> Detail</a>
                                    {{-- <button type="button" class="btn btn-link" data-toggle="modal" data-target="#editModal-{{$stockOpname->id}}"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</button> --}}
                                    {{-- {{ Form::open(['route' => ['stock-opname.destroy', $stockOpname->id], 'method' => 'DELETE'] ) }}
                                                <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button>
                                            {{ Form::close() }} --}}
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
    $("#stock-opname").addClass("active");

    $('#ingredient-table').DataTable( {
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
        'columnDefs': [
            // {
            //     "orderable": false,
            //     'targets': [0, 2]
            // },
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
