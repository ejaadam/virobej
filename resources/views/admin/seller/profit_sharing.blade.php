@extends('admin.common.layout')
@section('pagetitle')
Suppliers List
@stop
@section('top_navigation')
@include('admin.top_nav.supplier_navigation')
@stop
@section('layoutContent')
<div class="row" id="list-panel">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Commision List </h4>
            </div>
            <div class="panel_controls" >
                <div class="row">
                    <form id="listfrm" action="{{url()->current()}}" class="form form-bordered" method="post">             
                        <div class="col-sm-2">							
							<input type="search" id="search_text" name="search_text" class="form-control" placeholder="Search Term" value="{{(isset($search_text) && $search_text != '') ? $search_text : ''}}" />
						</div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                <input class="form-control" type="text" id="from" name="from_date" placeholder="From">
                                <span class="input-group-addon">-</span>
                                <input class="form-control" type="text" id="to" name="to_date" placeholder="To">
                            </div>
                        </div>
						<div class="col-sm-2">
							<div class="input-group">								
								<select class="form-control" name="status" id="status">
									<option value="0">Pending</option>
									<option value="1">Accept</option>
									<option value="2">Rejected</option>
									<option value="3">Closed</option>
								</select>
							</div>
						</div>
                        <div class="col-sm-4">                               
							<button type="button" id="searchbtn" class="btn btn-primary btn-sm "><i class="fa fa-search"></i>Search</button>&nbsp;
							<button type="button" id="resetbtn" class="btn btn-primary btn-sm"><i class="fa fa-repeat"></i>Reset</button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="msg"></div>
            <table id="listtbl" class="table table-bordered table-striped" >
                <thead>
                    <tr>
                        <th>Created On</th>
                        <th >Sellers</th>
                        <th>Commisions</th>
                        <th>Status</th>
                        <th>Updated On</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>        
    </div>
</div>
<div id="form-panel" style="display:none;">
    @include('admin.seller.profit_sharing_form')
</div>
<div id="view-panel" style="display:none;">
    @include('admin.seller.profit_sharing_view')
</div>
@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('resources/supports/admin/seller/profit_sharing.js')}}"></script>		
@stop
