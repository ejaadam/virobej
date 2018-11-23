@extends('admin.common.layout')
@section('title')
| Create User
@stop
@section('layoutContent')

<script src="<?php echo URL::asset('/js/providers/admin/franchisee/add_franchisee.js');?>" type="text/javascript" charset="utf-8"></script>
</script>
<section class="content">
    <!--Main row -->
    <div class="row">
        <!--Left col -->
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Create New Franchisee</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
			<div id="msg"></div>
                <!-- form start -->
                <form action="<?php echo URL::to('/admin/save_franchisee');?>" method="POST" class='form-horizontal form-validate' id="create_user"  enctype="multipart/form-data">
				 <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Franchisee Type : </label>
                       <div class="col-sm-6">
						<select name="fran_type" id="fran_type" class="form-control">
						   <option value="">Select Franchisee Type</option>
						   @if(!empty($franchisee_types))
							   @foreach($franchisee_types as $franchisee)
						       <option value="{{$franchisee->franchisee_typeid}}">{{$franchisee->franchisee_type}}</option> 
						   @endforeach
						   @endif
                         </select>   
                       </div>
                    </div>
				   <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">User Name:</label>
                        <div class="col-sm-6">
                            <input type="text" name="uname" id="uname" class="form-control"  placeholder="Enter User name" data-rule-required="true" value="" >
							<div id="uname_status"></div>
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
                        <label for="textfield" class="control-label col-sm-2">Email:</label>
                        <div class="col-sm-6">
                            <input type="email" name="email" id="email" class="form-control"  placeholder="Enter Email" data-rule-required="true" value="" >
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
                        <label for="textfield" class="control-label col-sm-2">Security Password:</label>
                        <div class="col-sm-6">
                            <input type="password" name="tpin" id="tpin" class="form-control"  placeholder="Enter Security Password" value="" >
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
				
                 
                  <!-- <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Phone Code:</label>
                         <div class="col-sm-6">
                            <input type="text" name="phonecode" id="phonecode" class="form-control" maxlength="10"  placeholder="Enter phonecode" data-rule-required="true" value="" >
                         </div>
                    </div>
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Mobile:</label>
                        <div class="col-sm-6">
                            <input type="text" name="mobile" id="mobile" class="form-control" maxlength="10"  placeholder="Enter Mobile" data-rule-required="true" value="" >
                        </div>
                    </div>-->
                    <div class="form-group">
                        <input type="hidden" id="currency" name="currency"  value="1" />
                    </div>
					
     				<div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Country:</label>
                        <div class="col-sm-6">
                            <select name="country" class="form-control" id="user_country">
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
                                            ?>><?php echo $row->name;?></option>
                                            <?php
                                        }
                                        ?>
                            </select>
                        </div>
                    </div>
					 <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">State:</label>
                        <div class="col-sm-6">
                            @if(!empty($states))
								<select name="state" class="form-control" id="user_state" required>
									<option value="">--Select State--</option>
									@foreach($states as $row)
									<option value="{{ $row->state_id}}" class="c_{{$row->country_id}}" >{{ $row->name }}</option>
									@endforeach
								</select>
								@endif
                        </div>
                    </div>
					 <div class="form-group city">
                        <label for="textfield" class="control-label col-sm-2">City:</label>
                        <div class="col-sm-6">
                        	<select name="city" class="form-control" id="user_city" required>
									<option value="">--City--</option>
									@foreach($citys as $city)
									<option value="{{ $city->city_id}}" class="c_{{$city->district_id}}" >{{ $city->city_name }}</option>
									@endforeach
								</select>
                         </div>
                     </div>
					 <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Mobile:</label>
                        <div class="col-sm-6">
                            <select  name="phonecode" id="phonecode" class="form-control"  style="width:20%; float:left; display:inline-block">
                            <option value=""></option>
                            @if (!empty($country))
                            @foreach ($country as $row)
                            <option id="c_<?php echo $row->country_id;?>" value="<?php echo $row->phonecode;?>" ><?php echo $row->phonecode;?></option>
                            @endforeach
                            @endif
                        </select>
                       
                            <input type="text" name="mobile" id="mobile" class="form-control" maxlength="16"  placeholder="Enter Mobile" style="width:50%" data-rule-required="true" value="<?php if (isset($user_details) && !empty($user_details)) echo $user_details->mobile;?>" >
                        </div>
                    </div>
			      
                       <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Address:</label>
                        <div class="col-sm-6">
                            <input type="text" name="address" id="address" class="form-control"  placeholder="Enter address" data-rule-required="true" value="" >
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
			<!-- end create User details -->
			
			<!-- Franchisee access details -->
			
			<div id="access_form" style="display:none">
			<form action="<?php echo URL::to('admin/franchisee_access'); ?>" method="POST" class='form-horizontal form-validate' id="franchisee_access"  enctype="multipart/form-data" >
			
			          <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Franchisee Type:</label>
                        <div class="col-sm-6">
					    	<h5><div id="franchi_name"></div></h5>
				        </div>
                     </div>
					 <div class="form-group">
                        <label for="textfield" class="control-label col-sm-2">Franchisee Username:</label>
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
						    <input type="hidden" name="tpin" id="tpin" value="">
                             <select name="country" class="form-control" id="country" required>
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
                                            ?>><?php echo $row->name;?></option>
                                            <?php
                                        }
                                        ?>
                            </select>
                        </div>
                    </div>
					 <div class="form-group region" style="display:none">
                        <label for="textfield" class="control-label col-sm-2">Region:</label>
                        <div class="col-sm-6">
                            @if(!empty($states))
								<select name="region" class="form-control" id="region"  required>
									<option value="">--Select Region--</option>
									@foreach($states as $row)
									<option value="{{ $row->state_id}}" class="c_{{$row->country_id}}" >{{ $row->name }}</option>
									@endforeach
								</select>
								@endif
                        </div>
                    </div>
			        <div class="form-group state" style="display:none">
                        <label for="textfield" class="control-label col-sm-2">State:</label>
                        <div class="col-sm-6">
                            @if(!empty($states))
								<select name="state" class="form-control" id="state" required>
									<option value="">--Select State--</option>
									@foreach($states as $row)
									<option value="{{ $row->state_id}}" class="c_{{$row->country_id}}" >{{ $row->name }}</option>
									@endforeach
								</select>
								@endif
                        </div>
                    </div>
					<div class="form-group district"  style="display:none">
                        <label for="textfield" class="control-label col-sm-2">District:</label>
                        <div class="col-sm-6">
						<select name="district" class="form-control" id="district" required>
									<option value="">--District--</option>
									@foreach($districts as $district)
									<option value="{{ $district->district_id}}" class="c_{{$district->state_id}}" >{{ $district->district_name }}</option>
									@endforeach
								</select>
                           
                        </div>
                    </div>
					 <div class="form-group city"  style="display:none">
                        <label for="textfield" class="control-label col-sm-2">City:</label>
                        <div class="col-sm-6">
						<select name="city" class="form-control" id="city" required>
									<option value="">--City--</option>
									@foreach($citys as $city)
									<option value="{{ $city->city_id}}" class="c_{{$city->district_id}}" >{{ $city->city_name }}</option>
									@endforeach
								</select>
                        </div>
                    </div>
                  
                      
                    <input type="hidden" id="status"   class='icheck-me' name="status" data-skin="square" data-color="blue"
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

        </div><!-- /.box -->
    </div><!--/.row (main row) -->
