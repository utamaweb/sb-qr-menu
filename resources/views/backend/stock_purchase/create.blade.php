@extends('backend.layout.main')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('content')
<section class="forms">
    <div class="container-fluid">
        @if(session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert"
                aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>Tambah Stok</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic">
                            <small>Label yang bertanda (*) wajib diisi.</small>
                        </p>
                        <form action="{{route('pembelian-stok.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Cabang *</strong> </label>
                                        <div class="input-group">
                                            @if($roleName == "Superadmin")
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
                                            @else
                                            <input type="hidden" readonly name="warehouse_id" value="{{auth()->user()->warehouse->id}}" class="form-control">
                                            <input type="text" readonly name="warehouse_name" value="{{auth()->user()->warehouse->name}}"  class="form-control">
                                            @endif
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
                                    <label for="example-text-input" class="col-md-1 col-form-label">Bahan Baku</label>

                                    <div class="col-md-2">
                                        <select name="ingredient_id[]" required class="form-control">
                                            <option value="">Pilih Bahan Baku</option>
                                            @foreach($ingredients as $ingredient)
                                            <option value="{{$ingredient->id}}">{{$ingredient->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <input type="number" placeholder="Qty" name="qty[]" min="1" class="form-control quantity" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input required type="text" placeholder="Harga Satuan" name="price[]" class="form-control harga-satuan input-number" oninput="changeValue(this)">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" placeholder="Subtotal" readonly name="subtotal[]" class="form-control subtotal input-number">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" placeholder="Catatan" name="notes[]" class="form-control">
                                    </div>

                                    <div class="col-md-2">
                                        <a href="javascript:void(0);" class="addCF btn btn-warning add_more"><i class="fa fa-plus"></i></a>
                                        <strong><a href="javascript:void(0);" class="remCF btn btn-danger"><i class="fa fa-times"></i></a></strong>
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
    $("#tambah-stok").addClass("active");

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
        var maxAppend = 0;


        // $('.input-number').on('input', function() {
        //     var value = this.value;
        //     // Update the input field value with the formatted number
        //     this.value = formatNumber(this.value);
        // });

        $("#add_more").click(function() {
            if (maxAppend >= 9)
            {
                alert("Maksimal 10 Bahan Baku!");
            } else {
                var add_new = $('<div class="form-group row">\n\
                    <label for="example-text-input" class="col-md-1 col-form-label"></label>\n\
                    <div class="col-md-2">\n\
                                        <select name="ingredient_id[]" required class="form-control">\n\
                                            <option value="">Pilih Bahan Baku</option>\n\
                                            @foreach($ingredients as $ingredient)\n\
                                            <option value="{{$ingredient->id}}">{{$ingredient->name}}</option>\n\
                                            @endforeach\n\
                                        </select>\n\
                                    </div>\n\
                                    <div class="col-md-1">\n\
                                        <input required type="number" placeholder="Qty" name="qty[]" min="1" class="form-control quantity">\n\
                                    </div>\n\
                                    <div class="col-md-2">\n\
                                        <input required type="text" placeholder="Harga Satuan" name="price[]" class="form-control harga-satuan input-number" oninput="changeValue(this)">\n\
                                    </div>\n\
                                    <div class="col-md-2">\n\
                                        <input type="text" placeholder="Subtotal" readonly name="subtotal[]" class="form-control subtotal input-number">\n\
                                    </div>\n\
                                    <div class="col-md-2">\n\
                                        <input type="text" placeholder="Catatan" name="notes[]" class="form-control">\n\
                                    </div>\n\
                    <div class="col-md-2 hapus">\n\
                        <a href="javascript:void(0);" class="addCF btn btn-warning add_more"><i class="fa fa-plus"></i></a>\n\
                    <strong><a href="javascript:void(0);" class="remCF btn btn-danger"><i class="fa fa-times"></i></a></strong>  \n\
                    </div>');
                maxAppend++;
                $("#add_new").append(add_new);
            }
        });

        $("#add_new").on('click', '.remCF', function() {
            $(this).parent().parent().parent().remove();
        });

        function calculateSubtotal(row) {
        var hargaSatuan = parseFloat($(row).find('.harga-satuan').val().replace(/,/g, ""));
        var qty = $(row).find('.quantity').val();
        var subtotal = hargaSatuan * qty;
        $(row).find('.subtotal').val(formatNumber(subtotal)); // Assuming 2 decimal places
    }

    $('#add_new').on('click', '.add_more', function() {
        var add_new = $('<div class="form-group row">\n\
            <label for="example-text-input" class="col-md-1 col-form-label"></label>\n\
                    <div class="col-md-2">\n\
                                        <select name="ingredient_id[]" required class="form-control">\n\
                                            <option value="">Pilih Bahan Baku</option>\n\
                                            @foreach($ingredients as $ingredient)\n\
                                            <option value="{{$ingredient->id}}">{{$ingredient->name}}</option>\n\
                                            @endforeach\n\
                                        </select>\n\
                                    </div>\n\
                                    <div class="col-md-1">\n\
                                        <input type="number" placeholder="Qty" name="qty[]" min="1" class="form-control quantity">\n\
                                    </div>\n\
                                    <div class="col-md-2">\n\
                                        <input type="text" placeholder="Harga Satuan" name="price[]" class="form-control harga-satuan input-number" oninput="changeValue(this)">\n\
                                    </div>\n\
                                    <div class="col-md-2">\n\
                                        <input type="text" placeholder="Subtotal" readonly name="subtotal[]" class="form-control subtotal input-number">\n\
                                    </div>\n\
                                    <div class="col-md-2">\n\
                                        <input type="text" placeholder="Catatan" name="notes[]" class="form-control">\n\
                                    </div>\n\
                    <div class="col-md-2 hapus">\n\
                        <a href="javascript:void(0);" class="addCF btn btn-warning add_more"><i class="fa fa-plus"></i></a>\n\
                <strong><a href="javascript:void(0);" class="remCF btn btn-danger"><i class="fa fa-times"></i></a></strong>   \n\
                    </div>');
        maxAppend++;
        $(this).closest('.form-group').after(add_new);
    });

    // Event listener for changes in harga satuan or qty
    $('#add_new').on('input', '.harga-satuan, .quantity', function() {
        var row = $(this).closest('.form-group');
        calculateSubtotal(row);
    });
    });
</script>
@endpush
