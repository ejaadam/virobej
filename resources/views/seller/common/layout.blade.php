@extends('mainLayout')
@section('main-title',(trim($__env->yieldContent('title')))?'Seller | '.$__env->yieldContent('title'):'Seller')
@section('head-style')
<link rel="stylesheet" href="{{asset('resources/assets/supplier/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/todc-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/img/flags/flags.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/retina.css')}}">

<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/dataTables/media/DT_bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/dataTables/extras/TableTools/media/css/TableTools.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/bootstrap-switch/stylesheets/bootstrap-switch.css')}}">

<!--link rel="stylesheet" href="{{asset('resources/assets/theme/bracket/css/bootstrap-fileupload.min.css')}}"-->

<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/bootstrap-switch/stylesheets/ebro_bootstrapSwitch.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/iCheck/skins/minimal/minimal.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/multi-select/css/multi-select.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/multi-select/css/ebro_multi-select.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/select2/select2.css')}}">

<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/select2/ebro_select2.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/js/lib/datepicker/css/datepicker.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/style.css')}}">

<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/theme/color_13.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/css/uploadfile.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/plugins/notifIt.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/supplier/Datatable/css/buttons.dataTables.min.css')}}">
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
        @include('seller.common.topnav')        
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
        <div class="modal fade" id="login_modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="login_panel log_section">
                            <div class="login_head">
                                <h1>supplier Login</h1>
                            </div>
                            <div id="login_mess" style="color: red; text-align: center;">    </div>
                            <form id="loginfrm" method="POST" action="{{URL::to('supplier/login_check')}}">
                                <div class="form-group">
                                    <label for="login_username">Username</label>
                                    <input type="text" id="user_login" name="username" class="form-control input-lg" placeholder="User Name">
                                    <span class="errmsg_yellow" style="display:none"></span>
                                </div>
                                <div class="form-group">
                                    <label for="login_password">Password</label>
                                    <input type="password" id="user_password" name="password" class="form-control input-lg" placeholder="Password">
                                    <span class="errmsg_yellow" style="display:none"></span>
                                    <label class="checkbox"><input type="checkbox" name="login_remember" id="login_remember"> Remember me</label>
                                </div>
                                <div class="login_submit">
                                    <button class="btn btn-primary btn-block btn-lg" type="submit" name="signmebutton" id="login_button">
                                        LOGIN
                                    </button>
                                </div>
                                <div class="text-center">
                                    <small><a class="form_toggle" href="#reg_form"> Forgot password?</a></small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
	<script src="{{asset('resources/assets/supplier/js/tinynav.js')}}"></script>
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
	<script src="{{asset('resources/assets/supplier/js/lib/navgoco/jquery.navgoco.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/ebro_common.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/jquery.sticky.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/jMenu.jquery.js')}}"></script>	
	<script src="{{asset('resources/assets/supplier/js/lib/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/date_format.js')}}"></script>
	<script src="{{asset('resources/assets/plugins/notifIt.min.js')}}"></script>    
	<script src="{{asset('resources/assets/supplier/js/jquery.uploadfile.min.js')}}"></script>
	<script src="{{asset('js/providers/app.js')}}"></script>
	<script src="{{asset('js/providers/Jquery-loadSelect.js')}}"></script>
	<script src="{{asset('js/providers/seller/background_login.js')}}"></script>
	<script src="{{asset('resources/assets/supplier/js/lib/ckeditor/ckeditor.js')}}"></script>
	<!--script src="{{asset('resources/supports/pushNotifications.js')}}"></script-->
	<script src="{{asset('resources/assets/supplier/js/pushNotifications.js')}}"></script>
	<script src="{{asset('https://www.gstatic.com/firebasejs/3.9.0/firebase.js')}}"></script>    
    @yield('scripts')
    <!--[if lte IE 9]>
            <script src="{{URL::to('assets/supplier/js/ie/jquery.placeholder.js')}}"></script>
            <script>
                    $(function() {
                            $('input, textarea').placeholder();
                    });
            </script>
    <![endif]-->
</body>
@stop
