<div class="row">
											<div class="col-sm-12">
												<!-- div class="panel panel-default" -->
													<div class="">
														<div class="row">
															<div class="col-sm-12">
															<form class="form-horizontal" id="shipping_details" action="{{route('seller.account-settings.update-return-address')}}" enctype="multipart/form-data">
																<div>
																	
																	<div class="panel panel-info">
																		<div class="panel-heading">
																			<h4 class="panel-title">
																				<a>
																					Return Address
																				</a>
																			</h4>
																		</div>
																		<div id="acc2_collapseTwo" class="panel-collapse collapse in">
																			<div class="panel-body">
																				<div class="col-sm-12">
																					<fieldset>														
																						<span class="form-horizontal">
																							<input type="hidden" name="country_id" id="shipping_country_id" value="{{$country_id}}">
																							<input type="hidden" name="city" id="shipping_cityid" value="{{$location_details->city_id or ''}}">
																							<div class="form-group">
																								<label for="inputEmail" class="col-sm-2 control-label">Flat No/ Street</label>
																								<div class="col-sm-10">
																									<input type="text" class="form-control" id="flat_no" name="flat_no" placeholder="Flat No/Street" value="{{$pickup_adrress->flatno_street or ''}}">
																								</div>
																							</div>
																							<div class="form-group">
																								<label class="col-sm-2 control-label">LandMark</label>
																								<div class="col-sm-10">
																									<input type="text" class="form-control" id="address" name="address[landmark]" placeholder="LandMark" value="{{$pickup_adrress->landmark or ''}}" >
																								</div>
																							</div>
																							<div class="form-group">
																								<label class="col-sm-2 control-label">Postal Code</label>
																								<div class="col-sm-10">
																									<input type="text" class="form-control" id="shipping_postal_code" name="address[postal_code]" placeholder="Postal Code" value="{{$pickup_adrress->postal_code or ''}}">
																								</div>
																							</div>
																							<div class="form-group">
																								<label class="col-sm-2 control-label">City</label>
																								<div class="col-sm-10">
																									<select  name="address[city_id]" id="shipping_city_id" class="form-control"></select>
																										
																								</div>
																							</div>	
																							<div class="form-group">
																								<label class="col-sm-2 control-label">State</label>
																								<div class="col-sm-10">
																									<select  name="address[state_id]" id="shipping_state_id" class="form-control">
																									
																									</select>
																								</div>
																							</div>	
																							<div class="form-group">
																								<label class="col-sm-2 control-label">Country</label>
																								<div class="col-sm-10">
																									<input type="text" class="form-control" id="shipping_phone_no" name="country" placeholder="" disabled value="{{$country or ''}}">
																								</div>
																							</div>
																							<div class="form-group">
																								<label class="col-sm-2 control-label">Phone Number</label>
																								<div class="col-sm-10">
																									<input type="text" class="form-control" id="shipping_phone_no" name="address[phone_no]" placeholder="Phone Number" value="{{$pickup_adrress->phone_no or ''}}">
																								</div>
																							</div>
																							<div class="form-group">
																								<label class="col-sm-2 control-label">Email </label>
																								<div class="col-sm-10">
																									<input type="text" class="form-control" id="shipping_email" name="address[email]" placeholder="Email" value="{{$pickup_adrress->email or ''}}">
																								</div>
																							</div>
																						</span>
																					</fieldset>     
																				</div>	
																				
																				 <div class="col-sm-offset-2 col-sm-2 pull-left">
																					<input type="submit" id="Submit" class="btn btn-primary" value="Update Address">
																				</div>											
																			</div>
																		</div>
																	</div>
																</div>
															</form>	
															</div>
														</div>
													</div>
												<!--/div-->
											</div>
										</div>
<script src="{{asset('js/providers/seller/return_address.js')}}"></script>										