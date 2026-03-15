<!doctype html>
<html class="no-js" lang="nl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{env('APP_NAME')}}</title>
    <meta name="robots" content="index, follow" />
    <meta name="description" content="Deburenkoken.nl - Vind en bestel huisgemaakte gerechten bij jou in de buurt, vers van de buren!">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Place favicon.png in the root directory -->
    <link rel="shortcut icon" href="{{asset('img/favicon.png')}}" type="image/x-icon" />
    <!-- FontAwesome (single load) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Font Icons css -->
    <link rel="stylesheet" href="{{asset('css/font-icons.css')}}">
    <!-- plugins css -->
    <link rel="stylesheet" href="{{asset('css/plugins.css')}}">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/custom.css')}}">
    <!-- Responsive css -->
    <link rel="stylesheet" href="{{asset('css/responsive.css')}}">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@3.4.0/minified/introjs.min.css">
    <script src="https://cdn.jsdelivr.net/npm/intro.js@3.4.0/minified/intro.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/js/shepherd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@10.0.1/dist/css/shepherd.css"/>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    
    @yield('style')
    @yield('page.style')
</head>

<body class="container">
    @yield('sidebar')
    <div class="body-wrapper">
        @include('layout.menu')
        @yield('menu')
        @if (session('message'))
            <div class="message" id="message">
                <div class="content">
                    {{session('message')}}
                </div>
            </div>
        @endif
        <div class="h-100-v">
            @yield('content')
        </div>

       <?php
        /*  @if(!str_contains(\Illuminate\Support\Facades\Route::currentRouteName(), 'dashboard'))
            @include('layout.footer')
            @endif
        */
       ?>
        @include('layout.footer')
    </div>
    @yield('dashboard.footer')
    @include('layout.scripts')
</body>
</html>
