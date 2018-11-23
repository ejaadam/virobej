<?php
namespace App\Http\Controllers\Api\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\User\UserApiBaseController;
use App\Models\Api\User\AffModel;
use App\Models\Api\User\AccountModel;
use App\Models\Api\User\CashbackModel;
use App\Models\CommonModel;
use App\Models\Api\User\StoreModel;
use Session;
use App\Helpers\CommonNotifSettings;
use Config;
use CommonLib;
class CashbackController extends UserApiBaseController
{
    public function __construct ()
    {
        parent::__construct();
		$this->caskbackObj=new CashbackModel();
		$this->apiaffObj = new AffModel();
		$this->accountObj = new AccountModel();
		$this->commonObj =new CommonModel();
		$this->storeObj =new StoreModel();
    }	
	
	public function getStoreSearch ()
    {
        $op = array();
        $postdata = $this->request->all();         
		$postdata['country_id'] = $this->geo->current->country_id;            
		$postdata['account_id'] = $this->userSess->account_id;
		$store = $this->caskbackObj->getCashbackStoreSearch($postdata);				
		//print_r($store);exit;	
		$this->session->forget('CASHBACK');
		if (!empty($store))
		{
			if ($store->shop_and_earn)
			{   				
				if ($cashback_setting = $this->caskbackObj->checkuser_cashback_percentage(array_merge($postdata, (array) $store)))
				{ 						
					$this->session->set('CASHBACK.GetBillAmount', $store);
					$op['data'] = [
						'mrid'=>['label'=>trans('user/cashback.supplier_id'), 'value'=>$store->store_code],
						'mrbusiness_name'=>['label'=>trans('user/cashback.mrbusiness_name'), 'value'=>$store->store_name],
						'location'=>['label'=>trans('user/cashback.location'), 'value'=>$store->address],
						'currency'=>['code'=>$store->currency, 'symbol'=>$store->currency_symbol],
						'logo'=>$store->store_logo,
						'min_amount'=>10
					];
					$this->statusCode = $this->config->get('httperr.SUCCESS');
				}
				else
				{
					$this->statusCode = $this->config->get('httperr.NOT_FOUND');
					$op['msg'] = trans('user/cashback.offers_not_avaliable');
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
			$this->statusCode = $this->config->get('httperr.NOT_FOUND');
			$op['msg'] = trans('user/cashback.store_not_available');
		}        
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function setBillAmount ()
    {
        if ($this->session->has('CASHBACK.GetBillAmount'))
        {			
            $op = array();
            $postdata = $this->request->all();
            $store = $this->session->get('CASHBACK.GetBillAmount');	
            $store->bill_amount = $postdata['bill_amount'];					
            if ($cashback_setting = $this->caskbackObj->checkuser_cashback_percentage(array_merge($postdata, (array) $store)))
            {				
                if ($this->storeObj->checkMerchantCreditLimit((array) $store))
                {
                    $this->session->set('CASHBACK.ConfirmCashback', $store);
                    $op['data'] = [
                        'mrid'=>['label'=>trans('user/cashback.supplier_id'), 'value'=>$store->store_code],
                        'mrbusiness_name'=>['label'=>trans('user/cashback.mrbusiness_name'), 'value'=>$store->store_name],
						'bill_amount'=>['label'=>trans('user/cashback.bill_amount'), 'value'=>CommonLib::currency_format($store->bill_amount, $store->currency_id, true, false)],
						'location'=>['label'=>trans('user/cashback.location'), 'value'=>$store->address],						
                    ];
                   $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else
                {
                   $op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
                    $op['msg'] = trans('user/cashback.store_not_available');
                }
            }
            else
            {
                $op['status'] =$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('user/cashback.bill_amount_not_eligible');
            }
         }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $op['status'] =$this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function confirmCashback ()
    {
        $op = array();     
		
        if ($this->session->has('CASHBACK.ConfirmCashback'))
        {
            $store = $this->session->get('CASHBACK.ConfirmCashback');			            
			$store->code =  $this->session->has('CASHBACK.VerifyCashBack') ? $this->session->get('CASHBACK.VerifyCashBack')->code : rand(100000, 999999);				
			$op['hash_code'] =  md5($store->code);	
			//print_r($store);exit;
		    CommonNotifSettings::notify('USER.CONFIRM_CASHBACK', null, Config::get('constants.ACCOUNT_TYPE.USER'), ['code'=>$store->code, 'bill_amount'=>$store->bill_amount, 'name'=>$this->userSess->full_name,'full_name'=>$this->userSess->full_name, 'customer_id'=>$store->staff_mobile, 'mobile'=>$store->mobile_no, 'email'=>$store->staff_email, 'user_mobile'=>$this->userSess->mobile, 'store'=>$store->store_name], false, true, false, true, false);		
		
			$op['code']= $store->code;
			$this->session->put('CASHBACK.VerifyCashBack', $store);
			$op['msg'] = trans('user/cashback.confirm_msg');
            $op['status']= $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	
	
	public function verifyCashBack ()
    {
		$wallet_id = $this->config->get('constants.WALLETS.VIM');
        $op = array();
        if ($this->session->has('CASHBACK.VerifyCashBack'))
        {
			$store = $this->session->get('CASHBACK.VerifyCashBack');
			$like_status = 0;
			$store_codes = !empty($this->session->get('likes')) ? $this->session->get('likes') : [];
			if (in_array($store->store_code, $store_codes))
			{
				$like_status = 1;
			}	
			//print_r($store_codes);exit;
            if ($store->code == $this->request->code)
            {	                
                $store->account_id = $this->userSess->account_id;
                $store->full_name = $this->userSess->full_name;
                $store->user_currency = $this->userSess->currency_id;
                $store->country_id 	  = $this->userSess->country_id;
                $store->paymode 	  = $this->config->get('constants.PAYMODE.SHOP_AND_EARN');
                $store->payment_type  = $this->config->get('constants.PAYMENT_TYPES.CASH');
                $trans_info 		  = $this->caskbackObj->creditCashback((array) $store);		
		        if($trans_info)
                {
                    $balance = CommonLib::currency_format($trans_info->current_balance, $store->user_currency, false, false);
                    $cashback = CommonLib::currency_format($trans_info->cashback_amount, $store->user_currency, false, false);
                    $op['data'] = [                        
                        'order_code'=>$trans_info->order_code,
                        'congrats'=>trans('user/cashback.congrats'),
                        'you_have_just_got'=>trans('user/cashback.you_have_just_got'),
                        'merchant'=>$store->store_name,
						'currency'=>$cashback->currency_symbol,
                        'cashback_amount'=>$cashback->amount,                                                
                        'store_code'=>$store->store_code,                                                
                        'like_status'=>$like_status,                                                
                    ];
                    $op['share']['email'] = ['title'=>trans('user/account.cashback.store_share.email.title', (array) $store), 'content'=>trans('user/account.cashback.store_share.email.content', (array) $store)];
                    $this->session->forget('CASHBACK');
                    $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else
                {
                    $op['msg'] = trans('general.something_wrong');
                }
            }
            else
            {
                $op['msg'] = trans('user/cashback.invalid_otp');
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
