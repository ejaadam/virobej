<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <base href="{{url('/')}}/">
        <title>@yield('title') | {{$siteConfig->site_name}}</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="author" content={{$siteConfig->site_name}}">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
       
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="{{url('assets/admin/css/bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{url('assets/admin/css/font-awesome.min.css')}}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{url('assets/admin/css/ionicons.min.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{url('assets/admin/dist/css/AdminLTE.css')}}">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
        folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="{{url('assets/admin/dist/css/skins/_all-skins.min.css')}}">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{url('assets/admin/plugins/iCheck/flat/blue.css')}}">
        <!-- Morris chart -->
        <link rel="stylesheet" href="{{url('assets/admin/plugins/morris/morris.css')}}">
        <!-- jQuery 2.2.3 -->
        <script src="{{asset('assets/admin/plugins/jQuery/jquery-2.2.3.min.js')}}"></script>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="{{url('assets/admin/js/html5shiv/3.7.3/html5shiv.min.js')}}"></script>
        <script src="{{url('assets/admin/js/respond/1.4.2/respond.min.js')}}"></script>
        <![endif]-->
    </head>
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
                @yield('content')
            </div>
            <!-- Content Wrapper. Contains page content -->
            @include('admin.common.footer')
        </div>
        <!-- ./wrapper -->

        <!-- jQuery UI 1.11.4 -->
        <script src="{{asset('assets/admin/plugins/jQueryUI/jquery-ui.js')}}"></script>

        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>$.widget.bridge('uibutton', $.ui.button);</script>
        <!-- Bootstrap 3.3.6 -->
        <script src="{{asset('assets/admin/js/bootstrap.min.js')}}"></script>
        <!-- Sparkline -->
        <script src="{{asset('assets/admin/plugins/sparkline/jquery.sparkline.min.js')}}"></script>
        <!-- Slimscroll -->
        <script src="{{asset('assets/admin/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
        <!-- FastClick -->
        <script src="{{asset('assets/admin/plugins/fastclick/fastclick.js')}}"></script>
        <script src="{{asset('assets/admin/plugins/validation/jquery.validate.min.js')}}"></script>
        <script src="{{asset('assets/admin/plugins/validation/additional-methods.min.js')}}"></script>
        <!-- AdminLTE App -->
        <script src="{{asset('assets/admin/dist/js/app.js')}}"></script>
        <script src="{{asset('js/providers/admin/app.js')}}"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="{{url('assets/admin/dist/js/demo.js')}}"></script>
        @yield('scripts')
              <script>
		//$('body').toggleClass('loaded');
		
		 $(document).ready(function () {
            $('.sidebar-menu').on('click', '#user_quickLogin', function (e) {
                e.preventDefault();
                $('#retailer-qlogin-model').modal();
            });
             $('#retailer-qlogin-model').on('click', '#qloginBtn', function (e) {
                e.preventDefault();
                $('#unameErr').html('');
                if ($('#retailerUname').val() != '') {
                    $('#accErr').html('');
                    $.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: $('#retailerForm').attr('action'),
                        data: $('#retailerForm').serialize(),
                        beforeSend: function () {
                            $('#qloginBtn').text('Processing..');
                            $('#qloginBtn').attr('disabled', true);
                        },
                        success: function (res) {
				
                            if (res.status == 'ok') {
                                $('#qloginBtn').text('Submit');
                                $('#qloginBtn').attr('disabled', true);
								$('#accErr').html('<div class="alert alert-success">Account Available<a href="#" class="close" data-dismiss="alert" area-label="close">&times</a></div>');
                                if (res.redirect == true) {
                                    window.location.href = 'account/dashboard';
                                }
                            } else {
                                $('#qloginBtn').text('Submit');
                                $('#qloginBtn').attr('disabled', false);
                                $('#accErr').html('<div class="alert alert-danger">Account Not Found<a href="#" class="close" data-dismiss="alert" area-label="close">&times</a></div>');
                            }
                        },
                        error: function () {
                            $('#qloginBtn').text('Submit');
                            $('#qloginBtn').attr('disabled', false);
                        }
                    });
                } else {
                    $('#unameErr').addClass('text-danger').html('Please enter User uname or email');
                }
            })
        }); 
		</script>
    </body>
</html>
