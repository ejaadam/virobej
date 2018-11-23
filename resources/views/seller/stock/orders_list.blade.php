@extends('supplier.common.layout')
@section('pagetitle')
Orders
@stop
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')

<div id="main_content ">
    <!-- main content -->
    <div class="row">
        <div class="col-sm-12" id="orderbox"> <span class="message"></span>
            <div class="successmsg"></div>
            <div class="panel panel-default" id="orderreport">
                <div class="panel-heading">
                    <h4 class="panel-title"></h4>Orders List </h4>
                </div>
                <table id="orders_list" class="table table-striped">
                    <thead>
                        <tr>
                            <th nowrap="nowrap">Order Date</th>
                            <th>Order Code</th>
                            <th>Customer Name</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Discount</th>
                            <th>Net Pay</th>
                            <th>Status</th>
                            <th>Updated On</th>
                        </tr>
                    </thead>
                    <div class="panel_controls">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="input-group date ebro_datepicker" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                    <input class="form-control" type="text"name="start_date" id="start_date" placeholder="From">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span> </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group date ebro_datepicker" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                    <input class="form-control" type="text" name="end_date" id="end_date" placeholder="To">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span> </div>
                            </div>
                            <div class="input-group col-sm-1">

                            </div>
                            <div class="col-sm-1">
                                <button id="search" class="btn btn-primary btn-sm">Search</button>
                            </div>
                            <div class="col-sm-1">

                            </div>
                        </div>
                    </div>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop
@section('scripts')
{{ HTML::script('supports/supplier/stock/order_list.js') }}
@stop
