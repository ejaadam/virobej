@extends('admin.common.layout')
@section('title',trans('admin/finance.online_payment'))
@section('breadcrumb')
<li><a href="#"><i class="fa fa-dashboard"></i> {{trans('admin/finance.online_payment')}}</a></li>
@stop
@section('layoutContent')
<div class="panel panel-default">
    
        <div class="panel-body"  id="list">
			<form id="form" class="form form-bordered" action="{{route('admin.finance.order-payments')}}" method="get">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="terms"> {{trans('general.search')}}</label>
                        <input type="search" id="terms" name="terms" class="form-control" placeholder="search terms" value=""/>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group has-feedback">
                        <label for="from_date"> {{trans('admin/general.frm_date')}}</label>
                        <input type="date" id="from_date" name="from_date" class="form-control datepicker"/> <i class="fa fa-calendar form-control-feedback"></i>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group has-feedback">
                        <label for="to_date"> {{trans('admin/general.to_date')}}</label>
                        <input type="date" id="to_date" name="to_date" class="form-control datepicker"/><i class="fa fa-calendar form-control-feedback"></i>
                    </div>
                </div>
				<div class="col-sm-3">
                    <div class="form-group has-feedback">
                        <label for="purpose">Description</label>
                        <select name="purpose" class="form-control" id="purpose">
                            <option value="">All</option>
							@if(!empty($purpose))
							@foreach ($purpose as $k=>$v)
						    <option value="{{$k}}">{{$v}}</option>
						    @endforeach
						    @endif
                        </select>						
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group has-feedback">
                        <label for="status">{{trans('admin/general.status')}}</label>
                        <select name="status" class="form-control" id="status">
                            <option value="">All</option>
							@if(!empty($status))
							@foreach ($status as $k=>$v)
						    <option value="{{$k}}">{{$v}}</option>
						    @endforeach
						    @endif
                        </select>						
                    </div>
                </div>
                <div class="col-sm-6">
				  <label for="">&nbsp;</label>
                    <div class="form-group">
                        <div class=" btn-group">
                            <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i>&nbsp;&nbsp;{{trans('general.btn.search')}}</button>
                            <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i>&nbsp;&nbsp;{{trans('general.btn.reset')}}</button>
                        </div>
                    </div>
                </div>
            </form>
            <table id="log_table" class="table table-bordered table-striped" >
                <thead>
                    <tr>
                        <th>{{trans('general.label.created_on')}}</th>
                        <th>{{trans('admin/finance.report.trans_id')}}</th>
                        <th>{{trans('admin/finance.report.paid_by')}}</th>
                        <th>{{trans('admin/finance.report.gateway')}}</th>
                        <th>{{trans('admin/finance.report.amt')}}</th>
                        <th>{{trans('admin/finance.report.description')}}</th>
                        <th>{{trans('admin/finance.report.payment_status')}}</th>
                        <th>{{trans('admin/finance.report.status')}}</th>
                        <th>{{trans('general.label.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    
</div>
<div id="details" style="display:none" class="">
</div>
@stop
@push('scripts')
<script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>
<script src="{{asset('js/providers/admin/finance/online_payments.js')}}"></script>
@endpush
@include('admin.common.datatable_js')
