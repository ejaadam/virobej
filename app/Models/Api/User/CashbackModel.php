<?php
namespace App\Models\Api\User;

use DB;
use File;
use TWMailer;
use App\Models\BaseModel;
use App\Models\LocationModel;
use App\Models\Api\CommonModel;
use Config;
use CommonLib;

class CashbackModel extends BaseModel {
	
    public function __construct() {
        parent::__construct();		
		$this->lcObj = new LocationModel;
		$this->commonObj = new CommonModel();		
    }

	public function getCashbackStoreSearch (array $arr) 
	{
		$country_id = '';
        extract($arr);
        $query = DB::table($this->config->get('tables.SUPPLIER_MST').' as mm')
                ->join($this->config->get('tables.STORES').' as sm', 'sm.supplier_id', '=', 'mm.supplier_id')
                ->join($this->config->get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 'sm.store_id')
				->join($this->config->get('tables.ACCOUNT_MST').' as amst', 'amst.account_id', '=', 'mm.account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as lla', 'lla.account_id', '=', 'sm.last_login_account')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as llad', 'llad.account_id', '=', 'sm.last_login_account')
                ->join($this->config->get('tables.CASHBACK_SETTINGS').' as mcs', 'mcs.supplier_id', '=', 'sm.supplier_id')
                ->join($this->config->get('tables.ADDRESS_MST').' as am', function($am) use($country_id)
                {
                    $am->on('am.address_id', '=', 'sm.address_id');		
					/* As per instruction, country removed from store search */	
                    //->where('am.country_id', '=', $country_id);
                })
                ->leftjoin($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'sm.currency_id')
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
                ->where('mm.is_deleted', $this->config->get('constants.OFF'))
                ->where('mm.status', $this->config->get('constants.ON'))
                ->where('sm.is_approved', $this->config->get('constants.ON'))
                ->where('sm.status', $this->config->get('constants.ON'));
        if (isset($mobile) && !empty($mobile))
        {
            $query->where('sm.store_code', $mobile);
         
        }
        else
        {
            $query->where('sm.store_code', $store_code);
        }
		
        $details = $query->selectRaw('mm.account_id as supplier_account_id, sm.store_id, sm.supplier_id, mm.company_name, mm.supplier_code, lla.account_id as staff_account_id, COALESCE(lla.uname,sm.store_code) as staff_uname, COALESCE(CONCAT(llad.firstname,\' \',llad.lastname),sm.store_name) as staff_full_name, COALESCE(lla.mobile) as staff_mobile, COALESCE(lla.email) as staff_email, sm.store_code, sm.store_name, mcs.is_redeem_otp_required, mcs.shop_and_earn, sm.last_login_account, c.currency, c.currency_symbol, c.currency_id as currency_id, am.country_id, am.address, sm.store_logo, mm.logo, mm.is_premium, mm.has_credit_limit, amst.account_type_id, sm.store_id as store_account_id, se.mobile_no')->first();
	
        if (!empty($details))
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
        }
        return $details;	
	}
	
	public function checkuser_cashback_percentage (array $arr)
    {		
        extract($arr);
        return DB::table($this->config->get('tables.PROFIT_SHARING').' as ps')
                        ->Where(function($sqry) use ($supplier_id, $store_id)
                        {
                            $sqry->where('ps.supplier_id', $supplier_id)
								->where(function($sqry2) use ($store_id)
								{
									//$sqry2->where('ps.store_id', '=', $this->config->get('constants.OFF'))
									$sqry2->whereNull('ps.store_id')
									->orWhere('ps.store_id', $store_id);
								});
                        })
                        ->where('ps.status', $this->config->get('constants.ON'))
                        ->where('ps.is_deleted', $this->config->get('constants.OFF'))
                        ->orderBy('ps.store_id', 'desc')
                        ->first();
		
    }
	
	public function creditCashback (array $arr = array())
    {
		$store_account_id = 0;
        extract($arr);		
        
        if (!empty($arr))
        {			
            if (!isset($order_id))
            {	                		
                $order_id = DB::table($this->config->get('tables.ORDERS'))
                        ->insertGetID([
                    'account_id'=>$account_id,
                    'supplier_id'=>$supplier_id,
                    'store_id'=>$store_id,
                    'currency_id'=>$currency_id,
                    'bill_amount'=>$bill_amount,
                    'paid_amount'=>$bill_amount, 
					'statementline_id'=>$this->config->get('stline.MR_ORDERS.GET_CASHBACK'),	
                    'pay_through'=>$this->config->get('constants.PAYMODE.SHOP_AND_EARN'),
                    'order_type'=>$this->config->get('constants.ORDER.TYPE.IN_STORE'),
                    'collected_by'=>$account_type_id,
                    'payment_status'=>$this->config->get('constants.ORDER.PAYMENT_STATUS.PAID'),
                    'approved_by'=>$store_account_id,
					'created_by'=>$account_id,
                    'order_date'=>getGTZ(),
                ]);
                $pay = [];
                $pay['order_id'] = $order_id;
                $pay['payment_type_id'] = $payment_type;
                $pay['pay_mode_id'] = $this->config->get('constants.PAYMENT_MODES.CASH');                
                $pay['to_currency_id'] = $currency_id;
                $pay['to_amount'] = $bill_amount;
                $pay['from_currency_id'] = $currency_id;
               // $pay['from_amount'] = $bill_amount * $this->get_currency_rate($currency_id, $user_currency);
			    $pay['from_amount'] = $bill_amount;
                $pay['status'] = $this->config->get('constants.PAYMENT_STATUS.CONFIRMED');
                $pay['updated_on'] = $pay['created_on'] = $create_on = getGTZ();
                $pay['updated_by'] = $account_id;
                $pay_id = DB::table($this->config->get('tables.PAY'))->insertGetID($pay);						
                //$payment_id = rand(111111, 999999).$pay_id;		
				$payment_id = rand(100, 999).$pay_id;	
                DB::table($this->config->get('tables.PAY'))
                        ->where('pay_id', $pay_id)
                        ->update(['payment_id'=>$payment_id]);
						
                if (!empty($order_id))
                {
                    $this->commonObj->updateOrderCount($store_id);                    
					$update['order_code'] = $order_code = $store_id.rand(111, 999).$order_id;
                    DB::table($this->config->get('tables.ORDERS'))
                            ->where('order_id', $order_id)
                            ->update($update);
                }
            }
            else
            {
                $order_code = DB::table($this->config->get('tables.ORDERS'))
                        ->where('order_id', $order_id)
                        ->value('order_code');
            } 
		
            if ($order_id)
            {
                $arr['order_id'] = $order_id;
				if ($cashback_amt = $this->commonObj->updateOrderCommission($order_id))
                {						
                    $this->commonObj->releaseOrderCommission($order_id);
					$wallet_id = $this->config->get('constants.WALLETS.VIM');
					$balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ab')
                                ->join($this->config->get('tables.WALLET_LANG').' as w', function($w)
                                {
                                    $w->on('w.wallet_id', '=', 'ab.wallet_id');
                                    //$w->where('w.lang_id', '=', $this->config->get('app.locale_id'));
                                })
                                ->where('ab.account_id', $account_id)
                                ->where('ab.wallet_id', $wallet_id)
                                ->where('ab.currency_id', $user_currency)
                                ->select('w.wallet', 'ab.current_balance', 'ab.currency_id')
                                ->first();                    
                    if (!empty($balance))
                    {
                        $balance->cashback_amount = $cashback_amt;
                        $balance->order_code = $update['order_code'];
                    }
                    return (object) $balance; 
                }
            }
        }
        return false;
    }
}