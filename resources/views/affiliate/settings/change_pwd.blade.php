@extends('affiliate.layout.dashboard')
@section('title',\trans('affiliate/settings/changepwd.page_title'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa-home"></i> {{\trans('affiliate/settings/changepwd.profile_settings')}}</h1>
     
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>Settings</li>
        <li class="active">{{\trans('affiliate/settings/changepwd.breadcrumb_title')}}</li>
      </ol>
    </section>
   <!-- Main content -->
    <section class="content">  
		<!-- Small boxes (Stat box) -->
		<div class="row"  id="kycgrid">        
            <div class="col-md-6">
			    <div class="box box-primary">
					<div class="box-header with-border">
                    <h3 class="box-title">{{\trans('affiliate/settings/changepwd.login_password')}}</h3>
                    </div>
              <div class="panel-body">
              <form  method="post" id="changepassword" onsubmit="return false;" action="{{route('aff.settings.updatepwd')}}">
              <input type="hidden" name="_token" id="csrf-token" value="{!! csrf_token() !!}"/>
                <div class="form-group">
                  <label for="exampleInputEmail1">{{\trans('affiliate/settings/changepwd.current_password')}}</label>
                  <input type="password" id="oldpassword" name="oldpassword" class="form-control" id="exampleInputEmail1" 
                  placeholder="{{trans('affiliate/settings/changepwd.current_password')}}">
                  <span class="help-block" id="oldpassword_status"></span>
                </div>
                <div class="row">
                <div class="col-md-6 form-group">
                 
                  <label for="exampleInputPassword1">{{\trans('affiliate/settings/changepwd.new_password')}}</label>
                  <input type="password" id="newpassword" name="newpassword" class="form-control" id="exampleInputPassword1" 
                  placeholder="{{trans('affiliate/settings/changepwd.new_password')}}">
                  </div>
                  
                   <div class=" col-md-6 form-group">
                   <div class="">
                   <label for="exampleInputPassword2">{{\trans('affiliate/settings/changepwd.confirm_password')}}</label>
                  <input type="password" id="confirmpassword" name="confirmpassword" class="form-control" id="exampleInputPassword2" 
                  placeholder="{{trans('affiliate/settings/changepwd.confirm_password')}}">
                  </div>
                  </div>
               	</div>    
              	<div class="form-group" >
                    <button name ="Send" type="submit" id="updatepwd" class="btn btn-md bg-olive"><i class="fa fa-save"></i> {{\trans('affiliate/settings/changepwd.update_btn')}}</button>                    
                </div>	
			</form>
	</div>		
    </section>
    </div>
    </div>
    </div>
    <!-- /.content -->
@stop
@section('scripts')
<script src="<?php echo URL::asset('affiliate/validate/lang/change-pwd.js');?>"></script>
<script src="{{asset('js/providers/affiliate/setting/update-password.js')}}"></script>
@stop