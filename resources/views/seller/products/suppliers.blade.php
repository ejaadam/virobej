@extends('supplier.common.layout')
@section('pagetitle')
Supplier Products
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
    .danger {
        color: red;
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
    .error{
        color:red;
    }
    .select_font {
        color: #666E77 ! important;
    }
</style>

<div class="tabbable tabs-left tabbable-bordered">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#product_details">Product Details</a></li>
        <li class=""><a data-toggle="tab" href="#tb3_b">Manage Stock</a></li>
        <li class=""><a data-toggle="tab" href="#tb3_c">Manage Image</a></li>
        <li class=""><a data-toggle="tab" href="#tb3_d">Manage Combinations</a></li>
    </ul>
    <div class="tab-content">
        <div id="product_details" class="tab-pane active">
            <div class="panel panel-default mT0" id="form" style="">
                <div class="panel-heading">
                    <h4 class="panel-title">Add Product</h4>
                </div>
                <div class="panel-body">
                    <form name="supplier_product_form" class="form-horizontal" id="supplier_product_form" action="{{URL::to('supplier/products/save')}}">
                        <input type="hidden" name="supplier_product_id" id="supplier_product_id">
                        <div class="form-group hidden">
                            <label class="control-label col-md-2" for="supplier_id">Supplier:<span class="error">*</span></label>
                            <div class="col-md-8">
                                <select name="supplier_product[supplier_id]" id="supplier_id" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2" for="product">Store:<span class="error">*</span></label>
                            <div class="col-md-8">
                                <select name="supplier_product[store_id]" id="store_ids" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="supplier_product[is_existing]" id="supplier_product_exist">
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2" for="brand_ids">Brand:<span class="error">*</span></label>
                            <div class="col-md-8">
                                <select name="brand_id" id="brand_id" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2" for="category_ids">Category:<span class="error">*</span></label>
                            <div class="col-md-8">
                                <select name="category_id" id="category_id" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2" for="product_id">Product:<span class="error">*</span></label>
                            <div class="col-md-8">
                                <select name="supplier_product[product_id]" id="product_id" class="form-control"></select>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2" for="currency_id">Currency:<span class="error">*</span></label>
                            <div class="col-md-8">
                                <select name="supplier_product_new[currency_id]" id="currency_id" class="form-control"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2" for="mrp_price">MRP Price:<span class="error">*</span></label>
                            <div class="col-md-8">
                                <input type="number" min="1" max="999999999"  name="supplier_product[mrp_price]" id="mrp_price" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2" for="price">Price:<span class="error">*</span></label>
                            <div class="col-md-8">
                                <input type="number" min="1" max="999999999" name="supplier_product[price]" id="price" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2" for="pre_order">Pre Order:<span class="error">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control" name="supplier_product[pre_order]" id="pre_order">
                                    <option value="1" >Enable</option>
                                    <option value="0" >Disable</option>
                                </select>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="control-label col-md-2" for="email"></label>
                            <div class="col-md-8">
                                <input type="submit" class="btn btn-primary" value="Save" />
                                <button id="cancel_btn" class="btn btn-danger">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="tb3_b" class="tab-pane">

            <div class="panel panel-default mT0">
                <div class="panel-heading">
                    <h4 class="panel-title">Add New Stock</h4>
                </div>
                <div class="panel-body">
                    <form name="add_stocks" id="add_stocks" action="{{URL::to('supplier/products/add_stock')}}">
                        <input type="hidden" name="product_id" id ="product_id"/>
                        <div class="row">
                            <label class="col-md-3">{{Lang::get('supplier_product_list.product_name_label');}}</label>
                            <div class="col-md-9">
                                <p class="form-control-static" id="pro_name" name="pro_name"></p>
                            </div>
                        </div>
                        <div class="row">

                            <label class="col-md-3">{{Lang::get('supplier_product_list.product_sku');}}</label>
                            <div class="col-md-9">
                                <p class="form-control-static" id="sku_code" name="sku_code"></p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-md-3">{{Lang::get('supplier_product_list.product_current_stock');}}</label>
                            <div class="col-md-9">
                                <p class="form-control-static" id="current_stock_value" name="current_stock_value"></p>
                            </div>
                        </div>
                        <div class="row" id="select_combination">
                            <label class="col-md-3">{{Lang::get('supplier_product_list.product_select_combination');}}</label>
                            <div class="col-md-9">
                                <select class="form-control" name="combination_id" id="combination_id">
                                </select>
                            </div>
                        </div>

                        <div class="row" id="new_stock_value">
                            <label class="col-md-3">{{Lang::get('supplier_product_list.product_stock_value');}}</label>
                            <div class="col-md-9">
                                <input name="stock_value" class="form-control" id="stock_value">
                            </div>
                        </div>

                        <div class="row">
                            <label class="col-md-3"></label>
                            <div class="col-md-9">
                                <input type="submit" class="btn btn-info" rel="" name="addstock"  id="addstocks">
                                <button name="add_new_category"  id="add_new_category" style="display:none">{{Lang::get('general.add_stock');}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="tb3_c" class="tab-pane">
            <div id="upload_image" style="">
                <div class="col-sm-12">
                    <div class="panel panel-default" id="list">
                        <div class="panel-heading">
                            <h4 class="panel-title">{{Lang::get('supplier_product_list.product_upload_image');}}</h4>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Lang::get('supplier_product_list.product_upload_pro_image');}}<span class="danger" style="color:red;">*</span></label>
                                <div class="col-sm-9">
                                    <div id="image-holder" class="fileupload-new img-thumbnail" style="width:108px; height:85px;">
                                        <img id="img_up"src="" width="100%" height="100%"/>
                                    </div>
                                </div>
                                <div class="pull-right col-sm-9 fileupload fileupload-new" data-provides="fileupload">
                                    <div class="input-append">
                                        <div class="uneditable-input">  <i class="glyphicon glyphicon-file fileupload-exists"></i> <span class="fileupload-preview"></span> </div>
                                        <span class="btn btn-default btn-file"> <span class="fileupload-new">Select file</span>
                                            <input type="file" name="logo_image" id="logo_image"  class="form-control"  data-url="<?php echo URL::asset('upload_img/1')?>" />
                                            <input type="hidden" name="image_name" id="image_name" value="" class="form-control" />
                                        </span>
                                        <div>File Format(*.gif, .jpg, .jpeg, *.png)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="tb3_d" class="tab-pane">
            <div id="combination_list" style="">
                <div class="pageheader">
                    <div class="row">
                        <div id="message"></div>
                        <div class="col-sm-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title col-sm-6">{{Lang::get('combination_list.page_head');}}</h4>
                                    <button type="button" id="add_combination_btn" class="btn btn-success btn-sm pull-right mR10"><span class="icon-plus"></span>{{Lang::get('general.category.add');}}</button>
                                </div>
                                <table id="combination_table" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="20%">{{Lang::get('combination_list.created_on');}}</th>
                                            <th width="40%">{{Lang::get('combination_list.product_cm_code');}}</th>
                                            <th width="20%">{{Lang::get('combination_list.stock_value');}}</th>
                                            <th width="5%">{{Lang::get('combination_list.impact_on_price');}}</th>
                                        </tr>
                                    </thead>
                                    <div class="panel_controls">
                                        <div class="row">
                                            <form id="category_list" name="category_list" action="{{URL::to('supplier/category/product_categories')}}" method="post">
                                                <div class="col-sm-3">
                                                    <input type="text" name="search_term" placeholder="{{Lang::get('general.search_ph');}}" id="search_term" class="form-control">
                                                </div>
                                                <div class="col-sm-3">
                                                    <button id="search" type="button" class="btn btn-primary btn-sm">{{Lang::get('general.search_btn');}}</button>
                                                    <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="{{Lang::get('general.export_btn');}}" formtarget="_new"/>
                                                    <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="{{Lang::get('general.print_btn');}}" formtarget="_new"/>
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
                </div>
            </div>
        </div>
    </div>
