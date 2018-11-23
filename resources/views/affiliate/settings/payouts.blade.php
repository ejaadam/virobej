@extends('affiliate.layout.dashboard')
@section('title',"Create Payout Request")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Payout Settings</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('affiliate/general.dashboard')}}</a></li>
        <li>{{trans('affiliate/general.settings')}}</li>
        <li class="active">{{trans('affiliate/general.Create_payout_Request')}}</li>
      </ol>
	
    </section>
    <!-- Main content -->
    <section class="content">
		<div class="wrapper">
			<div class="row">
				@if(isset($bank_transfer_settings)) 			  
					<div class="col-md-6">
						<div class="panel panel-info portlet-item" id="banktranfer">
							<header class="panel-heading">
							   <button class="btn btn-xs btn-primary pull-right" id="new_bank"><i class="fa fa-plus"></i> {{trans('affiliate/general.add_new')}}</button>
							   {{trans('affiliate/settings/payout_settings.bank_transfer')}}							
							</header>
							<section class="panel-body">
								<div class="panel-group bank" id="accordion">
								@foreach( $bank_transfer_settings as $settings)
									<?php echo $settings; ?>
								@endforeach
								
								</div>
							</section>
						</div>
					</div>
				@endif
				@if(isset($cashfree_transfer_settings))
					<div class="col-md-6">
						<div class="panel panel-info portlet-item">
							<header class="panel-heading">
								{{trans('affiliate/settings/payout_settings.cashfree_payment')}}	
							</header>
							<section class="panel-body">
								@foreach( $cashfree_transfer_settings as $settings)
									<?php echo $settings; ?>
								@endforeach
								
							</section>
						</div>
					</div>
				@endif
				@if(isset($paytm_transfer_settings ))
					<div class="col-md-6">
						<div class="panel panel-info portlet-item">
							<header class="panel-heading">
									{{trans('affiliate/settings/payout_settings.paytm_payment')}}	
							</header>
							<section class="panel-body">
								@foreach( $paytm_transfer_settings as $settings)
									<?php echo $settings; ?>
								@endforeach
							</section>
						</div>
					</div>
				@endif
			</div>
		</div>
	</section>   
	<style type="text/css">
    .help-block{
        color:#f56954;
    }
</style>
@stop
@section('scripts')
	<script src="<?php echo URL::asset('account/validate/lang/payout_settings');?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo URL::asset('js/providers/affiliate/setting/payout_settings.js');?>" type="text/javascript" charset="utf-8"></script>
@stop