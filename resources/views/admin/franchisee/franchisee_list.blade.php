
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
    
	<div class="col-md-12" id="users-list-panel">
        <div class="panel panel-default" id="list">
            <div class="panel-heading">

                <h4 class="panel-title">Admin Credit & Debit Report</h4>
            </div>

       
			    <div class="panel_controls">
                <div class="row">
                <form id="form" class="form form-bordered" action="{{route('admin.franchisee.manage')}}" method="post">
                        <input type="hidden" class="form-control" id="status_col"  value ="status_value">
                        <div class="input-group col-sm-3">
                             <label for="from"> {{trans('general.search')}}</label>
                                <select name="search_feild" class="form-control" id="search_feild">
                                        <option value="">All</option>
                                        @if(isset($search_feilds) && !empty($search_feilds))
                                        @foreach($search_feilds as $key=>$filed)
                                        <option value="{{$key}}">{{ucfirst($key)}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                        </div>
						 <div class="input-group col-sm-3">
                        <label for="from">{{trans('admin/finance.trans_type')}}</label>
                       <select name="franchisee_type" id="franchisee_type" class="form-control" >
                                <option value="">All</option>
                                @if(isset($franchisee_types) && !empty($franchisee_types))
                                @foreach($franchisee_types as $type)
                                <option value="{{$type->franchisee_typeid}}">{{$type->franchisee_type}}</option>
                                @endforeach
                                @endif
                            </select>
                           
                        </div>
                        <div class="col-sm-3">
						 <label for="from">{{trans('admin/general.date')}}</label>
                            <div class="input-group">
							
                                <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                 <input class="form-control" type="text" id="from" name="from" placeholder="From">
                                <span class="input-group-addon">-</span>
                                 <input class="form-control" type="text" id="to" name="to" placeholder="To">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <input  name ="submit" type="submit" class="btn btn-sm btn-primary" value="Search" />
                            <input name ="submit" type="submit" class="btn btn-sm btn-primary" value="Export" />
                            <input  name ="submit" type="submit" class="btn btn-sm btn-primary" value="Print" />
                            <button type="reset" class="btn btn-sm btn-warning"><i class="fa fa-repeat"></i> Reset</button>
                        </div>
                    </form>
                </div>
            </div>
           
            <div class="box-body table-responsive">
            
				    <table id="mange_center" class="table table-bordered table-striped" >
                    <thead>
                        <tr>
                            <th>DOR</th>
                            <th>Username</th>
                            <th>Support Center Name</th>
                            <th>Support Center Type</th>
                            <th>Country</th>
                        <!--<th>District Support Center</th>
                            <th>State Support Center</th>
                            <th>Regional Support Center</th>
                            <th>Country Support Center</th>-->
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
		


<div id="retailer-qlogin-model" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change Password</h4>
            </div>
            <div class="modal-body">
                <div id="accErr"></div>
              <div id="change_Member_pwd" style="display:none;">
                @include('admin.franchisee.change_pwd')
               
           </div>
            </div>
        </div>
    </div>
</div>

<div id="retailer-qlogin-model1" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change Pin</h4>
            </div>
            <div class="modal-body">
                <div id="accErr"></div>
              <div id="change_Member_pin" style="display:none;">
                @include('admin.franchisee.change_pin')
               
           </div>
            </div>
        </div>
    </div>
</div>

<div id="view_user_profile" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change Pin</h4>
            </div>
            <div class="modal-body">
                <div id="accErr"></div>
              <div id="change_Member_pin" style="display:none;">
                @include('admin.franchisee.change_pin')
               
           </div>
            </div>
        </div>
    </div>
</div>
</section>


@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('admin/validate/lang/change-pwd.js')}}"></script>
<script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>

<script src="{{asset('js/providers/admin/franchisee/manage_franchisee.js')}}"></script>
@stop

