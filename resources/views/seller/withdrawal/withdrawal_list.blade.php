@extends('supplier.common.layout')
@section('pagetitle')
{{$pg_title}}
@stop
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')
<div id="withdraw_block" class="row" data-status="{{$status_key}}">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title col-sm-6">{{$pg_title}}</h4>
            </div>
            <div class="panel_controls">
                <div class="row">
                    <div id="msg_content"></div>
                    <form id="withdrawal_list_frm" method="post">
                        <div class="col-sm-2">
                            <input type="text" placeholder="{{Lang::get('general.search_terms')}}" name="search_term" id="Search Term" class="form-control" />
                        </div>
                        <div class="col-sm-2">
                            <input type="text" placeholder="{{Lang::get('general.from_date')}}" name="from" id="from" class="form-control" />
                        </div>
                        <div class="col-sm-2">
                            <input type="text" placeholder="{{Lang::get('general.to_date')}}" name="to" id="to" class="form-control"/>
                        </div>
                        <div class="col-sm-2">
                            <select name="payout_type" id="payout_type" class="form-control">
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select name="currency" id="currency_id" class="form-control">
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <input type="button"  class="btn btn-sm btn-primary" id="search" value="Search" name="search_term" />
                        </div>

                    </form>
                </div>
            </div>

            <table id="withdraw_list_tbl" class="table table-bordered table-striped" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th> {{Lang::get('general.requested_on')}}</th>
                        <th>{{Lang::get('general.transaction_id')}}</th>
                        <th>Requested Amount</th>
                        <th>Charges</th>
                        <th>Net Amount</th>
                        <th>{{Lang::get('general.payment_mode')}}</th>
                        @if ($status==Config::get('constants.WITHDRAWAL_STATUS.CONFIRMED'))
                        <th>{{Lang::get('general.transferred_on')}}</th>
                        @elseif ($status==Config::get('constants.WITHDRAWAL_STATUS.CANCELLED'))
                        <th>Cancelled On</th>
                        @else
                        <th>{{Lang::get('general.edoc')}}</th>
                        @endif
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="cancel_info">

</div>
<div id="withdrawal_details" class="row" style="display:none;">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="pull-right">
                    <div class="options">

                    </div>
                    <a class="btn btn-xs btn-danger close-withdrawal_details"><i class="fa fa-times"></i></a>
                </div>

                <h4 class="panel-title col-sm-6"></h4>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="form-label col-sm-4">Requested On</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="created_on"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label col-sm-4">Payment Type</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="payment_type"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label col-sm-4">Status</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="status"></p>
                    </div>
                </div>
                <table class="table table-bordered table-stripped" id="conversion_details">
                    <thead>
                        <tr><th colspan="5">Related Transactions</th></tr>
                        <tr>
                            <th>Wallet</th>
                            <th>From Amount</th>
                            <th>To Amount</th>
                            <th>Debit Transaction ID</th>
                            <th>Credit Transaction ID</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

                <table class="table table-bordered table-stripped" id="account_info">
                    <thead><tr><th colspan="2">Account Information</th></tr></thead>
                    <tbody>

                    </tbody>
                </table>
                <div class="form-group">
                    <label class="form-label col-sm-4">Amount</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="amount"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label col-sm-4">Charges</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="handleamt"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label col-sm-4">Net Amount</label>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="paidamt"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@section('scripts')
{{ HTML::script('supports/supplier/withdrawal/withdrawal_list.js') }}
@stop

