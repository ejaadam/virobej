<?php
namespace App\Http\Controllers\Api\Seller;
use App\Http\Controllers\Api\APIBase;
use App\Models\Seller\Cashback;
use Response;
use Session;
use Config;
use App\Helpers\CommonLib;

class APICashbackController extends APIBase
{
    public function __construct ()
    {
        parent::__construct();
        $this->cashbackObj = new Cashback();
		
    }
	
	public function searchCustomer ()
    {
		$op = array();		
        if (isset($this->supplier_id))
        {
            $postdata = $this->request->all();
			$result = $this->cashbackObj->isCachbackEnabled(['supplier_id'=>$this->supplier_id, 'account_id'=>$this->account_id, 'store_id'=>'']);			
            if ($result)
            {				
                $customer = $this->cashbackObj->searchCustomer($postdata);		
				//print_r($customer);exit;
				if ($customer) {	
					Session::set('getBillAmount', $customer);
					$op['data'] = [
						'customer_full_name'=>$customer->full_name,
						'customer_uname'=>$customer->uname,
						'customer_mobile'=>$customer->mobile,
						'customer_id'=>$customer->account_id,
						'currency_id'=>$customer->user_currency,
						'currency'=>$customer->code
					];
					$this->statusCode = 200;
				} else {
					$this->statusCode = 400;
					$op['msg'] = 'Customer Not Found.';
				}
            }
            else
            {
                $this->statusCode = 400;
                $op['msg'] = trans('general.service_not_enabled');
            }
        }
        else
        {
            $op['status'] = $this->statusCode = 400;
            //$op['stores'] = $this->accountObj->getAllocatedStoresList(['system_role_id'=>$this->userSess->system_role_id, 'account_id'=>$this->userSess->account_id, 'supplier_id'=>$this->userSess->supplier_id]);
            $op['msg'] = trans('general.select_merchat_to_proceed');
        }
		return Response::json($op, $this->statusCode, $this->headers, $this->options);         
    }
	
	public function getBillAmount ()
    {   		
        if (Session::has('getBillAmount'))
        {
            $op = array();
            $postdata = $this->request->all();
            $customer = Session::get('getBillAmount');		
			
            $customer->bill_amount = $postdata['amount'];
            $customer->paymode = Config::get('constants.PAYMODE.SHOP_AND_EARN');
            $customer->currency_id = $customer->user_currency;                
            $customer->payment_type = Config::get('constants.PAYMENT_TYPES.CASH');
            //$customer->store_id = $this->userSess->store_id;
            $customer->store_id = 6;
            $customer->supplier_id = $this->supplier_id;
			$customer->customer_id = $customer->account_id;		
            $customer->account_id = $this->account_id;
            $customer->account_type_id = $this->account_type_id;            	
            $res_data = $this->cashbackObj->creditCashback((array) $customer);						
            if ($res_data)
            {
                //$cashback = CommonLib::currency_format($res_data->cashback_amt, $res_data->user_currency, false);
                $cashback = CommonLib::currency_format(100, 2, false);
                $op['data'] = [
                    'customer_full_name'=>$customer->full_name,
                    'customer_uname'=>$customer->uname,
                    //'prof_img'=>$this->userSess->profile,
                    'customer_mobile'=>$customer->mobile,
                    //'cashback_amount'=>$cashback->amount,
                    'cashback_amount'=>100,
                    'currency'=>$cashback->currency_symbol
                ];
                //$this->session->set('offerCashbackRating', ['account_id'=>$customer->account_id, 'post_id'=>$customer->store_id, 'order_id'=>$res_data['order_id']]);
                Session::set('offerCashbackRating', ['account_id'=>$customer->account_id, 'post_id'=>$customer->supplier_id, 'order_id'=>$res_data]);
                $op['msg'] = 'Cashback added successfully.';
                $this->session->forget('getBillAmount');
                $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['msg'] = trans('general.something_wrong');
            }
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }        
		return Response::json($op, $this->statusCode, $this->headers, $this->options);    
    }

    

}
