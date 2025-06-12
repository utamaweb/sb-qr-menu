@extends('backend.layout.main')

@section('content')
<section>
    <div class="container-fluid">

        @include('includes.alerts')

        {{-- Filter --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Laporan Omset Produk Per Bulan</h3>
            </div>

            <div class="card-body">
                <form action="" method="GET">
                    {{-- Month input --}}
                    <div class="form-group">
                        <label for="month-input" class="form-label">Bulan <span class="text-danger">*</span></label>
                        <input type="month" name="month" id="month-input" class="form-control" value="{{ request()->month ?? date('Y-m') }}" required>
                    </div>
                    {{-- End of month input --}}

                    {{-- Outlet input --}}
                    @if (auth()->user()->hasRole(['Admin Bisnis', 'Report']))
                        <div class="form-group">
                            <label for="outlet-input" class="form-label">Outlet <span class="text-danger">*</span></label>
                            <select name="outlet" id="outlet-input" class="form-control selectpicker" data-live-search="true" data-live-search-style="begins" title="Pilih Outlet" required>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" {{ $outlet->id == request()->outlet ? 'selected' : '' }}>{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="outlet" id="outlet-input" value="{{ auth()->user()->warehouse_id }}">
                    @endif
                    {{-- End of outlet input --}}

                    {{-- Submit button --}}
                    @if (!auth()->user()->hasRole('Admin Outlet'))
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    @endif
                    {{-- End of submit button --}}

                </form>
            </div>
        </div>
        {{-- End of filter --}}

        {{-- Content --}}
        @if (!empty($data))
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Produk</span>
                <button type="button" class="btn btn-sm btn-success" onclick="exportExcel()">Export Excel</button>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-hover" id="product-table">
                    <thead>
                        <th>#</th>
                        <th>Produk</th>
                        <th>Check</th>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td width="5%">{{ $loop->iteration }}</td>
                                <td>{{ $item->name }}</td>
                                <td><input type="checkbox" name="check[]" value="{{ $item->product_id }}"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        {{-- End of content --}}
    </div>
</section>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $("ul#report").siblings('a').attr('aria-expanded','true');
        $("ul#report").addClass("show");
        $("ul#report li#product-omzet-by-month").addClass("active");

        $('.selectpicker').selectpicker();
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
            $('#product-table').DataTable();
        });

        function exportExcel() {
            // Get product IDs
            var productIDs = [];
            let table = $('#product-table').DataTable();
            let document = table.$('input[name="check[]"]:checked').serializeArray();
            productIDs = document.map(function(item) {
                return item.value;
            });

            if(productIDs.length == 0) {
                alert('Tidak ada produk yang dipilih');
            } else {
                // Separate month and year
                var month = $('#month-input').val().split('-')[1];
                var year = $('#month-input').val().split('-')[0];

                let url = "{{  route('report.productsOmzetByMonthExcel') }}";
                url += '?month=' + month;
                url += '&year=' + year;
                url += '&outlet=' + $('#outlet-input').val();
                url += '&productIDs=' + productIDs.join(',');

                window.open(url);
            }
        }
    </script>
@endpush
