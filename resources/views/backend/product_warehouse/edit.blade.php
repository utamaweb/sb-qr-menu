@extends('backend.layout.main') @section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>Ubah Produk Outlet</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>
                        {!! Form::open(['route' => ['produk-outlet.update', $productWarehouse->id], 'method' => 'put', 'files' => true]) !!}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>Produk *</strong></label>
                                        <select id="product-select" name="product_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins"
                                            title="Pilih produk...">
                                            @foreach($products as $product)
                                            <option value="{{$product->id}}" data-price="{{$product->price}}" {{$product->id == $productWarehouse->product_id ? 'selected' : ''}}>{{$product->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Outlet *</strong></label>
                                        @if(auth()->user()->hasRole('Superadmin'))
                                        <select name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins"
                                            title="Pilih outlet...">
                                            @foreach($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}" {{$warehouse->id == $productWarehouse->warehouse_id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                        @else
                                        <input type="hidden" readonly name="warehouse_id" value="{{auth()->user()->warehouse_id}}" class="form-control">
                                        <input type="text" readonly name="warehouse_name" value="{{auth()->user()->warehouse->name}}" class="form-control">
                                        @endif
                                    </div>
                                    <div class="form-group mt-3">
                                        <label><strong>Harga *</strong></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="text" id="product-price" name="price" required class="form-control" value="{{$productWarehouse->price}}" oninput="changeValue(this)">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex justify-content-end">
                                    <div class="form-group mt-3 mr-2">
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">Kembali</a>
                                    </div>
                                    <div class="form-group mt-3">
                                        <input type="submit" value="{{trans('file.submit')}}" id="submit-btn" class="btn btn-primary">
                                    </div>
                                </div>
                            </div>
                        {!! Form::close() !!}
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

    $(document).ready(function() {
        // Set price input to use formatNumber
        var priceInput = $('input[name="price"]');
        priceInput.val(formatNumber(priceInput.val()));
    });

$('#product-select').change(function(){
        // Mengambil harga produk yang dipilih
        var selectedPrice = $(this).find(':selected').data('price');
        console.log(selectedPrice);
        // Setel nilai input text harga sesuai dengan harga produk yang dipilih
        $('#product-price').val(formatNumber(selectedPrice));
    });


$("ul#outlet").siblings('a').attr('aria-expanded','true');
    // $("ul#outlet").addClass("show");
    // $("ul#outlet #user-list-menu").addClass("active");
    $("#produk-outlet").addClass("active");

    $('.selectpicker').selectpicker('refresh');

</script>
@endpush
