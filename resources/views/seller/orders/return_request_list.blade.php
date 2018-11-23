@extends('supplier.common.layout')
@section('pagetitle')
Return Orders
@stop
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')
<div id="main_content">
    <!-- main content -->
    <div class="row" id="list"> <span class="message"></span>
        <div class="col-sm-12" id="orderbox">
            <div class="successmsg"></div>
            <div class="panel panel-default" id="orderreport">
                <div class="panel-heading">
                    <h4 class="panel-title"></h4>Return orders </h4>
                </div>
                <table id="dt_basic" class="table table-striped">
                    <thead>
                        <tr>
                            <th nowrap="nowrap">Created Date</th>
                            <th>Order Code</th>
                            <th>Product Name</th>
                            <th>Customer Name</th>
                            <th>Reason</th>
                            <th>Return Type</th>
                            <th>Action</th>
                            <th></th>
                        </tr>
                    </thead>
                    <div class="panel_controls">
                        <div class="row">
                            <form id="order_list" name="return_list" >
                                <div class="col-sm-2">
                                    <div class="input-group date ebro_datepicker" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                        <input class="form-control" type="text"name=" start_date" id="start_date" placeholder="From">
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
                                    <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Export" formtarget="_new"/>
                                    <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Print" formtarget="_new"/>
                                    <button id="clear_filter" class="btn btn-primary btn-sm">Clear Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row" id="details" style="display:none">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title col-sm-6">Details</h4>
                <a href="#" id="back-to-list" class="btn btn-danger btn-sm pull-right"><i class="fa fa-times"></i></a>
            </div>
            <div class="panel_controls">
                <div class="row">

                    <div class="col-sm-6">
                        <h4>Order Details</h4>

                        <div class="form-group">
                            <label class="control-label col-md-3">Order Date</label>
                            <div class=" col-md-9">
                                <p class="form-control-static">{{$details->order_created_on or ''}}</p>
                            </div>
                        </div>


                    </div>


                    <div class="col-sm-6">
                        <small class="text_muted info"><h4>Shipping Details</h4>
                            <address>
                                <h3> {{$details->username or ''}}</h3>
                                {{$details->address or ''}},<br>
                                {{$details->landmark or ''}},<br>
                                {{$details->state_name or ''}},<br>
                                {{$details->city or ''}},<br>
                                {{$details->country_name or ''}},<br>
                                <br>Mobile No.: {{$details->mobile_no or ''}}
                            </address>
                        </small>
                    </div>

                </div>
            </div>

            <table id="sub_order_list_table" class="table table-striped">
                <thead>
                    <tr>
                        <th>Order Description</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Net Pay</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="OD7S6">
                    <tr>
                        <td class="stat_wide">
                            <div class="media">
                                <img alt="" width="100px" src="{{$details->imgs[0]->img_path}}" class="media-object img-thumbnail pull-left">
                                <div class="media-body">
                                    <h3><a href="#">{{$details->product_name or ''}}</a></h3>
                                    <p><span class="text-muted">Brand:</span>Nokia</p>
                                    <p><span class="text-muted">Price:</span><?php
                                        if (!empty($details->amount))
                                        {
                                            echo number_format($details->amount, 2, '.', ',').' '.$details->currency_symbol;
                                        }
                                        ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">1</td>
                        <td class="text-center"><span class="{{$details->order_class or ''}}}">{{$details->order_status or ''}}</span></td>
                        <td class="text-right"><?php
                            if (!empty($details->net_pay))
                            {
                                echo number_format($details->net_pay, 2, '.', ',').' '.$details->currency_symbol;
                            }
                            ?></td>

                    </tr>
                </tbody><tbody id="OD7S6">

                </tbody><tbody id="OD7S6">

                </tbody><tbody id="OD7S6">

                </tbody>

                <tbody><tr>
                        <th></th>
                        <th></th>
                        <th class="text-right"><h4>Total</h4></th>
                <th class="text-right"><h4></h4></th>
                <th class="text-right"><h4></h4></th>
                </tr>
                </tbody></table>
        </div>
    </div>

</div>

<div class="modal fade" id="shipment_details_modal" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg" style="width:800px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Details</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="{{URL::to('')}}" id="account_det">

                </form>
            </div>
        </div>
    </div>
</div>
@include('admin.common.assets')
@stop
@section('scripts')
{{ HTML::script('supports/supplier/return_list.js') }}
@stop
