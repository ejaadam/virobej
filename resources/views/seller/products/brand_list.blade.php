@extends('seller.common.layout')
@section('pagetitle')
{{Lang::get('general.fields.brand')}}
@stop

@section('layoutContent')
<div class="pageheader">
    <div class="row">
        <div id="alert-div"></div>
        <div class="col-sm-12">
            <div class="panel panel-default"  id="brand_list">
                <div class="panel-heading">
                    <h4 class="panel-title col-sm-6">{{Lang::get('general.fields.brand')}}</h4>
                    <button type="button" id="create_brand" class="btn btn-success btn-sm pull-right"><span class="icon-plus"></span>{{Lang::get('general.fields.create_brand')}}</button>
                </div>
                <table id="brands-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{Lang::get('general.fields.created_on')}}</th>
                            <th>{{Lang::get('general.fields.brand')}}</th>
                            <th>{{Lang::get('general.fields.status')}}</th>
                            <th>{{Lang::get('general.fields.verification_status')}}</th>
                        </tr>
                    </thead>
                    <div class="panel_controls">
                        <div class="row">
                            <form id="brand_list" name="brand_list" action="{{URL::to('supplier/brand/list')}}" method="get">
                                <div class="col-sm-3">
                                    <input type="text" name="search_term" placeholder="{{Lang::get('general.search_ph')}}" id="search_term" class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    <button id="search" type="button" class="btn btn-primary btn-sm">{{Lang::get('general.search_btn')}}</button>
                                    <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="{{Lang::get('general.export_btn')}}" formtarget="_new"/>
                                    <input name ="submit" type="submit" class="btn btn-primary btn-sm exportBtns" value="{{Lang::get('general.print_btn')}}" formtarget="_new"/>
                                </div>
                            </form>
                        </div>
                    </div>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="panel panel-default" id="add_brand_section" style="display:none">
                <div class="panel-heading">
                    <button type="button" id="close-add-brand-btn" class="btn btn-danger btn-sm pull-right"><i class="fa fa-times"></i></button>
                    <h4 class="panel-title col-sm-6">{{Lang::get('general.fields.create_brand')}}</h4>
                </div>
                <div class="panel_controls">
                    <div id="msg"></div>
                    <form name="brad_form" id="brand_form">
                        <div class="row">
                            <label class="col-md-3">{{Lang::get('general.fields.brand')}}<span class="danger">*</span>:</label>
                            <div class="col-md-9">
                                <input type="text" id="brand_name" name="brand_name" class="form-control" />
                                <div class="err_msg" class="text-danger"></div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-md-3"></label>
                            <div class="col-md-9">
                                <button class="btn btn-sm btn-primary" id="add_supplier_brand">{{Lang::get('general.save')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('seller.common.assets')
<script src="{{asset('resources/supports/supplier/brand_list.js')}}"></script>
@stop
