<div class="col-sm-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="box-title"><i class="fa fa-edit margin-r-5"></i> Edit Details</h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal form-bordered" id="profit-sharing-form" action="" method="post" autocomplete="off">
                <div class="form-group">
                    <label for="profit_sharing" class="control-label col-sm-4">{{trans('general.seller.profit-sharing.profit_sharing')}}</label>
                    <div class="col-sm-8">
                        <p class="form-control-static ignore-reset" id="profit_sharing" ></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cashback_on_pay" class="control-label col-sm-4">{{trans('general.seller.profit-sharing.cashback_on_pay')}}</label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <input type="number" min="0" max="100" class="form-control" placeholder="{{trans('general.seller.profit-sharing.cashback_on_pay')}}" name="profit_share[cashback_on_pay]" id="cashback_on_pay" data-err-msg-to="#cashback_on_pay_err"/>
                            <span class="input-group-addon profit_sharing_in_label"></span>
                        </div>
						<span id="cashback_on_pay_err"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cashback_on_redeem" class="control-label col-sm-4">{{trans('general.seller.profit-sharing.cashback_on_redeem')}}</label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <input type="number" min="0" max="100" class="form-control" placeholder="{{trans('general.seller.profit-sharing.cashback_on_redeem')}}" name="profit_share[cashback_on_redeem]" id="cashback_on_redeem" data-err-msg-to="#cashback_on_redeem_err"/>
                            <span class="input-group-addon profit_sharing_in_label"></span>
                        </div>
						<span id="cashback_on_redeem_err"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cashback_on_shop_and_earn" class="control-label col-sm-4">{{trans('general.seller.profit-sharing.cashback_on_shop_and_earn')}}</label>
                    <div class="col-sm-7">
                        <div class="input-group">
                            <input type="number" min="0" max="100" class="form-control" placeholder="{{trans('general.seller.profit-sharing.cashback_on_shop_and_earn')}}" name="profit_share[cashback_on_shop_and_earn]" id="cashback_on_shop_and_earn" data-err-msg-to="#cashback_on_shop_and_earn_err"/>
                            <span class="input-group-addon profit_sharing_in_label"></span>
                        </div>
						<span id="cashback_on_shop_and_earn_err"></span>
                    </div>
                </div>
				<input type="hidden" name="status" id="status" class="ignore-reset" value="accepted" />
				<div class="form-group">
					<label class="control-label col-sm-3">&nbsp;</label>
					<div class="col-sm-9">
							<input type="submit" class="btn btn-primary" value="">&nbsp;
							<button type="button" class="btn btn-warning close-btn">Close</button>
					</div>
                </div>
            </form>
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
