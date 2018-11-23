
@extends('admin.common.layout')
@section('pagetitle')
Affiliate
@stop
@section('top_navigation')
@include('admin.top_nav.supplier_navigation')
@stop
@section('layoutContent')
<section class="content">
<div class="row">
    
	<div class="col-md-12" id="Category_list">
        <div class="panel panel-default" >
            <div class="panel-heading">

                <h4 class="panel-title">In-Store Categories</h4>
            </div>
			    <div class="panel_controls">
                <div class="row">
               <form id="cat_listfrm" name="cat_listfrm" class="form form-bordered" action="{{route('admin.seller.in_store.category-list')}}" method="get">
                        <input type="hidden" class="form-control" id="status_col"  value ="status_value">
                        <div class="input-group col-sm-3">
                             <label for="from">{{trans('admin/general.search_term')}}</label>
                              <input type="search" id="search_term" name="search_term" class="panel_controls form-control is_valid_string col-xs-12"  placeholder="{{trans('admin/in_store_category.search_ph')}}" value="">
                        </div>
						
                        <div class="col-sm-6">
						  <div class="input-group">
				        <label for="from">&nbsp;</label>
                         <button type="button" id="searchbtn" class="btn btn-sm  btn-primary"><i class="fa fa-search"></i>     {{trans('general.btn.search')}}</button>&nbsp;
                        <button type="button" id="resetbtn" class="btn btn-sm  btn-primary"><i class="fa fa-repeat"></i>     {{trans('general.btn.reset')}}</button>&nbsp;
                        <button type="button" class="btn btn-sm btn-primary add_btn" data-toggle="modal" data-target=""> <span class="icon-plus"></span> {{trans('admin/in_store_category.add')}} </button>&nbsp;
						 <button type="submit" name="exportbtn" id="exportbtn" class="btn btn-sm btn-primary" value="Export"><i class="fa fa-file-excel-o"></i>    {{trans('admin/general.export_btn')}}</button>
                        </div>

                        </div>
                    </form>
                </div>
            </div>
           
            <div class="box-body table-responsive">
            
				  <table id="category_listtbl" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{trans('general.label.created_on')}}</th>
                        <th>{{trans('admin/in_store_category.category')}} </th>
                        <th>{{trans('admin/in_store_category.status')}} </th>
                        <th>{{trans('general.label.action')}} </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
          </div>
       
    </div>
    </div>
	</div>
		<!--  Category Edit -->
<div class="panel panel-default"  id="Category_edit" style="display:none">
    <div class="panel-heading">
       <div class="pull-right">
                <button class="btn btn-danger btn-sm close_btn" > <i class="fa fa-times"></i> </button>
            </div>	
      <h4 class="panel-title"></h4>
    </div>
    <div class="panel-body">
                <!-- form start -->
                 <form method="post" class="form-horizontal form-bordered form-validate" id="category_editfrm" action="{{route('admin.seller.in_store.update')}}" autocomplete="off"  enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/in_store_category.bcategory_name')}}</label>
                        <div class="col-sm-6">
                        <input type="text" name="bcategory_name" id="bcategory_name" class="form-control"  placeholder="{{trans('admin/in_store_category.bcat_name_ph')}}"  value="">
                        <input type="hidden" id="category_id" name="category_id" class=""  value="">
                        </div>
                    </div>
					   <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/in_store_category.url')}}</label>
                        <div class="col-sm-6">
						 <div class="input-group">
                            <input type="text" name="bcategory_slug" id="bcategory_slug" class="form-control"  placeholder="{{trans('admin/in_store_category.url_ph')}}" value="" readonly>
							<a class="input-group-addon" href="javascript:void(0);" id="editslugBtn"><i class="fa fa-edit"></i></a>
                        </div>
                       </div>
                    </div>
                    <div class="form-group">
					 <input type="hidden" id="current_cat_id" name="parent_bcategory_id" class=""  value="">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/in_store_category.parent_category')}} </label>
                        <div class="col-sm-6">
                        <button  id="choose_category">{{trans('admin/in_store_category.choose_pat_cat')}}</button>
                        <div id="category_linage"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/in_store_category.meta_title')}}</label>
                          <div class="col-sm-6">
						 <input type="text" name="meta_title" id="meta_title" class="form-control"  placeholder="{{trans('admin/in_store_category.meta_title_ph')}}">
                    </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/in_store_category.meta_desc')}} </label>
                        <div class="col-sm-6">
                             <textarea rows="7" name="meta_desc" id="meta_desc" class="form-control"  placeholder="{{trans('admin/in_store_category.meta_desc_ph')}}"  value=""></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/in_store_category.meta_keywords')}}</label>
                        <div class="col-sm-6">
                            <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"  placeholder="{{trans('admin/in_store_category.meta_key_ph')}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/in_store_category.category_image')}}</label>
                        <div class="col-sm-6">
                             <input type="file" class="cropper" style="display: none;" data-hide="#Category_edit" data-width="{{config('constants.BCATEGORY_IMG_PATH.UPLOAD.WIDTH')}}" data-height="{{config('constants.BCATEGORY_IMG_PATH.UPLOAD.HEIGHT')}}" name="image_url" accept=".gif,.jpg,.jpeg,.png" data-err-msg-to="#image_url-error" data-typeMismatch="Please select valid formet(*.gif, *.jpg, *.jpeg,*.pdf, *.png)" id="image_url" data-default="{{asset(config('constants.STORE_LOGO_PATH.WEB').'store.jpg')}}" />
                             <div class="col-sm-4">
                            <img class="img img-thumbnail editable-img" data-input="#image_url" id="image_url-preview" src="{{asset('resources/uploads/bcategories/default_img.jpg')}}" /><br>
                            <span id="image_url-error"></span>
                         </div>
						</div>
                       </div>
                      <div class="form-group form-actions">
                       <div class="col-md-9 col-md-offset-3">
                       <div class="modal-footer">
                         <button type="button" class="btn btn-sm btn-default close_btn" data-dismiss="modal">{{trans('admin/in_store_category.close')}} </button>
                       <button type="submit" name="update_category" id="update_category" class="btn btn-sm  btn-primary" value=""></button>
                   </div>
                  </div>
                </div>
              </form>
               </div>
            </div>
			
<div class="modal fade" id="parent_categoryModal" role="dialog" >
     <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close modal-close_btn" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{trans('admin/in_store_category.parent_category')}}</h4>
            </div>
            <div class="modal-body" style="height: 300px;">
                <p id="cat_link"></p>
                <select name="" id="catSelect" class="form-control" ></select>
            </div>
            <div class="modal-footer">
                <button type="button" class=" modal-close_btn btn btn-default" data-dismiss="modal">{{trans('admin/in_store_category.close')}}</button>
            </div>
        </div>
    </div>
</div>
</section>


@include('admin.common.assets')
@include('admin.common.cropper')
@stop
@section('scripts')
@include('admin.common.cropper_css_js')
<script src="{{asset('admin/validate/lang/in-store_category')}} " charset="utf-8"></script>
<script src="{{asset('js/providers/admin/seller/in_store_categories.js')}}"></script>
@stop

