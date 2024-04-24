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
                                        <select name="product_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins"
                                            title="Pilih outlet...">
                                            @foreach($products as $product)
                                            <option value="{{$product->id}}" {{$product->id == $productWarehouse->product_id ? 'selected' : ''}}>{{$product->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Outlet *</strong></label>
                                        <select name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins"
                                            title="Pilih outlet...">
                                            @foreach($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}" {{$warehouse->id == $productWarehouse->warehouse_id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label><strong>Harga *</strong></label>
                                        <input type="number" name="price" required class="form-control" value="{{$productWarehouse->price}}">
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
$("ul#outlet").siblings('a').attr('aria-expanded','true');
    $("ul#outlet").addClass("show");
    $("ul#outlet #user-list-menu").addClass("active");

    $("ul#people").siblings('a').attr('aria-expanded','true');
    $("ul#people").addClass("show");
    $('#biller-id').hide();
    $('#warehouseId').hide();



    $('select[name=role_id]').val($("input[name='role_id_hidden']").val());
    if($('select[name=role_id]').val() > 2){
        $('#warehouseId').show();
        $('select[name=warehouse_id]').val($("input[name='warehouse_id_hidden']").val());
        $('#biller-id').show();
        $('select[name=biller_id]').val($("input[name='biller_id_hidden']").val());
    }
    $('.selectpicker').selectpicker('refresh');

    $('select[name="role_id"]').on('change', function() {
        if($(this).val() > 2){
            $('select[name="warehouse_id"]').prop('required',true);
            $('select[name="biller_id"]').prop('required',true);
            $('#biller-id').show();
            $('#warehouseId').show();
        }
        else{
            $('select[name="warehouse_id"]').prop('required',false);
            $('select[name="biller_id"]').prop('required',false);
            $('#biller-id').hide();
            $('#warehouseId').hide();
        }
    });

    $('#genbutton').on("click", function(){
      $.get('../genpass', function(data){
        $("input[name='password']").val(data);
      });
    });

</script>
@endpush
