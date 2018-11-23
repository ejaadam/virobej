@extends('seller.common.layout')
@section('pagetitle')
Orders
@stop

@section('layoutContent')
<div class="pageheader">
    <div class="row">
        <div id="alert-div"></div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Transaction #{{$order_code}}</h4>
                </div>
                <div class="panel_controls" id="order-details" data-order_code="{{$order_code}}">
                    <div class="row">
                        <div class="col-md-12">	
							<div class="col-md-8">	
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
									<p><strong>In case of any queries/clarification</strong></p>									
									<a href="#" target="_blank" class="faq"><span>Contact Us</span></a>
									
								</div>
							</div>	
						</div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<script src="{{asset('js/providers/seller/reports/instores/transaction_details.js')}}"></script>
@stop
