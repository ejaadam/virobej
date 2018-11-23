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
	<script src="{{asset('js/providers/app.js')}}"></script>
	
    <div class="text-center">
        <img src="resources/assets/imgs/logo.png" alt="Virob">
    </div>
    <div id="sign-up-div" {{(isset($verify_mobile) || isset($signup_success) || isset($verify_email)) ? 'style=display:none;' : ''}}>
         <div class="text-center heading" >
			<h2>Tell us about your business</h2>
			<h3>Register on Virob!</h3>
		</div>
		<div class="col-sm-offset-3 col-sm-6">			
            <form class="form-horizontal hidden" autocomplete="off" id="check-user" action="{{route('seller.check-user')}}">                
                
                <div class="form-group">
                    <label class="control-label col-sm-3" for="mobile">Mobile / E-mail</label>
                    <div class="col-sm-9">
                        <!--input id="search_user" class="form-control" {!!build_attribute($fields['account_mst.mobile']['attr'])!!}/-->
                        <input id="search_user" class="form-control" name="search_user" required placeholder="Mobile / E-mail"/>
                    </div>
                </div>               
                
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <input type="submit" class="btn btn-sm btn-success" value="Continue"/>                      
                    </div>
                </div>
            </form>
        </div>
        <div class="col-sm-offset-3 col-sm-6">			            
            <form class="form-horizontal " autocomplete="off" id="supplier-sign-up-form" action="{{route('seller.sign-up')}}">		
				<div class="form-group">
                    <label class="control-label col-sm-2" for="account_mst[lastname]">{{$fields['buss_name']['label']}}</label>
                    <div class="col-sm-10">
                        <input id="bussiness_name" class="form-control" {!!build_attribute($fields['buss_name']['attr'])!!}/>
                    </div>
                </div>
				<div class="form-group row">
					<div class="row">
						<div class="col-md-6">
							<label class="control-label col-sm-4" for="firstname">{{$fields['account_details.firstname']['label']}}</label>
							<div class="col-sm-8">
								<input id="firstname" class="form-control" {!!build_attribute($fields['account_details.firstname']['attr'])!!}/>
							</div>
						</div>
						<div class="col-md-6">
							<label class="control-label col-sm-4" for="account_mst[lastname]">{{$fields['account_details.lastname']['label']}}</label>
							<div class="col-sm-8">
								<input id="lastname" class="form-control" {!!build_attribute($fields['account_details.lastname']['attr'])!!}/>
							</div>
						</div>
					</div>
		        </div>
				<div class="form-group row">
                    <div class="row">
						<div class="col-sm-6">
							<label class="control-label col-sm-4" for="pass_key">{{$fields['country']['label']}}</label>
							<div class="input-group col-md-8">
								<span class="input-group-addon"><img src="{{$flag or ''}}"></span>
								<select class="form-control" required name="country">
									@if(!empty($country_id))								
										<option value="{{$country_id}}" selected>{{$country}}</option>
									@endif
								</select>
							</div>
						</div>
						<input type="hidden" name="phonecode" value="{{$phonecode or ''}}">
						<div class="col-sm-6">
							<label class="control-label col-sm-4" for="pass_key">{{$fields['account_mst.mobile']['label']}}</label>
							<div class="input-group col-md-8">
								<span class="input-group-addon">{{$phonecode or ''}}
								</span>
								<input id="mobile" pattern="{{$mobile_validation or ''}}"  data-patternmismatch="Please enter valid mobile number" class="form-control" {!!build_attribute($fields['account_mst.mobile']['attr'])!!}/>
							</div>
						</div>
		            </div>
                </div>
				 <div class="form-group">
                    <label class="control-label col-sm-2" for="email">Merchant Type</label>
                    <div class="col-sm-10">
                        <select name="service_type" class="form-control" id="service_type">
							<option value="1">In-Store Merchant</option>
							<option value="2">Online Product Seller</option>
							<option selected value="3">Both</option>
						</select>
                    </div>
                </div>
			 <div class="form-group">
                    <label class="control-label col-sm-2" for="email">Number of Physical Locations</label>
                    <div class="col-sm-10">
                        <select name="phy_locations" class="form-control" id="phy_locations">
							<option value="1">None - Online only</option>
							<option value="2">None - I travel to my customers</option>
							<option value="3">1</option>
							<option value="4">2 - 9</option>
							<option value="4">10 - 49</option>
							<option value="5">50+</option>
						</select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="email">{{$fields['account_mst.email']['label']}}</label>
                    <div class="col-sm-10">
                        <input id="email" class="form-control" {!!build_attribute($fields['account_mst.email']['attr'])!!}/>
                    </div>
                </div>			
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pass_key">{{$fields['account_mst.pass_key']['label']}}</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <input id="pass_key" data-err-msg-to="#pass_key-group" class="form-control" {!!build_attribute($fields['account_mst.pass_key']['attr'])!!}/>
                            <span class="input-group-btn"><button type="button" id="show-hide-password" data-alternative="Hide" class="btn btn-info">Show</button></span>
                        </div>
                        <span id="pass_key-group"></span>
                    </div>
                </div>
				<div class="form-group" id="cateFld">
					<label class="control-label col-sm-2" for="pass_key">Business Category</label>
					<div class="col-sm-10">
						<div class="btn-group hierarchy-select" data-resize="auto" id="example-one">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<span class="selected-label pull-left">Select Category&nbsp;</span>
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu open">
								<div class="hs-searchbox">
									<input type="text" class="form-control" autocomplete="off">
								</div>
								<ul class="dropdown-menu inner" role="menu" style="max-height: 208px; overflow-y: auto;"></ul>
							</div>
							<input class="hidden hidden-field" name="search_form[category]" readonly aria-hidden="true" type="text"/>
						</div>                    
						<input type="hidden" name="bcategory" title="Category" placeholder="Category" required="1" data-valuemissing="Category is required." id="bcategory" value="">					
					</div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label class="checkbox-inline"><input data-err-msg-to="#checkbox-agree" data-valuemissing="This field is required" value="agreed"  {!!build_attribute($fields['agree']['attr'])!!} id="agree"/>{{$fields['agree']['label']}}</label>
                        </div>
                        <span id="checkbox-agree"></span>
                    </div>
                </div>
				
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" class="btn btn-sm btn-success" value="Continue"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Already a Virob Merchant?
                        <a href="{{route('seller.login')}}" class="btn btn-link">Sign in to Your Merchant Center.</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
	<div class="modal fade" id="parent_categoryModal" role="dialog" >
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close modal-close_btn" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{trans('admin/site-configuration/online_category/category.parent_category')}}</h4>
                </div>
                <div class="modal-body" style="height: 300px;">


                </div>
                <div class="modal-footer">
                    <button type="button" class=" modal-close_btn btn btn-default" data-dismiss="modal">{{trans('admin/site-configuration/online_category/category.close')}}</button>
                </div>
            </div>
        </div>
    </div>
	@if(isset($verify_mobile))
		<div id="mobile-verification-div" style="display:none">
				@include('seller.mobile_verification')
			<div class="text-center" id="signup-success-div" {!! isset($signup_success) ? '' : 'style="display:none;"'!!}>
				 <h3>Hi <span id="name"></span>,</h3>
				<p>Thank you for registering with us!</p>
			</div>
		</div>
	 @endif
	<script src="{{asset('resources/assets/plugins/Jquery-loadSelect.js')}}"></script>	
	<script src="{{asset('js/providers/seller/signup.js')}}"></script>
	<script src="{{asset('resources/assets/plugins/tree-search/hierarchy-select.js')}}"></script>
	<link rel="stylesheet" href="{{asset('resources/assets/plugins/tree-search/Tree-searchSelect.css')}}">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script>
		$(document).ready(function () {
			$('#example-one').hierarchySelect({
				width: '50%',
				hierarchy: true,
				search: true
			});
			
			$('#email,#pass_key').keypress(function(evt){
				var keycode = evt.charCode || evt.keyCode;
				  if (keycode  == 32) { 
					return false;
				  }
			})
		});
    </script>
</body>

@stop

