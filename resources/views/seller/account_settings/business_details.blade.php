<div class="row">
<div class="col-sm-12">
		<div class="">
			<div class="row">
				<!--div class="col-sm-1">
				</div-->							
				<div class="col-sm-12">
					<form class="form-horizontal" id="general_details" action="{{route('seller.account-settings.general-details')}}" enctype="multipart/form-data">
						<div>
							<div class="panel panel-info">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a>
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
												<?php if(isset($supplier_details->supplier_code)) { ?>
												<div class="form-group">
														<label class="col-sm-2 control-label">Seller ID</label>
														<div class="col-sm-10">
															<span class="text-muted">{{$supplier_details->supplier_code or ''}}</span>
														</div>
													</div>
												<?php } ?>
													<div class="form-group">
														<label class="col-sm-2 control-label">{!!$fields['amst.uname']['label']!!}</label>
														<div class="col-sm-10">
															<input  {!!build_attribute($fields['amst.uname']['attr'])!!} value="{{$supplier_details->uname or ''}}" id="uname" readonly class="form-control"  />
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Your Name</label>
														<div class="col-sm-10">
															<input type="text" class="form-control" id="firstname" name="details[firstname]" placeholder="Name" value="{{$supplier_details->firstname.' '.$supplier_details->lastname}}" readonly>
														</div>
													</div>
													<div class="form-group">
														<label for="inputEmail" class="col-sm-2 control-label">Email</label>
														<input type="hidden" class="ignore-reset" id="is_email_verified" name="is_email_verified" value="{{$supplier_details->is_email_verified}}" data-url="{{route('seller.verify-email')}}">
														<div class="col-sm-10">
														<div class="input-group">
															<a class="input-group-addon" href="#"><i class="icon-envelope"></i></a>
															<input type="text" class="form-control" id="email" name="email" placeholder="Email" value="{{$supplier_details->email or ''}}" disabled>
															<a class="input-group-addon" href="#" id="editslugBtn"><i class="icon-edit"></i></a>
														</div>
														</div>
													</div>
													<div class="form-group">
														<label for="inputEmail" class="col-sm-2 control-label">Phone Number</label>
														<div class="col-sm-10">
															<div class="input-group">
															<a class="input-group-addon" href="#">{{$supplier_details->phonecode or ''}}</a>
																<input type="text" class="form-control" id="mobile" name="mobile" placeholder="Email" value="{{$supplier_details->mobile or ''}}" disabled>
															 <a class="input-group-addon" href="#" id="edit_mobile_detail"><i class="icon-edit"></i></a>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Date of Birth</label>
														<div class="col-sm-10">
														<div class="input-group">
															<input type="text" class="form-control datepicker" id="dob" name="details[dob]" value="{{$supplier_details->dob or ''}}"placeholder="DOB" >
															<span class="input-group-addon"><i class="icon icon-calendar"></i></span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Gender</label>
														<div class="col-sm-10">
															<select id="gender" name="details[gender]" class="form-control">
																<option value="1" @if ($supplier_details->gender == 1) {!! 'selected' !!} @endif >Male</option>
																<option value="2" @if ($supplier_details->gender == 2) {!! 'selected' !!} @endif >Female</option>
																<option value="3" @if ($supplier_details->gender == 3) {!! 'selected' !!} @endif >Transgender</option>																	
															</select>
														</div>
													</div>	
													<div class="form-group">
														<label for="logo" class="col-sm-2 control-label">Profile Image</label>
														<input type="hidden" class="form-control" id="old_img" name="old_img" value="resources/uploads/seller/profile{{$supplier_details->profile_img or '/default-logo.png'}}" >
														<div class="col-sm-6">
															<div class="col-sm-3">
																<img class="img img-thumbnail editable-img" data-input="#logo" id="logo-preview" src="resources/uploads/seller/profile{{$supplier_details->profile_img or '/default-logo.png'}}" data-old-image=""/><br>
																<span id="logo-error"></span>
															</div>												
															<!--<div class="col-sm-3">
																<div class="btn btn-sm btn-success mt-20 waves-effect">
																	<span>Choose files</span>														
																	<input type="file"  class="cropper ignore-reset" data-hide="#profile-panel" name="shop_image"  accept="image/gif,image/jpg,image/jpeg,image/png" data-err-msg-to="#logo-error" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="logo" title="Choose File" data-default="{{asset(config('constants.SELLER.LOGO_PATH.WEB').'store.jpg')}}" data-width="200" data-height="200" />
																</div>									
																</br></br>
																<button id="prof-image-reset" data-target="#old_img" data-preview="#logo-preview" class="btn btn-sm btn-warning reset_cropper" title="Remove Image" type="text" disabled="disabled"><i class="fa fa-refresh"></i> Reset</button>
															</div>-->										
														</div>
														
														<input type="file" style="display:none"  class="cropper ignore-reset" data-hide="#profile-panel" name="shop_image"  accept="image/gif,image/jpg,image/jpeg,image/png" data-err-msg-to="#logo-error" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="logo" title="Choose File" data-default="{{asset(config('constants.SELLER.LOGO_PATH.WEB').'store.jpg')}}" data-width="200" data-height="200"/>
													
													</div>															
												</span>
											</fieldset>     

															<div class="form-group">
																<label for="inputEmail" class="col-sm-2 control-label">Email</label>
																<input type="hidden" class="ignore-reset" id="is_email_verified" name="is_email_verified" value="{{$supplier_details->is_email_verified}}" data-url="{{route('seller.verify-email')}}">
																<div class="col-sm-10">
																<div class="input-group">
																    <a class="input-group-addon" href="#"><i class="icon-envelope"></i></a>
																	<input type="text" class="form-control" id="email" name="email" placeholder="Email" value="{{$supplier_details->email or ''}}" disabled>
																	<a class="input-group-addon" href="#" id="editslugBtn"><i class="icon-edit"></i></a>
																</div>
																</div>
															</div>
															<div class="form-group">
																<label for="inputEmail" class="col-sm-2 control-label">Phone Number</label>
																<div class="col-sm-10">
																	<div class="input-group">
																	<a class="input-group-addon" href="#">{{$supplier_details->phonecode or ''}}</a>
																		<input type="text" class="form-control"  id="mobile" name="mobile" placeholder="Email" value="{{$supplier_details->mobile or ''}}" disabled>
                                                                     <a class="input-group-addon" href="#" id="edit_mobile_detail"><i class="icon-edit"></i></a>
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
															<div class="form-group">
																<label for="logo" class="col-sm-2 control-label">Profile Image</label>
																<input type="hidden" class="form-control" id="old_img" name="old_img" value="resources/uploads/seller/profile{{$supplier_details->profile_img or '/default-logo.png'}}" >
																<div class="col-sm-6">
																	<div class="col-sm-3">
																		<img class="img img-thumbnail editable-img" data-input="#logo" id="logo-preview" src="resources/uploads/seller/profile{{$supplier_details->profile_img or '/default-logo.png'}}" data-old-image=""/><br>
																		<span id="logo-error"></span>
																	</div>												
																	<!--<div class="col-sm-3">
																		<div class="btn btn-sm btn-success mt-20 waves-effect">
																			<span>Choose files</span>														
																			<input type="file"  class="cropper ignore-reset" data-hide="#profile-panel" name="shop_image"  accept="image/gif,image/jpg,image/jpeg,image/png" data-err-msg-to="#logo-error" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="logo" title="Choose File" data-default="{{asset(config('constants.SELLER.LOGO_PATH.WEB').'store.jpg')}}" data-width="200" data-height="200" />
																		</div>									
																		</br></br>
																		<button id="prof-image-reset" data-target="#old_img" data-preview="#logo-preview" class="btn btn-sm btn-warning reset_cropper" title="Remove Image" type="text" disabled="disabled"><i class="fa fa-refresh"></i> Reset</button>
																	</div>-->										
																</div>
																
																<input type="file" style="display:none"  class="cropper ignore-reset" data-hide="#profile-panel" name="shop_image"  accept="image/gif,image/jpg,image/jpeg,image/png" data-err-msg-to="#logo-error" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="logo" title="Choose File" data-default="{{asset(config('constants.SELLER.LOGO_PATH.WEB').'store.jpg')}}" data-width="200" data-height="200"/>
															
															</div>															
														</span>
													</fieldset>     
												</div>
												<div class="col-sm-offset-2 col-sm-12">									
													<input type="button" value="Save" id="general_btn" class="btn btn-primary">
												</div>
											</div>

										</div>
									</div>
								</div>
							</div>										
						</div>
					</form>	
				</div>
			</div>
		</div>
	<!--/div-->