</div>








<div id="new_product_image" style="display:none">
    <div class="col-sm-12">
        <div class="panel panel-default" id="list">
            <div class="panel-heading">
                <button id="add_new_image"class="btn btn-success btn-sm pull-right"><span class="icon-plus"></span>Add New Image</button>
                <button id="save_changes" type="button" class="btn btn-success btn-sm pull-right mR10">Save Changes</button>
                <h4 class="panel-title"> ADD Products Image</h4>
            </div>

            <div class="panel_controls">
                <div id="succ_img"></div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <td>File</td>
                    <td>Position</td>
                    <td>Mark as Cover Photos</td>
                    </thead>
                    <tbody id="image_values">


                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="" id="image_resize" tabindex="-1" role="dialog" aria-hidden="true" style="display:none">
    <div class="panel-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Crop Image</h4>
    </div>
    <div class="panel-body">
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
                                    <span class="fa fa-arrows"></span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-primary" data-method="setDragMode" data-option="crop" title="Crop">
                                <span class="docs-tooltip" data-toggle="tooltip" title="cropper.setDragMode(&quot;crop&quot;)">
                                    <span class="fa fa-crop"></span>
                                </span>
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
                                <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(0.1)">
                                    <span class="fa fa-search-plus"></span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
                                <span class="docs-tooltip" data-toggle="tooltip" title="cropper.zoom(-0.1)">
                                    <span class="fa fa-search-minus"></span>
                                </span>
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" data-method="rotate" data-option="-5" title="Rotate Left">
                                <span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(-5)">
                                    <span class="fa fa-rotate-left"></span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-primary" data-method="rotate" data-option="5" title="Rotate Right">
                                <span class="docs-tooltip" data-toggle="tooltip" title="cropper.rotate(5)">
                                    <span class="fa fa-rotate-right"></span>
                                </span>
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" data-method="scaleX" data-option="-1" title="Flip Horizontal">
                                <span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleX(-1)">
                                    <span class="fa fa-arrows-h"></span>
                                </span>
                            </button>
                            <button type="button" class="btn btn-primary" data-method="scaleY" data-option="-1" title="Flip Vertical">
                                <span class="docs-tooltip" data-toggle="tooltip" title="cropper.scaleY(-1)">
                                    <span class="fa fa-arrows-v"></span>
                                </span>
                            </button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" data-method="reset" title="Reset">
                                <span class="docs-tooltip" data-toggle="tooltip" title="cropper.reset()">
                                    <span class="fa fa-refresh"></span>
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

