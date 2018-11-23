@extends('seller.common.layout')
@section('pagetitle','Store Banking')
@section('layoutContent')
<div class="text-center">	
    <div class="col-sm-3">
        <a href="{{route('seller.bussiness-info')}}" class="">
            <div class="panel panel-warning">
                <div class="panel-header">
                    <h2>Business Info</h2>
                </div>
                <div class="panel-body">
                    <p>You need to provide your business informations</p>
                </div>
                <div class="panel-footer">					
                    Add Details			
					@if (!empty($userSess->completed_steps))	
						@if (in_array("1", explode(',', $userSess->completed_steps)))
							@if (in_array("1", explode(',', $userSess->verified_steps)))
							<span class="label label-success">Verified</span>
							@else
							<span class="label label-info">Completed</span>
							@endif
						@else
							<span class="label label-danger">Pending</span>
						@endif					
					@endif					
                </div>
            </div>
        </a>
    </div>
	<div class="col-sm-3">
        <a href="{{route('seller.account-info')}}" class="">
            <div class="panel panel-warning">
                <div class="panel-header">
                    <h2>Account Info</h2>
                </div>
                <div class="panel-body">
                    <p>You need to provide your Account informations</p>
                </div>
                <div class="panel-footer">					
                    Add Details			
					@if (!empty($userSess->completed_steps))	
						@if (in_array("2", explode(',', $userSess->completed_steps)))
							@if (in_array("2", explode(',', $userSess->verified_steps)))
							<span class="label label-success">Verified</span>
							@else
							<span class="label label-info">Completed</span>
							@endif
						@else
							<span class="label label-danger">Pending</span>
						@endif					
					@endif					
                </div>
            </div>
        </a>
    </div>
	<div class="col-sm-3">
        <a href="{{route('seller.store-info')}}" class="">
            <div class="panel panel-warning">
                <div class="panel-header">
                    <h2>Store Info</h2>
                </div>
                <div class="panel-body">
                    <p>We need your store details to approve your account</p>
                </div>
                <div class="panel-footer">
                    Add Store Details					
					@if (!empty($userSess->completed_steps))	
						@if (in_array("3", explode(',', $userSess->completed_steps)))
							@if (in_array("3", explode(',', $userSess->verified_steps)))
							<span class="label label-success">Verified</span>
							@else
							<span class="label label-info">Completed</span>
							@endif
						@else
							<span class="label label-danger">Pending</span>
						@endif				
					@endif				
                </div>
            </div>
        </a>
    </div>
	
	<div class="col-sm-3">
        <a href="{{route('seller.manage-cashback')}}" class="">
            <div class="panel panel-warning">
                <div class="panel-header">
                    <h2>Manage Cashback</h2>
                </div>
                <div class="panel-body">
                    <p>We need your cashback percentage for account approval</p>
                </div>
                <div class="panel-footer">
                    Add Store Details					
					@if (!empty($userSess->completed_steps))	
						@if (in_array("4", explode(',', $userSess->completed_steps)))
							@if (in_array("4", explode(',', $userSess->verified_steps)))
							<span class="label label-success">Verified</span>
							@else
							<span class="label label-info">Completed</span>
							@endif
						@else
							<span class="label label-danger">Pending</span>
						@endif				
					@endif				
                </div>
            </div>
        </a>
    </div>
	
    <div class="col-sm-3">
		<br>
        <a href="{{route('seller.store-banking')}}" class="">
            <div class="panel panel-warning">
                <div class="panel-header">
                    <h2>Bank Details</h2>
                </div>
                <div class="panel-body">
                    <p>We need your bank details and KYC documents to verify your bank account</p>
                </div>
                <div class="panel-footer">
                    Add Details					
					@if (!empty($userSess->completed_steps))	
						@if (in_array("5", explode(',', $userSess->completed_steps)))
							@if (in_array("5", explode(',', $userSess->verified_steps)))
							<span class="label label-success">Verified</span>
							@else
							<span class="label label-info">Completed</span>
							@endif
						@else
							<span class="label label-danger">Pending</span>
						@endif					
					@endif					
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-sm-3">
		<br>
        <a href="{{route('seller.kyc-verification')}}" class="">
            <div class="panel panel-warning">
                <div class="panel-header">
                    <h2>Add KYC</h2>
                </div>
                <div class="panel-body">
                    <p>You need to add minimum 5 listings to activate your account</p>
                </div>
                <div class="panel-footer">
                    Add KYC Details
					@if (!empty($userSess->completed_steps))	
						@if (in_array("6", explode(',', $userSess->completed_steps)))
							@if (in_array("6", explode(',', $userSess->verified_steps)))
							<span class="label label-success">Verified</span>
							@else
							<span class="label label-info">Completed</span>
							@endif
						@else
							<span class="label label-danger">Pending</span>
						@endif
					@endif					
                </div>
            </div>
        </a>
    </div>
</div>
@stop
