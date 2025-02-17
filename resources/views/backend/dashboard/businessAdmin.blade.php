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
                                <div class="count-number revenue-data">{{$countProduct}}</div>
                                <a href="{{ route('produk.index') }}" class="">
                                    <div class="name"><strong style="color: #733686">Total Produk</strong></div>
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
                                <div class="count-number revenue-data">{{$countIngredient}}</div>
                                <a href="{{ route('bahan-baku.index') }}">
                                    <div class="name"><strong style="color: #e9801e">Total Bahan Baku</strong></div>
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
                    <div class="col-sm-3">
                        <div class="wrapper count-title h-100">
                            <div class="icon"><i class="dripicons-wallet" style="color: #e9b61e"></i></div>
                            <div>
                                <div class="count-number revenue-data">@currency($totalIncomeThisMonth)</div>
                                <a href="javascript:void(0)">
                                    <div class="name"><strong style="color: #e9b61e">Total Pendapatan Bulan Ini</strong></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="wrapper count-title h-100">
                            <div class="icon"><i class="dripicons-wallet" style="color: #e9b61e"></i></div>
                            <div>
                                <div class="count-number revenue-data">@currency($totalIncomePreviousMonth)</div>
                                <a href="javascript:void(0)">
                                    <div class="name"><strong style="color: #e9b61e">Total Pendapatan Bulan Lalu</strong></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="wrapper count-title h-100">
                            <div class="icon"><i class="dripicons-wallet" style="color: #7b0ab8"></i></div>
                            <div>
                                <div class="count-number revenue-data">{{$countTransactionThisMonth}}</div>
                                <a href="javascript:void(0)">
                                    <div class="name"><strong style="color: #7b0ab8">Jumlah Transaksi Bulan Ini</strong></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="wrapper count-title h-100">
                            <div class="icon"><i class="dripicons-wallet" style="color: #7b0ab8"></i></div>
                            <div>
                                <div class="count-number revenue-data">{{$countTransactionPreviousMonth}}</div>
                                <a href="javascript:void(0)">
                                    <div class="name"><strong style="color: #7b0ab8">Jumlah Transaksi Bulan Lalu</strong></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
