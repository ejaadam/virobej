@extends('affiliate.layout.dashboard')
@section('title',\trans('affiliate/settings/security_pwd.page_title'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <!-- <h1><i class="fa fa-home"></i> {{\trans('affiliate/settings/security_pwd.page_title')}}</h1>--
     
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>Settings</li>
        <li class="active">{{\trans('affiliate/settings/security_pwd.breadcrumb_title')}}</li>
      </ol>
    </section>
   <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		
		<div id="otp_affliate">
		 <form  method="post" id="otp_affiliate"  onsubmit="return false;" action="{{route('aff.settings.otp_check')}}">
		  <div class="row">
		  <div class="col-md-4 form-group">
                  <label for="exampleInputEmail1">{{\trans('affiliate/settings/security_pwd.otp')}}</label>
                  <input type="text" id="otp" name="otp" class="form-control" id=""
                   placeholder="{{trans('affiliate/settings/security_pwd.otp')}}">
                  <span class="help-block" id="oldpassword_status"></span>
				  </div>
				  </div> <div class="row">
				  	<div class="form-group col-md-6 link_font">
                   <button name ="Send" type="submit" id="update_otp" class="btn btn-md bg-olive"><i class="fa fa-save"></i> Continue
                    </button>
                       </div>
                       </div>
                
		</form>
		</div>
		<div class="row"  id="kycgrid">        
            <div class="col-md-6">
			    <div class="box box-primary">
					<div class="box-header with-border">
                    <h3 class="box-title">{{\trans('affiliate/settings/security_pwd.login_password')}}</h3>
                    </div>
           <div class="panel-body">
              <form  method="post" id="updatetranscationpassword"  onsubmit="return false;" action="{{route('aff.settings.reset_update_pwd')}}">
              <input type="hidden" name="_token" id="csrf-token" value="{!! csrf_token() !!}"/>
            
                <div class="row">
                <div class="col-md-6 form-group">
                  <label for="exampleInputPassword1">{{\trans('affiliate/settings/security_pwd.new_password')}}</label>
                  <input type="password" id="tran_newpassword" name="tran_newpassword" class="form-control" id="exampleInputPassword1" 
                  placeholder="{{trans('affiliate/settings/security_pwd.new_security_pin')}}">
                 <span class="help-block" id="oldpassword_status"></span>
                 </div>
                  
                   <div class="row">
                   <div class=" col-md-6 form-group">
                   <label for="exampleInputPassword2">{{\trans('affiliate/settings/security_pwd.confirm_password')}}</label>
                  <input type="password" id="tran_confirmpassword" name="tran_confirmpassword" class="form-control" id="exampleInputPassword2" 
                  placeholder="{{trans('affiliate/settings/security_pwd.confirm_security_pin')}}">
                  </div>
                  </div>
               	</div>   
                <div class="row">
              	<div class="form-group col-md-6 link_font">
                    <button name ="Send" type="submit" id="update_securitypwd" class="btn btn-md bg-olive"><i class="fa fa-save"></i> {{\trans('affiliate/settings/security_pwd.update_btn')}}
                    </button>
                    <button type="reset" class="btn btn-md btn-warning"><i class="fa fa-repeat"></i> {{\trans('affiliate/settings/security_pwd.reset_btn')}}</button>
                       </div>	
                        </div>
                        <div class="form-group"></div>
                         <div class="alert alert-warning" id="forgot_mess" style="display:none;">
                         </div>
		         </form>
              </div>
              </div>
              </div>
              </div>
         </section>
        
    </div>
  </div>
@stop
@section('scripts')
<script src="<?php echo URL::asset('affiliate/validate/lang/change-otp.js');?>"></script>
<script src="<?php echo URL::asset('affiliate/validate/lang/change-pin.js');?>"></script>
<script src="{{asset('js/providers/affiliate/setting/reset_security_pwd.js')}}"></script>
@stop
                 