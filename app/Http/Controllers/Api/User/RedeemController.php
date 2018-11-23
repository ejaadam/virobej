<?php

namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Api\User\UserApiBaseController;
use App\Models\Api\User\RedeemModel;
use App\Models\Api\User\AffModel;
use CommonLib;
use App\Helpers\CommonNotifSettings;
use Config;

class RedeemController extends UserApiBaseController
{    
    public function __construct ()
    {		
        parent::__construct();		
		$this->apiaffObj = new AffModel();
        $this->redeemObj = new RedeemModel();
    }
	
	public function getStoreSearch ()
    {		
        $op = array();
        $postdata = $this->request->all();				
		if (!empty($this->userSess->is_affiliate)) 
		{   
			if (!empty($this->geo->current->country_id))
			{
				$postdata['country_id'] = $this->geo->current->country_id;
				$postdata['account_id'] = $this->userSess->account_id;			
				$store = $this->redeemObj->getStoreSearch($postdata);		
				// print_r($store);exit;
				$this->session->forget('REDEEM');
				if (!empty($store))
				{
					$store->account_id = $this->userSess->account_id;				
					if ($store->redeem)
					{
						$this->session->set('REDEEM.GetWallets', $store);					
						$op['data']['merchant'] = [
							'mrid'=>['label'=>trans('user/account.supplier_id'), 'value'=>$store->store_code],
							'mrbusiness_name'=>['label'=>trans('user/account.mrbusiness_name'), 'value'=>$store->store_name],                        
							'currency'=>['code'=>$store->code, 'symbol'=>$store->currency_symbol],
							'location'=>['label'=>trans('user/account.location'), 'value'=>$store->address],
							'logo'=>$store->store_logo,
							//'min_amount'=>$store->min_amount
							'min_amount'=>100
						];		
						$op['generate_security_pin'] = !empty($this->userSess->security_pin) ? false : true;
						$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					}
					else
					{
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						$op['msg'] = trans('general.store_not_offering_this_service');
					}
				}
				else
				{
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					$op['msg'] = 'Outlet Not Found';
				}
			}
			else
			{
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] = trans('general.set_location');
			}
		} else {
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$op['msg'] = trans('general.not_an_affiliate_user');
		}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function getWallets ()
    {
        $op = array();
        $postdata = $this->request->all();
        $bonus_wallet = array('vis', 'vib');
        $min_bonus = 0;
        if ($this->session->has('REDEEM.GetWallets'))
        {
            $store = $this->session->get('REDEEM.GetWallets');							
			if (isset($this->request->security_pin) && ($this->request->auth_type == 1))
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
				
				if ($this->userSess->security_pin == md5($this->request->security_pin))
				{
					$store->account_id = $this->userSess->account_id;
					$store->amount = $this->request->amount;			
					$postdata['currency_id'] = $this->userSess->currency_id;						
					if ($store->redeem)
					{
						$store->redeem_wallet = $postdata['wallet'];
						$store->amount = $postdata['amount'];
						//$store->amount = sprintf('%.2f', $postdata['amount']);
						//$postdata['amount'] = sprintf('%.2f', $postdata['amount']);					
						// Bonus Wallet
						$bw = $bonus_wallets = $this->redeemObj->getRedeemWallet((array) $store, $postdata);								
						$bonues = [];
						if (!empty($bw))
						{
							foreach ($bw as $bonus)
							{
								$bonues[] = $bonus->store_redeem_amount;
							}
							if (!empty($bonues))
							{
								$min_bonus = min($bonues);
							}							
							array_walk($bonus_wallets, function(&$result, $key) use ($store, $postdata)
							{
								$msg = ($result->redeem == true) ? 'Balance Available' : 'Insufficient Balance';
								$result = ['wallet'=>$result->wallet,
									'wallet_code'=>$result->wallet_code,
									'redeem_amount'=>$result->store_redeem_amount,
									'max_redeem_amt'=>$result->store_redeem_amount,
									'max_redeem_amt_with_decimal'=>sprintf('%.2f', $result->store_redeem_amount),
									'fmax_redeem_amt'=>CommonLib::currency_format($result->store_redeem_amount, $result->store_currency_id, true, false),
									//'currency_id'=>$result->store_currency_id,
									//'currency_code'=>$store->code,
									'user'=>['currency'=>$result->user_currency_id, 'redeem_amt'=>$result->user_redeem_amount, 'symbol'=>$result->symbol, 'code'=>$result->code],
									'store'=>['currency'=>$store->currency_id, 'redeem_amt'=>$result->store_redeem_amount, 'symbol'=>$store->currency_symbol, 'code'=>$store->code],
									'balance'=>$result->balance,
									'balance_to_pay' => $postdata['amount'] - $result->store_redeem_amount,
									'fbalance_to_pay' => sprintf('%.2f', $postdata['amount'] - $result->store_redeem_amount),
									'msg'=>$msg, 'is_redeem'=>$result->redeem];
							});
							$store->redeem_bonus_wallets = $bonus_wallets;
							$op['data']['bonus_wallet'] = $bonus_wallets;
							
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						}
						else
						{
							$op['data']['bonus_wallet'][] = ['msg'=>'Balance not Available', 'is_redeem'=>false];
							$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						}
						
						// Virob Credit Wallet
						$postdata['min_bonus'] = $min_bonus;
						$vim = $vimoney_credit = $this->redeemObj->getCashbackWallet((array) $store, $postdata);				
						if (!empty($vim))
						{
							array_walk($vimoney_credit, function(&$result, $key) use ($store)
							{
								$msg = ($result->redeem == true) ? 'Balance Available' : 'Insufficient Balance';
								$result = ['wallet'=>$result->wallet,
									'wallet_code'=>$result->wallet_code,
									'redeem_amount'=>$result->store_redeem_amount,
									'max_redeem_amt'=>$result->store_redeem_amount,
									'fmax_redeem_amt'=>CommonLib::currency_format($result->store_redeem_amount, $result->store_currency_id, true, false),
									//'currency_id'=>$result->store_currency_id,
									//'currency_code'=>$store->code,
									'user'=>['currency'=>$result->user_currency_id, 'redeem_amt'=>$result->user_redeem_amount, 'symbol'=>$result->symbol, 'code'=>$result->code],
									'store'=>['currency'=>$store->currency_id, 'redeem_amt'=>$result->store_redeem_amount, 'symbol'=>$store->currency_symbol, 'code'=>$store->code],
									'balance'=>$result->balance,
									'msg'=>$msg, 'is_redeem'=>$result->redeem];
							});
							$store->vimoney_wallet = $vimoney_credit;
							$this->session->set('REDEEM.vimoney_wallet', $vimoney_credit);
							//unset($xpayback_credit[0]['user']);
							$op['data']['vimoney_wallet'] = $vimoney_credit;
						}
						else
						{
							$op['data']['vimoney_wallet'][] = ['msg'=>'Balance not Available', 'is_redeem'=>false];
						}		
							
						if (!empty($bonus_wallets))
						{
							// Patment Gateway Types
							$payment_types = $this->redeemObj->getPaymentTypes(['account_id'=>$this->userSess->account_id, 'currency_id'=>$store->currency_id, 'country_id'=>$store->country_id, 'amount'=>$store->amount]);	
							//print_r($payment_types);exit;
							if (!empty($payment_types))
							{
								foreach ($payment_types as $pt)
								{
									if ($pt->id == 'vim' && isset($op['data']['vimoney_wallet'][0]) && $op['data']['vimoney_wallet'][0]['is_redeem'] == false)
									{
										continue;
									}
									else
									{
										$op['data']['payment_types'][] = $pt;
									}
								}
							}
							//print_r($payment_types);exit;
							$store->payment_types = $op['data']['payment_types'];                    
							$this->statusCode = $this->config->get('httperr.SUCCESS');
							$op['msg'] = 'Balance Available';
						}
						else
						{
							$payment_types = $this->redeemObj->getPaymentTypes(['account_id'=>$this->userSess->account_id, 'currency_id'=>$store->currency_id, 'country_id'=>$store->country_id, 'amount'=>$store->amount]);
							if (!empty($payment_types))
							{
								foreach ($payment_types as $pt)
								{
									if ($pt->id == 'xpc' && $op['data']['vimoney_wallet'][0]['is_redeem'] == false)
									{
										continue;
									}
									else
									{
										$op['data']['payment_types'][] = $pt;
									}
								}
							}
							$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
							//$op['msg'] = trans('general.insufficient_balance_to_redeem');
							$op['msg'] = 'You do not have enough balance to proceed further.';
						}
						if ($op['status'] == $this->config->get('httperr.SUCCESS'))
						{
							$this->session->set('REDEEM.RedeemWalletValidate', $store);
						}
					}
					else
					{
						$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						$op['msg'] = trans('general.store_not_offering_this_service');
					}
				}
				else
				{
					$op['msg'] = trans('user/account.invalid_security');
					$op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
				}  
			}
			
			if (isset($this->request->auth_status) && ($this->request->auth_type == 2)) {
				$store->account_id = $this->userSess->account_id;
				$store->amount = $this->request->amount;			
				$postdata['currency_id'] = $this->userSess->currency_id;						
				if ($store->redeem)
				{
					$store->redeem_wallet = $postdata['wallet'];
					$store->amount = $postdata['amount'];
					//$store->amount = sprintf('%.2f', $postdata['amount']);
					//$postdata['amount'] = sprintf('%.2f', $postdata['amount']);					
					// Bonus Wallet
					$bw = $bonus_wallets = $this->redeemObj->getRedeemWallet((array) $store, $postdata);								
					$bonues = [];
					if (!empty($bw))
					{
						foreach ($bw as $bonus)
						{
							$bonues[] = $bonus->store_redeem_amount;
						}
						if (!empty($bonues))
						{
							$min_bonus = min($bonues);
						}							
						array_walk($bonus_wallets, function(&$result, $key) use ($store, $postdata)
						{
							$msg = ($result->redeem == true) ? 'Balance Available' : 'Insufficient Balance';
							$result = ['wallet'=>$result->wallet,
								'wallet_code'=>$result->wallet_code,
								'redeem_amount'=>$result->store_redeem_amount,
								'max_redeem_amt'=>$result->store_redeem_amount,
								'max_redeem_amt_with_decimal'=>sprintf('%.2f', $result->store_redeem_amount),
								'fmax_redeem_amt'=>CommonLib::currency_format($result->store_redeem_amount, $result->store_currency_id, true, false),
								//'currency_id'=>$result->store_currency_id,
								//'currency_code'=>$store->code,
								'user'=>['currency'=>$result->user_currency_id, 'redeem_amt'=>$result->user_redeem_amount, 'symbol'=>$result->symbol, 'code'=>$result->code],
								'store'=>['currency'=>$store->currency_id, 'redeem_amt'=>$result->store_redeem_amount, 'symbol'=>$store->currency_symbol, 'code'=>$store->code],
								'balance'=>$result->balance,
								'balance_to_pay' => $postdata['amount'] - $result->store_redeem_amount,
								'fbalance_to_pay' => sprintf('%.2f', $postdata['amount'] - $result->store_redeem_amount),
								'msg'=>$msg, 'is_redeem'=>$result->redeem];
						});
						$store->redeem_bonus_wallets = $bonus_wallets;
						$op['data']['bonus_wallet'] = $bonus_wallets;
						
						$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					}
					else
					{
						$op['data']['bonus_wallet'][] = ['msg'=>'Balance not Available', 'is_redeem'=>false];
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					}
					
					// Virob Credit Wallet
					$postdata['min_bonus'] = $min_bonus;
					$vim = $vimoney_credit = $this->redeemObj->getCashbackWallet((array) $store, $postdata);				
					if (!empty($vim))
					{
						array_walk($vimoney_credit, function(&$result, $key) use ($store)
						{
							$msg = ($result->redeem == true) ? 'Balance Available' : 'Insufficient Balance';
							$result = ['wallet'=>$result->wallet,
								'wallet_code'=>$result->wallet_code,
								'redeem_amount'=>$result->store_redeem_amount,
								'max_redeem_amt'=>$result->store_redeem_amount,
								'fmax_redeem_amt'=>CommonLib::currency_format($result->store_redeem_amount, $result->store_currency_id, true, false),
								//'currency_id'=>$result->store_currency_id,
								//'currency_code'=>$store->code,
								'user'=>['currency'=>$result->user_currency_id, 'redeem_amt'=>$result->user_redeem_amount, 'symbol'=>$result->symbol, 'code'=>$result->code],
								'store'=>['currency'=>$store->currency_id, 'redeem_amt'=>$result->store_redeem_amount, 'symbol'=>$store->currency_symbol, 'code'=>$store->code],
								'balance'=>$result->balance,
								'msg'=>$msg, 'is_redeem'=>$result->redeem];
						});
						$store->vimoney_wallet = $vimoney_credit;
						$this->session->set('REDEEM.vimoney_wallet', $vimoney_credit);
						//unset($xpayback_credit[0]['user']);
						$op['data']['vimoney_wallet'] = $vimoney_credit;
					}
					else
					{
						$op['data']['vimoney_wallet'][] = ['msg'=>'Balance not Available', 'is_redeem'=>false];
					}		
						
					if (!empty($bonus_wallets))
					{
						// Patment Gateway Types
						$payment_types = $this->redeemObj->getPaymentTypes(['account_id'=>$this->userSess->account_id, 'currency_id'=>$store->currency_id, 'country_id'=>$store->country_id, 'amount'=>$store->amount]);	
						//print_r($payment_types);exit;
						if (!empty($payment_types))
						{
							foreach ($payment_types as $pt)
							{
								if ($pt->id == 'vim' && isset($op['data']['vimoney_wallet'][0]) && $op['data']['vimoney_wallet'][0]['is_redeem'] == false)
								{
									continue;
								}
								else
								{
									$op['data']['payment_types'][] = $pt;
								}
							}
						}
						//print_r($payment_types);exit;
						$store->payment_types = $op['data']['payment_types'];                    
						$this->statusCode = $this->config->get('httperr.SUCCESS');
						$op['msg'] = 'Balance Available';
					}
					else
					{
						$payment_types = $this->redeemObj->getPaymentTypes(['account_id'=>$this->userSess->account_id, 'currency_id'=>$store->currency_id, 'country_id'=>$store->country_id, 'amount'=>$store->amount]);
						if (!empty($payment_types))
						{
							foreach ($payment_types as $pt)
							{
								if ($pt->id == 'xpc' && $op['data']['vimoney_wallet'][0]['is_redeem'] == false)
								{
									continue;
								}
								else
								{
									$op['data']['payment_types'][] = $pt;
								}
							}
						}
						$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						//$op['msg'] = trans('general.insufficient_balance_to_redeem');
						$op['msg'] = 'You do not have enough balance to proceed further.';
					}
					if ($op['status'] == $this->config->get('httperr.SUCCESS'))
					{
						$this->session->set('REDEEM.RedeemWalletValidate', $store);
					}
				}
				else
				{
					$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					$op['msg'] = trans('general.store_not_offering_this_service');
				}
			}
			
			          
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }    
	
