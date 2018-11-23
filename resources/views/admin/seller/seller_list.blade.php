@extends('admin.common.layout')
@section('pagetitle')
Suppliers List
@stop
@section('top_navigation')
@include('admin.top_nav.supplier_navigation')
@stop
@section('layoutContent')
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default" id="retailer_listf">
            <div class="panel-heading">
<!--                <a href="{{URL::to('admin/suppliers/add')}}" id="create_supplier" class="btn btn-success btn-sm pull-right"><span class="icon-plus"></span>Add Suppliers </a>-->
                <h4 class="panel-title">Seller List </h4>
            </div>
            <div class="panel_controls">
                <div class="row">
					<form id="retailers_listfrm" class="form form-bordered" action="{{$route}}" method="post">						
							 
							<!--  Country List -->
							<div class="col-md-4">
								<label for="from"> {{trans('admin/general.country')}}</label>
								<select name="country" id="country" class="form-control">
									<option value="">{{trans('admin/general.country_search')}}</option>
									@if(!empty($country_list))
									@foreach ($country_list as $row)
									<option value="{{$row->country_id}}" {{ (isset($country) && $country == $row->country_id) ? 'selected':''}}>{{$row->country}}</option>
									@endforeach
									@endif
								</select>
							</div>
							<!--  Business Category List -->
							<div class="input-group col-md-4">
								<label for="from"> {{trans('admin/seller.business_category')}}</label>
								<select name="bcategory" id="bcategory" class="form-control">
									<option value="">{{trans('admin/seller.business_category_search')}}</option>
									@if(!empty($bcategory_list))
									@foreach ($bcategory_list as $row)
									<option value="{{$row->bcategory_id}}" {{ (isset($bcategory) && $bcategory == $row->bcategory_id) ? 'selected':''}}>{{$row->bcategory_name}}</option>
									@endforeach
									@endif
								</select>
							</div>
						
							<div class="input-group col-sm-4">
								<label for="search_term"> {{trans('admin/general.search_term')}} </label>
								<div class="">
									<input type="search" id="search_term" name="search_term" class="form-control" placeholder="{{trans('admin/general.search_term_ph')}}" value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" />
									<div class="input-group-btn ">
										<button data-toggle="dropdown" class="btn btn-default" aria-expanded="true">{{trans('admin/general.filter')}} <span class="caret"></span>
										</button>
										<ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right" id="chkbox">
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_code" type="checkbox" checked>	&nbsp;{{trans('admin/seller.merchant_code')}}</label>
											</li>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_name" type="checkbox">	&nbsp;{{trans('admin/seller.merchant_name')}}</label>
											</li>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_mobile" type="checkbox">	&nbsp;{{trans('admin/seller.mobile')}}</label>
											</li>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_email" type="checkbox">	&nbsp;{{trans('admin/seller.email')}}</label>
											</li>
											<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="merchant_uname" type="checkbox">	&nbsp;{{trans('admin/seller.uname')}}</label>
											</li>
										</ul>
									</div>
								</div>
							</div> 
						
							<div class="input-group col-sm-3">
								<div class="form-group has-feedback">
									<label for="from_date"> {{trans('admin/general.frm_date')}}</label>
									<input type="date" id="from_date" name="from_date" class="form-control datepicker" /> 
								</div>
							</div>
							<div class="input-group col-sm-3">
								<div class="form-group has-feedback">
									<label for="to_date"> {{trans('admin/general.to_date')}}</label>
									<input type="date" id="to_date" name="to_date" class="form-control datepicker"/>
								</div>
							</div>
							<div class="input-group col-sm-6">
								<div class="form-group" style="margin-top:25px;">
									<button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i>     {{trans('general.btn.search')}}</button>&nbsp;
									<button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i>     {{trans('general.btn.reset')}}</button>
								</div>
							</div>
						
					</form>
                </div>
            </div>
            
			<div class="panel_controls">
				<table id="retailer" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>{{trans('general.label.created_on')}}</th>
							<th>{{trans('admin/seller.merchant_code')}}</th>
							<th>{{trans('admin/seller.merchant_name')}}</th>
							<th>{{trans('admin/seller.business_category')}}</th>
							<th>{{trans('admin/seller.country')}}</th>
							<th>{{trans('admin/general.status')}}</th>
							<th>{{trans('admin/seller.activated_on')}}</th>
							<th></th>
						</tr>
					</thead>
					<tbody> </tbody>
				</table>
			</div>
			
            
        </div>
        @include('admin.meta-info')
    </div>
</div>
<div class="modal fade" id="suppliers_details" tabindex="-1" role="dialog" aria-labelledby="suppliers_detailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Suppliers Details</h4>
            </div>
            <div class="modal-body"> </div>
        </div>
    </div>
</div>
<div class="modal fade " id="suppliers_rpwd" tabindex="-1" role="dialog" aria-labelledby="suppliers_detailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> Supplier Reset Password</h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <form class="form-horizontal" id="suppliers_reset_pwd">
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">Supplier Name</label>
                            <div class="col-sm-8">
                                <p class="form-control-static" id="uname"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">User Name</label>
                            <div class="col-sm-8">
                                <p class="form-control-static" id="sid"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">New Password</label>
                            <div class="col-sm-8">
                                <input name="login_password" class="form-control" type="password" id="login_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">Confirm New Password</label>
                            <div class="col-sm-8">
                                <input name="confirm_login_password" class="form-control" type="password" id="confirm_login_password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="col-sm-4">&nbsp;</label>
                            <div class="col-sm-8">
                                <input type="submit" name="save" id="save" class="btn btn-sm btn-primary" value="Update" >
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_data" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Edit Supplier Details</h4>
            </div>
            <div class="modal-body"> </div>
        </div>
    </div>
</div>
@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('resources/supports/Jquery-loadselect.js')}}"></script>	
<script src="{{asset('resources/supports/admin/seller/seller_list.js')}}"></script>	
<script src="{{asset('resources/supports/admin/seller/meta-info.js')}}"></script>	
@stop
