@extends('backend.layout.main')
@section('content')

<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Laporan Selisih Stok</h3>
                        <h4 class="text-center mt-3">Tanggal: {{ \Carbon\Carbon::parse($start_date)->translatedFormat('j M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->translatedFormat('j M Y') }}</h4>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => 'report.differenceStockReport', 'method' => 'get']) !!}
                            <div class="form-group">
                                <label for=""><strong>Pilih Tanggal</strong></label>
                                <div class="input-group">
                                    <input type="text" name="start_date" class="form-control date" required value="{{ $start_date }}">
                                    <input type="text" name="end_date" class="form-control date" required value="{{ $end_date }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label><strong>Pilih Shift</strong></label>
                                <div class="input-group">
                                    <select id="shift-select" name="shift" class="form-control selectpicker" data-live-search="true" data-live-search-style="begins"
                                    title="Pilih shift">
                                        <option value="all" {{request()->shift == 'all' ? 'selected' : '' }}>Semua</option>
                                        <option value="1" {{$shift[0] == 1 && count($shift) == 1 ? 'selected' : ''}}>1</option>
                                        <option value="2" {{$shift[0] == 2 && count($shift) == 1 ? 'selected' : ''}}>2</option>
                                        <option value="3" {{$shift[0] == 3 && count($shift) == 1 ? 'selected' : ''}}>3</option>
                                    </select>
                                </div>
                            </div>

                            @if(auth()->user()->hasRole('Admin Bisnis'))
                                <div class="form-group">
                                    <label><strong>Pilih Outlet</strong></label>
                                    <select id="warehouse-select" name="warehouse_id" class="form-control selectpicker" data-live-search="true" data-live-search-style="begins"
                                    title="Pilih outlet">
                                        <option value="all" {{ $warehouse_request == 'all' ? 'selected' : ''}}>Semua Outlet</option>
                                        @foreach($warehouses as $warehouse)
                                        <option value="{{$warehouse->id}}" {{$warehouse->id == $warehouse_request ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ingredient-table" class="table">
                                <thead>
                                    <tr>
                                        <th>Outlet</th>
                                        <th>Bahan Baku</th>
                                        <th>Selisih</th>
                                        <th>Shift</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stocks as $stock)
                                        <tr>
                                            <td>{{ $stock->warehouse_name }}</td>
                                            <td>{{ $stock->ingredient_name }}</td>
                                            <td>{{ $stock->total_difference_stock }}</td>
                                            <td>{{ $stock->shift_number }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
$('.selectpicker').selectpicker();
$("ul#report").siblings('a').attr('aria-expanded','true');
$("ul#report").addClass("show");
$("ul#report #laporan-selisih").addClass("active");

$('.selectpicker').selectpicker('refresh');

$('#ingredient-table').DataTable( {
    "order": [],
    'language': {
        'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
        "info": '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
        "search": 'Cari',
        'paginate': {
                'previous': '<i class="dripicons-chevron-left"></i>',
                'next': '<i class="dripicons-chevron-right"></i>'
        }
    },
    'select': {
        style: 'multi',
        selector: 'td:first-child'
    },
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
</script>

<script>
    $(document).ready(function() {
        // Fungsi untuk memeriksa tanggal
        function validateDates() {
            var startDate = new Date($("input[name='start_date']").val());
            var endDate = new Date($("input[name='end_date']").val());

            // Jika tanggal mulai lebih besar dari tanggal selesai
            if (startDate > endDate) {
                alert("Tanggal Mulai tidak boleh lebih besar dari Tanggal Selesai.");
                $("input[name='start_date']").val(''); // Mengosongkan input tanggal mulai
                $("input[name='end_date']").val(''); // Mengosongkan input tanggal selesai
            }
        }

        // Event listener untuk perubahan pada input tanggal
        $("input[name='start_date'], input[name='end_date']").on("change", function() {
            validateDates();
        });
    });
    </script>
@endpush
