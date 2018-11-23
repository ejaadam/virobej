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
            $op['payment_fields'] = CommonNotifSettings::getHTMLValidation('user.withdraw-fund.get-bank-details');
            $op['vim_balance'] = isset($balance['vimoney_bal']) ? $balance['vimoney_bal'] : 0;
			//print_r($op);exit;
			
            /* if (isset($op['payment_fields']['account_details.b_bank_name']))
            {
                $op['payment_fields']['account_details.b_bank_name']['list'] = $this->withdrawalObj->getBankList($data);
            } */
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
            //$op['notes'] = trans('general.payment_details_notes');
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('user/general.please_contact_administrator');
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
		$payment_types = $this->withdrawalObj->getWithdrawalPaymentTypes($data);
		
		if (!empty($payment_types))
		{
			$data['payment_types'] = $op['payment_types'] = $payment_types;			
			$this->session->set('Withdraw.PaymentDetails', $data);
			if ($this->session->has('Withdraw.PaymentDetails'))
			{
				$data = $this->session->get('Withdraw.PaymentDetails');
				$data['payment_type'] 	 = 'BANK_TRANSFER';
				$data['amount'] 		 = $this->request->account_details['b_amount'];
				$data['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.'.$data['payment_type']);
				$data['paymentType'] = $paymentTypeDetails = $this->withdrawalObj->paymentTypeDetails($data);				
				$settings = $this->withdrawalObj->get_balance_bycurrency($data);				
				if (!empty($settings))
				{					
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
                                /* if ($id = $this->withdrawalObj->saveWithdrawal($data))
                                {
                                    $this->statusCode = $this->config->get('httperr.SUCCESS');
                                    $this->session->forget('Withdraw');
                                    $postdata = [];
                                    $postdata['id'] = $id;
                                    $postdata['account_id'] = $this->userSess->account_id;
                                    $op['details'] = $this->withdrawalObj->getWithdrawalDetails($postdata);
                                    $op['balance'] = $this->withdrawalObj->getWalletBalance($this->userSess->account_id, $this->userSess->currency_id, $this->config->get('constants.CASHBACK_CREDIT_WALLET'), true);
                                    $op['msg'] = trans('withdrawal.request_updated_successfully', ['payment_type'=>$data['paymentType']->withdrawal_payment_type]);
                                }
                                else
                                {
                                    $this->statusCode = 500;
                                    $op['msg'] = trans('general.something_wrong');
                                } */
                                $this->session->put('Withdraw.ConfirmWithdraw', $data);
                                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                                $op['msg'] = trans('user/withdrawal.confirm', ['payment_type'=>$data['paymentType']->withdrawal_payment_type, 'amount'=>CommonLib::currency_format($data['amount'], $data['currency_id'])]);
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
					$op['msg'] = 'Please Contact Administrator';
					//$op['msg'] = trans('general.please_contact_administrator');
				}
				
			}
			else 
			{
				$op['msg'] = trans('user/account.not_accessable');
				$op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
			}
		}		
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function ConfirmWithdraw ()
    {
		$op = [];
		$op['success'] = '';	
        if ($this->session->has('Withdraw.ConfirmWithdraw'))
        {			
			$data = $this->session->get('Withdraw.ConfirmWithdraw');	
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
						if ($id = $this->withdrawalObj->saveWithdrawal($data))
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							$postdata = [];
							$postdata['id'] = $id;
							$postdata['account_id'] = $this->userSess->account_id;
							$op['details'] = $this->withdrawalObj->getWithdrawalDetails($postdata);													
							$this->session->forget('Withdraw');
							//$op['msg'] = trans('user/withdrawal.request_updated_successfully', ['wid'=>$op['details']->wd_id, 'payment_type'=>$data['paymentType']->withdrawal_payment_type]);
							$op['success'] = 'SUCCESS!';
							$op['msg'] = trans('user/withdrawal.request_updated_successfully', ['wid'=>$op['details']->wd_id, 'bname'=>'SBI']);
						}
						else
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
							$op['msg'] = trans('general.something_wrong');
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
				if ($id = $this->withdrawalObj->saveWithdrawal($data))
				{
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$postdata = [];
					$postdata['id'] = $id;
					$postdata['account_id'] = $this->userSess->account_id;
					$op['details'] = $this->withdrawalObj->getWithdrawalDetails($postdata);											
					$this->session->forget('Withdraw');		
					$op['success'] = 'SUCCESS!';	
					$op['msg'] = trans('user/withdrawal.request_updated_successfully', ['wid'=>$op['details']->wd_id, 'bname'=>'SBI']);
				}
				else
				{
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					$op['msg'] = trans('general.something_wrong');
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
