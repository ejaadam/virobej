@extends('mainLayout')
@section('main-title','Seller Login')
@section('head-style')
<link rel="stylesheet" href="{{asset('resources/assets/supplier/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/todc-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/font-awesome/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/style.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/theme/color_1.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/plugins/notifIt.css')}}">
<link rel="stylesheet" href="{{asset('http://fonts.googleapis.com/css?family=Roboto:300&amp;subset=latin,latin-ext')}}">
@stop
@section('head-script')
<!--[if lt IE 9]>
        <script src="js/ie/html5shiv.js"></script>
        <script src="js/ie/respond.min.js"></script>
        <![endif]-->
@stop
@section('body')
<body>
    <div id="loader-wrapper">
        <div id="loader"></div>
    </div>
    <!-- @include('seller.common.login-header')   -->
	<script src="{{asset('resources/assets/supplier/js/jquery.min.js')}}"></script>
	<script src="{{asset('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js')}}"></script>
	<script src="{{asset('resources/assets/plugins/notifIt.min.js')}}"></script>
	<script src="{{asset('resources/assets/plugins/jquery.md5.js')}}"></script>
	<script src="{{asset('js/providers/app.js')}}"></script>
    @yield('contents')
</body>
@stop
