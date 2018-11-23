@extends('supplier.common.layout')
@section('pagetitle','Stocks')
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')
<div class="pageheader">
    <div class="row">
        <div id="alert-msg"></div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title col-sm-6">Stock List</h4>
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <form id="product_stock_list">
                            <div class="col-sm-2">
                                <input type="text" name="search_term" placeholder="Search terms" id="search_term" class="form-control">
                            </div>
                            <div class="col-sm-2">
                                <select name="brand_id" id="brand_id" class="form-control category">
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select name="category_id" id="category_id" class="form-control category">
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group date ebro_datepicker col-sm-6 pdL" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                    <input class="form-control" type="text" id="from" name="from" placeholder="From">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                </div>
                                <div class="input-group date ebro_datepicker col-sm-6 pdR" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                    <input class="form-control" type="text" id="to" name="to" placeholder="To">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button id="search" type="button" class="btn btn-primary btn-sm">Search</button>
                                <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Export" formtarget="_new"/>
                                <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Print" formtarget="_new"/>
                            </div>
                        </form>
                    </div>
                </div>
                <table id="stock_list" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Updated On</th>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Available</th>
                            <th>In-Progress</th>
                            <th>Sold</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
{{ HTML::script('supports/supplier/stock/product_list.js') }}
@stop
