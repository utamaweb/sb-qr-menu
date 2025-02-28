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
                                <div class="count-number revenue-data">@currency($revenue)</div>
                                <a href="{{ url('admin/report/daily_sale/'.date('Y').'/'.date('m')) }}">
                                    <div class="name"><strong style="color: #733686">Pendapatan</strong></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Count item widget-->
                    <div class="col-sm-3">
                        <div class="wrapper count-title">
                            <div class="icon"><i class="dripicons-return" style="color: #ff8952"></i></div>
                            <div>
                                <div class="count-number return-data">@currency($expense)</div>
                                <a href="{{ route('pengeluaran.index') }}">
                                    <div class="name"><strong style="color: #ff8952">Pengeluaran</strong></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Count item widget-->
                    <div class="col-sm-3">
                        <div class="wrapper count-title">
                            <div class="icon"><i class="dripicons-media-loop" style="color: #00c689"></i></div>
                            <div>
                                <div class="count-number purchase_return-data">{{number_format((float)$purchase_return, 2, '.', '')}}</div>
                                <div class="name"><strong style="color: #00c689">Produk Terjual</strong></div>
                            </div>
                        </div>
                    </div>
                    <!-- Count item widget-->
                    <div class="col-sm-3">
                        <div class="wrapper count-title">
                            <div class="icon"><i class="dripicons-trophy" style="color: #297ff9"></i></div>
                            <div>
                                <div class="count-number profit-data">@currency($profit)</div>
                                <div class="name"><strong style="color: #297ff9">{{trans('file.profit')}}</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
