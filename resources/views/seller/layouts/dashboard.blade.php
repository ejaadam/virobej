<!doctype html>
<html lang="en">
    <base href="{{URL::to('/')}}/">
    <head>
        <meta charset="UTF-8">
        <title>@yield('pagetitle') - {{$pagesettings->site_name}}</title>
        <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
        <!-- bootstrap framework-->
        {{ HTML::style('assets/supplier/bootstrap/css/bootstrap.min.css')}}
        {{ HTML::style('assets/supplier/css/todc-bootstrap.min.css')}}
      
        <!-- font awesome -->
        {{ HTML::style('assets/supplier/css/font-awesome/css/font-awesome.min.css')}}
        <!-- flag icon set -->
        {{ HTML::style('assets/supplier/img/flags/flags.css')}}
        <!-- retina ready -->
        {{ HTML::style('assets/supplier/css/retina.css')}}
        {{ HTML::style('assets/supplier/js/lib/dataTables/media/DT_bootstrap.css')}}
        {{ HTML::style('assets/supplier/js/lib/dataTables/extras/TableTools/media/css/TableTools.css')}}
        <!-- bootstrap switch -->
        {{HTML::style('assets/theme/bracket/css/bootstrap-fileupload.min.css')}}
        {{ HTML::style('assets/supplier/js/lib/bootstrap-switch/stylesheets/bootstrap-switch.css')}}
        {{ HTML::style('assets/supplier/js/lib/bootstrap-switch/stylesheets/ebro_bootstrapSwitch.css')}}
        {{ HTML::style('assets/supplier/js/lib/iCheck/skins/minimal/minimal.css')}}
        {{ HTML::style('assets/supplier/js/lib/multi-select/css/multi-select.css')}}
        {{ HTML::style('assets/supplier/js/lib/multi-select/css/ebro_multi-select.css')}}
        {{ HTML::style('assets/supplier/js/lib/select2/select2.css')}}
        {{ HTML::style('assets/supplier/js/lib/select2/ebro_select2.css')}}
        {{ HTML::style('assets/supplier/js/lib/datepicker/css/datepicker.css')}}
        {{HTML::style('assets/supplier/chosen/docsupport/prism.css')}}
        {{HTML::style('assets/supplier/chosen/chosen.css')}}
        {{HTML::style('supports/member/uploadfile.css') }}
        {{HTML::style('supports/chosen.css')}}
        <!-- ebro styles -->
        {{ HTML::style('assets/supplier/css/style.css')}}
        <!-- ebro theme -->
        {{ HTML::style('assets/supplier/css/theme/color_4.css')}}
        <!--[if lt IE 9]>
{{ HTML::style('assets/supplier/css/ie.css')}}
{{ HTML::script('assets/supplier/js/ie/html5shiv.js')}}
{{ HTML::script('assets/supplier/js/ie/respond.min.js')}}
{{ HTML::script('assets/supplier/js/ie/excanvas.min.js')}}
        <![endif]-->
        {{ HTML::script('assets/admin/js/jquery.min.js')}}
        <!-- custom fonts -->
        {{ HTML::style('http://fonts.googleapis.com/css?family=Roboto:300,700&amp;subset=latin,latin-ext')}}
        <script> var ajaxUrl = '<?php echo URl::to('');?>';</script>
        {{ HTML::style('supports/member/uploadfile.css')}}
        {{ HTML::script('supports/member/jquery.uploadfile.min.js')}}
    </head>
    <body class="sidebar_hidden side_fixed">
        <div id="wrapper_all">
            @include('supplier.common.topnav')
            @include('supplier.common.top_navigation')
            <!-- mobile navigation -->
            <nav id="mobile_navigation"></nav>
            <!--<section id="breadcrumbs">
                    <div class="container">
                            <ul>
                                    <li><a href="#">Ebro supplier</a></li>
                                    <li><a href="#">Other Pages</a></li>
                                    <li><span>Contact Page</span></li>
                            </ul>
                    </div>
            </section>-->
            @yield('breadCrumbs')
            <section class="container clearfix main_section">
                <div id="main_content_outer" class="clearfix">
                    <div id="main_content" class="clearfix">
                        @yield('layoutContent')
                    </div>
                </div>
            </section>
            <div id="footer_space"></div>
        </div>
        <div class="modal fade " id="suppliers_rpwd" tabindex="-1" role="dialog" aria-labelledby="suppliers_detailsLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Reset Password</h4>
                    </div>
                </div>
            </div>
        </div>
        @include('supplier.common.footer')
        <!--  @include('supplier.common.leftnav') -->
        <!--[[ common plugins ]]-->
        <!-- jQuery -->
        {{ HTML::script('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js')}}
        {{ HTML::script('assets/supplier/Datatable/js/jquery.dataTables.js')}}
        <script src="<?php echo URL::asset('assets/supplier/datatables/jquery.dataTables.js');?>" type="text/javascript"></script>
        <script src="<?php echo URL::asset('assets/supplier/datatables/dataTables.bootstrap.js');?>" type="text/javascript"></script>
        <script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js'></script>
        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.22/themes/start/jquery-ui.css" />
        {{HTML::script('assets/supplier/chosen/chosen.jquery.min.js')}}
        <script type="text/javascript">
            var config = {
                '.chosen-select': {},
                '.chosen-select-deselect': {allow_single_deselect: true},
                '.chosen-select-no-single': {disable_search_threshold: 10},
                '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
                '.chosen-select-width': {width: "95%"}
            }
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
        </script>
       
        <!--HTML::script('http://malsup.github.com/jquery.form.js')-->
        {{HTML::script('supports/jquery.form.js')}}
        <!-- bootstrap framework -->
        {{ HTML::script('assets/supplier/bootstrap/js/bootstrap.min.js')}}
        <!-- jQuery resize event -->
        {{ HTML::script('assets/supplier/js/jquery.ba-resize.min.js')}}
        <!-- jquery cookie -->
        {{ HTML::script('assets/supplier/js/jquery_cookie.min.js')}}
        <!-- retina ready -->
        {{ HTML::script('assets/supplier/js/retina.min.js')}}
        <!-- typeahead -->
        {{ HTML::script('assets/supplier/js/lib/typeahead.js/typeahead.min.js')}}
        {{ HTML::script('assets/supplier/js/lib/typeahead.js/hogan-2.0.0.js')}}
        <!-- tinyNav -->
        {{ HTML::script('assets/supplier/js/tinynav.js')}}
        {{ HTML::script('assets/supplier/js/lib/multi-select/js/jquery.multi-select.js')}}
        {{ HTML::script('assets/supplier/js/jquery.quicksearch.js')}}
        {{ HTML::script('assets/supplier/js/lib/select2/select2.min.js')}}
        {{ HTML::script('assets/supplier/js/lib/iCheck/jquery.icheck.min.js')}}
        {{ HTML::script('assets/supplier/js/pages/ebro_form_extended.js')}}
        {{ HTML::script('assets/supplier/js/lib/jquery-steps/jquery.steps.min.js')}}
        {{ HTML::script('assets/supplier/js/lib/parsley/parsley.min.js')}}
        <!-- {{ HTML::script('assets/supplier/js/pages/ebro_wizard.js')}}  -->
        {{ HTML::script('assets/supplier/js/lib/datepicker/js/bootstrap-datepicker.js')}}
        {{HTML::script('supports/member/jquery.uploadfile.min.js') }}
        <script src="{{URL::to('assets/supplier')}}/js/lib/jQuery-slimScroll/jquery.slimscroll.min.js"></script>
        <script src="{{URL::to('assets/supplier')}}/js/lib/navgoco/jquery.navgoco.min.js"></script>
        <script src="{{URL::to('assets/supplier')}}/js/ebro_common.js"></script>
        {{ HTML::script('supports/member/date_format.js')}}
        <!--   {{ HTML::script('assets/supplier/js/lib/ckeditor/ckeditor.js')}}
           {{ HTML::script('assets/supplier/js/pages/ebro_wysiwg.js')}}   -->
        <!-- {{ HTML::script('supports/supplier/create_user.js')}}-->
        <!--  {{ HTML::script('supports/supplier/create_user.js')}}   -->
        <!-- slimscroll -->
        {{ HTML::script('supports/app.js')}}
        {{ HTML::script('supports/Jquery-loadSelect.js')}}

        {{ HTML::script('supports/chosen.ajaxaddition.jquery.js')}}
        @yield('scripts')
        {{ HTML::script('supports/supplier/changepassword.js')}}
        <!--[if lte IE 9]>
                <script src="{{URL::to('assets/supplier/js/ie/jquery.placeholder.js')}}"></script>
                <script>
                        $(function() {
                                $('input, textarea').placeholder();
                        });
                </script>
        <![endif]-->
    </body>
</html>
