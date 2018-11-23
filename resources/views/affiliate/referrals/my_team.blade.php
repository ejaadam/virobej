@extends('affiliate.layout.dashboard')
@section('title','Team Referral Report')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('affiliate/general.my_team')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>{{trans('affiliate/general.dashboard')}}</a></li>
        <li >{{trans('affiliate/general.profile_pagehead')}}</li>
		<li class="active">{{trans('affiliate/general.my_team')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<div class="col-md-12">
             <div class="box box-primary">
					<div class="box-header with-border">
	                   <form id="downline_form" class="form form-bordered" action="{{url('affiliate/referrals/my_team')}}" method="post">
                       		{!! csrf_field() !!}
                            <!--<div class="col-lg-3">
							  <label>{{trans('affiliate/general.search_term')}}</label>
							  <div class="input-group">
									<input id="search_term" placeholder="Search Terms" name="search_term" class="form-control" type="text" placeholder="{{trans('affiliate/withdrawal/history.search_term_phn')}}" value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}">
								<div class="input-group-btn">
								 <button data-toggle="dropdown" class="btn btn-default" aria-expanded="true">{{trans('affiliate/general.filter')}} <span class="caret"></span></button>
								  <ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right" id="chkbox">
								     <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="UserName" type="checkbox" checked>{{trans('affiliate/referrels/my_referrels.username')}}</label></li>
									 <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="FullName" type="checkbox">{{trans('affiliate/referrels/my_referrels.full_name')}}</label></li>
								     <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="Group" type="checkbox">{{trans('affiliate/referrels/my_referrels.assigned_to')}}</label></li>
									 <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="InvitedBy" type="checkbox">{{trans('affiliate/referrels/my_referrels.invited_by')}}</label></li>
								  </ul>
								</div>
							  </div>
                            </div> -->
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" value="" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" value="" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group has-feedback">
                                    <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i>     {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i>     {{trans('affiliate/general.reset_btn')}}</button>
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn btn-sm bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('affiliate/general.export_btn')}}</button>
                                    <button type="submit" name="printbtn" id="printbtn" class="btn btn-sm bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                            
                        </form> 
					</div>
                    
                    <div class="box-body">
                    <table id="downlinelist" class="table table-bordered table-striped">
                        <thead>
                            <tr>                                                      
                               <!-- <th>{{trans('affiliate/referrels/my_referrels.username')}}</th>
                                <th>{{trans('affiliate/referrels/my_referrels.full_name')}}</th> -->
                                <th>{{trans('affiliate/referrels/my_referrels.user')}}</th>
                                <th>{{trans('affiliate/referrels/my_referrels.sponser')}}</th>
							    <th>{{trans('affiliate/referrels/my_referrels.signed')}}</th> 
                                <th>{{trans('affiliate/referrels/my_referrels.upline')}}</th>
                                <th>{{trans('affiliate/referrels/my_referrels.rank')}}</th>
                                <th>{{trans('affiliate/referrels/my_referrels.placement')}}</th>  
                                <th>{{trans('affiliate/referrels/my_referrels.qv')}}</th>                            
                                <th>{{trans('affiliate/referrels/my_referrels.cv')}}</th>
                                <!--   <th>{{trans('affiliate/general.status')}}</th> -->
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
					</div>
				</div>
			</div>			
		</div>
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@include('affiliate.common.datatable_js')
<script src="{{asset('js/providers/affiliate/referrals/my_team.js')}}"></script>
@stop
