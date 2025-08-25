<link rel="icon" type="image/png" href="{{ Storage::url('images/logo/'. $general_setting->site_logo) }}" />
<title>{{$general_setting->site_title}}</title>
<link rel="manifest" href="{{url('manifest.json')}}">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" type="text/css">
<link rel="preload" href="{{ asset('vendor/bootstrap-toggle/css/bootstrap-toggle.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{ asset('vendor/bootstrap/css/bootstrap-datepicker.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{ asset('vendor/jquery-timepicker/jquery.timepicker.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{ asset('vendor/bootstrap/css/awesome-bootstrap-checkbox.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{ asset('vendor/bootstrap/css/bootstrap-select.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{ asset('vendor/dripicons/webfont.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{ asset('css/grasp_mobile_progress_circle-1.0.0.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{ asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">

@if(Route::current()->getName() != '/')
<link rel="preload" href="{{ asset('vendor/daterange/css/daterangepicker.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{ asset('vendor/datatable/dataTables.bootstrap4.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="{{ asset('css/fixedHeader.bootstrap.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link href="{{ asset('css/fixedHeader.bootstrap.min.css') }}" rel="stylesheet"></noscript>
<link rel="preload" href="{{ asset('css/responsive.bootstrap.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
@endif

<link rel="stylesheet" href="{{ asset('css/style.default.css') }}" id="theme-stylesheet" type="text/css">
<link rel="stylesheet" href="{{ asset('css/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom-'.$general_setting->theme) }}" type="text/css" id="custom-style">

<!-- Google fonts - Nunito -->
<link rel="preload" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" as="style" onload="this.onload=null;this.rel='stylesheet'">
