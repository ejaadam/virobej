@extends('supplier.common.layout')
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')
<section class="container clearfix main_section">
    <div id="main_content">
        <!-- main content -->
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-2">
                        <form class="form-horizontal user_form">
                            <h3 class="heading_a">Account Information</h3>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">User Name</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->uname or '' }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">E-mail</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->email or ''}}</p>
                                </div>
                            </div>
                            <h3 class="heading_a">Contact info</h3>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"> Company Name</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->company_name or ''}}</p>
                                    <div class="hidden_control">
                                        <input type="text" class="form-control" value="John Smith">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Full Name</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->full_name or '' }}</p>
                                    <div class="hidden_control">
                                        <input type="text" class="form-control" value="John Smith">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Address</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->street1 or ''}}</p>
                                    <div class="hidden_control">
                                        <input type="text" class="form-control" value="John Smith">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">City</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->city or ''}}</p>
                                    <div class="hidden_control">
                                        <input type="text" class="form-control" value="John Smith">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">State</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->state or ''}}</p>
                                    <div class="hidden_control">
                                        <input type="text" class="form-control" value="John Smith">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Country</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->country_name or ''}}</p>
                                    <div class="hidden_control">
                                        <input type="text" class="form-control" value="John Smith">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Postcode</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->postal_code or ''}}</p>
                                    <div class="hidden_control">
                                        <input type="text" class="form-control" value="(+32) 123 456 789">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Phone No</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->office_phone or ''}}</p>
                                    <div class="hidden_control">
                                        <input type="text" class="form-control" value="(+32) 123 456 789">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Mob No</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->mobile or ''}}</p>
                                    <div class="hidden_control">
                                        <input type="text" class="form-control" value="(+32) 123 456 789">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Fax No</label>
                                <div class="col-sm-10 editable">
                                    <p class="form-control-static">{{$details->office_fax or ''}}</p>
                                    <div class="hidden_control">
                                        <input type="text" class="form-control" value="(+32) 123 456 789">
                                    </div>
                                </div>
                            </div>
                            <div class="form_submit clearfix" style="display:none">
                                <div class="row">
                                    <div class="col-sm-10 col-sm-offset-2">
                                        <button class="btn btn-primary btn-lg">Save all data</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <nav id="sidebar">
        <ul id="icon_nav_v" class="side_ico_nav">
            <li> <a href="#" title="Dashboard"><i class="icon-home"></i></a> </li>
            <li> <a href="#" title="Content"><i class="icon-edit"></i></a> </li>
            <li> <a href="#" title="Users"><i class="icon-group"></i></a> </li>
            <li> <a href="#"><i class="icon-tasks"></i></a> </li>
            <li class="active"> <a href="#"><i class="icon-beaker"></i></a> </li>
            <li> <a href="#"><i class="icon-book"></i></a> </li>
            <li> <a href="#"><i class="icon-tag"></i></a> </li>
            <li> <a href="#"><i class="icon-wrench"></i></a> </li>
        </ul>
    </nav>
</section>
@include('supplier.common.assets')
{{ HTML::script('assets/supplier/js/jquery.min.js') }}
{{ HTML::script('assets/supplier/js/jquery_cookie.min.js') }}
{{ HTML::script('assets/supplier/js/lib/parsley/parsley.min.js') }}
{{ HTML::script('supports/supplier/login.js') }}
@stop
