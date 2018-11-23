@if(!empty($mobile))
<form action="{{route('aff.settings.updatemobileverification')}}" method="post" class="form-horizontal form-bordered" id="change-mobile-form" autocomplete="off" onsubmit="return false;" novalidate="novalidate">
	<div class="form-group" id="error">
		<label class="col-sm-4 control-label" for="oldmobile">{{trans('affiliate/general.current_mobile_number')}}</label>
		<div class="col-sm-5">
			<div class="input-group">
				
				<input type="text" id="crnt_number" class="form-control" value="{{$mobile}}" readonly="readonly">
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label" for="newmobile">{{trans('affiliate/general.new_mobile_number')}}<span class="text-danger">*</span></label>
		<div class="col-sm-5">
		    <div class="input-group">
				<input type="text" id="mobile" name="mobile" class="form-control" placeholder="{{trans('affiliate/settings/change_mobile.enter_new_mobile_number')}}">
				<div id="mobile_avail_status"></div>
			</div>
		</div>
	</div>
	<div class="form-group form-actions">
		<div class="col-sm-12 col-sm-offset-4">
			<button name="Send" type="submit" class="btn btn-sm btn-primary" id="send_verify_code"><i class="fa fa-angle-right"></i>  {{trans('affiliate/general.submit_btn')}}</button>
		</div>
	</div>
</form>

<form action="{{route('aff.settings.update_mobile')}}" style="display:none" method="post" class="form-horizontal form-bordered" id="code_verify_form" autocomplete="off" onsubmit="return false;" novalidate="novalidate" >
	<h3>{{trans('affiliate/settings/change_mobile.enter_a_code')}}</h3><hr>
	<p id="code_msg"></p>
	<div class="form-group">
		<div class="col-sm-4">
			<input type="text" id="verification_code" name="verify_code" class="form-control" placeholder="{{trans('affiliate/settings/change_mobile.enter_verification_code')}}">
		</div>
	</div>
	<div class="form-group form-actions">
		<div class="col-sm-4">
		    <button name="update_mobile" type="submit" class="btn btn-sm btn-primary" id="update_mobile" >{{trans('affiliate/general.continue_btn')}}</button>&nbsp;
			<button name="cancel" type="submit" class="btn btn-sm btn-default" id="cancel" >{{trans('affiliate/general.cancel_btn')}}</button>
		</div>
		<div class="col-sm-4">
		   <p></p>
		   <a href="javascript:void(0)" id="mobile_no">{{trans('affiliate/settings/change_mobile.dont_get_code')}}</a>
			
		</div>
	</div>
</form>
 @endif
 