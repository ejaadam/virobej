@if(!isset($error))
<form method="post" action="{{URL::to('account/wallet/fund_transfer_save')}}" class="form-horizontal form-bordered" id="fundtransfer_confirm_form" autocomplete="off" onsubmit="return false;">    
    <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"> {{\trans('affiliate/wallet/fundtransfer.from_wallet')}}</label>
        <div class="col-lg-8">
            <p id="d_ewallet_id" class="form-control-static">{{$ewallet_name}}</p>
        </div>
    </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label">{{\trans('affiliate/wallet/fundtransfer.currency')}}</label>
        <div class="col-lg-8">
            <p id="d_currency_id" class="form-control-static">{{$currency_code}}</p>
        </div>
    </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"  for="user_avail_bal">{{\trans('affiliate/wallet/fundtransfer.available_bal')}}:</label>
        <div class="col-lg-8">
            <p id="d_user_balance" class="form-control-static">{{$userObj->amount_with_decimal($availbalance).' '.$currency_code}}</p>
        </div>
	   </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"  for="to_user">{{\trans('affiliate/wallet/fundtransfer.to_user')}}</label>
        <div class="col-lg-8">
            <p id="d_to_user" class="form-control-static">{{$to_user}}</p>
        </div>
    </div>
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label" for="amount">{{\trans('affiliate/wallet/fundtransfer.amount')}}</label>
        <div class="col-lg-8">
            <p id="d_totamount" class="form-control-static">{{$userObj->amount_with_decimal($totamount).' '.$currency_code}}</p>
        </div>
    </div>
   @if(isset($account_settings->otp_status) && $account_settings->otp_status == 1)
	 <div class="form-group">
        <label class="col-lg-4 col-sm-2 control-label"  for="tac_code">{{\trans('affiliate/wallet/fundtransfer.tac_code')}} *</label>
        <div class="col-lg-8">
            <div class="input-group">
                <input type="password" name="tac_code" id="tac_code" class="form-control" />
                <span class="input-group-btn">
                   <button class="btn btn-default" data-url="{{URL::to('account/wallet/send_tac_code')}}"  id="get_tac_code" type="button">{{\trans('affiliate/wallet/fundtransfer.get_tac_code')}} </button>
                </span>
            </div>
        </div>
    </div>
	 @endif	  
	  <div class="form-group" >
        <div class="col-lg-4 col-sm-1 text-right" >
            <input type="button" name="fund_transfer"  id="back" class="btn btn-sm btn-primary" value="{{\trans('affiliate/wallet/fundtransfer.bck_btn')}}" />
        </div>
        <div class="col-lg-4 col-sm-1" >
            <input type="button" name="fund_transfer"  id="confirm_fund_transfer" class="btn btn-sm btn-primary" value="{{\trans('affiliate/wallet/fundtransfer.confirm_transfer')}}" />
        </div>
    </div>	
	 </form> 

@else
<p id="fundtransfer_confirm_form">{{$error}}</p>
@endif