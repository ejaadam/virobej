@extends('admin.common.layout')
@section('title','Create Support Center')
@section('layoutContent')

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3c8dbc;
        border-color: #367fa9;
        padding: 1px 10px;
        color: #fff;
    }
</style>

<div class="col-md-12" id="list-panel">
    <div class="box box-primary">
        <div class="box-header with-border" >
		@if(isset($details) && !empty($details))
			<img class="img-responsive" src="{{$details->banner_path}}">
			<div class="row">
				<div class="col-sm-12">
					<h3>{{$details->store_name}}</h3>
					<img class="pull-left" src="{{$details->store_logo}}" class="img-responsive"><br>
					<div class="col-md-12 text-muted">
						<span><strong>Aff. Network</strong>: {{$details->mrbusiness_name}}</span><br>
						<span><strong>Featured:</strong></span><span class="label label-success">Yes</span><br>
						<p><strong>Website url:</strong> <a href="{{$details->website_url}}">{{$details->website_url}}</a></p>
						<span>Cashback: {{$details->new_cashback}}@if($details->cashback_type == 0){{'%'}}@else{{Flate}}@endif | </span>&nbsp;<span>Tracking In: {{$details->cb_tracking_days}}Days | </span><span>Cashback waiting In:  {{$details->cb_waiting_days}} Days | </span>&nbsp;&nbsp;<span>Waiting Days: 10 | </span><span>Expired On: {{$details->website_url}}</span>
					</div>
				</div>
			</div><br>
			<div class="row">
				<div class="col-sm-12">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#1b" data-toggle="tab" aria-expanded="false">Description</a></li>
						<li><a href="#2b" data-toggle="tab" aria-expanded="false">Do's Desc</a></li>
						<li><a href="#3b" data-toggle="tab" aria-expanded="false">Don't Desc</a></li>
						<li><a href="#4b" data-toggle="tab" aria-expanded="false">Conditions</a></li>
					</ul>
					<div class="tab-content clearfix">
						<div class="tab-pane active" id="1b">
							<div class="col-md-12">
								@if(is_array($details->description)) 
									@foreach($details->description as $descrb) <p>{{$descrb['desc'].'-'. $descrb['val']}}</p> @endforeach 
								@else{{strip_tags($details->description)}}@endif
							</div>
						</div>
						<div class="tab-pane" id="2b"><div class="col-md-12">{{strip_tags($details->dos_desc)}}</div></div>
						<div class="tab-pane" id="3b"><div class="col-md-12">{{strip_tags($details->dont_desc)}}</div></div>
						<div class="tab-pane" id="4b"><div class="col-md-12">{{strip_tags($details->cb_notes)}}</div></div>
					</div>
				</div>
			</div>
		@endif
        </div>
    </div>
</div>
@stop
@section('scripts')
	<script src="{{asset('js/providers/admin/affiliate/list.js')}}" charset="utf-8"></script>
@stop


