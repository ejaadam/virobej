@extends('seller.common.layout')
@section('pagetitle','Dashboard')
@section('breadCrumbs')
<li>Merchant</li>
<li>Account</li>
<li>Business Information</li>
@stop
@section('layoutContent')	
	<div class="panel panel-default">
	   <div class="row">
	   <div class="panel-body">
					<div class="col-sm-12">
					<form class="form-horizontal" id="change_mobile_otp_confirm" action="{{route('seller.profile-settings.change-mobile.new-mob-confirm')}}" enctype="multipart/form-data">
		
									<div class="panel-body">
										<div class="col-sm-12">
											<fieldset>														
										<div class="form-group">
										<label for="inputEmail" class="col-sm-3 control-label">Mobile Number</label>
										  <div class="col-sm-3">
											 <div class="input-group">
												<span class="input-group-addon">{{$supplier_details->phonecode or ''}}</span>
												<input {!!build_attribute($change_mobile_confirm_fileds['phone_no']['attr'])!!}  class="form-control" id="phone_code" value="" data-err-msg-to="#phone_err" onkeypress="return isNumberKey(event)"	>
												
													</div>
														<span id="phone_err"></span>
												  </div>
												   
												  <div class="col-sm-4">
														<button type="button" class="btn btn-info" id="change_mobile_verify_code" data-url="{{route('seller.profile-settings.change-mobile.new-mob-otp')}}">Send Code</button>
													</div>
												</div>
											<div class="form-group">
												<label for="inputEmail" class="col-sm-3 control-label">&nbsp;</label>
												   <div class="col-sm-3">
													<a href="{{route('seller.profile-settings.change-mobile.new-mob-otp-resend')}}" id="new-mobile-otp-resend" class="btn-link">Resend Verification Code</a>
													</div>
											</div>
												<div id="mobile_verify">
												<div class="form-group">
												<label for="inputEmail" class="col-sm-3 control-label">
														SMS Verification Code</label>
													<div class="col-sm-3">
														<input  {!!build_attribute($change_mobile_confirm_fileds['code']['attr'])!!} class="form-control" id="Verification_code" value="">
													</div>
												</div>
                                        <div class="form-group">										
							 			<div class="col-sm-offset-3 col-sm-3">									
											<input type="submit" value="Verify" class="btn btn-primary" disabled="disabled">
										</div>
									    </div>	
										</div>
                                    </fieldset>    										
										</div>		
									</div>
					  </form>	
					</div>
				   </div>
				 </div>
				</div>
		@stop	
	@section('scripts')
	<script src="{{asset('js/providers/seller/change_mobile.js')}}"></script>
	@stop