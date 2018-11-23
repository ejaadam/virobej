 @if(!empty($email))
<form action="{{route('aff.settings.updateemailverification')}}" method="post" class="form-horizontal form-bordered" id="change-email-form" autocomplete="off" onsubmit="return false;" novalidate="novalidate">
	<div class="form-group" id="error">
		<label class="col-sm-4 control-label" for="oldemail">{{trans('affiliate/general.current_email_id')}}</label>
		<div class="col-sm-5">
			<input type="email" class="form-control" id="crnt_email" value="{{$email}}" readonly="readonly">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label" for="newemail">{{trans('affiliate/general.new_email_id')}}<span class="text-danger">*</span></label>
		<div class="col-sm-5">
			<input type="email" id="email" name="email" class="form-control" placeholder="{{trans('affiliate/settings/change_email.enter_new_email_id')}}">
			<div id="email_avail_status"></div>
		</div>
	</div>
	<div class="form-group form-actions">
		<div class="col-sm-12 col-sm-offset-4">
			<button name="Send" type="submit" class="btn btn-sm btn-primary" id="send_verification_code"><i class="fa fa-angle-right"></i> {{trans('affiliate/general.submit_btn')}}</button>
		</div>
	</div>
</form>

<form action="{{route('aff.settings.update_email')}}" method="post" class="form-horizontal form-bordered" id="code_verification_form" autocomplete="off" onsubmit="return false;" novalidate="novalidate" style="display:none">
	<h3>{{trans('affiliate/settings/change_email.enter_a_code')}}</h3><hr>
	<p id="codemsg"></p>
	<div class="form-group">
		<div class="col-sm-4">
			<input type="text" id="verify_code" name="verify_code" class="form-control" placeholder="{{trans('affiliate/settings/change_email.enter_verification_code')}}">
		</div>
	</div>
	<div class="form-group form-actions">
	    <div class="col-sm-4">
			<button name="update_email" type="submit" class="btn btn-sm btn-primary" id="update_email" >{{trans('affiliate/general.continue_btn')}}</button>&nbsp;
			<button name="cancel" type="submit" class="btn btn-sm btn-default" id="cancel" > {{trans('affiliate/general.cancel_btn')}}</button>
		</div>
		<div class="col-sm-4">
		  <p></p>
		  <a href="javascript:void(0)" id="email_id">{{trans('affiliate/settings/change_email.dont_get_code')}}</a>
		</div>
	</div>
</form>
 @endif