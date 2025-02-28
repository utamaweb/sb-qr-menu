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


    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span>Kategori</span>
            @can('tambah-kategori')
                <div>
                    <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-sm btn-info"><i class="dripicons-plus"></i> Tambah Kategori</a>
                </div>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="ingredient-table" class="table">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Nama Kategori</th>
                            <th>Parent</th>
                            <th class="not-exported">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $key=>$category)
                        <tr data-id="{{$category->id}}">
                            <td class="text-center">{{++$key}}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->category_parent->name }}</td>
                            <td>
                                @can('ubah-kategori')
                                <button type="button" class="btn btn-link" data-toggle="modal" data-target="#editModal-{{$category->id}}"><i class="dripicons-document-edit"></i> Ubah</button>
                                {{-- Edit Modal --}}
                                <div id="editModal-{{$category->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                                    <div role="document" class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h5 id="exampleModalLabel" class="modal-title"> Ubah Kategori</h5>
                                        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                                        </div>
                                        <div class="modal-body">
                                        <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                                            <form action="{{route('kategori.update', $category->id)}}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group">
                                                <div class="form-group">
                                                    <label>Nama Kategori *</label>
                                                    <input type="text" value="{{$category->name}}" name="name" required class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label>Kategori Parent *</label>
                                                    <select name="category_parent_id" class="form-control">
                                                        @foreach($categoryParents as $parent)
                                                        <option value="{{$parent->id}}" {{$parent->id == $category->category_parent_id ? 'selected' : ''}}>{{$parent->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                </div>
                                                <input type="submit" value="Submit" class="btn btn-primary">
                                            </form>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                @endcan
                                @if($category->id != 7 && $category->id != 8 && $category->id != 9)
                                @can('hapus-kategori')
                                {{ Form::open(['route' => ['kategori.destroy', $category->id], 'method' => 'DELETE'] ) }}
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> Hapus</button>
                                {{ Form::close() }}
                                @endcan
                                @endif
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


<!-- Create Modal -->
<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'kategori.store', 'method' => 'post']) !!}
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Tambah Kategori</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                <form>
                    <div class="form-group">
                        <label>Nama Kategori *</label>
                        <input type="text" name="name" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Kategori Parent *</label>
                        <select name="category_parent_id" class="form-control">
                            @foreach($categoryParents as $parent)
                            <option value="{{$parent->id}}">{{$parent->name}}</option>
                            @endforeach
                        </select>
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
    // $("ul#product").siblings('a').attr('aria-expanded','true');
    // $("ul#product").addClass("show");
    // $("ul#product #category-menu").addClass("active");
    $("#kategori").addClass("active");
    var category_id = [];

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
    $(document).on('click', '.open-EditUnitDialog', function() {
        var url = "category/"
        var id = $(this).data('id').toString();
        url = url.concat(id).concat("/edit");

        $.get(url, function(data) {
            $("input[name='name']").val(data['name']);
            $("input[name='first_stock']").val(data['first_stock']);
            $("input[name='unit_id']").val(data['unit_id']);
            $("input[name='operation_value']").val(data['operation_value']);
            $("input[name='category_id']").val(data['id']);
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
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    } );
</script>
@endpush
