@extends('affiliate.layout.dashboard')
@section('title',trans('affiliate/profile.my_profile'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('affiliate/profile.my_profile')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('affiliate/general.dashboard')}}</a></li>
        <li >{{trans('affiliate/profile.page_title')}}</li>
        <li class="active">{{trans('affiliate/profile.my_profile')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		@if(!empty($profileInfo) && !empty($userSess))	 
		<div class="row" id="my_profile">    
			<!-- ./col -->
			<div class="col-md-3">
				<!-- Profile Image -->
				<div class="box box-primary">
					<div class="box-body box-profile">
					   <div class="row">
							@if(isset($userSess->profile_image) && !empty($userSess->profile_image)) 
							<img class="profile-user-img img-responsive img-circle" src="<?php echo url('' .config('constants.PROFILE_IMAGE_PATH').$userSess->profile_image); ?>" alt="{{trans('affiliate/profile.user_profile_picture')}}">
							@else				
							<img class="profile-user-img img-responsive img-circle" src="{{asset(config('constants.PROFILE_IMAGE_PATH').''.config('constants.DEFAULT_IMAGE'))}}" alt="{{trans('affiliate/profile.user_profile_picture')}}">
							@endif 	
						</div>
						<p></p>
						<div class="row text-center">
							<p id="add_prof_image"><i class="fa fa-fw fa-upload"></i>
							<a href="#">{{trans('affiliate/profile.upload_your_photo')}}</a></p>
							
							<p id="remove_prof_image">
							@if(isset($profileInfo['userdetails']->profile_image) && !empty($profileInfo['userdetails']->profile_image) && $profileInfo['userdetails']->profile_image != 
                            config('constants.DEFAULT_IMAGE'))							 
							<i class="fa fa-fw fa-times" ></i><a href="#"  class="text-danger">{{trans('affiliate/profile.remove_profile_photo')}}</a>
							@endif
							</p>
						</div>
						
						<h3 class="profile-username text-center">{{isset($profileInfo['userdetails']->full_name) ? $profileInfo['userdetails']->full_name : ''}}</h3>
 
						<p class="text-center text-danger">({{isset($profileInfo['userdetails']->uname) ? $profileInfo['userdetails']->uname : ''}})</p>

						<ul class="list-group list-group-unbordered">
						<li class="list-group-item">
						  <b>{{trans('affiliate/general.my_refferals')}}</b> <a class="pull-right">{{isset($profileInfo['refferal_cnt']) ? number_format($profileInfo['refferal_cnt']) : ''}}</a>
						</li>
						<li class="list-group-item">
						  <b>{{trans('affiliate/general.my_downlines')}}</b> <a class="pull-right">{{isset($profileInfo['tree_info']->my_team_cnt)  ? number_format($profileInfo['tree_info']->my_team_cnt) : ''}}</a>
						</li>						
						</ul>
					</div>
					<!-- /.box-body -->
				</div>
			</div>	
			
			<div class="col-md-6">	
			    <div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-user margin-r-5"></i> {{trans('affiliate/general.about_me')}}</h3>
					</div>
					<div class="box-body">
					    <div class="col-sm-12">
						    <div class="form-group alert alert-success">
							   <strong>{{trans('affiliate/account/profile.promotional_url')}}</strong>
							   <p class="text-muted"><a>{{url('/'.$profileInfo['userdetails']->uname)}}</a></p>
							</div>
						</div>
					    <div class="col-sm-6">
							<div class="form-group">
							   <strong>{{trans('affiliate/account/profile.signed_up_on')}}</strong>
							   <p class="text-muted">{{isset($profileInfo['userdetails']->signedup_on) ? $profileInfo['userdetails']->signedup_on : ''}}</p>
							</div>
							<div class="form-group">
							   <strong>{{trans('affiliate/account/profile.gender')}}</strong>
							   <p class="text-muted">{{isset($profileInfo['userdetails']->gender) ? $profileInfo['userdetails']->gender : ''}}</p>
							</div>
							<div class="form-group">
							   <strong>{{trans('affiliate/account/profile.invited_by')}}</strong>
							   <p class="text-muted">{{isset($profileInfo['tree_info']->referrer_name) ? $profileInfo['tree_info']->referrer_name : ''}}</p>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
							   <strong>{{trans('affiliate/account/profile.your_email')}}</strong>
							   <p class="text-muted">{{isset($profileInfo['userdetails']->email) ? $profileInfo['userdetails']->email : ''}}</p>
							</div>
							<div class="form-group">
							   <strong>{{trans('affiliate/general.mobile_no')}}</strong>
							   <p class="text-muted">{{isset($profileInfo['userdetails']->mobile) ? $profileInfo['userdetails']->mobile : ''}}</p>
							</div>
							<div class="form-group">
							   <strong>{{trans('affiliate/account/profile.invited_email')}}</strong>
							   <p class="text-muted">{{isset($profileInfo['tree_info']->referrer_email) ? $profileInfo['tree_info']->referrer_email : ''}}</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="image_upload" style="display:none">
		    <div class="col-md-8">	
			<!-- Upload Profile Image -->
				    @include('user.account.uploadprofileimage')
			</div>
		</div>
        <div class="row" id="location"> 
            <div class="col-md-3">		
				<!-- About Me Box -->
			    <div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-map-marker margin-r-5"></i>{{trans('affiliate/general.location')}}</h3>
					</div>
					<div class="box-body">
						<strong> {{trans('affiliate/account/profile.address')}}</strong>
						<p class="text-muted">{{isset($profileInfo['userdetails']->address) ? $profileInfo['userdetails']->address : ''}}</p>
					</div>	
				</div>
				<!-- /.box -->
			</div>
		</div>	
		@endif
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts') 
<script src="{{asset('affiliate/validate/lang/update_profile_image')}} " charset="utf-8"></script>
<script src="{{asset('resources/assets/themes/affiliate/plugins/cropper/cropper.js')}}" ></script>
<script src="{{asset('resources/assets/themes/affiliate/plugins/cropper/main.js')}}" ></script> 
<link rel="stylesheet" href="{{asset('resources/assets/themes/affiliate/plugins/cropper/cropper.css')}}" >
<script src="{{asset('js/providers/affiliate/account/updateprofileimage.js')}}" ></script> 
@stop  