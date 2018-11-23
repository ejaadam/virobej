@extends('affiliate.layout.dashboard')
@section('title',"Fast start Bonus")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Fast start Bonus</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li>Bonus</li>
        <li class="active">Fast start Bonus</li>
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
	                   <form id="form_referral_bonus" class="form form-bordered" action="{{URL::to('affiliate/reports/fast_start')}}" method="post">
                       		{!! csrf_field() !!}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="from">{{\trans('affiliate/fund_transfer_history.username')}}</label>
									   <div class="input-group">
                                    <input type="text" id="search_term" name="search_term" class="form-control col-xs-12"  value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" placeholder="{{trans('affiliate/withdrawal/history.search_term_phn')}}">
									<div class="input-group-btn ">
                               <button data-toggle="dropdown" class="btn btn-default" aria-expanded="true">{{trans('affiliate/general.filter')}} <span class="caret"></span></button>
                                     <ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right" id="chkbox">
									  <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="FromUser" type="checkbox" >{{trans('affiliate/bonus/referral_bonus.from_user')}}</label></li>
                                      <li><label class="col-sm-12"><input name="filterchk[]" class="filterchk" value="Referral" type="checkbox">{{trans('affiliate/bonus/referral_bonus.referrer')}}</label></li>
                                      </ul>
                                    </div>
									</div>
                                </div>
                            </div>
                             
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('admin/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('admin/general.reset_btn')}}</button>
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn btn-sm bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('admin/general.export_btn')}}</button>
                                    <button type="submit" name="printbtn" id="printbtn" class="btn btn-sm bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('admin/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 
					</div>
                    
                    <div class="box-body">
                    <table id="faststart_bouns_list" class="table table-bordered table-striped">
                        <thead>
                                                  
                           <tr>
						   <th>{{trans('affiliate/bonus/faststart.username')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.packagename')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.dateofpurchase')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.amount')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.qv')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.Earnings')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.Commission')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.tax')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.ngo')}}</th>
						   <th>{{trans('affiliate/bonus/faststart.netpay')}}</th>
						   
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
		<!-- /.row -->
    </section>
    <!-- /.content -->
@stop
@section('scripts')
@include('affiliate.common.datatable_js')
<script src="{{asset('js/providers/affiliate/bonus/faststart.js')}}"></script>
@stop

