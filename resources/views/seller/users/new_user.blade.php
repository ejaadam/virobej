@extends('seller.common.layout')
@section('pagetitle','Add User')
@section('breadCrumbs')
<li>In-stores</li>
<li>Manage Users</li>
<li>Add Users</li>
@stop
@section('layoutContent')   
	<div class="row" id="store-form-panel">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-info">
						<div class="panel-heading">
							<h4 class="panel-title">Add New user
							</h4>
						</div>
						<div class="panel-body">
							<div id="msg"></div>
							<form id="user-form" class="form form-horizontal" action="{{route('seller.manage_users.save_user')}}"  autocomplete="off">
					<div class="panel-body">
						<div class="col-sm-12">
							<br>
							<input class="form-control" id="country_id" name="address[country_id]" value="{{$country_id or ''}}" type="hidden">
							<div id="details" class="tab-pane active">
								<fieldset>								
									<span class="form-horizontal">
										<div class="form-group">
											<label for="mobile" class="control-label col-sm-2">Role* :</label>
											<div class="col-sm-3">
													<select name="role" class="form-control" id="role">	
														@foreach($access_level as $access)
															<option value="{{$access->access_id}}">{{$access->access_name}}</option>
															@endforeach
													</select>
											</div>
										</div>
										<div class="form-group">
												<label for="type" class="control-label col-sm-2">Full Name* :</label>
											<div class="col-sm-3">											
												<input type="text"  data-valuemissing="Full name is required." class="form-control" placeholder="Full Name" name="full_name" id="full_name" />												
											</div>
										</div>
										<div class="form-group">
											<label for="type" class="control-label col-sm-2">Email* :</label>
											<div class="input-group col-sm-3">
												<span class="input-group-addon"><i class="icon-envelope"></i>
												</span>
												<input type="email" data-typemismatch="The email you entered is not a valid email address." data-valuemissing="Business Email is required." class="form-control" placeholder="Email" name="email" id="email" />
											</div>
										</div>
										<div class="form-group">
											<label for="type" class="control-label col-sm-2">Phone Number* :</label>
											<div class="input-group col-sm-3">
												<span class="input-group-addon">{{$phonecode}}
												</span>
												<input type="text" maxlength="12" minlength="10" pattern="[0-9]{10}" data-valuemissing="Mobile is required." class="form-control" placeholder="Phone" name="mobile" id="mobile" />
											</div>
										</div>
										<div class="form-group">
											<label for="type" class="control-label col-sm-2">UserName* :</label>
											<div class="col-sm-3">
												<input type="text" onkeypress="return alphaBets_withspace(event)" class="form-control" placeholder="User Name" pattern="[A-Za-z]{3,100}" name="username" data-patternmismatch="User name format is invalid." id="username" />
											</div>
											
										</div>											
										<div class="form-group">
											<label for="type" class="control-label col-sm-2">Status* :</label>
											<div class="col-sm-3">
												<select name="status" id="status" class="form-control">
													<option value="1">Active</option>
													<option value="2">Inactive</option>
												</select>
											</div>
										</div>													
									</span>
								</fieldset>  
							</div>
						</div>
					</div>
						<div class="panel-footer text-right" id="save">
						 <button type="reset" class="btn btn-default">
								<i class="fa fa-save margin-r-5"></i>Cancel
							</button>&nbsp;
							<button type="button" class="btn btn-primary" id="submit">
								<i class="fa fa-save margin-r-5"></i>{{trans('general.btn.save')}}
							</button>
						</div>
				</form>	
						</div>					
					</div>
				</div>
			</div>
		</div>   
	</div>
@stop
@section('scripts')
<script src="{{asset('js/providers/seller/users/add_users.js')}}"></script>
<script src="{{asset('js/providers/seller/users/checkPincode.js')}}"></script>
@stop
