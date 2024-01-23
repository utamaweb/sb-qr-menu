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
                        <h4>Detail Pembelian Stok</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic">
                            {{-- <small>Label yang bertanda (*) wajib diisi.</small> --}}
                        </p>
                        <form action="{{route('stock-purchase.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Cabang *</strong> </label>
                                        <div class="input-group">
                                            <select name="warehouse_id" required class="form-control selectpicker" disabled id="warehouse_id">
                                                <option value="">{{$stockPurchase->warehouse->name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Tanggal *</strong> </label>
                                        <div class="input-group">
                                            <input type="text" readonly value="{{$dateNow}}" name="date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- @foreach($stockPurchaseDetails as $stockPurchaseDetail)
                            <div id="add_new" class="margin">
                                <div class="form-group row">
                                    <label for="example-text-input" class="col-md-1 col-form-label">Bahan Baku</label>

                                    <div class="col-md-2">
                                        <select name="ingredient_id[]" required class="form-control" disabled>
                                            <option value="">Pilih Bahan Baku</option>
                                            @foreach($ingredients as $ingredient)
                                            <option value="{{$ingredient->id}}" {{$stockPurchaseDetail->ingredient_id == $ingredient->id ? 'selected' :''}}>{{$ingredient->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" placeholder="Jumlah" name="qty[]" class="form-control" value="{{$stockPurchaseDetail->qty}}" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" placeholder="Harga Satuan" name="qty[]" class="form-control" value="{{$stockPurchaseDetail->subtotal / $stockPurchaseDetail->qty}}" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" placeholder="Subtotal" name="qty[]" class="form-control" value="{{$stockPurchaseDetail->subtotal}}" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" placeholder="Catatan" disabled value="{{$stockPurchaseDetail->notes}}" name="notes[]" class="form-control">
                                    </div>
                                </div>
                            </div>

                            @endforeach --}}
                            <div class="table-responsive">
                                <table id="ingredient-table" class="table">
                                    <thead>
                                        <tr>
                                            <th class="not-exported">No</th>
                                            <th>Bahan Baku</th>
                                            <th>Jumlah</th>
                                            <th>Harga Satuan</th>
                                            <th>Subtotal</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stockPurchaseDetails as $key=>$stockPurchase)
                                        <tr data-id="{{$stockPurchase->id}}">
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $stockPurchase->ingredient->name }}</td>
                                            <td>{{ $stockPurchase->qty}}</td>
                                            <td>{{$stockPurchase->subtotal / $stockPurchase->qty}}</td>
                                            <td>{{ $stockPurchase->subtotal}}</td>
                                            <td>{{ $stockPurchase->notes ?: '-'}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                                <div class="col-md-12 d-flex justify-content-end">
                                    <div class="form-group mt-3 mr-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">Kembali</a>
                                    </div>
                                </div>
                        </form>
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
