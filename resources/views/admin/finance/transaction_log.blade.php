@extends('admin.common.layout')
@section('title',$title)
@section('breadcrumb')
@foreach($breadcrumb as $li)
<li><a href="#"><i class="{{$li['icon']}}"></i> {{$li['title']}}</a></li>
@endforeach
@stop
@section('layoutContent')
<div class="col-md-12">
    <div class="box box-primary" id="transactions-list">
        <div class="box-header with-border" >
            <form id="form" action="{{url()->current()}}" class="form form-bordered" method="get">
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
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class=" btn-group">
                            <button type="button" id="searchbtn" class="btn btn-sm bg-olive"><i class="fa fa-search"></i>&nbsp;&nbsp;{{trans('general.btn.search')}}</button>
                            <button type="button" id="resetbtn" class="btn btn-sm bg-orange"><i class="fa fa-repeat"></i>&nbsp;&nbsp;{{trans('general.btn.reset')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            <div id="status_msg"></div>
            <table id="log_table" class="table table-bordered table-striped" >
                <thead>
                    <tr>
                        <th>{{trans('admin/finance.report.date')}}</th>
                        <th>Full name</th>
                        <th>Description</th>
                        <th>Amt</th>
                        <th>Current Balance</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="box box-primary" id="transactions-details" style="display:none;">
        <div class="box-header with-border">
            <i class="fa fa-money"></i>
            <h3 class="box-title">Transaction Details</h3>
            <div class="box-tools">
                <button class="btn btn-sm btn-danger pull-right" id="back"><i class="fa fa-lg fa-arrow-circle-left margin-r-5"></i>Back</button>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-striped" >

            </table>
        </div>
    </div>
</div>
@stop
@push('scripts')
<script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>
<script src="{{asset('js/providers/admin/finance/transaction_log.js')}}"></script>
@endpush
@include('admin.common.datatable_js')
