@extends('seller.common.layout')
@section('pagetitle','Dashboard')
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
                    <h4 class="panel-title">Wallet Balance</h4>
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <div class="box-body">
							<div id="wallets"></div>
						</div>
                    </div>
                </div>  
				<div class="panel_controls">
					<div class="panel-heading"><h4><i class="fa fa-exchange fa-fw" aria-hidden="true"></i>Transactions</h4></div>
					<table id="trans-table" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th class="text-center">{{trans('general.label.created_on')}}</th>
								<th>Descriptions</th>
								<th>Wallet</th>
								<th class="text-right">Amount</th>
							</tr>
						</thead>
						<tbody id="flds"></tbody>
					</table>
				</div>
            </div>
        </div>
    </div>
</div>

@include('seller.common.assets')
<script src="{{asset('js/providers/seller/wallet/balance.js')}}"></script>
@stop
