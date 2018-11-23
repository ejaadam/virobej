@extends('affiliate.layout.dashboard')
@section('title',trans('affiliate/package.mypackage_page_title'))
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><i class="fa fa fa-files-o"></i>{{\trans('affiliate/package.mypackage_page_title')}}</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>{{\trans('affiliate/dashboard.page_title')}}</a></li>
        <li>{{\trans('affiliate/package.package_page_title')}}</li>
        <li class="active">{{\trans('affiliate/package/subscriptions.mypackage_page_title')}}</li>
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
                        <form id="transaction_log" class="form form-bordered" action="{{URL::to('user/transaction_log')}}" method="post">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="from">{{trans('affiliate/wallet/transactions.search_term')}}</label>
                                    <input type="text" id="search_term" name="account_search_termterm" class="form-control is_valid_string col-xs-12"  value="{{(isset($search_term) && $search_term != '') ? $search_term : ''}}">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="wallet_id"> {{trans('affiliate/wallet/transactions.wallet')}} </label>
                                    <select name="wallet_id" id="wallet_id" class="form-control">                                       
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="from"> {{trans('affiliate/wallet/transactions.from_date')}}</label>
                                    <input type="text" id="from" name="from" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.from_date_phn')}}" />
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label  for="to"> {{trans('affiliate/wallet/transactions.to_date')}} </label>
                                    <input type="text" id="to" name="to" class="form-control datepicker" placeholder="{{trans('affiliate/wallet/transactions.to_date_phn')}}" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" style="margin-top:25px;">
                                    <button type="button" id="search_btn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i> {{trans('affiliate/general.search_btn')}}</button>
                                    <button type="button" id="reset_btn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i> {{trans('affiliate/general.reset_btn')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="box-body">
                    <table id="subscriptions" class="table table-bordered table-striped">
                        <thead>
                            <tr>                                                    
                                <th>{{trans('affiliate/package/subscriptions.tb_purchased_date')}}</th>
                                <th>{{trans('affiliate/package/subscriptions.tb_description')}}</th>
                                <th>{{trans('affiliate/package/subscriptions.tb_paymode')}}</th>
                                <th>{{trans('affiliate/general.amount')}}</th>
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
<script src="{{asset('js/providers/affiliate/package/subscriptions.js')}}"></script>
@stop