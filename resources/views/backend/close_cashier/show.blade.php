@extends('backend.layout.main')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <a href="{{route('close-cashier.index')}}" class="btn btn-info"><i class="dripicons-arrow-thin-left"></i> Kembali </a>
                    </div>
                    <div class="card-body">
                        <h4>Detail Tutup Kasir - {{$closeCashier->user->name}}</h4>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                Waktu Buka Kasir <br>
                                {{$closeCashier->open_time}}
                            </div>
                            <div class="col-md-6 text-right">
                                Waktu Tutup Kasir <br>
                                {{$closeCashier->close_time}}
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h2>Rp. {{$closeCashier->total_money}}</h2>
                                <p>Total Penerimaan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@endpush
