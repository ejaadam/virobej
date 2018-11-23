@extends('affiliate.layout.dashboard')
@section('title',"Tickets")
@section('content')
<style>
    .upload_format {
        color: #737373;
    }
	.change_status{color:#3c8dbc !important;}
	</style>
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
		<div class="row" id="report_div">        
			<!-- ./col -->
            <div class="col-md-12">
             <div class="box box-primary">
					<div class="box-header with-border">
                     <!---new tickets Form-->
						<i class="fa fa-list-alt"></i>
						<h3 class="box-title">{{trans('affiliate/support/support.support_tickets')}}</h3>
						<div class="box-tools pull-right">
							<button type="button" id="new_tickets_button" class="btn btn-sm btn-warning pull-right" data-toggle="collapse" data-target="#demo">{{trans('affiliate/support/support.new_ticket')}}</button>
                      	</div>					
                        
                        <div id="demo" class="collapse">
							<div class="col-md-12 new_ticket_panel" style="margin-bottom:50px">
							    <form id="form_new_ticket" class="form-horizontal" action="{{url('account/support/save-tickets')}}" method="post" name="compose" enctype="multipart/form-data" novalidate="novalidate">
									{!! csrf_field() !!}
										<div class="form-group">
											<label class="col-md-2 control-label" for="ticket_category_label"> {{trans('affiliate/support/support.category')}} </label>                                
											<div class="col-md-3">
											<select name="ticket_category_id" id="ticket_category_id" class="form-control">
												<option value="">{{trans('affiliate/support/support.select')}}</option>
												@if(!empty($category_name))
												 @foreach ($category_name as $cate_name)
												<option value="{{$cate_name->category_id}}">{{$cate_name->category_name}}</option>
												@endforeach
											   @endif
											</select> <!--<span class="errMsg"></span>-->
											</div>
											<label class="col-md-2 control-label" for="ticket_priority_label"> {{trans('affiliate/support/support.priority')}} </label>                                
										
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label" for="from">{{trans('affiliate/support/support.subject')}}</label>                                 
											<div class="col-md-8">
											<input type="text" id="ticket_subject" name="ticket_subject" class="form-control"> <!--<span class="errMsg"></span>-->
											</div>
										</div>                                
										<div class="form-group">
											<label class="col-md-2 control-label" for="to"> {{trans('affiliate/support/support.message')}}</label>
										
											<div class="col-md-8">
											<textarea id="ticket_message" name="ticket_message" class="wysihtml5 form-control" rows="5" cols="15"></textarea>
										  </div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label" for="example-disabled-input">{{trans('affiliate/support/support.attachment')}}</label> <!--<span class="errMsg"></span>-->
											<div class="col-md-8">
												<input type="file" id="file_attachment" name="file_attachment" size="30">
												<span class="upload_format">{{trans('affiliate/support/support.support_file_formats')}}</span>
											</div>
										</div>
										
									 <div class="form-group">  
										<label class="col-md-2" for="example-disabled-input"></label>  
										 <div class="col-md-8"  style="margin-top:20px;">
											<button type="submit" id="submit_btn" name="submit_btn" class="btn btn-sm bg-olive"><i class="fa fa-angle-right"></i> {{trans('affiliate/general.submit_btn')}}</button>
											<button type="button" id="resetbtn" class="btn btn-sm btn-danger" data-toggle="collapse" data-target="#demo"><i class="fa fa-times"></i> {{trans('affiliate/general.cancel')}}</button>
										</div>
									</div>
								</form>	
							</div>
                         </div>
                         <!---new tickets Form-->
	                   <form id="form_ticket" class="form-horizontal" action="{{url('account/support/tickets')}}" method="post">
                       		{!! csrf_field() !!}   
                         <div class="form-group">
                            <div class="col-sm-3">
                                    <label for="search_from_label">{{trans('affiliate/support/support.search_term')}}</label>
                                    <input type="text" id="search_term" name="search_term" class="form-control col-xs-12"  value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" placeholder="{{trans('affiliate/support/support.search_category_subject')}}">
                                </div>
                                
                                <div class="col-sm-2 has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/general.from_date_ph')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                                
                                <div class="col-sm-2 has-feedback">
                                    <label for="to"> {{trans('affiliate/general.to_date')}}</label>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/general.to_date_ph')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                                <div class="col-sm-3">
                                    <label for="ticket_status_label_id"> {{trans('affiliate/general.status')}} </label>
                                    <select name="ticket_status_id" id="ticket_status_id" class="form-control">
                                        <option value="">{{trans('affiliate/support/support.all_status')}}</option>
                                        @if(!empty($status_details))
                                         @foreach ($status_details as $status_name)
                                        <option value="{{$status_name->status_id}}">{{$status_name->status}}</option>
                                       	@endforeach
                                       @endif
                                    </select>
                             	</div>   
                            </div>
                            
                             <div class="form-group">
                                <div class="col-sm-6">
                                    <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn_ticket" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                </div>
                            </div>
                        </form> 
					</div>
                    <div class="box-body">
                     <div id="alert_div"></div>
                     <div id="alert_msg"></div>
                    <table id="ticketlist" class="table table-bordered table-striped">
                        <thead>
                            <tr>                                                    
                                <th>{{trans('affiliate/general.post_date')}}</th>   
								<th>{{trans('affiliate/general.id')}}</th>
                                <th>{{trans('affiliate/general.subject')}}</th>
                                <th>{{trans('affiliate/general.action')}}</th>                    
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
					</div>
                    
				</div>
			</div>
			<!-- ./col -->		
		</div>
        <div id="details_div" style="display:none">
        <div class="row">
            <div class="col-sm-8">
                <div id="err_msg_close"></div>
                <div class="box box-primary">
                    <!-- Description List Default Title -->
                    <div class="box-header with-border">
                        <div class="pull-right">
                            <button class="btn btn-default btn-sm backbtn close-it">  <i class="fa fa-times"></i>    {{trans('affiliate/general.close')}} </button>
                        </div>
                        <h4 class="box-title"><span id="tic_code"></span> - <span id="span_sub"></span></h4>
                    </div>
                    <div class="box-body">
                        <form class="form-horizontal" action="" method="">
                            <div class="form-group">
                            <div class="col-sm-3">
                                <label class="control-label" for="status_details_div">
                                </label>{{trans('affiliate/general.status')}}:
                                <span class="">  <b id="la-status"> </b>  </span>
                            </div>
                            <div class="col-sm-3">   
                                <label class="control-label" for="category_details_div"></label>{{trans('affiliate/general.category')}} :
                                <span class=""> <b id="la-category"> </b> </span>
                            </div>
                              <div class="col-sm-6"> 
                                <label class="control-label" for="attachment_details_div"></label>{{trans('affiliate/general.attachment')}} :
                                <div id="la-attachement">
                                <a id="la-attachement" href="{{ url( '/assets/img/tickets/".$detail[0]->attachment."')}}" target="_blank">{{ $detail[0]->attachment or ''}}</a></div>
                                <a id="la-attachement" href="{{ url( '/assets/img/tickets/')}}" target="_blank"><span class="">{{trans('affiliate/general.test')}}</span></a>
                               </div>
                               </div>
                               <div class="form-group"> 
                               	<div class="col-sm-12"> 
                                <strong>{{trans('affiliate/general.description')}}:</strong><br />
                                <div id="la-description"> 
                                    {{$detail[0]->description or ''}} 
                                </div>
                               </div>    
                            </div>
                        </form>
                    </div>
                </div>

                <div class="panel-group" id="accordion" >
                </div>

                <div class="panel panel-default box box-primary" id="post_reply_div">
                    <div class="box-header with-border" >
                        <h4 id="box-title" class="panel-title">{{trans('affiliate/general.post_a_reply')}}</h4>
                        
                    </div>
                    <div class="box-body">
                        <div id="reply_msg"></div>
                        <form class="form-horizontal" id="reply_forms" action="{{url('account/support/tickets_comment')}}" method="post" enctype="multipart/form-data" novalidate="novalidate">
                            {!! csrf_field() !!}
                            <input type="hidden" id="ticket_id_reply" name="ticket_id" value="">
                            
                            <div class="form-group">
                                <label class="col-sm-2" for="example-nf-description">{{trans('affiliate/general.description')}}  :</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="replay_comments" name="replay_comments"></textarea>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-2" for="example-nf-attachment">{{trans('affiliate/general.attachment')}}  :</label>
                                <div class="col-sm-10">
                                    <input type="file" id="file_attachment_comment" name="file_attachment_comment" size="30" >
                                    <span class="file_comment upload_format">{{trans('affiliate/support/support.support_file_formats')}}</span>
                                 </div>
                            </div>
                            <div class="form-group">
                             <label class="col-sm-2" for="example-nf-submit"></label>
                              <div class="col-sm-10">
                                <input type="submit" class="btn btn-sm btn-primary" value="{{trans('affiliate/general.submit_btn')}}" name="subject"/>
                               </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h4 id="feedback_panel_title" class="panel-title">{{trans('affiliate/general.submit_feedback_close_ticket')}}</h4>
                    </div>
                    <div class="box-body">
                        <div id="rating_msg">
                           </div>
                        <form class="form-horizontal" id="rating_form" action="{{url('account/support/tickets_close')}}" method="post">
                        {!! csrf_field() !!}
                            <input type="hidden" id="ticket_id" name="ticket_id" value="">
                            <input type="hidden" name="id" value="">
                            <div class="form-group">
                                <label class="col-sm-4" for="example-nf-open-at">{{trans('affiliate/general.opened_at')}}: </label><label for="example-nf-email">
                                <div id="opened_at"></div></label><br>
                                <label class="col-sm-4" for="example-nf-solve-at">{{trans('affiliate/general.solved_at')}}: </label><label for="example-nf-email">
                                <div id="solved_at"></div></label>
                            </div>
                            <form role="form">
                            <div class="form-group" id ="rate_sub_form">
                            	<div class="col-sm-12">
                                    <label for="example-nf-comments">{{trans('affiliate/general.leave_your_comments')}}:</label>
                                    <textarea class="form-control" name="comment" id="comment_input" placeholder="{{trans('affiliate/support/support.leave_ur_cmt')}}" ></textarea>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group"  id="rating_row">
                                <div class="col-sm-12">
                                    <label for="example-nf-rating" class="text-muted">{{trans('affiliate/general.overall_support_request')}}:</label><br>
                                    <label class="radio-inline"><input type="radio" name="rating" value="1">{{trans('affiliate/general.rate_poor')}}</label>
                                    <label class="radio-inline"><input type="radio" name="rating" value="2">{{trans('affiliate/general.rate_good')}}</label>
                                    <label class="radio-inline"><input type="radio" name="rating" value="3">
									{{trans('affiliate/general.rate_excellent')}}</label>
                                    <div id="rating_err"></div>
                                </div>
                            </div>
                               
                                <div class="form-group" id="rating_button">
                                   <div class="col-sm-12">
                                        <input type="submit" name="reteit" class="btn btn-sm btn-primary pull-left" value="{{trans('affiliate/support/support.submit_ticket')}}">  
                                   </div>
                                </div>
                            
                            </form>
                            <div class="feedback_body">
                             <div class="form-group">
                                <div id="feedback_comments">
                                    <label class="control-label">{{trans('affiliate/general.comments')}}:</label>
                                    <label class="">&nbsp;&nbsp;<small id="comments_fed"></small></label>
                                </div>
                               <div id="feedback_rating">
                                    <label class="control-label">{{trans('affiliate/general.rating')}}:</label>
                                    <label class="data control-label">&nbsp;&nbsp;<small id="rating_fed" class=""></small></label>
                               </div>
                              </div>
                           </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@include('user.common.datatable_js')
<script src="{{url('account/validate/lang/new-ticket')}}" charset="utf-8"></script> 
<script src="{{url('account/validate/lang/ticket_rating')}}" charset="utf-8"></script> 
<script src="{{url('account/validate/lang/ticket_replay')}} " charset="utf-8"></script> 
<script type="text/javascript" src="{{asset('js/providers/affiliate/support/tickets.js')}}"></script>
<script type="text/javascript" src="{{asset('js/providers/affiliate/support/ajax_form.js')}}"></script>

@stop