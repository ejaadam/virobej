@extends('mainLayout')
@section('main-title','Account Details')
@section('head-style')
{{ HTML::style('assets/supplier/bootstrap/css/bootstrap.min.css') }}
{{ HTML::style('assets/supplier/css/todc-bootstrap.min.css') }}
{{ HTML::style('assets/supplier/css/style.css') }}
{{ HTML::style('assets/supplier/css/theme/color_1.css') }}
{{ HTML::style('http://fonts.googleapis.com/css?family=Roboto:300&amp;subset=latin,latin-ext') }}
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
    @include('supplier.common.login-header')
    {{ HTML::script('assets/supplier/js/jquery.min.js')}}
    <form class="form" id="supplier-sign-up-form" action="{{URL::to('supplier/account-details')}}">
        <input type="hidden" name="account_id" id="account_id" value="{{$logged_userinfo->account_id or ''}}"/>
        <input type="hidden" name="supplier_id" id="supplier_id" value="{{$logged_userinfo->supplier_id or ''}}"/>
        <div class="row">
            <div class="col-sm-12">
                <div class="col-sm-4">
                    <h2 class="title">Business Details</h2>
                    <div class="form-group">
                        <label class="control-label" for="reg_company_name">{{fields['account_supplier.reg_company_name']['label']}}</label>
                        <input {{build_attr(fields['account_supplier.reg_company_name']['attr'])}} value="{{$logged_userinfo->reg_company_name or ''}}" id="reg_company_name" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="company_name">Company Name</label>
                        <input type="text" name="account_supplier[company_name]" value="{{$logged_userinfo->company_name or ''}}" id="company_name" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="type_of_bussiness">Type of Business</label>
                        <select type="text" name="account_supplier[type_of_bussiness]" id="type_of_bussiness" value="{{$logged_userinfo->type_of_bussiness or ''}}" class="form-control">
                            <option value="" hidden="hidden">Select Business Type</option>
                            @foreach($business_types as $type)
                            <option value="{{$type->business_id}}" {{(isset($logged_userinfo->type_of_bussiness) && $type->business_id==$logged_userinfo->type_of_bussiness)?'selected="selected"':'' }}>{{$type->business}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="website">Company URL1</label>
                        <input type="url" name="account_supplier[website]" value="{{$logged_userinfo->website or ''}}" id="website" value="{{$logged_userinfo->website or ''}}" class="form-control"/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <h2 class="title">Address Details</h2>
                    <div class="form-group">
                        <label class="control-label" for="country_id">Country</label>
                        <select name="address[country_id]" data-selected="{{$logged_userinfo->country_id or ''}}" id="country_id" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="state_id">State</label>
                        <select name="address[state_id]" data-selected="{{$logged_userinfo->state_id or ''}}" id="state_id" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="postal_code">Postal Code</label>
                        <input type="text" name="address[postal_code]" value="{{$logged_userinfo->postal_code or ''}}" id="postal_code" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="city">City</label>
                        <!--input type="text" name="address[city_id]" value="{{$logged_userinfo->city or '11'}}" id="city" class="form-control"/-->
                        <select name="address[city_id]" data-selected="{{$logged_userinfo->city_id or ''}}" id="city_id" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="street1">Address 1</label>
                        <input type="text" name="address[street1]" value="{{$logged_userinfo->address1 or ''}}" id="street1" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="street2">Address 2</label>
                        <input type="text" name="address[street2]" value="{{$logged_userinfo->address2 or ''}}" id="street2" class="form-control"/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <h2 class="title">Account Details</h2>
                    <div class="form-group">
                        <label class="control-label" for="email">Email</label>
                        <input type="email" name="login_mst[email]" id="email" value="{{(isset($login_mst['email']) && !empty($login_mst['email'])?$login_mst['email']:(isset($logged_userinfo->email) && !empty($logged_userinfo->email)?$logged_userinfo->email:''))}}" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="mobile">Mobile</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <select id="phonecode" name="login_mst[phonecode]" data-selected="{{(isset($login_mst['phonecode']) && !empty($login_mst['phonecode'])?$login_mst['phonecode']:(isset($logged_userinfo->phonecode) && !empty($logged_userinfo->phonecode)?$logged_userinfo->phonecode:''))}}" class="btn btn-default">
                                </select>
                            </span>
                            <input type="text" name="login_mst[mobile]" id="mobile" value="{{(isset($login_mst['mobile']) && !empty($login_mst['mobile'])?$login_mst['mobile']:(isset($logged_userinfo->mobile) && !empty($logged_userinfo->mobile)?$logged_userinfo->mobile:''))}}" class="form-control"/>
                        </div>
                    </div>
                    @if(!isset($logged_userinfo))
                    <div class="form-group">
                        <label class="control-label" for="pass_key">Password</label>
                        <input type="password" name="login_mst[pass_key]" id="pass_key" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="pass_key_confirmation">Confirm Password</label>
                        <input type="password" name="login_mst[pass_key_confirmation]" id="pass_key_confirmation" class="form-control"/>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-action col-sm-12 text-right">
            <input type="submit" class="btn btn-sm btn-success" value="{{isset($logged_userinfo)?'Save':'Sign Up'}}"/>
        </div>
    </form>
    {{ HTML::script('supports/Jquery-loadselect.js')}}
    {{ HTML::script('supports/app.js')}}
</body>
@stop
