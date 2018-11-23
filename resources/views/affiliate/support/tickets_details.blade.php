@extends('affiliate.layout.dashboard')
@section('title',"Tickets")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('affiliate/general.tickets')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>{{trans('affiliate/general.dashboard')}}</a></li>
        <li>{{trans('affiliate/general.support')}}</li>
        <li class="active">{{trans('affiliate/general.tickets')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<!-- ./col -->
            <div class="col-md-8">
             <div class="box box-primary">
				<div class="box-header with-border">
                <h3 class="box-title">{{trans('affiliate/general.tickets')}}--{{trans('affiliate/general.tickets')}}</h3>
                <a id="back-to-list" href="{{URL::to('account/support/tickets')}}" class="btn btn-primary btn-sm" role="button" style="float:right;"><i class="fa fa-backward" aria-hidden="true"></i>  {{trans('affiliate/general.back_btn')}}</a>
            	</div>
                 <div class="box-body">
				 </div>      
			</div>
             <div class="box box-primary">
				<div class="box-header with-border">
                <h3 class="box-title">{{trans('affiliate/general.post_a_reply')}}</h3>
            	</div>
                 <div class="box-body">
                     <div class="form-group">
                        <label class="col-md-3 control-label" for="from"> {{trans('affiliate/general.description')}}</label>
                          </div>
                            <div class="col-md-8 form-group">
                               <textarea id="ticket_message" name="ticket_message" class="form-control" rows="5" cols="15"></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="example-disabled-input">{{trans('affiliate/support/support.attachment')}}</label>
                                    <div class="col-md-8">
                                     <input type="file" id="file_attachment" name="file_attachment" size="30" onchange="return checkformat1(file_attachment, 'gif|jpg|jpeg|png|docx|pdf|doc', 'Please select valid formet(*.gif, .jpg, .jpeg, *.png, *.docx, *.pdf, *.doc)')">
                                        <span>Supported file formats (jpg,jpeg,png,gif,docx,pdf,ppt,doc)</span>
                                    </div>
                                </div>
				 </div>      
			</div>
			</div>
            
            <div class="col-md-4">
             <div class="box box-primary">
				<div class="box-header with-border">  
                <h2 class="box-title">{{trans('affiliate/general.feedback')}}</h2> 
                 </div>
                 <div class="box-body">
                   <div class="form-group">
                        <label class="control-label" for="from"> {{trans('affiliate/support/support.message')}}</label>
                          </div>
                            <div class="form-group">
                               <textarea id="ticket_message" name="ticket_message" class="form-control" rows="5" cols="15"></textarea>
                                </div>
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
<script src="{{asset('js/providers/affiliate/support/ajax_form.js')}}"></script>
@stop