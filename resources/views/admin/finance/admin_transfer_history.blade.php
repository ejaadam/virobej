@extends(request()->ajax()?'admin.layout.json_layout':'admin.layout.layout')
@section('title','Admin Fundransfer History'))
@section('breadcrumb')
<li><a href="#"><i class="fa fa-dashboard"></i> 'Admin Fundransfer History'</a></li>
@stop
@section('layoutContent')
<div class="col-md-12">
    <div class="box box-primary" id="member_list">
        <div class="box-header with-border" >
            <form id="form" class="form form-bordered" action="{{route('admin.finance.admin-transfer-history')}}" method="post">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="terms"> {{trans('general.search')}}</label>
                        <input type="search" id="terms" name="terms" class="form-control" placeholder="search terms"/>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group has-feedback">
                        <label for="from_date"> {{trans('admin/general.frm_date')}}</label>
                        <input type="date" id="from_date" name="from_date" class="form-control datepicker"/> <i class="fa fa-calendar form-control-feedback"></i>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group has-feedback">
                        <label for="to_date"> {{trans('admin/general.to_date')}}</label>
                        <input type="date" id="to_date" name="to_date" class="form-control datepicker"/><i class="fa fa-calendar form-control-feedback"></i>
                    </div>
                </div>
			    <div class="col-sm-2">
                    <label for="from">&nbsp;</label>
                    <div class="form-group">
                        <div class="btn-group">
                            <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i>     {{trans('general.btn.search')}}</button>
                            <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i>     {{trans('general.btn.reset')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            <div id="status_msg"></div>
            <table id="hist_table" class="table table-bordered table-striped" >
                <thead>
                    <tr>
                        <th>{{trans('admin/finance.report.date')}}</th>
                        <th>{{trans('admin/finance.report.trans_id')}}</th>
                        <th>{{trans('admin/finance.report.trans_from')}}</th>
                        <th>{{trans('admin/finance.report.trans_to')}}</th>
                        <th>{{trans('admin/finance.report.wallet')}}</th>
                        <th>{{trans('admin/finance.report.amt')}}</th>
                        <th>{{trans('admin/finance.report.hdl_amt')}}</th>
                        <th>{{trans('admin/finance.report.paid_amt')}}</th>
                        <th>{{trans('admin/finance.report.status')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
@push('scripts')
<script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>
<script src="{{asset('js/providers/admin/finance/admin_transfer_history.js')}}"></script>
@endpush
@include('admin.common.datatable_js')
