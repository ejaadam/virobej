@extends('seller.common.layout')
@section('pagetitle','Manage Users')
@section('breadCrumbs')
<li>Merchant</li>
<li>Manage Users</li>
@stop
@section('layoutContent')

    <div class="row">
        <div id="alert-div"></div>
        <div class="col-sm-12">
            <div class="panel panel-default report">
		
                <div class="panel-heading">
				<div class="col-sm-4">
                    <h4 class="panel-title">Manage Users</h4>
                      </div>
				     <form class="form-horizontal" action="{{route('seller.manage_users.user_list')}}" method="post" id="manage_user_list_form">
					<div class="row pull-right">
						<div class="col-sm-3">
							<button id ="add_user" type="button" class="btn btn-sm btn-primary" title="Search"><span class="icon-plus-sign"></span>Add User</button>
						</div>
						<div class="col-sm-4">
						 <div class="input-group">
						  <input type="text"  placeholder="Email" name="search_term" id="search_term" class="form-control">
						  <!--<button id ="search" type="button" class="" title="Search"> <i class="icon-search"></i> </button>-->
						  <span id ="search"  class="input-group-addon" title="Search"><i class="icon-search"></i></span>
						   </div>
						  </div>
						  
						</div>
						</form>
                </div>
			
                <table id="manage_user_list_table" class="table table-striped">
                    <thead>
                        <tr>
						    <th>Full name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>User Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
	        </div>
			<div class="panel panel-default add_user" style="display:none">
                <div class="panel-heading">
                <h4 class="panel-title"><span id="title_user">Add Users</span> <button class="back btn btn-sm -btn-warning pull-right btn-danger"><i class="icon-arrow-left"></i>&nbsp;Back</button></h4>
                </div>
				
				<form id="user-form" class="form form-horizontal" action="{{route('seller.manage_users.save_user')}}"  autocomplete="off">
					<div class="panel-body">
						<div class="col-sm-12">
							<br>
							<div id="msg"></div>
							<input class="form-control" id="country_id" name="address[country_id]" value="{{$country_id or ''}}" type="hidden">
							<div id="details" class="tab-pane active">
								<fieldset>	
									<input type="hidden" name="account_id" id="account_id"> 
									<span class="form-horizontal col-sm-6">
										<div class="form-group">
											<div class="col-sm-4">
											<label for="mobile" class="control-label">Role*</label>
											</div>
											<div class="col-sm-8">
												<select name="role" class="form-control" id="role">	
													@foreach($access_level as $access)
														@if($access->access_id != 1)
															<option value="{{$access->access_id}}">{{$access->access_name}}</option>
														@endif
														@endforeach
												</select>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-4">
												<label for="type" class="control-label">Full Name* :</label>
											</div>
											<div class="col-sm-8">											
												<input type="text"  data-valuemissing="Full name is required." class="form-control" placeholder="Full Name" name="full_name" id="full_name"  onkeypress="return alphaBets_withspace(event)" >												
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-4">
												<label for="type" class="control-label">Email* :</label>
											</div>
											<div class="col-sm-8">
												<div class="input-group">
													<span class="input-group-addon"><i class="icon-envelope"></i>
													</span>
													<input type="email" data-typemismatch="The email you entered is not a valid email address." data-valuemissing="Business Email is required." class="form-control" placeholder="Email" name="email" id="email" data-err-msg-to="#email_err"/ onkeypress="return RestrictSpace(event)">
												</div>
												<span id="email_err"></span>	
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-4">
												<label for="type" class="control-label">Phone Number* :</label>
											</div>
											<div class="col-sm-8">
												<div class="input-group">
													<span class="input-group-addon">{{$phonecode}}
											</span>
											<input type="text" maxlength="12" minlength="10" pattern="[0-9]{10}" data-valuemissing="Mobile is required." class="form-control" placeholder="Phone" name="mobile" id="mobile" data-err-msg-to="#phone_err" onkeypress="return isNumberKey(event)" />
												</div>
												<span id="phone_err"></span>
											</div>
												
										</div>
										<div class="form-group">
											<div class="col-sm-4">
												<label for="type" class="control-label">Username* :</label>
											</div>
											<div class="col-sm-8">
												<input type="text" class="form-control" placeholder="Username" pattern="[A-Za-z]{3,100}" name="username" data-patternmismatch="User name format is invalid." id="username" onkeypress="return alphaBets(event)" />
											</div>											
										</div>											
										<div class="form-group">
											<label for="type" class="control-label col-sm-4">Status* :</label>
											<div class="col-sm-8">
												<select name="status" id="status" class="form-control">
													<option value="1">Active</option>
													<option value="2">Inactive</option>
												</select>
											</div>
										<!--	<div class="col-sm-8">
											     <label class="checkbox-inline">
												<input type="checkbox" class="form-control" value="1" name="status" id="status" />
												 </label>
											</div>	-->									
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
								<i class="fa fa-save margin-r-5"></i>Submit
							</button>
						</div>
				</form>	
			</div>
			<div class="col-sm-8" id="supplier-stores-list" style="display:none;">
				@include('seller.users.stores_list')
			</div>
        </div>
		
    </div>
	
<div id="reset_password_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Reset Password</h4>
            </div>
            <div class="modal-body">
                <div id="accErr"></div>
                @include('seller.users.reset_password')
            </div>
        </div>
    </div>
</div>

@stop
@section('scripts')
<script src="{{asset('js/providers/seller/users/manage_user_list.js')}}"></script>
<script src="{{asset('js/providers/seller/users/add_users.js')}}"></script>
@stop
