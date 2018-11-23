@extends('seller.common.layout')
@section('pagetitle')
Add New Product
@stop

@section('layoutContent')
<div class="panel panel-default mT0">
    <div class="panel-heading">
        <h4 class="panel-title">Add New Product  <small class="text-muted">(<span class="error">*</span> - mentioned field are mandatory)</small></h4>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="tabbable tabs-left tabbable-bordered" id="add_new" >
                <ul class="nav nav-tabs">
                    <li class="info_tab active"><a data-toggle="tab" href="#info">Information</a></li>
                    <li class="seo_tab"><a data-toggle="tab" href="#seo">SEO</a></li>
                    <li class="ass_tab"><a data-toggle="tab" href="#assoc" id="assoc_tab">Association</a></li>
                </ul>
                <div class="tab-content">
                    <div id="info" class="tab-pane active">
                        <div class="panel panel-default mT0">
                            <div class="panel-heading">
                                <h4 class="panel-title">Information </h4>
                            </div>
                            <form class="form" id="meta_info_form" action="">
                                <div class="panel-body">
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="product">{!!$fields['product.product_name']['label']!!}<span class="error">*</span></label>
                                        <div class="col-sm-10">
                                            <input id="product_name" class="form-control" {!!build_attribute($fields['product.product_name']['attr'])!!}/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <label class="control-label col-sm-4" for="eanbarcode">{!!$fields['details.eanbarcode']['label']!!}<span class="error">*</span></label>
                                                <div class="col-sm-8">
                                                    <input id="eanbarcode" value="{{$data['eanbarcode'] or ''}}" class="form-control" {!!build_attribute($fields['details.eanbarcode']['attr'])!!}/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <label class="control-label col-sm-4" for="upcbarcode">{!!$fields['details.upcbarcode']['label']!!}<span class="error">*</span></label>
                                                <div class="col-sm-8">
                                                    <input id="upcbarcode" value="{{$data['upcbarcode'] or ''}}" class="form-control" {!!build_attribute($fields['details.upcbarcode']['attr'])!!}/>
                                                </div>
                                            </div>
                                        </div>
                                    </div >
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="sku">{!!$fields['details.sku']['label']!!}<span class="error">*</span></label>
                                        <div class="col-sm-3">
                                            <input id="sku" class="form-control"  {!!build_attribute($fields['details.sku']['attr'])!!}/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="description">{!!$fields['details.description']['label']!!}<span class="error">*</span></label>
                                        <div class="col-sm-10">
                                            <textarea class="ckeditor" cols="80" id="editor1" data-err-msg-to="#editor1_error" rows="10" {!!build_attribute($fields['details.description']['attr'])!!}></textarea>
                                            <span id="editor1_error" class=""></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <label class="control-label col-sm-4" for="is_exclusive">{!!$fields['details.is_exclusive']['label']!!}</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" id="exclusive" {!!build_attribute($fields['details.is_exclusive']['attr'])!!}>
                                                        <option value="1">Yes</option>
                                                        <option value="0" >No</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <label class="control-label col-sm-4" for="visiblity_id">{!!$fields['details.visiblity_id']['label']!!}<span class="error">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" id="product_visibility" {!!build_attribute($fields['details.visiblity_id']['attr'])!!} >
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="tags">{!!$fields['tags']['label']!!}<span class="error">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" id="tags" value="" {!!build_attribute($fields['tags']['attr'])!!}>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-md-2" for="weight">{!!$fields['details.weight']['label']!!}</label>
                                        <div class="col-md-4 ">
                                            <div class="input-group">
                                                <input data-err-msg-to="#weight-error" id="weight" value="{{$product_details->weight or ''}}" class="form-control" {!!build_attribute($fields['details.weight']['attr'])!!}/>
                                                <span class="input-group-addon">KG</span>
                                            </div>
                                            <span id="weight-error"></span>
                                        </div>
                                        <label class="control-label col-md-2" for="width">{!!$fields['details.width']['label']!!}</label>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input data-err-msg-to="#width-error" id="width" value="{{$product_details->width or ''}}" class="form-control" {!!build_attribute($fields['details.width']['attr'])!!}/>
                                                <span class="input-group-addon">CM</span>
                                            </div>
                                            <span id="width-error"></span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="control-label col-md-2" for="height">{!!$fields['details.height']['label']!!}</label>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input data-err-msg-to="#height-error" id="height" value="{{$product_details->height or ''}}" class="form-control" {!!build_attribute($fields['details.height']['attr'])!!}/>
                                                <span class="input-group-addon">CM</span>
                                            </div>
                                            <span id="height-error"></span>
                                        </div>
                                        <label class="control-label col-md-2" for="length">{!!$fields['details.length']['label']!!}</label>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <input data-err-msg-to="#length-error" id="length" value="{{$product_details->length or ''}}" class="form-control" {!!build_attribute($fields['details.length']['attr'])!!}/>
                                                <span class="input-group-addon">CM</span>
                                            </div>
                                            <span id="length-error"></span>
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
                                </div>
                                <div class="panel-footer">
                                    <a data-toggle="tab" href="#seo" id="seo_tab"><button class="btn btn-success pull-right" >NEXT</button></a>
                                    <br/><br/>									
                                </div>
                            <!--/form-->
                        </div>
                    </div>
                    <div id="seo" class="tab-pane">
                        <div class="panel panel-default mT0">
                            <div class="panel-heading">
                                <h4 class="panel-title">SEO</h4>
                            </div>
                            <div class="panel-body">
                                <!--form class="form" id="seo" action="" novalidate="novalidate"-->
                                    <input type="hidden" name="meta_info[post_type_id]" id="post_type_id" value="1">
                                    <input type="hidden" name="meta_info[relative_post_id]" id="relative_post_id" value="">
                                    <div class="row">
                                        <label class="control-label col-sm-4" for="description">{!!$fields['meta_info.description']['label']!!}</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" id="description" {!!build_attribute($fields['meta_info.description']['attr'])!!}></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-4" for="meta_keys">{!!$fields['meta_info.meta_keys']['label']!!}</label>
                                        <div class="col-sm-4">
                                            <input class="form-control" id="meta_keys" {!!build_attribute($fields['meta_info.meta_keys']['attr'])!!}/>
                                        </div>
                                    </div>
                                <!--/form-->
                            </div>
                            <div class="panel-footer">
                                 <a data-toggle="tab" href="#assoc" id="asso_tab"><button class="btn btn-success pull-right" >NEXT</button></a>
                                 <br/><br/>
                            </div>
                        </div>
                    </div>
                    <div id="assoc" class="tab-pane">
                        <div class="panel panel-default mT0">
                            <div class="panel-heading">
                                <h4 class="panel-title">Association</h4>
                            </div>
                            <!--form class="form" id="assoc_form" action="" novalidate="novalidate"-->
                                <div class="panel-body">
                                    <div class="row">
                                        <input type="hidden" name="product_id" value=""/>
                                        <!--div id="treeview-container">
                                        <?php

                                        function print_category ($product_categories)
                                        {
                                            echo '<ul>';
                                            foreach ($product_categories as $product_category)
                                            {
                                                echo '<li data-name="catlist[]" data-category_id="'.$product_category->category_id.'">'.$product_category->category;
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
                                        </div-->
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="category_id">{!!$fields['product.category_id']['label']!!}<span class="error">*</span></label>
                                        <div class="col-sm-10">
                                            <select id="category_id" class="form-control" {!!build_attribute($fields['product.category_id']['attr'])!!}></select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2" for="brand_id">{!!$fields['product.brand_id']['label']!!}<span class="error">*</span></label>
                                        <div class="col-sm-10">
                                            <select id="brand_id" class="form-control" {!!build_attribute($fields['product.brand_id']['attr'])!!}></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button type="submit" class="btn btn-success pull-right" >Save</button>
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
<script src="{{asset('resources/supports/supplier/add_new_product.js')}}"></script>
<script src="{{asset('resources/assets/supplier/support/logger.js')}}"></script>
<script src="{{asset('resources/assets/supplier/support/treeview.js')}}"></script>
@stop
