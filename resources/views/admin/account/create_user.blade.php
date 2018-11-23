@extends('admin.common.layout')
@section('title',trans('admin/general.add_new_route'))
@section('top_navigation')
@include('admin.top_nav.supplier_navigation')
@stop
@section('layoutContent')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>{{trans('admin/general.add_new_route')}}</h1>
    </section>
    <!-- Main content -->
<section class="content">
    <!--Main row -->
    <div class = "row">
        <!--Left col -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"> </h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <!-- form start -->
                <form action="{{route('admin.aff.root-account.save')}}" method="POST" class='form-horizontal form-validate' id="create_user"  enctype="multipart/form-data">
                  <input type="hidden" name="user_role" id="user_role" value="" />
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">First Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="first_name" id="first_name" class="form-control"  placeholder="Enter First name" data-rule-required="true" value="">
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Last Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="last_name" id="last_name" class="form-control"  placeholder="Enter Last name" data-rule-required="true" value="">
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">User Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="uname" id="uname" data-url="{{URL::to('admin/affiliate/check-uname')}}" class="form-control"  placeholder="Enter User name" data-rule-required="true" value="" >
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Email:</label>
                        <div class="col-sm-6">
                            <input type="email" name="email" id="email" class="form-control" data-url="{{URL::to('admin/affiliate/user_email_check')}}"   placeholder="Enter Email" data-rule-required="true" value="" >
                        </div>
                    </div>
					
                    <div class="form-group">
                        <input type="hidden" id="currency" name="currency"  value="1" />
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Password:</label>
                        <div class="col-sm-6">
                            <input type="password" name="password" id="password" class="form-control"  placeholder="Enter Password" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Confirm Password:</label>
                        <div class="col-sm-6">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control"  placeholder="Enter Confirm Password"  value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Gender:</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="gender" id="gender">
                                <option value="">Select Sex</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                        </div>
                    </div>
                   <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Date of birth:</label>
                        <div class="col-sm-6">
                            <input type="text" name="dob" id="dob" class="form-control datepicker"  placeholder="DOB"   value="">
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Country:</label>
                        <div class="col-sm-6">
                             <select name="country"   class="form-control" id="country_id">
			   <option value="">Select Country</option>
				@if(!empty($countries))
                   @foreach ($countries as $country_val)
                   <option value="{{$country_val->iso2}}">{{$country_val->country_name}}</option>
                   @endforeach
                   @endif
				  </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Mobile:</label>
                        <div class="col-sm-6">
                         <input type="text" name="mobile" id="mobile" data-url="{{URL::to('admin/affiliate/user_mobile_check')}}"  class="form-control" maxlength="16"  placeholder="Enter Mobile"  data-rule-required="true" value="">
                        </div>
                    </div>
					
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Zip/Pin Code:</label>
                        <div class="col-sm-6">
                            <input type="text" name="zipcode" id="zipcode" class="form-control"  placeholder="Enter Zip/Pin Code" data-rule-required="true" >
                        </div>
                    </div>
                   
                    <input type="hidden" id="status"   class='icheck-me' name="status" data-skin="square" data-color="blue"
                           value="<?php echo Config::get('constants.ACTIVE');?>"  >
						   
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">&nbsp;</label>
                        <div class="col-sm-6" >
                            <input type="submit" name="save_user" id="save_user" class="btn btn-primary" value="Save">
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.box -->
    </div><!--/.row (main row) -->
</section>
    <!-- /.content -->
@stop
@section('scripts')
@include('admin.common.datatable_js')
<script src="{{asset('js/providers/admin/account/create_user.js')}}"></script>  

@stop