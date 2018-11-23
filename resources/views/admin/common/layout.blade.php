@extends('mainLayout')
@section('main-title')
@if (trim($__env->yieldContent('title')))
Admin | @yield('title')
@else
Admin
@endif
@stop
@section('head-style')
<link rel="stylesheet" href="{{asset('resources/assets/admin/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/todc-bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/jquery.tagsinput.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/font-awesome/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/img/flags/flags.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/retina.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/dataTables/media/DT_bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/dataTables/extras/TableTools/media/css/TableTools.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/bootstrap-switch/stylesheets/bootstrap-switch.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/bootstrap-switch/stylesheets/ebro_bootstrapSwitch.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/iCheck/skins/minimal/minimal.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/multi-select/css/multi-select.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/multi-select/css/ebro_multi-select.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/select2/select2.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/select2/ebro_select2.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/js/lib/datepicker/css/datepicker.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/chosen.jquery.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/plugins/font-awesome/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/style.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/theme/color_4.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/admin/css/uploadfile.css')}}">
<link rel="stylesheet" href="{{asset('resources/assets/plugins/notifIt.css')}}">
<link rel="stylesheet" href="{{asset('http://fonts.googleapis.com/css?family=Roboto:300,700&amp;subset=latin,latin-ext')}}">
@stop
@section('head-script')
	<script src="{{asset('resources/assets/admin/js/jquery.min.js')}}"></script>
@stop
@section('body')
<body class="sidebar_hidden side_fixed">
    <div id="loader-wrapper">
        <div id="loader"></div>
    </div>
    <div id="wrapper_all">
        @include('admin.common.topnav')
        @yield('top_navigation')
        <!-- mobile navigation -->
        <nav id="mobile_navigation"></nav>
        <section id="breadcrumbs">
            <div class="container">
                <ul>
                    @yield('breadCrumbs')
                </ul>
            </div>
        </section>
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
                                <h1>Admin Login</h1>
                            </div>
                            <div id="login_mess" style="color: red; text-align: center;">    </div>
                            <form id="loginfrm" method="POST" action="{{URL::to('admin/login_check')}}">
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
    @include('admin.common.footer')
    @include('admin.common.leftnav')
    <!--[[ common plugins ]]-->
    <!-- jQuery -->
	<script src="{{asset('resources/assets/admin/js/jquery.validate.min.js')}}"></script>
	<script src="{{asset('resources/assets/admin/Datatable/js/jquery.dataTables.js')}}"></script>
	<link rel="stylesheet" href="{{asset('resources/assets/admin/Datatable/css/buttons.dataTables.min.css')}}">
	<script src="{{asset('resources/assets/admin/Datatable/js/dataTables.buttons.min.js')}}"></script>
	<script src="{{asset('resources/assets/admin/Datatable/js/buttons.print.min.js')}}"></script>		
   
	<script src="{{asset('resources/assets/admin/datatables/dataTables.bootstrap.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/jquery-ui.min.js')}}"></script>	
    
	<link rel="stylesheet" href="{{asset('resources/assets/admin/css/jquery-ui.css')}}">
    <script src="{{asset('resources/assets/admin/js/jquery.tagsinput.js')}}"></script>	
	<script src="{{asset('resources/supports/jquery.form.js')}}"></script>	
	
	<script src="{{asset('resources/assets/admin/bootstrap/js/bootstrap.min.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/jquery.ba-resize.min.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/jquery_cookie.min.js')}}"></script>	
	
	<script src="{{asset('resources/assets/admin/js/retina.min.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/lib/typeahead.js/typeahead.min.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/lib/typeahead.js/hogan-2.0.0.js')}}"></script>	

	<script src="{{asset('resources/assets/admin/js/tinynav.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/lib/multi-select/js/jquery.multi-select.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/jquery.quicksearch.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/chosen.jquery.min.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/chosen.ajaxaddition.jquery.js')}}"></script>	
	
	<script src="{{asset('resources/assets/admin/js/lib/select2/select2.min.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/lib/iCheck/jquery.icheck.min.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/pages/ebro_form_extended.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/lib/jquery-steps/jquery.steps.min.js')}}"></script>	
	<script src="{{asset('resources/assets/admin/js/lib/parsley/parsley.min.js')}}"></script>	
   
   <script src="{{asset('resources/assets/admin/js/lib/datepicker/js/bootstrap-datepicker.js')}}"></script>	
   <script src="{{asset('resources/assets/admin/js/lib/jQuery-slimScroll/jquery.slimscroll.min.js')}}"></script>	
   <script src="{{asset('resources/assets/admin/js/lib/navgoco/jquery.navgoco.min.js')}}"></script>	
   <script src="{{asset('resources/assets/admin/js/ebro_common.js')}}"></script>	
   <script src="{{asset('resources/assets/admin/js/lib/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>	
   <script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>	
   <script src="{{asset('resources/assets/admin/js/jquery.uploadfile.min.js')}}"></script>	
   
   <script src="{{asset('resources/assets/plugins/notifIt.min.js')}}"></script>	
   <script src="{{asset('js/providers/app.js')}}"></script>	
   <script src="{{asset('js/providers/Jquery-loadSelect.js')}}"></script>	
   <script src="{{asset('resources/supports/admin/background_login.js')}}"></script>	
   <script src="{{asset('resources/assets/supplier/js/lib/ckeditor/ckeditor.js')}}"></script>	
   
   <!--script src="{{asset('resources/supports/pushNotifications.js')}}"></script>	
   <script src="{{asset('https://www.gstatic.com/firebasejs/3.9.0/firebase.js')}}"></script-->	   
   @yield('scripts')   
</body>
@stop
