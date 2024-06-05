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
        @if(session()->has('not_permitted'))
    <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert"
            aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
    @endif
    <form action="{{route('produk.store')}}" method="POST" enctype="multipart/form-data">
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
                                                    title="Upload gambar dengan format .jpeg, .jpg, .png"></i>
                                                <div class="input_container">
                                                    <input accept=".jpg, .jpeg, .png" type="file" class="form-control" name="image" onchange="readURL(this);">
                                                </div>
                                                <span class="validation-msg" id="image-error"></span>
                                                <div class="text-center">
                                                    <img id="blah" />
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
                                                <input type="text" name="name" class="form-control" id="name" aria-describedby="name" required value="{{old('name')}}">
                                                <span class="validation-msg" id="name-error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Kode Produk *</strong> </label>
                                                <div class="input-group">
                                                    <input type="text" name="code" class="form-control" id="code"
                                                        aria-describedby="code" required value="{{old('code')}}">
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
                                                        data-live-search-style="begins" title="---Pilih Kategori--- ">
                                                        @foreach($lims_category_list as $category)
                                                        <option value="{{$category->id}}" {{old('category_id') == $category->id ? 'selected' : ''}}>{{$category->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="validation-msg"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Satuan Produk *</strong> </label>
                                            <div class="input-group">
                                                <select name="unit_id" required
                                                        class="selectpicker form-control" data-live-search="true"
                                                        data-live-search-style="begins" title="---Pilih Unit--- ">
                                                    @foreach($lims_unit_list as $unit)
                                                    <option value="{{$unit->id}}">{{$unit->unit_name}}</option>
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
                                                    <input type="text" name="price" required class="form-control" step="any" value="{{old('price')}}" oninput="changeValue(this)">
                                                </div>
                                                <span class="validation-msg"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Detail Produk</label>
                                                <input name="product_details" class="form-control" rows="3" value="{{old('product_details')}}">
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4">
                                            <label>Bahan Baku</label> --}}
                                            {{-- <div class="search-box input-group mb-4"> --}}
                                                {{-- <button class="btn btn-secondary"><i class="fa fa-barcode"></i></button> --}}
                                                {{-- <input type="text" name="product_code_name" id="lims_productcodeSearch" placeholder="Pilih bahan baku..." class="form-control" /> --}}
                                                {{-- <select class="form-control selectpicker" name="ingredients[]" multiple data-live-search="true" data-live-search-style="begins"> --}}
                                                    {{-- <option value="" disabled>Select Product Unit...</option> --}}
                                                    {{-- @foreach($ingredients as $ingredient)
                                                    @if($ingredient->base_unit==null)
                                                    <option value="{{$ingredient->id}}">{{$ingredient->name}}</option>
                                                    @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> --}}

                                        {{-- Start of Ingredient Section --}}
                                        <div class="container" id="ingredients">
                                            <label for="">Bahan Baku</label>
                                            <div class="row">
                                                {{-- Start of Ingredient Input --}}
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <select class="form-control" name="ingredients[]" placeholder="Pilih Bahan Baku">
                                                            <option value="" disabled>---Pilih Bahan Baku---</option>
                                                            @foreach($ingredients as $ingredient)
                                                            <option value="{{$ingredient->id}}">{{$ingredient->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                {{-- End of Ingredient Input --}}

                                                {{-- Start of Qty Input --}}
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" name="qty[]" id="ingredientQty" autocomplete="off" required placeholder="Qty">
                                                    </div>
                                                </div>
                                                {{-- End of Qty Input --}}

                                                {{-- Start of Add or Delete Button --}}
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <div class="btn-group">
                                                            <a class="btn btn-warning addIng"><i class="fa fa-plus"></i></a>
                                                            <a class="btn btn-danger remIng"><i class="fa fa-times"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- End of Add or Delete Button --}}
                                            </div>
                                        </div>
                                        {{-- End of Ingredient Section --}}

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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Start of ingredients script
    let ingredient = 0;

    // Function to create new ingredient input
    $('#ingredients').on('click', '.addIng', function() {
        $("#ingredients").append(`
        <div class="row">
            {{-- Start of Ingredient Input --}}
            <div class="col-md-6">
                <div class="form-group">
                    <select class="form-control" name="ingredients[]" palceholder="Pilih Bahan Baku">
                        {{-- <option value="" disabled>Select Product Unit...</option> --}}
                        @foreach($ingredients as $ingredient)
                        @if($ingredient->base_unit==null)
                        <option value="{{$ingredient->id}}">{{$ingredient->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            {{-- End of Ingredient Input --}}

            {{-- Start of Qty Input --}}
            <div class="col-md-4">
                <div class="form-group">
                    <input type="number" class="form-control" name="qty[]" id="ingredientQty" autocomplete="off" required placeholder="Qty">
                </div>
            </div>
            {{-- End of Qty Input --}}

            {{-- Start of Add or Delete Button --}}
            <div class="col-md-2">
                <div class="form-group">
                    <div class="btn-group">
                        <a class="btn btn-warning addIng"><i class="fa fa-plus"></i></a>
                        <a class="btn btn-danger remIng"><i class="fa fa-times"></i></a>
                    </div>
                </div>
            </div>
            {{-- End of Add or Delete Button --}}
        </div>
        `);
        ingredient++;
    });

    $('#ingredients').on('click', '.remIng', function() {
        // if(ingredient != 1) {
            $(this).parent().parent().parent().parent().remove();
            ingredient--;
        // }
    });

    // End of ingredients script
</script>

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


$( '#multiple-select-field' ).select2( {
    theme: "bootstrap-5",
    width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
    placeholder: $( this ).data( 'placeholder' ),
    closeOnSelect: false,
} );
    // $("ul#product").siblings('a').attr('aria-expanded','true');
    // $("ul#product").addClass("show");
    // $("ul#product #product-list-menu").addClass("active");
    $("#produk").addClass("active");
    $('[data-toggle="tooltip"]').tooltip();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


</script>
@endpush
