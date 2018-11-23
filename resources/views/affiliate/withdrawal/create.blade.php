@extends('affiliate.layout.dashboard')
@section('title',"Create Withdrawal Request")
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Create Withdrawal Request</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('affiliate/general.dashboard')}}</a></li>
        <li>{{trans('affiliate/general.Withdrawal')}}</li>
        <li class="active">{{trans('affiliate/general.Create_Withdrawal_Request')}}</li>
      </ol>
	
    </section>
    <!-- Main content -->
    <section class="content">
		<div class="wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" id="payment-list">
                <div class="panel-heading">
                    <h2 class="panel-title">withdrawal page-headz</h2>
                </div>
                <div class="panel_controls">
                    <div class="row">
						
                        <div class="col-md-8">
                            <div id="payout-types"  data-url="<?php echo url('account/withdrawal/payout')?>" >
                            </div>  
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-info" style="text-align:left">
                                <p><strong>Note: </strong></p>
                                <strong>Withdrawals are processed twice a month.</strong>
                                <ul>
                                    <li>Withdrawal application on 1st - 15th and 16th - month end will be paid out before month end and 15th of the following month respectively</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default" id="withdrawal-form-panel" style="display: none;">
                <div class="panel-heading"> 
                    <a class="btn btn-xs close-withdraw btn-danger pull-right"><i class="fa fa-times"></i></a>
                    <h4 class="panel-title" id="payment_type"></h4>
                </div>
                <div class="panel-body">
                    <form class="form form-horizontal" id="withdrawal-form"  data-url="<?php echo url('account/withdrawal/payout-details')?>" action= "<?php echo url('account/withdrawal/save')?>" >
                        <input type="hidden" name="payment_type_id" id="payment_type_id" value="">
                        <input type="hidden" name="payout_type_key" id="payout_type_key" value="">
                        <div class="form-group">
                            <label class="control-label col-sm-4">Currency</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="currency_id" id="currency_id">
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Amount</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="number" name="amount" id="amount"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Withdrawable Amount</label>
                            <div class="col-sm-8">
                                <p class="form-control-static" id="withdrawable_amount"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-4">Charges</label>
                            <div class="col-sm-8">
                                <p class="form-control-static" id="charge"></p>
                            </div>
                        </div>
                        <h4>Breakdowns</h4><hr/>
                        <div id="breakdowns">
                        </div>
                        <h4>Account Details</h4><hr/>
                        <div id="account-details">
                            <div class="form-group account-details cashfree" style="display: none;">
                                <label class="control-label col-sm-4">CashFree Email ID</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="email" name="account_details[cashfree_account_id]" id="cashfree_account_id"/>
                                </div>
                            </div>
							<div class="form-group account-details paytm" style="display: none;">
                                <label class="control-label col-sm-4">PayTm Email ID</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="email" name="account_details[paytm_account_id]" id="paytm_account_id"/>
                                </div>
                            </div>
							<div class="form-group account-details paytm" style="display: none;">
                                <label class="control-label col-sm-4">PayTm Account Holder Name</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[account_name]" id="paytm_account_name"/>
                                </div>
                            </div>
                            <div class="form-group account-details solid-trust-pay USD" style="display: none;">
                                <label class="control-label col-sm-4">Solid Trust Pay Username</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[stp_username]" id="stp_username"/>
                                </div>
                            </div>
                           
                            
                            <div class="form-group account-details express-withdrawal wire-transfer USD INR SGD MYR" style="display: none;">
                                <label class="control-label col-sm-4">Account Holder Name</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[acc_holder_name]" id="acc_holder_name"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer INR" style="display: none;">
                                <label class="control-label col-sm-4">Account Number</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[b_accno]" id="b_accno"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer INR" style="display: none;">
                                <label class="control-label col-sm-4">Branch</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[b_branch]" id="b_branch"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer INR" style="display: none;">
                                <label class="control-label col-sm-4">Account Type</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[b_acc_type]" id="b_acc_type"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer INR" style="display: none;">
                                <label class="control-label col-sm-4">Bank Name</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[b_nickname]" id="b_nickname"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer INR" style="display: none;">
                                <label class="control-label col-sm-4">IFSC Code</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[ifsc]" id="ifsc"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer INR" style="display: none;">
                                <label class="control-label col-sm-4">PIN Number</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[b_panid]" id="b_panid"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer INR" style="display: none;">
                                <label class="control-label col-sm-4">Country</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[b_country]" id="b_country"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer USD" style="display: none;">
                                <label class="control-label col-sm-4">Account Number</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[us_acc_no]" id="us_acc_no"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer USD" style="display: none;">
                                <label class="control-label col-sm-4">Account Type</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[us_account_type]" id="us_account_type"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer USD" style="display: none;">
                                <label class="control-label col-sm-4">Bank Name</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[us_nickname]" id="us_nickname"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer USD" style="display: none;">
                                <label class="control-label col-sm-4">Swift Code</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[us_swift_code]" id="us_swift_code"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer USD" style="display: none;">
                                <label class="control-label col-sm-4">Routing Number</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[us_routing_no]" id="us_routing_no"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer USD" style="display: none;">
                                <label class="control-label col-sm-4">State/City</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[us_state]" id="us_state"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer USD" style="display: none;">
                                <label class="control-label col-sm-4">Country</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[us_bank_country]" id="us_bank_country"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer SGD MYR" style="display: none;">
                                <label class="control-label col-sm-4">Account Number</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[sgd_acc_number]" id="sgd_acc_number"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer SGD MYR" style="display: none;">
                                <label class="control-label col-sm-4">Account Type</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[sgd_acc_type]" id="sgd_acc_type"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer SGD MYR" style="display: none;">
                                <label class="control-label col-sm-4">Bank Name</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[sgd_bank_name]" id="sgd_bank_name"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer SGD MYR" style="display: none;">
                                <label class="control-label col-sm-4">Swift Code</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[sgd_swift_code]" id="sgd_swift_code"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer SGD MYR" style="display: none;">
                                <label class="control-label col-sm-4">Bank Code</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[sgd_bank_code]" id="sgd_bank_code"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer SGD MYR" style="display: none;">
                                <label class="control-label col-sm-4">Country</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[sgd_country]" id="sgd_country"/>
                                </div>
                            </div>
                            <div class="form-group account-details ko-kart USD" style="display: none;">
                                <label class="control-label col-sm-4">Ko-Kard Account Number</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="email" name="account_details[kokard_account_no]" id="kokard_account_no"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer local-money-transfer USD INR SGD MYR" style="display: none;">
                                <label class="control-label col-sm-4">Address Line1</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[fst_addr]" id="fst_addr"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer local-money-transfer USD INR SGD MYR" style="display: none;">
                                <label class="control-label col-sm-4">Address Line2</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[sec_addr]" id="sec_addr"/>
                                </div>
                            </div>
                            <div class="form-group account-details express-withdrawal wire-transfer local-money-transfer USD INR SGD MYR" style="display: none;">
                                <label class="control-label col-sm-4">Account Country</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="account_details[acc_country]" id="acc_country"/>
                                </div>
                            </div>
                            <div class="form-group account-details local-money-transfer USD"  style="display: none;">
                                <label class="control-label col-sm-4">Full Name</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="email" name="account_details[full_name]" id="full_name"/>
                                </div>
                            </div>
                            <div class="form-group account-details local-money-transfer USD"  style="display: none;">
                                <label class="control-label col-sm-4">Mobile</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-btn"><input class="form-control" type="text" name="account_details[phonecode]" id="phonecode"/></span>
                                        <input class="form-control" type="text" name="account_details[mobile]" id="mobile"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <input class="btn btn-success" type="submit" value="Save">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    </section>
    <!-- /.content -->
@stop
@section('scripts')

<script src="<?php echo URL::asset('js/providers/affiliate/withdrawal/withdrawal.js');?>" type="text/javascript" charset="utf-8"></script>
@stop