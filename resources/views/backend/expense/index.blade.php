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

    @if(auth()->user()->hasRole('Kasir'))
        <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-info"><i class="dripicons-plus"></i> Tambah Pengeluaran</a>&nbsp;
    @endif
    </div>
    <div class="table-responsive">
        <table id="ingredient-table" class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pengeluaran</th>
                    <th>Kuantitas</th>
                    <th>Total</th>
                    <th>Cabang</th>
                    <th>Dibuat | Waktu</th>
                    @if(auth()->user()->hasRole('Kasir'))
                    <th class="not-exported">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $key=>$expense)
                <tr data-id="{{$expense->id}}">
                    <td>{{++$key}}</td>
                    <td>{{ $expense->expenseCategory->name }}</td>
                    <td>{{ $expense->qty }}</td>
                    <td>@currency($expense->amount)</td>
                    <td>{{ $expense->warehouse->name }}</td>
                    <td>{{ $expense->user->name }} | {{$expense->created_at}}</td>
                    @if(auth()->user()->hasRole('Kasir'))
                    <td>
                        @can('ubah-daftarpengeluaran')
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#editModal-{{$expense->id}}"><i class="dripicons-document-edit"></i> Uba</button>
                        {{-- Edit Modal --}}
                        <div id="editModal-{{$expense->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                            <div role="document" class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 id="exampleModalLabel" class="modal-title"> Ubah Pengeluaran</h5>
                                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                                </div>
                                <div class="modal-body">
                                <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                                    <form action="{{route('pengeluaran.update', $expense->id)}}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Nama Pengeluaran *</label>
                                                <select name="expense_category_id" class="selectpicker form-control" required
                                                    data-live-search="true" data-live-search-style="begins" title="Pilih Pengeluaran...">
                                                    @foreach($lims_expense_category_list as $expense_category)
                                                    <option value="{{$expense_category->id}}" {{$expense->expense_category_id == $expense_category->id ? 'selected' : ''}}>
                                                        {{$expense_category->name}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Kuantitas *</label>
                                                <input type="number" name="qty" required class="form-control" value="{{$expense->qty}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Total *</label>
                                                <input type="text" name="amount" required class="form-control" value="{{$expense->amount}}">
                                            </div>

                                            {{-- <div class="col-md-6 form-group">
                                                <label>Cabang *</label>
                                                <select name="warehouse_id" class="selectpicker form-control" required data-live-search="true"
                                                    data-live-search-style="begins" title="Pilih cabang...">
                                                    @foreach($lims_warehouse_list as $warehouse)
                                                    <option value="{{$warehouse->id}}" {{$expense->warehouse_id == $warehouse->id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div> --}}
                                            <div class="col-md-6 form-group">
                                                <label>Keterangan</label>
                                                <input type="text" name="note" class="form-control" value="{{$expense->note}}">
                                            </div>
                                        </div>

                                        {{-- {{Form::text('name',null,array('required' => 'required', 'class' => 'form-control'))}} --}}
                                        <input type="submit" value="Submit" class="btn btn-primary">
                                    </form>
                                </div>
                            </div>
                            </div>
                        </div>
                        @endcan

                        @can('hapus-daftarpengeluaran')
                        {{ Form::open(['route' => ['pengeluaran.destroy', $expense->id], 'method' => 'DELETE'] ) }}
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> Hapus</button>
                                {{ Form::close() }}
                        @endcan
                    </td>
                    @endif
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
            {!! Form::open(['route' => 'pengeluaran.store', 'method' => 'post']) !!}
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Tambah Pengeluaran</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Pengeluaran *</label>
                            <select name="expense_category_id" class="selectpicker form-control" required
                                data-live-search="true" data-live-search-style="begins" title="Pilih Pengeluaran...">
                                @foreach($lims_expense_category_list as $expense_category)
                                <option value="{{$expense_category->id}}">
                                    {{$expense_category->name}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kuantitas *</label>
                            <input type="number" name="qty" required class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Total *</label>
                            <input type="text" name="amount" required class="form-control">
                        </div>
                        {{-- <div class="col-md-6 form-group">
                            <label>Cabang *</label>
                            <select name="warehouse_id" class="selectpicker form-control" required data-live-search="true"
                                data-live-search-style="begins" title="Pilih cabang...">
                                @foreach($lims_warehouse_list as $warehouse)
                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="col-md-6 form-group">
                            <label>Keterangan</label>
                            <input type="text" name="note" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Tanggal</label>
                            <input type="text" name="created_at" readonly class="form-control" value="{{\Carbon\Carbon::now()->format('d-m-Y')}}">
                        </div>
                    </div>
                        <input type="submit" value="Submit" class="btn btn-primary">
        </div>
        {{ Form::close() }}
    </div>
</div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#expense").siblings('a').attr('aria-expanded','true');
    $("ul#expense").addClass("show");
    $("ul#expense #exp-list-menu").addClass("active");

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
