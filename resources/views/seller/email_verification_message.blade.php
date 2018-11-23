@extends('mainLayout')
@section('main-title','Supplier Sign Up')
@section('head-style')
<link rel="stylesheet" href="{{asset('resources/assets/supplier/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/todc-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/style.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/theme/color_1.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/plugins/notifIt.css')}}">
<link rel="stylesheet" href="{{asset('http://fonts.googleapis.com/css?family=Roboto:300&amp;subset=latin,latin-ext')}}">
<style>
    body {padding:80px 0 0}
    textarea, input[type="password"], input[type="text"], input[type="submit"] {-webkit-appearance: none}
    .navbar-brand {font:300 15px/18px 'Roboto', sans-serif}
    .login_wrapper {position:relative;width:380px;margin:0 auto}
    .login_panel {background:#f8f8f8;padding:20px;-webkit-box-shadow: 0 0 0 4px #ededed;-moz-box-shadow: 0 0 0 4px #ededed;box-shadow: 0 0 0 4px #ededed;border:1px solid #ddd;position:relative}
    .login_head {margin-bottom:20px}
    .login_head h1 {margin:0;font:300 20px/24px 'Roboto', sans-serif}
    .login_submit {padding:10px 0}
    .login_panel label a {font-size:11px;margin-right:4px}
    @media (max-width: 767px) {
        body {padding-top:40px}
        .navbar {display:none}
        .login_wrapper {width:100%;padding:0 20px}
    }
</style>
@stop
@section('head-script')
<!--[if lt IE 9]>
        <script src="js/ie/html5shiv.js"></script>
        <script src="js/ie/respond.min.js"></script>
        <![endif]-->
@stop
@section('body')
<body>
    @include('seller.common.login-header')    
	<script src="{{asset('resources/assets/supplier/js/jquery.min.js')}}"></script>	
	<script src="{{asset('resources/assets/plugins/notifIt.min.js')}}"></script>
	<script src="{{asset('resources/supports/app.js')}}"></script>
	<div class="text-center">
		<img src="resources/assets/imgs/logo.png" alt="Telserra Shopping">
	</div>
    <div >
        <div class="text-center">
			
            <h1>{{$msg}}</h1>
            <a href="seller/login" class="btn btn-link">Log In</a>
        </div>
        
    </div>
   
</body>
@stop

