@extends('supplier.common.layout')
@section('pagetitle')
Customer Management
@stop
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')
<div id="customer_management_block">
    <div class="pageheader">
        <div class="row">
            <div id="alert-msg" class="alert-msg"></div>
            <div class="col-sm-12">
                <div class="panel panel-default" id="list">
                    <div class="panel-heading">
                        <h4 class="panel-title col-sm-6">Customer List</h4>
                        <button id="new_zone" class="btn btn-success btn-sm pull-right "><span class="icon-plus"></span>Create Customer</button>

                    </div>
                    <div class="panel_controls">
                        <div class="row">
                            <form id="customer_list_form">
                                <div class="col-sm-3">
                                    <input type="text" name="search_term" placeholder="{{Lang::get('general.search_terms')}} " id="search_term" class="form-control">
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group date ebro_datepicker col-sm-6 pdL" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                        <input class="form-control" type="text" id="from" name="from" placeholder="From">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                    </div>
                                    <div class="input-group date ebro_datepicker col-sm-6 pdR" data-date-format="dd-mm-yyyy" data-date-autoclose="true">
                                        <input class="form-control" type="text" id="to" name="to" placeholder="To">
                                        <span class="input-group-addon"><i class="icon-calendar"></i></span>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <button id="search" type="button" class="btn btn-primary btn-sm">{{Lang::get('general.search_btn')}}</button>
                                </div>

                            </form>
                        </div>
                    </div>
                    <div id="successmsg"></div>
                    <table id="customer_list" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Created On</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Last Login</th>
                                <th>Account status</th>
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
</div>
<div id="reset_pwd_block" style="display:none">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <button id="close_zone" class="btn btn-danger btn-sm pull-right">x</button>
                <h4 class="panel-title">Reset Password</h4>
            </div>
            <div class="panel_controls">
                <form name="reset_pwd" id="reset_pwd" class="form-horizontal"  action="{{URL::to('supplier/customer/reset-pwd')}}">
                    <div id="msg"></div>
                    <div class="row">
                        <label class="col-md-3">Enter New password<span class="error">*</span></label>
                        <div class="col-md-9">
                            <input type="password" name="enter_new_pwd" class="form-control" id="enter_new_pwd">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-md-3">Confirm New password<span class="error">*</span></label>
                        <div class="col-md-9">
                            <input type="password" name="con_new_pwd" class="form-control" id="con_new_pwd">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-md-3"></label>
                        <div class="col-md-9">
                            <input type="submit" class="btn btn-primary" value="{{Lang::get('general.save')}}" />
                            <!--<button id="cancel_btn" class="btn btn-danger">Cancel</button>!-->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop
@section('scripts')
{{HTML::script('supports/jquery.form.js')}}
{{ HTML::script('supports/supplier/customer_management_list.js') }}

@stop
