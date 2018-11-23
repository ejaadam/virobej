@extends('affiliate.layout.dashboard')
@section('title',trans('affiliate/general.my_wallet'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('affiliate/general.my_wallet')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('affiliate/general.dashboard')}}</a></li>
        <li>{{trans('affiliate/general.settings')}}</li>
        <li class="active">{{trans('affiliate/general.my_wallet')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		
		<div class="row">  
		   @if(!empty($balInfo))
		   @foreach($balInfo as $balance)
		    <div class="col-lg-3 col-xs-6">
           <!-- small box -->
			    <div class="small-box bg-aqua">
					<div class="inner">
					   <h3>{{$balance->current_balance}}</h3>
					   <p>{{$balance->wallet_name}}</p>
					</div>
					<div class="inner trans">
					   <p>{{trans('affiliate/wallet/wallet_balance.tot_credit')}} : <span>{{$balance->tot_credit}}</span></p>
					   <p>{{trans('affiliate/wallet/wallet_balance.tot_debit')}}  : <span>{{$balance->tot_debit}}</span></p>
					</div>
					<div class="icon">
					   <i class="fa fa-shopping-cart"></i>
					</div>
			    </div>
            </div>
			@endforeach
		    @endif
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@stop