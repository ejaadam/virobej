@extends('affiliate.layout.dashboard')
@section('title',"Fund Transfer History")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('affiliate/general.fund_transfer_history')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>{{trans('affiliate/general.dashboard')}}</a></li>
        <li>{{trans('affiliate/general.wallet')}}</li>
        <li class="active">{{trans('affiliate/general.fund_transfer_history')}}</li>
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
	                   <form id="form_fundtransfer" class="form form-bordered" action="{{route('aff.wallet.fundtransfer.history')}}" method="post">
                       		{!! csrf_field() !!}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="from">{{\trans('affiliate/fund_transfer_history.username')}}</label>
                                    <input type="text" id="search_term" name="search_term" class="form-control col-xs-12"  value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}" placeholder="{{trans('affiliate/withdrawal/history.search_term_phn')}}">
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
                                    <label for="from"> {{trans('affiliate/general.frm_date')}}</label>
                                    <input type="text" id="to_date" name="to_date" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" /><i class="fa fa-calendar form-control-feedback"></i>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="wallet_id"> {{trans('affiliate/wallet/transactions.wallet')}} </label>
                                    <select name="wallet_id" id="wallet_id" class="form-control">
                                        <option value="">{{trans('affiliate/withdrawal/history.all_payments')}}</option>
                                        @if(!empty($wallet_list))
                                         @foreach ($wallet_list as $wallet)
                                        <option value="{{$wallet->wallet_id}}">{{$wallet->wallet}}</option>
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
                                    <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                    <button type="submit" name="exportbtn" id="exportbtn" class="btn btn-sm bg-blue" value="Export"><i class="fa fa-file-excel-o"></i> {{trans('affiliate/general.export_btn')}}</button>
                                    <button type="submit" name="printbtn" id="printbtn" class="btn btn-sm bg-blue" value="Print"><i class="fa fa-print"></i> {{trans('affiliate/general.print_btn')}}</button>
                                </div>
                            </div>
                        </form> 
					</div>
                    
                    <div class="box-body">
                    <table id="fundtransferlist" class="table table-bordered table-striped">
                        <thead>
                            <tr>                                                    
                                <th width="15%">{{trans('affiliate/fund_transfer_history.transfered_on')}}</th>  
                                <th width="15%">{{trans('affiliate/fund_transfer_history.transaction_id')}}</th>
                                <th>{{trans('affiliate/fund_transfer_history.from_account')}}</th>
                                <th>{{trans('affiliate/fund_transfer_history.to_account')}}</th>                                
                                <th width="12%">{{trans('affiliate/fund_transfer_history.wallet_name')}}</th>                                
                                <th width="10%">{{trans('affiliate/general.amount')}}</th>
                                <th width="10%">{{trans('affiliate/fund_transfer_history.paidamt')}}</th>    
                                <th width="5%">{{trans('affiliate/general.status')}}</th>                            
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
<script src="{{asset('js/providers/affiliate/wallet/fundtransfer_history.js')}}"></script>
@stop