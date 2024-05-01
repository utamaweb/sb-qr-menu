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
                        <div class="name"><strong style="color: #733686">Total Bisnis</strong></div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="wrapper count-title">
                    <div class="icon"><i class="dripicons-graph-bar" style="color: #365186"></i></div>
                    <div>
                        <div class="count-number revenue-data">{{$countWarehouse}}</div>
                        <div class="name"><strong style="color: #365186">Total Outlet</strong></div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="wrapper count-title">
                    <div class="icon"><i class="dripicons-graph-bar" style="color: #e9801e"></i></div>
                    <div>
                        <div class="count-number revenue-data">{{$countAdminBisnis}}</div>
                        <div class="name"><strong style="color: #e9801e">Total Admin Bisnis</strong></div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="wrapper count-title">
                    <div class="icon"><i class="dripicons-graph-bar" style="color: #62e91e"></i></div>
                    <div>
                        <div class="count-number revenue-data">{{$countAdminOutlet}}</div>
                        <div class="name"><strong style="color: #62e91e">Total Admin Outlet</strong></div>
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
                        <div class="name"><strong style="color: #733686">Total Produk</strong></div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="wrapper count-title">
                    <div class="icon"><i class="dripicons-graph-bar" style="color: #365186"></i></div>
                    <div>
                        <div class="count-number revenue-data">{{$countWarehouse}}</div>
                        <div class="name"><strong style="color: #365186">Total Outlet</strong></div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="wrapper count-title">
                    <div class="icon"><i class="dripicons-graph-bar" style="color: #e9801e"></i></div>
                    <div>
                        <div class="count-number revenue-data">{{$countIngredient}}</div>
                        <div class="name"><strong style="color: #e9801e">Total Bahan Baku</strong></div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="wrapper count-title">
                    <div class="icon"><i class="dripicons-graph-bar" style="color: #62e91e"></i></div>
                    <div>
                        <div class="count-number revenue-data">{{$countAdminOutlet}}</div>
                        <div class="name"><strong style="color: #62e91e">Total Admin Outlet</strong></div>
                    </div>
                  </div>
                </div>
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
                        <div class="name"><strong style="color: #733686">Pendapatan</strong></div>
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
                        <div class="name"><strong style="color: #ff8952">Pengeluaran</strong></div>
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
    $(document).ready(function(){
      $.ajax({
        url: '{{url("/yearly-best-selling-price")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var url = '{{url("/storage/images/product")}}';
            data.forEach(function(item){
              if(item.product_images)
                var images = item.product_images.split('|');
              else
                var images = ['zummXD2dvAtI.png'];
              $('#yearly-best-selling-price').find('tbody').append('<tr><td><img src="'+url+'/'+images[0]+'" height="25" width="30"> '+item.product_name+' ['+item.product_code+']</td><td>'+item.total_price+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/yearly-best-selling-qty")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var url = '{{url("/storage/images/product")}}';
            data.forEach(function(item){
              if(item.product_images)
                var images = item.product_images.split('|');
              else
                var images = ['zummXD2dvAtI.png'];
              $('#yearly-best-selling-qty').find('tbody').append('<tr><td><img src="'+url+'/'+images[0]+'" height="25" width="30"> '+item.product_name+' ['+item.product_code+']</td><td>'+item.sold_qty+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/monthly-best-selling-qty")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var url = '{{url("/storage/images/product")}}';
            data.forEach(function(item){
              if(item.product_images)
                var images = item.product_images.split('|');
              else
                var images = ['zummXD2dvAtI.png'];
              $('#monthly-best-selling-qty').find('tbody').append('<tr><td><img src="'+url+'/'+images[0]+'" height="25" width="30"> '+item.product_name+' ['+item.product_code+']</td><td>'+item.sold_qty+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/recent-sale")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            data.forEach(function(item){
              var sale_date = dateFormat(item.created_at.split('T')[0], '{{$general_setting->date_format}}')
              if(item.sale_status == 1){
                var status = '<div class="badge badge-success">{{trans("file.Completed")}}</div>';
              } else if(item.sale_status == 2) {
                var status = '<div class="badge badge-danger">{{trans("file.Pending")}}</div>';
              } else {
                var status = '<div class="badge badge-warning">{{trans("file.Draft")}}</div>';
              }
              $('#recent-sale').find('tbody').append('<tr><td>'+sale_date+'</td><td>'+item.reference_no+'</td><td>'+item.name+'</td><td>'+status+'</td><td>'+item.grand_total+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/recent-purchase")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            data.forEach(function(item){
              var payment_date = dateFormat(item.created_at.split('T')[0], '{{$general_setting->date_format}}')
              if(item.payment_status == 1){
                var status = '<div class="badge badge-success">{{trans("file.Completed")}}</div>';
              } else if(item.payment_status == 2) {
                var status = '<div class="badge badge-danger">{{trans("file.Pending")}}</div>';
              } else {
                var status = '<div class="badge badge-warning">{{trans("file.Draft")}}</div>';
              }
              $('#recent-purchase').find('tbody').append('<tr><td>'+payment_date+'</td><td>'+item.reference_no+'</td><td>'+item.name+'</td><td>'+status+'</td><td>'+item.grand_total+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/recent-quotation")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            data.forEach(function(item){
              var quotation_date = dateFormat(item.created_at.split('T')[0], '{{$general_setting->date_format}}')
              if(item.quotation_status == 1){
                var status = '<div class="badge badge-success">{{trans("file.Completed")}}</div>';
              } else if(item.quotation_status == 2) {
                var status = '<div class="badge badge-danger">{{trans("file.Pending")}}</div>';
              } else {
                var status = '<div class="badge badge-warning">{{trans("file.Draft")}}</div>';
              }
              $('#recent-quotation').find('tbody').append('<tr><td>'+quotation_date+'</td><td>'+item.reference_no+'</td><td>'+item.name+'</td><td>'+status+'</td><td>'+item.grand_total+'</td></tr>');
            })
        }
      });
    });

    $(document).ready(function(){
      $.ajax({
        url: '{{url("/recent-payment")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            data.forEach(function(item){
              var payment_date = dateFormat(item.created_at.split('T')[0], '{{$general_setting->date_format}}')
              $('#recent-payment').find('tbody').append('<tr><td>'+payment_date+'</td><td>'+item.payment_reference+'</td><td>'+item.amount+'</td><td>'+item.paying_method+'</td></tr>');
            })
        }
      });
    });

    function dateFormat(inputDate, format) {
        const date = new Date(inputDate);
        //extract the parts of the date
        const day = date.getDate();
        const month = date.getMonth() + 1;
        const year = date.getFullYear();
        //replace the month
        format = format.replace("m", month.toString().padStart(2,"0"));
        //replace the year
        format = format.replace("Y", year.toString());
        //replace the day
        format = format.replace("d", day.toString().padStart(2,"0"));
        return format;
    }


    $(document).ready(function(){
      $.ajax({
        url: '{{url("/")}}',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#userShowModal').modal('show');
            $('#user-id').text(data.id);
            $('#user-name').text(data.name);
            $('#user-email').text(data.email);
        }
      });
    })
    // Show and hide color-switcher
    $(".color-switcher .switcher-button").on('click', function() {
        $(".color-switcher").toggleClass("show-color-switcher", "hide-color-switcher", 300);
    });

    // Color Skins
    $('a.color').on('click', function() {
        /*var title = $(this).attr('title');
        $('#style-colors').attr('href', 'css/skin-' + title + '.css');
        return false;*/
        $.get('setting/general_setting/change-theme/' + $(this).data('color'), function(data) {
        });
        var style_link= $('#custom-style').attr('href').replace(/([^-]*)$/, $(this).data('color') );
        $('#custom-style').attr('href', style_link);
    });

    $(".date-btn").on("click", function() {
        $(".date-btn").removeClass("active");
        $(this).addClass("active");
        var start_date = $(this).data('start_date');
        var end_date = $(this).data('end_date');
        $.get('dashboard-filter/' + start_date + '/' + end_date, function(data) {
            //console.log(data);
            dashboardFilter(data);
        });
    });

    function dashboardFilter(data){
        $('.revenue-data').hide();
        $('.revenue-data').html(parseFloat(data[0]).toFixed({{$general_setting->decimal}}));
        $('.revenue-data').show(500);

        $('.return-data').hide();
        $('.return-data').html(parseFloat(data[1]).toFixed({{$general_setting->decimal}}));
        $('.return-data').show(500);

        $('.profit-data').hide();
        $('.profit-data').html(parseFloat(data[2]).toFixed({{$general_setting->decimal}}));
        $('.profit-data').show(500);

        $('.purchase_return-data').hide();
        $('.purchase_return-data').html(parseFloat(data[3]).toFixed({{$general_setting->decimal}}));
        $('.purchase_return-data').show(500);
    }
</script>
@endpush
