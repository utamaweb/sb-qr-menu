@extends('backend.layout.main')
@section('content')
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Group Permission')}}</h4>
                    </div>
                    {!! Form::open(['route' => 'role.setPermission', 'method' => 'post']) !!}
                    <div class="card-body">
                    	<input type="hidden" name="role_id" value="{{$role->id}}" />
						<div class="table-responsive">
						    <table class="table table-bordered permission-table">
						        <thead>
						        <tr>
						            <th colspan="5" class="text-center">{{$role->name}} Hak Akses</th>
						        </tr>
						        <tr>
						            <th rowspan="2" class="text-center">Module Name</th>
						            <th colspan="4" class="text-center">
						            	<div class="checkbox">
						            		<input type="checkbox" id="select_all">
						            		<label for="select_all">Centang Semua</label>
						            	</div>
						            </th>
						        </tr>
						        <tr>
						            <th scope="col">Lihat</th>
                                    <th scope="col">Tambah</th>
                                    <th scope="col">Edit</th>
                                    <th scope="col">Hapus</th>
						        </tr>
						        </thead>

						        <tbody>
                                    @foreach($menus as $task)
                                        <tr>
                                            <td scope="row">{{ $task->description }}</td>
                                            @foreach ($task->permissions as $permission)
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="{{$permission->name}}" name="izin_akses[]" value="{{ $permission->name }}" {{ in_array($permission->name, $izin) ? 'checked' : '' }}>
                                                        <label for="{{ $permission->name }}"></label>
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
									@if ($errors->has('izin_akses'))
									<div class=" container-fluid alert alert-warning alert-dismissible fade show" role="alert">
										{{ $errors->first('izin_akses')}}
										<button type="button" class="close" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									@endif
						        </tbody>
						    </table>
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
</section>

@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        $("#checkAll").click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    });
</script>
<script type="text/javascript">

	$("#select_all").on( "change", function() {
	    if ($(this).is(':checked')) {
	        $("tbody input[type='checkbox']").prop('checked', true);
	    }
	    else {
	        $("tbody input[type='checkbox']").prop('checked', false);
	    }
	});
</script>
@endpush
