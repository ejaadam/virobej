@extends('affiliate.layout.dashboard')
@section('title',"Leadership Bonus")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Leadership Bonus</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li>Bonus</li>
        <li class="active">Leadership Bonus</li>
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
	                   <form id="leadership_bonus_details" class="form form-bordered" action="{{URL::to('affiliate/reports/leadership')}}" method="post">
                       		{!! csrf_field() !!}
                           
                            <div class="col-sm-3">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="from_date" name="from_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group has-feedback">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn btn-sm bg-blue" value="Export"><i class="fa fa-file-excel-o"></i>{{trans('affiliate/general.export_btn')}}</button>
                                    <button type="submit" name="printbtn" id="printbtn" class="btn btn-sm bg-blue" value="Print"><i class="fa fa-print"></i>   {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 
					</div>
                    
                    <div class="box-body">
                    <table id="leadership_bonus_commission" class="table table-bordered table-striped">
                       <thead>
                            <tr>                                                    
                                 <th class="text-center">{{trans('affiliate/bonus/leadership_bonus.period')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.1g_2g_qv_cf')}}</th>
							     <th>{{trans('affiliate/bonus/leadership_bonus.3g_qv_cf')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.1g_2g_newqv')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.3g_newqv')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.total_1g_2g')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.total_3g_qv')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.matching_qv')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.earnings')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.commission')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.tax')}}</th>
								 <th>{{trans('affiliate/bonus/leadership_bonus.ngo_wallet')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.net_pay')}}</th>
                                 <th>{{trans('affiliate/bonus/leadership_bonus.status')}}</th>
                                                                               
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
 <script src="{{asset('js/providers/affiliate/bonus/leadership_bonus.js')}}"></script>
@stop