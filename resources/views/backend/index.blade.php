@extends('backend.layout.main')
@section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
      @php
        if($general_setting->theme == 'default.css') {
          $color = '#733686';
          $color_rgba = 'rgba(115, 54, 134, 0.8)';
        }
        elseif($general_setting->theme == 'green.css') {
            $color = '#2ecc71';
            $color_rgba = 'rgba(46, 204, 113, 0.8)';
        }
        elseif($general_setting->theme == 'blue.css') {
            $color = '#3498db';
            $color_rgba = 'rgba(52, 152, 219, 0.8)';
        }
        elseif($general_setting->theme == 'dark.css'){
            $color = '#34495e';
            $color_rgba = 'rgba(52, 73, 94, 0.8)';
        }
      @endphp
      <div class="row">
        <div class="container-fluid">
          <div class="col-md-12">
            <div class="brand-text float-left mt-4">
                <h3>{{trans('file.welcome')}} <span>{{Auth::user()->name}}</span></h3>
            </div>
            <div class="filter-toggle btn-group">
              {{-- <button class="btn btn-secondary date-btn" data-start_date="{{date('Y-m-d')}}" data-end_date="{{date('Y-m-d')}}">{{trans('file.Today')}}</button> --}}
              {{-- <button class="btn btn-secondary date-btn" data-start_date="{{date('Y-m-d', strtotime(' -7 day'))}}" data-end_date="{{date('Y-m-d')}}">{{trans('file.Last 7 Days')}}</button> --}}
              <button style="cursor: auto;" class="btn btn-secondary date-btn" disabled data-start_date="{{date('Y').'-'.date('m').'-'.'01'}}" data-end_date="{{date('Y-m-d')}}">{{date('F')}} {{date('Y')}}</button>
              {{-- <button class="btn btn-secondary date-btn" data-start_date="{{date('Y').'-01'.'-01'}}" data-end_date="{{date('Y').'-12'.'-31'}}">{{trans('file.This Year')}}</button> --}}
            </div>
          </div>
        </div>
      </div>
      @if(auth()->user()->hasRole('Superadmin'))
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
      @endif

      @if(auth()->user()->hasRole('Admin Bisnis'))
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
                {{-- <div class="col-md-12 mt-4">
                  <div class="card line-chart-example">
                    <div class="card-header d-flex align-items-center">
                      <h4>Pendapatan 6 Bulan Terakhir</h4>
                    </div>
                    <div class="card-body">
                      <canvas id="cashFlowAdminBusiness" data-color = "{{$color}}" data-color_rgba = "{{$color_rgba}}" data-recieved = "{{json_encode($payment_recieved)}}" data-month = "{{json_encode($month)}}" data-label1="Pendapatan"></canvas>
                    </div>
                  </div>
                </div> --}}
              </div>
            </div>
          </div>
        </div>
      </section>
      @endif
      @if(auth()->user()->hasRole('Admin Outlet'))
      <!-- Counts Section -->
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
                        {{-- <div class="count-number revenue-data">{{number_format((float)$revenue,$general_setting->decimal, '.', '')}}</div> --}}
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
                        {{-- <div class="count-number return-data">{{number_format((float)$return,$general_setting->decimal, '.', '')}}</div> --}}
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
                        <div class="count-number purchase_return-data">{{number_format((float)$purchase_return,$general_setting->decimal, '.', '')}}</div>
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
            <div class="col-md-7 mt-4">
              <div class="card line-chart-example">
                <div class="card-header d-flex align-items-center">
                  <h4>Arus Uang 6 Bulan Terakhir</h4>
                </div>
                <div class="card-body">
                  <canvas id="cashFlow" data-color = "{{$color}}" data-color_rgba = "{{$color_rgba}}" data-recieved = "{{json_encode($payment_recieved)}}" data-sent = "{{json_encode($payment_sent)}}" data-month = "{{json_encode($month)}}" data-label1="Pendapatan" data-label2="Pengeluaran"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-5 mt-4">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h4>{{date('F')}} {{date('Y')}}</h4>
                </div>
                <div class="pie-chart mb-2">
                    <canvas id="transactionChart" data-color = "{{$color}}" data-color_rgba = "{{$color_rgba}}" data-revenue={{$revenue}} data-expense={{$expense}} data-label2="Pendapatan" data-label3="Pengeluaran" width="100" height="95"> </canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      @endif


@endsection

@push('scripts')
<script type="text/javascript">
$("#dashboard").addClass("active");
</script>
@endpush
