<?php
namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Api\User\UserApiBaseController;
use App\Models\Api\User\WithdrawalModel;
use App\Models\Api\User\WalletModel;
use App\Models\Api\User\AffModel;
use CommonLib;
use App\Helpers\CommonNotifSettings;

class WithdrawalController extends UserApiBaseController
{   
    public function __construct ()
    {		
        parent::__construct();		
		$this->apiaffObj = new AffModel();
		$this->walletObj = new WalletModel();	
        $this->withdrawalObj = new WithdrawalModel();
    }	
	
	public function paymentTypeInfo ()
    {
        $op = [];
        $data = $this->request->all();
        $data['account_id'] = $this->userSess->account_id;
        $data['currency_id'] = $this->userSess->currency_id;
        $data['currency'] = $this->userSess->currency_code;
        $data['country_id'] = $this->userSess->address->country_id;
        $data['payment_type'] = $this->request->payment_type;		
        $payment_info = $this->withdrawalObj->getPaymentDetails($data);		
		$balance = $this->walletObj->wallet_balance($data);			
        if (!empty($payment_info))
        {
            $data['payment_info'] = $op['payment_type'] = $payment_info;			
            //$this->session->set('payout.saveAccountDetails', $data);
            $op['payment_fields'] = CommonNotifSettings::getHTMLValidation('user.withdraw-fund.save-bank-details');
            $op['vim_balance'] = isset($balance['vimoney_bal']) ? $balance['vimoney_bal'] : CommonLib::currency_format(0, $this->userSess->currency_id, true, false, 2);
			//print_r($op);exit;
			
            if (isset($op['payment_fields']['account_details.b_bank_name']))
            {
                $op['payment_fields']['account_details.b_bank_name']['list'] = $this->withdrawalObj->getBankList($data);
            } 
            if (isset($op['payment_fields']['account_details.b_acc_type']))
            {
                $op['payment_fields']['account_details.b_acc_type']['list'] = $this->withdrawalObj->getAccountTypeList($data);
            } 
			
            if ($this->request->is('api/*'))
            {
                array_walk($op['payment_fields'], function(&$f)
                {
                    $f = array_merge($f['attr'], $f);
                    unset($f['attr']);
                });
                $op['payment_fields'] = array_values($op['payment_fields']);
            }
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['notes'] = trans('user/withdrawal.payment_details_notes');
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('user/general.please_contact_administrator');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	
	public function Save_Bank_Details ()
    {		
        $op = $data = array();		
        $data = $this->request->all();			
		$data['account_id'] = $this->userSess->account_id;
        $data['currency_id'] = $this->userSess->currency_id;
        $data['country_id'] = $this->userSess->country_id;
        $data['currency'] = $this->userSess->currency_code;		
		if (!empty($data))
		{
			$result = $this->withdrawalObj->Save_Bank_Details($data);
			if ($result) {				
			    $data['payout_id'] = $result;
				$op['account_details'] = $this->withdrawalObj->getUserPaymentInfo($data);
				if (!empty($op['account_details']))
				{
					array_walk($op['account_details'], function(&$a, $k)
					{					
						$a = ['label'=>trans('user/withdrawal.account_details.'.$k), 'value'=>$a];					
					});
					$op['account_details']['logo'] = asset($this->config->get('path.MANAGED_BANK.LOGO_PATH.WEB').'sbi.png');
					$op['account_details']['id'] = $result;
				}
				$op['msg'] = trans('user/withdrawal.cashout_mode_updated');
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			} else {
				$op['msg'] = 'Something went wrong.';
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			}		
		}
		else 
		{
			$op['msg'] = trans('user/account.not_accessable');
			$op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
		}		
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function List_Bank_Details ()
    {		
        $op = $data = array();		 	
		$data['account_id'] = $this->userSess->account_id;
        $data['currency_id'] = $this->userSess->currency_id;        
		$result = $this->withdrawalObj->List_Bank_Details($data);		
		if ($result) {
				$op['data'] = $result;
				$op['msg'] = '';
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			} else {
				$op['msg'] = 'Data Not Found.';
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			}		
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function Delete_Bank_Details ()
    {		
        $op = $data = array();	
		$data = $this->request->all();				
		$data['account_id'] = $this->userSess->account_id;
        $data['currency_id'] = $this->userSess->currency_id;        		
		$result = $this->withdrawalObj->Delete_Bank_Details($data);					
		if ($result) {				
		        $op['data'] = $this->withdrawalObj->List_Bank_Details($data);		
				$op['msg'] = 'Account Details Removed Successfully';
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			} else {
				$op['msg'] = 'Account Details Already Removed';
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			}		
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function Get_Bank_Details ()
    {	
        $op = $data = array();		
        $data = $this->request->all();			
		$data['account_id'] = $this->userSess->account_id;
        $data['currency_id'] = $this->userSess->currency_id;
        $data['country_id'] = $this->userSess->country_id;
        $data['currency'] = $this->userSess->currency_code;
		$balance = $this->withdrawalObj->getWalletBalance($this->userSess->account_id, $this->userSess->currency_id, $this->config->get('constants.CASHBACK_CREDIT_WALLET'), true, true, $this->userSess->country_id);
		$payment_types = $this->withdrawalObj->getWithdrawalPaymentTypes($data);
		//$account_details = $this->withdrawalObj->getAccountDetails($data);
		$data['payment_type'] 	 = 'BANK_TRANSFER';		
		if ($balance)
        {
			if ($balance->balance >= $data['amount'])
			{
				if (!empty($payment_types))
				{				
					//$data['payment_types'] = $op['payment_types'] = $payment_types;
					//$op['balance'] = $balance;
					//$this->session->set('Withdraw.AccountDetails', $account_details);
					$this->session->set('Withdraw.paymentDetails', $data);
					
					$data['amount'] 		 = $this->request->amount;
					$data['payout_id'] 		 = $this->request->id;
					$data['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.'.$data['payment_type']);				
					$paymentType = $this->withdrawalObj->paymentTypeDetails($data);		
					if($paymentType) {
						$data['paymentType'] = $paymentType;
						$settings = $this->withdrawalObj->get_balance_bycurrency($data);	
						if (!empty($settings))
						{
							$data['amount'] = isset($data['amount']) && !empty($data['amount']) ? $data['amount'] : $settings['max'];
							if ($data['amount'] >= $settings['min'] && $data['amount'] <= $settings['max'])
							{
								$op['fields']['amount'] = [
									'label'=>trans('user/withdrawal.amount'),
									'attr'=>[
										'min'=>$settings['min'],
										'max'=>$settings['max'],
										'data-tooshort'=>trans('validation.min.numeric', ['attribute'=>trans('user/withdrawal.amount'), 'min'=>$settings['min']]),
										'data-tooLong'=>trans('validation.max.numeric', ['attribute'=>trans('user/withdrawal.amount'), 'max'=>$settings['max']]),
										'value'=>(double) $data['amount'],
										'name'=>'amount',
										'step'=>(1 / pow(10, $this->commonObj->getDecimalPlaces($data['currency_id'])))
									]
								];
								$op['payouts'] = $this->withdrawalObj->getUserPaymentInfo($data);
								if (!empty($op['payouts'])) {
									$op['title'] = $paymentType->withdrawal_payment_type;
									$op['desc'] = $paymentType->withdrawal_payment_desc;
									$op['charge'] = CommonLib::currency_format($settings['charge'], $data['currency_id']);
									$op['notes'] = trans('user/withdrawal.payment_details_notes');
									$this->session->set('Withdraw.saveWithdraw', $data);
									$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
								} else {
									$op['msg'] = 'Account Not Available';
									$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
								}
							}
							else
							{
								$op['msg'] = trans('withdrawal.minimum_bal_err', ['min_amount'=>$settings['min'].' '.$settings['currency_code']]);
							}
						}
						else
						{
							$op['msg'] = trans('general.please_contact_administrator');
						}
					}	
							
					
					
					$this->statusCode = $this->config->get('httperr.SUCCESS');
				}
				else
				{
					$op['msg'] = trans('general.please_contact_administrator');
				}
			} else {
				$op['msg'] = trans('user/withdrawal.insufficient_bal');
			}	
        }
        else
        {
            $op['msg'] = trans('user/withdrawal.insufficient_bal');
        }				
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	
	
	public function ConfirmWithdraw ()
    {
		$op = [];
		//$op['success'] = '';	
        if ($this->session->has('Withdraw.saveWithdraw'))
        {			
			$data = $this->session->get('Withdraw.saveWithdraw');	
			
			if (isset($this->request->security_pin))
            {
				if (empty($this->userSess->security_pin))
				{					
					$data = [];
					$data['security_pin'] = $this->request->security_pin;
					$data['account_id'] = $this->userSess->account_id;					
					if ($res = $this->apiaffObj->saveProfilePIN($data))
					{						
						$this->userSess->security_pin = md5($this->request->security_pin);			                       
						$this->session->set($this->sessionName, (array) $this->userSess);						
						//CommonLib::notify($this->userSess->account_id, 'set_profile_pin', ['pin'=>$data['security_pin']]);
					}
				}
					
				if (isset($this->userSess->security_pin) && !empty($this->userSess->security_pin))
				{					
					if ($this->userSess->security_pin == md5($this->request->security_pin))
					{						
						if ($data['account_details'] = $this->withdrawalObj->getUserPaymentInfo($data))
						{							
							$settings = $this->withdrawalObj->get_balance_bycurrency($data);							
							if (!empty($settings))
							{
								//$data['amount'] = isset($data['amount']) ? $data['amount'] : $settings['balance'];
								$data['breakdowns'] = ['vim'=>[$data['currency']=>$data['amount']]];								
								if ($data['amount'] >= $settings['min'] && $data['amount'] <= $settings['max'])
								{
									$proceed = true;
									$total_breakdowns = 0;
									if (isset($data['breakdowns']) && !empty($data['breakdowns']))
									{
										foreach ($settings['breakdowns'] as $balance_breakdowns)
										{
											if (isset($data['breakdowns'][$balance_breakdowns->wallet_code][$balance_breakdowns->currency]))
											{
												$dreakdown = $data['breakdowns'][$balance_breakdowns->wallet_code][$balance_breakdowns->currency];
												if ($proceed && $dreakdown > 0 && ($dreakdown < $balance_breakdowns->attr->min || $dreakdown > $balance_breakdowns->attr->max))
												{
													$proceed = false;
												}
												$total_breakdowns+=$dreakdown;
											}
										}
										if ($proceed && $total_breakdowns == $data['amount'])
										{
											unset($settings['breakdowns']);
											$data = array_merge((array) $data, $settings);											
											if ($id = $this->withdrawalObj->saveWithdrawal($data))
											{												
												$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
												$this->session->forget('Withdraw');
												$postdata = [];
												$postdata['id'] = $id;
												$postdata['account_id'] = $this->userSess->account_id;
												$op['details'] = $this->withdrawalObj->getWithdrawalDetails($postdata);
												$op['balance'] = $this->withdrawalObj->getWalletBalance($this->userSess->account_id, $this->userSess->currency_id, $this->config->get('constants.CASHBACK_CREDIT_WALLET'), true);												
												//$op['msg'] = trans('user/withdrawal.withdraw_success', ['withdraw_id'=>$id, 'bank'=>$data['account_details']['b_bank_name']]);
												$op['success'] = 'SUCCESS!';
												$op['msg'] = trans('user/withdrawal.request_updated_successfully', ['wid'=>$id, 'bname'=>'SBI']);
											}
											else
											{
												$this->statusCode = 500;
												$op['msg'] = trans('general.something_wrong');
											}											
										}
										else
										{
											$op['msg'] = trans('user/withdrawal.invalid_breakdown');
										}
									}
									else
									{
										$op['msg'] = trans('user/withdrawal.invalid_breakdown');
									}
								}
								else if ($data['amount'] > $settings['max'])
								{
									$op['msg'] = trans('user/withdrawal.insufficient_bal');
								}
								else if ($data['amount'] < $settings['min'])
								{
									$op['msg'] = trans('user/withdrawal.min_amount', ['min'=>$settings['min']]);
								}
							}
							else
							{
								$op['msg'] = trans('general.please_contact_administrator');
							}
						}
						else
						{
							$op['msg'] = trans('general.invalid_payout_id');
						}					
					}
					else
					{
						$op['msg'] = trans('user/account.invalid_security');
						$op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
					}
				}
			}
			else if (isset($this->request->auth_status) && ($this->request->auth_status == true))
            {				
				if ($data['account_details'] = $this->withdrawalObj->getUserPaymentInfo($data))
				{							
					$settings = $this->withdrawalObj->get_balance_bycurrency($data);							
					if (!empty($settings))
					{
						//$data['amount'] = isset($data['amount']) ? $data['amount'] : $settings['balance'];
						$data['breakdowns'] = ['vim'=>[$data['currency']=>$data['amount']]];								
						if ($data['amount'] >= $settings['min'] && $data['amount'] <= $settings['max'])
						{
							$proceed = true;
							$total_breakdowns = 0;
							if (isset($data['breakdowns']) && !empty($data['breakdowns']))
							{
								foreach ($settings['breakdowns'] as $balance_breakdowns)
								{
									if (isset($data['breakdowns'][$balance_breakdowns->wallet_code][$balance_breakdowns->currency]))
									{
										$dreakdown = $data['breakdowns'][$balance_breakdowns->wallet_code][$balance_breakdowns->currency];
										if ($proceed && $dreakdown > 0 && ($dreakdown < $balance_breakdowns->attr->min || $dreakdown > $balance_breakdowns->attr->max))
										{
											$proceed = false;
										}
										$total_breakdowns+=$dreakdown;
									}
								}
								if ($proceed && $total_breakdowns == $data['amount'])
								{
									unset($settings['breakdowns']);
									$data = array_merge((array) $data, $settings);											
									if ($id = $this->withdrawalObj->saveWithdrawal($data))
									{											
										$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
										$this->session->forget('Withdraw');
										$postdata = [];
										$postdata['id'] = $id;
										$postdata['account_id'] = $this->userSess->account_id;
										$op['details'] = $this->withdrawalObj->getWithdrawalDetails($postdata);
										$op['balance'] = $this->withdrawalObj->getWalletBalance($this->userSess->account_id, $this->userSess->currency_id, $this->config->get('constants.CASHBACK_CREDIT_WALLET'), true);												
										//$op['msg'] = trans('user/withdrawal.withdraw_success', ['withdraw_id'=>$id, 'bank'=>$data['account_details']['b_bank_name']]);
										$op['success'] = 'SUCCESS!';
										$op['msg'] = trans('user/withdrawal.request_updated_successfully', ['wid'=>$id, 'bname'=>'SBI']);
									}
									else
									{
										$this->statusCode = 500;
										$op['msg'] = trans('general.something_wrong');
									}											
								}
								else
								{
									$op['msg'] = trans('user/withdrawal.invalid_breakdown');
								}
							}
							else
							{
								$op['msg'] = trans('user/withdrawal.invalid_breakdown');
							}
						}
						else if ($data['amount'] > $settings['max'])
						{
							$op['msg'] = trans('user/withdrawal.insufficient_bal');
						}
						else if ($data['amount'] < $settings['min'])
						{
							$op['msg'] = trans('user/withdrawal.min_amount', ['min'=>$settings['min']]);
						}
					}
					else
					{
						$op['msg'] = trans('general.please_contact_administrator');
					}
				}
				else
				{
					$op['msg'] = trans('general.invalid_payout_id');
				}
			}
			else
			{
				$op['msg'] = trans('user/account.generate_profile_pin');
			}			
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
	}	
}
