@extends('backend.layout.main')
@section('content')

<section>
    <div class="container-fluid">
        @include('includes.alerts')

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Pengeluaran</span>
                @if(auth()->user()->hasRole('Kasir'))
                    <a href="#" data-toggle="modal" data-target="#createModal" class="btn btn-info"><i class="dripicons-plus"></i> Tambah Pengeluaran</a>
                @endif
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="expense-table" class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pengeluaran</th>
                                <th>Keterangan</th>
                                <th>Kuantitas</th>
                                <th>Total</th>
                                <th>Outlet</th>
                                <th>Dibuat | Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh DataTables secara otomatis -->
                            @forelse($expenses as $expense)
                                <tr>
                                    <td>{{ ($expenses->currentPage() - 1) * $expenses->perPage() + $loop->iteration }}</td>
                                    <td>{{ $expense->expenseCategory->name }}</td>
                                    <td>{{ $expense->note }}</td>
                                    <td>{{ $expense->qty }}</td>
                                    <td>{{ $expense->amount }}</td>
                                    <td>{{ $expense->warehouse->name }}</td>
                                    <td>{{ $expense->created_at }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">Data Tidak Ditemukan</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                    {{ $expenses->links() }}
                </div>

            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#ingredient-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('expenses.data') }}",
            columns: [
                { data: null, name: 'DT_RowIndex', orderable: false, searchable: false, render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }},
                { data: 'expense_category.name', name: 'expenseCategory.name' },
                { data: 'note', name: 'note' },
                { data: 'qty', name: 'qty' },
                { data: 'amount', name: 'amount', render: function(data, type, row) {
                    return 'Rp ' + Number(data).toLocaleString('id-ID');
                }},
                { data: 'warehouse.name', name: 'warehouse.name' },
                { data: 'created_at', name: 'created_at' },
            ],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data per halaman',
                info: 'Menampilkan _START_ hingga _END_ dari _TOTAL_ data',
                paginate: {
                    previous: '<i class="dripicons-chevron-left"></i>',
                    next: '<i class="dripicons-chevron-right"></i>'
                }
            },
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
