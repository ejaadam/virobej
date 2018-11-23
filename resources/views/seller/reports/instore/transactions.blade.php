@extends('seller.common.layout')
@section('pagetitle','Orders')
@section('breadCrumbs')
<li>Finance</li>
<li>Transaction</li>
@stop
@section('layoutContent')   
	<div class="row" id="balance-fields">
		<div class="col-sm-4">
			<div class="box_stat box_ico">				
				<span class="stat_ico stat_ico_2"><i class="li_vallet"></i></span>
				<h4 id="current_balance">0</h4>
				<small class="text-muted text-uppercase font-weight-bold">{{trans('seller/general.label.balance')}}</small>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="box_stat box_ico">
				<span class="stat_ico stat_ico_3"><i class="li_vallet"></i></span>
				<h4 id="pending_balance">0</h4>
				<small class="text-muted text-uppercase font-weight-bold">{{trans('seller/general.label.pending_balance')}}</small>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="box_stat box_ico">
				<span class="stat_ico stat_ico_1"><i class="li_vallet"></i></span>
				<h4 id="last_settlement">0</h4>
				<small class="text-muted text-uppercase font-weight-bold">{{trans('seller/general.label.last_settlement')}}</small>
			</div>				
		</div>
	</div>
	<div class="row" id="transactions-list">	
		<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Transactions</h4>
			</div>
			<div class="panel_controls">
				<form id="filter_form" class="form form-bordered" action="{{route('seller.reports.instore.transactions')}}" method="post">
					<div class="row">
						<div class="col-sm-3 input-group">
							<input type="text"  placeholder="Search Term" name="search_term" id="search_term" class="form-control" >
							<div class="input-group-btn">
								<button data-toggle="dropdown" class="btn btn-default" aria-expanded="true">{{trans('admin/general.filter')}} <span class="caret"></span></button>
								<ul class="dropdown-menu dropdown-menu-form dropdown-menu-right" id="chkbox">
									<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="Store" type="checkbox">{{trans('general.merchants.stores.store')}}</label></li>
									<li><label class="col-sm-12"><input name="filterTerms[]" class="filterTerms" value="Order" type="checkbox">{{trans('general.merchants.stores.order')}}</label></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-2">
							<select name="pay_through" id="pay_through" class="form-control">
								<option value="">All</option>
								@foreach(trans('seller/general.order.commissions.status') as $id=>$value)
								<option value="{{$id}}">{{$value}}</option>
								@endforeach										
							</select>
						</div>						
						<div class="col-sm-2">
							<div class="form-group">	
								<div class="input-group">
									<input type="text" id="from" name="from" class="form-control datepicker"/>
									<span class="input-group-addon"><i class="icon icon-calendar"></i></span>
								</div>								
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<div class="input-group">
									<input type="text" id="to" name="to" class="form-control datepicker"/>
									<span class="input-group-addon"><i class="icon icon-calendar"></i></span>
								</div>
							</div>							
						</div>
						<div class="col-sm-2">
							<div class="form-group">								
								<select name="currency" id="currency" class="form-control">
								<option value="">Select</option>
								</select>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group">
								<button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="icon icon-search"></i> {{trans('general.btn.search')}}</button>
								<button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="icon icon-repeat"></i> {{trans('general.btn.reset')}}</button>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="panel-body">
				<form id="trans_list" action="{{route('seller.reports.instore.transactions')}}">
					<table id="list_table" class="table table-bordered table-striped" data-order='[[ 1, "asc" ]]'>
						<thead>
							<tr>								
								<th>Details</th>								                  
								<th>{{trans('general.label.status')}}</th>
								<th>Amount</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</form>
			</div>
		</div>
		</div>
	</div>	
	<br>
	<div class="col-md-12" id="transactions-details" style="display:none;">
		<div class="panel panel-default">
			<div class="panel-heading">				
				<h3 class="panel-title">Transaction Details</h3>				
				<div class="box-tools pull-right">					
					<button class="btn btn-sm btn-danger pull-right" id="back">Back</button>
				</div>
			</div>
			<div class="panel_controls">
				<div class="col-md-12">
					<div class="col-md-6">
						<div id="ord-details">
						</div>
						<hr>
						<div id="mr-details">

						</div>
						<hr>
						<div id="pay-details">

						</div>
						<hr>
						<div id="query">
							<p><strong>In case of any queries/clarification</strong>
							</p>
							<a href="#" target="_blank" class="faq"><span>Contact Us</span></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop
@section('scripts')
<script src="{{asset('js/providers/seller/reports/instores/transactions.js')}}"></script>
@stop
