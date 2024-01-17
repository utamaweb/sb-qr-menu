@extends('backend.layout.main') @section('content')

<section>
    <div class="table-responsive">
        <table id="ingredient-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>Waktu Buka / Tutup Kasir</th>
                    <th>Kasir</th>
                    <th>Outlet</th>
                    <th>Modal Awal</th>
                    <th>Saldo Akhir</th>
                    <th>Total Tunai</th>
                    <th>Total Non Tunai</th>
                    <th class="not-exported">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($closeCashiers as $key => $closeCashier)
                <tr data-id="{{$closeCashier->id}}">
                    <td>{{$key}}</td>
                    <td><b>Buka:</b> {{ $closeCashier->open_time }} <br></td>
                    <td>{{ $closeCashier->created_at}}</td>
                    <td>{{ $closeCashier->created_at}}</td>
                    <td>
                        <div class="row">
                        <a href="{{route('stock-opname.show', $closeCashier->id)}}" class="btn btn-link"><i class="dripicons-italic"></i> Detail</a>
                        {{-- <button type="button" class="btn btn-link" data-toggle="modal" data-target="#editModal-{{$closeCashier->id}}"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</button> --}}
                        {{-- {{ Form::open(['route' => ['stock-opname.destroy', $closeCashier->id], 'method' => 'DELETE'] ) }}
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button>
                                {{ Form::close() }} --}}
                            </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>


<!-- Stock Opname Modal -->
<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'stock-opname.store', 'method' => 'post']) !!}
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Stock Opname</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="name">Nama Stock Opname</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                        <div class="row">
                        <div class="form-group col-md-6">Bahan Baku</div>
                        <div class="form-group col-md-6">Kuantitas</div>
                    </div>
                    <hr>
                    @foreach($ingredients as $ingredient)
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{$ingredient->name}}
                            <input type="hidden" name="ingredient_id[]" value="{{$ingredient->id}}">
                        </div>
                        <div class="form-group col-md-6">
                            <input type="number" class="form-control" name="qty[]" value="{{$ingredient->last_stock}}">
                        </div>
                    </div>
                    @endforeach

                    <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
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
