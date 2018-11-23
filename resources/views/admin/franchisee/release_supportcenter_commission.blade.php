@extends('admin.common.layout')
@section('title','Debit Funds')
@section('layoutContent')
<section class="content">
    <!--Main row -->
    <div class = "row">
        <!--Left col -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Release Franchisee Commission</h3>
            </div><!-- /.box-header -->
            <!-- form start -->
            <form method="POST" class='form-horizontal form-validate' name="commission_release" id="commission_release"  enctype="multipart/form-data" >
                <div class="form-group">
                    <label for="username" class="control-label col-sm-2">Support Center User Name:</label>
                    <div class="col-sm-6">
                        <input type="text" name="username" id="username" class="form-control"  placeholder="Enter User name" data-rule-required="true" value="" >
                        <input type="hidden" name="user_id" id="user_id"/>
                        <span id="user_avail_status"></span>
                    </div>
                </div>
                <div class="form-group" id="uname_check">
                    <label for="textfield" class="control-label col-sm-2">&nbsp;</label>
                    <div class="col-sm-6">
                        <input type="button" name="uname_check" id="fr_uname_check" class="btn btn-primary" value="CHECK" data-url="{{URL::to('admin/check_supportcenter_username_details')}}" />
                    </div>
                </div>
                <div id="user_details" style="display:none">
                	 
                    
                    <div class="form-group">
                        <label for="username" class="control-label col-sm-2">Full Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="rec_name" id="rec_name" class="form-control" disabled/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label col-sm-2">Email:</label>
                        <div class="col-sm-6">
                            <input type="text" name="rec_email" id="rec_email" class="form-control" disabled/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Support Center Type:</label>
                        <div class="col-sm-6">
                            <input type="text" name="franchisee_type_name" id="franchisee_type_name" class="form-control" disabled/>
                            <input type="hidden" name="franchisee_type" id="franchisee_type"/>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username" class="control-label col-sm-2">Commission Type:</label>
                        <div class="col-sm-6">
                           <select name="commission_type" id="commission_type" class="form-control" data-url="{{URL::to('admin/check_supportcenter_comm_details')}}">
                           <option value=""> -Select Commission Type-</option>
                           <option value="2,3,4" >Center PG Commission</option>
                           <option value="7,8,9,10" >Center Root Id Incentive</option>
                           </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">For Month:</label>
                        <div class="col-sm-6">
                            <select name="for_month" id="for_month" class="form-control">  
                            <option value=""> -Select Month- </option>
                            </select>
                        </div>
                    </div>
                    
                     <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">For Year:</label>
                        <div class="col-sm-6">
                            <select name="for_year" id="for_year" class="form-control"> 
                            <option value=""> -Select Year- </option>                          
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2">&nbsp;</label>
                        <div class="col-sm-6">
                            <input type="submit" id="Submit" class="btn btn-primary" value="Submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section> 
{{HTML::script('js/providers/admin/franchisee/franchisee/release_commission.js')}}
{{HTML::script('js/providers/admin/franchisee/other_functionalities.js')}}
@stop
