@extends('seller.common.layout')
@section('pagetitle')
Account Verification
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
<div class="pageheader">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default" id="document_upload">
                <div class="panel-heading">
                    <h4 class="panel-title col-sm-6">Upload Document</h4>
                </div>
                <form id="upload_form" action="{{URL::route('api.v1.seller.verification.list-data')}}">
                    <div class="panel-body">
                        <div class="row">
                            <label class="control-label col-md-2" for="product">Upload File:<span class="error">*</span></label>
                            <div class="col-md-3">
                                <input type="file" data-format="jpg|jpeg|png|pdf" name="document" id="doc4" placeholder="Inlcude some file" class="form-control upload"required="true">
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-md-2" for="document_type_id"> Document Type:<span class="error">*</span></label>
                            <div class="col-md-3">
                                <select name="document_type_id" class="form-control select_type" id="document_type_id" required>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-offset-2 col-sm-3">
                                <button id="" type="submit" class="btn btn-primary btn-sm">Upload</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="alert-msg" class="alert-msg"></div>
        <div class="col-sm-12">
            <div class="panel panel-default" id="list">
                <div class="panel-heading">
                    <h4 class="panel-title col-sm-6">Account Verification </h4>
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <form id="product_list_form" action="{{URL::route('api.v1.seller.verification.list-data')}}">
                            <div class="col-sm-2">
                                <select  name="type_filer"  id="type_filer" class="form-control">
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group date ebro_datepicker col-sm-6 pdL" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                    <input class="form-control" type="text" id="from" name="from" placeholder="From">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                </div>
                                <div class="input-group date ebro_datepicker col-sm-6 pdR" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                    <input class="form-control" type="text" id="to" name="to" placeholder="To">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <button id="search" type="button" class="btn btn-primary btn-sm">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <table id="image_verify_list" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Created On</th>
                            <th>Image(or)Document Link</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
<script src="{{asset('resources/supports/supplier/account_verification.js')}}"></script>
<script src="{{asset('resources/supports/Jquery-loadselect.js')}}"></script>
<script src="{{asset('resources/supports/app.js')}}"></script>
@stop
