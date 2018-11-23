<?php
namespace App\Models\Api\User;
use DB;
use File;
use TWMailer;
use App\Models\BaseModel;
use CommonLib;
use App\Helpers\CommonNotifSettings;
use App\Models\Api\CommonModel;

class PayModel extends BaseModel {
	
    public function __construct() 
	{
        parent::__construct();		
		$this->commonObj = new CommonModel();
		
    }
	
	public function getStoreSearch (array $arr = array())
    {		
        extract($arr);
	
        $query = DB::table($this->config->get('tables.SUPPLIER_MST').' as mm')
                ->join($this->config->get('tables.STORES').' as sm', function($sm)
                {
                    $sm->on('sm.supplier_id', '=', 'mm.supplier_id')
                    ->where('sm.is_approved', '=', $this->config->get('constants.MERCHANT.STORE.IS_APPROVED.APPROVED'))
                    ->where('sm.status', '=', $this->config->get('constants.MERCHANT.STORE.STATUS.ACTIVE'))
                    ->where('sm.is_deleted', '=', $this->config->get('constants.OFF')); 
                })
				->join($this->config->get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 'sm.store_id')	
                ->join($this->config->get('tables.CASHBACK_SETTINGS').' as mcs', 'mcs.supplier_id', '=', 'sm.supplier_id')				
				->join($this->config->get('tables.ADDRESS_MST').' as am', function($am) use($country_id)
                {
                    $am->on('am.address_id', '=', 'sm.address_id');
                    $am->where('am.country_id', '=', $country_id);
                })
				->leftjoin($this->config->get('tables.ACCOUNT_MST').' as lla', 'lla.account_id', '=', 'sm.last_login_account')
				->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as llad', 'llad.account_id', '=', 'sm.last_login_account')
				->leftjoin($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'sm.currency_id')
				->where('mm.is_verified', '=', $this->config->get('constants.MERCHANT.IS_VERIFIED.VERIFIED'))               
                ->where('mm.is_deleted', $this->config->get('constants.OFF'))
                ->where(function($c)
                {
                    $c->whereNull('mcs.store_id')
                    ->orWhere('mcs.store_id', '=', DB::raw('sm.store_id'));
                })
				->where(function($c)
                {
                    $c->where('mcs.is_cashback_period', $this->config->get('constants.OFF'))
                    ->orWhere(function($c2)
                    {
                        $c2->where('mcs.is_cashback_period', $this->config->get('constants.ON'))
                        ->whereDate('mcs.cashback_start', '<=', getGTZ())
                        ->whereDate('mcs.cashback_end', '>=', getGTZ());
                    });
                })
                ->where('mm.status', $this->config->get('constants.MERCHANT.STATUS.ACTIVE'))				
				->where('sm.store_code', $store_code);        
		
        $details = $query->selectRaw('COALESCE(sm.last_login_account, mm.account_id) as supplier_account_id, sm.store_id, sm.supplier_id, mm.company_name, mm.supplier_code, COALESCE(lla.uname, sm.store_code) as staff_uname, COALESCE(CONCAT(llad.firstname,\' \',llad.lastname), sm.store_name) as staff_full_name, COALESCE(lla.mobile,se.mobile_no) as staff_mobile, COALESCE(lla.email,se.email) as staff_email, se.mobile_no, sm.store_code, sm.store_name, sm.currency_id, am.country_id, mcs.is_redeem_otp_required, mcs.pay, sm.last_login_account, c.currency as code, c.currency_symbol, c.decimal_places, c.currency_id, am.address, sm.store_logo, mm.logo, c.min_amount')->first();
			
        if(!empty($details))
        {			
            if (!empty($details->store_logo))
            {
                $details->store_logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$details->store_logo);
            }
            else if (!empty($details->logo) && empty($details->store_logo))
            {
                $details->store_logo = asset($this->config->get('path.SELLER.PROFILE_IMG_PATH.WEB').$details->logo);
            }
            else
            {
                $details->store_logo = asset($this->config->get('constants.STORE_LOGO_PATH.DEFAULT'));
            }
            unset($details->logo);
			return $details;
        }
		return false;
        
    }
	
	public function getPayLimit (array $arr = array())
    {	
        extract($arr);			 
        return DB::table($this->config->get('tables.PAY_PAYMENT_SETTINGS').' as pps')		
                        ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', function($pt)
                        {
                            $pt->on('pt.payment_type_id', '=', 'pps.payment_type_id')
                            ->where('pt.can_pay_by_card', '=', $this->config->get('constants.ON'));
                        }) 
                        ->where('pps.currency_id', $currency_id)
                        ->where('pps.country_id', $country_id)
                        ->selectRaw('min(min_amount) as min_amount,max(max_amount) as max_amount') 
                        ->first();						
    }
	
	public function getPaymentModes (array $arr = array())
    {	
        extract($arr);
        $result = DB::table($this->config->get('tables.PAY_PAYMENT_SETTINGS').' as ps')
                ->join($this->config->get('tables.APP_PAYMENT_MODES').' as apm', 'apm.pay_mode_id', '=', 'ps.pay_mode')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'ps.payment_type_id')                                
				->where('pt.for_pay', $this->config->get('constants.ON'))
                ->where(function($check_amount) use($amount)
                {
                    $check_amount->where(function($check_null)
                    {
                        $check_null->whereNull('min_amount')
                        ->whereNull('max_amount');
                    })
                    ->orWhere(function($max_null) use($amount)
                    {
                        $max_null->whereNotNull('min_amount')
                        ->whereNull('max_amount')
                        ->where('min_amount', '<=', $amount);
                    })
                    ->orWhere(function($min_null) use($amount)
                    {
                        $min_null->whereNotNull('max_amount')
                        ->whereNull('min_amount')
                        ->where('max_amount', '>=', $amount);
                    })
                    ->orWhere(function($check) use($amount)
                    {
                        $check->whereNotNull('min_amount')
                        ->whereNotNull('max_amount')
                        ->where('min_amount', '<=', $amount)
                        ->where('max_amount', '>=', $amount);
                    });
                })    
                ->where('ps.currency_id', $merchant_currency_id)
                ->where('ps.country_id', $country_id)
                ->selectRaw('apm.pay_mode_id, apm.pay_mode as name, apm.code as id, pt.save_card as has_card_ui, apm.logo as icon, apm.is_online as is_pg')				
                ->get();
        
		foreach ($result as $k => $value)
        {
            if ($value->id === 'vim')
            {
				$balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ab')
                                ->leftjoin($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS').' as ces', function($ces) use($merchant_currency_id)
                                {
                                    $ces->on('ces.from_currency_id', '=', 'ab.currency_id')
                                    ->where('ces.to_currency_id', '=', $merchant_currency_id);
                                })
                                ->where('ab.account_id', $account_id)
                                ->where('ab.wallet_id', $this->config->get('constants.WALLETS.VIM'))
                                ->where(DB::raw('(IF(ces.live_rate is not NULL, ces.live_rate, 1) * ab.current_balance)'), '>=', $amount)
                                ->value('current_balance');
                if (!$balance)
                {
                    unset($result[$k]);
                } else {					
					$value->balance = CommonLib::currency_format($balance, $merchant_currency_id, true, false);
				}
            }  
            $value->icon = asset($this->config->get('constants.PAYMENT_MODE_IMG_PATH.LOCAL').$value->icon);
            $value->saved_cards = ($value->has_card_ui) ? $this->getStoredCards($arr) : null;
        }   
        return array_values($result);
    }
	
	/* public function getPaymentTypes (array $arr = array())
    {	
        extract($arr);
        $result = DB::table($this->config->get('tables.PAY_PAYMENT_SETTINGS').' as ps')
                ->join($this->config->get('tables.APP_PAYMENT_MODES').' as apm', 'apm.pay_mode_id', '=', 'ps.pay_mode')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'ps.payment_type_id')                
                ->where('apm.is_online', $this->config->get('constants.ON'))
                 ->where(function($check_amount) use($amount)
                {
                    $check_amount->where(function($check_null)
                    {
                        $check_null->whereNull('min_amount')
                        ->whereNull('max_amount');
                    })
                    ->orWhere(function($max_null) use($amount)
                    {
                        $max_null->whereNotNull('min_amount')
                        ->whereNull('max_amount')
                        ->where('min_amount', '<=', $amount);
                    })
                    ->orWhere(function($min_null) use($amount)
                    {
                        $min_null->whereNotNull('max_amount')
                        ->whereNull('min_amount')
                        ->where('max_amount', '>=', $amount);
                    })
                    ->orWhere(function($check) use($amount)
                    {
                        $check->whereNotNull('min_amount')
                        ->whereNotNull('max_amount')
                        ->where('min_amount', '<=', $amount)
                        ->where('max_amount', '>=', $amount);
                    });
                }) 
                ->where('ps.currency_id', $merchant_currency_id)
                ->where('ps.country_id', $country_id)
                ->selectRaw('apm.pay_mode_id, apm.pay_mode as name, apm.code as id, pt.save_card as has_card_ui, apm.logo as icon, apm.is_online as is_pg')
                ->get();

        array_walk($result, function(&$p) use($arr)
        {
            $p->icon = asset($this->config->get('path.PAYMENT_MODE_IMG_PATH.LOCAL').$p->icon);
            $p->saved_cards = ($p->has_card_ui) ? $this->getStoredCards($arr) : null;
        }); 
        return $result;
    } */
	
	public function getStoredCards (array $arr = array())
    {
        /* extract($arr);
        $details = DB::table($this->config->get('tables.ACCOUNT_PAYMENT_CARD_SETTINGS').' as pcs')
                ->join($this->config->get('tables.PAYMENT_CARD_TYPES').' as pct', 'pct.card_type_id', '=', 'pcs.card_type_id')
                ->where('pcs.is_deleted', $this->config->get('constants.OFF'))
                ->where('pcs.status', $this->config->get('constants.ON'))
                ->where('pcs.account_id', $account_id)
                ->selectRaw('id,display_card_no as card_no,card as card_type,img_path as card_type_img')
                ->get();
        array_walk($details, function(&$d)
        {
            $d->card_type_img = asset($d->card_type_img);
        }); */
        return !empty($details) ? $details : null;
    }
	
	public function getPaymentTypeId (array $arr = array())
    {	//return $arr;
        extract($arr);
        $qry = DB::table($this->config->get('tables.PAY_PAYMENT_SETTINGS').' as ps')
                ->join($this->config->get('tables.APP_PAYMENT_MODES').' as apm', 'apm.pay_mode_id', '=', 'ps.pay_mode')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'ps.payment_type_id')                
                ->where('pt.for_pay', $this->config->get('constants.ON'))
                ->where('ps.currency_id', $currency_id)
                ->where('ps.country_id', $country_id)
                ->select('pt.payment_key as payment_code', 'pt.payment_type_id', 'pt.payment_type');

        if (is_array($payment_mode))
        {
            if (is_numeric($payment_mode[0]))
            {
                $qry->whereIn('apm.pay_mode_id', $payment_mode);
            }
            else
            {
                $qry->whereIn('apm.code', $payment_mode);
            }
        }
        else
        {
            if (is_numeric($payment_mode))
            {
                $qry->where('apm.pay_mode_id', $payment_mode);
            }
            else
            {
                $qry->where('apm.code', $payment_mode);
            }
        }
        return $qry->first();
    }
	
	public function getGateWayInfo ($payment_code, array $arr = array())
    {	
        extract($arr);
        $payment_details = DB::table($this->config->get('tables.PAYMENT_TYPES').' as pt')
                //->join($this->config->get('tables.PAYMENT_TYPES_LANG').' as ptl', 'ptl.payment_type_id', '=', 'pt.payment_type_id')
                ->where('payment_key', $payment_code)
                ->selectRaw('pt.payment_type_id, gateway_settings, save_card, pt.payment_type as paymentgateway_name')
                ->first();
				
        if (!empty($payment_details))
        {
            $settings = $payment_details->gateway_settings = json_decode($payment_details->gateway_settings);
            if (!empty($settings) && is_object($settings))
            {
                /* if (!empty($card_id))
                {
                    $card_details = DB::table($this->config->get('tables.ACCOUNT_PAYMENT_CARD_SETTINGS'))
                            ->where('is_deleted', $this->config->get('constants.OFF'))
                            ->where('status', $this->config->get('constants.ON'))
                            ->where('account_id', $account_id)
                            ->where('id', $card_id)
                            ->selectRaw('card_type_id,account_details')
                            ->first();
                    if (!empty($card_details))
                    {
                        $card_details->account_details = $this->xpb_decrypt($card_details->account_details);
                    }
                } */
                $settings = $settings->status ? (array) $settings->live : (array) $settings->sandbox;
                $pgr = [];
                $pgr['account_id'] = $account_id;
                $pgr['account_log_id'] = $account_log_id;
                $pgr['payment_type_id'] = $payment_details->payment_type_id;
                $pgr['pay_mode_id'] = $this->config->get('constants.PAYMENT_MODES.'.$payment_mode);
                $pgr['purpose'] = $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.'.$purpose);
                $pgr['relative_post_id'] = $id;
                $pgr['currency_id'] = $currency_id;
                $pgr['amount'] = $amount;
                $pgr['created_on'] = getGTZ();				
                $pgr_id = DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->insertGetID($pgr);
                switch ($payment_code)
                {
					case 'safexpay':
                        $payment_details->gateway_settings->modes = (array) $payment_details->gateway_settings->modes;
                        //$settings['order_no'] = substr(hash('sha256', mt_rand().microtime()), 0, 20);                        
                        $settings['order_no'] = hash('sha256', $id);                        
                        $settings['Amount'] = number_format((float) $amount, 2, '.', '');
                        $settings['Country'] = 'IND';
                        $settings['Currency'] = 'INR';
						$settings['success_url'] = route('payment-gateway-response.success', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);						
                        $settings['failure_url'] = route('payment-gateway-response.failure', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);                        
                        $settings['Channel'] = 'MOBILE';
                        $settings['pg_id'] = 1;
						$settings['Paymode'] = $payment_details->gateway_settings->modes[$payment_mode];
						$settings['Scheme'] = 1;						
						$settings = array_merge($settings, ['productinfo'=>$remark, 'cust_name'=>$firstname, 'email_id'=>$email, 'mobile_no'=>$mobile, 'udf1'=>$pgr_id, 'udf2'=>$id]);
                        $settings['paymentgateway_name'] = $payment_details->paymentgateway_name;                 
                        break;
                    case 'pay-u':
                        $payment_details->gateway_settings->modes = (array) $payment_details->gateway_settings->modes;
                        $settings = array_merge($settings, ['productinfo'=>$remark, 'firstname'=>$firstname, 'email'=>$email, 'mobile'=>$mobile, 'udf1'=>$pgr_id]);
                        $settings['paymentgateway_name'] = $payment_details->paymentgateway_name;
                        $settings['amount'] = number_format((float) $amount, 2, '.', '');
                        $settings['txnid'] = substr(hash('sha256', mt_rand().microtime()), 0, 20);

                        $hashSequence = 'key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt';
                        $hashVarsSeq = explode('|', $hashSequence);
                        $hash_string = '';
                        foreach ($hashVarsSeq as $hash_var)
                        {
                            $hash_string .= isset($settings[$hash_var]) ? $settings[$hash_var] : ($this->config->get('app.is_api') && in_array($hash_var, ['udf1', 'udf2', 'udf3', 'udf4', 'udf5']) ? $hash_var : '');
                            $hash_string .= '|';
                        }
                        $settings['hash_values_ulr'] = route('payment-gateway-response.check-sum', ['payment_type'=>$payment_code]);
                        $settings['hash'] = strtolower(hash('sha512', trim($hash_string, '|')));
                        $settings['vas_for_mobile_sdk_hash'] = strtolower(hash('sha512', $settings['key'].'|vas_for_mobile_sdk|default|'.$settings['salt']));
                        $settings['verify_payment_hash'] = strtolower(hash('sha512', $settings['key'].'|verify_payment|'.$settings['txnid'].'|'.$settings['salt']));
                        $settings['payment_related_details_for_mobile_sdk_hash'] = strtolower(hash('sha512', $settings['key'].'|payment_related_details_for_mobile_sdk|default|'.$settings['salt']));
                        $settings['pg'] = $payment_details->gateway_settings->modes[$payment_mode];
                        $settings['surl'] = route('payment-gateway-response.success', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['furl'] = route('payment-gateway-response.failure', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['curl'] = route('payment-gateway-response.cancelled', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        break;
                    case 'pay-dollar':
                        /* $settings['orderRef'] = $pgr_id;
                        $settings['paymentgateway_name'] = $payment_details->paymentgateway_name;
                        $settings['currCode'] = $payment_details->gateway_settings->currCode->{$currency_id};
                        $settings['payType'] = $payment_details->gateway_settings->payType;
                        $settings['lang'] = $payment_details->gateway_settings->lang;
                        $settings['amount'] = number_format((float) $amount, 2, '.', '');
                        $settings['billingFirstName'] = $firstname;
                        $settings['billingLastName'] = $lastname;
                        $settings['billingEmail'] = $email;
                        $settings['custIPAddress'] = $ip;
                        if (isset($card_details) && !empty($card_details))
                        {
                            $settings['epMonth'] = $card_details['month'];
                            $settings['epYear'] = $card_details['year'];
                            $settings['cardNo'] = $card_details['card_no'];
                            $settings['cardHolder'] = $card_details['holder'];
                            $settings['pMethod'] = $payment_details->gateway_settings->pMethod->{$card_details->card_type_id};
                        }
                        else
                        {
                            $settings['epMonth'] = null;
                            $settings['epYear'] = null;
                            $settings['cardNo'] = null;
                            $settings['cardHolder'] = null;
                            $settings['pMethod'] = NULL;
                        }
                        $settings['securityCode'] = null;
                        $settings['remark'] = $remark;
                        $settings['successUrl'] = route('payment-gateway-response.success', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['failUrl'] = route('payment-gateway-response.failure', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['cancelUrl'] = route('payment-gateway-response.cancelled', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]); */
                        break;
                    case 'cashfree':
                       /*  $payment_details->gateway_settings->modes = (array) $payment_details->gateway_settings->modes;
                        $settings['paymentgateway_name'] = $payment_details->paymentgateway_name;
                        $settings['paymentModes'] = $payment_details->gateway_settings->modes[$payment_mode];
                        $settings['merchant_name'] = $this->siteConfig->site_name;
                        $settings['merchant_url'] = url('/');
                        $settings['orderId'] = $pgr_id;
                        $settings['orderNote'] = 'test'; //$remark;
                        $settings['orderCurrency'] = $this->get_currency_code($currency_id);
                        $settings['customerName'] = $firstname;
                        $settings['customerEmail'] = $email;
                        $settings['customerPhone'] = $mobile;
                        $settings['orderAmount'] = number_format($amount, 2, '.', ',');
                        $settings['returnUrl'] = route('payment-gateway-response.return', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['notifyUrl'] = route('payment-gateway-response.notify', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                        $settings['checksumUrl'] = route('payment-gateway-response.check-sum', ['payment_type'=>$payment_code]);
                        ksort($settings);
                        
                        $signatureData = 'appId='.$settings['appId'].'&orderId='.$settings['orderId'].'&orderAmount='.$settings['orderAmount'].'&returnUrl='.$settings['returnUrl'].'&paymentModes='.$settings['paymentModes'];
                        $settings['signature'] = base64_encode(hash_hmac('sha256', $signatureData, $settings['secretKey'], true)); */
                        break;
                }
                $settings['id'] = base64_encode($pgr_id);
                $settings['datafeed'] = route('payment-gateway-response.datafeed', ['payment_type'=>$payment_code, 'id'=>base64_encode($pgr_id)]);
                return $this->xpb_encrypt($settings);
            }
        }
        return false;
    }
	
	public function xpb_encrypt (array $encrypt = array(), $key = null)
    {
        return $encrypt;
    }
	
	public function savePay (array $arr = array())
    {	
        extract($arr);
        $order = $pay = [];
        $order['supplier_id'] = $supplier_id;
        $order['store_id'] = $store_id;
        $order['order_type'] = $this->config->get('constants.ORDER.TYPE.IN_STORE');
        $order['pay_through'] = $this->config->get('constants.PAYMODE.PAY');
        $order['statementline_id'] = $this->config->get('stline.MR_ORDERS.PAY');
        $pay['payment_type_id'] = $payment_type_id;
        $pay['pay_mode_id'] = $this->config->get('constants.PAYMENT_MODES.'.$payment_mode);
        $order['currency_id'] = $pay['to_currency_id'] = $currency_id;
        $order['bill_amount'] = $order['paid_amount'] = $pay['to_amount'] = $bill_amount;
		if ($pay['pay_mode_id'] == $this->config->get('constants.PAYMENT_MODES.vim'))
        {
            $pay['from_wallet_id'] = $this->config->get('constants.WALLETS.VIM');
        }         
        $pay['from_currency_id'] = $user_currency_id;
        $pay['from_amount'] = $bill_amount * $this->get_currency_rate($currency_id, $user_currency_id);
        $pay['status'] = $this->config->get('constants.PAY_STATUS.PENDING');
        $order['account_id'] = $pay['updated_by'] = $account_id;
        $order['approved_by'] = $supplier_account_id;
        $order['order_date'] = $pay['updated_on'] = getGTZ();			
        if (!isset($pay_id))
        {		
            $pay['created_on'] = getGTZ();			
            $pay['order_id'] = DB::table(Config('tables.ORDERS'))->insertGetID($order);		
            if ($pay['order_id'])
            {
                $this->commonObj->updateOrderCount($store_id);
                DB::table($this->config->get('tables.ORDERS'))
                        ->where('order_id', $pay['order_id'])                       
                        ->update(['order_code'=>$store_id.rand(111, 999).$pay['order_id']]);

                $pay_id = DB::table($this->config->get('tables.PAY'))
                        ->insertGetID($pay);
                $payment_id = $store_code . rand(100, 999).$pay_id;
                DB::table($this->config->get('tables.PAY'))
                        ->where('pay_id', $pay_id)
                        ->update(['payment_id'=>$payment_id]); 
                $this->commonObj->updateOrderCommission($pay['order_id']);
                return $pay_id;
            }
        }
        else
        {           
			$order_id = DB::table($this->config->get('tables.PAY'))
                    ->where('pay_id', $pay_id)
                    ->value('order_id');
            DB::table($this->config->get('tables.ORDERS'))
                    ->where('order_id', $order_id)
                    ->update($order);
            DB::table($this->config->get('tables.PAY'))
                    ->where('pay_id', $pay_id)
                    ->update($pay);			
            $this->commonObj->updateOrderCommission($order_id);
            return $pay_id;
        }
        return false;
    }
	
	public function confirmPay ($pay_id, $status = null, $payment_id = null)
    {			
			$details = DB::table($this->config->get('tables.PAY').' as p')
                    ->join($this->config->get('tables.ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                    ->join($this->config->get('tables.SUPPLIER_MST').' as m', 'm.supplier_id', '=', 'mo.supplier_id')
                    ->join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
                    ->where('p.pay_id', $pay_id)
                    ->where('p.is_deleted', $this->config->get('constants.OFF'))
                    ->selectRaw('mo.order_id, mo.order_code, p.pay_id, p.pay_mode_id, m.account_id as to_account_id, mo.account_id as from_account_id, p.from_wallet_id, p.payment_type_id, p.from_currency_id, p.to_currency_id, p.from_amount, p.to_amount, mo.supplier_id, mo.store_id, ms.store_name, (select concat_ws(" ",ad.firstname,ad.lastname) from '.$this->config->get('tables.ACCOUNT_DETAILS').' ad where ad.account_id = mo.account_id) as full_name') 
                    ->first();
			           
            if ($details)
            {
                DB::begintransaction();
				if (($details->payment_type_id != $this->config->get('constants.PAYMENT_TYPES.WALLET') && !empty($status) && $status == 'CONFIRMED') || (empty($status) && $details->payment_type_id == $this->config->get('constants.PAYMENT_TYPES.WALLET') && $payment_id = $this->updateAccountTransaction([
                    'from_account_id'=>$details->from_account_id,
                    //'from_wallet_id'=>$this->config->get('constants.WALLETS.VIM'),
					'from_wallet_id'=>$details->from_wallet_id,
                    'payment_type_id'=>$details->payment_type_id,
                    'pay_mode'=>$details->pay_mode_id,
                    'currency_id'=>$details->from_currency_id,
                    'amt'=>$details->from_amount,
                    //'relation_id'=>$details->order_id,
                    'relation_id'=>$details->pay_id,
                    'debit_remark_data'=>['amount'=>CommonLib ::currency_format($details->from_amount, $details->from_currency_id, true, true), 'store_name'=>$details->store_name, 'order_code'=>$details->order_code],
                    'transaction_for'=>'ORDER_PAYMENT'])))
                {					
                    if (DB::table($this->config->get('tables.PAY'))
                                    ->where('pay_id', $details->pay_id)
                                    ->update([
										//'payment_id'=>$payment_id,
                                        'status'=>$this->config->get('constants.PAY_STATUS.CONFIRMED'),
                                        'updated_on'=>getGTZ(),
                                    ]))
                    {
                        if ($cashback_amount = $this->commonObj->updateOrderCommission($details->order_id))
                        {							
                            $this->commonObj->releaseOrderCommission($details->order_id);
                            DB::commit();
                            return $this->getPayResponse($pay_id, true, $details->full_name);
                        }
                    }
                } else {
				
					if (DB::table($this->config->get('tables.PAY'))
							->where('pay_id', $pay_id)
							->update([
								'status'=>$this->config->get('constants.PAY_STATUS.'.$status),
								'updated_on'=>getGTZ(),
							]))
					{
						DB::commit();
						return $this->getPayResponse($pay_id, true, $details->full_name);
					}				
				}
				DB::rollback();
            } 
            return false;       
    }
	
	public function getPayResponse ($pay_id, $is_first = false, $fname)
    {
        $op = [];
        $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $op['purpose'] = $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY');		
        $details = DB::table($this->config->get('tables.PAY').' as p')
                 ->join($this->config->get('tables.APP_PAYMENT_MODES').' as apm', 'apm.pay_mode_id', '=', 'p.pay_mode_id')
                ->join($this->config->get('tables.ORDERS').' as o', 'o.order_id', '=', 'p.order_id')
                ->join($this->config->get('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'o.supplier_id')
              ->join($this->config->get('tables.STORES').' as mms', 'mms.store_id', '=', 'o.store_id')
              ->join($this->config->get('tables.STORES_EXTRAS').' as mmse', 'mmse.store_id', '=', 'o.store_id')
                ->join($this->config->get('tables.ADDRESS_MST').' as am', 'am.address_id', '=', 'mms.address_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'o.currency_id')
               ->join($this->config->get('tables.ACCOUNT_BALANCE').' as ab', function($ab)
                {
                    $ab->on('ab.account_id', '=', 'o.account_id')
                    ->on('ab.currency_id', '=', 'p.from_currency_id')
                    ->where('ab.wallet_id', '=', $this->config->get('constants.WALLETS.VIM'));
                })
                ->leftjoin($this->config->get('tables.FAVOURITES').' as fav', function($am)
                {
                    $am->on('fav.relative_post_id', '=', 'mms.store_id')
                    ->on('fav.supplier_id', '=', 'mms.supplier_id')
                    ->on('fav.account_id', '=', 'o.account_id')
                    ->where('fav.post_type', '=', $this->config->get('constants.POST_TYPE.STORE'))
                    ->where('fav.is_deleted', '=', $this->config->get('constants.OFF'));
                }) 
                ->where('p.pay_id', $pay_id)
                ->selectRaw('o.account_id, o.payment_status, p.status, apm.pay_mode, o.pay_through, o.order_code, p.pay_id, p.payment_id, o.bill_amount, p.from_amount, p.from_currency_id, p.updated_on, o.currency_id, c.currency as code, mm.company_name, mm.supplier_code, mms.store_name, o.order_id, o.created_by, am. address, am.landmark, mms.store_code, concat_ws("",mmse.phonecode,mmse.mobile_no) as mobile, mms.store_id, fav.fav_id, ab.current_balance, c.currency_symbol')
                ->first();
		
        if (!empty($details))
        {			
            if ($details->status == $this->config->get('constants.PAY_STATUS.CONFIRMED'))
            {
                if ($details->pay_through == $this->config->get('constants.PAYMODE.PAY'))
                {				
                    $op['data'] = [
                        'msg'=>'Payment Successfull',
                        'you_just_paid'=>'You just paid',
                        'merchant'=>'To '.$details->store_name,
						'paid_amount'=>$details->from_amount,
						'currency_symbol'=>$details->currency_symbol,
						'currency_code'=>$details->code,
						'payment_id'=>$details->payment_id,                                               
						'order_code'=>$details->order_code,                                               
                    ];
					$op['receipt']['email'] = trans('general.receipt.email', [
						'fname'=>$fname,
                        'bill_amount'=>CommonLib::currency_format($details->bill_amount, $details->currency_id, true, false),
                        'amount'=>CommonLib::currency_format($details->from_amount, $details->from_currency_id, true, false),
                        'store_name'=>$details->store_name,
                        'address'=>$details->address,
                        'mobile'=>$details->mobile,
                        'order_code'=>$details->order_code]);                   
                    //$op['receipt']['print'] = route('user.payment.receipt', ['order_code'=>$details->order_code]);                   
					$op['receipt']['share'] = trans('general.receipt.share', ['amount'=>CommonLib::currency_format($details->from_amount, $details->from_currency_id, true, false), 'store_name'=>$details->store_name, 'address'=>$details->address, 'mobile'=>$details->mobile]);
                    $op['share']['email'] = ['title'=>trans('user/account.pay.store_share.email.title', ['mrbusiness_name'=>$details->company_name]), 'content'=>trans('user/account.pay.store_share.email.content', ['mrbusiness_name'=>$details->company_name])];                    					
                    if ($is_first)
                    {
						CommonNotifSettings::notify('USER.PAYMENT_SUCCESSFUL', $details->account_id, $this->config->get('constants.ACCOUNT_TYPE.USER'), [
                            'merchant_id'=>$details->store_code,
                            'merchant'=>$details->store_name,
                            'mobile'=>$details->mobile,
                            'bill_amount'=>CommonLib::currency_format($details->bill_amount, $details->currency_id, true, false),
                            'paid_amount'=>CommonLib::currency_format($details->from_amount, $details->from_currency_id, true, false),
                            'payment_id'=>$details->payment_id,
                            'payment_type'=>$details->pay_mode,
                            'trans_id'=>$details->payment_id,
                            'location'=>$details->address,
                            'landmark'=>$details->landmark,
                            'date'=>showUTZ($details->updated_on, 'd-M-Y')
                        ], true, false, false, true, false);  
						
                    } 
                   
                }
                elseif ($details->pay_through == $this->config->get('constants.PAYMODE.REDEEM'))
                {
                    $trans_info = DB::table($this->config->get('tables.REDEEMS').' as r')
                            ->join($this->config->get('tables.WALLET_LANG').' as w', function($w)
                            {
                                $w->on('w.wallet_id', '=', 'r.from_wallet_id')
                                ->where('w.lang_id', '=', $this->config->get('app.locale_id'));
                            })
                            ->where('r.order_id', $details->order_id)
                            ->selectRaw('r.from_amount,w.wallet')
                            ->first();
                    if ($trans_info)
                    {
                        $op['data'] = [
                            'msg'=>trans('user/account.redeem_successful'),
                            'merchant'=>$details->company_name,
                            'store'=>$details->store_name,
                            'store_id'=>$details->store_id,
                            'bill_amount'=>$details->from_amount,
                            'currency_code'=>$details->code];
                        $redeem = CommonLib::currency_format($trans_info->redeem_amount, $details->from_currency_id, false);
                        $op['data']['bonus_wallet'] = [
                            'redeem_success'=>trans('user/account.redeem_success'),
                            'merchant'=>$details->company_name,
                            'store'=>$details->store_name,
                            'redeem_amount'=>$redeem->amount,
                            'currency_symbol'=>$redeem->currency_symbol,
                            'code'=>$details->code,
                            'wallet'=>$trans_info->wallet
                        ];
                    }
                }
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('user/cashback.payment_failed', ['merchant'=>$details->company_name, 'amount'=>CommonLib::currency_format($details->from_amount, $details->from_currency_id)]);
                $op['title'] = trans('user/cashback.payment_failed_title');
            }
        }
        return $op;
    }
    
	
   


}