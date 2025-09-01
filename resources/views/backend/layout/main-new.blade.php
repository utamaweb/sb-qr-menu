<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('backend.layout.partials._head')
    @stack('css')
</head>


<body class="" onload="myFunction()">
    <div id="loader"></div>
    {{-- Sidebar --}}
    @include('backend.layout.components.sidebar')
    {{-- End of sidebar --}}

        <div class="page">
            {{-- Navbar --}}
            @include('backend.layout.components.navbar')
            {{-- End of navbar --}}


            <div style="display:none" id="content" class="animate-bottom">
                @include('includes.session_message')
                <section>
                    <div class="container-fluid">
                        @include('includes.alerts')

                        @yield('content')
                    </div>
                </section>
            </div>

            {{-- Footer --}}
            @include('backend.layout.components.footer')
            {{-- End of footer --}}
        </div>

    @include('backend.layout.partials._foot')
    @stack('scripts')
</body>
</html>
