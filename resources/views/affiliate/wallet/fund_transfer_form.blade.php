     <form method="post" action="{{route('aff.wallet.fund_transfer_confirm')}}"class="form-horizontal form-bordered" id="fundtransferform" autocomplete="off" onsubmit="return false;">
			  <div class="form-group">
                <label class="col-lg-4 col-sm-2 control-label">{{\trans('affiliate/wallet/fundtransfer.wallet')}}</label>
                   <div class="col-lg-8 form_field">
<select name="wallet_id" id="wallet_id" class="form-control" required >
				       <option value="">{{\trans('affiliate/wallet/fundtransfer.select_wallet')}}</option>
					   @foreach($wallet_list as $value)
           	             <option value="<?php echo $value->wallet_id;?>" <?php echo (isset($wallet_id) && $wallet_id == $value->wallet_id) ? 'selected' : ''?>><?php echo $value->wallet;?></option>
                                                @endforeach
                       </select>
					    </div>
                        </div>
<div class="form-group hidefld">
						<label class="col-lg-4 col-sm-2 control-label">{{\trans('affiliate/wallet/fundtransfer.currency')}}</label>
                        <div class="col-lg-8 form_field">	
<select name="currency_id" id="currency_id" class="form-control" required >
                          <option value="">{{\trans('affiliate/wallet/fundtransfer.select_currency')}}</option>	
                          </select>
                          </div>
                          </div>
<div class="form-group  hidefld1">   
                           <label class="col-lg-4 col-sm-2 control-label"  for="user_avail_bal">{{\trans('affiliate/wallet/fundtransfer.available_bal')}}:</label>
                           <div class="col-lg-8 form_field">
			               <span id="user_balance" style="margin-top:9px; display:inline-block">{{ isset($max_trans_amount) ? $max_trans_amount : '' }}</span>
                           <input type="hidden" name="user_avail_bal" id="user_avail_bal" class="form-control" value="<?php echo $current_balance ? $current_balance : '0';?>"  />
                           </div>
</div>

<div class="form-group hidefld1">
                            <label class="col-lg-4 col-sm-2 control-label"  for="to_user">{{\trans('affiliate/wallet/fundtransfer.to_user')}} *</label>
						     <div class="col-lg-8 form_field">
							<div class="input-group input-group-md form_field">
                            <input type="text" name="to_user" id="to_user" class="form-control" value="{{ (isset($rec_email) &&!empty($to_user))? $to_user : ''}}" />
							<span class="" id="to_user_status"></span>
							<div class="input-group-btn">
							<button type="button" value="search" class="btn btn-md bg-olive btn-flat" onclick="user_check();"> 
							<span class="glyphicon glyphicon-search"></span> Search
                             </button>
							 </div>
							</div></div>
                            <input  type="hidden" name="to_account_id" id="to_account_id" value="" />
                            <input type="hidden" name="to_cur_balance" id="to_cur_balance" />
                            </div>
							
<div class="form-group hidefld2">
                            <label class="col-lg-4 col-sm-2 control-label"  for="to_user">{{\trans('affiliate/wallet/fundtransfer.to_user_full_name')}}</label>
                            <div class="col-lg-8 form_field">
                            <input type="text" name="rec_name" id="rec_name" class="form-control" value="{{ (isset($rec_name) &&!empty($rec_name))? $rec_name : ''}}" disabled/>
                            </div>
</div>

<div class="form-group hidefld2">
                           <label class="col-lg-4 col-sm-2 control-label"  for="to_user">{{\trans('affiliate/wallet/fundtransfer.to_user_email')}}</label>
                           <div class="col-lg-8 form_field">
                           <input type="text" name="rec_email" id="rec_email" class="form-control" value="{{ (isset($rec_email) &&!empty($rec_email))? $rec_email : ''}}" disabled/>
                           </div>
</div>

<div class="form-group hidefld2">
                            <label class="col-lg-4 col-sm-2 control-label" for="amount">{{\trans('affiliate/wallet/fundtransfer.amount')}} *</label>
							 <div class="col-lg-8 form_field">
                             <input type="text" id="totamount" name="totamount" class="form-control"  onkeyup="checkamount()" onkeypress="return isNumberKey(event);"  placeholder="" value="{{ (isset($totamount) &&!empty($totamount))? $totamount : ''}}">
							 <input type="hidden" name="avail_balance" id="avail_balance" value="<?php echo $availbalance;?>" />
                                            <input type="hidden" name="amount" id="amount" value="{{ (isset($totamount) &&!empty($totamount))? $totamount : ''}}" />
                                            <input type="hidden"  name="max_trans_amount"  id="max_trans_amount" value="<?php echo $availbalance;?>" />
                                            <input type="hidden"  name="min_trans_amount"  id="min_trans_amount" value="{{ (isset($min_trans_amount) &&!empty($min_trans_amount))? $min_trans_amount : ''}}"  />
                                            <input type="hidden" name="charge" id="charge" value="{{ (isset($charge) &&!empty($charge))? $charge : ''}}" />
                                            <span class="help-block" id="amount_status"></span>
                                        </div>
                                    </div>									<div class="form-group hidefld3 form_field" >
<div class="col-lg-offset-4 col-lg-8 hidefld3 form_field" >
                                        <div class="col-lg-offset-4 col-lg-8" >
                            <input type="submit" name ="fund_transfer"  id="fund_transfer" class="btn btn-sm btn-primary" value="{{\trans('affiliate/wallet/fundtransfer.transfer_btn')}}"/>
                                        </div>
                                    </div>
              
            
			
			</form>
	