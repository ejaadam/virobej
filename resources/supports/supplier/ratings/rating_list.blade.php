@extends('supplier.layouts.dashboard')
@section('layoutContent')
<style>
.info {    
    height:71px;
    border-left:0px ! important;
}
.align{
    padding-top:25px ! important;
    border-right:0px ! important;
    border-left:1px solid #CCC ! important;
}
.panel-heading {
   line-height: 2.5;
  }
</style>
<div class="contentpanel">
    <div class="col-sm-12">
        <div id="successmsg"></div>
        <div class="panel panel-default">
            <div class="panel-heading"> <span id="groups"></span>
                <h4 class="panel-title">Product Ratings</h4>
            </div>
            <?php
            if (Session::has('msg'))
            {
                echo Session::get('msg');
            }
            ?>
            <form class="form-horizontal form-bordered" name="rating_list" id="rating_lists" action="" method="post" >
                <div class="panel_controls">
                    <div class="row  row-pad-5">
                        <div class="col-sm-3">
                            <input type="text" id="search_term" name="search_term" placeholder="Search Term" class="form-control" />
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="mm/dd/yyyy" id="from_date">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="mm/dd/yyyy" id="to_date">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-sm-2 ">
                            <button type="button" class="btn btn-success btn-sm" name="search" id="search">Search</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="panel-body">
            <div id="msg"></div>
                <table id="rating_list" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th nowrap="nowrap" >Created On</th>
                            <th nowrap="nowrap" width="10%">Member Name</th>
                            <th>Product Code</th>
                            <th nowrap="nowrap" width="30%">Review Title</th>
                             <th>Ratings</th>
                            <th>Updated On</th>
                            <th>status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="edit_data1" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" style="width: 4
         50px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"> Rating Product Details</h4>
            </div>
            <div class="modal-body"> </div>
        </div>
    </div>
</div>
{{ HTML::script('supports/supplier/rating/rating_list.js') }}
	<script>
   $(document).ready(function () {
    $('#search').trigger('click');
	 $('#start_date').val('');
	 $("#end_date").val('');
	 $("#search_term").val(''); 
	 });
</script>
@stop 