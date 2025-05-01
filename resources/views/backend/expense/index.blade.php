@extends('backend.layout.main')
@section('content')

<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Laporan Pengeluaran Outlet</h3>
                        <h4 class="text-center mt-3">Tanggal: {{ \Carbon\Carbon::parse($start_date)->translatedFormat('j M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->translatedFormat('j M Y') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="GET">
                            <div class="form-group">
                                <label for=""><strong>Pilih Tanggal</strong></label>
                                <div class="input-group">
                                    <input type="text" name="start_date" class="form-control date" required value="{{ $start_date }}">
                                    <span class="input-group-text">s/d</span>
                                    <input type="text" name="end_date" class="form-control date" required value="{{ $end_date }}">
                                </div>
                            </div>

                            @if(auth()->user()->hasRole('Admin Bisnis'))
                                <div class="form-group">
                                    <label><strong>Pilih Outlet</strong></label>
                                    <select id="warehouse-select" name="warehouse_id" class="form-control selectpicker" data-live-search="true" data-live-search-style="begins"
                                    title="Pilih outlet">
                                        @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ $warehouse->id == $warehouseId ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Pengeluaran Outlet: {{ $warehouse->name }}</span>
                        <a href="{{ route('expense.export', $warehouse->id) }}?start_date={{ $start_date }}&end_date={{ $end_date }}" class="btn btn-sm btn-success">Export Excel</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="ingredient-table" class="table">
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
                                    @forelse($expenses as $expense)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $expense->expenseCategory->name }}</td>
                                            <td>{{ $expense->note }}</td>
                                            <td>{{ $expense->qty }}</td>
                                            <td>@currency($expense->amount)</td>
                                            <td>{{ $expense->warehouse->name }}</td>
                                            <td>{{ $expense->created_at }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Data Tidak Ditemukan</td>
                                        </tr>
                                    @endforelse
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
