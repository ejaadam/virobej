<div class="row">
	<div class="col-sm-12">
		<!--div class="panel panel-default"-->					
			<div class="">
				<div class="row">														
					<div class="col-sm-12">
						
							<div>	
								<div class="panel panel-info">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a>
												Change Password
											</a>
										</h4>
									</div>
									<div id="acc2_collapseFive" class="panel-collapse collapse in">
										<div class="panel-body">
											<div class="col-sm-12">
												<p class="alert alert-info"><i class="icon-info-sign" aria-hidden="true"></i> The password should be at least 6 charactes long. It must contain upper and lower case characters and at least one number.</p>
												<form class="form-horizontal" id="changepassword" action="{{route('seller.account-settings.update-password')}}">
													<fieldset>
														<div class="form-group">
															<label class="col-sm-3 control-label">Email Address</label>
															<label class="col-sm-6 text-left" id="current-email" style="margin-bottom: 0px;padding: 6px 15px 0px 15px;"><b>{{$userSess->email}}</b></label>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">{{trans('change_password.old_password')}} <span class="danger">*</span></label>
															<div class="col-sm-6">
															    <div class="input-group">
															        <input {!!build_attribute($change_pwd_fields['oldpassword']['attr'])!!} class="form-control" id=  "oldpassword" name="oldpassword" onkeypress="return RestrictSpace(event)" data-err-msg-to="#oldpwd_err">
																    <!--input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="{{trans('change_password.enter_your_old_password')}}" onkeypress="return RestrictSpace(event)"-->
															        <span class="input-group-addon pwdHS"><i class="icon-eye-close" aria-hidden="true"></i></span>
																</div>
																<span id="oldpwd_err"></span>							
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">{{trans('change_password.new_password')}} <span class="danger">*</span></label>
															<div class="col-sm-6 fieldgroup">
																<div class="input-group">
																	<input {!!build_attribute($change_pwd_fields['newpassword']['attr'])!!} class="form-control" id="newpassword" onkeypress="return RestrictSpace(event)" data-err-msg-to="#newpwd_err">
																	<!--input type="password" class="form-control" id="newpassword" name="newpassword" placeholder="{{trans('change_password.enter_your_new_password')}}" onkeypress="return RestrictSpace(event)"-->
																	<span class="input-group-addon pwdHS"><i class="icon-eye-close" aria-hidden="true"></i></span>
																</div>
																<span id="newpwd_err"></span>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3 control-label">Retype Password <span class="danger">*</span></label>
															<div class="col-sm-6 fieldgroup">
																<div class="input-group">
																	<input {!!build_attribute($change_pwd_fields['confirmpassword']['attr'])!!} class="form-control" id="confirmpassword" onkeypress="return RestrictSpace(event)" data-err-msg-to="#cnfrmpwd_err">
																	<!--input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="{{trans('change_password.please_retype_your_password')}}" onkeypress="return RestrictSpace(event)"-->
																	<span class="input-group-addon pwdHS"><i class="icon-eye-close" aria-hidden="true"></i></span>
																</div>
																<span id="cnfrmpwd_err"></span>
															</div>
														</div>
														<div class="form-group">
															<label class="col-sm-3"> </label>
															<div class="col-sm-3 fieldgroup">
																<button type="submit" class="btn btn-primary" id="submit" name="submit" >Update Password</button>
															</div>
														</div>
													</fieldset>   
												</form>	
											</div>	
										</div>
									</div>
								</div>
							</div>
						
					</div>
				</div>
			<!--/div-->
		</div>
	</div>
</div>
<script src="{{asset('js/providers/seller/account_settings.js')}}"></script>
