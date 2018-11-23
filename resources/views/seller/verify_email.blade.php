@extends('seller.common.layout')
@section('pagetitle','Verity-email')
@section('layoutContent')
<div class="row">
	<div class="col-sm-12">		
		<!--div class="user_heading">
			<div class="row">				
				<div class="col-sm-10">
					<div class="user_heading_info">						
						<h1>Seller Information</h1>
						<h2>Home -Account Settings - Verify Email</h2>
					</div>
				</div>
			</div>
		</div-->	
		<div class="row">
			<div class="col-sm-12">
				<!--div class="col-md-offset-1 col-md-10"-->
				    <p align="center" class="well text-info">
					<strong><i class="icon-info-sign"></i> Please complete this simple step to verify your e-mail address. Doing so would ensure that you receive all seller communication related to your business on Virob.</strong></p>
				<!--/div-->
			</div>
			<div class="col-sm-6" id="verify-email">
				<div class="panel panel-success">
					<div class="panel-heading"><a href="{{route('seller.account-settings').'#tbb_a'}}" class="btn btn-sm btn-danger" style="float: right; position: relative;top:-2px;right: 0px;"><i class="icon-arrow-left"></i> Back</a> <strong>E-mail Address Verification</strong></div>
					<div class="panel-body">
					    <br>
						@if($details->is_email_verified == 0)
						<!--p class="text-center txt alert alert-success" id="verification_msg" style="display:none;"><strong>Email Verification link has been sent to your email address</strong></p-->
						<p id="verification_msg"></p>
						@endif
						<div>
							<span class="text-info">E-mail :</span><strong>&nbsp;&nbsp;&nbsp;<span id="email-fld">{{$details->email}}</span></strong>&nbsp;&nbsp;&nbsp;<span id="txt-status"> &nbsp;&nbsp;@if($details->is_email_verified == 1)<span class="label label-success"><i class="icon-check"></i> Verified</span>@else<span class="label label-danger"><i class="icon-warning-sign"></i> Not Verified</span> @endif</span>
						</div>
						<br>
						@if($details->is_email_verified == 0)
						    <a href="#" id="resend-verification-code" data-url="{{route('seller.verify-email')}}"><i class="icon-repeat"></i> Resend Verification Code</a><br>
					    @endif
						<a href="#" id="change-email"><i class="icon-edit"></i> Change E-mail Address</a>
						<br>
						@if($details->is_email_verified == 0)
							<!--form id="email-verification-form" action="{{route('seller.check-email-verification')}}">
								<div class="form-group">
									<span ><strong>Email Address Verification Code</strong></span>
									<input {!!build_attribute($evfields['verification_code']['attr'])!!} id="verification_code" class="form-control" onkeypress="return isNumberKey(event)"/>	
									<a href="" class="btn-link" data-url="{{route('seller.verify-email')}}" id="resend-verification-code">Resend Verification Code</a>
								</div>
								<div class="form-group">
									<input type="submit" class="form-control btn btn-sm btn-success" id="verify" value="VERIFICATION CONTINUE"/>
								</div>
							</form-->
						@endif
					</div>
				</div>
			</div>
			<div class="col-sm-6" align="justify">
				<h4>Most popular FAQs</h4>
				<div class="panel-group" id="accordion">
				    <div class="panel panel-default">
						<div class="panel-heading">
						  <h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
							Why do I need to verify my e-mail id?</a>
						  </h4>
						</div>
						<div id="collapse1" class="panel-collapse collapse in">
							<div class="panel-body">
								Virob uses an authenticated mode to share communications related to your business on a verified email address. E-mail verification is critical to maintain the confidentiality of such communications
							</div>
						</div>
				    </div>				  
				    <div class="panel panel-default">
						<div class="panel-heading">
						  <h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
							Did not receive verification mail?</a>
						  </h4>
						</div>
						<div id="collapse2" class="panel-collapse collapse">
							<div class="panel-body">
								Check your spam/junk folder or click on Resend Verification Code.
						  </div>
					    </div>
				    </div>				  
				    <div class="panel panel-default">
						<div class="panel-heading">
						    <h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
							Can I change my verified e-mail address?</a>
						    </h4>
						</div>
						<div id="collapse3" class="panel-collapse collapse">
							<div class="panel-body">
								After your seller registration form is submitted and details validated by our team, you will get access to seller panel where these details can be edited.
						    </div>
					    </div>
				    </div>
					<br>
                    <br>
					<h4>FAQs related to other queries</h4>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
							   <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">Can I provide a different pick-up address from that stated in GST Certificate?</a>
							</h4>
						</div>
						<div id="collapse4" class="panel-collapse collapse">
							<div class="panel-body">
								Yes. However, the address should be within the same state where GST certificate is registered.
							</div>
						</div>
					</div>			  
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapse5">
							Can I change my pick-up address at later stage?</a>
							</h4>
						</div>
						<div id="collapse5" class="panel-collapse collapse">
							<div class="panel-body">
								Yes, you can change pick-up address any time after completing registration process. However, the address should be within the same state where GST certificate is registered.
							</div>
						</div>
					</div>			  
					<div class="panel panel-default">
						<div class="panel-heading">
						    <h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapse6">
							How and when will I get my payment?</a>
						    </h4>
						</div>
						<div id="collapse6" class="panel-collapse collapse">
							<div class="panel-body">
								Your payment will be credited to your account directly after successful delivery based on your payment cycle.
						  </div>
						</div>
			 		</div>
			    </div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade modal-sm" id="change-email-modal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
        <div class="modal-content">
			<div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal">&times;</button>
			    <h4 class="modal-title text-center">Change E-mail Address</h4>
			</div>
			<div class="modal-body">
			    <div id="error_msg"></div>
				<form id="change-email-form" class="form-horizontal" action="{{route('seller.profile-settings.change-email.new-email-otp')}}">
				    <!-- seller.verify-new-email -->
					<div class="form-group">
					    <label for="email" class="col-sm-4 control-label">Email Address</label>
						<div class="col-sm-8">
							<div class="input-group">
							    <span class="input-group-addon"><i class="icon-envelope"></i></span>
								<input {!!build_attribute($cefields['email']['attr'])!!} id="email" class="form-control" data-err-msg-to="#new_email-err" onkeypress="return RestrictSpace(event)"/>
								<!-- input type="text" name="email" id="email"  placeholder="Email Id" class="form-control input-lg" data-err-msg-to="#new_email-err"/-->
							</div>
							<span id="new_email-err"></span>
					    </div>
					</div>					
					<div class="form-group">
						<div class="col-sm-offset-4 col-sm-8">
							<button type="button" class="btn btn-success" id="change-email-btn">CHANGE</button>						
						</div>
					</div>					
				</form>
			</div>
        </div>
    </div>
</div>
@include('seller.common.assets')
<script src="{{asset('js/providers/seller/email-id-verification.js')}}"></script>
@stop