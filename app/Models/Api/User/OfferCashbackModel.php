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

class OfferCashbackModel extends BaseModel {
	
    public function __construct() {
        parent::__construct();		
		$this->lcObj = new LocationModel;
		$this->commonObj = new CommonModel();		
    }

	public function isCachbackEnabled (array $arr = array())
    {
        extract($arr);
        $dt = getGTZ();
        return DB::table(config('tables.CASHBACK_SETTINGS'))
				->where('supplier_id', $supplier_id)
				->Where(function($sqry) use ($supplier_id, $store_id)
				{
					$sqry->whereNull('store_id')
					->orWhere('store_id', $store_id);
				})
				->where('offer_cashback', config('constants.ON'))
				->Where(function($sqry) use ($dt)
				{
					$sqry->where('is_cashback_period', config('constants.OFF'))
					->orWhere(function($sqry2) use ($dt)
					{
						$sqry2->where('is_cashback_period', config('constants.ON'));
						$sqry2->whereRaw('DATE(cashback_start) <= \''.$dt.'\' AND  DATE(cashback_end)>=\''.$dt.'\'');
					});
				})
				->exists();
    }
	
	 public function searchCustomer (array $arr = array())
    {
        extract($arr);
        return DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
                         ->Join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
                         ->Join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ast', 'ast.account_id', '=', 'um.account_id')
                         ->Join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'ast.currency_id')
                         ->where('um.account_type_id', $this->config->get('constants.ACCOUNT_TYPE.USER'))
                         ->where('um.is_deleted', $this->config->get('constants.OFF'))
                         ->selectRaw('um.account_id, um.user_code, um.uname, um.mobile, concat_ws(\' \',ud.firstname,ud.lastname) as full_name, ast.currency_id as user_currency, um.email, c.currency as code, c.currency_symbol, ud.profile_img')
                        ->where('um.mobile', $user_code)
                        ->orwhere('um.user_code', $user_code)
                        ->first();
    }
	
	public function checkMerchantCreditLimit ($arr = array())
    {
        $store_id = null;
        $system_received_amount = 0;
        extract($arr);
        if (!$is_premium || $has_credit_limit)
        {			
            $details = DB::table($this->config->get('tables.PROFIT_SHARING').' as p')
                    ->where(function($sqry) use($store_id)
                    {
                        $sqry->whereNull('p.store_id');
                        if (!empty($store_id))
                        {
                            $sqry->orWhere('p.store_id', '=', $store_id);
                        }
                    })
                    ->where('p.status', $this->config->get('constants.ON'))
                    ->where('p.is_deleted', $this->config->get('constants.OFF'))
                    ->where('p.supplier_id', $supplier_id)
                    ->orderBy('p.store_id', 'desc')
                    ->selectRaw('p.profit_sharing, p.cashback_on_pay, p.cashback_on_redeem, p.cashback_on_shop_and_earn')
                    ->first();
            
            if (!empty($details))
            {
                $merchant_info = DB::table($this->config->get('tables.ACCOUNT_TAX_DETAILS'))
									->where('relative_postid', '=', $supplier_id)
									->where('post_type', '=', $this->config->get('constants.ACCOUNT_TYPE.SELLER'))
									->where('status', '=', $this->config->get('constants.ON'))
									->where('is_deleted', '=', $this->config->get('constants.OFF'))
									->selectRaw('country_id, state_id')
									->first();
						
                if (!empty($merchant_info))
                {
                    $tot_tax = 0;
                    $charges = [];
                    $cashback_profit = ($bill_amount / 100) * $details->profit_sharing;
                    if ($cashback_profit > 0)
                    {
                        list($tot_tax, $taxes) = $this->getTax([
                            'amount'=>$cashback_profit,
                            'currency_id'=>$currency_id,
                            'country_id'=>$merchant_info->country_id,
                            'state_id'=>$merchant_info->state_id,
                            'statementline_id'=>$this->config->get('stline.ORDER_PAYMENT_COMMISSION.DEBIT')
                                ], true);
                    }
                    $transaction_charge = $bill_amount * ($this->config->get('constants.TRANSACTION_CHARGE') / 100);
                    $settlement_amount = $system_received_amount - ($cashback_profit + $tot_tax + $transaction_charge);
                    $credit_limit = json_decode($this->getSetting('supplier_credit_limit_by_currency'), true);
                    $credit_limit = array_key_exists($currency_id, $credit_limit) ? $credit_limit[$currency_id] : 0;
                    return $settlement_amount >= 0 || DB::table($this->config->get('tables.SUPPLIER_MST').' as m')
                                    ->join($this->config->get('tables.ACCOUNT_BALANCE').' as ab', function($ab) use($currency_id)
                                    {
                                        $ab->on('ab.account_id', '=', 'm.account_id')
                                        ->where('ab.currency_id', '=', $currency_id)
                                        ->where('ab.wallet_id', '=', $this->config->get('constants.WALLETS.VIM'));
                                    })
                                    ->where('m.supplier_id', $supplier_id)
                                    ->where(DB::raw('ab.current_balance+'.$settlement_amount), '>=', $credit_limit)
                                    ->exists();
                }
                else
                {
                    return false;
                }
            }
        }
        return true;
    }
	
	public function creditCashback (array $arr)
    {
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
                    'approved_by'=>$mr_account_id,
                    'order_type'=>$this->config->get('constants.ORDER.TYPE.IN_STORE'),
                    'pay_through'=>$this->config->get('constants.PAYMODE.SHOP_AND_EARN'),
                    'payment_status'=>$this->config->get('constants.ORDER.PAYMENT_STATUS.PAID'),
                    'statementline_id'=>$this->config->get('stline.MR_ORDERS.GET_CASHBACK'),
                    'order_date'=>getGTZ(),
                ]); 
				
                if (!empty($order_id))
                {
                    $this->commonObj->updateOrderCount($store_id);
                    $update['order_code'] = $store_id.rand(111, 999).$order_id;
                    DB::table($this->config->get('tables.ORDERS'))
                            ->where('order_id', $order_id)
                            ->update($update);
                }

                $pay['order_id'] = $order_id;
                $pay['payment_type_id'] = $payment_type;
                $pay['pay_mode_id'] = $this->config->get('constants.PAYMENT_MODES.CASH');
                $pay['from_currency_id'] = $currency_id;
                $pay['from_amount'] = $bill_amount;
                $pay['to_currency_id'] = $currency_id;
                $pay['to_amount'] = $bill_amount;
                $pay['status'] = $this->config->get('constants.ORDER.PAY.STATUS.PAID');
                $pay['created_on'] = getGTZ();
                $pay['updated_by'] = $mr_account_id;				
                $pay_id = DB::table($this->config->get('tables.PAY'))->insertGetID($pay);
				
            }
            if ($order_id)
            {
                $arr['order_id'] = $order_id;
                if ($cashback_amt = $this->commonObj->updateOrderCommission($order_id))
                {
                    $this->commonObj->releaseOrderCommission($order_id);
                    return (object) ['cashback_amt'=>$cashback_amt, 'order_code'=>$update['order_code'], 'order_id'=>$order_id, 'user_currency'=>$user_currency];
                }
                else
                {
                    return false;
                }
            }
        }
        return false;
    }
}