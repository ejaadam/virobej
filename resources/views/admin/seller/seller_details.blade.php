@extends('admin.common.layout')
@section('pagetitle')
Suppliers Details
@stop
@section('layoutContent')

@if(!empty($retailerInfo) )
	@if(!empty($retailerInfo->activationSteps))
		<div class="col-md-12">
			<div class="panel panel-default" id="account-info">
				<div class="panel-heading">
					<!--i class="fa fa-user"></i><h3 class="box-title">{{trans('retailer/account.activate_account')}}</h3-->
					 <h4 class="panel-title">{{trans('admin/seller.activate_account')}}</h4>
				</div>
				<div class="panel-body">
					<ul class="list-unstyled actions-list">
						@foreach($retailerInfo->activationSteps as $step)
						<li><i class="{{$step->is_completed?'text-success fa fa-check-circle':'text-danger fa fa-times-circle'}}"></i><span>&nbsp;&nbsp;{{$step->step}}</span></li>
						@endforeach
					</ul>
				</div>
			</div>
		</div>
	@endif
	<br>
	<div class="col-md-3">
		<div class="panel panel-default" id="merchant_profile">
			<div class="panel-body box-profile">
				<div class="row " >
					@if(isset($retailerInfo->mrlogo) && !empty($retailerInfo->mrlogo))
					<!-- <img class="profile-merchant-logo img-responsive img-circle " style="margin-left: 75px;" height="100" width="100" src="<?php echo url(''.config('constants.MERCHANT.LOGO_PATH.WEB').$retailerInfo->mrlogo);?>" alt="{{$retailerInfo->mrlogo}}" >  -->
					<img class="profile-merchant-logo img-responsive img-circle" style="margin-left: 75px;" height="100" width="100" src="{{asset(config('path.MERCHANT.LOGO_PATH.DEFAULT'))}}" alt="{{trans('admin/seller.details.logo')}}">
					@else
					<img class="profile-merchant-logo img-responsive img-circle" style="margin-left: 75px;" height="100" width="100" src="{{asset(config('path.MERCHANT.LOGO_PATH.DEFAULT'))}}" alt="{{trans('admin/seller.details.logo')}}">
					@endif
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<strong>{{trans('admin/seller.merchant_name')}}</strong>
						<p class="text-muted">{{isset($retailerInfo->mrbusiness_name) ? $retailerInfo->mrbusiness_name : ''}}</p>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<strong>{{trans('admin/seller.merchant_code')}}</strong>
						<p class="text-muted" id="suppliercode">{{isset($retailerInfo->mrcode) ? $retailerInfo->mrcode : ''}}</p>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<strong>{{trans('general.label.created_on')}}</strong>
						<p class="text-muted">{{isset($retailerInfo->created_on) ? $retailerInfo->created_on : ''}}</p>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="form-group">
						<strong>{{trans('admin/seller.activated_on')}}</strong>
						<p class="text-muted">{{isset($retailerInfo->activated_on) ? $retailerInfo->activated_on : ''}}</p>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default" id="account-info">
			<div class="panel-heading">				
				<h4 class="panel-title">{{trans('admin/seller.details.account_info')}}</h4>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<strong>{{trans('admin/seller.business_category')}}</strong>
					<p class="text-muted">{{isset($retailerInfo->bcategory_name) ? $retailerInfo->bcategory_name : ''}}</p>
				</div>
				<div class="form-group">
					<strong>{{trans('admin/seller.country')}}</strong>
					<p class="text-muted">{{isset($retailerInfo->country) ? $retailerInfo->country : ''}}</p>
				</div>
				<div class="form-group">
					<strong>{{trans('admin/seller.details.is_redeem_otp_required')}}</strong></br>
					<p class="label label-{{$retailerInfo->redeem_otp_disp_class}}">{{isset($retailerInfo->redeem_otp_status_name) ? $retailerInfo->redeem_otp_status_name : ''}}</p>
				</div>
				<div class="form-group">
					<strong>{{trans('admin/seller.status')}}</strong><br>
					<span class="label label-{{$retailerInfo->status_disp_class}}">{{isset($retailerInfo->status_name) ? $retailerInfo->status_name : ''}}</span>
					@if(isset($retailerInfo->verify_disp_class))
					<span class="label label-{{$retailerInfo->verify_disp_class}}">{{isset($retailerInfo->is_verified_name) ? $retailerInfo->is_verified_name : ''}}</span>
					@endif
				</div>
			</div>
		</div>
	</div>
		
	<div class="col-md-9">
		<div class="panel panel-default">
		<div id="msg"></div>
			<div class="panel-heading">
				<div class="pull-right">
					<button class="btn btn-info btn-sm close_btn" id="back-to-list">  <i class="glyphicon glyphicon-arrow-left"></i> {{trans('admin/general.back_to_list')}}</button>
				</div>
				<div class="toolbar">
					<ul class="nav nav-tabs no-border tabs-left" style="border:none;">
						<li class="active"><a href="#about" data-toggle="tab"><!--i class="fa fa-user margin-r-5"></i--> {{trans('admin/seller.details.about')}}</a></li>
						<li><a href="#store" id="store_list" data-toggle="tab">{{trans('admin/seller.details.store')}}</a></li>
						<li><a href="#cashiers" id="cashiers-info" data-toggle="tab">{{trans('admin/seller.details.cashiers')}}</a></li>
						<li><a href="#tax" id="tax-info" data-toggle="tab">{{trans('admin/seller.details.tax')}}</a></li>
						<li><a href="#cashback" id="admin-info" data-toggle="tab">{{trans('admin/seller.details.manage_cashback')}}</a></li>
					</ul>
				</div>
			</div>
			<div class="panel-body">
				<div class="col-md-12">
					<div class="tab-content">
						<div id="about" class="tab-pane fade in active">
							<div class="form-group">
								<strong>{{trans('admin/seller.description')}}</strong>
								<div class="text-muted">{{isset($retailerInfo->description) ? $retailerInfo->description : ''}}</div>
							</div>
							@if(!empty($primeaccInfo) )
							<div class="box-header with-border">
								<!--i class="glyphicon glyphicon-user"></i-->
								<h3 class="box-title">{{trans('admin/seller.details.prime_account_info')}}</h3>
							</div>
							<div class="form-group col-sm-12">
								<div class="col-sm-2 text-right"><strong>{{trans('admin/seller.details.user_name')}} :</strong></div>
								<div class="col-sm-10 text-muted">{{isset($primeaccInfo->uname) ? $primeaccInfo->uname : ''}}
								</div>
							</div>
							<div class="form-group col-sm-12">
								<div class="col-sm-2 text-right"><strong>{{trans('admin/seller.details.full_name')}} :</strong></div>
								<div class="col-sm-10 text-muted">{{isset($primeaccInfo->fullname) ? $primeaccInfo->fullname : ''}}
								</div>
							</div>
							<div class="form-group col-sm-12">
								<div class="col-sm-2 text-right"><strong>{{trans('admin/seller.details.mobile')}} :</strong></div>
								<div class="col-sm-10 text-muted">{{isset($primeaccInfo->mobile) ? $primeaccInfo->mobile : ''}}
								</div>
							</div>
							<div class="form-group col-sm-12">
								<div class="col-sm-2 text-right"><strong>{{trans('admin/seller.details.email')}} :</strong></div>
								<div class="col-sm-10 text-muted">{{isset($primeaccInfo->email) ? $primeaccInfo->email : ''}}
								</div>
							</div>
							<div class="form-group col-sm-12">
								<div class="col-sm-2 text-right"><strong>{{trans('admin/seller.details.address')}} :</strong></div>
								<div class="col-sm-10 text-muted">{{isset($primeaccInfo->address) ? $primeaccInfo->address : '--'}}
								</div>
							</div>
							@endif
						</div>
						<div id="store" class="tab-pane fade">
							<table id="seller_store_list" class="table table-bordered table-striped ">
								<thead>
									<tr>
										<th>{{trans('general.label.created_on')}}</th>
										<th>{{trans('general.label.outlet')}}</th>
										<th>{{trans('admin/seller.store.image')}}</th>
										<th>{{trans('admin/seller.store.mobile')}}</th>
										<th>{{trans('admin/seller.store.status')}}</th>
										<th>{{trans('admin/seller.store.approval')}}</th>
										<th>Premium</th>
										<th>{{trans('general.label.updated_by')}}</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
						<div id="cashiers" class="tab-pane fade">
							<table id="admin-details" class="table table-bordered table-striped ">
								<thead>
									<tr>
										<th>{{trans('admin/seller.details.signedup_on')}}</th>
										<th>{{trans('admin/seller.details.user_name')}}</th>
										<th>{{trans('admin/seller.details.full_name')}}</th>
										<th>{{trans('admin/seller.details.mobile')}}</th>
										<th>{{trans('admin/seller.details.email')}}</th>
										<th>{{trans('admin/seller.details.store_name')}}</th>
									</tr>
								</thead>
								<tbody> </tbody>
							</table>
						</div>
						<div id="tax" class="tab-pane fade">
							@if(!empty($retailerInfo->tax_info))
							@foreach($retailerInfo->tax_info as $v)
							<div class="col-sm-12">
								<div class="form-group">
									<div class="row">
										<div class="col-md-4"><strong>{{$v['lable']}} </strong></div>
										<div class="col-md-2"> :{{!empty($v['value'])?$v['value']:'  --'}}</div>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							@endforeach
							@endif
						</div>
						<div id="cashback" class="tab-pane fade">
							<div class="row">
								<label for="profit_sharing" class="control-label col-sm-3">{{trans('general.seller.profit-sharing.profit_sharing')}}</label>
								<div class="col-sm-9">
									<p class="form-control-static" id="profit_sharing" >{{$retailerInfo->profit_sharing}}</p>
								</div>
							</div>
							<div class="row">
								<label for="cashback_on_pay" class="control-label col-sm-3">{{trans('general.seller.profit-sharing.cashback_on_pay')}} :</label>
								<div class="col-sm-9">
									<p class="form-control-static" id="cashback_on_pay">{{$retailerInfo->cashback_on_pay}}</p>
								</div>
							</div>
							<div class="row">
								<label for="cashback_on_redeem" class="control-label col-sm-3">{{trans('general.seller.profit-sharing.cashback_on_redeem')}} :</label>
								<div class="col-sm-9">
									<p class="form-control-static" id="cashback_on_redeem" >{{$retailerInfo->cashback_on_redeem}}</p>
								</div>
							</div>
							<div class="row">
								<label for="cashback_on_shop_and_earn" class="control-label col-sm-3">{{trans('general.seller.profit-sharing.cashback_on_shop_and_earn')}} :</label>
								<div class="col-sm-9">
									<p class="form-control-static" id="cashback_on_shop_and_earn">{{$retailerInfo->cashback_on_shop_and_earn}}</p>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-3 control-label">Offer Cashback :</label>
								<div class="col-sm-9">
									<p class="form-control-static">{{trans('admin/seller.details.redeem.'.$retailerInfo->redeem)}}</p>
								</div>
							</div>
							<div class="row">
								<label for="member_redeem_wallets" class="col-sm-3 control-label">Period :</label>
								<div class="col-sm-9">
									<p class="form-control-static">{{trans('admin/seller.details.is_cashback_period.'.$retailerInfo->is_cashback_period)}}</p>
									@if($retailerInfo->is_cashback_period)
									<p class="form-control-static">{{trans('admin/seller.details.from')}}: {{$retailerInfo->cashback_start}}</p>
									<p class="form-control-static">{{trans('admin/seller.details.to')}}: {{$retailerInfo->cashback_end}}</p>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endif



@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('resources/supports/admin/seller/details.js')}}"></script>	
<!--script src="{{asset('resources/supports/admin/seller/account_verification.js')}}"></script-->	
@stop

