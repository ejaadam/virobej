
<div class="greybox">

				<div class="greybox-body form" id="change-email">				
					
					<form id="verify-propin-form" class="form-horizontal" action="{{route('seller.verify-profile-pin')}}" method="post" style="">
					<!--	<div class="form-group">
							<h5 class="col-sm-12"><b>Verify Security PIN</b></h5>
						</div> -->
						<div class="form-group">
							<label for="code" class="col-sm-4 control-label">Enter Security PIN</label>
							<div class="col-sm-8">
								<div class="input-group">
									@if(isset($profile_pin_verify_fileds['profile_pin']['attr']))						
									 <input id="profile_pin" class="form-control" data-err-msg-to="#verify_propin-err" onkeypress="return isNumberKey(event)" />
									 	@endif
									 <span class="input-group-addon curnt_pin"><i class="icon-eye-close" aria-hidden="true"></i></span>
								</div>
								<span id="verify_propin-err"></span><br>
								<a href="{{route('seller.profile-settings.profile-pin.forgot')}}" class="btn-link" id="forgot-pin">Forgot Security PIN</a>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-4 col-sm-8">
								<button type="submit" class="btn btn-success" >Verify</button>&nbsp;&nbsp;&nbsp;
								<button type="button" id="cancel-change-email" class="btn btn-danger cancel-change-email">Cancel</button>
							</div>
						</div>
					</form>
					<form id="reset-pinfrm" class="form-horizontal" action="{{route('seller.profile-settings.profile-pin.reset')}}" method="post" style="display: none;">
						<!--<div class="form-group">
							<h5 class="col-sm-12"><b>Forgot Security PIN</b></h5>
						</div>-->
						<div class="verify-profile-pin">
							<div class="form-group">
								<label for="code" class="col-sm-4 control-label">Enter Verification Code</label>
								<div class="col-sm-8">
									<input  id="code" class="form-control"/><br>
									<a href="{{route('seller.profile-settings.profile-pin.resend-forgot-otp')}}" class="btn-link" id="resend-forgot-otp">Resend Verification Code</a>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-offset-4 col-sm-8">
									<button type="button" id="verify-verification-code" class="btn btn-success">Verify</button>&nbsp;&nbsp;&nbsp;
									<button type="button" id="cancel-change-email" class="btn btn-danger cancel-change-email">Cancel</button>					
								</div>
							</div>
						</div>
					<div class="new-profile-pin" style="display: none;">
							<div class="form-group">
								<label for="code" class="col-sm-4 control-label">New Security PIN</label>
								<div class="col-sm-8">
									<div class="input-group">
										<input  id="profile_pin" class="form-control" data-err-msg-to="#new_pin-err" onkeypress="return isNumberKey(event)"/>
										<span class="input-group-addon new_pin"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
									</div>
									<span id="new_pin-err"></span>
								</div>
							</div>
							<div class="form-group">
								<label for="code" class="col-sm-4 control-label">Confirm Security PIN</label>
								<div class="col-sm-8">
									<div class="input-group">
										<input  id="confirm_profile_pin" class="form-control" data-err-msg-to="#cnfrm_pin-err" onkeypress="return isNumberKey(event)"/>
										<span class="input-group-addon cnfrm_pin"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
									</div>
									<span id="cnfrm_pin-err"></span>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-offset-4 col-sm-8">
									<button type="submit" class="btn btn-success">Reset</button>&nbsp;&nbsp;
									<button type="button" class="btn btn-danger back-to-verification-code">Cancel</button>
								</div>
							</div>
						</div>
					</form>
					<form id="change-email-new-email" class="form-horizontal" action="{{route('seller.profile-settings.change-email.new-email-otp')}}" method="post" style="display: none;">
						<!--<div class="form-group">
							<h5 class="col-sm-12"><b>Change Email ID</b></h5>
						</div>-->
						<div class="form-group">
							<label for="email" class="col-sm-4 control-label">Ener New Email ID</label>
							<input type="hidden" name="propin_session" id="propin_session"/>
							<div class="col-sm-8">
								<div class="input-group">
									<span class="input-group-addon"><i class="icon-envelope"></i></span>
									<input  id="email" class="form-control"  data-err-msg-to="#new_email-err" />						
								</div>
								<span id="new_email-err"></span>	
							</div>
						</div>           
						<div class="form-group">
							<div class="col-sm-offset-4 col-sm-8">
								<button type="submit" class="btn btn-success" >{{trans('general.btn.continue')}}</button>&nbsp;&nbsp;&nbsp;
								<button type="button" id="cancel-change-email" class="btn btn-danger cancel-change-email">Cancel</button>
							</div>
						</div>
					</form>
					<form id="change-email-confirm" class="form-horizontal" action="{{route('seller.profile-settings.change-email.confirm')}}" method="post" style="display: none;">
						<!--<div class="form-group">
							<h5 class="col-sm-12"><b>Change Email ID</b></h5>
						</div>-->
						<div class="form-group">
							<label for="code" class="col-sm-4 control-label">Enter verification_code</label>
							<div class="col-sm-8">
								<input  id="code" class="form-control" onkeypress="return isNumberKey(event)"/><br>
								<a href="{{route('seller.profile-settings.change-email.new-mob-email-resend')}}"  id="new-email-otp-resend" class="btn-link">Resend Verification Code</a>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-4 col-sm-8">
								<button type="submit" class="btn btn-success" >Confirm</button>&nbsp;&nbsp;&nbsp;
								<button type="button" id="cancel-change-email" class="btn btn-danger cancel-change-email">Cancel</button>
							</div>
						</div>
					</form>
                   </div>
				   </div>



