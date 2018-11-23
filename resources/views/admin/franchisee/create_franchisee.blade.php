@extends('admin.common.layout')
@section('title','Create Support Center')
@section('layoutContent')
<div class="panel panel-default">
    <div class="panel-heading">		
        <h4 class="panel-title">Create New Support Center</h4>
    </div>
    <div class="panel-body">
		<div id="msg"></div>
                <!-- form start -->
              
                <form action="{{route('admin.franchisee.save')}}" method="POST" class='form-horizontal form-validate' id="create_user" onsubmit="return false;">
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Support Center Type : </label>
                        <div class="col-sm-6">
                            <select name="fran_type" id="fran_type" class="form-control">
                                <option value="">Select Support Center Type</option>
                                @if(!empty($franchisee_types))
                                @foreach($franchisee_types as $franchisee)
                                <option value="{{$franchisee->franchisee_typeid}}" data-val="{{ $package[$franchisee->franchisee_typeid]['frachisee_package_amount']}}">{{$franchisee->franchisee_type}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
					   <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Country:</label>
                        <div class="col-sm-6">
                            <select name="country" class="form-control" id="user_country" data-url="{{route('admin.franchisee.states')}}">
                                <option value="">--Select Country--</option>
                                <?php foreach ($country as $row) { ?>
								<option  data-currency_ids="{{$row->currency_id}}" value="<?php echo $row->country_id;?>" <?php
								if (isset($user_details) && !empty($user_details))
									if ($user_details->country_id == $row->country_id){
										echo "selected";                                                     
									 }
									 ?>
									 ><?php echo $row->country_name;?>
								</option>
								<?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Deposited : </label>
                        <div class="col-sm-6">
                            <div><input type="radio" name="isdeposited" class="simple isdeposited" value="{{Config::get('constants.ON')}}" />Yes
                                <input type="radio" name="isdeposited" class="simple isdeposited" value="{{Config::get('constants.OFF')}}" />No  </div>
                            <div id="isdeposited_err"></div>
                        </div>
                    </div>
                    <div class="form-group" style="display:none" id="package_view_label">
                        <label for="textfield" class="control-label col-sm-2">Package : </label>
                        <div class="col-sm-6" id="package_info">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Office Available : </label>
                        <div class="col-sm-6">
                            <div><input type="radio" name="office_available" class="simple" value="{{Config::get('constants.ON')}}" />Yes
                                <input type="radio" name="office_available" class="simple" value="{{Config::get('constants.OFF')}}" />No
                            </div>
                            <div id="office_available_err"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">User Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="uname" id="uname" class="form-control"  placeholder="Enter User name" data-url="{{route('admin.franchisee.validate.username')}}" data-rule-required="true" value="" >
                            <div id="uname_status"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Email:</label>
                        <div class="col-sm-6">
                            <input type="email" name="email" id="email" class="form-control"  placeholder="Enter Email"  data-url="{{route('admin.franchisee.validate.email')}}" data-rule-required="true" value="" >
                            <div id="email_status"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Password:</label>
                        <div class="col-sm-6">
                            <input type="password" name="password" id="password" class="form-control"  placeholder="Enter Password"  value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Security Pin:</label>
                        <div class="col-sm-6">
                            <input type="password" name="tpin" id="tpin" class="form-control"  placeholder="Enter Security Pin" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Support Center Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="company_name" id="company_name" class="form-control"  placeholder="Enter Support Center name" data-rule-required="true" value="" >
                            <span><small>Company or Firm Name</small></span>
                        </div>
                    </div>
                    <!--<div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Address:</label>
                        <div class="col-sm-6">
                            <textarea name="company_address" id="company_address" class="form-control" data-rule-required="true"></textarea>
                            <span><small>Company or Firm Address</small></span>
                        </div>
                    </div>-->
					<div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Flat/Street No:</label>
                        <div class="col-sm-6">
                            <input type="text" name="cmpy_flat_no" id="cmpy_flat_no" class="form-control"  placeholder="Enter Flat/Street No" data-rule-required="true" value="" >
                        </div>
                    </div>
					<div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">LandMark:</label>
                        <div class="col-sm-6">
                            <input type="text" name="cmpy_land_mark" id="cmpy_land_mark" class="form-control"  placeholder="Enter LandMark" data-rule-required="true" value="" >
                        </div>
                    </div>
					<div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Pincode:</label>
                        <div class="col-sm-6">
                        <input type="text" name="cmpy_pincode" id="cmpy_pincode" class="form-control"  placeholder="Enter Pincode" data-rule-required="true" value="" >
                        </div>
                    </div>
                    <div class="form-group">
						<div class="col-sm-12">
                        <label><b>Contact Person Details</b></label>
						</div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">First Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="first_name" id="first_name" class="form-control"  placeholder="Enter First name" data-rule-required="true" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Last Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="last_name" id="last_name" class="form-control"  placeholder="Enter Last name" data-rule-required="true" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Sex:</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="sex" id="sex">
                                <option value="">Select Sex</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Date of birth:</label>
                        <div class="col-sm-6">
                            <input type="text" name="dob" id="dob" class="form-control"  placeholder="DOB" data-rule-required="true" value="" >
                        </div>
                    </div>                    
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Address:</label>
                        <div class="col-sm-6">
                            <input type="text" name="address" id="address" class="form-control"  placeholder="Enter address" data-rule-required="true" value="" >
                        </div>
                    </div>
                 
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">State:</label>
                        <div class="col-sm-6">
                            <select name="state" class="form-control" id="user_state" required data-url="{{route('admin.franchisee.districts')}}">
                                <option value="">--Select State--</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">District:</label>
                        <div class="col-sm-6">
                            <select name="district" class="form-control" id="user_district" required data-url="{{route('admin.franchisee.cities')}}">
                                <option value="">--Select District--</option>
                            </select>
                            <br />
                            <input type="text" style="display:none" class="form-control" name="user_district_others" id="user_district_others" placeholder = "Enter District Name" />
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Zip/Pin Code:</label>
                        <div class="col-sm-6">
                            <input type="text" name="zipcode" id="zipcode" class="form-control"  placeholder="Enter Zip/Pin Code"   required data-url="{{route('admin.franchisee.zipcode')}}">
                        </div>
						
                    </div>
					<div class="form-group city">
                        <label for="textfield" class="control-label col-sm-2">Locality:</label>
                        <div class="col-sm-6">
                            <select name="city" class="form-control" id="user_city" required>
                                <option value="">--Select Locality--</option>
                            </select><br />
                            <input type="text" style="display:none" class="form-control" name="user_city_others" id="user_city_others" placeholder = "Enter City Name" />
                        </div>
                    </div>
					 <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Currency:</label>
                        <div class="col-sm-6">
                            <select  name="currency" id="currency" class="form-control" readonly>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Mobile:</label>
                        <div class="col-sm-6">
                            <select  name="phonecode" id="phonecode" readonly class="form-control"  style="width:20%; float:left; display:inline-block">
                                <option value=""></option>
                            </select>
                            <input type="text" name="mobile" id="mobile" class="form-control" maxlength="16"  placeholder="Enter Mobile" style="width:50%" data-rule-required="true" value="<?php if (isset($user_details) && !empty($user_details)) echo $user_details->mobile;?>" >
                            <div id="mobile_status"></div>
                        </div>
                    </div>
					
          
					<span id="error"></span>
                   
                    <input type="hidden" id="status"   class='icheck-me' name="status" data-skin="square" data-color="blue"
                           value="<?php echo Config::get('constants.ACTIVE');?>"  >
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">&nbsp;</label>
                        <div class="col-sm-6" >
                            <input type="submit" name="save_user" id="save_user" class="btn btn-primary" value="Save">
                        </div>
                    </div>
                </form>
                <!-- end create User details -->
                <!-- Franchisee access details -->
                <div id="access_form" style="display:none">
                    <br />
                    <div id="franchisee_status"></div>
                    <br />
                    <form action="{{route('admin.franchisee.access.save')}}" method="POST" class='form-horizontal form-validate' id="franchisee_access"  onsubmit="return false;">
                        <div class="form-group">
                            <label for="textfield" class="control-label col-sm-2">Support Center Type:</label>
                            <div class="col-sm-6">
                                <h5><div id="franchi_name"></div></h5>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="textfield" class="control-label col-sm-2">Support Center Username:</label>
                            <div class="col-sm-6">
                                <h5><div id="franchi_uname"></div></h5>
                            </div>
                        </div>
                        <div class="form-group country"  style="display:none">
                            <label for="textfield" class="control-label col-sm-2">Country:</label>
                            <div class="col-sm-6">
                                <input type="hidden" name="franchi_type" id="franchi_type" value="">
                                <input type="hidden" name="user_id" id="user_id" value="">
                                <input type="hidden" name="pwd" id="pwd" value="">
                                <input type="hidden" name="tpin" id="tpin1" value="">
                                <select name="country" class="form-control" id="country" required data-url="{{route('admin.franchisee.acc.state')}}">
                                    <option value="">--Select Country--</option>
                                    <?php
                                    foreach ($country as $row)
                                    {
                                        ?>
                                        <option value="<?php echo $row->country_id;?>" <?php
                                        if (isset($user_details) && !empty($user_details))
                                            if ($user_details->country == $row->country_id)
                                            {
                                                ?>
                                                        selected
                                                        <?php
                                                    }
                                                ?>><?php echo $row->country_name;?></option>
                                                <?php
                                            }
                                            ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group region1" style="display:none">
                            <label for="textfield" class="control-label col-sm-2">Region:</label>
                            <div class="col-sm-6">
                                <select name="region" class="form-control" id="region"  required>
                                    <option value="">--Select Region--</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group state" style="display:none">
                            <label for="textfield" class="control-label col-sm-2">State:</label>
                            <div class="col-sm-6">
                                <select name="state" class="form-control" id="state" required data-url="{{route('admin.franchisee.acc.district')}}">
                                    <option value="">--Select State--</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group union_territory" style="display:none">
                            <label for="textfield" class="control-label col-sm-2">Union Territory:</label>
                            <div class="col-sm-6">
                                <select name="union_territory[]" class="form-control" id="union_territory" required multiple="multiple" >
                                </select>
                            </div>
                        </div>
                        <div class="form-group district"  style="display:none">
                            <label for="textfield" class="control-label col-sm-2">District:</label>
                            <div class="col-sm-6">
                                <select name="district" class="form-control" id="district" required data-url="{{route('admin.franchisee.acc.city')}}">
                                    <option value="">--District--</option>
                                </select>
                                <br />
                                <input type="text" style="display:none" class="form-control" name="district_others" id="district_others" placeholder = "Enter District Name" />
                            </div>
                        </div>
                        <div class="form-group city"  style="display:none">
                            <label for="textfield" class="control-label col-sm-2">City:</label>
                            <div class="col-sm-6">
                                <select name="city" class="form-control" id="city" required>
                                    <option value="">--City--</option>
                                </select>
                                <br />
                                <input type="text" style="display:none" class="form-control" name="city_others" id="city_others" placeholder = "Enter City Name" />
                            </div>
                        </div> 
                        <div id="franchisee_mapped_user">
                        </div>
                        <input type="hidden" id="status1"   class='icheck-me' name="status" data-skin="square" data-color="blue"
                               value="<?php echo Config::get('constants.ACTIVE');?>"  >
                        <div class="form-group">
                            <label for="textfield" class="control-label col-sm-2">&nbsp;</label>
                            <div class="col-sm-6" >
                                <input type="submit" name="submit_access" id="submit_access" class="btn btn-primary" value="Save">
                            </div>
                        </div>
                    </form>
                </div>
	</div>
</div>
@stop
@section('scripts')
<script src="{{asset('js/providers/admin/franchisee/register.js')}}" type="text/javascript" charset="utf-8"></script>
@stop