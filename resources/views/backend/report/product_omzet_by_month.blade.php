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
                    @if (auth()->user()->hasRole('Admin Bisnis'))
                        <div class="form-group">
                            <label for="outlet-input" class="form-label">Outlet <span class="text-danger">*</span></label>
                            <select name="outlet" id="outlet-input" class="form-control selectpicker" data-live-search="true" data-live-search-style="begins" title="Pilih Outlet" required>
                                @foreach ($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" {{ $outlet->id == request()->outlet ? 'selected' : '' }}>{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    {{-- End of outlet input --}}

                    {{-- Submit button --}}
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    {{-- End of submit button --}}

                </form>
            </div>
        </div>
        {{-- End of filter --}}
    </div>
</section>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('.selectpicker').selectpicker();
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
        });
    </script>
@endpush