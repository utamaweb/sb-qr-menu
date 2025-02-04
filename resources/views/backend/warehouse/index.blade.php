@extends('backend.layout.main')
@section('content')

    <section>
        <div class="container-fluid">

            @include('includes.alerts')

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Outlet</span>
                    @can('tambah-warehouse')
                        <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-sm btn-info"><i class="dripicons-plus"></i> Tambah Outlet</a>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="outlet-table" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Outlet</th>
                                    <th>Tipe</th>
                                    <th>Bisnis</th>
                                    <th>Alamat</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Tagihan</th>
                                    <th>Tanggal Expired</th>
                                    <th class="not-exported">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lims_warehouse_all as $key=>$warehouse)
                                <tr data-id="{{$warehouse->id}}">
                                    <td>{{++$key}}</td>
                                    <td>{{ $warehouse->name }}</td>
                                    <td>{{ ($warehouse->is_self_service == 0) ? 'Hanya Kasir' : 'Self Service' }}</td>
                                    <td>{{ $warehouse->business->name }}</td>
                                    <td>{{ $warehouse->address }}</td>
                                    <td>{{ date('d M Y', strtotime($warehouse->created_at)) }}</td>
                                    <td>Rp. {{ number_format($warehouse->tagihan, 0, ',', '.') }}</td>
                                    <td>{{ $warehouse->expired_at ? date('d M Y', strtotime($warehouse->expired_at)) : '-' }}</td>
                                    <td>
                                        @can('ubah-warehouse')
                                            <button type="button" class="btn btn-sm btn-link" onclick="editModal({{$warehouse->id}})"><i class="dripicons-document-edit"></i> Edit</button>
                                        @endcan
                                        @can('hapus-warehouse')
                                            {{ Form::open(['route' => ['outlet.destroy', $warehouse->id], 'method' => 'DELETE'] ) }}
                                                <button type="submit" class="btn btn-sm btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> Delete</button>
                                            {{ Form::close() }}
                                        @endcan
                                        @if (auth()->user()->hasRole('Superadmin'))
                                            <button type="button" class="btn btn-sm btn-link" onclick="renewalModal({{$warehouse->id}})"><i class="dripicons-clockwise"></i> Renewal</button>
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

    {{-- Create modal --}}
    @include('backend.warehouse.createModal')
    {{-- End of create modal --}}

    {{-- Edit Modal --}}
    @include('backend.warehouse.editModal')
    {{-- End of edit modal --}}

    {{-- Renewal modal --}}
    @include('backend.warehouse.renewalModal')
    {{-- End of renewal modal --}}
@endsection

@push('scripts')
<script>
    // Function to change input value to formattedNumber
    function changeValue(input) {
        var value = formatNumber(input.value);
        input.value = value;
    }

    // Function to format number into number format
    function formatNumber(number) {
        // Remove non-digit characters
        var numericValue = number.toString().replace(/\D/g, "");

        // Add thousand separators
        var formattedNumber = numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        return formattedNumber;
    }
</script>

<script type="text/javascript">
    $("#outlet").addClass("active");

    $('#outlet-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
            'info'      : '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            'search'    : 'Cari',
            'paginate'  : {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next'    : '<i class="dripicons-chevron-right"></i>'
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
    } );
</script>
@endpush
