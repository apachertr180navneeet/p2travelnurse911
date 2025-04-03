<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MRC39ZV7');</script>
<!-- End Google Tag Manager -->

    <meta charset="utf-8" />
    <!-- Responsive -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="{{ $keywords ?? '-'}}" />
    <meta name="author" content="" />
    <title>{{ isset($title) ? $title : '' }}</title>
    <meta name="description" content="{{ $metadescription ?? '-'}}"/>
    <link rel="shortcut icon" href="{{ asset('public/assets/images/fav.png') }}" />

    <link rel="stylesheet" href="{{ asset('public/assets/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/leaflet.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/responsive.css') }}" />
    <link rel="canonical" href="{{ url()->current() }}" />

    <!--[if lt IE 9
      ]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script
    ><![endif]-->
    <!--[if lt IE 9]><script src="{{ asset('public/assets/js/respond.js') }}"></script><![endif]-->

    <script src="{{ asset('public/assets/js/jquery.js') }}"></script>
    <script type="text/javascript">
        var base_url = "{{ url('/') }}/";
    </script>

	<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Travel Nurse 911",
  "url": "https://travelnurse911.com/",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://travelnurse911.com/?s={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>

<meta name="msvalidate.01" content="7F18256324D04313061F22815AA9C925" />

</head>

<body <?php if (isset($cur_page) && ($cur_page == 'homepage')) {
            echo 'data-anm=".anm"';
        } ?>>
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MRC39ZV7"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
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

    <script>
    (function(e,t,o,n,p,r,i){e.visitorGlobalObjectAlias=n;e[e.visitorGlobalObjectAlias]=e[e.visitorGlobalObjectAlias]||function(){(e[e.visitorGlobalObjectAlias].q=e[e.visitorGlobalObjectAlias].q||[]).push(arguments)};e[e.visitorGlobalObjectAlias].l=(new Date).getTime();r=t.createElement("script");r.src=o;r.async=true;i=t.getElementsByTagName("script")[0];i.parentNode.insertBefore(r,i)})(window,document,"https://diffuser-cdn.app-us1.com/diffuser/diffuser.js","vgo");
    vgo('setAccount', '255273029');
    vgo('setTrackByDefault', true);
 
    vgo('process');
    $('img').on('contextmenu', function(event) {
        event.preventDefault();
    });
</script>

</body>

</html>