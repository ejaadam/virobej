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
                        @if (!empty($wallets))
							@foreach ($wallets as $values)
								<div class="col-sm-6 col-md-4">
									<div class="box_stat box_ico">
										<span class="stat_ico stat_ico_1"><i class="li_shop"></i></span>
										<h4><b>Balance :<b> {{$values->currency . ' ' . $values->current_balance}}</h4>
										<p>{{$values->wallet}} ({{$values->wallet_code}})</p>
										<p></p>										
									</div><br>
								</div>
							@endforeach
						@else
							<div class="col-sm-6 col-md-11">
								<div class="box_stat box_ico">
									
									<h5>Balance not found in your wallet.	</h5>
									
									
								</div>
							</div>
							
						@endif
                    </div>
                </div>
               
            </div>
        </div>
    </div>
</div>

@include('seller.common.assets')
@stop
