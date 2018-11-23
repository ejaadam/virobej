@extends('seller.common.layout')
@section('pagetitle','Dashboard')
@section('layoutContent')
@if(empty($logged_userinfo->is_verified))
<div class="text-center">	
	<div class="row">
		@if(empty($logged_userinfo->is_email_verified))
		<div class="col-sm-12">	
			<p class="alert alert-danger"><i class="icon-warning-sign"></i> Your email address is not yet verified. <a href="{{route('seller.verify-email')}}" class="text-danger"><b>Verify Now</b></a></p>
		</div>
		@endif
		<div class="col-sm-3">
			<a href="{{route('seller.account-settings').'#tbb_a'}}" class="">  <!-- 'seller.account-settings.business-details'  #tbb_d--> 
				<div class="panel panel-warning">
					<div class="panel-header">
						<h2>Seller Information</h2>
					</div>
					<div class="panel-body">
						<p>You need to provide your Business Informations</p>
					</div>
					<div class="panel-footer">					
						View Details				
						@if (!empty($userSess->completed_steps))	
							@if (in_array("2", explode(',', $userSess->completed_steps)))
								@if (in_array("2", explode(',', $userSess->verified_steps)))
								<span class="label label-success">Verified</span>
								@else
								<span class="label label-info">Completed</span>
								@endif
							@else
								<span class="label label-warning">Pending</span>
							@endif
						@else
							<span class="label label-warning">Pending</span>								
						@endif					
					</div>
				</div>
			</a>
		</div>
		<div class="col-sm-3">
			<a href="{{route('seller.verify-email')}}" class="">   <!-- 'seller.verify-email'  -->
				<div class="panel panel-warning">
					<div class="panel-header">
						<h2>Email Verification</h2>
					</div>
					<div class="panel-body">
						<p>Your email address should be verified to get account activity</p>
					</div>
					<div class="panel-footer">					
						View Details				
						@if (!empty($userSess->completed_steps))	
							@if (in_array("3", explode(',', $userSess->completed_steps)))
								@if (in_array("3", explode(',', $userSess->verified_steps)))
								<span class="label label-success">Verified</span>
								@else
								<span class="label label-info">Completed</span>
								@endif
							@else
								<span class="label label-warning">Pending</span>
							@endif		
						@else
							<span class="label label-warning">Pending</span>		
						@endif					
					</div>
				</div>
			</a>
		</div>	
		@if($userSess->service_type != config('constants.SERVICE_TYPE.ONLINE'))
		<div class="col-sm-3">		
			<a href="{{route('seller.account-settings').'#tbb_d'}}" class="">   <!-- 'seller.account-settings.manage-cashback'  -->
				<div class="panel panel-warning">
					<div class="panel-header">
						<h2>Manage Cashback</h2>
					</div>
					<div class="panel-body">
						<p>We need your cashback percentage for account approval</p>
					</div>
					<div class="panel-footer">
						View Details					
						@if (!empty($userSess->completed_steps))	
							@if (in_array("4", explode(',', $userSess->completed_steps)))
								@if (in_array("4", explode(',', $userSess->verified_steps)))
								<span class="label label-success">Verified</span>
								@else
								<span class="label label-info">Completed</span>
								@endif
							@else
								<span class="label label-warning">Pending</span>
							@endif		
						@else
							<span class="label label-warning">Pending</span>	
						@endif					
					</div>
				</div>
			</a>
		</div>
		@endif
		<div class="col-sm-3">
			<a href="{{route('seller.account-settings').'#tbb_e'}}" class="">  <!-- 'seller.account-settings.tax-information'  -->
				<div class="panel panel-warning">
					<div class="panel-header">
						<h2>Tax Information</h2>
					</div>
					<div class="panel-body">
						<p>We need your Business TAN and other Tax Informations</p>
					</div>
					<div class="panel-footer">
						View Details						
						@if (!empty($userSess->completed_steps))	
							@if (in_array("5", explode(',', $userSess->completed_steps)))
								@if (in_array("5", explode(',', $userSess->verified_steps)))
								<span class="label label-success">Verified</span>
								@else
								<span class="label label-info">Completed</span>
								@endif
							@else
								<span class="label label-warning">Pending</span>
							@endif
						@else
							<span class="label label-warning">Pending</span>	
						@endif				
					</div>
				</div>
			</a>
		</div>
		<div class="col-sm-3">
			<a href="{{route('seller.account-settings').'#tbb_g'}}" class="">   <!-- 'seller.account-settings.bank-details'  -->
				<div class="panel panel-warning">
					<div class="panel-header">
						<h2>Bank Details</h2>
					</div>
					<div class="panel-body">
						<p>We need your bank account details and KYC documents to verify your bank account</p>
					</div>
					<div class="panel-footer">
						View Details					
						@if (!empty($userSess->completed_steps))	
							@if (in_array("6", explode(',', $userSess->completed_steps)))
								@if (in_array("6", explode(',', $userSess->verified_steps)))
								<span class="label label-success">Verified</span>
								@else
								<span class="label label-info">Completed</span>
								@endif
							@else
								<span class="label label-warning">Pending</span>
							@endif		
						@else
							<span class="label label-warning">Pending</span>	
						@endif				
					</div>
				</div>
			</a>
		</div>
    </div>
