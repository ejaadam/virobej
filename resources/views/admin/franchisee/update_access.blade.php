	  				<div id="franchisee_status" style="margin:5px;"></div>                    
			       <form action="<?php echo URL::to('admin/add_access');?>" method="POST" class='form-horizontal form-validate' id="franchisee_access"  enctype="multipart/form-data" >
                   <div class="col-lg-4">
				   <div class="form-group fld">
                        <label for="textfield" class=" col-md-12">Username: </label>
                        <div class="col-md-12">
					    	<div id="username">{{!empty($uname)?$uname:''}}</div>
				        </div>
                     </div>
                     </div>
                     
                     <div class="col-lg-4">
					 <div class="form-group fld">
                        <label for="textfield" class="col-sm-12">Email: </label>
                        <div class="col-sm-12">
					    	<div id="email">{{!empty($email)?$email:''}}</div>
				        </div>
                     </div>
                     </div>
                     
                     <div class="col-lg-4">
					 <div class="form-group fld">
                        <label for="textfield" class="col-sm-12">Support Center Type: </label>
                        <div class="col-sm-12">
					    	<div id="franchi_typename">{{!empty($franchisee_typename)?$franchisee_typename:''}}</div>
				        </div>
                     </div>
                     </div>
                     
                     <div class="col-lg-4">                     
                      <div class="form-group fld">
                        <label for="textfield" class="col-sm-12">Country Support Center: </label>
                        <div class="col-sm-12">
					    	<div id="franchi_typename">
                            	@if(isset($franchisee_details->country_frname) && !empty($franchisee_details->country_frname) )
                         		{{ $franchisee_details->country_frname }}
                                @elseif(isset($franchisee_details->country_frname1) && !empty($franchisee_details->country_frname1))
                                    {{$franchisee_details->country_frname1}}
                                @elseif(isset($franchisee_details->country_frname3) && !empty($franchisee_details->country_frname2))
                                     {{$franchisee_details->country_frname2}} 
                                @elseif(isset($franchisee_details->country_frname3) && !empty($franchisee_details->country_frname3))
                                     {{$franchisee_details->country_frname3}} 
                                @else
                                    {{ '-' }}              
                                @endif
                            </div>
				        </div>
                     </div>
                     </div>
                     
                       <div class="col-lg-4">   
                      <div class="form-group fld">
                        <label for="textfield" class="col-sm-12">Regional Support Center: </label>
                        <div class="col-sm-12">
					    	<div id="franchi_typename">
                            	@if(isset($franchisee_details->region_frname) && !empty($franchisee_details->region_frname) )
                         		{{ $franchisee_details->region_frname }}
                                @elseif(isset($franchisee_details->region_frname1) && !empty($franchisee_details->region_frname1) )
                                    {{ $franchisee_details->region_frname1 }}
                                @elseif(isset($franchisee_details->region_frname2) && !empty($franchisee_details->region_frname2) )
                                    {{ $franchisee_details->region_frname2 }}  
                                @else
                                    {{ '-' }}      
                                @endif
                            </div>
				        </div>
                     </div>
                     </div>
                     
                       <div class="col-lg-4">   
                      <div class="form-group fld">
                        <label for="textfield" class="col-sm-12">State Support Center: </label>
                        <div class="col-sm-12">
					    	<div id="franchi_typename">
                            	@if(isset($franchisee_details->state_frname) && !empty($franchisee_details->state_frname) )
                         		{{ $franchisee_details->state_frname }}
                               @elseif(isset($franchisee_details->state_frname1) && !empty($franchisee_details->state_frname1) )
                                    {{ $franchisee_details->state_frname1 }}
                                 @else
                                    {{ '-' }}        
                                @endif
                            </div>
				        </div>
                     </div>
                     </div>
                     
                       <div class="col-lg-4">   
                      <div class="form-group fld">
                        <label for="textfield" class="col-sm-12">District Support Center: </label>
                        <div class="col-sm-12">
					    	<div id="franchi_typename">
                            	 @if(isset($franchisee_details->district_frname) && !empty($franchisee_details->district_frname) )
                         		{{ $franchisee_details->district_frname }}
                                @else
                            	{{ '-' }}      
                              @endif  
                            </div>
				        </div>
                     </div>
                     </div>

                     <hr style="clear:both" width="100%" />
					
				    <div class="form-group">
                        <label for="textfield" class="col-sm-3">Support Center Type:</label>
                        <div class="col-sm-6">
                            @if(!empty($franchisee_types))
								<select name="franchi_type" class="form-control" id="franchi_type"  required>
									<option value="">Select Type</option>
									@foreach($franchisee_types as $row)
									 <option @if($type == $row->franchisee_typeid){{'selected'}}@endif value="{{$row->franchisee_typeid}}">{{$row->franchisee_type}}</option>
									@endforeach
								</select>
								@endif
                        </div>
                    </div> 	                    
					<div class="form-group country" style="">
                        <label for="textfield" class="col-sm-3">Country:</label>
                        <div class="col-sm-6">
						     <input type="hidden" name="user_id" id="user_id" value="{{!empty($user_id)?$user_id:''}}">
						     <select name="country" class="form-control" id="country" required>
                                <option value="">--Select Country--</option>
                                <?php
                                foreach ($country as $row)
                                {
                                    ?>
                                    <option value="<?php echo $row->country_id;?>" <?php
                                    if (!empty($access_country))
                                        if ($access_country == $row->country_id)
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
                        <label for="textfield" class="col-sm-3">Region:</label>
                        <div class="col-sm-6">
                           
								<select name="region" class="form-control" id="region"  required>
									<option value="">--Select Region--</option>
									@if(!empty($regions))
									@foreach($regions as $row)
									<option value="{{ $row->region_id}}" <?php if($access_region == $row->region_id) {echo "selected='selected'";} ?> class="c_{{$row->country_id}}">{{ $row->region_name }}</option>									
									@endforeach
									@endif
								</select>
								
                        </div>
                    </div>
			        <div class="form-group state" style="display:none">
                        <label for="textfield" class="col-sm-3">State:</label>
                        <div class="col-sm-6">
                            
								<select name="state" class="form-control" id="state" required>
									<option value="">--Select State--</option>
                                    @if(!empty($states))
									@foreach($states as $row)
									<option value="{{ $row->state_id}}" @if($access_state == $row->state_id) {{'selected'}} @endif>{{ $row->name }}</option>
									@endforeach
                                    @endif
								</select>
								
                        </div>
                    </div>
                    <div class="form-group union_territory" style="display:none">
                        <label for="textfield" class="col-sm-3">Union Territory:</label>
                        <div class="col-sm-6">                          
								<select name="union_territory[]" class="form-control" id="union_territory" multiple="multiple" style="height:100px;" required>																	
								</select>							
                        </div>
                    </div>
					<div class="form-group district" style="display:none">
                        <label for="textfield" class="col-sm-3">District:</label>
                        <div class="col-sm-6">
                         	<select name="district" class="form-control" id="district" required>
									<option value="">--District--</option>
                                    @if(!empty($districts))
									@foreach($districts as $district)
									<option value="{{ $district->district_id}}" @if($access_district== $district->district_id) {{'selected'}} @endif >{{ $district->district_name }}</option>
									@endforeach
                                    @endif
								</select><br />
                                 <input type="text" style="display:none" class="form-control" name="district_others" id="district_others" placeholder = "Enter District Name" />
                        </div>
                    </div>
					 <div class="form-group city" style="display:none">
                        <label for="textfield" class="col-sm-3">City:</label>
                        <div class="col-sm-6">
                        	<select name="city" class="form-control" id="city" required>
									<option value="">--City--</option>
                                     @if(!empty($citys))
									@foreach($citys as $city)
									<option value="{{ $city->city_id}}" @if($access_city== $city->city_id) {{'selected'}} @endif  >{{ $city->city_name }}</option>
									@endforeach
                                     @endif
								</select>
                                <br />
                                <input type="text" style="display:none" class="form-control" name="city_others" id="city_others" placeholder = "Enter City Name" />
                         </div>
                     </div>
                  
                      <div id="franchisee_mapped_user">
                      </div>
                      
                    <input type="hidden" id="status"   class='icheck-me' name="status" data-skin="square" data-color="blue"
                           value="<?php echo Config::get('constants.ACTIVE');?>"  >
                    <div class="form-group">
                        <label for="textfield" class="control-label col-sm-3">&nbsp;</label>
                        <div class="col-sm-6" >
                            <input type="submit" name="update_access" id="update_access" class="btn btn-primary" value="Save">
                        </div>
                    </div>
				   </form>
                         <script src="{{URL::asset('js/providers/admin/franchisee/franchisee_update_access.js')}}"></script>