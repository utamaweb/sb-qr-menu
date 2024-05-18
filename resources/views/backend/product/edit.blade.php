@extends('backend.layout.main')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
img{
  max-width:180px;
}
.input_container {
  border: 1px solid #e5e5e5;
}

input[type=file]::file-selector-button {
  background-color: #fff;
  color: #000;
  border: 0px;
  border-right: 1px solid #e5e5e5;
  padding: 10px 15px;
  /* margin-right: 20px; */
  transition: .5s;
}

input[type=file]::file-selector-button:hover {
  background-color: #eee;
  border: 0px;
  border-right: 1px solid #e5e5e5;
}
/* #blah{
    margin-left: 100px;
} */
</style>
@endpush
@section('content')
<section class="forms">
    <div class="container-fluid">
        <form action="{{route('produk.update', $product->id)}}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>Gambar Produk</h4>
                        </div>
                        <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Gambar Produk</strong> </label> <i
                                                        class="dripicons-question" data-toggle="tooltip"
                                                        title="Upload gambar dengan format .jpeg, .jpg, .png, .gif."></i>
                                                    <div class="input_container">
                                                        <input type="file" class="form-control" name="image" accept=".jpg, .jpeg, .png" onchange="readURL(this);">
                                                    </div>
                                                    <span class="validation-msg" id="image-error"></span>
                                                    <div class="text-center">
                                                        <img id="blah" src="{{Storage::url('product_images/'.$product->image)}}"/>
                                                    </div>
                                                </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>Data Produk</h4>
                        </div>
                        <div class="card-body">
                            <p class="italic">
                                <small>Inputan yang ditandai dengan * wajib diisi.</small>
                            </p>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Nama Produk *</strong> </label>
                                                    <input type="text" name="name" value="{{$product->name}}" class="form-control" id="name" aria-describedby="name" required>
                                                    <span class="validation-msg" id="name-error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Kode Produk *</strong> </label>
                                                    <div class="input-group">
                                                        <input type="text" name="code" class="form-control" id="code"
                                                            aria-describedby="code" required value="{{$product->code}}">
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
                                                        <select name="category_id" required
                                                            class="selectpicker form-control" data-live-search="true"
                                                            data-live-search-style="begins" title="Select Category...">
                                                            @foreach($lims_category_list as $category)
                                                            <option value="{{$category->id}}" {{$product->category_id == $category->id ? 'selected' : ''}}>{{$category->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <span class="validation-msg"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Satuan Produk *</strong> </label>
                                                <div class="input-group">
                                                    <select required class="form-control selectpicker" name="unit_id">
                                                        <option value="" disabled selected>Pilih Unit...</option>
                                                        @foreach($lims_unit_list as $unit)
                                                        @if($unit->base_unit==null)
                                                        <option value="{{$unit->id}}" {{$product->unit_id == $unit->id ? 'selected' : ''}}>{{$unit->unit_name}}</option>
                                                        @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="validation-msg"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Harga Produk *</strong> </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp.</span>
                                                        <input type="text" name="price" required class="form-control" step="any" value="{{$product->price}}" oninput="changeValue(this)">
                                                    </div>
                                                    <span class="validation-msg"></span>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Detail Produk</label>
                                                    <input name="product_details" class="form-control" rows="3" value="{{$product->product_details}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Bahan Baku</label>
                                                <div class="search-box input-group mb-4">
                                                    {{-- <button class="btn btn-secondary"><i class="fa fa-barcode"></i></button> --}}
                                                    {{-- <input type="text" name="product_code_name" id="lims_productcodeSearch" placeholder="Pilih bahan baku..." class="form-control" /> --}}
                                                    <select class="form-control selectpicker" name="ingredients[]" multiple data-live-search="true" data-live-search-style="begins">
                                                        {{-- <option value="" disabled>Select Product Unit...</option> --}}
                                                        @foreach($ingredients as $ingredient)
                                                        @if($ingredient->base_unit==null)
                                                        <option value="{{$ingredient->id}}" @if(in_array($ingredient->id, $ingredientProducts)) selected @endif>{{$ingredient->name}}</option>
                                                        @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                    </div>
                                    </div>
                                    <div class="col-md-12 d-flex justify-content-end">
                                        <div class="form-group mt-3 mr-2">
                                            <a href="{{ route('produk.index') }}" class="btn btn-outline-primary">Kembali</a>
                                        </div>
                                        <div class="form-group mt-3">
                                            <input type="submit" value="Submit" id=""
                                                class="btn btn-primary">
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

    // Function to change input value to formattedNumber
    function changeValue(input) {
        var value = formatNumber(input.value);
        input.value = value;
    }

    // Function to format number into number format
    function formatNumber(number) {
        // Remove non-digit characters
        var numericValue = number.toString().replace(/\D/g, "");

        // Add thousand separators
        var formattedNumber = numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        return formattedNumber;
    }

    function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
$(document).ready(function() {

    // Set price input to use formatNumber
    var priceInput = $('input[name="price"]');
    priceInput.val(formatNumber(priceInput.val()));

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
    // $("ul#product").addClass("show");
    $("#produk").addClass("active");
    // $("ul#product #product-list-menu").addClass("active");
    var product_id = <?php echo json_encode($product->id) ?>;

    $('.selectpicker').selectpicker({
      style: 'btn-link',
    });


</script>
@endpush
