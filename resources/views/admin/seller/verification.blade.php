@extends('admin.common.layout')
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
        <div id="alert-msg" class="alert-msg"></div>
        <div class="col-sm-12">
            <div class="panel panel-default" id="list">
                <div class="panel-heading">
                    <h4 class="panel-title col-sm-6">Supplier Verification </h4>
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <form id="verification_docs_list" action="{{URL::to('admin/seller/verification')}}">
                            <div class="col-sm-3">
                                <input class="form-control" type="text" id="search_term" name="search_term" placeholder="Search">
                            </div>
                            <div class="col-sm-2">
                                <select  name="type_filer"  id="type_filer" class="form-control">
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select  name="status"  id="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="0" {{isset($status) && $status==0 ? 'selected="selected"' : ''}}>Pending</option>
                                    <option value="1" {{isset($status) && $status==1 ? 'selected="selected"' : ''}}>Verified</option>
                                    <option value="2" {{isset($status) && $status==2 ? 'selected="selected"' : ''}}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                    <input class="form-control" type="text" id="from" name="from" placeholder="From">
                                    <span class="input-group-addon">-</span>
                                    <input class="form-control" type="text" id="to" name="to" placeholder="To">
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <button id="search" type="button" class="btn btn-primary btn-sm">Search</button>
                            </div>
                            <input type="hidden" id="uname" name="uname" value="{{$uname or ''}}">
                        </form>
                    </div>
                </div>
                <table id="image_verify_list" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Created On</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Image</th>
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
<script src="{{asset('resources/supports/admin/seller/account_verification.js')}}"></script>	
@stop
