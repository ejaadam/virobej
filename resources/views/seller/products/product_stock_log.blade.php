@extends('supplier.layouts.dashboard')
@section('pagetitle')
Stock Login
@stop
@section('layoutContent')
<style>
    .thumb-image {
        float:left;
        width:100px;
        position:relative;
        padding:5px;
    }
    .info {
        height:71px;
        border-left:0px ! important;
    }
    .align{
        padding-top:25px ! important;
        border-right:0px ! important;
        border-left:1px solid #CCC ! important;
    }
    .select_font {
        color: #666E77 ! important;
    }
</style>
{{HTML::style('supports/member/cropperjs/cropper.css')}}

<div class="pageheader">
    <div class="row">
        <div id="alert-msg" class="alert-msg"></div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title col-sm-6">stock log List</h4>
                    <button type="button" id="new_product" class="btn btn-success btn-sm pull-right"><span class="icon-plus"></span>Add New Product </button>
                </div>
                <!--<div class="panel-heading">
                          <h4 class="panel-title">Product List </h4>
                          <button type="button" id="new_product" class="btn btn-success btn-sm pull-right"><span class="icon-plus"></span>Add New Product </button>
                </div>-->
                <div class="panel_controls">
                    <div class="row">
                        <form id="product_stock_log" name="product_stock_log" action="" method="post">
                            <div class="col-sm-3">
                                <input type="text" name="search_term" placeholder="Search terms" id="search_term" class="form-control">
                            </div>
                            <div class="col-sm-2">
                                <select name="product_id" id="product_id" class="form-control" data-supplier_id="@if(!empty($stock_list)){{$stock_list[0]->supplier_id}}@endif"></select>
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group date ebro_datepicker  pdL" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                    <input class="form-control" type="text" id="start_date" name="start_date" placeholder="From">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group date ebro_datepicker pdR" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                    <input class="form-control" type="text" id="end_date" name="end_date" placeholder="To">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button id="search" type="button" class="btn btn-primary btn-sm">Search</button>
                                <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Export" formtarget="_new"/>
                                <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Print" formtarget="_new"/>
                            </div>
                    </div>
                </div>
                <table id="table3" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Created On</th>
                            <th>Product</th>
                            <th>Supplier</th>
                            <th>Stock</th>
                            <th>Current stock</th>
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
<div class="modal fade" id="add_product_model" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg" style="width:800px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add New stock</h4>
            </div>
            <div class="modal-body">
                <div id="msg"></div>
                <div id="new_product_form">
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@section('scripts')

{{HTML::script('supports/supplier/stock_log_list.js')}}

@stop
