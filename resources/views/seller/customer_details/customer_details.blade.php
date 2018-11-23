@extends('supplier.common.layout')
@section('pagetitle','Orders')
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')
<style>
    .info {
        height:71px;
        border-left:0px ! important;
    }
    .align{
        padding-top:25px ! important;
        border-right:0px ! important;
        border-left:1px solid #CCC ! important;
    }
    .panel-heading {
        line-height: 2.5;
    }
</style>
<div id="main_content ">
    <!-- main content -->
    <div class="row">
        <div class="col-sm-12"> <span class="message"></span>
            <div class="successmsg"></div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title"></h4> Customer  List </h4>
                </div>
                <table id="dt_basic" class="table table-striped">
                    <thead>
                        <tr>
                            <th nowrap="nowrap">Customer Name</th>
                            <th>Email ID</th>
                            <th>Mobile Number</th>
                            <th>No Of  Orders</th>


                        </tr>
                    </thead>
                    <div class="panel_controls">
                        <div class="row">

                            <div class="input-group col-sm-3">
                                <input type="text" id="search_term" name="search_term" class="form-control">

                            </div>
                            <div class="col-sm-1">
                                <button id="search" class="btn btn-primary btn-sm">Search</button>
                            </div>
                            <div class="col-sm-1">
                                <button id="clear_filter" class="btn btn-primary btn-sm">Clear Search</button>
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


@include('admin.common.assets')
@stop
@section('scripts')
{{ HTML::script('supports/supplier/customer_details.js') }}
@stop
