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
                        <h4>Tambah User</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic">
                            <small>Inputan yang ditandai dengan * wajib diisi.</small>
                        </p>
                        {!! Form::open(['route' => 'user.store', 'method' => 'post', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>Nama *</strong> </label>
                                    <input type="text" name="name" required class="form-control">
                                    @if($errors->has('name'))
                                    <small>
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </small>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label><strong>Username *</strong> </label>
                                    <input type="text" name="username" required class="form-control">
                                    @if($errors->has('username'))
                                    <small>
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </small>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label><strong>{{trans('file.Password')}} *</strong> </label>
                                    <div class="input-group">
                                        <input type="password" name="password" required class="form-control">
                                        <div class="input-group-append">
                                            <button id="genbutton" type="button"
                                                class="btn btn-default">{{trans('file.Generate')}}</button>
                                        </div>
                                        @if($errors->has('password'))
                                        <small>
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label><strong>{{trans('file.Email')}} *</strong></label>
                                    <input type="email" name="email" placeholder="example@example.com" required
                                        class="form-control">
                                    @if($errors->has('email'))
                                    <small>
                                        <strong class="text-danger">{{ $errors->first('email') }}</strong>
                                    </small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><strong>{{trans('file.Role')}} *</strong></label>
                                    <select name="role_id" required class="selectpicker form-control role"
                                        data-live-search="true" data-live-search-style="begins" title="Pilih Role...">
                                        @foreach($lims_role_list as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if(auth()->user()->hasRole('Superadmin'))
                                <div class="form-group bisnis-select" style="display: none;">
                                    <label><strong>Bisnis *</strong></label>
                                    <select name="business_id" class="selectpicker form-control" data-live-search="true"
                                        data-live-search-style="begins" title="Pilih Bisnis...">
                                        @foreach($business as $bisnis)
                                        <option value="{{$bisnis->id}}">{{$bisnis->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group warehouse-select" style="display: none;">
                                    <label><strong>Outlet *</strong></label>
                                    <select name="warehouse_id" class="selectpicker form-control" data-live-search="true"
                                        data-live-search-style="begins" title="Pilih outlet...">
                                        @foreach($lims_warehouse_list as $warehouse)
                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @elseif(auth()->user()->hasRole('Admin Bisnis'))
                                <div class="form-group">
                                    <label><strong>Outlet *</strong></label>
                                    <select name="warehouse_id" required class="selectpicker form-control" data-live-search="true"
                                        data-live-search-style="begins" title="Pilih outlet...">
                                        @foreach($lims_warehouse_list as $warehouse)
                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="form-group">
                                    <label><strong>Nomor HP *</strong></label>
                                    <input type="text" name="phone_number" required class="form-control">
                                    @if($errors->has('phone_number'))
                                    <small>
                                        <strong>{{ $errors->first('phone_number') }}</strong>
                                    </small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 d-flex justify-content-end">
                                <div class="form-group mt-3 mr-2">
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-primary">Kembali</a>
                                </div>
                                <div class="form-group mt-3">
                                    <input type="submit" value="{{trans('file.submit')}}" id="submit-btn"
                                        class="btn btn-primary">
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
// Mendengarkan perubahan pada elemen role_id
document.querySelector('select[name="role_id"]').addEventListener('change', function() {
        var selectedRole = this.value;
        console.log(selectedRole);

        // Sembunyikan semua opsi
        // document.querySelectorAll('.form-group').forEach(function(group) {
        //     group.style.display = 'none';
        // });

        // Tampilkan opsi yang sesuai dengan peran yang dipilih
        if (selectedRole == 1) {
            // Tampilkan hanya opsi bisnis
            document.querySelector('.bisnis-select').style.display = 'none';
            document.querySelector('.warehouse-select').style.display = 'none';
        } else if (selectedRole == 2) {
            // Tampilkan hanya opsi outlet
            document.querySelector('.bisnis-select').style.display = 'block';
            document.querySelector('.warehouse-select').style.display = 'none';
        } else {
            // Tampilkan kedua opsi (bisnis dan outlet)
            document.querySelector('.bisnis-select').style.display = 'none';
            document.querySelector('.warehouse-select').style.display = 'block';
        }
    });

$("ul#outlet").siblings('a').attr('aria-expanded','true');
    $("ul#outlet").addClass("show");
    $("ul#outlet #user-list-menu").addClass("active");

    $('#warehouseId').hide();
    $('#biller-id').hide();
    $('.customer-section').hide();

    $('.selectpicker').selectpicker({
      style: 'btn-link',
    });

    @if(config('database.connections.saleprosaas_landlord'))
        numberOfUserAccount = <?php echo json_encode($numberOfUserAccount)?>;
        $.ajax({
            type: 'GET',
            async: false,
            url: '{{route("package.fetchData", $general_setting->package_id)}}',
            success: function(data) {
                if(data['number_of_user_account'] > 0 && data['number_of_user_account'] <= numberOfUserAccount) {
                    localStorage.setItem("message", "You don't have permission to create another user account as you already exceed the limit! Subscribe to another package if you wants more!");
                    location.href = "{{route('user.index')}}";
                }
            }
        });
    @endif

    $('#genbutton').on("click", function(){
      $.get('genpass', function(data){
        $("input[name='password']").val(data);
      });
    });

    $('select[name="role_id"]').on('change', function() {
        if($(this).val() == 5) {
            $('#biller-id').hide(300);
            $('#warehouseId').hide(300);
            $('.customer-section').show(300);
            $('.customer-input').prop('required',true);
            $('select[name="warehouse_id"]').prop('required',false);
            $('select[name="biller_id"]').prop('required',false);
        }
        else if($(this).val() > 2 && $(this).val() != 5) {
            $('select[name="warehouse_id"]').prop('required',true);
            $('select[name="biller_id"]').prop('required',true);
            $('#biller-id').show(300);
            $('#warehouseId').show(300);
            $('.customer-section').hide(300);
            $('.customer-input').prop('required',false);
        }
        else {
            $('select[name="warehouse_id"]').prop('required',false);
            $('select[name="biller_id"]').prop('required',false);
            $('#biller-id').hide(300);
            $('#warehouseId').hide(300);
            $('.customer-section').hide(300);
            $('.customer-input').prop('required',false);
        }
    });
</script>
@endpush
