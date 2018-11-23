@extends('affiliate.layout.dashboard')
@section('title',"Tickets")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Tickets</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li>Support</li>
        <li class="active">Tickets</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
            <div class="col-md-12">
            	 <div class="box box-primary">
					<div class="box-header with-border">
                       <a id="back-to-list" href="{{URL::to('account/support/tickets')}}" class="btn btn-primary btn-sm" role="button" style="float:right;"><i class="fa fa-backward" aria-hidden="true"></i>  {{trans('affiliate/general.back_btn')}}</a>
                          <p>New Ticket is successfully generated at support content</p><br/>
                          <p>Recent Ticket ID is<h1><?php print_r($ticket_value)?></h1></p>
                     </div>
                   </div>  
			</div>
			<!-- ./col -->		
		
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@include('user.common.datatable_js')
<script src="{{asset('js/providers/affiliate/support/tickets.js')}}"></script>
@stop