<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>@yield('title')</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/dashboard/images/fav-logo.png') }}">
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- End Google Font -->
    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/bootstrap.min.css') }}">
    <!-- End Bootstrap -->
    <!-- Xzoom -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/xzoom.css') }}">  
    <!-- Xzoom End -->
    <!-- Swiper Slider -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/swiper-bundle.min.css') }}">
    <!-- End Swiper Slider -->
    <!-- Menu -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/menu.css') }}">
    <!-- End Menu -->
    <!-- Style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/custom.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/style-french.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/responsive.css') }}">
    <!-- End Style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
    
    <script type="text/javascript">
  window._mfq = window._mfq || [];
  (function() {
    var mf = document.createElement("script");
    mf.type = "text/javascript"; mf.defer = true;
    mf.src = "//cdn.mouseflow.com/projects/a1f36707-b54f-44e6-974e-cd307ffbe8f8.js";
    document.getElementsByTagName("head")[0].appendChild(mf);
  })();
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-C2CNS2VSGQ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
 
  gtag('config', 'G-C2CNS2VSGQ');
</script>
</head>
<body id="appBody" class="@if(\Session::get('language')!=1){{'fr-FR'}}@endif">