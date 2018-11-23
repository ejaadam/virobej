@extends('supplier.common.layout')
@section('pagetitle')
Product Items
@stop
@section('top-nav')
@include('supplier.common.top_navigation')
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
                    <h4 class="panel-title col-sm-6">Product List</h4>
                    <button type="button" id="new_product" class="btn btn-success btn-sm pull-right"><span class="icon-plus"></span>Add New Product </button>
                </div>
                <!--<div class="panel-heading">
                          <h4 class="panel-title">Product List </h4>
                          <button type="button" id="new_product" class="btn btn-success btn-sm pull-right"><span class="icon-plus"></span>Add New Product </button>
                </div>-->
                <table id="table3" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Created On</th>
                            <th>Product</th>
                            <th>Product Image</th>
                            <th>Amount</th>
                            <th>Stock</th>
                            <th>Quantity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <div class="panel_controls">
                        <div class="row">
                            <form id="product_list" name="product_list" action="{{URL::to('supplier/products/product_items')}}" method="post">
                                <div class="col-sm-2">
                                    <input type="text" name="search_term" placeholder="Search terms" id="search_term" class="form-control">
                                </div>
                                <div class="col-sm-2">
                                    <select name="category" id="category" class="form-control category">
                                        <option value="">Select Category</option>
                                        @if(!empty($categories))
                                        @foreach($categories as $category)
                                        <option value="{{$category->category_id}}">{{$category->category_name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <input type="hidden" name="product_image_path" id="product_image_path" value="{{Config::get('constants.SUPPLIER_PRODUCT_IMAGE_PATH')}}" />
                                    <select name="currency_id" id="currency_id" class="form-control">
                                        <option value="">--Select Currency--</option>
                                        @if(!empty($currencies))
                                        @foreach($currencies as $currency)
                                        <option value="{{$currency->id}}">{{$currency->currency}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group date ebro_datepicker col-sm-6 pdL" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                        <input class="form-control" type="text" id="start_date" name="start_date" placeholder="From">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                    </div>
                                    <div class="input-group date ebro_datepicker col-sm-6 pdR" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
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
                <h4 class="modal-title">Add New Product</h4>
            </div>
            <div class="modal-body">
                <div id="msg"></div>
                <div id="new_product_form">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="image_resize" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width:700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title1">Crop Image</h4>
            </div>
            <div class="modal-body">
                <div class="block">
                    <div class=" form-group row">
                        <div class="col-md-12">
                            <div class="img-container">
                                <img src="{{URL::asset('assets/upload/project_images/default.png')}}" class="img img-thumbnail col-lg-12" alt="Picture">
                            </div>
                        </div>
                        <div class="col-lg-12 text-center" style="margin-top: 10px;" id="actions">
                            <div class="col-md-12 docs-buttons">
                                <!-- <h3 class="page-header">Toolbar:</h3> -->
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="setDragMode" data-option="move" title="Move">
                                        <span class="docs-tooltip" data-toggle="tooltip" title="cropper.setDragMode(&quot;move&quot;)">
                                            <span class="icon-move"></span>
                                        </span>
                                    </button>
                                    <button type="button" class="btn btn-primary" data-method="setDragMode" data-option="crop" title="Crop">
                                        <span class="docs-tooltip" data-toggle="tooltip" title="cropper.setDragMode(&quot;crop&quot;)">
                                            <span class="icon-crop"></span>
                                        </span>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
                                        <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(0.1)">
                                            <span class="icon-zoom-in"></span>
                                        </span>
                                    </button>
                                    <button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
                                        <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(-0.1)">
                                            <span class="icon-zoom-out"></span>
                                        </span>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="-5" title="Rotate Left">
                                        <span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(-5)">
                                            <span class="icon-undo"></span>
                                        </span>
                                    </button>
                                    <button type="button" class="btn btn-primary" data-method="rotate" data-option="5" title="Rotate Right">
                                        <span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(5)">
                                            <span class="icon-repeat"></span>
                                        </span>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="scaleX" data-option="-1" title="Flip Horizontal">
                                        <span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleX(-1)">
                                            <span class="icon-resize-horizontal"></span>
                                        </span>
                                    </button>
                                    <button type="button" class="btn btn-primary" data-method="scaleY" data-option="-1" title="Flip Vertical">
                                        <span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleY(-1)">
                                            <span class="icon-resize-vertical"></span>
                                        </span>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" data-method="reset" title="Reset">
                                        <span class="docs-tooltip" data-toggle="tooltip" title="cropper.reset()">
                                            <span class="icon-refresh"></span>
                                        </span>
                                    </button>
                                </div>
                                <div class="btn-group btn-group-crop">
                                    <button type="button" class="btn btn-primary" data-method="getCroppedCanvas" data-option="{ &quot;width&quot;: 200, &quot;height&quot;: 200 }">
                                        <span class="docs-tooltip" data-toggle="tooltip" title="Save">
                                            Save
                                        </span>
                                    </button>
                                </div>
                            </div><!-- /.docs-buttons -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
{{HTML::script('supports/jquery.form.js')}}
{{HTML::script('supports/supplier/product_list.js')}}
{{HTML::script('supports/supplier/add_product.js')}}
{{HTML::script('supports/member/cropperjs/cropper.js')}}
{{HTML::script('supports/member/cropperjs/main1.js')}}
@stop
