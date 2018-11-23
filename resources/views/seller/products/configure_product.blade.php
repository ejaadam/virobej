<div class="tabbable tabs-left tabbable-bordered" id="configure" data-product_id="" data-supplier_product_id="" style="display: none;">
    <ul class="nav nav-tabs" id="nav_id">
        <li class="active"><a data-toggle="tab" href="#info">Information</a></li>
        <li class=""><a data-toggle="tab" href="#pric">Price</a></li>
        <li class=""><a data-toggle="tab" href="#seo">SEO</a></li>        <li class=""><a data-toggle="tab" id="assoc_tab" href="#assoc">Association</a></li>
        <li class="" id="project_det"><a data-toggle="tab" href="#product_details" >Product Details</a></li>
        <li id="manage_sto"><a data-toggle="tab" href="#manage_stock_tab" >Manage Stock</a></li>
        <li id="manage_img"><a data-toggle="tab" href="#manage_image_tab" >Manage Image</a></li>
        <li id="manage_combi"><a data-toggle="tab" id="manage_combo" href="#manage_combination_tab" >Manage Combinations</a></li>
    </ul>
    <div class="tab-content">
        <div id="info" class="tab-pane">
            <div class="panel panel-default mT0">
                <div class="panel-heading">
                    <h4 class="panel-title">Information</h4>
                </div>
                <form class="form" id="meta_info_form" action="" novalidate="novalidate">
                    <div class="panel-body">                        <div class="row">
                            <label class="control-label col-md-2" for="product">Product Name:<span class="error">*</span></label>
                            <div class="col-md-3">
                                <input type="text" name="product[product_name]" id="product_name" class="form-control"/>
                            </div>
                        </div>                        <div class="row">
                            <label class="control-label col-md-2" for="sku">SKU:<span class="error">*</span></label>
                            <div class="col-md-3">
                                <input type="text" name="product[sku]" id="sku" class="form-control"/>
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2" for="description">Description:<span class="error">*</span></label>
                            <div class="col-md-10">
                                <textarea class="ckeditor" cols="80" id="editor1" name="product[description]" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2" for="product">Store:<span class="error">*</span></label>
                            <div class="col-md-3">
                                <select name="supplier_product_new[store_id]" id="store_id" class="form-control"></select>
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2" for="pre_order">Enabled:</label>
                            <div class="col-md-2">
                                <select class="form-control" name="supplier_product_new[pre_order]" id="status">
                                    <option value="1" >Yes</option>
                                    <option value="0" >No</option>
                                </select>
                            </div>
                        </div>                        <div class="row" id="redirect_status" style="display:none">
                            <label class="control-label col-md-2" for="pre_order">Redirect when disabled:</label>
                            <div class="col-md-3">
                                <select class="form-control" name="product[redirect_id]" id="redirect_stat">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2" for="pre_order">Condition:</label>
                            <div class="col-md-3">
                                <select class="form-control" name="product[condition_id]" id="condition">                                </select>
                            </div>
                        </div>                        <div class="row">
                            <label class="control-label col-md-2" for="pre_order">Visiblity:<span class="error">*</span></label>
                            <div class="col-md-3">
                                <select class="form-control" name="product[visiblity_id]" id="product_visibility">
                                </select>
                            </div>
                        </div>                        <div class="row">
                            <label class="control-label col-md-2" for="pre_order">Tags:<span class="error">*</span></label>                            <select id="states-multi-select" name="states-multi-select" multiple="" style="display: none;">
                                <option value=""></option>                            </select>
                            <div class="chosen-container chosen-container-multi" style="width: 150px;" title="" id="states_multi_select_chosen"><ul class="chosen-choices"><li class="search-field"><input value="Select Some Options" class="default" autocomplete="off" style="width: 140px;" type="text"></li></ul><div class="chosen-drop"><ul class="chosen-results"></ul></div></div>
                        </div>                    </div>
                    <div class="panel-footer">
                        <button type="button" id="" class="btn btn-success pull-right product_save_btn" >Save</button>
                        <input type="button" class="btn btn-danger back-btn" value="Cancel">
                    </div>
                </form>
            </div>
        </div>
        <div id="pric" class="tab-pane">
            <div class="panel panel-default mT0" id="price-list-panel">
                <div class="panel-heading">
                    <h4 class="panel-title">Price</h4>
                    <a href="#" id="add-new-price-btn" class="btn btn-success btn-sm pull-right "><span class="icon-plus"></span>Add Currency Price</a>
                </div>
                <div class="panel-body">
                    <table class="table"  id="product-price">
                        <thead>
                            <tr>
                                <th>Currency</th>
                                <th>MRP Price</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="panel-footer">
                    <button type="button" class="btn btn-success pull-right product_save_btn" >Save</button>
                    <input type="button" class="btn btn-danger back-btn" value="Cancel">
                </div>
            </div>
            <div class="panel panel-default mT0" id="price-form-panel">
                <div class="panel-heading">
                    <h4 class="panel-title">Price</h4>
                </div>
                <div class="panel-body">
                    <form class="form" id="price_form" action="" novalidate="novalidate">
                        <div class="row">
                            <label class="control-label col-md-2" for="mrp_price">MRP Price:<span class="error">*</span></label>
                            <div class="col-md-10">
                                <input type="number" min="1" max="999999999"  name="supplier_product_price[currency]" id="mrp_price" class="form-control"/>
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2" for="mrp_price">MRP Price:<span class="error">*</span></label>
                            <div class="col-md-10">
                                <input type="number" min="1" max="999999999"  name="supplier_product_price[mrp_price]" id="mrp_price" class="form-control"/>
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2" for="price">Price:<span class="error">*</span></label>
                            <div class="col-md-10">
                                <input type="number" min="1" max="999999999" name="supplier_product_price[price]" id="price" class="form-control"/>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel-footer">
                    <button type="button" class="btn btn-success pull-right product_save_btn" >Save</button>
                    <input type="button" id="back-to-price-list" class="btn btn-danger" value="Cancel">
                </div>
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
                        <div class="row hidden">
                            <label class="control-label col-md-4" for="url_slug">URL Slug:</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="meta_info[url_slug]" id="url_slug">
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
                                <input type="text" class="form-control" name="meta_info[meta_keys]" id="meta_keys"/>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel-footer">
                    <button type="button" id="" class="btn btn-success pull-right product_save_btn" >Save</button>
                    <input type="button" class="btn btn-danger back-btn" value="Cancel">                </div>
            </div>        </div>
        <div id="assoc" class="tab-pane">
            <div class="panel panel-default mT0">
                <div class="panel-heading">
                    <h4 class="panel-title">Association</h4>
                </div>
                <div class="panel-body">
                    <form class="form" id="assoc" action="" novalidate="novalidate">
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
                        </div>                    </form>
                </div>
                <div class="panel-footer">                    <button type="button" id="" class="btn btn-success pull-right product_save_btn" >Save</button>
                    <input type="button" class="btn btn-danger back-btn" value="Cancel">                </div>
            </div>
        </div>
        <div id="product_details" class="tab-pane active">
            <div class="panel panel-default mT0" id="form" style="">
                <div class="panel-heading">
                    <h4 class="panel-title">Edit Product</h4>
                </div>
                <div class="panel-body">
                    <form name="supplier_product_form" class="form-horizontal" id="supplier_product_form" action="{{URL::to('supplier/products/save')}}">
                        <input type="hidden" name="supplier_product_id" id="supplier_product_id_com">
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
                            </div>                        </div>
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
        </div>        <div id="manage_stock_tab" class="tab-pane">            <div class="panel panel-default mT0">
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
                        <div class="row">                            <label class="col-md-3">{{Lang::get('supplier_product_list.product_sku');}}</label>
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
                        </div>                        <div class="row" id="new_stock_value">
                            <label class="col-md-3">{{Lang::get('supplier_product_list.product_stock_value');}}</label>
                            <div class="col-md-9">
                                <input name="stock_value" class="form-control" id="stock_value">
                            </div>
                        </div>                        <div class="row">
                            <label class="col-md-3"></label>
                            <div class="col-md-9">
                                <input type="submit" class="btn btn-info" rel="" name="addstock"  id="addstocks">
                                <button name="add_new_category"  id="add_new_category" style="display:none">{{Lang::get('general.add_stock');}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>        <div id= "manage_image_tab" class="tab-pane">
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
        <div id="manage_combination_tab" class="tab-pane">            <div id="add_combination" style="display:none">
                <form class="form" id="add-edit-form" action="{{URL::to('supplier/products/combinations/save')}}" style="">
                    <input type="hidden" name="product_cmb_id" id="product_cmb_id">
                    <input type="hidden" name="product_cmb[product_id]" id="product_id">
                    <input type="hidden" name="product_supplier_id" id="product_supplier_id">
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
                                </div>                            </div>
                        </div>
                    </div>
                </form>
            </div>            <div id="combination_list" style="">
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
                                            <form id="category_list" name="category_list" action="{{URL::to('supplier/category/product_categories')}}" method="post">                                            </form>
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
