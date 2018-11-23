@extends('affiliate.layout.dashboard')
@section('title',trans('affiliate/settings/change_email.change_email'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('affiliate/settings/change_email.change_email')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('affiliate/general.dashboard')}} </a></li>
        <li>{{trans('affiliate/general.settings')}}</li>
        <li class="active">{{trans('affiliate/settings/change_email.change_email')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->		
		<div class="row">  		   
		    <div class="col-md-12">
				<div class="box box-primary">
					<div class="box-body">
						<!-- nav tabs start -->
						<div class="col-md-2">
						<ul class="nav nav-tabs no-border tabs-left" style="border:none;">
							<li class="active"><a href="#change-email" data-toggle="tab">{{trans('affiliate/general.change_email_id')}}</a></li>
							<li><a href="#change-mobile" data-toggle="tab">{{trans('affiliate/general.change_mobile_no')}}</a></li>
						</ul>
						</div>
						<div class="col-md-10">
							<div class="tab-content">
								<div id="change-email" class="tab-pane fade in active">
									@include('affiliate.settings.change_email')
								</div>
								<div id="change-mobile" class="tab-pane fade ">
									@include('affiliate.settings.change_mobile')
								</div>
							</div>
						</div>
						<!-- nav tab end -->
					</div>		
				</div>       
            </div>			
		</div>     
		<!-- /.row -->   
    </section>
    <!-- /.content -->
@stop 
@section('scripts') 
<script src="{{url('affiliate/validate/lang/change-email')}}" charset="utf-8"></script> 
<script type="text/javascript" src="{{asset('js/providers/affiliate/setting/change_email.js')}}"  ></script> 
<script src="{{url('affiliate/validate/lang/change-mobile')}}" charset="utf-8"></script> 
<script type="text/javascript" src="{{asset('js/providers/affiliate/setting/change_mobile.js')}}"  ></script> 
@stop 