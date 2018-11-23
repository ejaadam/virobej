@extends('seller.common.layout')
@section('pagetitle','Dashboard')
@section('top-nav')	
@stop
@section('layoutContent')
<div class="row">
	<div class="col-sm-12">
		<div class="user_heading">
			<div class="row">				
				<div class="col-sm-10">
					<div class="user_heading_info">						
						<h1>Shipping Information</h1>
						<span class="small">Home > Account Settings > Shipping Informations</span>
					</div>
				</div>
			</div>
		</div>	
		
		<div class="row">
			<div class="col-sm-12">
				<!--div class="panel panel-default"-->
					<!--div class="panel-heading">
						<h4 class="panel-title">Edit Seller</h4>
					</div-->
					<div class="">
						<div class="row">
							<!--div class="col-sm-1">
							</div-->							
							<div class="col-sm-12">
							<form class="form-horizontal" id="shipping_info" action="{{route('seller.account-settings.shipping-info')}}" enctype="multipart/form-data">
								<div class="panel-group" id="accordion2">
									
									
									<div class="panel panel-info">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#acc2_collapseFive">
													Shipping Informations
												</a>
											</h4>
										</div>
										<div id="acc2_collapseFive" class="panel-collapse collapse in">
											<div class="panel-body">
												<div class="col-sm-12">
													<fieldset>														
														<div class="form-group">
															<label for="inputEmail" class="col-sm-2 control-label">Free delivery if order amount is greater than</label>
															<div class="col-sm-3">
																<input class="form-control" id="free_delivery_amt" name="free_delivery_amt" type="text" onKeypress="return isNumberKey(event)" disabled>
															</div>
															<div class="col-sm-1">
																<label class="radio-inline">
																	<input name="free_delivery" class="free_delivery" value="1" type="radio">
																	Yes
																</label>
															</div>
															<div class="col-sm-1">
																<label class="radio-inline">
																	<input name="free_delivery" class="free_delivery" value="0" type="radio" checked>
																	No
																</label>
															</div>
														</div>
														<div class="form-group">
															<label for="inputEmail" class="col-sm-2 control-label">Free delivery for postal codes</label>															
															<div class="col-sm-1">
																<label class="radio-inline">
																	<input name="fd_postal_codes" class="fd_postal_codes"  value="1" type="radio">
																	Yes
																</label>
															</div>
															<div class="col-sm-1">
																<label class="radio-inline">
																	<input name="fd_postal_codes" class="fd_postal_codes" value="0" type="radio" checked>
																	No
																</label>
															</div>
														</div>
														<div class="form-group">
															<label for="inputEmail" class="col-sm-2 control-label">Postal Codes</label>
															<div class="col-sm-5">
																<div class="input-group">
																	<input class="form-control" id="postal_codes" name="postal_codes" type="text" onKeypress="return isNumberKey(event)" disabled>
																	<span class="input-group-btn">
																		<button type="submit" class="btn btn-primary" id="submit" disabled>Go!</button>
																	</span>
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
					</div>
				<!--/div-->
			</div>
		</div>
	</div>
	
    
</div>
@include('seller.common.assets')
<script src="{{asset('js/providers/seller/account_settings.js')}}"></script>
@stop
