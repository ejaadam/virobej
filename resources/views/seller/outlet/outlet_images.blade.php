@extends('seller.common.layout')
@section('pagetitle','Outlet Images')
@section('breadCrumbs')
<li>Merchant</li>
<li>Outlet Images</li>
@stop
@section('top-nav')	
@stop
@section('layoutContent')
<div class="col-md-12">
    <div class="box box-primary" id="store-img-panel">
        <div class="box-header with-border">
            <i class="fa fa-list-alt"></i>
            <h3 class="box-title store-name"></h3>
            <div class="box-tools ">
                <input type="button" class="btn btn-sm" id="add-store-imgs" value="Add">
            </div>
        </div>
        <div class="box-body">
            <input type="hidden" name="store_code" id="store_code" value="{{$store_code}}">
            <form id="store_image_list" action="{{route('seller.outlet.photos.list')}}">
                <table id="store-image-list-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Created On</th>
                            <th>Image</th>                            
                            <th>Status</th>
                            <th>Verification</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <div class="box box-primary" id="store-form-panel" style="display:none;">
        <div class="box-header with-border">
            <i class="fa fa-list-alt"></i>
            <h3 class="box-title store-name"></h3>
            <div class="box-tools pull-right">
                <a href="#" class="btn btn-social bg-navy btn-sm" id="close-form-panel"><i class="fa fa-times"></i>Close</a>
            </div>
        </div>
        <div class="box-body">
            <form id="upload-photos-form" method="post" enctype="multipart/form-data" class="form form-horizontal" action="{{route('seller.outlet.photos.upload')}}">
                <input type="hidden" name="store_code" id="store_code" value="{{$store_code}}">
                <div class="form-group">
                    <label class="control-label col-sm-2">{{trans('general.stores.images.outlet_img')}} :</label>
                    <div class="col-sm-6">
                        <input type="file" multiple="multiple" accept="image/gif,image/jpg,image/jpeg,image/png" name="files[]" id="files" class="form-control"/>
                        <span class="" id="file_err"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-sm-2">{{trans('general.stores.status')}} :</label>
                    <div class="col-sm-6">
                        <select name="status" id="status" class="form-control">
                            <option value="">Select</option>
                            @if(!empty($status))
                            @foreach($status as $key=>$val)
                            <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="box-footer" id="save">
                    <button type="submit" class="btn btn-primary pull-right" id="update-details">{{trans('general.btn.submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
@section('scripts')
	@include('seller/common/cropper_css_js')
	<script src="{{asset('js/providers/seller/outlet/upload_photos.js')}}"></script>	
@stop
