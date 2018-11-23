@extends('affiliate.layout.dashboard')
@section('title',"Withdrawal")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{$withdrawal_status}} - Withdrawal</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>Withdrawal</li>
        <li class="active">Withdrawal</li>
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
	                   <form id="withdrawal_log" class="form form-bordered" action="" method="post">
                       {!! csrf_field() !!}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="from">{{\trans('affiliate/withdrawal/history.customer_id')}}</label>
                                    <input type="text" id="search_term" name="search_term" class="form-control col-xs-12"  value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" placeholder="{{trans('affiliate/withdrawal/history.search_term_phn')}}">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="from" name="from" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" />
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="from"> {{trans('affiliate/general.to_date')}}</label>
                                    <input type="text" id="to" name="to" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="wallet_id"> {{trans('affiliate/withdrawal/history.payment_types')}} </label>
                                    <select name="payment_type_id" id="payment_type_id" class="form-control">
                                        <option value="">{{trans('affiliate/withdrawal/history.all_payments')}}</option>
                                        @if(!empty($payments))
                                         @foreach ($payments as $pay_name)
                                        <option value="{{$pay_name->withdrawal_payout_type_id}}">{{$pay_name->withdrawal_payout_type}}</option>
                                       	@endforeach
                                       @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="wallet_id"> {{trans('affiliate/withdrawal/history.currencies')}} </label>
                                    <select name="currency_id" id="currency_id" class="form-control">
                                        <option value="">{{trans('affiliate/withdrawal/history.all_currencies')}}</option>
                                        @if(!empty($currencies))
                                        @foreach ($currencies as $curItem)
                                        <option value="{{$curItem->currency_id}}">{{$curItem->currency_code}}</option>
                                       	@endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <button type="button" id="search_btn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="reset_btn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                    <button type="submit" id="exportbtn" name="exportbtn" value="Export" class="btn btn-sm bg-blue"><i class="fa fa-file-excel-o"></i>  {{trans('affiliate/general.export_btn')}}</button>
                                    <button type="submit" id="printbtn" name="printbtn" value="Print" class="btn btn-sm bg-blue"><i class="fa fa-print"></i>  {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 
					</div>
                    
                    <div class="box-body">
                    <table id="withdrawallist" class="table table-bordered table-striped">
                        <thead>
                            <tr>                                                    
                                <th>{{trans('affiliate/withdrawal/history.requested_on')}}</th>  
                                <th>{{trans('affiliate/withdrawal/history.username')}}</th>
                                <th>{{trans('affiliate/general.country')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.payment_mode')}}</th>
                                <th>{{trans('affiliate/general.amount')}}</th>
                                <!--<th>{{trans('affiliate/withdrawal/history.currency_code')}}</th>-->
                                <th>{{trans('affiliate/withdrawal/history.charges')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.netpay')}}</th>
                                <th>{{trans('affiliate/general.status')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.expected_date_of_credit')}}</th>
                                <th>{{trans('affiliate/withdrawal/history.updated_on')}}</th>  
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
@include('user.common.datatable_js')
<script src="{{asset('js/providers/affiliate/withdrawal/withdrawal.js')}}"></script>
@stop