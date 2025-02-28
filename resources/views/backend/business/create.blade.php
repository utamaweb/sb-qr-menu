@extends('backend.layout.main')

@section('content')
    <section>
        <div class="container-fluid">

            @include('includes.alerts')

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Bisnis</span>
                    {{-- @can('tambah-bisnis') --}}
                    <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-sm btn-info"><i class="dripicons-plus"></i> Tambah Bisnis</a>
                    {{-- @endcan --}}
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="business-table" class="table">
                            <thead>
                                <th class="text-center">#</th>
                                <th>Nama Bisnis</th>
                                <th class="not-exported">Aksi</th>
                            </thead>
                            <tbody>
                                @foreach($business as $key=>$bisnis)
                                <tr data-id="{{$bisnis->id}}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $bisnis->name }}</td>
                                    <td style="display: inline-block">
                                        {{-- @can('ubah-bisnis') --}}
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#editModal-{{$bisnis->id}}"><i class="dripicons-document-edit"></i> Ubah</button>
                                        {{-- Edit Modal --}}
                                        <div id="editModal-{{$bisnis->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                                            <div role="document" class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 id="exampleModalLabel" class="modal-title">Ubah Bisnis</h5>
                                                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                                                </div>
                                                <div class="modal-body">
                                                <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                                                    <form action="{{route('business.update', $bisnis->id)}}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-group">
                                                        <div class="form-group">
                                                            <label>Nama Bisnis *</label>
                                                            <input type="text" value="{{$bisnis->name}}" name="name" required class="form-control">
                                                        </div>
                                                        </div>
                                                        <input type="submit" value="Submit" class="btn btn-primary">
                                                    </form>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                        {{-- @endcan --}}

                                        {{-- @can('hapus-bisnis') --}}
                                        {{ Form::open(['route' => ['business.destroy', $bisnis->id], 'method' => 'DELETE', 'class' => 'd-inline-block'] ) }}
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirmDelete()"><i class="dripicons-trash"></i> Hapus</button>
                                        {{ Form::close() }}
                                        {{-- @endcan --}}
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
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">Tambah Bisnis</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic"><small>Input yang memiliki tanda (<span class="text-danger">*</span>) wajib diisi</small></p>
                    {!! Form::open(['route' => 'business.store', 'method' => 'post']) !!}
                        <div class="form-group">
                            <label>Nama Bisnis <span class="text-danger">*</span></label>
                            <input type="text" name="name" required class="form-control">
                        </div>
                        <input type="submit" value="Submit" class="btn btn-primary">
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#business-table').DataTable({
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
        });
    });
</script>
@endpush
