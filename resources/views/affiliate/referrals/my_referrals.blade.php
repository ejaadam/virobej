@extends('affiliate.layout.dashboard')
@section('title',"Direct Referral Report")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('affiliate/general.my_referrals')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('affiliate/general.dashboard')}}</a></li>
        <li >{{trans('affiliate/general.profile_pagehead')}}</li>
		<li class="active">{{trans('affiliate/general.my_referrals')}}</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
		<!-- Small boxes (Stat box) -->
		<div class="row">        
			<div class="col-md-12">
             <div class="box box-primary">
					<div class="box-header with-border">
	                   <form id="form_referrals" class="form form-bordered" action="{{URL::to('affiliate/referrals/my-referrals')}}" method="post">
                       		{!! csrf_field() !!}
                            <!-- <div class="col-lg-3">
							 <label>{{trans('affiliate/general.search_term')}}</label>
                               <div class="input-group">
                                  <input type="text" id="search_term" name="search_term" class="form-control" placeholder="{{trans('affiliate/withdrawal/history.search_term_phn')}}" value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" />
									<div class="input-group-btn ">
                               <button data-toggle="dropdown" class="btn btn-default" aria-expanded="true">{{trans('affiliate/general.filter')}} <span class="caret"></span></button>
                                     <ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right" id="chkbox">
                                      <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="UserName" type="checkbox" checked>{{trans('affiliate/referrels/my_referrels.username')}}</label></li>
									  <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="Fullname" type="checkbox" checked>{{trans('affiliate/referrels/my_referrels.full_name')}}</label></li>
                                      <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="Mobile" type="checkbox">{{trans('affiliate/referrels/my_referrels.mobile')}}</label></li>
                                      <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="Sponsor" type="checkbox">{{trans('affiliate/referrels/my_referrels.sponsor')}}</label></li>
                                      </ul>
                                    </div>
                                </div>
                            </div>-->
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
                                    <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn btn-sm bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('affiliate/general.export_btn')}}</button>
                                    <button type="submit" name="printbtn" id="printbtn" class="btn btn-sm bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 
					</div>
                    
                    <div class="box-body">
                    <table id="referralslist" class="table table-bordered table-striped">
                        <thead>
                            <tr>                                                    
                               
                               <!-- <th class="text-center">{{trans('affiliate/referrels/my_referrels.username')}}</th>
							    <th>{{trans('affiliate/referrels/my_referrels.name')}} </th>
                                <th>{{trans('affiliate/referrels/my_referrels.mobile')}} </th>
								<th>{{trans('affiliate/referrels/my_referrels.email_address')}} </th> -->
								<th>{{trans('affiliate/referrels/my_referrels.user')}} </th>
							    <th>{{trans('affiliate/referrels/my_referrels.status')}} </th>
								<th> {{trans('affiliate/referrels/my_referrels.signed_up_on')}}</th>
								<th> {{trans('affiliate/referrels/my_referrels.placement')}}</th>
                                <th> {{trans('affiliate/referrels/my_referrels.package_value')}}</th>
								<th>{{trans('affiliate/referrels/my_referrels.qv')}} </th>
							     <th>{{trans('affiliate/referrels/my_referrels.cv')}} </th>
								<!--<th>{{trans('affiliate/referrels/my_referrels.sponsor_uname')}} </th>
								<th>{{trans('affiliate/referrels/my_referrels.last_pack_purchased')}} </th>
								<th>{{trans('affiliate/referrels/my_referrels.last_purchased_on')}} </th>-->
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
<script src="{{asset('js/providers/affiliate/referrals/my_referrals.js')}}"></script>
@stop