</section>
<style type="text/css">
    .help-block{
        color:#f56954;
    }
</style>
<script>
$(document).ready(function(){
    $("#user_country").change(function () {
        var country_id = $("#user_country").val();
        $("#user_state").val('');
		$("#user_city").val('');
        $("#user_state option").attr('hidden', true).attr('disabled', true);
        $('#user_state option[value=""]').removeAttr('hidden').removeAttr('disabled').attr('selected', true);
        $("#user_state .c_" + country_id).removeAttr('hidden').removeAttr('disabled');
         $("#phonecode").val($("#phonecode #c_" + country_id).text()); 
    });
	
    $("#franchisee_access").on('change','#country',function() {
        var country_id = $("#country").val();
        $("#franchisee_access #state").val('');
		$("#district").val('');
		$("#city").val('');
        $("#franchisee_access #state option").attr('hidden', true).attr('disabled', true);
        $('#franchisee_access #state option[value=""]').removeAttr('hidden').removeAttr('disabled').attr('selected', true);
        $("#franchisee_access #state .c_" + country_id).removeAttr('hidden').removeAttr('disabled');
    }); 
	
		/* district */
	 $("#franchisee_access").on('change','#state',function () {
        var state_id = $("#state").val();
	    $("#district").val('');
		$("#city").val('');
        $("#district option").attr('hidden', true).attr('disabled', true);
        $('#district option[value=""]').removeAttr('hidden').removeAttr('disabled').attr('selected', true);
        $("#district .c_" + state_id).removeAttr('hidden').removeAttr('disabled');
    });
	
	/* city */
	$("#franchisee_access").on('change','#district',function () {
        var district_id = $("#district").val();
	    $("#city").val('');
        $("#city option").attr('hidden', true).attr('disabled', true);
        $('#city option[value=""]').removeAttr('hidden').removeAttr('disabled').attr('selected', true);
        $("#city .c_" + district_id).removeAttr('hidden').removeAttr('disabled');
    });
	
	$('#email').change(function(){
	    var email = $(this).val();
         $.ajax({
			 dataType : "json",
			 type     : "post",
			 data     : {email:email},
			 url      : '<?php echo URL::to('admin/franchisee_check_email'); ?>',
			 success:function(data){
				 if(data.status =='ok'){
					$('#email_status').html(data.msg);
					$('#save_user').show();
				 }else if(data.status =='error'){
					$('#email_status').html(data.msg);
					$('#save_user').hide();
				 }
			 }
		 })		
	})
	$('#uname').change(function(){
	    var uname = $(this).val();
         $.ajax({
			 dataType : "json",
			 type     : "post",
			 data     : {uname:uname},
			 url      : '<?php echo URL::to('admin/franchisee_check_username'); ?>',
			 success:function(data){
				 if(data.status =='ok'){
					$('#uname_status').html(data.msg);
				 }else if(data.status =='error'){
					$('#uname_status').html(data.msg);
					$('#save_user').hide();
				 }
			 }
		 })		
	})
			   var  dt = new Date();
					dt.setFullYear(new Date().getFullYear()-18);

					$('#dob').datepicker(
						{
							viewMode: "years",
							endDate : dt
						}
					);
})	
</script>
@stop
