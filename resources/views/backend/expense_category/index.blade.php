@extends('backend.layout.main') @section('content')

<section>
    <div class="container-fluid">

    @if($errors->has('name'))
    <div class="alert alert-danger alert-dismissible text-center">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}
    </div>
    @endif
    @if(session()->has('message'))
    <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert"
            aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
    @endif
    @if(session()->has('not_permitted'))
    <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert"
            aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
    @endif
        @can('tambah-pengeluaran')
        <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-info"><i class="dripicons-plus"></i> Tambah Nama Pengeluaran</a>&nbsp;
        @endcan
    </div>
    <div class="table-responsive">
        <table id="ingredient-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>Nama Pengeluaran</th>
                    <th class="not-exported">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_expense_category_all as $key=>$expense_category)
                <tr data-id="{{$expense_category->id}}">
                    <td>{{$key}}</td>
                    <td>{{ $expense_category->name }}</td>
                    <td>
                        @can('ubah-pengeluaran')
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#editModal-{{$expense_category->id}}"><i class="dripicons-document-edit"></i> Ubah</button>
                        {{-- Edit Modal --}}
                        <div id="editModal-{{$expense_category->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 id="exampleModalLabel" class="modal-title"> Update Nama Pengeluaran</h5>
                                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                                </div>
                                <div class="modal-body">
                                <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                                    <form action="{{route('expense_categories.update', $expense_category->id)}}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                        <div class="form-group">
                                            <label>Nama Nama Pengeluaran *</label>
                                            <input type="text" value="{{$expense_category->name}}" name="name" required class="form-control">
                                        </div>
                                        {{-- {{Form::text('name',null,array('required' => 'required', 'class' => 'form-control'))}} --}}
                                        </div>
                                        <input type="submit" value="Submit" class="btn btn-primary">
                                    </form>
                                </div>
                            </div>
                            </div>
                        </div>
                        @endcan
                        @can('hapus-pengeluaran')
                        {{ Form::open(['route' => ['expense_categories.destroy', $expense_category->id], 'method' => 'DELETE'] ) }}
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> Hapus</button>
                                {{ Form::close() }}
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>


<!-- Create Modal -->
<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'expense_categories.store', 'method' => 'post']) !!}
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Tambah Nama Pengeluaran</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                <form>
                    <div class="form-group">
                        <label>Nama Pengeluaran *</label>
                        <input type="text" name="name" required class="form-control">
                    </div>
                    <input type="submit" value="Submit" class="btn btn-primary">
            </form>
        </div>
        {{ Form::close() }}
    </div>
</div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#product").siblings('a').attr('aria-expanded','true');
    $("ul#product").addClass("show");
    $("ul#product #unit-menu").addClass("active");

    var ingredient_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

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

    $( "#select_all" ).on( "change", function() {
        if ($(this).is(':checked')) {
            $("tbody input[type='checkbox']").prop('checked', true);
        }
        else {
            $("tbody input[type='checkbox']").prop('checked', false);
        }
    });

    $("#export").on("click", function(e){
        e.preventDefault();
        var unit = [];
        $(':checkbox:checked').each(function(i){
          unit[i] = $(this).val();
        });
        $.ajax({
           type:'POST',
           url:'/exportunit',
           data:{

                unitArray: unit
            },
           success:function(data){
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
        if($(this).val()){
            $("#createModal .operator").show();
            $("#createModal .operation_value").show();
        }
        else{
            $("#createModal .operator").hide();
            $("#createModal .operation_value").hide();
        }
    });

    $('#base_unit_edit').on('change', function() {
        if($(this).val()){
            $("#editModal .operator").show();
            $("#editModal .operation_value").show();
        }
        else{
            $("#editModal .operator").hide();
            $("#editModal .operation_value").hide();
        }
    });
});

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
            {
                "orderable": false,
                'targets': [0, 2]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
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
            {
                text: '<i title="delete" class="dripicons-cross"></i>',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        ingredient_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                ingredient_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(ingredient_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'ingredient/deletebyselection',
                                data:{
                                    unitIdArray: ingredient_id
                                },
                                success:function(data){
                                    alert(data);
                                }
                            });
                            dt.rows({ page: 'current', selected: true }).remove().draw(false);
                        }
                        else if(!ingredient_id.length)
                            alert('No unit is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    } );
</script>
@endpush
