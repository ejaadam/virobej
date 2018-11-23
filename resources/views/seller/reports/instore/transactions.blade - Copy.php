@extends('seller.common.layout')
@section('pagetitle')
Orders
@stop
@section('top-nav')
@include('seller.common.top_navigation')
@stop
@section('layoutContent')
<div class="pageheader">
    <div class="row">
        <div id="alert-div"></div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Transactions</h4>
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <form class="form" action="" method="post" id="listfrm">
                            <div class="col-sm-3 input-group">
                                <input type="text"  placeholder="Search Term" name="search_text" id="search_text" class="form-control" >
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
                                <input type="text"  placeholder="From Date" name="from" id="from" class="form-control" />
                            </div>
                            <div class="col-sm-2">
                                <input type="text"  placeholder="To Date" name="to" id="to" class="form-control" />
                            </div>
                            <div class="col-sm-3">
                                <!--button id ="search" type="button" class="btn btn-sm btn-primary" title="Search">Search</button-->   
								<button type="button"  id="searchbtn" class="btn btn-sm bg-olive">{{trans('general.btn.search')}}</button>&nbsp;
								<button type="button" id="resetbtn" class="btn btn-sm bg-orange">{{trans('general.btn.reset')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <table id="listtbl" class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{trans('seller/general.label.date')}}</th>
							<th>{{trans('seller/general.label.order_id')}}</th>
							<th>{{trans('seller/general.label.stores')}}</th>
							<th>{{trans('seller/general.order.commissions.amount')}}</th>
							<th>{{trans('seller/general.order.commissions.commission')}}</th>
							<th>{{trans('seller/general.order.commissions.tax')}}</th>
							<th>{{trans('seller/general.order.commissions.handle_amt')}}</th>
							<th>{{trans('seller/general.order.commissions.received_amt')}}</th>
							<th>{{trans('seller/general.order.commissions.system_settlement')}}</th>
							<th>{{trans('seller/general.label.status')}}</th>
							<th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('js/providers/seller/reports/instores/transactions.js')}}"></script>
@stop
