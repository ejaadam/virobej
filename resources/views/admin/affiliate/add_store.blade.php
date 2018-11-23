@extends('admin.common.layout')
@section('title','Create Support Center')
@section('layoutContent')

<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3c8dbc;
        border-color: #367fa9;
        padding: 1px 10px;
        color: #fff;
    }
</style>
<div class="col-md-12" id="form-panel" >
<div class="panel panel-default">
    <div class="panel-heading">		
        <h4 class="panel-title">Online Retailers</h4>
    </div>
	<div id="msg"></div>
    <div class="panel-body">
		<div id="msg"></div>
		 <form class="form-horizontal form-bordered" id="affiliate-form" action="{{route('admin.online.store-save')}}" method="post" novalidate="novalidate" autocomplete="off" enctype='multipart/form-data'>
              <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#edit-store-info" data-toggle="tab" aria-expanded="false">Basic Details</a></li>
                        <li><a href="#edit-store-address" data-toggle="tab" aria-expanded="false">Descriptions</a></li>
                    </ul>
                 <div class="tab-content">
                        <div class="tab-pane active" id="edit-store-info">
                              <div class="form-group">
								  <label for="textfield" class="control-label col-sm-2">Store Name :</label>
								 <div class="col-sm-6">
								<div id="locationField">
											<input type="text" class="form-control" name="affiliate[store_name]" id="store_name" placeholder="Store name" >
										</div>
								  </div>
                             </div>
					   <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.Affliate_netwrk')}} :</label>
                        <div class="col-sm-6">
                             <select name="affiliate[aff_netwrk]"  id="aff_netwrk" class="form-control">
                                        <option value="">Select network category</option>
                                        @if(!empty($netwrks))
                                        @foreach($netwrks as $key=>$category)
                                        <option value="{{$category->supplier_id}}">{{$category->company_name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.featured')}} : </label>
                        <div class="col-sm-6">
                           <label class="checkbox-inline">
                                        <input type="checkbox" name="affiliate[is_featured]" id="is_featured" value="1"><span class="fade">&nbsp;</span>
                                    </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Image Type :</label>
                              <label class="radio-inline"><input class="imgCls" type="radio" value="1" name="img_type" checked="checked">Image Path</label>
                             <label class="radio-inline"><input class="imgCls" type="radio" value="2" name="img_type">Image Upload</label>
                    </div>
					
                    <div class="form-group" id="image_type1">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.image_url')}} :</label>
                        <div class="col-sm-6">
                            <input type="text" name="affiliate[logo_url]" id="logo_url" class="form-control" placeholder="Logo URL">

                        </div>
                    </div>
                    <div class="form-group"  id="image_type2" style="display:none;">
                        <label for="textfield" class="control-label col-sm-2">Affiliate Logo</label>
                        <div class="col-sm-6">
                           <input type="file" class="cropper"  style="display: none;" data-hide="#form-panel" name="image_url" accept=".gif,.jpg,.jpeg,.png" data-err-msg-to="#image_url-error" data-typeMismatch="Please select valid formet(*.gif, *.jpg, *.jpeg,*.pdf, *.png)" id="image_url" data-default="{{asset(config('constants.AFFILIATE.STORE.LOGO_PATH.DEFAULT'))}}" />
                                    <div class="col-sm-2">
                                        <img class="img img-thumbnail editableImg" data-input="#image_url" id="image_url-preview" src="{{asset(config('constants.AFFILIATE.STORE.LOGO_PATH.DEFAULT'))}}"/><br> 
									   <!--   <input type="file" id="id_image" name="id_image" data-formatallowd="jpg|jpeg|png|pdf" data-error="Please select valid formet(*.jpg, *.jpeg,*.pdf, *.png)" class="file_upload"> -->
											
                                        <span id="image_url-error"></span>
                                    </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.program_id')}} :</label>
                        <div class="col-sm-6">
                            <input type="text" name="affiliate[program_id]" id="program_id" class="form-control" placeholder="{{trans('admin/affiliate.program_id')}}">
                                    <span class="text-muted">Note: Merchant ID from affiliate network</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.affiliate_url')}} :</label>
                        <div class="col-sm-6">
                               <input type="text" name="affiliate[url]" id="url" class="form-control" placeholder="URL">
                                    <span class="text-muted">Note: {USER_ID} must present with URL</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.affiliate_desc_type')}} :</label>
                        <div class="col-sm-6">
                          <label class="radio-inline"><input class="desCls" id="radio_one" type="radio" value="1" name="desc_type" checked="">{{trans('admin/affiliate.text_desc')}}</label>
                           <label class="radio-inline"><input class="desCls" id="radio_two" type="radio" value="2" name="desc_type">{{trans('admin/affiliate.cashback_list')}}</label>
                                
                        </div>
                    </div>
                    <div class="form-group"  id="descFld">
                        <label for="textfield" class="control-label col-sm-2">&nbsp; </label>
                        <div class="col-sm-6">
                             <textarea name="affiliate[description]" data-err-msg-to="#description-error" id="description" class="form-control" placeholder="{{trans('admin/affiliate.affiliate_desc')}}"></textarea>
                                    <span id="description-error"></span>
                        </div>
                    </div>
              
					<div class="form-group"  id="cbFld" style="display:none">
                        <label for="textfield" class="control-label col-sm-2">&nbsp;</label>
                        <div class="col-sm-6"  id="multiFld">
                        </div>
                    </div>
					<div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.affiliate_category')}} :</label>
                        <div class="col-sm-6"  id="tree">
					   </div>
						 <div id="categorySel"></div>
                    </div>
                    
                    <div class="form-group"  id="div_1">
                        <label for="textfield" class="control-label col-sm-2">Country :</label>
                        <div class="col-sm-6" id="tree_country">
                            <div id="countrySel"></div>
					   </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.website_url')}} :</label>
                        <div class="col-sm-6">
                           <input type="text" name="affiliate[website_url]" id="website_url" class="form-control" placeholder="{{trans('admin/affiliate.website_url')}}">
						</div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Select banner :</label>
                        <div class="col-sm-6">
                            <select name="store_banner" id="store_banner" class="form-control">
								  <option value="">Select...</option>
									@if(!empty($banners))
										@foreach($banners as $banner)
											<option value="{{$banner->banner_id}}" data-thumbnail="{{$banner->banner_path}}"> {{$banner->banner_name}} </option>
										@endforeach
									@endif	
								</select>
                        </div>
                    </div>
                    <div class="form-group" id="bannerFld" style="display:none;">
                        <label for="textfield" class="control-label col-sm-2">Banner Preview:</label>
                        <div class="col-sm-6">
                            
						</div>
                    </div>                    
                    </div>                    
                   <div class="tab-pane" id="edit-store-address">
				   
				     <div class="form-group">
                            <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.tags')}} :</label>
                             <div class="col-sm-6">
							 <input type="text" name="affiliate[tags]" id="tags" class="form-control" placeholder="{{trans('admin/affiliate.tags')}}">
                                
                              </div>
                       </div>  
					<div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.meta_title')}} :</label>
                             <div class="col-sm-6">
							      <input type="text" name="affiliate[meta_title]" id="meta_title" class="form-control" placeholder="{{trans('admin/affiliate.meta_title')}}">
                              </div> 
                      </div>  
					  <div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.meta_keyword')}} :</label>
                              <div class="col-sm-6">
							    <textarea name="affiliate[meta_keyword]" id="meta_keyword" class="form-control" cols="100" rows="3" placeholder="{{trans('admin/affiliate.meta_keyword')}}"></textarea>
                              </div>
                       </div>
					    <div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.meta_desc')}} :</label>
                              <div class="col-sm-6">
							  <textarea name="affiliate[meta_desc]" id="meta_desc" class="form-control" cols="100" rows="3" placeholder="{{trans('admin/affiliate.meta_desc')}}"></textarea>
                               
                              </div>
                       </div>
					    <div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.dos')}} :</label>
                              <div class="col-sm-6">
							  <textarea name="affiliate[dos_desc]" id="dos_desc" class="form-control ignore-reset" cols="100" rows="3" placeholder="{{trans('admin/affiliate.dos')}}"></textarea>
                                
                              </div>
                       </div>
					    <div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.dont')}} :</label>
                              <div class="col-sm-6">
							  <textarea name="affiliate[dont_desc]" id="dont_desc" class="form-control ignore-reset" cols="100" rows="3" placeholder="{{trans('admin/affiliate.dont')}}"></textarea>
                               
                              </div>
                       </div>
					    <div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.cashback_old')}} :</label>
                              <div class="col-sm-6">
							    <input type="text" name="affiliate[old_cashback]" id="old_cashback" class="form-control" placeholder="{{trans('admin/affiliate.cashback_old')}}">
                               
                              </div>
							   <div class="col-sm-3">
                                    <select name="affiliate[old_cashback_type]" id="old_cashback_type" class="form-control">
                                        <option value="">Select</option>
                                        <option value="0" selected="selected">% Percent</option>
                                        <option value="1" >Flate</option>
                                    </select>
                                </div>
                       </div>
					   <div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.cashback')}} :</label>
                              <div class="col-sm-6">
							  <input type="text" name="affiliate[cashback]" id="cashback" class="form-control" placeholder="{{trans('admin/affiliate.cashback')}}">
                              </div>
							   <div class="col-sm-3">
                                    <select name="affiliate[cashback_type]" id="cashback_type" class="form-control">
                                        <option value="">Select</option>
                                        <option value="0" selected="selected">% Percent</option>
                                        <option value="1">Flate</option>
                                    </select>
                                </div>
                       </div>
					   <div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.cb_notes')}} :</label>
                              <div class="col-sm-6">
							   <textarea name="affiliate[cb_notes]" id="cb_notes" class="form-control ignore-reset" cols="100" rows="3" placeholder="{{trans('admin/affiliate.cb_notes')}}"></textarea>
                              </div>
                       </div>
					   <div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.cb_traking_period')}} :</label>
                               
                                <div class="col-sm-6">
									<div class="input-group">
									    <input type="text" name="affiliate[cb_traking_period]" id="cb_traking_period" class="form-control" placeholder="{{trans('admin/affiliate.cb_traking_period')}}" data-err-msg-to="cb_traking_period-err">
										<span class="input-group-addon">days</span>
									</div>  
									<span id="cb_traking_period-err" class="error"></span>
                                </div>
                             </div> 
							 <div class="form-group">
								<label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.cb_waiting_period')}} :</label>
                                <div class="col-sm-6">
									<div class="input-group">
									    <input type="text" name="affiliate[cb_waiting_period]" id="cb_waiting_period" class="form-control" placeholder="{{trans('admin/affiliate.cb_waiting_period')}}" data-err-msg-to="cb_waiting_period-err">
										<span class="input-group-addon">days</span>
									</div>         
									<span id="cb_waiting_period-err" class="error"></span>
                                </div>
                       </div>
					   <div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.expired_on')}} :</label>
                              <div class="col-sm-6">
							  <input type="date" name="affiliate[expired_on]" id="from_date" class="form-control datepicker" placeholder="{{trans('admin/affiliate.expired_on')}}">
                                    <span class="text-muted">YYYY-mm-dd</span>
                              </div>
                       </div>
					<!--   <div class="form-group">
                              <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.status')}} :</label>
                              <div class="col-sm-6">
                              </div>
                       </div> -->
					   <div class="form-group">
                               <label for="textfield" class="control-label col-sm-2">{{trans('admin/affiliate.status')}} :</label>
                              <div class="col-sm-6">
							   <select id="status" name="affiliate[status]" class="form-control">
                                        <option value="0">Inactive</option>
                                        <option value="1">Active</option>
                                        <option value="2">Expired</option>
                                    </select>
                              </div>
                       </div>
				   
				   </div>
                    
                </div>
	</div>
	          <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">{{trans('general.submit')}}</button>
            </div>
			</form>
	</div>
	</div>
	</div>
@include('admin.common.cropper')

@stop
@section('scripts')
@include('admin.common.cropper_css_js')
<script>
$(document).ready(function () {
	

});
</script>

<script src="{{asset('resources/assets/plugins/ckeditor/ckeditor.js')}}"></script>
<script src="{{asset('js/providers/admin/affiliate/list.js')}}" charset="utf-8"></script>
<script src="{{asset('js/providers/form.js')}}"></script>
<link rel="stylesheet" href="{{asset('resources/assets/plugins/fancy-tree/dist/skin-win7/ui.fancytree.css')}}">
<script src="{{asset('resources/assets/plugins/fancy-tree/fancy_tree.js')}}"></script>
@stop
