<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">            
            <div class="panel_controls" id="list-panel">
                <div class="row">
                    <div class="col-sm-6">
						<div class="panel panel-default" id="new_request">
							<div class="panel-heading">
								<div class="pull-right">
									<button class="btn btn-danger btn-xs close-btn"> <i class="fa fa-times"></i> Close</button>
								</div>
								<h3 class="box-title"><i class="fa fa-edit margin-r-5"></i> New Request</h3>
							</div>
							<div class="panel_controls">								
								<div class="row">
									<label for="profit_sharing" class="control-label col-sm-6">{{trans('general.seller.profit-sharing.profit_sharing')}}</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="profit_sharing" ></p>
									</div>
								</div>
								<div class="row">
									<label for="cashback_on_pay" class="control-label col-sm-6">{{trans('general.seller.profit-sharing.cashback_on_pay')}}</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="cashback_on_pay"></p>
									</div>
								</div>
								<div class="row">
									<label for="cashback_on_redeem" class="control-label col-sm-6">{{trans('general.seller.profit-sharing.cashback_on_redeem')}}</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="cashback_on_redeem" > </p>
									</div>
								</div>
								<div class="row">
									<label for="cashback_on_shop_and_earn" class="control-label col-sm-6">{{trans('general.seller.profit-sharing.cashback_on_shop_and_earn')}}</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="cashback_on_shop_and_earn" ></p>
									</div>
								</div>
								
								
								
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="box-title">{{trans('general.seller.seller')}}</h3>
							</div>
							<div class="panel_controls">
								<div class="row">
									<label for="supplier_name" class="control-label col-sm-6">{{trans('general.seller.seller')}} </label>
									<div class="col-sm-6">
										<p class="form-control-static ignore-reset"><span id="supplier_name"></span> (<span id="supplier_code"></span>)</p>
									</div>
								</div>
							   
								<div class="row">
									<label for="contact" class="control-label col-sm-6">Contact Person</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="contact" ></p>
									</div>
								</div>
								
								<div class="row">
									<label for="mobile" class="control-label col-sm-6">Mobile</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="mobile" ></p>
									</div>
								</div>
								<div class="row">
									<label for="email" class="control-label col-sm-6">Email</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="email" ></p>
									</div>
								</div>
								 
								<div class="row">
									<label for="profit_sharing" class="control-label col-sm-6">Address</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="formated_address" ></p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-6">
						
					</div>

					<div class="col-sm-6" id="current">
						<div class="panel panel-default">
							<div class="panel-heading">            
								<h3 class="box-title"> Current Cashback Details</h3>
							</div>
							<div class="panel_controls" >
								<div class="row">
									<label for="profit_sharing" class="control-label col-sm-6">{{trans('general.seller.profit-sharing.profit_sharing')}}</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="profit_sharing" ></p>
									</div>
								</div>
								<div class="row">
									<label for="cashback_on_pay" class="control-label col-sm-6">{{trans('general.seller.profit-sharing.cashback_on_pay')}}</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="cashback_on_pay"></p>
									</div>
								</div>
								<div class="row">
									<label for="cashback_on_redeem" class="control-label col-sm-6">{{trans('general.seller.profit-sharing.cashback_on_redeem')}}</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="cashback_on_redeem" ></p>
									</div>
								</div>
								<div class="row">
									<label for="cashback_on_shop_and_earn" class="control-label col-sm-6">{{trans('general.seller.profit-sharing.cashback_on_shop_and_earn')}}</label>
									<div class="col-sm-6">
										<p class="form-control-static" id="cashback_on_shop_and_earn" ></p>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>           
        </div>        
    </div>
</div>

