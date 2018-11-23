@extends('seller.common.layout')
@section('pagetitle')
Payment Report
@stop

@section('layoutContent')
<div class="pageheader">
    <div class="row">
        <div id="alert-div"></div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Payment Report</h4>
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <form class="form" action="#" method="post" id="transaction_report">
                            <div class="col-sm-3">
                                <input type="text"  placeholder="Search Term" name="term" id="term" class="form-control"/>
                            </div>
                            <div class="col-sm-2">
                                <input type="text"  placeholder="From Date" name="from" id="from" class="form-control"/>
                            </div>
                            <div class="col-sm-2">
                                <input type="text"  placeholder="To Date" name="to" id="to" class="form-control"/>
                            </div>
                            <div class="col-sm-3">
                                <button id ="search" type="button" class="btn btn-sm btn-primary" title="Search">Search</button>
                                <!--button name ="submit" type="submit" class="btn btn-sm btn-primary" title="Export" value="Export"/>Export</button>
                                <button name ="submit" type="submit" class="btn btn-sm btn-primary" title="Print" value="Print"/>Print</button-->
                            </div>
                        </form>
                    </div>
                </div>
                <table id="transaction_report_datatable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product Info</th>
                            <th>Order Code</th>
                            <th>Item Code</th>
                            <th>MRP Price</th>
                            <th>Discount(%)</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Site Commission Tot.</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('resources/supports/supplier/payment_report.js')}}"></script>
@stop
