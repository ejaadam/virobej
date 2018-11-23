@extends('affiliate.layout.dashboard')
@section('title',\trans('affiliate/settings/security_pwd.page_title'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-home"></i> {{\trans('affiliate/settings/security_pwd.page_title')}}</h1>
     
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>Settings</li>
        <li class="active">{{\trans('affiliate/settings/security_pwd.breadcrumb_title')}}</li>
      </ol>
    </section>
   <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row"  id="kycgrid">        
            <div class="col-md-6">
			    <div class="box box-primary">
					<div class="box-header with-border">
                    <h3 class="box-title">{{\trans('affiliate/settings/security_pwd.login_password')}}</h3>
                    </div>
                    <div class="panel-body">
              <form  method="post" id="changetranscationpassword" onsubmit="return false;" action="{{route('aff.settings.check_securitypin')}}">
              <input type="hidden" name="_token" id="csrf-token" value="{!! csrf_token() !!}"/>
                <div class="form-group">
                  <label for="exampleInputEmail1">{{\trans('affiliate/settings/security_pwd.current_password')}}</label>
                  <input type="password" id="tran_oldpassword" name="tran_oldpassword" class="form-control" id="exampleInputEmail1"
                   placeholder="{{trans('affiliate/settings/security_pwd.current_sec_phn')}}">
                  <span class="help-block" id="oldpassword_status"></span>
                </div>
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
                </div>	
				<div class="form-group col-md-6 link_font">
					<a href="{{route('aff.settings.forgot_security_pin')}}" id="forgot_sec_pwd">{{trans('affiliate/settings/security_pwd.forgot_security_pin')}}</a>
				</div>
				</div>
					<div class="form-group"></div>
					<div class="alert alert-warning" id="forgot_mess" style="display:none;"></div>
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
<script src="<?php echo URL::asset('affiliate/validate/lang/change-pin.js');?>"></script>
<script src="{{asset('js/providers/affiliate/setting/update_securitypwd.js')}}"></script>
@stop
                 