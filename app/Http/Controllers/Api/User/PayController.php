<?php

namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Api\User\UserApiBaseController;
use App\Models\Api\User\PayModel;
use App\Models\Api\User\AffModel;
use CommonLib;

class PayController extends UserApiBaseController
{

    private $payObj;
    public function __construct ()
    {		
        parent::__construct();
		$this->apiaffObj = new AffModel();
        $this->payObj = new PayModel();
    }

    public function getStoreSearch ()
    {		
        $op = array();        
		$postdata = $this->request->all();	
		//print_r($this->geo);exit;	
        if (!empty($this->geo->current->country_id))
        {
            $postdata['country_id'] = $this->geo->current->country_id;
            $store = $this->payObj->getStoreSearch($postdata);				
            $this->session->forget('XPAY');
            if (!empty($store))
            {				
                if ($store->pay)
                {					
                    $this->session->set('XPAY.SetBillAmount', $store);
                    $op['data']['merchant'] = [
                        'mrid'=>['label'=>trans('user/account.supplier_id'), 'value'=>$store->store_code],
                        'mrbusiness_name'=>['label'=>trans('user/account.mrbusiness_name'), 'value'=>$store->store_name],
						'currency'=>['code'=>$store->code, 'symbol'=>$store->currency_symbol],
                        'location'=>['label'=>trans('user/account.location'), 'value'=>$store->address],
                        'logo'=>$store->store_logo,
						//'min_amount'=>$store->min_amount
                    ];
                    $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else
                {
                    $this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
                    $op['msg'] = trans('general.store_not_offering_this_service');
                }
            }
            else
            {
		        $this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('general.not_found', ['which'=>trans('general.in-store')]);
            }
        }
        else
        {
            $op['msg'] = trans('user/account.set_location');
			 $this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
        }
	    return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function setBillAmount ()
    {
		//print_r($this->userSess);exit;
        $op = array();
        if ($this->session->has('XPAY.SetBillAmount'))
        {
            $store = $this->session->get('XPAY.SetBillAmount');			
        $payment_limit = $this->payObj->getPayLimit(['country_id'=>$this->geo->current->country_id, 'currency_id'=>$store->currency_id]);	
			//print_r($payment_limit);exit;
            if ($payment_limit && $payment_limit->min_amount <= $this->request->amount && (empty($payment_limit->max_amount) || (!empty($payment_limit->max_amount) && $payment_limit->max_amount >= $this->request->amount)))
            {				
                $store->account_id = $this->userSess->account_id;
                $store->user_currency_id = $this->userSess->currency_id;
                $store->bill_amount = $this->request->amount;				
                $op['data'] = [
                    'mrid'=>['label'=>trans('user/account.supplier_id'), 'value'=>$store->store_code],
                    'mrbusiness_name'=>['label'=>trans('user/account.mrbusiness_name'), 'value'=>$store->store_name],
					'location'=>['label'=>trans('user/account.location'), 'value'=>$store->address],
					//'currency'=>['code'=>$store->code, 'symbol'=>$store->currency_symbol],
                    'bill_amount'=>['label'=>trans('user/account.bill_amount'), 'value'=>CommonLib::currency_format($store->bill_amount, $store->currency_id)],
                ];
                //$op['security_pin'] = !empty($this->userSess->security_pin) ? false : true;
                $op['generate_security_pin'] = !empty($this->userSess->security_pin) ? false : true;
                $this->session->set('XPAY.GetPaymentTypes', $store);				
                $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('user/account.cannot_pay_this_amount', ['min_amount'=>CommonLib::currency_format($payment_limit->min_amount, $store->currency_id), 'max_amount'=>CommonLib::currency_format($payment_limit->max_amount, $store->currency_id)]);
            }
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function getPaymentTypes ()
    {
        $op = array();
        if ($this->session->has('XPAY.GetPaymentTypes'))
        {
            $store = $this->session->get('XPAY.GetPaymentTypes');			
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
				
                if ($this->userSess->security_pin == md5($this->request->security_pin))
                {				
                    $op['data'] = [
                        'mrid'=>['label'=>trans('user/account.supplier_id'), 'value'=>$store->store_code],
                        'mrbusiness_name'=>['label'=>trans('user/account.mrbusiness_name'), 'value'=>$store->store_name],
						'location'=>['label'=>trans('user/account.location'), 'value'=>$store->address],
						//'currency'=>['code'=>$store->code, 'symbol'=>$store->currency_symbol],                        
                        'bill_amount'=>['label'=>trans('user/account.bill_amount'), 'value'=>CommonLib::currency_format($store->bill_amount, $store->currency_id)],
                        'logo'=>$store->store_logo
                    ];
                    $op['payment_modes'] = $this->payObj->getPaymentModes(['account_id'=>$this->userSess->account_id, 'merchant_currency_id'=>$store->currency_id, 'country_id'=>$store->country_id, 'amount'=>$store->bill_amount]);							
                    $payment_modes = [];
                    if (!empty($op['payment_modes']))
                    {
                        $payment_modes = [];
                        foreach ($op['payment_modes'] as $pt)
                        {
                            $payment_modes[] = $pt->id;
                        }
                    } 
					//print_r($payment_modes);exit;
                    $payoutDetails = $this->payObj->getPaymentTypeId([
                        'payment_mode'=>$payment_modes,
                        'currency_id'=>$store->currency_id,
                        'country_id'=>$store->country_id]);
					//print_r($payoutDetails);exit;	
                    $op['payment_type'] = ['title'=>$payoutDetails->payment_type, 'gateway_code'=>$payoutDetails->payment_code];  
                    $this->session->set('XPAY.GetPaymentInfo', $store); 					
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else
                {
                    $op['msg'] = trans('user/account.invalid_security');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
                }
            }
            else if (isset($this->request->auth_status) && ($this->request->auth_status == true))
            {
                $op['data'] = [
                    'mrid'=>['label'=>trans('user/account.supplier_id'), 'value'=>$store->store_code],
                    'mrbusiness_name'=>['label'=>trans('user/account.mrbusiness_name'), 'value'=>$store->store_name],                    
					'location'=>['label'=>trans('user/account.location'), 'value'=>$store->address],
                    //'currency'=>['code'=>$store->code, 'symbol'=>$store->currency_symbol],                    
                    'bill_amount'=>['label'=>trans('user/account.bill_amount'), 'value'=>CommonLib::currency_format($store->bill_amount, $store->currency_id)],
                    'logo'=>$store->store_logo
                ];
                $op['payment_modes'] = $this->payObj->getPaymentModes(['account_id'=>$this->userSess->account_id, 'merchant_currency_id'=>$store->currency_id, 'country_id'=>$store->country_id, 'amount'=>$store->bill_amount]);
                 $payment_modes = [];
                if (!empty($op['payment_modes']))
                {
                    $payment_modes = [];
                    foreach ($op['payment_modes'] as $pt)
                    {
                        $payment_modes[] = $pt->pay_mode_id;
                    }
                } 
                $payoutDetails = $this->payObj->getPaymentTypeId([
                    'payment_mode'=>$payment_modes,
                    'currency_id'=>$store->currency_id,
                    'country_id'=>$store->country_id]);
                $op['payment_type'] = ['title'=>$payoutDetails->payment_type, 'gateway_code'=>$payoutDetails->payment_code]; 

                $this->session->set('XPAY.GetPaymentInfo', $store);
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS'); 
            }
            else
            {
                $op['msg'] = trans('user/account.invalid_security');
                $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
            }
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function getPaymentInfo ()
    {		
        $op = array();
        if ($this->session->has('XPAY.GetPaymentInfo'))
        {
            $store = $this->session->get('XPAY.GetPaymentInfo');			
            $op['data'] = [
                'mrid'=>['label'=>trans('user/account.supplier_id'), 'value'=>$store->store_code],
                'mrbusiness_name'=>['label'=>trans('user/account.mrbusiness_name'), 'value'=>$store->store_name],                
                'location'=>['label'=>trans('user/account.location'), 'value'=>$store->address],
                'bill_amount'=>['label'=>trans('user/account.bill_amount'), 'value'=>CommonLib::currency_format($store->bill_amount, $store->currency_id)],
            ];
            $store->payment_mode = $this->request->payment_mode;
            $payoutDetails = $this->payObj->getPaymentTypeId([
                'payment_mode'=>$store->payment_mode,
                'currency_id'=>$store->currency_id,
                'country_id'=>$store->country_id
            ]);
            $store->payment_type_id = $payoutDetails->payment_type_id;
            $store->payment_code = $payoutDetails->payment_code;
            $store->user_currency_id = $this->userSess->currency_id;			
            $store->pay_id = $this->payObj->savePay((array) $store);	            
			if ($store->payment_type_id == $this->config->get('constants.PAYMENT_TYPES.WALLET'))
            {	               
                $op = $this->payObj->confirmPay($store->pay_id);				
				if (!empty($op) && ($op['status'] == $this->config->get('httperr.SUCCESS')))
                {
                    $this->statusCode = $this->config->get('httperr.SUCCESS');
                    $this->session->forget('XPAY');
                }
            }
            else
            {					
				$gi = [
					'amount'=>$store->bill_amount,
					'payment_mode'=>$store->payment_mode,
					'firstname'=>$this->userSess->firstname,
					'lastname'=>$this->userSess->lastname,
					'mobile'=>$this->userSess->mobile,
					'email'=>$this->userSess->email,
					'account_id'=>$this->userSess->account_id,
					'account_log_id'=>$this->userSess->account_log_id,
					'payment_type'=>$store->payment_code,
					'purpose'=>'PAY',
					'id'=>$store->pay_id,
					'ip'=>$this->request->getClientIP(),
					'currency_id'=>$store->currency_id,
					'card_id'=>$this->request->has('id') ? $this->request->id : null,
					//'remark'=>trans('general.pay_remark', ['mrbusiness_name'=>$store->mrbusiness_name, 'amount'=>CommonLib::currency_format($store->bill_amount, $store->currency_id)])];
					'remark'=>$store->store_name];		
				//print_r($gi);exit;		
				$store->gateway_info = $op['gateway_info'] = $this->payObj->getGateWayInfo($store->payment_code, $gi);
				$this->statusCode = $this->config->get('httperr.SUCCESS');
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
