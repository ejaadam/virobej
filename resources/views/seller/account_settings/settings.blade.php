@extends('seller.common.layout')
@section('pagetitle','Dashboard')
@section('breadCrumbs')
<li>Merchant</li>
<li>Account</li>
<li>Business Information</li>
@stop
@section('layoutContent')	
<div class="row" id="profile-panel">
	@if(empty($logged_userinfo->is_email_verified))
	<div class="col-sm-12">	
		<p class="alert alert-danger"><i class="icon-warning-sign"></i> Your email address is not yet verified. <a href="{{route('seller.verify-email')}}" class="text-danger" id="verify-email"><b>Verify Now</b></a></p>
	</div>
	@endif
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Seller Information</h4>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-12"  id="ac_settings">
						<ul class="nav subnav nav-tabs">
							<li><a data-toggle="tab" rel="{{route('seller.account-settings.profile_info')}}" class="tabs" href="#tbb_a">Seller Info</a></li>
							<li><a data-toggle="tab" class="tabs" rel="{{route('seller.account-settings.manage-cashback')}}" href="#tbb_d">Manage Cashback</a></li>
							<li><a data-toggle="tab" class="tabs" rel="{{route('seller.account-settings.tax-info')}}" href="#tbb_e">Tax Info</a></li>
							
							@if(!empty($logged_userinfo->is_verified))
								<li><a data-toggle="tab" class="tabs" rel="{{route('seller.account-settings.pickup-address')}}" href="#tbb_f">Picku-up Address</a></li>
							@endif
								<li><a data-toggle="tab" class="tabs" rel="{{route('seller.account-settings.bank-info')}}" href="#tbb_g">Bank Details</a></li>
							@if(!empty($logged_userinfo->is_verified))	
								<li><a data-toggle="tab" class="tabs" rel="{{route('seller.account-settings.change-password')}}" href="#tbb_i">Change Password</a></li>
								<li><a data-toggle="tab" class="tabs" rel="{{route('seller.account-settings.return-address')}}"  href="#tbb_j">Shipping Info</a></li>
							@if(!empty($userSess->security_pin))
								<li><a data-toggle="tab" class="tabs"  href="#tbb_h">Change PIN</a></li>
							@else
								<li><a data-toggle="tab" class="tabs"  href="#tbb_h">Create Security PIN</a></li>
							@endif
							@endif
								<li><a data-toggle="tab" class="tabs"  href="#tbb_m" id="change_mob">Change Mobile</a></li>
							
						</ul>
						<div id="alt-msg">
							<!--<div class="alert alert-success"><a class="close" data-dismiss="alert" area-label="close" >&times;</a><p>Updated Successfulluy</div>-->
						</div>
						<div class="tab-content">
							<div id="tbb_a" class="tab-pane active">
								
							</div>
							<div id="tbb_d" class="tab-pane">
							</div>
							<div id="tbb_e" class="tab-pane">
								
							</div>
							<div id="tbb_f" class="tab-pane">
								
							</div>
							<div id="tbb_g" class="tab-pane">
								
							</div>
							<div id="tbb_i" class="tab-pane">
							
							</div>
							<div id="tbb_j" class="tab-pane">
								
							</div>									
							<div id="tbb_h" class="tab-pane">
							    <div class="panel panel-info">					
								    <div class="panel-heading">
										<h4 class="panel-title">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#acc2_collapseFive">
											{{!empty($userSess->security_pin) ? 'Change PIN':'Create Security PIN '}} 
											</a>
										</h4>
									</div>									
									<form id="change-pro-pin-form" class="form-horizontal profile" action="{{route('seller.account-settings.change-pin')}}" method="post" autocomplete="off"  style="{{!empty($userSess->security_pin)? '':'display:none;'}}">
										<div class="form-group"></div>
										<div class="form-group">
											<label for="current_profile_pin" class="col-sm-3 control-label">Current Security PIN</label>
											<div class="col-sm-6">
												<div class="input-group">
													<input {!!build_attribute($security_pin_cfileds['current_pin']['attr'])!!} class="form-control" id="old_pin" onkeypress="return isNumberKey(event)" data-err-msg-to="#current_profile_pin_err"/>
													<span class="input-group-addon pwdHS"><i class="icon-eye-close" aria-hidden="true"></i></span>
												</div>
												<span id="current_profile_pin_err"></span>
											</div>
										</div>
										<div class="form-group">
											<label for="new_profile_pin" class="col-sm-3 control-label">New Security PIN</label>
											<div class="col-sm-6">
												<div class="input-group">
													<input {!!build_attribute($security_pin_cfileds['new_pin']['attr'])!!} class="form-control" id="new_pin" onkeypress="return isNumberKey(event)" data-err-msg-to="#new_profile_pin_err"/>
													<span class="input-group-addon pwdHS"><i class="icon-eye-close"></i></span>
												</div>
												<span id="new_profile_pin_err"></span>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-3 col-sm-8">
												<button type="submit"  class="btn btn-primary btn-sm"><i class="fa fa-save margin-r-5"></i>Save</button>&nbsp;&nbsp;
												<a href="#" id="forgot-profile-pin"  data-url="" ><u>Forgot Security Pin?</u></a>
											</div>
										</div>
									</form>
									<form id="reset-pro-pin-form" class="form-horizontal profile" style="display:none" action="{{route('seller.security-pin.forgot')}}" method="post" data-url="{{route('seller.security-pin.reset')}}" autocomplete="off">
										<div class="form-group"></div>
										<div class="form-group">
											<label for="code" class="col-sm-3 control-label">Verification Code</label>
											<div class="col-sm-8">
												<input {!!build_attribute($security_pin_rfileds['code']['attr'])!!} class="form-control" id="code" onkeypress="return isNumberKey(event)">
											</div>
										</div>
										<div class="form-group">
											<label for="profile_pin" class="col-sm-3 control-label">New Security PIN</label>
											<div class="col-sm-8">
												<div class="input-group">
													<input {!!build_attribute($security_pin_rfileds['profile_pin']['attr'])!!} class="form-control" id="profile_pin" data-err-msg-to="#profile_pins_err" onkeypress="return isNumberKey(event)">
													<span class="input-group-addon pwdHS"><i class="icon-eye-close"></i></span>
												</div>
												<span id="profile_pins_err" for="" class=""></span>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-3 col-sm-8">
												<button type="submit" id="resetBtn" class="btn btn-primary btn-sm"><i class="fa fa-save margin-r-5"></i>Save</button>&nbsp;&nbsp; <a href="{{route('seller.security-pin.resend-forgot-otp')}}" id="resend-verification-code" data-url=""><u><i class="fa fa-repeat margin-r-5" aria-hidden="true"></i>Resend OTP</u>
												</a>
											</div>
										</div>
									</form>
									<form id="create-pro-pin-form" class="form-horizontal profile"  action="{{route('seller.security-pin.save')}}" method="post" autocomplete="off" style="{{empty($userSess->security_pin)? '':'display:none;'}}">
										<div class="form-group"></div>
										<div class="form-group">
											<label for="code" class="col-sm-3 control-label">Security PIN</label>
											<div class="col-sm-8">
											    <div class="input-group">
											    	<input {!!build_attribute($security_pin_sfileds['profile_pin']['attr'])!!} class="form-control" id="code"  data-err-msg-to="#security_pin_err" onkeypress="return isNumberKey(event)">
													<span class="input-group-addon pwdHS"><i class="icon-eye-close"></i></span>
											    </div>
												<span id="security_pin_err"></span>
											</div>
										</div>
										<div class="form-group">
											<label for="profile_pin" class="col-sm-3 control-label">Confirm Security PIN</label>
											<div class="col-sm-8">
												<div class="input-group">
												    <input {!!build_attribute($security_pin_sfileds['confirm_pin']['attr'])!!} class="form-control" id="profile_pin" data-err-msg-to="#confirm_pin_err" onkeypress="return isNumberKey(event)">											
													<span class="input-group-addon pwdHS"><i class="icon-eye-close"></i></span>
												</div>
												<span id="confirm_pin_err" for="" class=""></span>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-offset-3 col-sm-8">
												<button type="submit" id="savebtn" class="btn btn-primary btn-sm"><i class="fa fa-save margin-r-5"></i>Save</button>&nbsp;&nbsp;
											</div>
										</div>
									</form>								
							    </div>
							</div>
							<div id="tbb_m" class="tab-pane">								
								<div class="panel panel-info">					
								   <div class="panel-heading">
										<h4 class="panel-title">
											<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#acc2_collapseFive">
												Change Mobile
											</a>
										</h4>
									</div>										 
									<form id="change_phone_number" class="form-horizontal profile" action="{{route('seller.profile-settings.change-mobile.new-mob')}}" method="post" autocomplete="off">
										<div class="form-group">
											<h4><label class="col-sm-4 control-label">Change Mobile Number</label></h4>
											<div class="col-sm-4">
											    <div class="input-group">
												    <span class="input-group-addon"><i class="icon-phone"></i></span>
												    <input type="text" class="form-control" value="{{isset($userSess->phonecode) ? $userSess->phonecode.'-':''}}{{isset($userSess->mobile) ? $userSess->mobile:''}}" readonly>
													<input type="hidden" name="change_mobile" id="change_mobile" value="{{isset($userSess->mobile) ? $userSess->mobile:''}}">
												</div>
												<span id="current_profile_pin_err"></span>
											</div>
										</div>
										<span id="change_mobile_content"></span>
										<div class="form-group">
											<div class="col-sm-offset-4 col-sm-8">
												<button type="submit"  class="btn btn-primary btn-sm"><i class="fa fa-save margin-r-5"></i>Send Verification</button>
											</div>
										</div>
										
										<!--div class="form-group">
											<div class="col-sm-offset-3 col-sm-6">
												<div class="input-group">
													<h4><p align="center">Change Phone Number <?php if(isset($change_mobile)){ echo $change_mobile;} ?></p></h4>
													<input type="hidden" name="change_mobile" id="change_mobile" value="<?php if(isset($change_mobile)){ echo $change_mobile;} ?>" >
												</div>
												<span id="current_profile_pin_err"></span>
											</div>
										</div>
										<span id="change_mobile_content"></span>
										<div class="form-group">
											<div class="col-sm-offset-4 col-sm-6">
												<button type="submit"  class="btn btn-primary btn-sm"><i class="fa fa-save margin-r-5"></i>Send Verification</button>
											</div>
										</div-->
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
		    </div>
		</div>
    </div>	
</div>	
<link rel="stylesheet" href="{{asset('resources/assets/plugins/tagsAutocomplete/src/jquery.tagsinput-revisited.css')}}" />
@include('seller/common/cropper')
@stop
@section('scripts')
@include('seller/common/cropper_css')
<script src="{{asset('js/providers/seller/change_pin.js')}}"></script>
<script src="{{asset('js/providers/seller/change_mobile.js')}}"></script>
<!--<script src="{{asset('js/providers/seller/account_settings.js')}}"></script>-->
<script src="js/providers/seller/seller-info.js"></script>
@stop