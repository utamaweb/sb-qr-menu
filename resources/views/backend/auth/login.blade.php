<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{$general_setting->site_title}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <link rel="manifest" href="{{url('manifest.json')}}">
    <link rel="icon" type="image/png" href="{{ Storage::url('images/logo/'. $general_setting->site_logo) }}" />
    <link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo asset('css/auth.css') ?>" id="theme-stylesheet" type="text/css">
    <link rel="preload" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" rel="stylesheet"></noscript>
</head>

<body>
    <div class="page login-page">
        <div class="container">
            <div class="form-outer text-center d-flex align-items-center">
                <div class="form-inner">
                    <div class="logo">
                        {{-- @if($general_setting->site_logo)
                        <img src="{{ Storage::url('images/logo/'. $general_setting->site_logo) }}" width="110">
                        @else
                        <span>{{$general_setting->site_title}}</span>
                        @endif --}}
                        <img src="{{ asset('logo/sb-logo.png') }}" width="110">
                    </div>
                    @if(session()->has('delete_message'))
                    <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                            data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>{{ session()->get('delete_message') }}</div>
                    @endif
                    <form method="POST" action="{{ route('admin.auth.login') }}" id="login-form">
                        @csrf
                        <div class="form-group-material">
                            <input id="login-username" autofocus type="text" name="username" required class="input-material" value="" placeholder="Username">
                            @if(session()->has('error'))
                            <p>
                                <strong>{{ session()->get('error') }}</strong>
                            </p>
                            @endif
                        </div>

                        <div class="form-group-material">
                            <input id="login-password" type="password" name="password" required class="input-material"
                                value="" placeholder="Password">
                            @if(session()->has('error'))
                            <p>
                                <strong>{{ session()->get('error') }}</strong>
                            </p>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
@if(!config('database.connections.saleprosaas_landlord'))
<script type="text/javascript" src="<?php echo asset('vendor/jquery/jquery.min.js') ?>"></script>
@else
<script type="text/javascript" src="<?php echo asset('../../vendor/jquery/jquery.min.js') ?>"></script>
@endif
<script>
    // ------------------------------------------------------- //
    // Material Inputs
    // ------------------------------------------------------ //

</script>
