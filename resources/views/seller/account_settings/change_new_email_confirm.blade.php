@extends('seller.common.simple_layout')
@section('pagetitle','Verify New Email')
@section('breadCrumbs')
@stop
@section('layoutContent')
<div class="row">	
	<div class="col-sm-12" {!!($verify_new_email)? '' : 'style=display:none;'!!}> 
		<div class="panel panel-default">
			<div class="panel-heading">
                <h4 class="panel-title"><!--i class="icon-undo"></i--> Verify New Email</h4>
			</div>
			<div class="panel-body">
			    <p class="text-success text-center"><i class="icon-ok-sign text-success" style="font-size: 70px;"></i></p>
				<p class="text-center">{{$msg}}</p>				
				<p class="text-center"><a href="{{route('seller.dashboard')}}" class="btn btn-success">{{(isset($btnMsg) ? $btnMsg : 'Click Here to Login')}}</a></p>
			</div>
		</div>
	</div>	
	<div class="col-sm-12" {!!($verify_new_email)? 'style=display:none;' : ''!!}>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title"> Sorry!</h4>
			</div>
			<div class="panel-body">
			    <p class="text-success text-center"><i class="icon-warning-sign text-danger" style="font-size: 70px;"></i></p>
				<p class="text-center">{{$msg}}</p>		
			</div>
		</div>
	</div>
</div>
@stop