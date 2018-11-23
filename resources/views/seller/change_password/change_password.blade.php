@extends('supplier.common.layout')
@section('top-nav')
@include('supplier.common.top_navigation')
@stop
@section('layoutContent')
<div id="main_content">
    <!-- main content -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">{{trans('change_password.change_password')}}</h4>
                </div>
                <div class="panel-body">
                    <fieldset class="col-sm-9">
                                                  <!--legend><span>Form horizontal</span></legend-->
                        <div id="login_mess" style="color: red; text-align: center;">  <?php echo Session::get('msg');?>  </div>
                        <form class="form-horizontal" id="changepassword" action="<?php echo URL::asset('/supplier/save_changepasswrord')?>" val="">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{trans('change_password.old_password')}}<span class="danger">*</span></label>
                                <div class="col-sm-6">
                                    <input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="{{trans('change_password.enter_your_old_password')}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{trans('change_password.new_password')}} <span class="danger">*</span></label>
                                <div class="col-sm-6 fieldgroup">
                                    <input type="password" class="form-control" id="newpassword" name="newpassword" placeholder="{{trans('change_password.enter_your_new_password')}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{trans('change_password.confirm_password')}} <span class="danger">*</span></label>
                                <div class="col-sm-6 fieldgroup">
                                    <input type="password" class="form-control" id="confirmpassword" name="confirmpassword" placeholder="{{trans('change_password.please_retype_your_password')}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3"> </label>
                                <div class="col-sm-3 fieldgroup">
                                    <button type="submit" class="btn btn-success" id="submit" name="submit" >Submit</button>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
{{ HTML::script('supports/supplier/change_pwd.js') }}
@stop
