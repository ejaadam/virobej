<?php
namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Api\User\UserApiBaseController;
use App\Models\Api\User\OfferCashbackModel;
use App\Models\Api\CommonModel;
use Session;
use App\Helpers\CommonNotifSettings;
use Config;
use CommonLib;

class OfferCashbackController extends UserApiBaseController
{
    public function __construct ()
    {
        parent::__construct();
		$this->off_caskbackObj = new OfferCashbackModel();		
		$this->commonObj = new CommonModel();
    }	
	
	public function searchCustomer ()
    {   
        $op = array();
        if (isset($this->userSess->store_id) && !empty($this->userSess->store_id))
        {
            $postdata = $this->request->all();			
            if ($this->off_caskbackObj->isCachbackEnabled(['supplier_id'=>$this->userSess->supplier_id, 'store_id'=>$this->userSess->store_id]))
            {				
                $customer = $this->off_caskbackObj->searchCustomer($postdata);	
				//print_r($customer);exit;	
                if (!empty($customer))
                {
                    $this->session->forget('OfferCashback');
                    $customer->store = (object) [];
                    $customer->store->currency = $this->commonObj->getCurrencyInfo($this->userSess->currency_id);					
                    $this->session->set('OfferCashback.getBillAmount', $customer);
					$op['data'] = [
						'full_name'=>['label'=>trans('user/offer_cashback.full_name'), 'value'=>$customer->full_name],
						'user_code'=>['label'=>trans('user/offer_cashback.user_code'), 'value'=>$customer->user_code],
						'uname'=>['label'=>trans('user/offer_cashback.uname'), 'value'=>$customer->uname],
						'mobile'=>['label'=>trans('user/offer_cashback.mobile'), 'value'=>$customer->mobile],
						'currency'=>['code'=>$customer->store->currency->code, 'symbol'=>$customer->store->currency->currency_symbol],
						//'min_amount'=>$this->userSess->min_amount,
                        //'max_amount'=>$this->userSess->max_amount						
					];                   
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    $op['msg'] = trans('user/offer_cashback.customer_not_found');
                }
            }
            else
            {				
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('user/offer_cashback.service_not_enabled');
            }
        }
        else
        {
			if ($this->userSess->account_type == $this->config->get('constants.ACCOUNT_TYPE.USER')) {
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');            
				$op['msg'] = trans('user/offer_cashback.access_denied');
			} else {
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');            
				$op['msg'] = trans('user/offer_cashback.select_store_to_proceed');
			}
            
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	
	public function getBillAmount ()
    {  		
        if ($this->session->has('OfferCashback.getBillAmount'))
        {
            $op = array();
            $postdata = $this->request->all();			
            $customer = $this->session->get('OfferCashback.getBillAmount');		
			//print_r($customer);exit;	
            $customer->bill_amount = $postdata['amount'];			
            $customer->paymode = $this->config->get('constants.PAYMODE.SHOP_AND_EARN');			
            $customer->currency_id = $this->userSess->currency_id;
            $customer->supplier_id = $this->userSess->supplier_id;
            $customer->is_premium = 0;
            $customer->country_id = $this->userSess->country_id;			
            //$customer->has_credit_limit = $this->userSess->has_credit_limit;
            $customer->store_id = $this->userSess->store_id;
            $customer->payment_type = $this->config->get('constants.PAYMENT_TYPES.CASH');            			            
            $customer->mr_account_id = $this->userSess->account_id;
            $customer->account_type = $this->userSess->account_type;			
            $customer->customer_id = $customer->account_id;	
            if ($this->off_caskbackObj->checkMerchantCreditLimit((array) $customer))
            {
                $res_data = $this->off_caskbackObj->creditCashback((array) $customer);
				//print_r($res_data);exit;
                if ($res_data)
                {
                    $cashback = CommonLib::currency_format($res_data->cashback_amt, $res_data->user_currency, false);
                   /*  $op['data'] = [
                        'customer_full_name'=>$customer->full_name,
                        'customer_uname'=>$customer->uname,
                        //'prof_img'=>$this->userSess->profile,
                        'customer_mobile'=>$customer->mobile,
                        'cashback_amount'=>$cashback->amount,
                        'currency'=>$cashback->currency_symbol
                    ]; */
					$op['order_code'] = $res_data->order_code;					
					$op['customer_image'] = asset($this->config->get('path.ACCOUNT.PROFILE_IMG.WEB.100x100').$customer->profile_img);
					$op['msg'] = trans('user/offer_cashback.successs', ['amount'=>$cashback->currency_symbol . ' '.$cashback->amount, 'full_name'=>$customer->full_name]);
                    $this->session->set('OfferCashback.offerCashbackRating', ['account_id'=>$customer->account_id, 'post_id'=>$customer->store_id, 'order_id'=>$res_data->order_id]);$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else
                {
                    $op['msg'] = trans('general.something_wrong');
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
            else
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('user/offer_cashback.service_not_enabled');
            }
        }
        else
        {
            $op['msg'] = trans('general.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
 

}
