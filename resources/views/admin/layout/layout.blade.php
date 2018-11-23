@extends('mainLayout')
@section('main-title')
@hasSection('title')
Admin | @yield('title')
@else
Admin
@endif
@stop
@section('head-style')
<!-- Bootstrap 3.3.6 -->
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/bootstrap.min.css')}}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/font-awesome.min.css')}}">
<!-- Theme style -->
<link rel="stylesheet" href="{{asset('resources/assets/admin/dist/css/AdminLTE.css')}}">
<!-- AdminLTE Skins. Choose a skin from the css/skins
folder instead of downloading all of them to reduce the load. -->
<link rel="stylesheet" href="{{asset('resources/assets/admin/dist/css/skins/_all-skins.min.css')}}">
<!-- iCheck -->
<link rel="stylesheet" href="{{asset('resources/assets/admin/plugins/iCheck/flat/blue.css')}}">
<!-- Morris chart -->
<link rel="stylesheet" href="{{asset('resources/assets/admin/plugins/morris/morris.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/plugins/notifIt.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/select2/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/plugins/datatable-responsive/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/plugins/datatable-responsive/css/responsive.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/plugins/datepicker/datepicker3.css')}}">

<div id="xbp-styles">
    @stack('styles')
</div>
@stop
@section('head-script')
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="{{asset('assets/admin/js/html5shiv/3.7.3/html5shiv.min.js')}}"></script>
<script src="{{asset('assets/admin/js/respond/1.4.2/respond.min.js')}}"></script>
<![endif]-->
@stop
@section('body')
<body class="hold-transition skin-blue sidebar-mini">
    <div id="loader-wrapper">
        <div id="loader"></div>
    </div>
    <!-- ./wrapper -->
    <div class="wrapper">
        @include('admin.common.header')
        @include('admin.common.left_nav')
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="content-header">
                <h1 class="xbp-icon-title"><i class="fa fa-@yield('title-icon','files-o')"></i>@yield('title')</h1>
                @hasSection('breadcrumb')
                <ol class="breadcrumb" id="xbp-breadcrumb">
                    <li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('general.dashboard')}}</a></li>
                    @yield('breadcrumb')
                    <li class="active xbp-icon-title"><i class="fa fa-@yield('title-icon','files-o')"></i>@yield('title')</li>
                </ol>
                @endif
            </section>
            <section class="content">
                <div class="row" id="xbp-content">
                    @yield('content')

                </div>
            </section>
        </div>
        <!-- Content Wrapper. Contains page content -->
        @include('admin.common.footer')
    </div>
    <!-- ./wrapper -->
    <!-- jQuery 2.2.3 -->
    <script src="{{asset('resources/assets/admin/plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
    jQuery UI 1.11.4
    <script src="{{asset('resources/assets/admin/plugins/jQueryUI/jquery-ui.js')}}"></script>
    Resolve conflict in jQuery UI tooltip with Bootstrap tooltip
    <script>$.widget.bridge('uibutton', $.ui.button);</script>
    <script src="{{asset('resources/assets/admin/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/notifIt.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/fastclick/fastclick.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/validation/additional-methods.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/providers/admin/pushNotifications.js')}}"></script>
    <script type="text/javascript" src="https://www.gstatic.com/firebasejs/4.5.0/firebase.js"></script>
    <script src="{{asset('resources/assets/admin/dist/js/app.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/Jquery-loadSelect.js')}}"></script>
	<script src="{{asset('resources/assets/admin/plugins/iCheck/icheck.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/dist/js/demo.js')}}"></script>
    <script src="{{asset('resources/assets/admin/js/fastselect.js')}}"></script>
    <script src="{{asset('resources/assets/admin/js/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('resources/assets/user/plugins/jQuery/jquery.form.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/datatable-responsive/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/datatable-responsive/js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/datatable-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/datatable-responsive/js/responsive.bootstrap.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    @section('head-style')
    <!--    <link rel="preload" href="{{asset('public/admin/all.css')}}" as="style" onload="this.rel = 'stylesheet'"/>-->
    <div id="xbp-styles">
        @stack('styles')
    </div>
    @stop
    @section('head-script')
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="{{asset('resources/assets/admin/js/html5shiv/3.7.3/html5shiv.min.js')}}"></script>
    <script src="{{asset('resources/assets/admin/js/respond/1.4.2/respond.min.js')}}"></script>
    <![endif]-->
    @stop
<!--    <script src="{{asset('public/admin/all.js')}}"></script>-->
    <script>$.widget.bridge('uibutton', $.ui.button);</script>
    <div id="xbp-scripts">
        @stack('scripts')
    </div>
    <script src="{{asset('resources/assets/app.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/providers/admin/home.js')}}"></script>
</body>
@stop
