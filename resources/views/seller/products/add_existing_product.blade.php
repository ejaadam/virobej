@extends('supplier.common.layout')
@section('pagetitle')
Add New Product
@stop
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')
<div class="panel panel-default mT0">
    <div class="panel-heading">
        <h4 class="panel-title">Add New Product</h4>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="tabbable tabs-left tabbable-bordered" id="add_new" >
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#info">Information</a></li>
                    <li><a data-toggle="tab" href="#seo"> SEO</a></li>
                    <li><a data-toggle="tab" id="assoc_tab" href="#assoc">Association</a></li>
                </ul>
                <div class="tab-content">
                    <div id="info" class="tab-pane active">
                        <div class="panel panel-default mT0">
                            <div class="panel-heading">
                                <h4 class="panel-title">Information</h4>
                            </div>
                            <form class="form" id="meta_info_form" action="" novalidate="novalidate">
                                <div class="panel-body">
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="product">Product Name:<span class="error">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="product[product_name]" id="product_name" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <label class="control-label col-sm-4" for="eanbarcode">EAN Barcode:<span class="error">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="product[eanbarcode]" id="eanbarcode" class="form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <label class="control-label col-sm-4" for="upcbarcode">UPC Barcode:<span class="error">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="product[upcbarcode]" id="upcbarcode" class="form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div >
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="sku">SKU:<span class="error">*</span></label>
                                        <div class="col-sm-3">
                                            <input type="text" name="product[sku]" id="sku" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="description">Description:<span class="error">*</span></label>
                                        <div class="col-sm-10">
                                            <textarea class="ckeditor" cols="80" id="editor1" name="product[description]" rows="10"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <label class="control-label col-sm-4" for="is_exclusive">Exclusive :</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" name="product[is_exclusive]" id="exclusive">
                                                        <option value="1">Yes</option>
                                                        <option value="0" >No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <label class="control-label col-sm-4" for="visiblity_id">Visiblity:<span class="error">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" name="product[visiblity_id]" id="product_visibility">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="states-multi-select">Tags:<span class="error">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" id="tags" name="tags" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="weight">{{Lang::get('create_zone_shipment.weight');}}</label>
                                        <div class="col-md-4 input-group">
                                            <input type="number" min="0" max="999999999"  name="product[weight]" id="weight" value="{{$product_details->weight or ''}}" class="form-control" onkeypress="return isNumberKey(event);"/>
                                            <span class="input-group-addon">kg</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="width">{{Lang::get('create_zone_shipment.width');}}</label>
                                        <div class="col-md-4 input-group">
                                            <input type="number" min="0" max="999999999"  name="product[width]" id="width" value="{{$product_details->width or ''}}" class="form-control" onkeypress="return isNumberKey(event);"/>
                                            <span class="input-group-addon">cm</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="height">{{Lang::get('create_zone_shipment.height');}}</label>
                                        <div class="col-md-4 input-group">
                                            <input type="number" min="0" max="999999999"  name="product[height]" id="height" value="{{$product_details->height or ''}}" onkeypress="return isNumberKey(event);" class="form-control"/>
                                            <span class="input-group-addon">cm</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="length">{{Lang::get('create_zone_shipment.length');}}</label>
                                        <div class="col-md-4 input-group">
                                            <input type="number" min="0" max="999999999"  name="product[length]" id="length" value="{{$product_details->length or ''}}" onkeypress="return isNumberKey(event);" class="form-control"/>
                                            <span class="input-group-addon">cm</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button type="button" class="btn btn-success pull-right product_save_btn" >Save</button>
                                    <input type="button" class="btn btn-danger back-btn" value="Cancel">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="seo" class="tab-pane">
                        <div class="panel panel-default mT0">
                            <div class="panel-heading">
                                <h4 class="panel-title">SEO</h4>
                            </div>
                            <div class="panel-body">
                                <form class="form" id="seo" action="" novalidate="novalidate">
                                    <input type="hidden" name="meta_info[post_type_id]" id="post_type_id" value="1">
                                    <input type="hidden" name="meta_info[relative_post_id]" id="relative_post_id" value="">
                                    <div class="row">
                                        <label class="control-label col-sm-4" for="description">Meta Description:</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" name="meta_info[description]" id="description"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-4" for="meta_keys">Meta Keys:</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="meta_info[meta_keys]" id="meta_keys"/>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="panel-footer">
                                <button type="button" class="btn btn-success pull-right product_save_btn" >Save</button>
                                <input type="button" class="btn btn-danger back-btn" value="Cancel">
                            </div>
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
                                        <input type="hidden" name="product_id" value=""/>
                                        <?php

                                        function print_category ($product_categories)
                                        {
                                            echo '<ul>';
                                            foreach ($product_categories as $product_category)
                                            {
                                                echo '<li data-category_id="'.$product_category->category_id.'"><input type="checkbox" name="catlist[]"class="form-control"  value="'.$product_category->category_id.'"/>'.$product_category->category;
                                                if (!empty($product_category->sub_categories))
                                                {
                                                    print_category($product_category->sub_categories);
                                                }
                                                echo '</li>';
                                            }
                                            echo '</ul>';
                                        }

                                        print_category($product_categories);
                                        ?>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="category_id">Category:<span class="error">*</span></label>
                                        <div class="col-sm-10">
                                            <select name="product[category_id]" id="category_id" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="brand_id">Brand:<span class="error">*</span></label>
                                        <div class="col-sm-10">
                                            <select name="product[brand_id]" id="brand_id" class="form-control"></select>
                                        </div>
                                    </div>

                                </div>
                                <div class="panel-footer">
                                    <button type="button" class="btn btn-success pull-right product_save_btn" >Save</button>
                                    <input type="button" class="btn btn-danger back-btn" value="Cancel">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
{{HTML::script('supports/supplier/add_new_product.js')}}
@stop