	public function ValidateWallet ()
    {
        $op = array();
        $pgArr = ['credit-card', 'debit-card', 'netbanking'];
        if ($this->session->has('REDEEM.RedeemWalletValidate'))
        {
			$store = $this->session->get('REDEEM.RedeemWalletValidate');
            $postdata = $this->request->all();			
            $postdata['user_currency_id'] = $this->userSess->currency_id;            
            $postdata['user_currency_code'] = $this->userSess->currency_code;            
            $postdata['user_currency_symbol'] = $this->userSess->currency_symbol;       		
            $postdata['wallet'] = $store->redeem_wallet;       				
            if (isset($postdata['wallet']))
            {				
                $postdata['bonus_wallets'] = $store->redeem_bonus_wallets;							
                $result = $this->redeemObj->redeemWalletValidate((array) $store, $postdata);		
				//print_r($result);exit;	
                if ($result['status'] == $this->config->get('httperr.SUCCESS'))
                {
                    $op['bonus_wallet'] = $bonus_wallet = $result['bonus_wallet'];
                    $op['status'] = $this->statusCode = $result['status'];
                    $store->wallet = $bonus_wallet[0]['wallet'];
                    $store->redeem_amount = $bonus_wallet[0]['redeem_amount'];
                    $store->balance_to_pay = $bonus_wallet[0]['balance_to_pay'];
                }
                else
                {
                    $op['msg'] = $result['msg'];
                    $op['status'] = $this->statusCode = $result['status'];
                    return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
                }
            }			
            if (isset($postdata['opayment_type']) && ($postdata['opayment_type'] == 'vim'))
            {
                $postdata['amount'] = $store->amount;
                if (isset($postdata['wallet']))
                {
                    $postdata['vimoney'] = $bonus_wallet[0]['balance_to_pay'];
                }
                else
                {
                    $postdata['vimoney'] = $store->amount;
                }				
                $postdata['vimoney_wallet'] = $this->session->get('REDEEM.vimoney_wallet');						
                if (!empty($postdata['vimoney_wallet']))
                {
                    $res = $this->redeemObj->redeemVimoneyWalletValidate((array) $store, $postdata);	
                    if ($res['status'] == $this->config->get('httperr.SUCCESS'))
                    {
                        unset($op['status']);
                        $op['vimoney_wallet'] = $vimoney_wallet = $res['vimoney_wallet'];
                        $op['status'] = $this->statusCode = $res['status'];
                    }
                    else
                    {
                        unset($op['bonus_wallet']);
                        $op['msg'] = $res['msg'];
                        $op['status'] = $this->statusCode = $res['status'];
                        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
                    }
                }
                else
                {
                    unset($op['bonus_wallet']);
                    $op['msg'] = 'You do not have enough balance to redeem from Vi-Money Credit';
                    $op['status'] = $this->statusCode = 422;
                    return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
                }
            }
            else if (isset($postdata['opayment_type']) && ($postdata['opayment_type'] == 'cash'))
            {
                if (isset($postdata['wallet']))
                {
                    $postdata['amount'] = $bonus_wallet[0]['balance_to_pay'];
                    $postdata['cash'] = $bonus_wallet[0]['balance_to_pay'];
                }
                else
                {
                    $postdata['amount'] = $store->amount;
                    $postdata['cash'] = $store->amount;
                }				
                $store->system_received_amount = $store->amount - $postdata['cash'];
                $store->bill_amount = $store->amount;				
                if ($result = $this->redeemObj->checkSupplierCreditLimit((array) $store))
                {					
                    unset($op['status']);
                    $op['cash'] = $cash = ['amount'=>$postdata['cash'],
                        'currency_id'=>$store->currency_id,
                        'currency_code'=>$store->code,
                        'balance_to_pay'=>$postdata['amount'] - $postdata['cash']];
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else
                {					
                    $op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
                    $op['msg'] = trans('user/cashback.store_not_available');
                }
            }        
            $op['store'] = $store;
			$store->is_redeem_otp_required = 0;
            $op['is_otp_required'] = $store->is_redeem_otp_required ? true : false;            
            $this->session->set($store->is_redeem_otp_required ? 'REDEEM.ProceedRedeemOTP' : 'REDEEM.ConfirmRedeem', $store);
            //$this->session->forget('REDEEM.xpayback_wallet');
            $this->session->set('REDEEM.virob_payment', $op);			
            //unset($op['bonus_wallet'][0]['user']);
            //unset($op['xpayback_wallet'][0]['user']);
            unset($op['store']);
			return $this->confirmRedeem();
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function proceedRedeemOTP ()
    {
        $op = array();
        if ($this->session->has('REDEEM.ProceedRedeemOTP'))
        {
            $store = $this->session->get('REDEEM.ProceedRedeemOTP');    
			//print_r($store);exit;	
            //$op['code'] = $store->otp = rand(100000, 999999);
			$op['code'] = $store->otp = $this->session->has('REDEEM.ConfirmRedeem') ? $this->session->get('REDEEM.ConfirmRedeem')->otp : rand(100000, 999999);
            $op['hash_code'] = md5($store->code);
            $this->session->set('REDEEM.ConfirmRedeem', $store);
			$data = [
                'code'=>$store->otp,
                'account_id'=>$this->userSess->account_id,
                'bill_amount'=>CommonLib::currency_format($store->amount, $store->currency_id, true, false),
                'redeem_amount'=>CommonLib::currency_format($store->redeem_amount, $store->currency_id, true, false),
                'balance_to_pay'=>CommonLib::currency_format($store->balance_to_pay, $store->currency_id, true, false),
                    ];				
			CommonNotifSettings::sms($store->mobile_no, \Lang::get('user/account.redeem.otp_msg', ['code'=>$store->otp, 'name'=>$this->userSess->full_name, 'bill_amount'=>CommonLib::currency_format($store->amount, $store->currency_id, true, false), 'store'=>$store->store_name, 'wallet'=>$store->wallet]));					
            $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['msg'] = trans('user/account.code_sent_to_merchant_mobile');
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function confirmRedeem ()
    {
		$data = [];		
        $op = array();
        if ($this->session->has('REDEEM.ConfirmRedeem'))
        {			
            $info = $cash = $vim = '';
            $store = $this->session->get('REDEEM.ConfirmRedeem');	
			//print_r($store);exit;	
            if (!$store->is_redeem_otp_required || ($store->is_redeem_otp_required && $store->otp == $this->request->code))
            {
                $payments = $this->session->get('REDEEM.virob_payment');				
                $payments['account_id'] = $this->userSess->account_id;
                $payments['full_name'] = $this->userSess->full_name;      
				$supplier_account_id = $store->supplier_account_id;
                $payments['user_currency_id'] = $this->userSess->currency_id;	 				
                if ($payments)
                {
                    // Bonus & Cashback Wallet
                    if (isset($payments['bonus_wallet']) && !empty($payments['bonus_wallet']))
                    {
						//print_r(array_merge((array) $store, $payments));exit;
                        $info = $this->redeemObj->confirmRedeem(array_merge((array) $store, $payments));	
						$like_status = 0;
						$store_codes = !empty($this->session->get('likes')) ? $this->session->get('likes') : [];
						if (in_array($store->store_code, $store_codes))
						{
							$like_status = 1;
						}						
                        if (!empty($info))
                        {                           
							$op['data'] = [
                                'msg'=>trans('user/account.redeem.success_msg'),
								'seller'=>$store->company_name,
                                'remarks'=>trans('user/account.redeem.remarks'),
								'bill_amount'=>CommonLib::currency_format($store->amount, $info->currency_id, true, false),
								'store'=>'To '.$store->store_name,
								'store_id'=>$store->store_code,
								'payment_id'=>$info->payment_id,
								'order_code'=>$info->order_code,
								'currency_code'=>$store->code,	
								'bonus_wallet_remarks'=>trans('user/account.redeem.bonus_wallet_remarks', ['amount'=>CommonLib::currency_format($info->redeem_amount, $info->currency_id, true, false), 'wallet'=>$info->wallet]),
								'like_status'=>$like_status,   
                                ];
                            $payments['order_id'] = $store->order_id = $info->order_id;
                            $redeem = CommonLib::currency_format($info->redeem_amount, $store->currency_id, false);
                            $balance = CommonLib::currency_format($info->current_balance, $info->currency_id, true, false);
                            $op['data']['bonus_wallet'] = [
                                'redeem_success'=>trans('user/account.redeem.success_msg'),
                                'supplier'=>$store->company_name,
                                'store'=>$store->store_name,
                                //'fav_id'=>$store->fav_id,
                                'redeem_amount'=>(float) number_format($info->redeem_amount, 2, '.', ''),
                                'currency_symbol'=>$redeem->currency_symbol,
                                'code'=>$store->code,
                                'wallet'=>$info->wallet,
                            ]; 
							
							
							$bp_redeem_amount = (isset($info->redeem_amount) && !empty($info->redeem_amount)) ? $info->redeem_amount : 20;
					
							// Vi-Money Wallet
							if (isset($payments['vimoney_wallet']) && !empty($payments['vimoney_wallet']))
							{
								$vim = $this->redeemObj->confirmVimoneyRedeem($payments);								
								if (!empty($vim))
								{
									$remaining_amt = CommonLib::currency_format($vim->redeem_amount, $vim->currency_id, true, true);
									$op['data']['vim'] = [
										'redeem_success'=>trans('user/account.redeem.success_msg'),
										'supplier'=>$store->company_name,
										'store'=>$store->store_name,
										//'fav_id'=>$store->fav_id,
										'redeem_amount'=>(float) number_format($vim->redeem_amount, 2, '.', ''),
										'currency_symbol'=>$redeem->currency_symbol,
										'code'=>$store->code,
										'wallet'=>$vim->wallet,
									];
									$op['data']['vim_remarks'] = trans('user/account.redeem.vim_remarks', ['amount'=>CommonLib::currency_format($vim->redeem_amount, $vim->currency_id, true, false), 'wallet'=>$vim->wallet]);
									/* Notification */
									CommonNotifSettings::notify('USER.REDEEM_THRU_VIM', $this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.USER'), ['wallet_name'=>'Vi-Money', 'redeem_amount'=>CommonLib::currency_format($vim->redeem_amount, $vim->currency_id, true, false), 'bill_amt'=>CommonLib::currency_format($store->amount, $store->currency_id, true, false), 'name'=>$this->userSess->full_name], false);
								}
							} 
							
							// Cash
							if (isset($payments['cash']) && !empty($payments['cash']))
							{							
								$cash = $this->redeemObj->confirmCashPayment($payments);	
								//print_r($cash);exit;
								if (!empty($cash))
								{
									$remaining_amt = CommonLib::currency_format($payments['cash']['amount'], $payments['cash']['currency_id'], true, true);
									$op['data']['cash'] = [
										'success'=>'Successfully paid at',
										'merchant'=>$store->company_name,
										'store'=>$store->store_name,
										//'fav_id'=>$store->fav_id,
										'amount'=>(float) number_format($payments['cash']['amount'], 2, '.', ''),
										'currency_symbol'=>$redeem->currency_symbol,
										'code'=>$payments['cash']['currency_code'],
										'currency_id'=>$payments['cash']['currency_id'],
										'type'=>'Cash'
									];
									$op['data']['cash_remarks'] = trans('user/account.redeem.cash_remarks', ['amount'=>CommonLib::currency_format($payments['cash']['amount'], $payments['cash']['currency_id'], true, false)]);
									/* Notification */
									CommonNotifSettings::notify('USER.REDEEM_THRU_CASH', $this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.USER'), ['paid_thru'=>'Cash', 'paid_amount'=>CommonLib::currency_format($payments['cash']['amount'], $payments['cash']['currency_id'], true, false), 'bill_amt'=>CommonLib::currency_format($store->amount, $store->currency_id, true, false), 'name'=>$this->userSess->full_name], false);									
									
								}
							}						
                        }
                        else
                        {
                            $this->statusCode = $this->config->get('httperr.CODE_ERROR');
                            $op['msg'] = trans('general.something_wrong');
                        }
                    } 
						
				
			/* $data['mobile'] = $store->staff_mobile;
			$data['email'] = $store->staff_email;
			$data['full_name'] = $this->userSess->full_name;
			$data['user_code'] = $this->userSess->user_code;
			//$data['bill_amount'] = $store->currency_symbol . ' ' . $store->amount;
			$data['bill_amount'] = CommonLib::currency_format($store->amount, $store->currency_id, true, false);
			$data['txnid'] = $info->trans_id;
			$data['sitename'] = $this->siteConfig->site_name;			
			CommonNotifSettings::notify('USER.REDEEM.CONFIRM', null, Config::get('constants.ACCOUNT_TYPE.USER'), $data, false, true, false, true, false);  */      			
				
			CommonNotifSettings::notify('USER.REDEEM.CONFIRM', $this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.USER'), [
				'merchant'=>$store->store_name,
				'bill_amount'=>CommonLib::currency_format($store->amount, $store->currency_id, true, false),
				'bp_trans_id'=>(isset($info->trans_id) && !empty($info->trans_id)) ? $info->trans_id : '',
				'bp_payment_id'=>(isset($info->payment_id) && !empty($info->payment_id)) ? $info->payment_id : '',
				'bp_wallet'=>(isset($info->wallet) && !empty($info->wallet)) ? $info->wallet : '',
				'bp_wallet_bal'=>(isset($info->current_balance) && !empty($info->current_balance)) ? CommonLib::currency_format($info->current_balance, $info->currency_id, true, false) : '',
				'bp_redeem_time'=>(isset($info->redeem_time) && !empty($info->redeem_time)) ? showUTZ($info->redeem_time, 'd-M-Y H:i:s') : '',
				'bp_redeem_date'=>(isset($info->redeem_time) && !empty($info->redeem_time)) ? showUTZ($info->redeem_time, 'd-M-Y') : '',
				'bp_redeem_amount'=>(isset($info->currency_id) && !empty($info->currency_id)) ? CommonLib::currency_format($bp_redeem_amount, $info->currency_id, true, false) : '',
				'vim_trans_id'=>(isset($vim->trans_id) && !empty($vim->trans_id)) ? $vim->trans_id : '',
				'vim_wallet'=>(isset($vim->wallet) && !empty($vim->wallet)) ? $vim->wallet : '',
				'remaining_amt'=>(isset($remaining_amt) && !empty($remaining_amt)) ? $remaining_amt : '',
			], true, false, false, true, false);
			

                    $store = array_filter((array) $store, function($s)
                    {
                        return is_array($s) || is_object($s) ? false : true;
                    });

                    $op['share']['email'] = ['title'=>trans('user/account.redeem.store_share.email.title', (array) $store), 'content'=>trans('user/account.redeem.store_share.email.content', (array) $store)];
                    $this->session->forget('REDEEM');
                    $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
            }
            elseif ($store->is_redeem_otp_required && $store->code != $this->request->code)
            {
                $op['msg'] = trans('user/account.redeem.invalid_otp');
            }
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
}
