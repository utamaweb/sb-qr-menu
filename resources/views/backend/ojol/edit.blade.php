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
<section class="form">
    <div class="container-fluid">
        @if(session()->has('not_permitted'))
            <div class="alert alert-danger alert-dismissible text-center">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session()->get('not_permitted') }}</div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>Edit Ojol</h4>
                    </div>

                    <div class="card-body">
                        <p class="italic">
                            <small>Inputan yang ditandai dengan * wajib diisi.</small>
                        </p>
                        {{-- Create Ojol Form --}}
                        <form action="{{ route('ojol.update', $ojol->id) }}" method="POST">
                            @method('PUT')
                            @csrf
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name*</label>
                                        <input type="text" class="form-control" id="name" name="name" required value="{{ ($errors->has('name')) ? @old('name') : $ojol->name }}">
                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="extra_price">Tambahan Harga</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="text" name="extra_price" id="extra_price" class="form-control" oninput="changeValue(this)" value="{{ ($errors->has('extra_price')) ? @old('extra_price') : (($ojol->extra_price == NULL) ? '' : $ojol->extra_price) }}">
                                            @error('extra_price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="percent">Persen</label>
                                        <input type="number" min="0" max="100" name="percent" id="percent" class="form-control" value="{{ ($errors->has('percent')) ? @old('percent') : (($ojol->percent == NULL) ? '' : $ojol->percent) }}">
                                        @error('percent')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 d-flex justify-content-end pt-3">
                                    <div class="form-group">
                                        <a href="{{ route('ojol.index') }}" class="btn btn-outline-primary mr-2">Kembali</a>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                        {{-- End of Create Ojol Form --}}
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
    <script>
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

        $('document').ready(function() {
            // Set price input to use formatNumber
            $('input[name="extra_price"]').val(formatNumber($('input[name="extra_price"]').val()));
        })
    </script>
@endpush
