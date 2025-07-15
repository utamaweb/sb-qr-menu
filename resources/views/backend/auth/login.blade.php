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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preload" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <style>
        .password-toggle {
            cursor: pointer;
            border: none;
            background: transparent;
            color: #6c757d;
            height: 38px;
            display: flex;
            align-items: center;
            position: absolute;
            right: 10px;
            top: 0;
            z-index: 10;
        }
        .password-toggle:hover, .password-toggle:focus {
            color: #343a40;
            background: transparent;
            border: none;
            box-shadow: none;
        }
        .password-toggle i {
            font-size: 1rem;
        }
        .form-group-material {
            position: relative;
        }
    </style>
    <noscript>
        <link href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" rel="stylesheet"></noscript>
    <!-- Cloudflare Turnstile CAPTCHA -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
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
                            <button type="button" class="btn password-toggle" tabindex="-1">
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </button>
                            @if(session()->has('error'))
                            <p>
                                <strong>{{ session()->get('error') }}</strong>
                            </p>
                            @endif
                        </div>

                        @if(isset($appEnv) && $appEnv === 'production')
                        <div class="form-group-material mb-3">
                            <div class="cf-turnstile" data-sitekey="{{ $siteKey }}" data-theme="light"></div>
                            @if(session()->has('captcha_error'))
                            <p class="text-danger mt-2">
                                <strong>{{ session()->get('captcha_error') }}</strong>
                            </p>
                            @endif
                        </div>
                        @endif

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

    // Password toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const passwordToggleBtn = document.querySelector('.password-toggle');
        const passwordField = document.querySelector('#login-password');

        if (passwordToggleBtn && passwordField) {
            passwordToggleBtn.addEventListener('click', function() {
                // Toggle password visibility
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    passwordToggleBtn.querySelector('i').classList.remove('fa-eye-slash');
                    passwordToggleBtn.querySelector('i').classList.add('fa-eye');
                } else {
                    passwordField.type = 'password';
                    passwordToggleBtn.querySelector('i').classList.remove('fa-eye');
                    passwordToggleBtn.querySelector('i').classList.add('fa-eye-slash');
                }
            });
        }
    });
</script>
