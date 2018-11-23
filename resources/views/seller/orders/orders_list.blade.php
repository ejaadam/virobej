@extends('seller.common.layout')
@section('pagetitle')
{{ucfirst($order_status)}} Orders
@stop

@section('layoutContent')
<div id="main_content">
    <!-- main content -->
    <div class="row"><span class="message"></span>
        <div class="col-sm-12" id="orderbox">
            <div class="successmsg"></div>
            <div class="panel panel-default" id="orderreport">
                <div class="panel-heading">
                    <h4 class="panel-title"></h4>{{ucfirst($order_status)}} </h4>
                </div>
                <table id="dt_basic" class="table table-striped" data-status="{{$order_status}}">
                    <thead>
                        <tr>
                            <th>Ordered On</th>
                            <th>Sub Order Code</th>
                            <th>Customer Name</th>
                            <th>Quantity</th>
                            <th>Net Pay</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <div class="panel_controls">
                        <div class="row">
                            <form id="order_list" name="order_list" >
                                <div class="col-sm-2">
                                    <div class="input-group date ebro_datepicker" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                        <input class="form-control" type="text"name="start_date" id="start_date" placeholder="From">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span> </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group date ebro_datepicker" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                        <input class="form-control" type="text" name="end_date" id="end_date" placeholder="To">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span> </div>
                                </div>
                                <div class="input-group col-sm-4">
                                    <input type="text" id="search_term" name="search_term" class="form-control">
                                    <div class="input-group-btn">
                                        <button data-toggle="dropdown" class="btn  btn-default ">Filter <span class="caret"></span></button>
                                        <ul class="dropdown-menu  dropdown-menu-form dropdown-menu-right">
                                            <li><label class="col-sm-12"><input type="checkbox" id="full_name"  name="filterTerms"  value="full_name"></input>Customer Name</label></li>
                                            <li><label class="col-sm-12"><input type="checkbox" id="code_order"  name="filterTerms"  value="order_code"></input> Order Code</label></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <button id="search" type="button" class="btn btn-primary btn-sm">Search</button>
                                    <!--input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Export" formtarget="_new"/>
                                    <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Print" formtarget="_new"/-->
                                    <button id="clear_filter" class="btn btn-primary btn-sm">Clear Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div id="orderdetails"></div>
        </div>
    </div>
</div>
@include('seller.common.assets')
@stop
@section('scripts')
<script src="{{asset('resources/supports/supplier/orders_list.js')}}"></script>
@stop
