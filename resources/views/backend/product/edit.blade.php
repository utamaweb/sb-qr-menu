@extends('backend.layout.main')

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>Edit Produk</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic">
                            <small>Inputan yang ditandai dengan * wajib diisi.</small>
                        </p>
                        <form id="product-form" action="{{route('produk.update', $lims_product_data->id)}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{$lims_product_data->id}}" />
                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nama Produk *</strong> </label>
                                                <input type="text" name="name" value="{{$lims_product_data->name}}"
                                                    required class="form-control">
                                                <span class="validation-msg" id="name-error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Kode Produk *</strong> </label>
                                                <div class="input-group">
                                                    <input type="text" name="code" id="code"
                                                        value="{{$lims_product_data->code}}" class="form-control"
                                                        required>
                                                    <div class="input-group-append">
                                                        <button id="genbutton" type="button"
                                                            class="btn btn-sm btn-default"
                                                            title="{{trans('file.Generate')}}"><i
                                                                class="fa fa-refresh"></i></button>
                                                    </div>
                                                </div>
                                                <span class="validation-msg" id="code-error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Kategori *</strong> </label>
                                                <div class="input-group">
                                                    <select name="category_id" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins">
                                                        @foreach($lims_category_list as $category)
                                                        <option value="{{$category->id}}" {{$lims_product_data->category_id == $category->id ? 'selected' : ''}}>{{$category->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="validation-msg"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Satuan Produk *</strong> </label>
                                            <div class="input-group">
                                                <select required class="form-control selectpicker" name="unit_id" data-live-search="true" data-live-search-style="begins">
                                                    <option value="" disabled selected>Select Product Unit...</option>
                                                    @foreach($lims_unit_list as $unit)
                                                    @if($unit->base_unit==null)
                                                    <option value="{{$unit->id}}" {{$lims_product_data->unit_id == $unit->id ? 'selected' : ''}}>{{$unit->unit_name}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <span class="validation-msg"></span>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Gambar Produk</strong> </label> <i
                                                    class="dripicons-question" data-toggle="tooltip"
                                                    title="Upload gambar dengan format .jpeg, .jpg, .png, .gif."></i>
                                                {{-- <div id="imageUpload" class="dropzone"></div> --}}
                                                <input type="file" class="form-control" name="image">
                                                <p>Gambar Sebelumnya : <img width="20%" src="{{Storage::url('product_images/'.$lims_product_data->image)}}" alt=""></p>
                                                <span class="validation-msg" id="image-error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Harga Produk *</strong> </label>
                                                <input type="number" name="price" required class="form-control"
                                                    step="any" value="{{$lims_product_data->price}}">
                                                <span class="validation-msg"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div id="unit" class="col-md-12">
                                    <div class="row ">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Detail Produk</label>
                                                <input name="product_details" class="form-control"
                                                    rows="3" value="{{$lims_product_data->product_details}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Bahan Baku</label>
                                            <div class="search-box input-group mb-4">
                                                {{-- <button class="btn btn-secondary"><i class="fa fa-barcode"></i></button> --}}
                                                {{-- <input type="text" name="product_code_name" id="lims_productcodeSearch" placeholder="Pilih bahan baku..." class="form-control" /> --}}
                                                <select class="form-control selectpicker" name="ingredients[]" multiple data-live-search="true" data-live-search-style="begins">
                                                    {{-- <option value="" disabled>Select Product Unit...</option> --}}
                                                    @foreach($ingredients as $ingredient)
                                                    <option value="{{$ingredient->id}}" @if(in_array($ingredient->id, $ingredientProducts)) selected @endif>{{$ingredient->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex justify-content-end">
                                    <div class="form-group mt-3 mr-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">Kembali</a>
                                    </div>
                                    <div class="form-group mt-3">
                                        <button class="btn btn-primary" type="submit" id="">Submit</button>
                                    </div>
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
<script type="text/javascript">
$(document).ready(function() {
    $('#genbutton').click(function() {
        var randomCode = generateRandomCode(8); // Panggil fungsi untuk menghasilkan 8 angka acak
        $('#code').val(randomCode); // Set nilai input dengan angka acak yang dihasilkan
    });
});

// Fungsi untuk menghasilkan 8 angka acak
function generateRandomCode(length) {
    var result = '';
    var characters = '0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

$("ul#product").siblings('a').attr('aria-expanded','true');
    $("ul#product").addClass("show");
    $("ul#product #product-list-menu").addClass("active");
    var product_id = <?php echo json_encode($lims_product_data->id) ?>;

    $('.selectpicker').selectpicker({
      style: 'btn-link',
    });


</script>
@endpush
