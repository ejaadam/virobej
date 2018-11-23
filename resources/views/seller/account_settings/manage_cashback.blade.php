
														<div class="tab-content">
															<div class="row">
																<div class="well">
																	<div id="sett_msg"></div>
																	<form id="change-settings" class="form-horizontal" action="{{route('seller.account-settings.update-cashback')}}" method="post">
																	
																		<div id="wallets_div">
																			<div class="form-group">
																				<label for="member_redeem_wallets" class="col-sm-3 control-label">Accept Payment :</label>
																				<div class="col-sm-9" id="">
																					<div class="checkbox">
																						<input type="checkbox" id="accept_payment" value="1"  name="services[pay]" class="flat-red">					
																						<label><b>Accept Credit / Debit Cards</b></label>
																					</div>
																					<span class="text-muted ml15" id="ap-notes"></span>
																				</div>											
																			</div>
																			
																			<div class="form-group">
																				<label for="member_redeem_wallets" class="col-sm-3 control-label">Offer Cashback :</label>
																				<div class="col-sm-9">
																					<div class="checkbox">
																						<input type="checkbox" value="1" name="services[shop_and_earn]" id="offer_cashback" class="flat-red">			
																						<label><b>Cashback</b></label>
																					</div>
																					<span class="text-muted ml15" id="oc-notes"></span>
																				</div>											
																			</div>
																			<div class="form-group">
																				<div class="col-sm-offset-2 col-sm-2">
																					<button type="button" id="submit_setting" class="btn btn-success pull-right">Update</button>&nbsp;
																				</div>
																			</div>
																		</div>
																	</form>
																</div>
															</div>
															<!-- /.tab-pane -->
															<div class="row">
																<div class="well">																		
																	<h4>Commission to Virob</h4>
																	<div id="comm_msg"></div> 
																	<div class="row">
																		<div class="col-md-4">
																			<div class="well" id="current_setting">
																			</div>	
																		</div>
																		<div class="col-md-4">
																			<div class="well" id="pending_setting">
																			</div>	
																		</div>	
																	</div>																		
																	<div class="row">
																		<div class="col-sm-12">									
																			<div class="well">																	
																				<form id="commission_form" class="form-horizontal" action="{{route('seller.account-settings.add_profit_sharing')}}" data-get-url="{{route('seller.account-settings.commision')}}" method="post">
																					<div id="form" >
																						<h4>New Commission</h4>
																						<div class="form-group">
																							<label for="member_redeem_wallets" class="col-sm-2 control-label">Enter Commision :</label>
																							<div class="row">
																								<div class="col-md-2">
																									<div class="input-group">
																										<input type="text" name="profit_sharing" data-err-msg-to="#comm_err" class="form-control" id="profit_sharing" maxlength="2" onkeypress="return isNumberKey(event);" placeholder="0">
																										<span class="input-group-addon">%</span>
																									</div>
																									<span id="comm_err"></span>
																								</div>
																							</div>
																						</div>
																						<div class="form-group">
																							<label for="member_redeem_wallets" class="col-sm-2 control-label">Cashback has expiry :</label>
																							<div class="row col-sm-3">
																								<div class="checkbox">
																									<input type="radio" value="1" class="is_period" name="is_cashback_period">
																									Yes&nbsp;&nbsp;&nbsp;&nbsp;
																									<input type="radio" value="0" class="is_period" name="is_cashback_period">No
																								</div>
																							</div>
																						</div>
																						<div class="form-group" id="valid_date" style="display:none;">
																							<label for="member_redeem_wallets" class="col-sm-2 control-label">Offer Validity :</label>
																							<div class="row">
																								<div class="col-md-2">
																									<div class="form-group">
																										<div class="input-group date">
																											<div class="input-group-addon">
																												<i class="icon-calendar"></i>
																											</div>
																											<input type="date" name="cashback_start" class="form-control" id="cashback_start">
																										</div>
																									</div>
																								</div>
																								<div class="col-md-2">
																									<div class="form-group">
																										<div class="input-group date">
																											<div class="input-group-addon"><i class="icon-calendar"></i></div>
																											<input type="date" name="cashback_end" class="form-control" id="cashback_end">
																										</div>
																									</div>
																								</div>
																							</div>
																						</div>
																						<div class="form-group">
																							<div class="col-sm-offset-2 col-sm-4">
																							
																								<button type="submit"  class="btn btn-warning">Submit</button>&nbsp;
																							</div>
																						</div>
																					</div>
																				</form>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
												
<script src="{{asset('js/providers/seller/manage_cashback.js')}}"></script>
