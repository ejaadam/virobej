@extends('seller.common.layout')
@section('pagetitle','Dashboard')
@section('breadCrumbs')
<li>Merchant</li>
<li>Shop Management</li>
@stop
@section('top-nav')	
@stop
@section('layoutContent')
<div class="row" id="store-list">
	<div class="col-sm-12">
		<!--<div class="user_heading">
			<div class="row">				
				<div class="col-sm-10">
					<div class="user_heading_info">						
						<h1>Shop Management</h1>
						<h2>Home - Merchant - Shop Management</h2>
					</div>
				</div>
			</div>
		</div>		-->	
		<div class="row">
			<div class="col-sm-12">
			    <div id="alt-msg"></div>
				<div class="panel panel-info">
					<div class="panel-heading">
						<h4 class="panel-title">Shop List
							<button type="button" id="add-store" class="btn btn-sm bg-aqua pull-right btn btn-success"><i class="icon-plus margin-r-5"></i>&nbsp;&nbsp; Add Shop</button>
						</h4>
					</div>
					<form id="store-list-form" class="form form-bordered" action="{{route('seller.outlet.list')}}" method="post">
						
					</form>
					<table id="store-list-table" class="table table-striped">
						<thead>
							<tr>
								<th>{{trans('general.label.created_on')}}</th>
								<th>{{trans('general.label.logo')}}</th>
								<th>{{trans('general.label.outlet_name')}}</th>
								<th>{{trans('general.label.country')}}</th>
								<th>{{trans('general.label.status')}}</th>
								<th>{{trans('general.label.approval')}}</th>
								<th></th>
							</tr>
						</thead>
						<tbody></tbody>										
					</table>
				</div>
			</div>
		</div>
	</div>   
</div>