</div>			
</div>
<!--  Business Information  -->
<div class="row">
<div class="col-sm-12">
	<!--div class="panel panel-default"-->
		<div class="">
			<div class="row">
				<div class="col-sm-12">
					<form class="form-horizontal" id="business_details" action="{{route('seller.account-settings.business-details')}}" enctype="multipart/form-data">
						<div>									
							<div class="panel panel-info">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a>
											Business Information
										</a>
									</h4>
								</div>
								<div id="acc2_collapseTwo" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="col-sm-12">
											<fieldset>				
												<input class="form-control" id="country_id"  value="{{$country_id}}" type="hidden">
												<span class="form-horizontal">
													<div class="form-group">
														<label for="inputEmail" class="col-sm-2 control-label">Legal Name / Company Name*</label>
														<div class="col-sm-10">
															<input type="text" class="form-control" id="bussiness_name" name="bussiness_name" placeholder="Business Name" value="{{$supplier_details->company_name or ''}}" readonly>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Business Filing Status*</label>
														<div class="col-sm-10">
															<select id="business_filing_status" name="mrmst[business_filing_status]" class="form-control">
																<option value="" hidden="hidden">-Select Business Filing Status-</option>
																@foreach($business_filing_status as $status)
																<option value="{{$status->bfs_id}}"  {{(isset($supplier_details->bfs) && $status->bfs_id == $supplier_details->bfs)?'selected="selected"':'' }}>{{$status->status}}</option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Address*</label>
														<div class="col-sm-10">
															<input type="text" class="form-control" id="address" name="address[address]" placeholder="Business Address" value="{{$supplier_details->account_address2 or ''}}" >
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Postal Code*</label>
														<div class="col-sm-10">
															<input type="text" class="form-control" id="postal_code" name="address[postal_code]" placeholder="Postal Code" value="{{$supplier_details->postal_code or ''}}">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">City*</label>
														<div class="col-sm-10">
															<select  name="address[city_id]" id="city_id" class="form-control"></select>
														</div>
													</div>	
													<div class="form-group">
														<label class="col-sm-2 control-label">State*</label>
														<div class="col-sm-10">
															<select  name="address[state_id]" id="state_id" class="form-control"></select>
														</div>
													</div>
													<div class="form-group">
														<label for="inputEmail" class="col-sm-2 control-label">Website URL*</label>
														<div class="col-sm-10">
															<input type="text" class="form-control website" id="website" name="mrmst[website]" placeholder="Website" value="{{$supplier_details->website or ''}}" >
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Business Phone*</label>
														<div class="col-sm-10">																	
															<div class="input-group">
																<span class="input-group-addon">{{$supplier_details->phonecode or ''}}</span>
																<input class="form-control" id="office_phone" name="mrmst[office_phone]" type="text" placeholder="Bussiness Phone" value="{{$supplier_details->office_phone or ''}}">
															</div>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Person inCharge</label>
														<div class="col-sm-10">																	
															<input class="form-control" id="in-charge" name="mrmst[person_in_charge]" type="text" placeholder="Person in Charge" value="{{$supplier_details->person_in_charge or ''}}">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Business Registration No</label>
														<div class="col-sm-10">																	
															<input class="form-control" id="reg_no" name="mrmst[reg_no]" type="text" placeholder="Business Registration No" value="{{$supplier_details->reg_no or ''}}">
														</div>
													</div>
													
													<div class="form-group">
														<label class="col-sm-2 control-label">Type of Services*</label>
														<div class="col-sm-10">																	
															<select id="service_type" name="mrmst[service_type]" class="form-control">
																<option value="1" @if ($supplier_details->service_type == 1) {!! 'selected' !!} @endif >Offline Sales</option>
																<option value="2" @if ($supplier_details->service_type == 2) {!! 'selected' !!} @endif >Online Sales</option>
																<option value="3" @if ($supplier_details->service_type == 3) {!! 'selected' !!} @endif >Both</option>
															</select>																	
														</div>
													</div>
													<div class="form-group" id="cate_fld">
														<label class="col-sm-2 control-label">Category</label>
														<div class="col-sm-10">
															<span class="text-info"><strong>{{$supplier_details->parent_category}}</strong></span>
														</div>
													</div>
													<div class="form-group" id="cate_fld">
														<label class="col-sm-2 control-label">Sub Category</label>
														<div class="col-sm-10">
															<input type="text" id="form-tags-4" name="tags" class="form-control" value="{{$supplier_details->tags}}">
														</div>
													</div>
													<div class="form-group">
													   <label class="col-sm-2 control-label">Is your Operating address same as business address? - Yes/No  </label>
														<div class="col-sm-10">
														 <label class="checkbox-inline">
															<input type="checkbox" class="form-control" value="1" name="business_status" id="business_status">
															 </label>
														</div>
													</div>
											<div id="operating_address_status">
													<div class="form-group">
														<label class="col-sm-2 control-label">Operating address*</label>
														<div class="col-sm-10">
															<input type="text" class="form-control" id="operating_address" name="operating_address[address]" placeholder="Operating Address" value="" >
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">Postal Code*</label>
														<div class="col-sm-10">
															<input type="text" class="form-control" id="operating_address_postal_code" name="operating_address[postal_code]" placeholder="Postal Code" value="">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label">City*</label>
														<div class="col-sm-10">
															<select  name="operating_address[city_id]" id="operating_address_city_id" class="form-control"></select>
														</div>
													</div>	
													<div class="form-group">
														<label class="col-sm-2 control-label">State*</label>
														<div class="col-sm-10">
															<select  name="operating_address[state_id]" id="operating_address_state_id" class="form-control"></select>
														</div>
													</div>
												</div>
													<!--<div class="row">
														<div class="col-sm-6 clearfix">
															<h4 class="heading_a">Business Categories</h4>
															<div class="sepH_a">
																<select id="s2_ext_value" class="form-control" multiple>
																</select>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-2 control-label"></label>
														<div class="col-sm-10">																	
															<div class="checkbox">
																<label>
																	<input type="checkbox" value="1" name="op_address">
																	Is your Operating address same as business address
																</label>
															</div>
														</div>
													</div-->		
												</span>
											</fieldset>     
										</div>												
									</div>
								</div>
							</div>
							<div class="panel panel-info">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a>
											Shop Information
										</a>
									</h4>
								</div>
								<div id="acc2_collapseThree" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="col-sm-12">
											<fieldset>														
												<div class="form-group">
													<label for="logo" class="col-sm-3 control-label">Logo</label>
													<input type="hidden" class="form-control" id="store_logo_old" name="store_logo_old" value="resources/uploads/seller/store{{$supplier_details->mr_logo or asset(config('constants.SELLER.LOGO_PATH.DEFAULT'))}}" >
													<div class="col-sm-6">
														<div class="col-sm-3">
															<img class="img img-thumbnail editable-img" data-input="#logo" id="store_logo-preview" src="resources/uploads/seller/store{{$supplier_details->mr_logo or asset(config('constants.SELLER.LOGO_PATH.DEFAULT'))}}" data-old-image=""/><br>
															<span id="logo-error"></span>
														</div>												
														<div class="col-sm-4">
															<div class="btn btn-sm btn-success mt-20 waves-effect">
																<span>Choose files</span>														
																<input type="file"  class="cropper ignore-reset" data-hide="#profile-panel" name="shop_image"  accept="image/gif,image/jpg,image/jpeg,image/png" data-err-msg-to="#logo-error" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="store_logo" title="Choose File" data-default="{{asset(config('constants.SELLER.LOGO_PATH.WEB').'store.jpg')}}" data-width="300" data-height="300" />
															</div>									
															</br></br>
															<button id="store-logo-reset" data-target="#store_logo_old" data-preview="#store_logo-preview" class="btn btn-sm btn-warning reset_cropper" title="Remove Image" type="text" disabled="disabled"><i class="fa fa-refresh"></i> Reset</button>
														</div>												
													</div>
												</div>
												<div class="form-group">
													<form name="store-img" id="store-img" enctype="multipart/form-data" >	
														<label for="store_image" class="col-sm-3 control-label">Store Images</label>
														<div class="col-sm-6">
														<input type="hidden" id="gal_image_old" value="{{asset(config('constants.SELLER.LOGO_PATH.DEFAULT'))}}">
															<div class="col-sm-3">
																<img class="img img-thumbnail editable-img" data-input="#gal_image" id="gal_image-preview" src="{{asset(config('constants.SELLER.LOGO_PATH.DEFAULT'))}}" data-old-image="" /><br>
																<span id="logo-error2"></span>
															</div>												
															<div class="col-sm-4">
																<div class="btn btn-sm btn-success mt-20 waves-effect">
																	<span>Choose files</span>														
																	<input type="file" class="ignore-reset" data-hide="#profile-panel" name="store_image"  accept="image/gif,image/jpg,image/jpeg,image/png" data-err-msg-to="#logo-error2" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="gal_image" title="Choose File" data-default="{{asset(config('constants.SELLER.LOGO_PATH.WEB').'store.jpg')}}" data-width="700" data-height="450" />
																</div>									
																</br></br>
																<button id="upload_images" rel="{{Route('seller.account-settings.upload-store-images')}}" class="btn btn-sm btn-success" title="Upload Iamge" type="button"><i class="fa fa-refresh"></i>Upload</button>&nbsp;&nbsp;
																<button id="clear" class="btn btn-sm btn-default reset_cropper" data-target="#gal_image_old" data-preview="#gal_image-preview" type="button"><i class="fa fa-refresh"></i>Clear</button>
															</div>	
														</div>
													</form>
												</div>
												<span class="form-horizontal">															
													<div class="form-group">
														<label class="col-sm-2 control-label">Banner Image <br>(960px X 200px)</label>
														<div class="col-sm-10">
															<div data-provides="fileupload" class="fileupload fileupload-new ">
																<span class="btn btn-default btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span><input type="file" id="banner_image" name="banner_image" onchange="loadFile3(event)"></span>
																<a data-dismiss="fileupload" class="btn btn-default fileupload-exists" href="#">Remove</a>
																<div style="width: 50px; height: 50px;" class="fileupload-new img-thumbnail"><img src="resources/uploads/seller/banner/{{$supplier_details->banner or '/store.jpg'}}" alt="" id="banner"></div>
																<div style="width: 50px; height: 50px;" class="fileupload-preview fileupload-exists img-thumbnail"></div>
															</div>  
														</div>
													</div>																
												</span>
												<div class="form-group">
													<label class="col-sm-2 control-label">Store Galler Images </label>
													<div class="col-sm-10 well">
														@if(isset($store_images) && !empty($store_images))
															@foreach($store_images as $images)
																<div class="col-md-2"><image style="width:150px;height:75px; padding:10px" src="{{config('path.SELLER.STORE_IMG_PATH.IMAGES').$images->file_path}}">
															</div>	
															@endforeach
														@endif
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-2 control-label">Business Description* </label>
													<div class="col-sm-10">
														<textarea class="form-control" name="mrmst[description]" id="description" cols="70" rows="3">{{$supplier_details->description or ''}}</textarea>
													</div>
												</div>														
											</fieldset>     
										</div>			
										<div class="col-sm-offset-2 col-sm-12">									
											<input type="submit" value="Save" class="btn btn-primary">
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>								
				</div>
			</div>
		</div>
	<!--/div-->