<div id="add_property" style="display:none">
    <form class="form" id="product_properties_form" action="{{URL::to('supplier/products/properties/save')}}">
        <input type="hidden" name="product_id" id="product_id" value="">
        <div class="panel panel-default mrT0" id="" style="">
            <div class="panel-heading">
                <h4 id="panel-title" class="panel-title col-sm-6">Category Properties List</h4>
                <div class="text-right">
                    <button type="submit" class="btn btn-success btn-sm" title="Save"><span class="icon-save"></span></button>
                    <button type="button" class="btn btn-danger btn-sm manage-products">&times;</button>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-sm-12">
                    <div class="panel panel-default mrT0">
                        <div class="panel-heading">
                            <h4 class="panel-title col-sm-6">Properties List</h4>
                            <div class=" col-sm-6 text-right">Choosable</div>
                        </div>
                        <div class="panel-body">
                            <ul class="list-unstyled" id="properties" ></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="add_combination" style="display:none">
    <form class="form" id="add-edit-form" action="{{URL::to('supplier/products/combinations/save')}}" style="">
        <input type="hidden" name="product_cmb_id" id="product_cmb_id">
        <input type="hidden" name="product_cmb[product_id]" id="product_id">
        <div class="panel panel-default mrT0">
            <div class="panel-heading">
                <h4 id="panel-title" class="panel-title col-sm-6">Product Combinations</h4>
                <div class="text-right">
                    <button type="submit" class="btn btn-success btn-sm" title="Save"><span class="icon-save"></span></button>
                    <button type="button" class="btn btn-danger btn-sm back-btn">&times;</button>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-sm-12">
                    <div class="row form-group">
                        <label class="control-label col-sm-3" for="product_cmb">Combination Name:<span class="error">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="product_cmb[product_cmb]" id="product_cmb" readonly="readonly" class="form-control"/>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-3" for="sku">Combination SKU:<span class="error">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="product_cmb[sku]" id="sku" class="form-control"/>
                        </div>
                    </div>
                    <div class="row form-group" id="add-property-block">
                        <label class="control-label col-sm-3" for="product_cmb">Property:<span class="error">*</span></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select name="property_id" id="property_id" class="form-control"></select>
                                    </div>
                                    <div class="col-sm-6">
                                        <select name="value_id" id="value_id" class="form-control"></select>
                                    </div>
                                </div>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary" id="add-property-btn"><i class="fa fa-plus"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-3" for="product_cmb">Combination Properties:<span class="error">*</span></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <select name="product_cmb_properties[]" id="product_cmb_properties" multiple="" class="form-control"></select>
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary" id="remove-property-btn"><i class="fa fa-remove"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-3" for="product_cmb">Stock Value:<span class="error">*</span></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="number" name="product_cmb[stock_value]" id="product_stock_value" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <label class="control-label col-sm-3" for="product_cmb">Impact on Price:<span class="error">*</span></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="number" name="product_cmb[impact_on_price]" id="product_impact_price" class="form-control"/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

@stop

@section('scripts')
<script>
    var image_product = '{{Config::get("path.PRODUCT_THUMBNAIL_PATH")}}';
            var supplier_id = <?php echo $supplier_id;?>;
            var product_id = {{$product_id or ''}};
            var add_stock = {{$add_stock or ''}};
<?php if ($im)
{
    ?>
        var combinations = '';
                var add_stock = '';
                var image = true;
<?php }?>
<?php if ($combinations)
{
    ?>
        var image = '';
                var add_stock = '';
                var combinations = true;
<?php }?>
<?php if ($add_stock)
{
    ?>

        var image = '';
                var add_stock = true;
                var combinations = '';
                var product_name = '{{$product_name or ''}}';
                var sku = '{{$sku or ''}}';
                var stock_value = '{{$stock_value or ''}}';
                var product_code = '{{$product_code or ''}}';
<?php }?>
</script>
{{HTML::script('supports/supplier/supplier_product_list.js')}}
{{HTML::script('supports/supplier/cropperjs/cropper.js')}}
{{HTML::style('supports/supplier/cropperjs/cropper.css')}}
{{HTML::script('supports/supplier/products/product_properties.js')}}
{{HTML::script('supports/supplier/cropperjs/main.js')}}
@stop
