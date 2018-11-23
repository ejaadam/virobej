@extends('admin.common.layout')
@section('pagetitle')
Supplier Brand
@stop
@section('layoutContent')
<div class="pageheader">
    <div class="row">
        <div id="alert-div"></div>
        <div class="col-sm-12">
            <div class="panel panel-default" id="list">
                <div class="panel-heading">
                    <h4 class="panel-title col-sm-6">Product Brand</h4>
                    <button type="button" id="create_brand" class="btn btn-success btn-sm pull-right"><span class="icon-plus"></span>Create Brand</button>
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <form id="brand_list" name="brand_list" action="{{URL::to('admin/catalog/products/suppliers/brands')}}" method="get">
                            <div class="col-sm-3">
                                <input type="text" name="search_term" placeholder="Search" id="search_term" class="form-control">
                            </div>
                            <div class="col-sm-3">
                                <select name="supplier_id" id="supplier_id" class="form-control"></select>
                            </div>
                            <div class="col-sm-3">
                                <select name="brand_id" id="brand_id" class="form-control"></select>
                            </div>
                            <div class="col-sm-3">
                                <button id="search" type="button" class="btn btn-primary btn-sm">Search</button>
                                <!--input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Export" formtarget="_new"/>
                                <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="Print" formtarget="_new"/-->
                            </div>
                        </form>
                    </div>
                </div>
                <table id="table3" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Created On</th>
                            <th>Supplier</th>
                            <th>Brand Name</th>
                            <th>Status</th>
                            <th>Verification Status</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
            </div>
            <form class="form" id="brand-categories-form" action="{{URL::to('admin/catalog/brands/categories/save')}}"  style="display:none;">
                <input type="hidden" name="brand_id" id="brand_id" value="">
                <div class="panel panel-default mrT0">
                    <div class="panel-heading">
                        <h4 id="panel-title" class="panel-title col-sm-6">Brand Categories </h4>
                        <div class="text-right">
                            <button type="submit" class="btn btn-success btn-sm" title="Save"><span class="icon-save"></span></button>
                            <button type="button" class="btn btn-danger btn-sm manage-brands">&times;</button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="col-sm-12">
                            <ul class="list-unstyled" id="categories_tree" ></ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="new_brand" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 550px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">add brand</h4>
            </div>
            <div class="modal-body">
                <div id="msg"></div>
                <form name="brand_form" id="brand_form">
                    <div class="row">
                        <div class="col-md-8">
                            <label>Supplier Name:</label>
                            <select name="supplier_id"  class="form-control" id="supplier_id">
                            </select>
                            <div class="err_msg text-danger"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Brand Name<span class="danger">*</span>:</label>
                            <input type="text" id="brand_name" name="brand_name" class="form-control" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <button name="add_supplier_brand" class="btn btn-info" id="add_supplier_brand">Add Brand</button>
                            <input type="hidden" name="brand_id" id="brand_id">
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
<script src="{{asset('resources/supports/admin/products/supplier_brand_list.js')}}"></script>	
@stop
