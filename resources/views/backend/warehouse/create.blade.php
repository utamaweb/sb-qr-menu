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
    @can('tambah-warehouse')
        <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-info"><i class="dripicons-plus"></i> Tambah Outlet</a>&nbsp;
    @endcan
    </div>
    <div class="table-responsive">
        <table id="ingredient-table" class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Outlet</th>
                    <th>Bisnis</th>
                    <th>Alamat</th>
                    <th class="not-exported">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_warehouse_all as $key=>$warehouse)
                <tr data-id="{{$warehouse->id}}">
                    <td>{{++$key}}</td>
                    <td>{{ $warehouse->name }}</td>
                    <td>{{ $warehouse->business->name }}</td>
                    <td>{{ $warehouse->address }}</td>
                    <td>
                        @can('ubah-warehouse')
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#editModal-{{$warehouse->id}}"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</button>
                        {{-- Edit Modal --}}
                        <div id="editModal-{{$warehouse->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 id="exampleModalLabel" class="modal-title"> Ubah Outlet</h5>
                                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                                </div>
                                <div class="modal-body">
                                <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                                    <form action="{{route('outlet.update', $warehouse->id)}}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                        <div class="form-group">
                                            <label>Nama Outlet *</label>
                                            <input type="text" value="{{$warehouse->name}}" name="name" required class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Nama Bisnis *</label>
                                            @if(auth()->user()->hasRole('Superadmin'))
                                            <select name="business_id" class="form-control">
                                                <option value="">---Pilih Bisnis---</option>
                                                @foreach($business as $bisnis)
                                                <option value="{{$bisnis->id}}" {{$bisnis->id == $warehouse->business_id ? 'selected' : ''}}>{{$bisnis->name}}</option>
                                                @endforeach
                                            </select>
                                            @elseif(auth()->user()->hasRole('Admin Bisnis'))
                                            <input type="text" readonly name="business_id" class="form-control" value="{{auth()->user()->business->name}}">
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label>Alamat *</label>
                                            <input type="text" value="{{$warehouse->address}}" name="address" required class="form-control">
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
                        @can('hapus-warehouse')
                        {{ Form::open(['route' => ['outlet.destroy', $warehouse->id], 'method' => 'DELETE'] ) }}
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button>
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
            {!! Form::open(['route' => 'outlet.store', 'method' => 'post']) !!}
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Tambah Outlet</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                <form>
                    <div class="form-group">
                        <label>Nama Outlet *</label>
                        <input type="text" name="name" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nama Bisnis *</label>
                        @if(auth()->user()->hasRole('Superadmin'))
                        <select name="business_id" class="form-control">
                            <option value="">---Pilih Bisnis---</option>
                            @foreach($business as $bisnis)
                            <option value="{{$bisnis->id}}">{{$bisnis->name}}</option>
                            @endforeach
                        </select>
                        @elseif(auth()->user()->hasRole('Admin Bisnis'))
                        <input type="text" readonly class="form-control" name="business_id" value="{{auth()->user()->business->name}}">
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Alamat *</label>
                        <input type="text" name="address" required class="form-control">
                    </div>
                    <input type="submit" value="Submit" class="btn btn-primary">
            </form>
        </div>
        {{ Form::close() }}
    </div>
</div>
</div>

<div id="importUnit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['route' => 'outlet.import', 'method' => 'post', 'files' => true]) !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title"> {{trans('file.Import Unit')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
            <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
            <p>{{trans('file.The correct column order is')}} (name*, unit_name*, base_unit [unit code], operator, operation_value) {{trans('file.and you must follow this')}}.</p>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{trans('file.Upload CSV File')}} *</label>
                        {{Form::file('file', array('class' => 'form-control','required'))}}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label> {{trans('file.Sample File')}}</label>
                        <a href="sample_file/warehouse.csv" class="btn btn-info btn-block btn-md"><i class="dripicons-download"></i>  {{trans('file.Download')}}</a>
                    </div>
                </div>
            </div>
            <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
        </div>
        {{ Form::close() }}
      </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#outlet").siblings('a').attr('aria-expanded','true');
    $("ul#outlet").addClass("show");
    $("ul#outlet #warehouse-menu").addClass("active");

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
            $("input[name='address']").val(data['address']);
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
    } );
</script>
@endpush
