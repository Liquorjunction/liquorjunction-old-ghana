<!DOCTYPE html>
<html>
<head>
    @include('dashboard.layouts.head')
</head>
<body>
<div class="app auth_app" id="app">
    @yield('content')
</div>
@include('dashboard.layouts.foot')
</body>
</html>
