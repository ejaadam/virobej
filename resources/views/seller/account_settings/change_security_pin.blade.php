@extends('seller.common.layout')
@section('pagetitle','Dashboard')

@section('layoutContent')
<div class="pageheader">
    <div class="row">
	<div class="col-sm-12">
	<div class="user_heading">
			<div class="row">				
				<div class="col-sm-10">
					<div class="user_heading_info">						
						<h1>Change Security PIN</h1>
						<h2>Home - Account Settings - Change Security PIN</h2>
					</div>
				</div>
			</div>
		</div>	
        <div id="alert-div"></div>
			<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-default">
					<div class="">
						<div class="row">
        <div class="col-sm-12">
			<div class="panel-group" id="accordion2">
									<div class="panel panel-info">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#acc2_collapseFive">
						Change Security PIN
					</a>
				</h4>
			</div>
				
					<form id="change-pro-pin-form" class="form-horizontal profile" action="{{route('seller.account-settings.change-pin')}}" method="post" autocomplete="off" >
						<div class="form-group"></div>
						<div class="form-group">
							<label for="current_profile_pin" class="col-sm-3 control-label">Current Security PIN</label>
							<div class="col-sm-6">
								<div class="input-group">
									<input class="form-control" id="old_pin" name="current_pin" maxlength="4"  onkeypress="return isNumberKey(event)" data-err-msg-to="#current_profile_pin_err"/>
									<span class="input-group-addon curnt_pin"><i class="fa fa-eye-slash"></i></span>
								</div>
								<span id="current_profile_pin_err"></span>
							</div>
						</div>
						<div class="form-group">
							<label for="new_profile_pin" class="col-sm-3 control-label">New Security PIN</label>
							<div class="col-sm-6">
								<div class="input-group">
									<input  class="form-control" id="new_pin" name="new_pin" maxlength="4" onkeypress="return isNumberKey(event)" data-err-msg-to="#new_profile_pin_err"/>
									<span class="input-group-addon new_pin"><i class="fa fa-eye-slash"></i></span>
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
				
			</div>
        </div>
        
        </div>
    </div>
</div>
</div>
					</div>
				</div>
			</div>
			</div>
		</div>

<script src="{{asset('js/providers/seller/change_pin.js')}}"></script>
@stop
