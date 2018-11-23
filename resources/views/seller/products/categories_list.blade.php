@extends('seller.common.layout')
@section('pagetitle')
{{Lang::get('product_category_list.page_title')}}
@stop

@section('layoutContent')
<div id="categories_list">
    <div class="pageheader">
        <div class="row">
            <div id="message"></div>
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title col-sm-6">{{Lang::get('product_category_list.page_head')}}</h4>
                        <button type="button" id="add_category_btn" class="btn btn-success btn-sm pull-right mR10"><span class="icon-plus"></span>{{Lang::get('general.category.add')}}</button>
                    </div>
                    <table id="table3" class="table table-striped">
                        <thead>
                            <tr>
                                <th width="20%">{{Lang::get('product_category_list.created_on')}}</th>
                                <th width="40%">{{Lang::get('product_category_list.category_name')}}</th>
                                <th width="20%">{{Lang::get('product_category_list.parent_category')}}</th>
                                <th width="5%">{{Lang::get('product_category_list.status')}}</th>
                            </tr>
                        </thead>
                        <div class="panel_controls">
                            <div class="row">
                                <form id="category_list" name="category_list" action="{{URL::to('supplier/category/product_categories')}}" method="post">
                                    <div class="col-sm-3">
                                        <input type="text" name="search_term" placeholder="{{Lang::get('general.search_ph')}}" id="search_term" class="form-control">
                                    </div>
                                    <div class="col-sm-3">
                                        <button id="search" type="button" class="btn btn-primary btn-sm">{{Lang::get('general.search_btn')}}</button>
                                        <!--input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="{{Lang::get('general.export_btn')}}" formtarget="_new"/>
                                        <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="{{Lang::get('general.print_btn')}}" formtarget="_new"/-->
                                    </div>
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
<div id="add_category_section"  style="display:none">
    <div class="pageheader">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <button id="close_category_list" class="btn btn-danger btn-sm pull-right">x</button>
                        <h4 class="panel-title col-sm-6">{{Lang::get('general.category.add')}}</h4>
                    </div>
                    <div id="msg"></div>
                    <div class="panel_controls">
                        <form name="add_existing_categories" id="add_existing_categories" action="{{URL::route('api.v1.seller.catalog.categories.add')}}">
                            <input type="hidden" name="supplier_id" id="supplier_id" value="{{$supplier_id or ''}}">
                            <div class="row">
                                <label class="col-md-3">{{Lang::get('product_category_list.category_name_label')}}<span class="error">*</span>:</label>
                                <div class="col-md-9">
                                    <select name="category_id" class="form-control" id="category_id"></select>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-3"></label>
                                <div class="col-md-9">
                                    <input type="submit" class="btn btn-info push-left" value="{{Lang::get('common.save')}}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('seller.common.assets')
<script src="{{asset('resources/supports/supplier/categories_list.js')}}"></script>
@stop
