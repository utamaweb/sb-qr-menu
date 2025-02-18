@extends('backend.layout.main')

@section('content')
    <section>
        <div class="container-fluid">
            @include('includes.alerts')

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span>Custom Message</span>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#createModal"><i class="dripicons-plus"></i> Tambah</button>
                </div>

                <div class="card-body">
                    <table class="table table-hover tabel-bordered" id="custom-message-table">
                        <thead>
                            <th>#</th>
                            <th>Key</th>
                            <th>Value</th>
                            <th class="not-exported">Action</th>
                        </thead>
                        <tbody>
                            @foreach ($custom_messages as $item)
                                <tr>
                                    <td style="width: 10%">{{ $loop->iteration }}</td>
                                    <td style="width: 30%">{{ $item->key }}</td>
                                    <td>{{ $item->value }}</td>
                                    <td style="width: 20%">
                                        <button type="button" class="btn btn-sm btn-success" onclick="edit('{{ $item->id }}')"><i class="dripicons-pencil"></i> Edit</button>
                                        <a href="#" class="btn btn-sm btn-danger" onclick="return confirmDelete()"><i class="dripicons-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- Create modal --}}
    @include('backend.custom_message.createModal')
    {{-- End of create modal --}}

    {{-- Edit modal --}}
    @include('backend.custom_message.editModal')
    {{-- End of edit modal --}}

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#custom-message-table').DataTable({
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
        });
    });
</script>

<script>
    function edit(id) {
        // Set form edit action
        $url = "{{ route('custom-message.update', '__id__') }}".replace('__id__', id);
        $('#edit-form').attr('action', $url);

        // Get custom message
        $.ajax({
            url: "{{ route('custom-message.edit', '__id__') }}".replace('__id__', id),
            method: 'GET',
            success: function(response) {
                // Set key and value
                $('#key-edit').val(response.key);
                $('#value-edit').val(response.value);

                $('#editModal').modal('show');
            },
            error: function(error) {
                console.error('Error fetching custom message:', error);
            }
        });
    }
</script>
@endpush
