@extends('backend.layout.main') @section('content')

<section>
    <div class="container-fluid">

        @if($errors->has('name'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}</div>
        @endif
        @if($errors->has('image'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ $errors->first('image') }}</div>
        @endif
        @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
        @endif
        @if(session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
        @endif

        <!-- Trigger the modal with a button -->
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#order-type-modal"><i
                class="dripicons-plus"></i> Tipe Pesanan</button>
    </div>
    <div class="table-responsive">
        <table id="" class="table" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>Tipe Pesanan</th>
                    {{-- <th>{{trans('file.Parent Category')}}</th> --}}
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
                @foreach ($orderTypes as $orderType)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$orderType->name}}</td>
                        <td>
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal-{{$orderType->id}}" ><i class="dripicons-document-edit"></i>Edit</button>
                            {{-- <button class="btn btn-warning">Edit</button> --}}
                            <!-- Edit Modal -->
                            <div id="editModal-{{$orderType->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                            class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                                <div class="modal-content">
                                    {{ Form::open(['route' => ['order_type.update', 1], 'method' => 'PUT', 'files' => true] ) }}
                                    <div class="modal-header">
                                        <h5 id="exampleModalLabel" class="modal-title">Update Tipe Pesanan</h5>
                                        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i
                                                    class="dripicons-cross"></i></span></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="italic">
                                            <small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <label>{{trans('file.name')}} *</label>
                                                <input type="text" value="{{$orderType->name}}" name="name" class="form-control">
                                            </div>
                                            <input type="hidden" name="order_type_id" value="{{$orderType->id}}">

                                            <div class="col-md-12 d-flex justify-content-end">
                                                <div class="form-group">
                                                    <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            </div>
                            </div>
                            <form action="{{route('order_type.destroy', $orderType->id)}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirmDelete()"><i class="dripicons-trash"></i>Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </thead>
        </table>
    </div>
</section>



{{-- Tambah Modal --}}
<div id="order-type-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
    class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{route('order_type.store')}}">
                @csrf
            {{-- {!! Form::open(['route' => 'order_type.store', 'method' => 'post', 'files' => true]) !!} --}}
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Tambah Tipe Pesanan</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i
                            class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <p class="italic">
                    <small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label>Nama</label>
                        {{-- {{Form::text('name',null,array('required' => 'required', 'class' => 'form-control', 'placeholder' => 'Masukkan nama tipe pesanan..'))}} --}}
                        <input type="text" name="name" required class="form-control" placeholder="Masukkan tipe pesanan...">
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                <div class="form-group">
                    <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>


@endsection
@push('scripts')
<script type="text/javascript">
    // $("ul#order-type").siblings('a').attr('aria-expanded','true');
    // $("ul#order-type").addClass("show");
    $("li#order-type").addClass("active");

    function confirmDelete() {
      if (confirm("If you delete order type all products under this order type will also be deleted. Are you sure want to delete?")) {
          return true;
      }
      return false;
    }

    var orderType_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).on("click", ".open-EditOrderTypeDialog", function(){
        $("#editModal input[name='is_sync_disable']").prop("checked", false);
        $("#editModal input[name='featured']").prop("checked", false);
        var url ="order_type/";
        var id = $(this).data('id').toString();
        url = url.concat(id).concat("/edit");
        $.get(url, function(data){
            $("#editModal input[name='name']").val(data['name']);
            $("#editModal select[name='parent_id']").val(data['parent_id']);
            $("#editModal input[name='orderType_id']").val(data['id']);
            if (data['is_sync_disable']) {
                $("#editModal input[name='is_sync_disable']").prop("checked", true);
            }
            if (data['featured']) {
                $("#editModal input[name='featured']").prop("checked", true);
            }
            $("#editModal input[name='page_title']").val(data['page_title']);
            $("#editModal input[name='short_description']").val(data['short_description']);
            $('.selectpicker').selectpicker('refresh');
        });
    });

    $('#orderType-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax":{
            url:"order_type/all",
            dataType: "json",
            type:"post"
        },
        "createdRow": function( row, data, dataIndex ) {
            $(row).attr('data-id', data['id']);
        },
        "columns": [
            {"data": "key"},
            {"data": "name"},
            {"data": "options"},
        ],
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
                footer:true
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
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
                footer:true
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                text: '<i title="delete" class="dripicons-cross"></i>',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        orderType_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                orderType_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(orderType_id.length && confirm("If you delete order type all products under this order type will also be deleted. Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'order_type/deletebyselection',
                                data:{
                                    orderTypeIdArray: orderType_id
                                },
                                success:function(data){
                                    dt.rows({ page: 'current', selected: true }).deselect();
                                    dt.rows({ page: 'current', selected: true }).remove().draw(false);
                                }
                            });
                        }
                        else if(!orderType_id.length)
                            alert('No order type is selected!');
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
