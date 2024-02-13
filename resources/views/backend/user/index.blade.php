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
    @can('tambah-user')
        <a href="{{route('user.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> Tambah User</a>
            {{-- <a href="#" data-toggle="modal" data-target="#importProduct" class="btn btn-primary add-product-btn"><i class="dripicons-copy"></i> {{__('file.import_product')}}</a> --}}
    @endcan
        </div>
    <div class="table-responsive">
        <table id="ingredient-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Telp</th>
                    <th>Hak Akses</th>
                    <th>Status</th>
                    <th class="not-exported">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_user_list as $key=>$user)
                <tr data-id="{{$user->id}}">
                    <td>{{$key}}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email}}</td>
                    <td>{{ $user->phone}}</td>
                    <?php $role = DB::table('roles')->find($user->role_id);?>
                    <td>{{ $role->name }}</td>
                    @if($user->is_active)
                    <td><div class="badge badge-success">Aktif</div></td>
                    @else
                    <td><div class="badge badge-danger">Tidak Aktif</div></td>
                    @endif
                    <td>
                        @canany(['ubah-user', 'hapus-user'])
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                @can('ubah-user')
                                <li>
                                	<a href="{{ route('user.edit', $user->id) }}" class="btn btn-link"><i class="dripicons-document-edit"></i> Ubah</a>
                                </li>
                                @endcan
                                <li class="divider"></li>
                                @can('hapus-user')
                                {{ Form::open(['route' => ['user.destroy', $user->id], 'method' => 'DELETE'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> Hapus</button>
                                </li>
                                {{ Form::close() }}
                                @endcan
                            </ul>
                        </div>
                        @endcanany
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#outlet").siblings('a').attr('aria-expanded','true');
    $("ul#outlet").addClass("show");
    $("ul#outlet #user-list-menu").addClass("active");

    var ingredient_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
    $(document).on('click', '.open-EditUnitDialog', function() {
        var url = "ingredient/"
        var id = $(this).data('id').toString();
        url = url.concat(id).concat("/edit");

        $.get(url, function(data) {
            $("input[name='name']").val(data['name']);
            $("input[name='first_stock']").val(data['first_stock']);
            $("input[name='unit_id']").val(data['unit_id']);
            $("input[name='operation_value']").val(data['operation_value']);
            $("input[name='ingredient_id']").val(data['id']);
            $("#base_unit_edit").val(data['base_unit']);
            if(data['base_unit']!=null)
            {
                $(".operator").show();
                $(".operation_value").show();
            }
            else
            {
                $(".operator").hide();
                $(".operation_value").hide();
            }
            $('.selectpicker').selectpicker('refresh');

        });
    });

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
