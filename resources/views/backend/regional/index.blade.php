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
            <span>Regional</span>
            {{-- @can('tambah-kategori') --}}
                <div>
                    <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-sm btn-info"><i class="dripicons-plus"></i> Tambah Regional</a>
                </div>
            {{-- @endcan --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="ingredient-table" class="table">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Nama Regional</th>
                            <th>Bisnis</th>
                            <th class="not-exported">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regionals as $key => $regional)
                        <tr data-id="{{$regional->id}}">
                            <td class="text-center">{{++$key}}</td>
                            <td>{{ $regional->name }}</td>
                            <td>{{ $regional->business->name }}</td>
                            <td>

                                 <button type="button" class="btn btn-link btn-edit" data-id="{{ $regional->id }}" data-name="{{ $regional->name }}">
                                    <i class="dripicons-document-edit"></i> Edit
                                </button>

                                {{ Form::open(['route' => ['regional.destroy', $regional->id], 'method' => 'DELETE'] ) }}
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> Hapus</button>
                                {{ Form::close() }}
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

<!-- Edit Modal -->
<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="editModalLabel" class="modal-title">Edit Regional</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                <form id="edit-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label>Nama Regional *</label>
                        <input type="text" id="edit-name" name="name" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Bisnis *</label>
                        <input type="text" readonly value="{{ auth()->user()->business->name }}" class="form-control">
                    </div>
                    <button type="button" id="update-btn" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(['route' => 'regional.store', 'method' => 'post']) !!}
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">Tambah Regional</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                <form>
                    <div class="form-group">
                        <label>Nama Regional *</label>
                        <input type="text" name="name" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Bisnis *</label>
                        <input type="text" readonly value="{{ auth()->user()->business->name }}" class="form-control">
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
    $("#kategori").addClass("active");


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


<script type="text/javascript">
    // Edit button click handler
    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');

        $('#edit-id').val(id);
        $('#edit-name').val(name);
        $('#editModal').modal('show');
    });

    // Update button click handler
    $('#update-btn').on('click', function() {
        let id = $('#edit-id').val();
        let name = $('#edit-name').val();
        let btn = $(this);

        if (!name) {
            alert('Nama Regional wajib diisi');
            return;
        }

        // Show loading state
        btn.html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        btn.prop('disabled', true);

        $.ajax({
            url: "{{ url('admin/regional') }}/" + id,
            method: 'PUT',
            data: {
                '_token': "{{ csrf_token() }}",
                'name': name
            },
            success: function(response) {
                if (response.success) {
                    $('#editModal').modal('hide');
                    alert('Regional berhasil diupdate');
                    location.reload();
                } else {
                    alert('Terjadi kesalahan: ' + response.message);
                    // Reset button state
                    btn.html('Update');
                    btn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = '';

                for (let field in errors) {
                    errorMessage += errors[field][0] + '\n';
                }

                alert('Terjadi kesalahan: ' + (errorMessage || xhr.responseJSON.message || 'Unknown error'));

                // Reset button state
                btn.html('Update');
                btn.prop('disabled', false);
            }
        });
    });


    // Create form submit handler
    $('#createModal form').on('submit', function() {
        let submitBtn = $(this).find('input[type="submit"]');

        // Show loading state
        submitBtn.val('Loading...');
        submitBtn.prop('disabled', true);

        // Form will submit normally
        return true;
    });
</script>
@endpush
