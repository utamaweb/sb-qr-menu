@extends('backend.layout.main')
@section('content')
    @include('includes.alerts')

    <div class="row">
        <div class="container-fluid">
        <div class="col-md-12">
            <div class="brand-text float-left mt-4">
                <h3>{{trans('file.welcome')}} <span>{{Auth::user()->name}}</span></h3>
            </div>
            <div class="filter-toggle btn-group">
                <button style="cursor: auto;" class="btn btn-secondary date-btn" disabled data-start_date="{{date('Y').'-'.date('m').'-'.'01'}}" data-end_date="{{date('Y-m-d')}}">{{date('F')}} {{date('Y')}}</button>
            </div>
        </div>
        </div>
    </div>

    @if(auth()->user()->hasRole('Superadmin'))
        @include('backend.dashboard.superadmin')
    @elseif(auth()->user()->hasRole('Admin Bisnis'))
        @include('backend.dashboard.businessAdmin')
    @elseif(auth()->user()->hasRole('Admin Outlet'))
        @include('backend.dashboard.outletAdmin')
    @endif
@endsection

@push('scripts')
<script type="text/javascript">
$("#dashboard").addClass("active");
</script>
@endpush
