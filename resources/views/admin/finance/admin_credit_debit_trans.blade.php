@extends('admin.common.layout')
@section('pagetitle')
Affiliate
@stop
@section('top_navigation')
@include('admin.top_nav.supplier_navigation')
@stop
@section('layoutContent')
<div class="row">
    <div class="col-sm-12">
	<div class="col-md-12" id="users-list-panel">
        <div class="panel panel-default" id="list">
            <div class="panel-heading">

                <h4 class="panel-title">Admin Credit & Debit Report</h4>
            </div>
            <div class="panel_controls">
                <div class="row">
                <form id="form" class="form form-bordered" action="{{route('admin.finance.admin-credit-debit-history')}}" method="post">
                        <input type="hidden" class="form-control" id="status_col"  value ="status_value">
                        <div class="input-group col-sm-3">
                             <label for="from"> {{trans('general.search')}}</label>
                             <input type="search" id="terms" name="terms" class="form-control" placeholder="search terms" value=""/>
                        </div>
						 <div class="input-group col-sm-3">
                        <label for="from">{{trans('admin/finance.trans_type')}}</label>
                        <select name="trans_type" id="trans_type" class="form-control">
                            <option value="">Select</option>
                            <option value="1">Credit</option>
                            <option value="0">Debit</option>
                        </select>
                           
                        </div>
                        <div class="col-sm-3">
						 <label for="from">{{trans('admin/general.date')}}</label>
                            <div class="input-group">
							
                                <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                 <input class="form-control" type="text" id="from" name="from" placeholder="From">
                                <span class="input-group-addon">-</span>
                                 <input class="form-control" type="text" id="to" name="to" placeholder="To">
                            </div>
                        </div>
                        <div class="col-sm-3">
						 <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i>     {{trans('general.btn.search')}}</button>
                        
							 <button type="submit" name="exportbtn" id="exportbtn" class="btn btn-primary btn-sm exportBtns" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('admin/general.export_btn')}}</button>
                             <button type="submit" name="printbtn" id="printbtn" class="btn btn-primary btn-sm exportBtns" value="Print"><i class="fa fa-print"></i>   {{trans('admin/general.print_btn')}}</button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="msg"></div>
             <table id="hist_table" class="table table-bordered table-striped" >
                <thead>
                    <tr>
                        <th>{{trans('admin/finance.report.date')}}</th>
                        <th>{{trans('admin/finance.report.trans_id')}}</th>
                        <th>User Name</th>
                        <th>{{trans('admin/finance.report.wallet')}}</th>
                        <th>{{trans('admin/finance.report.amt')}}</th>
                        <th>{{trans('admin/finance.report.hdl_amt')}}</th>
                        <th>{{trans('admin/finance.report.paid_amt')}}</th>
                        <th>{{trans('admin/finance.report.trans_by')}}</th>
                        <th>{{trans('admin/finance.report.status')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
       
    </div>
    </div>
	

	
</div>


@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>
<script src="{{asset('js/providers/admin/finance/admin_credit_debit_trans.js')}}"></script>
@stop
