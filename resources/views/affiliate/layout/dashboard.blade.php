<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <base href="{{url('/')}}/">
  <title>@yield('title') | {{$pagesettings->site_name}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="author" content={{$pagesettings->site_name}}">  
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="shortcut icon" href="{{asset('favicon.ico')}}">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/affiliate/css/bootstrap.min.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/affiliate/css/font-awesome.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/affiliate/css/ionicons.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/affiliate/dist/css/AdminLTE.css')}}">  
  <!-- AdminLTE Skins. Choose a skin from the css/skins
  folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/affiliate/dist/css/skins/_all-skins.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/affiliate/plugins/iCheck/flat/blue.css')}}">
  <!-- Morris chart -->
  <link rel="stylesheet" href="{{asset('resources/assets/themes/affiliate/plugins/morris/morris.css')}}">
  <!-- jQuery 2.2.3 -->
<script src="{{asset('resources/assets/themes/affiliate/plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="{{asset('resources/assets/themes/affiliate/js/html5shiv/3.7.3/html5shiv.min.js')}}"></script>
  <script src="{{asset('resources/assets/themes/affiliate/js/respond/1.4.2/respond.min.js')}}"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div id="loader-wrapper">
    <div id="loader"></div>  
</div>
<!-- ./wrapper -->
<div class="wrapper">
	@include('affiliate.common.header')
	@include('affiliate.common.left_nav')
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
	@yield('content')
	</div>
	<!-- Content Wrapper. Contains page content -->	
	@include('affiliate.common.footer')
</div>
<!-- ./wrapper -->

<!-- jQuery UI 1.11.4 -->
<script src="{{asset('resources/assets/themes/affiliate/plugins/jQueryUI/jquery-ui.js')}}"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>$.widget.bridge('uibutton', $.ui.button);</script>
<!-- Bootstrap 3.3.6 -->
<script src="{{asset('resources/assets/themes/affiliate/js/bootstrap.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{asset('resources/assets/themes/affiliate/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
<!-- Slimscroll -->
<script src="{{asset('resources/assets/themes/affiliate/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('resources/assets/themes/affiliate/plugins/fastclick/fastclick.js')}}"></script>
<script src="{{asset('resources/assets/themes/affiliate/plugins/validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('resources/assets/themes/affiliate/plugins/validation/additional-methods.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('resources/assets/themes/affiliate/dist/js/app.js')}}"></script>
<script src="{{asset('js/providers/affiliate/app.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('resources/assets/themes/affiliate/dist/js/demo.js')}}"></script>
@yield('scripts')
<script>$('body').toggleClass('loaded');</script>
</body>
</html>
