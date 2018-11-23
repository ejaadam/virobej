@extends('seller.common.layout')
@section('pagetitle')
{{$sub_order_code or ''}} Order's Details
@stop
@section('layoutContent')
<div class="pageheader">
    <div class="row">
        <div id="alert-msg" class="alert-msg"></div>
        <div class="col-sm-12">
            <div class="panel panel-default" id="details-panel" data-url="{{URL::to('api/v1/seller/order/details/'.$sub_order_code)}}">
                <div class="panel-heading">
                    <h4 class="panel-title col-sm-6"> Order's Details-{{$sub_order_code}}</h4>
                    <!--form  action="{{URL::to('/seller/order/details/'.$sub_order_code)}}">
                        <div class="pull-right">
                            <input name="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Print" formtarget="_new"/>
                        </div>
                    </form-->
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <div class="col-sm-6">
                            <!--h4>Order Details</h4-->
							<br>
                            <div class="form-group">
                                <label class="control-label col-md-3">Order Date</label>
                                <div class=" col-md-9">
                                    <p class="form-control-static" id="created_on"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Order Status</label>
                                <div class=" col-md-9">
                                    <p class="form-control-static" id="status"><span class=""></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            
								<!--h4>Shipping Details</h4-->
                                <address id="shipping_details">
                                </address>
                            <small class="text_muted info"></small>
                        </div>
                    </div>
                </div>
                <table id="sub_order_list_table" class="table table-striped">
                    <thead>
                        <tr >                            
                            <th  class = "text-center">Order Description</th>
                            <th  class = "text-center">Qty</th>
                            <th  class = "text-center">Status</th>
                            <th  class = "text-center">Net Pay</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="{{$sub_order_code}}" >
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class = "text-center"><h4>Total</h4></th>							
							<th class = "text-center"><h4 id="qty"></h4></th>
							<th></th>
							<th class = "text-center"><h4 id="net_pay"></h4></th>
							 <th></th>
						</tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="shipment_details_modal" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-lg" style="width:800px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Shipment Details</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" action="" id="shipment_details">
                        <div class="form-group">
                            <label class="control-label col-md-3">Courier Name</label>
                            <div class="col-md-9">
                                <select class="form-control" name="shipment[courier_id]" id="courier_id"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Bill Number</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="shipment[bill_number]" id="bill_number"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Remarks</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="shipment[remarks]" id="remarks"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Weight</label>
                            <div class="col-md-9">
                                <input type="number" class="form-control" name="shipment[weight]" id="weight" min="0"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-2">
                                <input type="submit" class="form-control" value="Dispatch"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
<script src="{{asset('resources/supports/supplier/orders_details.js')}}"></script>
@stop
