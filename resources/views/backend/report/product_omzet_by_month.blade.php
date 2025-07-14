@extends('backend.layout.main')

@section('content')
<section>
    <div class="container-fluid">

        @include('includes.alerts')

        {{-- Filter --}}
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">Laporan Omset Produk</h3>
            </div>

            <div class="card-body">
                <form action="" method="GET">
                    {{-- Date Range input --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start-date-input" class="form-label">Dari Tanggal <span class="text-danger">*</span></label>
                                <input type="text" id="start-date-input" name="start_date" required class="form-control date" value="{{ $start_date }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end-date-input" class="form-label">Sampai Tanggal <span class="text-danger">*</span></label>
                                <input type="text" name="end_date" class="form-control date" id="end-date-input" required value="{{ $end_date }}">
                            </div>
                        </div>
                    </div>
                    {{-- End of date range input --}}

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
                // Get date range
                var startDate = $('#start-date-input').val();
                var endDate = $('#end-date-input').val();

                let url = "{{  route('report.productsOmzetByMonthExcel') }}";
                url += '?start_date=' + startDate;
                url += '&end_date=' + endDate;
                url += '&outlet=' + $('#outlet-input').val();
                url += '&productIDs=' + productIDs.join(',');

                window.open(url);
            }
        }
    </script>
@endpush
