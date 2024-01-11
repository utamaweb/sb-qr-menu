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
                        <h4>Tambah Pembelian Stok</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic">
                            <small>Label yang bertanda (*) wajib diisi.</small>
                        </p>
                        <form action="{{route('stock-purchase.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Cabang *</strong> </label>
                                        <div class="input-group">
                                            <select name="warehouse_id" required class="form-control selectpicker" id="warehouse_id">
                                                <option value="">Pilih Cabang</option>
                                                @foreach($warehouses as $warehouse)
                                                @if($roleName == 'Superadmin')
                                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                                @else
                                                <option value="{{$warehouse->id}}" {{auth()->user()->warehouse_id == $warehouse->id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                                @endif
                                                @endforeach
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

                            <div id="add_new" class="margin">
                                <div class="form-group row">
                                    <label for="example-text-input" class="col-md-2 col-form-label">Bahan Baku</label>

                                    <div class="col-md-4">
                                        <select name="ingredient_id[]" required class="form-control">
                                            <option value="">Pilih Bahan Baku</option>
                                            @foreach($ingredients as $ingredient)
                                            <option value="{{$ingredient->id}}">{{$ingredient->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" placeholder="Jumlah" name="qty[]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" placeholder="Catatan" name="notes[]" class="form-control">
                                    </div>

                                    <div class="col-md-2">
                                        <a href="javascript:void(0);" class="addCF btn btn-warning" id="add_more"><i class="fa fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>

                                <div class="col-md-12 d-flex justify-content-end">
                                    <div class="form-group mt-3 mr-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">Kembali</a>
                                    </div>
                                    <div class="form-group mt-3">
                                        <input type="submit" value="{{trans('file.submit')}}" id=""
                                            class="btn btn-primary">
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
    $(document).ready(function() {
        var maxAppend = 0;
        $("#add_more").click(function() {
            if (maxAppend >= 9)
            {
                alert("Maksimal 10 Bahan Baku!");
            } else {
                var add_new = $('<div class="form-group row">\n\
                    <label for="example-text-input" class="col-md-2 col-form-label"></label>\n\
                    <div class="col-md-4">\n\
                                        <select name="ingredient_id[]" required class="form-control">\n\
                                            <option value="">Pilih Bahan Baku</option>\n\
                                            @foreach($ingredients as $ingredient)\n\
                                            <option value="{{$ingredient->id}}">{{$ingredient->name}}</option>\n\
                                            @endforeach\n\
                                        </select>\n\
                                    </div>\n\
                                    <div class="col-md-2">\n\
                                        <input type="number" placeholder="Jumlah" name="qty[]" class="form-control">\n\
                                    </div>\n\
                                    <div class="col-md-2">\n\
                                        <input type="text" placeholder="Catatan" name="notes[]" class="form-control">\n\
                                    </div>\n\
                    <div class="col-md-2 hapus">\n\
                    <strong><a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp; Hapus</a></strong>   \n\
                    </div>');
                maxAppend++;
                $("#add_new").append(add_new);
            }
        });

        $("#add_new").on('click', '.remCF', function() {
            $(this).parent().parent().parent().remove();
        });
    });
</script>
@endpush
