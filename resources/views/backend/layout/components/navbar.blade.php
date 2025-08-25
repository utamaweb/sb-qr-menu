<header class="container-fluid">
    <nav class="navbar">
        <a id="toggle-btn" href="#" class="menu-btn"><i class="fa fa-bars"> </i></a>
        <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
            <li class="nav-item"><a id="btnFullscreen" data-toggle="tooltip" title="Full Screen"><i class="dripicons-expand"></i></a></li>
            <li class="nav-item">
                <a rel="nofollow" data-toggle="tooltip" class="nav-link dropdown-item"><i class="dripicons-user"></i> <span>{{ucfirst(Auth::user()->name)}}</span> <i class="fa fa-angle-down"></i>
                </a>
                <ul class="right-sidebar">
                    <li>
                        <a href="{{route('user.profile', ['id' => Auth::id()])}}"><i class="dripicons-user"></i> Profile</a>
                    </li>
                    <li>
                        <a style="cursor: pointer;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="dripicons-power"></i> Logout</a>
                        <form id="logout-form" action="{{ route('admin.auth.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
