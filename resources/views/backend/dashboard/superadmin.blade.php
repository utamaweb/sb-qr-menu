<section class="dashboard-counts">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 form-group">
                <div class="row">
                    <!-- Count item widget-->
                    <div class="col-sm-3">
                        <div class="wrapper count-title">
                            <div class="icon"><i class="dripicons-graph-bar" style="color: #733686"></i></div>
                            <div>
                                <div class="count-number revenue-data">{{$countBusiness}}</div>
                                <a href="{{ route('business.index') }}">
                                    <div class="name"><strong style="color: #733686">Total Bisnis</strong></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="wrapper count-title">
                            <div class="icon"><i class="dripicons-graph-bar" style="color: #365186"></i></div>
                            <div>
                                <div class="count-number revenue-data">{{$countWarehouse}}</div>
                                <a href="{{ route('outlet.index') }}">
                                    <div class="name"><strong style="color: #365186">Total Outlet</strong></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="wrapper count-title">
                            <div class="icon"><i class="dripicons-graph-bar" style="color: #e9801e"></i></div>
                            <div>
                                <div class="count-number revenue-data">{{$countAdminBisnis}}</div>
                                <a href="{{ route('user.index') }}">
                                    <div class="name"><strong style="color: #e9801e">Total Admin Bisnis</strong></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="wrapper count-title">
                            <div class="icon"><i class="dripicons-graph-bar" style="color: #62e91e"></i></div>
                            <div>
                                <div class="count-number revenue-data">{{$countAdminOutlet}}</div>
                                <a href="{{ route('user.index') }}">
                                    <div class="name"><strong style="color: #62e91e">Total Admin Outlet</strong></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
