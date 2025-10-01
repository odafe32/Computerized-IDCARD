<!DOCTYPE html>
<html lang="en" data-bs-theme="light" data-menu-color="brand" data-topbar-color="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $meta_title }}</title>

    <!-- Updated Meta Description -->
    <meta name="description" content="A Laravel-based web application that automates the process of student ID card requests and issuance. Students can submit their details, upload photos, and track request statuses online, while administrators can review applications, approve or reject requests, generate ID cards with QR codes, and manage records efficiently.">

    <meta name="author" content="A Laravel-based web application that automates the process of student ID card requests and issuance. Students can submit their details, upload photos, and track request statuses online, while administrators can review applications, approve or reject requests, generate ID cards with QR codes, and manage records efficiently.." />
    <meta content="A Laravel-based web application that automates the process of student ID card requests and issuance. Students can submit their details, upload photos, and track request statuses online, while administrators can review applications, approve or reject requests, generate ID cards with QR codes, and manage records efficiently." name="description" />
    <meta content="{{ $meta_title }}" property="og:title" />
    <meta content="A Laravel-based web application that automates the process of student ID card requests and issuance. Students can submit their details, upload photos, and track request statuses online, while administrators can review applications, approve or reject requests, generate ID cards with QR codes, and manage records efficiently.. />
    <meta content="{{ $meta_title }}" property="twitter:title" />
    <meta content="A Laravel-based web application that automates the process of student ID card requests and issuance. Students can submit their details, upload photos, and track request statuses online, while administrators can review applications, approve or reject requests, generate ID cards with QR codes, and manage records efficiently.." />
    <meta content="{{ $meta_image }}" property="og:image" />
    <meta content="{{ $meta_image }}" property="twitter:image" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta property="og:type" content="website" />
    <meta content="summary_large_image" name="twitter:card" />
    <meta content="Teranium Co" name="generator" />

    <!-- favicon -->
    <link rel="shortcut icon" href="{{ url('favicon.ico') }}" />

        <link href="{{ url('assets/css/bootstrap.min.css?v=' .env('CACHE_VERSION')) }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ url('assets/css/icons.min.css?v=' .env('CACHE_VERSION')) }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ url('assets/css/app.min.css?v=' .env('CACHE_VERSION')) }}" id="app-style" rel="stylesheet" type="text/css" />



</head>

<body class="authentication-bg">
    {{ csrf_field() }}
      <div class="account-pages my-5 pt-sm-5">

            @yield('content')
     </div>



  <!-- JAVASCRIPT -->
        <script src="{{ url('assets/libs/jquery/jquery.min.js?v=' .env('CACHE_VERSION')) }}"></script>
        <script src="{{ url('assets/libs/bootstrap/js/bootstrap.bundle.min.js?v=' .env('CACHE_VERSION')) }}"></script>
        <script src="{{ url('assets/libs/metismenu/metisMenu.min.js?v=' .env('CACHE_VERSION')) }}"></script>
        <script src="{{ url('assets/libs/simplebar/simplebar.min.js?v=' .env('CACHE_VERSION')) }}"></script>
        <script src="{{ url('assets/libs/node-waves/waves.min.js?v=' .env('CACHE_VERSION')) }}"></script>
        <script src="{{ url('assets/libs/jquery-sparkline/jquery.sparkline.min.js?v=' .env('CACHE_VERSION')) }}"></script>
        <!-- App js -->
        <script src="{{ url('assets/js/app.js?v=' .env('CACHE_VERSION')) }}"></script>
</body>

</html>
