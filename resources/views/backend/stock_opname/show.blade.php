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
                        <h4>Detail Stok Opname</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic">
                            {{-- <small>Label yang bertanda (*) wajib diisi.</small> --}}
                        </p>
                        <form action="{{route('stock-opname.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Nama Stok Opname</strong> </label>
                                        <input type="text" readonly name="name" class="form-control" id="name"
                                        aria-describedby="name" value="{{$stockOpname->name}}" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Cabang</label>
                                        <div class="input-group">
                                            <input type="text" readonly class="form-control" value="{{$stockOpname->warehouse->name}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes">Catatan</strong> </label>
                                        <input type="text" name="notes" disabled value="{{$stockOpname->notes}}" class="form-control" id="notes" aria-describedby="notes" required>
                                    </div>
                                </div>
                            </div>

                            @foreach($stockOpnameDetails as $stockOpnameDetail)
                            <div id="add_new" class="margin">
                                <div class="form-group row">
                                    <label for="example-text-input" class="col-md-2 col-form-label">Bahan Baku</label>

                                    <div class="col-md-6">
                                        {{-- <select name="ingredient_id[]" required class="form-control" disabled>
                                            <option value="">{{$stockOpnameDetail->ingredient->name}}</option>
                                        </select> --}}
                                        <input type="text" readonly value="{{$stockOpnameDetail->ingredient->name}}" class="form-control">
                                        <small class="text-danger">Bahan Baku</small>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="qty[]" class="form-control" placeholder="Stok Aktual" disabled value="{{$stockOpnameDetail->qty}}">
                                        <small class="text-danger">Qty</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach

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
<script>
    $("#stock-opname").addClass("active");
</script>
@endpush
