@extends('supplier.common.layout')
@section('pagetitle')
Ratings and Reviews List
@stop
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')
<div id="stores_list">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default" id="list">
                <div class="panel-heading">
                    <h4 class="panel-title">{{trans('stores_list.page_title')}} </h4>
                </div>
                <div class="panel_controls">
                    <div class="row">
                        <form id="reviews_list_form" method="post">
                            <div class="col-sm-3">
                                <input type="text" id="search_text" name="search_text" class="form-control">
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group date ebro_datepicker" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                    <input class="form-control" type="text" id="start_date" name="start_date" placeholder="From Date">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span> </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group date ebro_datepicker" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                    <input class="form-control" type="text" id="end_date" name="end_date" placeholder="To Date">
                                    <span class="input-group-addon"><i class="icon-calendar"></i></span> </div>
                            </div>
                            <div class="col-sm-3">
                                <button id="search" type="button" class="btn btn-primary btn-sm">{{trans('general.search_btn')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="msg"></div>
                <table id="reviews_list" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Reviewed On</th>
                            <th>Reviewed By</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Ratings</th>
                            <th>Likes</th>
                            <th>Unlikes</th>
                            <th>Verification Status</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('admin.common.assets')
@stop
@section('scripts')
{{ HTML::script('supports/supplier/ratings_list.js') }}
{{HTML::Script('supports/admin/meta-info.js')}}
@stop