</div>
@endif
@if(!empty($logged_userinfo->is_verified))
<div class="pageheader" id="seller_dashboard" data-url="{{route('seller.dashboard')}}">    <!-- 'seller.dashboard'  -->
    <div class="row">   
	    @if(empty($logged_userinfo->is_email_verified))
		<div class="col-sm-12">
			<p class="alert alert-danger"><i class="icon-warning-sign"></i> Your email address is not yet verified. <a href="{{route('seller.verify-email')}}" class="text-danger"><b>Verify Now</b></a></p>
		</div>
		@endif
        <div class="col-sm-9">
			<div class="row">
				<div class="col-lg-4 col-xs-6 widget" data-widget-name="sales">
					<!-- small box -->
					<div class="small-box bg-aqua-fade" >
						<div class="inner">
							<h3 id="total_sales">0</h3>
							<p>Sales</p>
						</div>
						<div class="icon">
							<i class="ion ion-bag"></i>
						</div>
						<a href="#" class="small-box-footer">More Info <i class="icon-circle-arrow-right"></i></a>
					</div>
				</div>
				<div class="col-lg-4 col-xs-6 widget" data-widget-name="orders">
					<!-- small box -->
					<div class="small-box bg-green-fade">
						<div class="inner">
							<h3 id="total_orders">0</h3>
							<p>Orders </p>
						</div>
						<div class="icon">
							<i class="icon-shopping-cart"></i>
						</div>
						<a href="#" class="small-box-footer">More Info <i class="icon-circle-arrow-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
				<div class="col-lg-4 col-xs-6 widget" data-widget-name="visitors">
					<!-- small box -->
					<div class="small-box bg-yellow-fade">
						<div class="inner">
							<h3 id="total_visitorss">0</h3>
							<p>Visitors</p>
						</div>
						<div class="icon">
							<i class="icon-group"></i>
						</div>
						<a href="#" class="small-box-footer">More Info <i class="icon-circle-arrow-right"></i></a>
					</div>
				</div>
			</div>
			<div class="card-group mb-4">
				<div class="card">
					<div class="card-body text-center">
						<h4 id="today_sales">0</h4>
						<small class="text-muted text-uppercase font-weight-bold">Today's Sales</small>
					</div>
			    </div>
				<div class="card">
					<div class="card-body text-center">
						<h4 id="current_month">0</h4>
						<small class="text-muted text-uppercase font-weight-bold">Current month</small>
					</div>
				</div>
				<div class="card">
					<div class="card-body text-center">
						<h4 id="three_monthago">0</h4>
						<small class="text-muted text-uppercase font-weight-bold">3 months ago</small>
					</div>
				</div>
				<div class="card">
					<div class="card-body text-center">
						<h4 id="six_monthago">0</h4>
						<small class="text-muted text-uppercase font-weight-bold">6 months ago</small>
					</div>
				</div>
			</div>	
		</div>	
		<div class="col-sm-3">
			<div  id="balance-box" class="box_stat box_ico my-4 widget" data-widget-name="acc_balance">
				<span class="stat_ico stat_ico_2"><i class="li_user"></i></span>
				<h4 id="current_balance">0</h4>
				<div class="text-muted text-uppercase font-weight-bold small">Account Balance</div>
			</div>			
				
			<div class="panel panel-default widget" id="transactions" data-widget-name="transactions">
				<div class="panel-heading">
					<h4 class="panel-title pull-left">Recent Transactions</h4>									
				</div>
				<div class="panel-body">
					<table id="transaction_tbl" class="table table-striped font-roboto">
						<tbody>Loading..</tbody>
					</table>
				</div>
				<div class="panel-footer" style="display:none">
					<a href="{{route('seller.reports.instore.transactions')}}" title="" class="pull-right" type="button">More..</a>
				</div>
			</div>
			
			<div class="panel panel-warning widget" id="acc_manager" data-widget-name="acc_manager">
				<div class="panel-heading">
					<h4 class="panel-title pull-left">Account manager</h4>									
				</div>
				<div class="panel-body">
					<span><i class="icon-user icon-4x" style="float: left;margin-right: 15px;" aria-hidden="true"></i></span>
					<span id="manager_details"></span>
				</div>
			</div>
		</div>
    </div>
</div>
@endif
@include('seller.common.assets')
<script src="{{asset('resources/assets/supplier/js/jquery_cookie.min.js')}}"></script>
<script src="{{asset('resources/assets/supplier/js/lib/parsley/parsley.min.js')}}"></script>
<!--script src="{{asset('js/providers/seller/login.js')}}"></script-->
<script src="{{asset('js/providers/seller/dashboard.js')}}"></script>
@stop
