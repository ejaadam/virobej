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
                    <h4 class="panel-title">Add Money</h4>
                </div>
                <div class="panel_controls">
                    <div class="col-sm-12" id="wallet_balance">
						<div class="box box-primary">
							<div class="box-header with-border">
								<i class="fa fa-money"></i>
								
								<div class="box-tools">
									<button class="btn btn-sm btn-danger close-btn" style="display: none;"><i class="fa fa-arrow-left"></i></button>
								</div>
							</div>
							<div class="box-body">
								<br>
								<form class="form form-horizontal" id="set-amount-from" action="{{route('api.v1.seller.add-money.set-amount')}}" method="post">
									<div class="form-group">
										<label class="col-sm-4 control-label">Amount</label>
										<div class="col-sm-4">
											<input type="number" class="form-control" name="amount" id="amount"/>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-offset-4">
											<input type="submit" class="btn btn-primary" value="Submit"/>
										</div>
									</div>
								</form>
								<form class="form form-horizontal" id="payment-info-form" action="{{route('api.v1.seller.add-money.payment-info')}}" method="post" style="display: none;">
									<div class="form-group">
										<label class="col-sm-4 control-label">{{trans('general.label.amount')}}</label>
										<div class="col-sm-8">
											<p class="form-control-static" id="amount"/>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label">{{trans('general.label.payment_mode')}}</label>
										<div class="col-sm-8" id="paymodes">
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-offset-4">
											<input type="submit" class="btn btn-primary" value="{{trans('general.btn.submit')}}"/>
										</div>
									</div>
								</form>
								<div id="payment-forms">
									@include('seller.common.paymentGateway')
								</div>
							</div>
						</div>
					</div>
                </div>
               
            </div>
        </div>
    </div>
</div>
@include('seller.common.assets')
@stop
