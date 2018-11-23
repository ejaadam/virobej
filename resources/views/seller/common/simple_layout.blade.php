@extends('mainLayout')
@section('main-title',(trim($__env->yieldContent('title')))?'Seller | '.$__env->yieldContent('title'):'Seller')
@section('head-style')
<link rel="stylesheet" href="{{asset('resources/assets/supplier/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/todc-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/jquery.tagsinput.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/img/flags/flags.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/retina.css')}}">

<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/dataTables/media/DT_bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/dataTables/extras/TableTools/media/css/TableTools.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/bootstrap-switch/stylesheets/bootstrap-switch.css')}}">

<!--link rel="stylesheet" href="{{asset('resources/assets/theme/bracket/css/bootstrap-fileupload.min.css')}}"-->

<!--link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/bootstrap-switch/stylesheets/ebro_bootstrapSwitch.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/iCheck/skins/minimal/minimal.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/multi-select/css/multi-select.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/multi-select/css/ebro_multi-select.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/select2/select2.css')}}">

<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/select2/ebro_select2.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/datepicker/css/datepicker.css')}}"-->
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/style.css')}}">

<!--link rel="stylesheet" href="{{asset('resources/assets/supplier/css/theme/color_13.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/uploadfile.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/plugins/notifIt.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/Datatable/css/buttons.dataTables.min.css')}}"-->
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/jquery-ui.css')}}">
<link rel="stylesheet" href="{{asset('http://fonts.googleapis.com/css?family=Roboto:300,700&amp;subset=latin,latin-ext')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/font-awesome/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/font-awesome/css/font-awesome.min.css')}}">
@yield('stylesheets')
@stop
@section('head-script')

@stop
@section('body')
<script src="{{asset('resources/assets/supplier/js/jquery.min.js')}}"></script>
</head>
<body class="full_width">
    <!--div id="loader-wrapper">
        <div id="loader"></div>
    </div-->
    <div id="wrapper_all">
	    <!-- header -->
	    <header id="top_header">
			<div class="container">
				<div class="row">
					<div class="col-xs-6 col-sm-2">
						<a href="{{URL::asset('/seller/dashboard')}}" class="logo_main" title="{{$pagesettings->site_name}}"><img src="{{$pagesettings->site_logo_large}}" alt="{{$pagesettings->site_name}}"></a>
					</div>						
					<div class="col-xs-6 col-sm-10">						
					</div>						
				</div>
			</div>
		</header>
        <!-- mobile navigation -->
        <nav id="mobile_navigation"></nav>
		@hasSection('breadCrumbs')
        <ol id="breadcrumbs">
			<li><a href="#">Home</a></li>
			@yield('breadCrumbs')
		</ol>		
        @endif       
        <section class="container clearfix main_section">
            <div id="main_content_outer" class="clearfix">
                <div id="main_content" class="clearfix">
                    @yield('layoutContent')
                </div>
            </div>
        </section>
        <div id="footer_space"></div>
    </div>
    @include('seller.common.footer')
    
    <!--[[ common plugins ]]-->
    <!-- jQuery -->
	<script src="{{asset('resources/assets/supplier/js/jquery.validate.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/Datatable/js/jquery.dataTables.js')}}"></script>
    
	<script src="{{asset('resources/assets/supplier/Datatable/js/dataTables.buttons.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/Datatable/js/buttons.print.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/datatables/dataTables.bootstrap.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/jquery-ui.min.js')}}"></script>

	<script src="{{asset('resources/assets/supplier/js/jquery.tagsinput.js')}}"></script>
	<script src="{{asset('js/providers/jquery.form.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/bootstrap.min.js')}}"></script>	
	<script src="{{asset('resources/assets/supplier/js/jquery.ba-resize.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/jquery_cookie.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/retina.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/jquery.md5.js')}}"></script>

    <script src="{{asset('resources/assets/supplier/js/lib/typeahead.js/typeahead.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/lib/typeahead.js/hogan-2.0.0.js')}}"></script>    
    <!-- tinyNav -->
	<!--script src="{{asset('resources/assets/supplier/js/tinynav.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/lib/multi-select/js/jquery.multi-select.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/jquery.quicksearch.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/lib/select2/select2.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/lib/iCheck/jquery.icheck.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/pages/ebro_form_extended.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/lib/jquery-steps/jquery.steps.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/lib/parsley/parsley.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/pages/ebro_wizard.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/lib/datepicker/js/bootstrap-datepicker.js')}}"></script>	
	<script src="{{asset('resources/assets/supplier/js/lib/jQuery-slimScroll/jquery.slimscroll.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/lib/navgoco/jquery.navgoco.min.js')}}"></script-->
	<script src="{{asset('resources/assets/supplier/js/ebro_common.js')}}"></script>
	<!--script src="{{asset('resources/assets/supplier/js/jquery.sticky.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/jMenu.jquery.js')}}"></script>	
	<script src="{{asset('resources/assets/supplier/js/lib/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/date_format.js')}}"></script>
	<script src="{{asset('resources/assets/plugins/notifIt.min.js')}}"></script>    
	<script src="{{asset('resources/assets/supplier/js/jquery.uploadfile.min.js')}}"></script-->
	<script src="{{asset('js/providers/app.js')}}"></script>
	<script src="{{asset('js/providers/seller/login.js')}}"></script>
	<!--script src="{{asset('js/providers/Jquery-loadSelect.js')}}"></script>
	<script src="{{asset('js/providers/seller/background_login.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/lib/ckeditor/ckeditor.js')}}"></script>
	<script src="{{asset('resources/supports/pushNotifications.js')}}"></script>
	<script src="{{asset('https://www.gstatic.com/firebasejs/3.9.0/firebase.js')}}"></script-->    
    @yield('scripts')  
</body>
@stop
