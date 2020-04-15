<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:400,500,700,400italic|Material+Icons">
    <title>{{ config('app.name', 'MicroPowerManager') }} @yield('page-title')</title>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">


    <style>

        .md-icon.md-theme-default.md-icon-image svg {
            fill: #ccc !important;
        }
    </style>
</head>
<!-- use material theme -->
<body>

<div id="app">

</div>

@include('skeletons.partials.scripts')
</body>

<script src="{{asset('js/app.js')}}"></script>
</html>
