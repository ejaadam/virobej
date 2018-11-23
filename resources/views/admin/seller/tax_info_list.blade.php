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
            <div class="panel-heading">Seller Tax Information

                <h4 class="panel-title"></h4>
            </div>
			    <div class="panel_controls">
                <div class="row">
                <form id="proof_documents_details" class="form form-bordered" action="{{URL::to('admin/seller/tax-info')}}" method="post">
                        <input type="hidden" class="form-control" id="status_col"  value ="status_value">
                        <div class="input-group col-sm-3">
						<label for="from">Search</label>
                                 <input class="form-control" type="text" id="search_term" name="search_term" placeholder="Search">
                            </div>
					   <div class="input-group col-sm-2">
                        <label for="from">Document Types</label>
                        <select  name="type_filer"  id="type_filer" class="form-control">
						  <option value="">All</option>
                                </select>
                           
                        </div>
						<div class="input-group col-sm-2">
                        <label for="from">Status</label>
                          <select  name="status"  id="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="0" >PENDING</option>
                                    <option value="1" >APPROVED</option>
                                    <option value="2" >REJECTED</option>
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
                      	<div class="input-group col-sm-2">
						 <label for="from">&nbsp;</label>
                            <button id="search" type="button" class="btn btn-primary btn-sm">Search</button>
                           
                        </div>
                    </form>
                </div>
            </div>
           
            <div class="box-body table-responsive">
            
				    <table id="proof_verification_details" class="table table-bordered table-striped" >
                    <thead>
                     <tr>
                            <th>Created On</th>
                            <th>Name</th>
                            <th>Documents</th>
                            <th>Status</th>
                            <th>Updated On</th> 
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
	
	<div id="view_user_profile" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div id="accErr"></div>
              
            </div>
        </div>
    </div>
</div>

</section>


@include('admin.common.assets')
@stop
@section('scripts')
<script src="{{asset('resources/assets/admin/js/date_format.js')}}"></script>
<script src="{{asset('resources/supports/admin/seller/tax_information.js')}}"></script>	
<script src="{{asset('resources/supports/admin/seller/proof_status.js')}}"></script>	
@stop

