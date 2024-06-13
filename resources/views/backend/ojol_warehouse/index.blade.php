@extends('backend.layout.main')

@section('content')
    <section>
        <div class="container-fluid">

            @if ($errors->has('name'))
                <div class="alert alert-danger alert-dismissible text-center">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}
                </div>
            @endif
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                        data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
            @endif
            @if (session()->has('not_permitted'))
                <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                        data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
            @endif

            {{-- @can('tambah-ojol') --}}
            {{-- <a href="{{ route('ojol.create') }}" class="btn btn-info"><i class="dripicons-plus"></i> Tambah Ojol Outlet</a> --}}
            {{-- @endcan --}}
        </div>

        <div class="table-responsive">
            <table id="ojol-table" class="table">
                <thead>
                    <th class="text-center">#</th>
                    <th>Nama</th>
                    <th>Persen</th>
                    <th>Harga Tambahan</th>
                    <th>Aksi</th>
                </thead>
                <tbody>
                    @foreach ($ojols as $key => $ojol)
                        <tr data-id="{{ $ojol->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ojol->name }}</td>
                            <td>{{ $ojol->percent }}%</td>
                            <td>Rp. {{ number_format($ojol->extra_price, 0, '', '.') }}</td>
                            <td>
                                {{-- @can('ubah-ojol') --}}
                                <a href="{{ route('ojol-warehouse.form', $ojol->id) }}" class="btn btn-link"><i
                                        class="dripicons-document-edit"></i> Edit</a>
                                {{-- @endcan --}}

                                {{-- @can('hapus-ojol') --}}
                                {{-- {{ Form::open(['route' => ['ojol-warehouse.destroy', $ojol->id], 'method' => 'DELETE']) }}
                                    <button type="submit" class="btn btn-link" onclick="return confirm('Hapus data ojol?')"><i class="dripicons-trash"></i> Hapus</button>
                                {{ Form::close() }} --}}
                                {{-- @endcan --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $('#ojol-table').DataTable({
            "order": [],
            'language': {
                'lengthMenu': '_MENU_ {{ trans('file.records per page') }}',
                "info": '<small>{{ trans('file.Showing') }} _START_ - _END_ (_TOTAL_)</small>',
                "search": 'Cari',
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
            'select': {
                style: 'multi',
                selector: 'td:first-child'
            },
            'lengthMenu': [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            dom: '<"row"lfB>rtip',
            buttons: [{
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
    </script>
@endpush
