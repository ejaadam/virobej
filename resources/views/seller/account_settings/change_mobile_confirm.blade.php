@extends('seller.common.simple_layout')
@section('pagetitle','Change Email')
@section('breadCrumbs')
@stop
@section('layoutContent')
<div class="row">

	<div class="col-sm-12">
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