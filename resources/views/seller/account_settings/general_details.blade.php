@extends('seller.common.layout')
@section('pagetitle','General Details')
@section('top-nav')
@stop
@section('layoutContent')
<div class="row">
	<div class="col-sm-12">
		<div class="user_heading">
			<div class="row">				
				<div class="col-sm-10">
					<div class="user_heading_info">						
						<h1>Seller Account Information</h1>
						<h6>Home > Account Settings > Seller Account Information</h6>
					</div>
				</div>
			</div>
		</div>	
		<div class="row" id="profile-panel">
			<div class="col-sm-12">
				<div class="panel panel-default">
					<!--div class="panel-heading">
						<h4 class="panel-title">Edit Seller</h4>
					</div-->
					<div class="">
						<div class="row">
							<!--div class="col-sm-1">
							</div-->							
							<div class="col-sm-12">
								<form class="form-horizontal" id="general_details" action="{{route('seller.account-settings.general-details')}}" enctype="multipart/form-data">
									<div class="panel-group" id="accordion2">
										<div class="panel panel-info">
											<div class="panel-heading">
												<h4 class="panel-title">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#acc2_collapseOne">
														Seller Account Information
													</a>
												</h4>
											</div>
											<div id="acc2_collapseOne" class="panel-collapse collapse in">
												<div class="panel-body">
													<div class="col-sm-12">
														<fieldset>
															<!--legend><span>General Details</span></legend-->
															<span class="form-horizontal">
																<div class="form-group">
																	<label class="col-sm-2 control-label">{!!$fields['amst.uname']['label']!!}</label>
																	<div class="col-sm-10">
																		<input  {!!build_attribute($fields['amst.uname']['attr'])!!} value="{{$supplier_details->uname or ''}}" id="uname" class="form-control"  />
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-2 control-label">Name on ID</label>
																	<div class="col-sm-10">
																		<input type="text" class="form-control" id="firstname" name="details[firstname]" placeholder="Name" value="{{$supplier_details->firstname or ''}}" readonly>
																	</div>
																</div>
												
																<div class="form-group">
																	<label for="inputEmail" class="col-sm-2 control-label">Email</label>
																	<div class="col-sm-10">
																	<div class="input-group">
																		<input type="text" class="form-control" id="email" name="email" placeholder="Email" value="{{$supplier_details->email or ''}}" readonly>
																		<a class="input-group-addon" href="#" id="editslugBtn"><i class="fa fa-edit"></i></a>
																	</div>
																	</div>
																</div>
																<div class="form-group">
																	<label for="inputEmail" class="col-sm-2 control-label">Phone Number</label>
																	<div class="col-sm-10">
																	<div class="input-group">
																	<a class="input-group-addon" href="#" id="editslugBtn">{{$supplier_details->phonecode or ''}}</a>
																		<input type="text" class="form-control" id="email" name="email" placeholder="Email" value="{{$supplier_details->mobile or ''}}" readonly>
																		
																	</div>
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-2 control-label">Date of Birth</label>
																	<div class="col-sm-10">
																		<input type="date" class="form-control" id="dob" name="details[dob]" value="{{$supplier_details->dob or ''}}"placeholder="DOB">
																	</div>
																</div>
																<div class="form-group">
																	<label class="col-sm-2 control-label">Gender</label>
																	<div class="col-sm-10">
																		<select id="gender" name="details[gender]" class="form-control">
																			<option value="1" @if ($supplier_details->gender == 1) {!! 'selected' !!} @endif >Male</option>
																			<option value="2" @if ($supplier_details->gender == 2) {!! 'selected' !!} @endif >Female</option>
																			<option value="3" @if ($supplier_details->gender == 3) {!! 'selected' !!} @endif >Others</option>																	
																		</select>
																	</div>
																</div>	
																<!--<div class="form-group">
																	<label class="col-sm-2 control-label">Profile Image <br>(200px x 200px)</label>
																	<div class="col-sm-10">
																		<div data-provides="fileupload" class="fileupload fileupload-new ">
																			<span class="btn btn-default btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span><input type="file" id="profile_image" name="profile_image" onchange="loadFile(event)"></span>
																			<a data-dismiss="fileupload" class="btn btn-default fileupload-exists" href="#">Remove</a>
																			<div style="width: 50px; height: 50px;" class="fileupload-new img-thumbnail"><img src="resources/uploads/seller/profile{{$supplier_details->profile_img or '/default-logo.png'}} " alt="" id="profile"></div>
																			<div style="width: 50px; height: 50px;" class="fileupload-preview fileupload-exists img-thumbnail"></div>
																		</div>  
																	</div>
																</div>-->	

														<div class="form-group">
															<label for="logo" class="col-sm-2 control-label">Profile Image</label>
															<input type="hidden" class="form-control" id="old_img" name="old_img" value="" >
															<div class="col-sm-6">
																<div class="col-sm-3">
																	<img class="img img-thumbnail editable-img" data-input="#logo" id="logo-preview" src="resources/uploads/seller/profile{{$supplier_details->profile_img or '/default-logo.png'}}" data-old-image=""/><br>
																	<span id="profileimg-error"></span>
																</div>												
																<div class="col-sm-3">
																	<div class="btn btn-sm btn-success mt-20 waves-effect">
																		<span>Choose files</span>														
																		<input type="file"  class="cropper ignore-reset" data-hide="#profile-panel" name="logo"  accept="image/gif,image/jpg,image/jpeg,image/png" data-err-msg-to="#logo-error" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="logo" title="Choose File" data-default="{{asset(config('constants.SELLER.LOGO_PATH.WEB').'store.jpg')}}" data-width="200" data-height="200" />
																	</div>									
																	</br></br>
																	<button id="prof-image-reset" class="btn btn-sm btn-warning" title="Remove Image" type="text" disabled="disabled"><i class="fa fa-refresh"></i> Reset</button>
																</div>												
															</div>
														</div>															
															</span>
														</fieldset>     
													</div>
													<div class="col-sm-12">									
														<input type="submit" value="Save" class="btn btn-primary pull-right">
													</div>
												</div>
											</div>
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
	
	@include('seller/common/cropper');
@include('seller/common/cropper_css_js');
</div>
@include('seller.common.assets')
<script src="{{asset('js/providers/seller/account_settings.js')}}"></script>
@stop
