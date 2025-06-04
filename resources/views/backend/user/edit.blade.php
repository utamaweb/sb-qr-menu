@extends('backend.layout.main')
@section('content')

@if(session()->has('not_permitted'))
    <div class="alert alert-danger alert-dismissible text-center">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session()->get('not_permitted') }}
    </div>
@endif

<section class="forms">
        <div class="container-fluid">
                <div class="row">
                        <div class="col-md-12">
                                <div class="card">
                                        <!-- Card Header -->
                                        <div class="card-header d-flex align-items-center">
                                                <h4>{{trans('file.Update User')}}</h4>
                                        </div>

                                        <!-- Card Body -->
                                        <div class="card-body">
                                                <p class="italic"><small>Inputan yang ditandai dengan * wajib diisi.</small></p>

                                                {!! Form::open(['route' => ['user.update', $user->id], 'method' => 'put', 'files' => true]) !!}
                                                <div class="row">
                                                        <!-- Left Column -->
                                                        <div class="col-md-6">
                                                                <!-- Basic Info -->
                                                                <div class="form-group">
                                                                        <label><strong>Nama *</strong></label>
                                                                        <input type="text" name="name" required class="form-control" value="{{$user->name}}">
                                                                        @if($errors->has('name'))
                                                                        <span><strong>{{ $errors->first('name') }}</strong></span>
                                                                        @endif
                                                                </div>

                                                                <div class="form-group">
                                                                        <label><strong>Username *</strong></label>
                                                                        <input type="text" name="username" required class="form-control" value="{{$user->username}}">
                                                                        @if($errors->has('username'))
                                                                        <span><strong>{{ $errors->first('username') }}</strong></span>
                                                                        @endif
                                                                </div>

                                                                <div class="form-group">
                                                                        <label><strong>Ganti Password (Kosongkan bila tidak)</strong></label>
                                                                        <div class="input-group">
                                                                                <input type="password" name="password" class="form-control">
                                                                        </div>
                                                                </div>

                                                                <div class="form-group mt-3">
                                                                        <label><strong>{{trans('file.Email')}} *</strong></label>
                                                                        <input type="email" name="email" placeholder="example@example.com" required class="form-control" value="{{$user->email}}">
                                                                        @if($errors->has('email'))
                                                                        <span><strong>{{ $errors->first('email') }}</strong></span>
                                                                        @endif
                                                                </div>

                                                                <div class="form-group">
                                                                        <input class="mt-2" type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                                                                        <label class="mt-2"><strong>{{trans('file.Active')}}</strong></label>
                                                                </div>
                                                        </div>

                                                        <!-- Right Column -->
                                                        <div class="col-md-6">
                                                                <!-- Role Selection -->
                                                                <div class="form-group">
                                                                        <label><strong>{{trans('file.Role')}} *</strong></label>
                                                                        <input type="hidden" name="role_id_hidden" value="{{$user->role_id}}">
                                                                        <select name="role_id" required class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Role...">
                                                                                @foreach($lims_role_list as $role)
                                                                                        <option value="{{$role->id}}" {{$role->id == $user->role_id ? 'selected' : ''}}>{{$role->name}}</option>
                                                                                @endforeach
                                                                        </select>
                                                                </div>

                                                                <!-- Business & Warehouse Selection Logic -->
                                                                @if(auth()->user()->hasRole('Superadmin'))
                                                                        <!-- Superadmin View -->
                                                                        <div class="form-group bisnis-select" @if($user->hasRole(['Superadmin'])) style="display:none;" @endif>
                                                                                <label><strong>Bisnis *</strong></label>
                                                                                <select name="business_id" class="selectpicker form-control" data-live-search="true"
                                                                                        data-live-search-style="begins" title="Pilih Bisnis...">
                                                                                        @foreach($business as $bisnis)
                                                                                        <option value="{{$bisnis->id}}"
                                                                                                @if(!$user->hasRole('Superadmin'))
                                                                                                        {{$user->business_id == $bisnis->id ? 'selected' : ''}}
                                                                                                @endif>
                                                                                                {{$bisnis->name}}
                                                                                        </option>
                                                                                        @endforeach
                                                                                </select>
                                                                        </div>

                                                                        <div class="form-group warehouse-select" @if($user->hasRole(['Admin Bisnis', 'Superadmin', 'Report'])) style="display:none;" @endif>
                                                                                <label><strong>Outlet *</strong></label>
                                                                                <select name="warehouse_id" class="selectpicker form-control" data-live-search="true"
                                                                                        data-live-search-style="begins" title="Pilih outlet...">
                                                                                        @foreach($lims_warehouse_list as $warehouse)
                                                                                        <option value="{{$warehouse->id}}"
                                                                                                @if(!$user->hasRole('Admin Bisnis'))
                                                                                                        {{$user->warehouse_id == $warehouse->id ? 'selected' : ''}}
                                                                                                @endif>
                                                                                                {{$warehouse->name}}
                                                                                        </option>
                                                                                        @endforeach
                                                                                </select>
                                                                        </div>
                                                                @elseif(auth()->user()->hasRole('Admin Bisnis'))
                                                                        <!-- Admin Bisnis View -->
                                                                        <div class="form-group bisnis-select" style="display: none;">
                                                                                <label><strong>Bisnis *</strong></label>
                                                                                <input type="text" readonly value="{{auth()->user()->business->name}}" name="business_name" class="form-control">
                                                                                <input type="hidden" value="{{auth()->user()->business_id}}" name="business_id" class="form-control">
                                                                        </div>

                                                                        <div class="form-group warehouse-select" @if($user->hasRole('Admin Bisnis') || $user->hasRole('Report')) style="display:none;" @endif>
                                                                                <label><strong>Outlet *</strong></label>
                                                                                <select name="warehouse_id" required class="selectpicker form-control" data-live-search="true"
                                                                                        data-live-search-style="begins" title="Pilih outlet...">
                                                                                        @foreach($lims_warehouse_list as $warehouse)
                                                                                        <option value="{{$warehouse->id}}"
                                                                                                @if(!$user->hasRole('Admin Bisnis'))
                                                                                                        {{$user->warehouse_id == $warehouse->id ? 'selected' : ''}}
                                                                                                @endif>
                                                                                                {{$warehouse->name}}
                                                                                        </option>
                                                                                        @endforeach
                                                                                </select>
                                                                        </div>
                                                                @else
                                                                        <!-- Other Roles View -->
                                                                        <div class="form-group warehouse-select">
                                                                                <label><strong>Outlet *</strong></label>
                                                                                <input type="text" name="warehouse_name" value="{{auth()->user()->warehouse->name}}" class="form-control" readonly>
                                                                                <input type="hidden" name="warehouse_id" value="{{auth()->user()->warehouse_id}}" class="form-control" readonly>
                                                                        </div>
                                                                @endif

                                                                <div class="form-group mt-3">
                                                                        <label><strong>Nomor HP *</strong></label>
                                                                        <input type="text" name="phone" required class="form-control" value="{{$user->phone}}">
                                                                </div>
                                                        </div>

                                                        <!-- Form Buttons -->
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
        // Handle role selection changes
        document.querySelector('select[name="role_id"]').addEventListener('change', function() {
                var selectedRole = this.value;

                // Show/hide appropriate fields based on role
                if (selectedRole == 1) { // Superadmin
                        document.querySelector('.bisnis-select').style.display = 'none';
                        document.querySelector('.warehouse-select').style.display = 'none';
                } else if (selectedRole == 2 || selectedRole == 6) { // Admin Bisnis or role 6
                        document.querySelector('.bisnis-select').style.display = 'block';
                        document.querySelector('.warehouse-select').style.display = 'none';
                } else { // Other roles
                        document.querySelector('.bisnis-select').style.display = 'none';
                        document.querySelector('.warehouse-select').style.display = 'block';
                }
        });

        // Menu highlighting
        $("ul#outlet").siblings('a').attr('aria-expanded','true');
        $("#user").addClass("active");
</script>
@endpush
