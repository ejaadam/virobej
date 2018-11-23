<div class="row"  id="bank_details_main">
	<div class="col-sm-12">
		<!--div class="panel panel-default">
			<!--div class="panel-heading">
				<h4 class="panel-title">Edit Seller</h4>
			</div-->
			<div class="">
				<div class="row">
					<!--div class="col-sm-1">
					</div-->							
					<div class="col-sm-12">
					<form class="form-horizontal" id="bank_details" action="{{route('seller.account-settings.bank-details')}}" enctype="multipart/form-data">
						<div>
							<div class="panel panel-info">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a>
											Bank Details
										</a>
									</h4>
								</div>
								<div id="acc2_collapseFive" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="col-sm-12">
											<fieldset>														
												<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label">{!!$fields['payment_setings.beneficiary_name']['label']!!}</label>
													<div class="col-sm-10">
														<input class="form-control" id="account_name" {!!build_attribute($fields['payment_setings.beneficiary_name']['attr'])!!} value="{{$bank_account_details->beneficiary_name or ''}}">
													</div>
												</div>
												<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label">{!!$fields['payment_setings.account_no']['label']!!}</label>
													<div class="col-sm-10">
														<input class="form-control" id="account_no" {!!build_attribute($fields['payment_setings.account_no']['attr'])!!} value="{{$bank_account_details->account_no or ''}}" onkeypress="return RestrictSpace(event)">
													</div>
												</div>
												<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label">{!!$fields['payment_setings.confirm_account_no']['label']!!}</label>
													<div class="col-sm-10">
														<input class="form-control" id="confirm_account_no" {!!build_attribute($fields['payment_setings.confirm_account_no']['attr'])!!} value="{{$bank_account_details->confirm_account_no or ''}}" onkeypress="return RestrictSpace(event)">
													</div>
												</div>
												<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label">{!!$fields['payment_setings.ifsc_code']['label']!!}</label>
													<div class="col-sm-10">
														<input class="form-control" id="ifsc_code" data-url="{{route('seller.account-settings.get-ifsc-details')}}" {!!build_attribute($fields['payment_setings.ifsc_code']['attr'])!!} value="{{$bank_account_details->ifsc_code or ''}}" onkeypress="return RestrictSpace(event)">
													</div>
												</div>
											<!--	<div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label"></label>
													<div class="col-sm-10">
													<div class="checkbox  pull-right">
													<input type="checkbox" id="no_ifsc" value="1" name="gstin" class="flat-red gstin">	
													<label><b>I don't remember IFSC Code</b></label>
													</div>
												    </div>
												    </div>-->
																										
													@if (!empty($bank_account_details))
														<div class="form-group bank_data">
															<label for="inputEmail" class="col-sm-2 control-label">Bank Name</label>
															<div class="col-sm-10">
																<span>{{$bank_account_details->bank_name or ''}}</span>
															</div>
														</div>
														<div class="form-group bank_data">
															<label for="inputEmail" class="col-sm-2 control-label">Branch</label>
															<div class="col-sm-10">
																<span>{{$bank_account_details->branch_name or ''}}</span>
															</div>
														</div>													
													@endif
												<div  id="bank_det" style="display:none">
													<div class="form-group">
														<label for="inputEmail" class="col-sm-2 control-label">Bank Name</label>
														<div class="col-sm-10">
															<input class="form-control" id="bank_value"{!!build_attribute($fields['payment_setings.bank_name']['attr'])!!} value="{{$bank_account_details->bank_name or ''}}" readonly>
														</div>
													</div>
													<div class="form-group">
														<label for="inputEmail" class="col-sm-2 control-label">Branch</label>
														<div class="col-sm-10">
															<input class="form-control" id="branch_value" {!!build_attribute($fields['payment_setings.branch_name']['attr'])!!} value="{{$bank_account_details->branch_name or ''}}" readonly>
														</div>
													</div>
												
                                     <!-- IFSC Code View-->
												<!--<div class="row">
													<div class="col-sm-2">
														<address >
															
														</address>
													</div>
													<div class="col-sm-2">
														<address id="address_title" style="display:none;">
															<p><strong>Bank Name</strong></p>
															<p><strong>Branch</strong></p>
															<p><strong>City</strong></p>
															<p><strong>State</strong></p>
															<p><strong>District</strong></p>
															<p><strong>IFSC</strong></p>
															<p><strong>Address</strong></p>																	
														</address>
													</div>
													<div class="col-sm-8">																
														<address>																	
															<span id="ifsc_bank_details"></span>
														</address>
													</div>
												</div>-->
												<!-- IFSC Code View End  -->
											   
                              <!--         <div class="form-group">
													<label for="inputEmail" class="col-sm-2 control-label"></label>
													<div class="col-sm-10">
													<div class="checkbox">
													<input type="checkbox" id="terms_conditions" value="1" name="terms_conditions" class="flat-red gstin">	
													<label><b>I agree Terms & conditions</b></label>
													</div>
												</div>
												</div>		-->			
													<div class="form-group">										
														<div class="col-sm-offset-2 col-sm-3">									
															<input type="submit" value="Save" class="btn btn-primary " >
														</div>
													</div>		
											    </div>
											</fieldset> 
										</div>		
									</div>
								</div>
							</div>
						</div>
					  </form>	
					</div>
				</div>
			<!--/div-->
		</div>
	</div>
</div>
<!-- modal -->
<div id="retailer-qlogin-model1" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Find Your Bank IFSC Code</h4>
			</div>
			<div class="modal-body">
				<div id="accErr"></div>
				<div id="change_Member_pin" style="display:none;">
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{asset('js/providers/seller/account_settings.js')}}"></script>