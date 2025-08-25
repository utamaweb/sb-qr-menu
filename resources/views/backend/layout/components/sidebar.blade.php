<nav class="side-navbar pl-0 px-2">
    <span class="brand-big">
    <a href="{{url('/')}}"><img src="{{ asset('logo/sb-logo.png') }}" width="50">
    </span>


    <ul id="side-main-menu" class="side-menu list-unstyled">
        {{-- Common Menu Item --}}
        <li id="dashboard">
            <a href="{{ route('admin.dashboard') }}">
                <i class="dripicons-meter"></i>
                <span>Beranda</span>
            </a>
        </li>

        {{-- Menu Superadmin --}}
        @if (auth()->user()->hasRole('Superadmin'))
            @include('backend.layout.components.partials.sidebar._superadmin')
        {{-- Menu Admin Bisnis --}}
        @elseif(auth()->user()->hasRole('Admin Bisnis'))
            @include('backend.layout.components.partials.sidebar._businessAdmin')
        {{-- Menu Admin Outlet --}}
        @elseif(auth()->user()->hasRole('Admin Outlet'))
            @include('backend.layout.components.partials.sidebar._outletAdmin')
        {{-- Menu Report --}}
        @elseif(auth()->user()->hasRole('Report'))
            @include('backend.layout.components.partials.sidebar._report')
        {{-- Menu Sales --}}
        @elseif(auth()->user()->hasRole('Sales'))
            @include('backend.layout.components.partials.sidebar._sales')
        @endif
    </ul>
</nav>
