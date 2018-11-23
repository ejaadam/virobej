@extends('seller.common.layout')
@section('pagetitle')
Supplier Products
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
    .error {
        color:red;
    }
    .select_font {
        color: #666E77 ! important;
    }
    .chosen-container-multi .chosen-choices li.search-field input[type="text"] {  width:300px ! important;  }
</style>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title title"></h4>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="tabbable tabs-left tabbable-bordered" id="configure" data-supplier_product_code="{{$supplier_product_code or ''}}">
                <ul class="nav nav-tabs" id="nav_id">
                    <li class="active"><a data-toggle="tab" href="#info">Information</a></li>
                    <li class=""><a data-toggle="tab" href="#seo"> SEO</a></li>
                    <li class=""><a data-toggle="tab" id="assoc_tab" href="#assoc">Association</a></li>
                    <li><a data-toggle="tab" href="#manage-properties" class="manage-properties">Manage Properties</a></li>
                    <li id="manage_img"><a data-toggle="tab" id="manage_image" href="#manage_image_tab" >Manage Image</a></li>
                    <li class=""><a data-toggle="tab" href="#pric" id="price_tab">Price</a></li>
                    <!--li id="manage_combi"><a data-toggle="tab" id="manage_combo" href="#manage_combination_tab" >Other Combinations</a></li-->
                    <li class=""><a data-toggle="tab" href="#shipping_det"> Shipment</a></li>
                    <li id="manage_sto"><a data-toggle="tab" href="#manage_stock_tab" >Manage Stock</a></li>
                    <li id="manage_pay"><a data-toggle="tab" id="manage_payment" href="#manage_payment_tab" >Pay Modes</a></li>
                    <li id="manage_cnty"><a data-toggle="tab" id="manage_country" href="#manage_country_tab" >Country</a></li>
                </ul>
                <div class="tab-content" style="min-height:750px;">
                    <div id="manage_country_tab" class="tab-pane">
                        <div class="panel panel-default mT0">
                            <div class="panel-heading">
                                <h4 class="panel-title">Country</h4>
                            </div>
                            <form class="form" id="country" action="" novalidate="novalidate">
                                <div class="panel-body">
                                    <input type="hidden" class="ignore-reset" name="product[product_id]" id="product_id" >
                                    <input type="hidden" class="ignore-reset" name="prod[country_id]" id="cnty_id" >
                                    <div class="row">
                                        <label class="control-label col-md-4" for="country">Select Country:</label>
                                        <div class="col-md-8">
                                            <select class="form-control" name="product[country_id]" id="country_id"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button type="button" class="btn btn-success pull-right" id="country_save_btn">Add</button>
                                    <input type="button" class="btn btn-danger back-btn" value="Cancel">
                                </div>
                            </form>
                        </div>
                        <div class="panel panel-default mT10">
                            <div class="panel-heading">
                                <h4 class="panel-title">Countries List</h4>
                            </div>
                            <div class="panel-body">
                                <ul class="list-unstyled col-sm-4" id="conutry-list">
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="info" class="tab-pane active">
                        <div class="panel panel-default mT0">
                            <div class="panel-heading">
                                <h4 class="panel-title">Information</h4>
                            </div>
                            <form class="form" id="product_info_form" action="" novalidate="novalidate">
                                <div class="panel-body">
                                    <div class="row">
                                        <input type="hidden" class="ignore-reset" name="supplier_product_id" id="supplier_product_id" />
                                        <input type="hidden" class="ignore-reset" name="product_id" id="product_id" />
                                        <input type="hidden" class="ignore-reset" name="details[eanbarcode]" id="eanbarcode" />
                                        <input type="hidden" class="ignore-reset" name="details[upcbarcode]" id="upcbarcode" />
                                        <label class="control-label col-md-2" for="product">Product Name:<span class="error">*</span></label>
                                        <div class="col-md-3">
                                            <input type="text" name="product[product_name]" id="product_name"  class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="sku">SKU:<span class="error">*</span></label>
                                        <div class="col-md-3">
                                            <input type="text" name="details[sku]" id="sku"  class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="description">Description:<span class="error">*</span></label>
                                        <div class="col-md-10">
                                            <textarea class="ckeditor" cols="80" id="description" name="details[description]" rows="10"></textarea>
                                        </div>
                                    </div>
                                    <div class="row" id="redirect_status" style="display:none">
                                        <label class="control-label col-md-2" for="pre_order">Redirect when disabled:</label>
                                        <div class="col-md-3">
                                            <select class="form-control" name="product[redirect_id]" id="redirect_stat">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="pre_order">Visiblity:<span class="error">*</span></label>
                                        <div class="col-md-3">
                                            <select class="form-control" name="details[visiblity_id]" id="product_visibility" >
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="pre_order">Tags:<span class="error">*</span></label>
                                        <div class="col-md-10">
                                            <input class="tags" id="tags" name="tags" type="text" >
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="weight">{{trans('create_zone_shipment.weight')}}</label>
                                        <div class="col-md-4 input-group">
                                            <input type="number" min="0" max="999999999" step="0.01" name="details[weight]" id="weight"  class="form-control"/>
                                            <span class="input-group-addon">KG</span>
                                        </div>
                                        <label class="control-label col-md-2" for="width">{{trans('create_zone_shipment.width')}}</label>
                                        <div class="col-md-4 input-group">
                                            <input type="number" min="0" max="999999999" step="0.01" name="details[width]" id="width"  class="form-control"/>
                                            <span class="input-group-addon">CM</span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="control-label col-md-2" for="height">{{trans('create_zone_shipment.height')}}</label>
                                        <div class="col-md-4 input-group">
                                            <input type="number" min="0" max="999999999" step="0.01" name="details[height]" id="height"  class="form-control"/>
                                            <span class="input-group-addon">CM</span>
                                        </div>
                                        <label class="control-label col-md-2" for="length">{{trans('create_zone_shipment.length')}}</label>
                                        <div class="col-md-4 input-group">
                                            <input type="number" min="0" max="999999999" step="0.01" name="details[length]" id="length"  class="form-control"/>
                                            <span class="input-group-addon">CM</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="checkbox col-md-offset-1">
                                            <label class="control-label col-md-5 " for="featured">
                                                <input type="checkbox" class="simple" name="featured" id="featured" value="1">Featured
                                            </label>
                                        </div>
                                        <div class="checkbox col-md-offset-1">
                                            <label class="control-label col-md-5 " for="promote_to_homepage">
                                                <input type="checkbox" class="simple" name="promote_to_homepage" id="promote_to_homepage" value="1">Promote to Home Page
                                            </label>
                                        </div>
                                    </div>
                                    <!--div class="row">
                                        <label class="control-label col-md-2" for="tax_class_id">{{trans('create_zone_shipment.tax_class')}}<span class="error">*</span></label>
                                        <div class="col-md-4">
                                            <select name="product[tax_class_id]" id="tax_class_id" class="form-control">
                                            </select>
                                        </div>
                                    </div-->
                                </div>
                                <div class="panel-footer">
                                    <button type="button" class="btn btn-success pull-right product_save_btn" >Save</button>
                                    <input type="button" class="btn btn-danger back-btn" value="Cancel">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="pric" class="tab-pane">
                        <div class="panel panel-default mT0" id="price-list-panel">
                            <div class="panel-heading">
                                <a href="#" id="add-new-price-btn" class="btn btn-success btn-sm pull-right "><span class="icon-plus"></span>Add Currency Price</a>
                                <h4 class="panel-title">Price</h4>
                            </div>
                            <div class="panel-body">
                                <table class="table"  id="product-price">
                                    <thead>
                                        <tr>
                                            <th>Currency</th>
                                            <th>MRP Price</th>
                                            <th>Selling Price</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="panel panel-default" id="price-form-panel" style="display:none">
                            <form class="form" id="price_form" action="" novalidate="novalidate">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Price</h4>
                                </div>
                                <div class="panel-body">
                                    <input type="hidden" name="spp_id"  id="spp_id"/>
                                    <input type="hidden" name="spp[product_id]"  id="product_id"/>
                                    <input type="hidden" name="supplier_product_code"  id="supplier_product_code" value="{{$supplier_product_code or ''}}"/>
                                    <!--input type="text" name="spp[product_cmb_id]" id="product_cmb_id" /-->
                                    <div class="row hidden">
                                        <label class="control-label col-md-2" for="currency_id">Currency:<span class="error">*</span></label>
                                        <div class="col-md-10">
                                            <select name="spp[currency_id]" id="currency_id" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="checkbox col-md-offset-4">
                                        <label class="control-label col-md-4 text-right" for="is_shipping_beared"><input type="checkbox" class="simple" name="supplier_product[is_shipping_beared]" id="is_shipping_beared" value="1">I'll bear the shipping charge<span class="error">*</span></label>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-4 text-right" for="mrp_price">MRP Price:<span class="error">*</span></label>
                                        <div class="col-md-4">
                                            <input type="number" min="1" max="999999999"  name="spp[mrp_price]" id="mrp_price" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-4 text-right" for="price">Selling Price:<span class="error">*</span></label>
                                        <div class="col-md-4">
                                            <input type="number" min="1" max="999999999" name="spp[price]" id="price" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-4" for="impact_on_price">Impact on Price:<span class="error">*</span></label>
                                        <div class="col-md-10">
                                            <input type="number" min="1" max="999999999" name="spcp[impact_on_price]" id="impact_on_price" class="form-control"/>
                                        </div>
                                    </div>
                                    <div id="deductions"></div>
                                </div>
                                <div class="panel-footer">
                                    <!--button type="submit" class="btn btn-success pull-right" >Save</button-->
                                    <input type="button" id="save_price" class="btn btn-success pull-right" value="Save">
                                    <input type="button" id="back-to-price-list" class="btn btn-danger" value="Cancel">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="seo" class="tab-pane">
                        <div class="panel panel-default mT0">
                            <div class="panel-heading">
                                <h4 class="panel-title">SEO</h4>
                            </div>
                            <form class="form" id="seo" action="" novalidate="novalidate">
                                <div class="panel-body">
                                    <input type="hidden" class="ignore-reset" name="meta_info[post_type_id]" id="post_type_id" value="1">
                                    <input type="hidden" class="ignore-reset" name="meta_info[relative_post_id]" id="relative_post_id" >
                                    <div class="row hidden">
                                        <label class="control-label col-md-4" for="url_slug">URL Slug:</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="url_slug">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-4" for="description">Meta Description:</label>
                                        <div class="col-md-8">
                                            <textarea class="form-control" name="meta_info[description]" id="description"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-4" for="meta_keys">Meta Keys:</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="meta_info[meta_keys]"  id="meta_keys"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button type="submit" class="btn btn-success pull-right product_save_btn" >Save</button>
                                    <input type="button" class="btn btn-danger back-btn" value="Cancel">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="assoc" class="tab-pane">
                        <div class="panel panel-default mT0">
                            <div class="panel-heading">
                                <h4 class="panel-title">Association</h4>
                            </div>
                            <form class="form" id="assoc_form" action="" novalidate="novalidate">
                                <div class="panel-body">
                                    <div class="row">
                                        <input type="hidden" class="ignore-reset" name="product_id" id="product_id" />
                                        <input type="hidden" class="ignore-reset" name="supplier_product_id" id="supplier_product_id" />
                                        <div id="treeview-container">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="category_id">Category:<span class="error">*</span></label>
                                        <div class="col-md-10">
                                            <select name="product[category_id]" id="category_id" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="brand_id">Brand:<span class="error">*</span></label>
                                        <div class="col-md-10">
                                            <select name="product[brand_id]" id="brand_id" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="is_exclusive">Exclusive :</label>
                                        <div class="col-md-2">
                                            <select class="form-control" name="details[is_exclusive]" id="exclusive">
                                                <option value="1">Yes</option>
                                                <option value="0" >No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <!--button type="submit" class="btn btn-success pull-right" >Save</button-->
                                    <button type="button" class="btn btn-success pull-right product_save_btn" >Save</button>
                                    <input type="button" class="btn btn-danger back-btn" value="Cancel">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="manage-properties" class="tab-pane">
                        <div id="add_property">
                            <form class="form" id="product_properties_form">
                                <input type="hidden" name="product_id" id="product_id" >
                                <div class="panel panel-default mrT0">
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
                    </div>
                    <div id="manage_stock_tab" class="tab-pane">
                        <div class="panel panel-default mT0">
                            <div class="panel-heading">
                                <h4 class="panel-title">Add New Stock</h4>
                            </div>
                            <form name="add_stocks" id="add_stocks">
                                <input type="hidden" class="ignore-reset" name="supplier_product_id" id ="supplier_product_id"/>
                                <input type="hidden" class="ignore-reset" name="product_id" id ="product_id"/>
                                <div class="panel-body">
                                    <div class="row">
                                        <label class="col-md-3">{{trans('supplier_product_list.product_name_label')}}</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static title" id="pro_name"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-3">{{trans('supplier_product_list.product_sku')}}</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static" id="sku_code"></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-3">{{trans('supplier_product_list.product_current_stock')}}</label>
                                        <div class="col-md-9">
                                            <p class="form-control-static" id="current_stock_value"></p>
                                        </div>
                                    </div>
                                    <div class="row" id="new_stock_value">
                                        <label class="col-md-3">{{trans('supplier_product_list.product_stock_value')}}</label>
                                        <div class="col-md-9 input-group">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <span class="input-group-btn">
                                                        <select class="form-control" name="transaction_type" id="transaction_type">
                                                            <option value="1" >Increment</option>
                                                            <option value="0" >Decrement</option>
                                                        </select>
                                                    </span>
                                                </div>
                                                <div class="col-md-4">
                                                    <input name="stock_value" class="form-control" id="stock_value">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <!--button type="submit" class="btn btn-success pull-right ">Save</button-->
                                    <input type="button" id="add_stock" class="btn btn-success pull-right" value="Save">
                                    <input type="button" class="btn btn-danger back-btn" value="Cancel">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id= "manage_image_tab" class="tab-pane">
                        <div id="add_image" class="mB10">
                            <div class="panel panel-default mB10">
                                <div class="panel-heading">
                                    <h4 class="panel-title"> ADD Products Image</h4>
                                </div>
                                <div class="panel-body" >
                                    <div class="row">
                                        <label class="col-sm-3 control-label">Upload Files</label>
                                        <div class="col-sm-9">
                                            <div id="extraupload" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="height:300px; overflow-y:auto">
                                        <ul id="gallery_grid" class="galMix grid">
                                            <li>
                                                <div class="gal_sort_list clearfix">
                                                    <div class="img_wrapper"></div>
                                                    <div class="meta name">Name</div>
                                                    <div class="meta user">User</div>
                                                    <div class="meta date">Date</div>
                                                    <div class="meta category">Category</div>
                                                </div>
                                            </li>
                                            <li class="gal_no_result">Sorry, there are no images to show.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-footer text-right">
                                    <button type="button" id="delete_selected_image" class="btn btn-success">Delete Images</button>
                                    <button type="button" id="add_selected_image" class="btn btn-success">Add to Image List</button>
                                </div>
                            </div>
                        </div>
                        <div id="image_list" class="">
                            <div class="panel panel-default" id="list">
                                <div class="panel-heading">
                                    <button id="save_changes" type="button" class="btn btn-success btn-sm pull-right mR10">Save Changes</button>
                                    <h4 class="panel-title col-sm-4"> Images List Image</h4>
                                    <label class="control-label col-sm-2">Choose Combination<span class="error">*</span></label>
                                    <div class="col-sm-4">
                                        <select id="combination_select" class="form-control">
                                        </select>
                                    </div>
                                </div>
                                <div class="panel-body no-padding">
                                    <div id="succ_img"></div>
                                    <table class="table table-striped dataTable no-footer ">
                                        <thead>
                                            <tr>
                                                <th>File</th>
                                                <th>Position</th>
                                                <th class="hidden">Mark as Cover Photos</th>
                                            </tr>
                                        </thead>
                                        <tbody id="image_values">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="manage_combination_tab" class="tab-pane">
                        <form class="form" id="product-combination-form" style="display: none;">
                            <input type="hidden" class="ignore-reset" name="product_cmb_id" id="product_cmb_id" value="NULL">
                            <input type="hidden" class="ignore-reset" name="product_cmb[product_id]" id="product_id">
                            <input type="hidden" class="ignore-reset" name="cmb_ppt_id1" id="cmb_ppt_id">
                            <input type="hidden" class="ignore-reset" name="supplier_product_id" id="supplier_product_id">
                            <div class="panel panel-default mB10">
                                <div class="panel-heading">
                                    <h4 id="panel-title"  class="panel-title col-sm-6">{{trans('product_combinations.product_combinations_tit')}}</h4>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-danger btn-sm back-btn">&times;</button>
                                    </div>
                                </div>
                                <div id="error_msg"></div>
                                <div class="panel-body combination_tit">
                                    <div class="col-sm-12">
                                        <div class="row form-group">
                                            <!-- <label class="control-label col-sm-3" for="product_cmb">Combination Name:<span class="error">*</span></label>!-->
                                            <div class="col-sm-9">
                                                <input type="hidden" class="ignore-reset" name="product_cmb[product_cmb]" id="product_cmb" />
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <label class="control-label col-sm-3" for="sku">{{trans('product_combinations.sku')}}<span class="error">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="product_cmb[sku]" id="sku" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <label class="control-label col-sm-3" for="sku"></label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="col-sm-1">
                                                        <input type="checkbox" name="product[same_details]" id="same_details" class="form-control"/>
                                                    </div>
                                                    <div class="col-sm-11">
                                                        Same as Product Details
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-group" id="add-property-block">
                                            <label class="control-label col-sm-3" for="product_cmb">{{trans('product_combinations.property')}}<span class="error">*</span></label>
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
                                                        <button type="button" class="btn btn-success" id="add-property-btn"><i class="fa fa-plus"></i>+</button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <label class="control-label col-sm-3" for="product_cmb">{{trans('product_combinations.combination_prop')}}<span class="error">*</span></label>
                                            <div class="col-sm-9">
                                                <span class="input-group-btn">
                                                    <select id="product_cmb_properties" name="product_cmb_properties[]" multiple="" class="form-control"></select>
                                                    <button type="button" class="btn btn-xs btn-danger pull-right"  id="remove-property-btn"><i class="glyphicon glyphicon-minus"></i> {{trans('product_combinations.remove_combination')}}</button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <label class="control-label col-md-3" for="is_exclusive">Exclusive :</label>
                                            <div class="col-md-2">
                                                <select class="form-control" name="product_cmb[is_exclusive]" id="exclusive">
                                                    <option value="1" >Yes</option>
                                                    <option value="0" selected="selected" >No</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1">&nbsp;</div>
                                            <label class="control-label col-md-3" for="brand_id">Impact On Price:<span class="error">*</span></label>
                                            <div class="col-md-3">
                                                <input type="number" min="0" name="product_cmb_price[impact_on_price]" id="product_impact_price"  class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="row" id="prod_info">
                                            <div class="panel panel-default mT0">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">Information</h4>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <input type="hidden" class="ignore-reset" name="supplier_product_id" id="supplier_product_id" />
                                                        <input type="hidden" class="ignore-reset" name="product_id" id="product_id" />
                                                        <input type="hidden" class="ignore-reset" name="brand_id" id="brand_id" />
                                                        <input type="hidden" class="ignore-reset" name="category_id" id="category_id" />
                                                        <label class="control-label col-md-2" for="product">Product Name:<span class="error">*</span></label>
                                                        <div class="col-md-3">
                                                            <input type="text" name="prod[product_name]" id="product_name"  class="form-control"/>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="control-label col-md-2" for="sku">SKU:<span class="error">*</span></label>
                                                        <div class="col-md-3">
                                                            <input type="text" name="det[sku]" id="sku"  class="form-control"/>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="control-label col-md-2" for="description">Description:<span class="error">*</span></label>
                                                        <div class="col-md-10">
                                                            <textarea class="ckeditor" cols="80" id="descrip" name="det[descrip]" rows="10"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="row" id="redirect_status" style="display:none">
                                                        <label class="control-label col-md-2" for="pre_order">Redirect when disabled:</label>
                                                        <div class="col-md-3">
                                                            <select class="form-control" name="prod[redirect_id]" id="redir_stat">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="control-label col-md-2" for="pre_order">Visiblity:<span class="error">*</span></label>
                                                        <div class="col-md-3">
                                                            <select class="form-control" name="det[visiblity_id]" id="prod_visibility" >
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <!--div class="row">
                                                            <label class="control-label col-md-2" for="pre_order">Tags:<span class="error">*</span></label>
                                                            <div class="col-md-10">
                                                                    <input class="tags" id="tagss" name="tagss" type="text" >
                                                            </div>
                                                    </div-->
                                                    <div class="row">
                                                        <label class="control-label col-md-2" for="weight">{{trans('create_zone_shipment.weight')}}</label>
                                                        <div class="col-md-4 input-group">
                                                            <input type="number" min="0" max="999999999" step="0.01" name="det[wgt]" id="wgt"  class="form-control"/>
                                                            <span class="input-group-addon">KG</span>
                                                        </div>
                                                        <label class="control-label col-md-2" for="width">{{trans('create_zone_shipment.width')}}</label>
                                                        <div class="col-md-4 input-group">
                                                            <input type="number" min="0" max="999999999" step="0.01" name="det[wdh]" id="wdh"  class="form-control"/>
                                                            <span class="input-group-addon">CM</span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="control-label col-md-2" for="height">{{trans('create_zone_shipment.height')}}</label>
                                                        <div class="col-md-4 input-group">
                                                            <input type="number" min="0" max="999999999" step="0.01" name="det[hgt]" id="hgt"  class="form-control"/>
                                                            <span class="input-group-addon">CM</span>
                                                        </div>
                                                        <label class="control-label col-md-2" for="length">{{trans('create_zone_shipment.length')}}</label>
                                                        <div class="col-md-4 input-group">
                                                            <input type="number" min="0" max="999999999" step="0.01" name="det[len]" id="len"  class="form-control"/>
                                                            <span class="input-group-addon">CM</span>
                                                        </div>
                                                    </div>

                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button type="button" id="product_comb" class="btn  btn-success pull-right" title="Save">{{trans('general.save_btn')}}</button>
                                    <button type="button" class="btn btn-danger  back-btn"> {{trans('general.cancel_btn')}}</button>
                                </div>
                            </div>
                        </form>
                        <form class="form" id="supplier-product-combination-form" style="display:none">
                            <input type="hidden" class="ignore-reset" name="supplier_product_id" id="supplier_product_id" />
                            <input type="hidden" class="ignore-reset" name="supplier_product[product_id]" id="product_id" />
                            <div class="panel panel-default mB10">
                                <div class="panel-heading">
                                    <h4 id="panel-title"  class="panel-title col-sm-6">{{trans('product_combinations.product_combinations_tit')}}</h4>
                                    <div class="text-right">
                                  <!--      <button type="submit" class="btn btn-success btn-sm" title="Save"><span class="icon-save"></span></button>-->
                                        <button type="button" class="btn btn-danger btn-sm back-btn">&times;</button>
                                    </div>
                                </div>
                                <div id="error_msg"></div>
                                <div class="panel-body combination_tit">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <label class="control-label col-sm-2" for="product_cmb_id">{{trans('product_combinations.combination')}}<span class="error">*</span></label>
                                            <div class="col-sm-9">
                                                <select name="supplier_product[product_cmb_id]" id="product_cmb_id" class="form-control"></select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="control-label col-sm-2" for="product_cmb">{{trans('product_combinations.product_impact_price')}}<span class="error">*</span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon" id="currency" style="width:10%"></span>
                                                    <input type="number" name="spcp[impact_on_price]" id="product_impact_price" class="form-control"/>
                                                    <span class="input-group-addon" id="selling-price" style="width:30%"></span>
                                                    <span class="input-group-addon" id="price" style="width:30%"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="control-label col-sm-2" for="pre_order">{{trans('product_combinations.condition')}}</label>
                                            <div class="col-md-3">
                                                <select class="form-control" name="supplier_product[condition_id]" id="condition">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="control-label col-sm-2" for="is_replaceable">{{trans('product_combinations.replacement')}}</label>
                                            <div class="col-sm-2">
                                                <select class="form-control" name="supplier_product[is_replaceable]" id="is_replaceable">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="control-label col-sm-2" for="pre_order">{{trans('product_combinations.pre_order')}}</label>
                                            <div class="col-sm-2">
                                                <select class="form-control" name="supplier_product[pre_order]" id="status">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button type="submit" class="btn btn-success pull-right" title="Save">{{trans('general.save_btn')}}</button>
                                    <button type="button" class="btn btn-danger  back-btn"> {{trans('general.cancel_btn')}}</button>
                                </div>
                            </div>
                        </form>
                        <div id="combination_list" >
                            <div class="pageheader">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="panel panel-default mB10">
                                            <div class="panel-heading">
                                                <h4 class="panel-title col-sm-6">{{trans('combination_list.page_head')}}</h4>
                                                <button type="button" id="add-product-combination-btn" class="btn btn-success btn-sm pull-right mR10"><span class="icon-plus"></span>{{trans('combination_list.add_new_combination')}}</button>
                                                <button type="button" id="add-supplier-product-combination-btn" class="btn btn-success btn-sm pull-right mR10"><span class="icon-plus"></span>{{trans('combination_list.add_combination')}}</button>
                                            </div>
                                            <table id="combination_table" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="20%">{{trans('combination_list.created_on')}}</th>
                                                        <th width="40%">{{trans('combination_list.product_cm_code')}}</th>
                                                        <th width="20%">{{trans('combination_list.stock_value')}}</th>
                                                        <th width="5%">{{trans('combination_list.impact_on_price')}}</th>
                                                        <th width="5%">{{trans('general.status')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <div class="row">
                                                    <form id="category_list" name="category_list" action="{{URL::to('supplier/category/product_categories')}}" method="post">
                                                    </form>
                                                </div>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="manage_payment_tab" class="tab-pane">
                        <div id="add_payment" style="display:none">
                            <form class="form" id="payment_add-form" >
                                <input type="hidden" class="ignore-reset" name="product_cmb_id" id="product_cmb_id">
                                <input type="hidden" class="ignore-reset" name="product_id" id="product_id" >
                                <input type="hidden" class="ignore-reset" name="supplier_product_id" id="supplier_product_id" >
                                <div class="panel panel-default mB10">
                                    <div class="panel-heading">
                                        <h4 id="panel-title"  class="panel-title col-sm-6">Payment Add</h4>
                                        <div class="text-right">
                                      <!--      <button type="submit" class="btn btn-success btn-sm" title="Save"><span class="icon-save"></span></button>-->
                                            <button type="button" class="btn btn-danger btn-sm back-btn">&times;</button>
                                        </div>
                                    </div>
                                    <div id="error_msg"></div>
                                    <div class="panel-body ">
                                        <div class="row" id="payment_modes">
                                            <label class="col-md-3">{{trans('payment_list.select_payment_mode')}}</label>
                                            <div class="col-md-9">
                                                <select class="form-control" name="payment_list_id" id="payment_id">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <label class="control-label col-sm-3" for="product_cmb">{{trans('payment_list.payment_gateways')}}<span class="error">*</span></label>
                                            <div class="col-sm-9">
                                                <span class="input-group-btn">
                                                    <select name="payment_gateway_select[]" id="payment_gateway_select" multiple="" class="form-control"></select>
                                                    <button type="button" class="btn btn-xs btn-danger mT10"   id="remove-gateway-btn"><i class="glyphicon glyphicon-minus"></i> {{trans('payment_list.remove_payment_gateway')}}</button> </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <!--button type="submit" class="btn  btn-success pull-right">{{trans('general.save_btn')}}</button-->
                                        <input type="button" id="payment_add" class="btn btn-success pull-right" value="{{trans('general.save_btn')}}">
                                        <button type="button" class="btn btn-danger  back-btn">{{trans('general.cancel_btn')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="payment_list" >
                            <div class="pageheader">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="panel panel-default mB10">
                                            <div class="panel-heading">
                                                <h4 class="panel-title col-sm-6">{{trans('payment_list.page_head')}}</h4>
                                                <button type="button" id="add_payment_btn" class="btn btn-success btn-sm pull-right mR10"><span class="icon-plus"></span>{{trans('payment_list.add_payment')}}</button>
                                            </div>
                                            <table id="payment_list_table" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="20%">{{trans('payment_list.created_on')}}</th>
                                                        <th width="40%">{{trans('payment_list.mode_payment')}}</th>
                                                        <th width="20%">{{trans('payment_list.available')}}</th>
                                                    </tr>
                                                </thead>
                                                <div class="panel_controls">
                                                    <div class="row">
                                                        <form id="payment_list" name="payment_list" method="post">
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
                    <div id="shipping_det" class="tab-pane">
                        <div class="panel panel-default mB10" id="shipment-form-panel"  style="display:none">
                            <div class="panel-heading">
                                <h4 class="panel-title">Add Shipment Information</h4>
                            </div>
                            <form name="shipment-form" class="form-horizontal" id="shipment-form">
                                <div class="panel-body">
                                    <input type="hidden" class="ignore-reset" name="psc_id" id="psc_id">
                                    <input type="hidden" class="ignore-reset" name="supplier_product_zone[pss_id]" id="pss_id">
                                    <input type="hidden" class="ignore-reset" name="supplier_product_id" id="supplier_product_id">
                                    <div class="form-group">
                                        <label class="control-label col-md-2" for="geo_zone">{{trans('create_zone_shipment.geo_zone')}}<span class="error">*</span></label>
                                        <div class="col-md-8">
                                            <select name="geo_zone_id" id="geo_zone_id" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-2" for="mode_id">{{trans('create_zone_shipment.mode_id')}}<span class="error">*</span></label>
                                        <div class="col-md-8">
                                            <select name="supplier_product_zone[mode_id]" id="mode_id" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-2" for="delivery_days">{{trans('create_zone_shipment.delivery_days')}}<span class="error">*</span></label>
                                        <div class="col-md-8">
                                            <input type="number" min="0" max="365" name="supplier_product_zone[delivery_days]" id="delivery_days" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-2" for="delivery_charge">{{trans('create_zone_shipment.delivery_charges')}}<span class="error">*</span></label>
                                        <div class="col-md-8">
                                            <input type="number" min="0" max="999999999"  name="supplier_product_zone[delivery_charge]" id="delivery_charge" class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button type="submit" class="btn  btn-success pull-right">{{trans('general.save_btn')}}</button>
                                    <button type="button" class="btn btn-danger  back-btn"> {{trans('general.cancel_btn')}}</button>
                                </div>
                            </form>
                        </div>
                        <div id="shipping_list">
                            <div class="pageheader">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title col-sm-6">{{trans('create_zone_shipment.titlebar_heading')}}</h4>
                                                <button type="button" id="new_zone_btn" class="btn btn-success btn-sm pull-right mR10"><span class="icon-plus"></span>{{trans('create_zone_shipment.create_shipment')}}</button>
                                            </div>
                                            <table id="shipment-table" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>{{trans('create_zone_shipment.geo_zone_id_tbl')}}</th>
                                                        <th>{{trans('create_zone_shipment.mode_id_tbl')}}</th>
                                                        <th>{{trans('create_zone_shipment.delivery_days_tbl')}}</th>
                                                        <th>{{trans('create_zone_shipment.delivery_charges_tbl')}}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
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
        </div>
    </div>
</div>
@stop
@section('scripts')
<script src="{{asset('resources/supports/chosen.ajaxaddition.jquery.js')}}"></script>
<script src="{{asset('resources/supports/chosen.jquery.min.js')}}"></script>
<script src="{{asset('resources/supports/supplier/products/product_properties.js')}}"></script>
<script src="{{asset('resources/supports/supplier/products/product_configure.js')}}"></script>
<script src="{{asset('resources/assets/supplier/support/logger.js')}}"></script>
<script src="{{asset('resources/assets/supplier/support/treeview.js')}}"></script>
@stop
