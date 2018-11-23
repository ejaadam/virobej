@extends('affiliate.layout.dashboard')
@section('title',"Personal Customer Commission")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Personal Customer Commission</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li>Bonus</li>
        <li class="active">Personal Customer Commission </li>
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
	                   <form id="personal_customer_commission" class="form form-bordered" action="" method="post">
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
                    <table id="personal_commission" class="table table-bordered table-striped">
                       <thead>
                            <tr>              
                                 <th class="text-center">{{trans('affiliate/bonus/personal_commission.period')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.directs_cv')}}</th>
							     <th>{{trans('affiliate/bonus/personal_commission.self_cv')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.slab')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.total_cv')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.earnings')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.commission')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.tax')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.ngo_wallet')}}</th>
                                 <th>{{trans('affiliate/bonus/personal_commission.net_pay')}}</th>
								  <th>{{trans('affiliate/bonus/personal_commission.status')}}</th>
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
 <script src="{{asset('js/providers/affiliate/bonus/personal_commission.js')}}"></script>
@stop