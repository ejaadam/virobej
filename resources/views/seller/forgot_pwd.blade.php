@extends('seller.common.simple_layout')
@section('pagetitle','Reset Password')
@section('breadCrumbs')
@stop
@section('layoutContent')
<div class="row">
	<div class="col-sm-12" id="reset_password" {!!($pwd_resetfrm)? '' : 'style=display:none;'!!}>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title"><!--i class="icon-undo"></i-->  Reset Password</h4>
			</div>
			<div class="panel-body" id="pwdreset_panel">
				<p class="alert alert-info"><i class="icon-info-sign"></i>  The password should be at least 6 charactes long. It must contain upper and lower case characters and at least one number.</p>
				<form id="password-resetfrm" class="form-horizontal profile" action="{{route('seller.pwdreset-link')}}" method="post"> 
				 <!-- route('seller.pwdreset-link')-->				
					<div class="form-group">
						<input name="token" id="token" required="1" data-valuemissing="Token is required." type="hidden" value="{{(isset($token) && !empty($token))? $token:''}}">
					</div>				
					<div class="form-group">
						<label for="new_password" class="col-sm-3 control-label">New Password<span class="danger"> *</span></label>
						<div class="col-sm-4"> 
							<div class="input-group">   <!-- pattern="/^\S{6,20}$/" data-patternmismatch="New Password must 6 to 20 characters"-->
								<input {!!build_attribute($rfields['newpwd']['attr'])!!} id="newpwd" class="form-control" onkeypress="return RestrictSpace(event)" data-err-msg-to="#newpwd_err">
								<span class="input-group-addon new_pwd"><i class="icon-eye-close" aria-hidden="true"></i></span>
							</div>
							<span id="newpwd_err"></span>
						</div>
					</div>
					<div class="form-group">
						<label for="confirm_password" class="col-sm-3 control-label">Retype Password<span class="danger"> *</span></label>
						<div class="col-sm-4">
							<div class="input-group">  								
								<input {!!build_attribute($rfields['confirm_pwd']['attr'])!!} id="confirm_pwd" class="form-control" onkeypress="return RestrictSpace(event)" data-err-msg-to="#confirm_newpwd_err">
								<span class="input-group-addon cnfrm_pwd"><i class="old_pin icon-eye-close" aria-hidden="true"></i></span>
							</div>
							<span id="confirm_newpwd_err"></span>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-4">
							<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Reset Password</button>
						</div>
					</div>
				</form>
            </div>
			<div class="panel-body" id="pwdreset_success" style="display:none;">
				<p class="text-success text-center"><i class="icon-ok-sign" style="font-size: 70px;"></i></p>
				<p class="text-center" id="success_msg"></p>
				<p class="text-center"><a href="{{route('seller.login')}}" class="btn btn-success"> Click Here to Login</a></p>
            </div>
        </div>
    </div>
	<div class="col-sm-12" {!!($pwd_resetfrm)? 'style=display:none;' : ''!!}>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title"> Sorry!</h4>
			</div>
			<div class="panel-body">
			    <p class="text-success text-center"><i class="icon-warning-sign text-danger" style="font-size: 70px;"></i></p>
				<p class="text-center">{{$msg}}</p>
				<p class="text-center"><a href="{{route('seller.login')}}" class="btn btn-success"> Click Here to Login</a></p>
			</div>
		</div>
	</div>
</div>
@stop