<div class="row" id="store-form-panel" style="display: none;">
	<div class="col-sm-12">
	<!--	<div class="user_heading">
			<div class="row">				
				<div class="col-sm-10">
					<div class="user_heading_info">						
						<h1>Outlet Management</h1>
						<h2>Home - Merchant - Outlet Edit</h2>
					</div>
				</div>
			</div>
		</div>			-->
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-info">
					<div class="panel-heading">
						<h4 class="panel-title">Outlet Edit
						<div class="box-tools pull-right">
							<a href="#" class="btn bg-red btn-sm back-to-list btn btn-danger"><i class="fa fa-times margin-r-5"></i>Close</a>
						</div>
						</h4>
					</div>
					<div class="panel-body">
						<form id="store-form" class="form form-horizontal" action="{{route('seller.outlet.store-save-web')}}" encrpt="multipart/form-data">
							<div class="col-sm-12">
							    <input class="form-control" id="country_id" name="address[country_id]" value="{{$logged_userinfo->country_id or ''}}" type="hidden">
								<ul class="nav nav-tabs">
									<li class="active"><a data-toggle="tab" href="#details">{{trans('general.seller.outlet.details')}}</a></li>
									<li><a data-toggle="tab" href="#addres">{{trans('general.seller.outlet.address')}}</a></li>
									<li><a data-toggle="tab" href="#hours">{{trans('general.seller.outlet.working_hours')}}</a></li>
								</ul>
								<div class="tab-content">
									<div id="details" class="tab-pane active">
										<fieldset>								
											<span class="form-horizontal">
												<div class="form-group">
													<input type="hidden" name="store_id" id="store_id" />
													<input type="hidden" name="account_id" id="account_id"/>									
													<input type="hidden" name="status" id="status" />
													<input type="hidden" id="bcategory" name="bcategory_id" value="">
													<label for="type" class="control-label col-sm-2">{{trans('general.seller.outlet_name')}}*</label>
													<div class="col-sm-6">
														<input type="text" onkeypress="return alphaBets_withspace(event)" class="form-control" placeholder="{{trans('general.seller.outlet_name')}}" name="store_name" id="store_name" />
													</div>
												</div>													
												<!--<div class="form-group">
													<label class="col-sm-2 control-label">Logo* <br>(200px x 200px)</label>
													<div class="col-sm-10">
														<div data-provides="fileupload" class="fileupload fileupload-new ">
															<span class="btn btn-default btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span><input type="file" name="store_logo" onchange="loadFile(event)"></span>
															<a data-dismiss="fileupload" class="btn btn-default fileupload-exists" href="#">Remove</a>
															<div style="width: 50px; height: 50px;" class="fileupload-new img-thumbnail"><img src="{{asset(config('path.SELLER.STORE_IMG_PATH.DEFAULT'))}}" alt="" id="store_logo"></div>
															<div style="width: 50px; height: 50px;" class="fileupload-preview fileupload-exists img-thumbnail"></div>
														</div>  
													</div>
												</div>-->
												
												
												<div class="form-group">
													<label for="logo" class="col-sm-2 control-label">Logo(200px X 200px)</label>
													<input type="hidden" class="form-control" id="old_img" name="old_img" value="resources/uploads/seller/profile{{$supplier_details->profile_img or '/default-logo.png'}}" >
													<div class="col-sm-6">
														<div class="col-sm-3">
															<img class="img img-thumbnail editable-img" data-input="#logo" id="logo-preview" src="@if(!empty($store_logo)){{Config('path.SELLER.STORE_IMG_PATH.LOCAL').$store_logo}} @else resources/uploads/seller/profile/default-logo.png @endif" data-old-image=""/><br>
															<span id="logo-error"></span>
														</div>												
														<div class="col-sm-3">
															<div class="btn btn-sm btn-success mt-20 waves-effect">
																<span>Choose files</span>														
																<input type="file"  class="cropper ignore-reset" data-hide="#store-form-panel" name="store_logo"  accept="image/gif,image/jpg,image/jpeg,image/png" data-err-msg-to="#logo-error" data-typemismatch="Please select valid formet(*.gif, *.jpg, *.jpeg, *.png)" id="logo" title="Choose File" data-default="{{asset(config('constants.SELLER.LOGO_PATH.WEB').'store.jpg')}}" data-width="200" data-height="200" />
															</div>									
															</br></br>
															<button id="prof-image-reset" data-target="#old_img" data-preview="#logo-preview" class="btn btn-sm btn-warning reset_cropper" title="Remove Image" type="text" disabled="disabled"><i class="fa fa-refresh"></i> Reset</button>
														</div>												
													</div>
												</div>	
												
												
												<div class="form-group">
													<label for="mobile" class="control-label col-sm-2">{{trans('general.email')}}*</label>
													<div class="col-sm-6">
														<div class="input-group">															
															<input type="email" class="form-control" placeholder="Email" name="email" id="email" data-err-msg-to="#email_err"/>
														</div>
														<span id="email_err"></span>
													</div>
												</div>
												<div class="form-group">
													<label for="mobile" class="control-label col-sm-2">{{trans('general.seller.mobile')}}*</label>
													<div class="col-sm-6">
														<div class="input-group">											
															<input type="text" class="form-control" onkeypress="return isNumberKey(event)" placeholder="" name="mobile" id="mobile" data-err-msg-to="#mobile_err"/>
														</div>
														<span id="mobile_err"></span>
													</div>
												</div>
												<div class="form-group">
													<label for="mobile" class="control-label col-sm-2">{{trans('general.seller.phone')}}</label>
													<div class="col-sm-6">
														<div class="input-group">											
															<input type="text" class="form-control" onkeypress="return isNumberKey(event)" placeholder="" name="phone" id="phone" data-err-msg-to="#phone_err"/>
														</div> 
														<span id="phone_err"></span>
													</div>
												</div>                            
												<div class="form-group">
													<label for="mobile" class="control-label col-sm-2">{{trans('general.seller.outlet.title')}}*</label>
													<div class="col-sm-6">
														<input type="text" onkeypress="return alphaNumeric_withspace(event)" class="form-control" placeholder="{{trans('general.seller.outlet.title')}}" name="title" id="title" />
													</div>
												</div>
												<div class="form-group">
													<label for="mobile" class="control-label col-sm-2">{{trans('general.seller.outlet.description')}}*</label>
													<div class="col-sm-6">
														<textarea name="description" onkeypress="return alphaNumeric_specialchar(event)" class="form-control" placeholder="Description" id="description">
														</textarea>
													</div>
												</div>
											</span>
										</fieldset>  
									</div>
									<div id="addres" class="tab-pane">
										<fieldset>	
											<div class="form-group">
												<label class="col-sm-2 control-label">Flat/Address*</label>
												<div class="col-sm-10">
													<input type="text" class="form-control" id="flatno_street" name="address[address]" placeholder="Address" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-2 control-label">Landmark</label>
												<div class="col-sm-10">
													<input type="text" class="form-control" id="landmark" name="address[landmark]" placeholder="Landmark" >
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-2 control-label">Postal Code*</label>
												<div class="col-sm-10">
													<input type="hidden" id="country_idd" >
													<input type="hidden" id="district_id" name="address[district_id]" >
													<input type="text" class="form-control" id="postal_code" name="address[postal_code]" placeholder="Postal Code">
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-2 control-label">City/town*</label>
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
												<label class="col-sm-2 control-label">Country</label>
												<div class="col-sm-10">
													<select  name="country" id="country" class="form-control"></select>
												</div>
											</div>
										</fieldset> 
									</div>
									<div id="hours" class="tab-pane">
										<div class="form-group ">
											<div class="col-sm-12">
												<div class="col-sm-4">
													<div class="col-sm-10">														
														<label class="radio-inline" id="no_time"> <input type="radio" name="specify_working_hrs" class="specify_working_hrs" value="1"> I prefer not to specify operating hours</label><br>
														
														
														<label class="radio-inline" id="time">
														<input type="radio" name="specify_working_hrs" class="specify_working_hrs" value="3"> Specify operating hours </label>
														<span id="specify_working_hrs_error"></span>
													</div>
												</div>
												<div class="col-sm-3">
													
													<label class="radio-inline" id="global_time"><input type="radio" name="specify_working_hrs" class="specify_working_hrs" value="2"> Use global opening hours</label>
													<label class="checkbox-inline working_hrs" id="global_time">
														<input type="checkbox" name="split_working_hrs" id="split_working_hrs" class="split_working_hrs inline" value="1"/> Split Working Hours
													</label>
													<span id="#split_working_hrs-error"></span>
													
													
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12 working_hrs" style="display:none;" id="">
												<!-- Info box -->
												<div class="">
													<?php $weekdays = ['mon'=>'Monday', 'tue'=>'Tuesday', 'wed'=>'Wednesday', 'thu'=>'Thursday', 'fri'=>'Friday', 'sat'=>'Saturday', 'sun'=>'Sunday'];?>
													<div class="box-body">
														@foreach ($weekdays as $key => $value)
														<div class="form-group form-group{{$key}}">
															<div class="col-sm-1">
																<label ></label>
																<label class="control-label" ><br><span class="timeings">{{$value}}</span></label>
															</div>
															<div class="col-sm-2 is-closed-div">
																<label >&nbsp;</label>
																<div class="checkbox">
																	<label>
																		<input type="checkbox" name="operating_hrs[{{$key}}][closed]" id="operating_hrs[{{$key}}]" data="{{$key}}" value="1" class="closed"> 
																		Closed
																	</label>
																	<span class="col-sm-12" id="{{$key}}-closed"></span>
																</div>
															</div>
															<div class="col-sm-2 session-1">
																<label >From Time</label>
																<input type="time" class="form-control" name="operating_hrs[{{$key}}][0][from]" id="operating_hrs_{{$key}}_0_from" />
															</div>
															<div class="col-sm-2 session-1">
																<label >To Time</label>
																<input type="time" class="form-control" name="operating_hrs[{{$key}}][0][to]" id="operating_hrs_{{$key}}_0_to" />
															</div>
															<div class="col-sm-2 session-2" style="display:none;">
																<label >From Time</label>
																<input type="time" class="form-control" name="operating_hrs[{{$key}}][1][from]"  id="operating_hrs_{{$key}}_1_from"/>
															</div>
															<div class="col-sm-2 session-2" style="display:none;">
																<label >To Time</label>
																<input type="time" class="form-control" name="operating_hrs[{{$key}}][1][to]"  id="operating_hrs_{{$key}}_1_to"/>
															</div>
														</div>
														@endforeach
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="box-footer" id="save">
									<button type="submit" class="btn btn-primary pull-right" id="update-details">
										<i class="fa fa-save margin-r-5"></i>{{trans('general.btn.save')}}
									</button>
								</div>   
							</div>
						</form>
					</div>					
				</div>
			</div>
		</div>
	</div> 
</div>
@include('seller/common/cropper')
	<div class="" id="store_view_details" style="display:none;">
		@include('seller.outlet.view_outlet_details')
	</div>
@stop
@section('scripts')
@include('seller/common/cropper_css_js')
	<script src="{{asset('js/providers/seller/outlet/outlet_list.js')}}"></script>
	<!--<script src="{{asset('resources/supports/supplier/stores/store_list.js')}}"></script>-->
@stop
