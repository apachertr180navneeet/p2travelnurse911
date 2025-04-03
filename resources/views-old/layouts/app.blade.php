<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <!-- Responsive -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <title>@if(isset($title)) {{ $title }} @endif @if(!isset($home)) | {{ config('app.name') }} @endif</title>
    <link rel="shortcut icon" href="{{ asset('public/assets/images/logo.jpeg') }}" />

    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/leaflet.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/responsive.css') }}" />

    <!--[if lt IE 9
      ]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script
    ><![endif]-->
    <!--[if lt IE 9]><script src="{{ asset('public/assets/js/respond.js') }}"></script><![endif]-->

    <script src="{{ asset('public/assets/js/jquery.js') }}"></script>
    <script type="text/javascript">
        var base_url = "{{ url('/') }}/";
    </script>


</head>

<body <?php if (isset($cur_page) && ($cur_page == 'homepage')) {
            echo 'data-anm=".anm"';
        } ?>>
    <div class="page-wrapper">
        @include('layouts.header')
        @yield('content')
        @include('layouts.footer')
    </div>


    <script src="{{ asset('public/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/chosen.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/jquery.fancybox.js') }}"></script>
    <script src="{{ asset('public/assets/js/jquery.modal.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/mmenu.polyfills.js') }}"></script>
    <script src="{{ asset('public/assets/js/mmenu.js') }}"></script>
    <script src="{{ asset('public/assets/js/appear.js') }}"></script>

    <script src="{{ asset('public/assets/js/anm.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/owl.js') }}"></script>
    <script src="{{ asset('public/assets/js/wow.js') }}"></script>
    <script src="{{ asset('public/assets/js/heatmap.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/leaflet.js') }}"></script>
    <script src="{{ asset('public/assets/js/leaflet-heatmap.js') }}"></script>

    <script src="{{ asset('public/assets/js/script.js') }}"></script>


</body>

</html>