</div>
</div>
<div id="general_details_modal" class="modal fade" role="dialog" >
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Change Email</h4>
		</div>
		<div class="modal-body">
			<div id="accErr"></div>
			@include('seller.account_settings.change_email')
		</div>
	</div>
</div>
</div>	
<script src="{{asset('js/providers/seller/account_settings.js')}}"></script>
<script src="{{asset('resources/assets/plugins/cropper/cropper-main.js')}}"></script>
<script src="{{asset('resources/assets/plugins/tagsAutocomplete/src/jquery.tagsinput-revisited.js')}}"></script>
<script>
var state_id = <?php echo !empty($supplier_details->state_id)?$supplier_details->state_id:0?>;
var city_id = <?php echo  !empty($supplier_details->city_id)?$supplier_details->city_id:0?>;


/* $('.tagsInput').fastsearch(); */

/* $('.tagsInput').fastsearch({
'noResultsText': 'No results found',
'onItemCreate': function($item, model, fastsearchApi){
$item.append(model.subtitle);
}
});  */
$(document).ready(function(){
var sourceTags = [];

$('#form-tags-4').tagsInput({
'autocomplete': {
	source: function(request,response) {
		$.ajax({
			url: "seller/get_tags",
			dataType: "JSON",
			data: { term: request.term },
			success: function( data ) {
			   response( $.map( data.tags, function(item) {
					return {
						label: item,
						value: item
					}
				}));
			}
		});
	}
}
});
$("#dob").datepicker();
/* -------------------------- */
/*	
$("#mytags").tagit({
		autocomplete: { source: function( request, response ) {
		$.ajax({
			url: "http://ws.geonames.org/searchJSON",
			dataType: "jsonp",
			data: {
				featureClass: "P",
				style: "full",
				maxRows: 12,
				name_startsWith: request.term
			},
			success: function( data ) {
				response( $.map( data.geonames, function( item ) {
					return {
						label: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName,
						value: item.name
					}
				}));
			}
		});
	},
	minLength: 2 
			}
		});
*/




});
</script>