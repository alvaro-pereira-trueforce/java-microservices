<html id="adminpage">
<head>
    <!-- Latest compiled and minified CSS -->
    <link href="{{ asset('custom_css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/bootstrap-social.css') }}" rel="stylesheet">
    <link href="{{ asset('custom_css/bootstrap-override.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body>
    <script src="{{ asset('custom_js/angularjs/angular.min.js') }}"></script>
    <script src="{{ asset('custom_js/angularjs/angular-route.min.js') }}"></script>
    <script src="{{ asset('custom_js/less.min.js') }}" ></script>
    <script src="{{ asset('modules/shared/windows_service.js') }}" ></script>
    @yield('content')
    @include('footer')
</body>
</html>