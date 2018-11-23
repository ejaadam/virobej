<?php
namespace App\Models\Api\User;
use DB;
use File;
use TWMailer;
use App\Models\BaseModel;
use CommonLib;
use App\Models\Api\CommonModel;

class RedeemModel extends BaseModel {
	
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
				->join($this->config->get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 'sm.supplier_id')                
                ->join($this->config->get('tables.CASHBACK_SETTINGS').' as mcs', 'mcs.supplier_id', '=', 'mm.supplier_id')
				->join($this->config->get('tables.ADDRESS_MST').' as am', function($am) use($country_id)
                {
                    $am->on('am.address_id', '=', 'sm.address_id')
                    ->where('am.country_id', '=', $country_id);
                })            
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as lla', 'lla.account_id', '=', 'sm.last_login_account')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as llad', 'llad.account_id', '=', 'sm.last_login_account')
                ->leftjoin($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'sm.currency_id')
				//->where('mm.is_verified', '=', $this->config->get('constants.MERCHANT.IS_VERIFIED.VERIFIED'))               
               // ->where('mm.is_deleted', $this->config->get('constants.OFF'))
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
        $details = $query->selectRaw('COALESCE(sm.last_login_account, mm.account_id) as supplier_account_id, sm.store_id, sm.supplier_id, mm.company_name, mm.supplier_code, lla.account_id as staff_account_id, COALESCE(lla.uname, sm.store_code) as staff_uname, COALESCE(CONCAT(llad.firstname,\' \',llad.lastname), sm.store_name) as staff_full_name, COALESCE(lla.mobile,se.mobile_no) as staff_mobile, COALESCE(lla.email,se.email) as staff_email, se.mobile_no, sm.store_code, sm.store_name, sm.currency_id, am.country_id, mcs.is_redeem_otp_required, mcs.redeem, sm.last_login_account, c.currency as code, c.currency_symbol, c.decimal_places, c.currency_id, am.address, sm.store_logo, mm.logo, c.min_amount')->first();        						
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
	
	public function getRedeemWallet (array $arr = array(), $postdata)
    {   
        extract($arr);
        $redeem_amount = 0;
        $user_bill_amount = 0;
		//return $supplier_id;
		//return $postdata;
        $profit_sharing = DB::table($this->config->get('tables.PROFIT_SHARING'))
                         ->where('status', $this->config->get('constants.ON'))
                         ->where('store_id', $store_id)
					     ->orwhereNull('store_id')
                         ->where('supplier_id', $supplier_id) 
						 ->value('profit_sharing');		
        $balance = '';
        $query = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ab')
                ->leftjoin($this->config->get('tables.WALLET').' as w', 'w.wallet_id', '=', 'ab.wallet_id')
                ->leftjoin($this->config->get('tables.WALLET_LANG').' as wl', function($w)
                {
                    $w->on('wl.wallet_id', '=', 'ab.wallet_id');
                    //->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                }) 
				->leftjoin($this->config->get('tables.CURRENCIES').' as c', function($w) use ($postdata)
				{
					$w->on('c.currency_id', '=', 'ab.currency_id')
					->where('c.currency_id', '=', $postdata['currency_id']);
				});
		if (!empty($postdata['wallet']))
        {
            $query->where('w.wallet_code', $postdata['wallet']);
        }
        $query->where('ab.current_balance', '>', 0);
        $balance = $query->where('ab.account_id', $account_id)
                ->where('w.is_point', $this->config->get('constants.ON'))
                ->select('wl.wallet', 'w.redeem_debit_per', 'w.wallet_code', 'ab.current_balance', 'c.currency_id', 'c.currency as code', 'c.currency_symbol as symbol', 'c.decimal_places')
                ->get();
        array_walk($balance, function(&$wallet, $key) use($profit_sharing, $amount, $currency_id)
        {
            $redeem_per = ($wallet->redeem_debit_per * $profit_sharing) / 100;
            $redeem_amount = round(($amount * $redeem_per) / 100, $wallet->decimal_places);
            $wallet->redeem = true;			
            if ($wallet->currency_id != $currency_id)
            {
                $currency_rate = DB::table($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                        ->where('from_currency_id', $wallet->currency_id)     // User Currency
                        ->where('to_currency_id', $currency_id)   // Store Currency
                        ->value('rate');
                $user_bill_amount = $amount * $currency_rate;
                $user_redeem_amt = $redeem_amount * $currency_rate;
                $wallet->current_balance = $wallet->current_balance * $currency_rate;
            }
            else
            {
                $user_bill_amount = $amount;
                $user_redeem_amt = $redeem_amount;
            }
            $user_redeem_amt = $user_redeem_amt;
            $wallet->store_currency_id = $currency_id;
            $wallet->user_currency_id = $wallet->currency_id;
            if ($wallet->current_balance >= $user_redeem_amt)
            {
                $wallet->user_redeem_amount = $user_redeem_amt;
                $wallet->store_redeem_amount = $redeem_amount;
                //$result->user_redeem_amount =  sprintf('%.2f', $user_redeem_amt);
                //$result->store_redeem_amount =  sprintf('%.2f', $redeem_amount);
                $wallet->redeem = true;
            }
            else
            {
                //$result->user_redeem_amount = 0;
                //$result->store_redeem_amount = 0;
                //$result->redeem = false;
                //$result->msg = 'Insufficient Balance';
                $wallet->user_redeem_amount = $wallet->current_balance;
                $wallet->store_redeem_amount = $wallet->current_balance;
                $wallet->redeem = true;
            }
            $wallet->balance = CommonLib ::currency_format($wallet->current_balance, $currency_id, true, false);
            unset($wallet->current_balance);
            unset($wallet->redeem_debit_per);
            unset($wallet->currency_id);
        });		
        return $balance;
    }
	
	public function getCashbackWallet (array $arr = array(), $postdata)
    {
        extract($arr);
        $redeem_amount = 0;
        $user_bill_amount = 0;       
						
		$profit_sharing = DB::table(Config('tables.PROFIT_SHARING'))
                         ->where('status', Config('constants.ON'))
                         ->where('store_id', $store_id)
					     ->orwhereNull('store_id')
                         ->where('supplier_id', $supplier_id) 
						 ->value('profit_sharing');	

						 
        $balance = '';
		$query = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ab')
                ->join($this->config->get('tables.WALLET').' as w', 'w.wallet_id', '=', 'ab.wallet_id')
                ->join($this->config->get('tables.WALLET_LANG').' as wl', function($w)
                {
                    $w->on('wl.wallet_id', '=', 'ab.wallet_id');
                    //->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                }) 
				->join($this->config->get('tables.CURRENCIES').' as c', function($w) use ($postdata)
				{
					$w->on('c.currency_id', '=', 'ab.currency_id')
					->where('c.currency_id', '=', $postdata['currency_id']);
				});
        
        if (!empty($postdata['wallet']) && ($postdata['wallet'] == 'xpc'))
        {
            $query->where('w.wallet_code', $postdata['wallet']);
        }
        $query->where('ab.current_balance', '>', 0);
        $balance = $query->where('ab.account_id', $account_id)
                ->where('w.is_point', $this->config->get('constants.OFF_FLAG'))				
                ->select('wl.wallet', 'w.redeem_debit_per', 'w.wallet_code', 'ab.current_balance', 'c.currency_id', 'c.currency as code', 'c.currency_symbol as symbol', 'c.decimal_places')
                ->get();

        array_walk($balance, function(&$result, $key) use($profit_sharing, $amount, $currency_id, $postdata)
        {
            $redeem_amount = $amount;
            $result->redeem = true;
            if ($result->currency_id != $currency_id)
            {
                /* $currency_rate = DB::table($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                        ->where('from_currency_id', $result->currency_id)
                        ->where('to_currency_id', $currency_id)
                        ->value('rate');
                $user_bill_amount = $amount * $currency_rate;
                $user_redeem_amt = round($redeem_amount * $currency_rate, $result->decimal_palces);
                $store_redeem_amt = round($redeem_amount, $result->decimal_places);
                $result->current_balance = $result->current_balance * $currency_rate; */
            }
            else
            {
                $user_bill_amount = $amount;
                $user_redeem_amt = round($redeem_amount, $result->decimal_places);
                $store_redeem_amt = round($redeem_amount, $result->decimal_places);
            }
            $result->store_currency_id = $currency_id;
            $result->user_currency_id = $result->currency_id;
            if ($result->current_balance >= ($user_redeem_amt - $postdata['min_bonus']))
            {
                $result->redeem = true;
                $result->user_redeem_amount = $user_redeem_amt;
                $result->store_redeem_amount = $store_redeem_amt;
            }
            else
            {
                $result->user_redeem_amount = 0;
                $result->store_redeem_amount = 0;
                $result->redeem = false;
                $result->msg = 'Insufficient Balance';
            }
            $result->balance = CommonLib ::currency_format($result->current_balance, $currency_id, true, false);
            unset($result->redeem_debit_per);
            unset($result->currency_id);
        });
        return $balance;
    }
	
	
	public function redeemWalletValidate (array $arr, $data)
    {	
        if (isset($data['wallet']) && !empty($data['wallet']))
        {
            if (!isset($data['bonus_wallets']) && empty($data['bonus_wallets']))
            {
                $op['msg'] = 'You dont have sufficient balance to redeem from '.$data['wallet'].' wallet.';                
                $op['status'] = 422;
                return $op;
            }
			//return $data;
            if (isset($data['bonus_wallets']) && !empty($data['bonus_wallets']))
            {
                $bonus_wallet = array('vis', 'vib');                
                if (in_array($data['wallet'], $bonus_wallet))
                {
                    $bonus_wallets = $data['bonus_wallets'];
                    array_walk($bonus_wallets, function(&$result, $key) use(&$bonus_wallets, $data, $arr)
                    {
                        if (isset($result['wallet_code']))
                        {
                            $comp_res = strcmp($result['wallet_code'], $data['wallet']);
                            //print_r($comp_res);exit;
                            if ($comp_res == 0)
                            {
                                $result['redeem_amount'] = $data['amount'];
                                $result['is_redeem'] = true;
                                //balance_to_pay = bill amount - wallet redeem amount
                                $result['balance_to_pay'] = $arr['amount'] - $data['amount'];
                                $bonus_wallets = [$result];
                            }
                            else
                            {
                                unset($result[$key]);
                            }
                        }
                    });
                    $bonus_wallets = array_values($bonus_wallets);

                    if ($bonus_wallets[0]['wallet_code'] != $data['wallet'])
                    {
						print_r($data);exit;
                        //$op['msg'] = 'You dont have sufficient balance in this '.$data['wallet'].' wallet.';
                        $op['msg'] = 'You dont have sufficient balance in this '.$bonus_wallets[0]['wallet'];
                        $op['status'] = 422;
                        return $op;
                    }
                    if ($data['amount'] > $bonus_wallets[0]['max_redeem_amt'])
                    {
                        $op['msg'] = 'You Cannot Redeem from '.$bonus_wallets[0]['wallet'].'. Your Maximum Redeem Amount is '.$bonus_wallets[0]['max_redeem_amt'].' '.$bonus_wallets[0]['store']['code'];
                        $bonus_wallets[0]['is_redeem'] = false;
                        $op['status'] = 422;
                    }
                    else
                    {
                        if ($data['user_currency_id'] != $arr['currency_id'])
                        {
                            $currency_rate = DB::table($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                                    ->where('from_currency_id', $arr['currency_id'])     // Store Currency
                                    ->where('to_currency_id', $data['user_currency_id'])    // User Currency
                                    ->value('rate');
                            $redeem_amount = $data['amount'] * $currency_rate;
                            $bonus_wallets[0]['user'] = ['currency'=>$data['user_currency_id'], 'redeem_amt'=>$redeem_amount, 'rate'=>$currency_rate, 'symbol'=>'', 'code'=>''];
                            $bonus_wallets[0]['store'] = ['currency'=>$arr['currency_id'], 'redeem_amt'=>$data['amount'], 'symbol'=>$bonus_wallets[0]['store']['symbol'], 'code'=>$bonus_wallets[0]['store']['code']];
                        }
                        else
                        {
                            $bonus_wallets[0]['user'] = ['currency'=>$data['user_currency_id'], 'redeem_amt'=>$data['amount'], 'rate'=>1, 'symbol'=>$data['user_currency_symbol'], 'code'=>$data['user_currency_code']];
                            $bonus_wallets[0]['store'] = ['currency'=>$arr['currency_id'], 'redeem_amt'=>$data['amount'], 'symbol'=>$bonus_wallets[0]['store']['symbol'], 'code'=>$bonus_wallets[0]['store']['code']];
                        }
                        $op['msg'] = 'ok';
                        $op['status'] = 200;
                    }
                    $op['bonus_wallet'] = $bonus_wallets;
                    return $op;
                }
            }
        }
    }

	public function redeemVimoneyWalletValidate (array $arr, array $data)
    {
        $vimoney_wallet = $data['vimoney_wallet'];
        if ($data['amount'] >= $data['vimoney'])
        {
            array_walk($vimoney_wallet, function(&$result, $key) use($data, $arr)
            {
                $result['redeem_amount'] = $data['vimoney'];
                $result['is_redeem'] = true;
            });

            $vimoney_wallet = array_values($vimoney_wallet);
            //return $cashback_wallet;
            if ($vimoney_wallet[0]['max_redeem_amt'] < $data['amount'])
            {
                //$op['msg'] = 'You Cannot Redeem. Your Maximum Redeem Amount is '.$vimoney_wallet[0]['max_redeem_amt'].' '.$vimoney_wallet[0]['currency_code'];
                $op['msg'] = 'You Cannot Redeem. Your Maximum Redeem Amount is '.$vimoney_wallet[0]['balance'];
                $op['status'] = 400;
            }
            else
            {

                if ($data['user_currency_id'] != $arr['currency_id'])
                {
                    $currency_rate = DB::table($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                            ->where('from_currency_id', $arr['currency_id'])     // Store Currency
                            ->where('to_currency_id', $data['user_currency_id'])    // User Currency
                            ->value('rate');

                    $redm_amount = $data['vimoney'] * $currency_rate;
                    $vimoney_wallet[0]['user'] = ['currency'=>$data['user_currency_id'], 'redeem_amt'=>$redm_amount, 'rate'=>$currency_rate, 'symbol'=>'', 'code'=>''];
                    $vimoney_wallet[0]['store'] = ['currency'=>$arr['currency_id'], 'redeem_amt'=>$data['vimoney'], 'symbol'=>$vimoney_wallet[0]['store']['symbol'], 'code'=>$vimoney_wallet[0]['store']['code']];
                }
                else
                {
                    $vimoney_wallet[0]['user'] = ['currency'=>$data['user_currency_id'], 'redeem_amt'=>$data['vimoney'], 'rate'=>1, 'symbol'=>$data['user_currency_symbol'], 'code'=>$data['user_currency_code']];
                    $vimoney_wallet[0]['store'] = ['currency'=>$arr['currency_id'], 'redeem_amt'=>$data['vimoney'], 'symbol'=>$vimoney_wallet[0]['store']['symbol'], 'code'=>$vimoney_wallet[0]['store']['code']];
                }
                $op['msg'] = 'ok';
                $op['status'] = 200;
            }
        }
        else
        {
            $op['msg'] = 'Your Vi-Money redeem amount must be less than the bill amount.';
            $op['status'] = 400;
        }
        $op['vimoney_wallet'] = $vimoney_wallet;

        return $op;
    }
	
	public function checkSupplierCreditLimit ($arr = array())
    {	
        $store_id = null;
		$is_premium = null;
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
                    ->selectRaw('p.profit_sharing,p.cashback_on_pay,p.cashback_on_redeem,p.cashback_on_shop_and_earn')
                    ->first();
            if (!empty($details))
            {
				$merchant_info = DB::table($this->config->get('tables.ACCOUNT_TAX_DETAILS'))
                        ->where('relative_postid', '=', $supplier_id)
                        ->where('post_type', '=', $this->config->get('constants.ACCOUNT_TYPE.SELLER'))
                        ->where('status', '=', $this->config->get('constants.ON'))
                        ->where('is_deleted', '=', $this->config->get('constants.OFF'))
                        ->selectRaw('country_id,state_id')
                        ->first();
						
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
        }
        return true;
    }
	
	public function confirmRedeem (array $arr = array())
    {		    
        extract($arr);        
        $bill_amount = $store->amount;
        $currency_id = $store->currency_id;        
        $redeem_amount = $bonus_wallet[0]['user']['redeem_amt'];
        $rate = $bonus_wallet[0]['user']['rate'];
        $store_redeem_amount = $bonus_wallet[0]['store']['redeem_amt'];
        $wallet_code = $bonus_wallet[0]['wallet_code'];
        $wallet_id = $this->config->get('constants.BONUS_WALLET.'.$wallet_code);
        $user_account_id = $store->account_id;		
        $order_id = DB::table($this->config->get('tables.ORDERS'))
                ->insertGetID([
            'order_type'=>$this->config->get('constants.ORDER.TYPE.IN_STORE'),
            'account_id'=>$user_account_id,
            'supplier_id'=>$store->supplier_id,
            'store_id'=>$store->store_id,
            'currency_id'=>$currency_id,
            'bill_amount'=>$bill_amount,
			'statementline_id'=>$this->config->get('stline.MR_ORDERS.REDEEM'),
            'pay_through'=>$this->config->get('constants.PAYMODE.REDEEM'),
            'approved_by'=>$supplier_account_id,
            'order_date'=>getGTZ(),
        ]); 		
        if ($order_id)
        {			
            $this->commonObj->updateOrderCount($store->store_id);
            //$update['order_code'] = $this->config->get('constants.ORDER_CODE_PREFIX').$order_id;
			//$update['order_code'] = $order_code = rand(1111111, 9999999).$order_id;
			$update['order_code'] = $order_code = $supplier_id.rand(111, 999).$order_id;
            DB::table($this->config->get('tables.ORDERS'))
                    ->where('order_id', $order_id)
                    ->update($update);
            $pay['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.WALLET');
            $pay['pay_mode_id'] = $this->config->get('constants.PAYMENT_MODES.vim');
            $pay['from_currency_id'] = $currency_id;
            $pay['from_amount'] = $redeem_amount;
            $pay['from_wallet_id'] = $wallet_id;
            $pay['to_currency_id'] = $user_currency_id;
            $pay['to_amount'] = $arr['redeem_amount'] * $this->get_currency_rate($user_currency_id, $currency_id);
            $pay['status'] = $this->config->get('constants.PAY_STATUS.PENDING');
            $pay['updated_by'] = $account_id;
            $pay['created_on'] = getGTZ();
            $pay['order_id'] = $order_id;			
            $pay_id = DB::table($this->config->get('tables.PAY'))->insertGetID($pay);			            
			$payment_id = $store_code . rand(100, 999).$pay_id;
            DB::table($this->config->get('tables.PAY'))
                    ->where('pay_id', $pay_id)
                    ->update(['payment_id'=>$payment_id]);
            $transaction_id = $this->generateTransactionID();
            $created_on = getGTZ();			
            $redeem_id = DB::table($this->config->get('tables.REDEEMS'))
                    ->insertGetID([
                'order_id'=>$order_id,
                'updated_by'=>$account_id,
                'from_wallet_id'=>$wallet_id,                
                'from_currency_id'=>$currency_id,
                'from_amount'=>$store_redeem_amount,
                'currency_id'=>$user_currency_id,
                'transaction_id'=>$transaction_id,
                'redeem_amount'=>$redeem_amount,
                'created_on'=>$created_on,
                'status'=>$this->config->get('constants.REDEEM_STATUS.PENDING')
            ]); 			
            if (!empty($redeem_id))
            {
                 if ($trans_id = $this->updateAccountTransaction([
                    'from_account_id'=>$account_id,
                    'from_transaction_id'=>$transaction_id,
                    'from_wallet_id'=>$wallet_id,
                    'currency_id'=>$currency_id,
                    'amt'=>$redeem_amount,
					'paidamt'=>$redeem_amount,
                    'relation_id'=>$redeem_id,
                    'debit_remark_data'=>['bill_amount'=>CommonLib::currency_format($bill_amount, $currency_id, true, false), 'store_name'=>$store->store_name, 'order_code'=>$update['order_code']],
                    'transaction_for'=>'REDEEM']))
                {					
                    DB::table($this->config->get('tables.REDEEMS'))
                            ->where('redeem_id', $redeem_id)
                            ->update(['status'=>$this->config->get('constants.REDEEM_STATUS.CONFIRMED'), 'updated_on'=>getGTZ(),]);
                    DB::table($this->config->get('tables.PAY'))
                            ->where('pay_id', $pay_id)
                            ->update([
                                'status'=>$this->config->get('constants.PAY_STATUS.CONFIRMED'),
                                'updated_on'=>getGTZ(),
                                'updated_by'=>$account_id
                    ]);
                    if ($cashback_amount = $this->commonObj->updateOrderCommission($order_id))
                    {						
                        $this->commonObj->releaseOrderCommission($order_id);
						$balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ab')
                                ->join($this->config->get('tables.WALLET_LANG').' as w', function($w)
                                {
                                    $w->on('w.wallet_id', '=', 'ab.wallet_id');
                                    //$w->where('w.lang_id', '=', $this->config->get('app.locale_id'));
                                })
                                ->where('ab.account_id', $account_id)
                                ->where('ab.wallet_id', $wallet_id)
                                ->where('ab.currency_id', $user_currency_id)
                                ->select('w.wallet', 'ab.current_balance', 'ab.currency_id')
                                ->first();                         
                        if (!empty($balance))
                        {
                            $redeem_amount = $bonus_wallet[0]['store']['redeem_amt'];
                            $balance->redeem_amount = $redeem_amount;
                            $balance->redeem_time = $created_on;
                            $balance->order_id = $order_id;
                            $balance->order_code = $update['order_code'];
                            $balance->trans_id = $trans_id;
                            $balance->payment_id = $payment_id;
                            return (object) $balance;
                        }
                    }
                }
                else
                {					
                    DB::table($this->config->get('tables.REDEEMS'))
                            ->where('redeem_id', $redeem_id)
                            ->update(['status'=>$this->config->get('constants.REDEEM_STATUS.FAILED')]);
                    DB::table($this->config->get('tables.PAY'))
                            ->where('pay_id', $pay_id)
                            ->update([
                                'status'=>$this->config->get('constants.PAY_STATUS.FAILED'),
                                'updated_on'=>getGTZ(),
                                'updated_by'=>$account_id
                    ]);
                }
            }
        }
        return false;
    }
	
	public function confirmCashPayment (array $arr = array())
    {	
        extract($arr);        
        $currency_id = $store->currency_id;
        if (!isset($order_id))
        {			
            $order_id = DB::table($this->config->get('tables.ORDERS'))
                    ->insertGetID([
                'order_type'=>$this->config->get('constants.ORDER.TYPE.IN_STORE'),
                'account_id'=>$account_id,
                'supplier_id'=>$store->supplier_id,
                'store_id'=>$store->store_id,
                'currency_id'=>$currency_id,
                'bill_amount'=>$store->amount,   
				'statementline_id'=>$this->config->get('stline.MR_ORDERS.REDEEM'),	
                'pay_through'=>$this->config->get('constants.PAYMODE.REDEEM'),
                'order_date'=>getGTZ(),
            ]);
            $this->commonObj->updateOrderCount($store->store_id);
            //$update['order_code'] = $this->config->get('constants.ORDER_CODE_PREFIX').$order_id;
			$update['order_code'] = $order_code = $supplier_id.rand(111, 999).$order_id;
            DB::table($this->config->get('tables.ORDERS'))
                    ->where('order_id', $order_id)
                    ->update($update);
        }
        if ($order_id)
        {			
            $pay_id = DB::table($this->config->get('tables.PAY'))
                    ->insertGetID([
                'order_id'=>$order_id,
                'pay_mode_id'=>$this->config->get('constants.PAYMENT_MODES.CASH'),
                'payment_type_id'=>$this->config->get('constants.PAYMENT_TYPES.CASH'),                
                'from_currency_id'=>$currency_id,
                'to_currency_id'=>$user_currency_id,
                'from_amount'=>$cash['amount'],
                'to_amount'=>($cash['amount'] * $this->get_currency_rate($currency_id, $user_currency_id)),
                'created_on'=>getGTZ()
            ]);

            $payment_id = rand(100, 999).$pay_id;
            DB::table($this->config->get('tables.PAY'))
                    ->where('pay_id', $pay_id)
                    ->update(['payment_id'=>$payment_id]);

            if (DB::table($this->config->get('tables.PAY'))
                            ->where('pay_id', $pay_id)
                            ->update([
                                'status'=>$this->config->get('constants.PAY_STATUS.CONFIRMED'),
                                'updated_on'=>getGTZ(),
                                'updated_by'=>$account_id
                            ]))
            {
                if ($cashback_amount = $this->commonObj->updateOrderCommission($order_id))
                {
                    $this->commonObj->releaseOrderCommission($order_id);
                    return $cashback_amount;
                }
            }            
        }
        return false;
    }
	
	public function confirmVimoneyRedeem (array $arr = array())
    {	//return $arr;
        $order_code = '';
        extract($arr);
        //$to_wallet_id = $this->config->get('constants.CASHBACK_CREDIT_WALLET');
        $currency_id = $store->currency_id;
        //$arr['redeem_amount'] = $redeem_amount = $xpayback_wallet[0]['redeem_amount'];
        $arr['redeem_amount'] = $redeem_amount = $vimoney_wallet[0]['user']['redeem_amt'];
        //$redeem_amount = $bonus_wallet[0]['user']['redeem_amt'];
        $rate = $vimoney_wallet[0]['user']['rate'];
        $store_redeem_amount = $vimoney_wallet[0]['store']['redeem_amt'];
        $wallet_code = $vimoney_wallet[0]['wallet_code'];        
		//$wallet_id = 1;	
        $wallet_id = $this->config->get('constants.VIMONEY_WALLET');        	
        if (!isset($order_id))
        {
            $order_id = DB::table($this->config->get('tables.ORDERS'))
                    ->insertGetID([
                'order_type'=>$this->config->get('constants.ORDER.TYPE.IN_STORE'),
                'pay_through'=>$this->config->get('constants.PAYMODE.REDEEM'),
				'statementline_id'=>$this->config->get('stline.MR_ORDERS.REDEEM'),
                'account_id'=>$account_id,
                'supplier_id'=>$store->supplier_id,
                'store_id'=>$store->store_id,
                'currency_id'=>$currency_id,
                'bill_amount'=>$store->amount,
                'order_date'=>getGTZ(),
            ]);
            $this->commonObj->updateOrderCount($store->store_id);
            //$order_code = $update['order_code'] = $this->config->get('constants.ORDER_CODE_PREFIX').$order_id;
			$update['order_code'] = $order_code = $supplier_id.rand(111, 999).$order_id;
            DB::table($this->config->get('tables.ORDERS'))
                    ->where('order_id', $order_id)
                    ->update($update);
        }
        else
        {
            $order_code = DB::table($this->config->get('tables.ORDERS'))
                    ->where('order_id', $order_id)
                    ->value('order_code');
        }
		
        if ($order_id)
        {
            $pay['payment_type_id'] = $this->config->get('constants.PAYMENT_TYPES.WALLET');
            $pay['pay_mode_id'] = $this->config->get('constants.PAYMENT_MODES.WALLET');
            $pay['from_currency_id'] = $currency_id;
            $pay['from_amount'] = $arr['redeem_amount'];
            $pay['from_wallet_id'] = $wallet_id;
            $pay['to_currency_id'] = $user_currency_id;
            $pay['to_amount'] = $arr['redeem_amount'] * $this->get_currency_rate($user_currency_id, $currency_id);
            $pay['status'] = $this->config->get('constants.PAY_STATUS.CONFIRMED');
            $pay['updated_by'] = $account_id;
            $pay['created_on'] = getGTZ();
            $pay['order_id'] = $order_id;			
            $pay_id = DB::table($this->config->get('tables.PAY'))->insertGetID($pay);
            $payment_id = rand(100, 999).$pay_id;
            DB::table($this->config->get('tables.PAY'))
                    ->where('pay_id', $pay_id)
                    ->update(['payment_id'=>$payment_id]);
            $arr['order_id'] = $order_id;
            $transaction_id = $this->generateTransactionID();
            $created_on = getGTZ();
            $redeem_id = DB::table($this->config->get('tables.REDEEMS'))
                    ->insertGetID([
                'order_id'=>$order_id,
                'updated_by'=>$account_id,
                'from_wallet_id'=>$wallet_id,                
                'from_currency_id'=>$currency_id,
                'currency_id'=>$user_currency_id,
                'transaction_id'=>$transaction_id,
                'from_amount'=>$store_redeem_amount,                
                'redeem_amount'=>$redeem_amount,
                'created_on'=>$created_on,
                'status'=>$this->config->get('constants.REDEEM_STATUS.PENDING')
            ]);
            if ($redeem_id)
            {	
				$user_bill_amount = $store->amount * $this->get_currency_rate($currency_id, $user_currency_id);
                if ($trans_id = $this->updateAccountTransaction([
                    'from_account_id'=>$account_id,
                    'from_transaction_id'=>$transaction_id,
                    'from_wallet_id'=>$wallet_id,
                    'currency_id'=>$user_currency_id,
                    //'amt'=>$user_bill_amount,
                    'amt'=>$redeem_amount,
                    'paidamt'=>$redeem_amount,
                    'relation_id'=>$redeem_id,
                    'debit_remark_data'=>['bill_amount'=>CommonLib::currency_format($user_bill_amount, $user_currency_id, true, false), 'store_name'=>$store->store_name, 'order_code'=>$order_code],
                    'transaction_for'=>'REDEEM']))
                {
                    DB::table($this->config->get('tables.REDEEMS'))
                            ->where('redeem_id', $redeem_id)
                            ->update(['status'=>$this->config->get('constants.REDEEM_STATUS.CONFIRMED')]);
                }
                else
                {
                    DB::table($this->config->get('tables.REDEEMS'))
                            ->where('redeem_id', $redeem_id)
                            ->update(['status'=>$this->config->get('constants.REDEEM_STATUS.FAILED')]);
                }				
                
                // Cashback credit
                if ($cashback_amount = $this->commonObj->updateOrderCommission($order_id))
                {
                    $this->commonObj->releaseOrderCommission($order_id);
                    $balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ab')
                            ->join($this->config->get('tables.WALLET_LANG').' as w', function($w)
                            {
                                $w->on('w.wallet_id', '=', 'ab.wallet_id');
                                //->where('w.lang_id', '=', $this->config->get('app.locale_id'));
                            })
                            ->where('ab.account_id', $account_id)
                            ->where('ab.wallet_id', $wallet_id)
                            ->where('ab.currency_id', $user_currency_id)
                            ->select('w.wallet', 'ab.current_balance', 'ab.currency_id')
                            ->first();
                    if (!empty($balance))
                    {
                        $redeem_amount = $vimoney_wallet[0]['store']['redeem_amt'];
                        $balance->redeem_amount = $redeem_amount;
                        $balance->redeem_time = $created_on;
                        $balance->trans_id = $trans_id;
                        $balance->payment_id = $payment_id;
                        return (object) $balance;
                    }
                }
            }
        }
        return false;
    }
	
	public function getPaymentTypes (array $arr = array())
    {	//return $arr;
        extract($arr);
        $result = DB::table($this->config->get('tables.PAY_PAYMENT_SETTINGS').' as ps')
                ->join($this->config->get('tables.APP_PAYMENT_MODES').' as apm', function($apm)
                {
                    $apm->on('apm.pay_mode_id', '=', 'ps.pay_mode')
                    ->where('apm.is_online', '=', $this->config->get('constants.OFF'));
                })
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'ps.payment_type_id')            
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
                ->where('ps.currency_id', $currency_id)
                ->where('ps.country_id', $country_id)
                ->selectRaw('apm.pay_mode as name, apm.code as id, pt.save_card as has_card_ui, apm.logo as icon, apm.is_online as is_pg') 
                ->get();
        $balance = 0;
        array_walk($result, function(&$p) use($arr, $currency_id, $balance, $account_id)
        {
            if ($p->id == 'xpc')
            {
                $balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                        ->where('account_id', $account_id)
                        ->where('wallet_id', $this->config->get('constants.WALLET.xpc'))
                        ->value('current_balance');
                $p->balance = CommonLib ::currency_format($balance, $currency_id, true, false);
            }
            $p->icon = asset($this->config->get('constants.PAYMENT_MODE_IMG_PATH.WEB').$p->icon);
            $p->saved_cards = ($p->has_card_ui) ? $this->getStoredCards($arr) : '';
        });
        return $result;
    }
	
	public function getTax_bk (array $arr = array())
    {
        extract($arr);
        $tax = DB::table($this->config->get('tables.TAX_CLASSES').' as tc')
                ->join($this->config->get('tables.TAX_CLASS_TAXES').' as tct', function($tct)
                {
                    $tct->on('tct.tax_class_id', '=', 'tc.tax_class_id')
                    ->where('tct.is_deleted', '=', $this->config->get('constants.OFF'));
                })
                ->join($this->config->get('tables.TAXES').' as t', function($t)
                {
                    $t->on('t.tax_id', '=', 'tct.tax_id')
                    ->where('t.is_deleted', '=', $this->config->get('constants.OFF'))
                    ->where('t.status', '=', $this->config->get('constants.ON'));
                })
                ->where('tc.is_deleted', $this->config->get('constants.OFF'))
                ->selectRaw('t.tax_id,CONCAT_WS(\'-\',tc.tax_class,t.tax) as tax,t.tax_value');
        if (isset($geo_zone_id) && !empty($geo_zone_id))
        {
            $tax->where('t.geo_zone_id', $geo_zone_id);
        }
        elseif (isset($country_id) && !empty($country_id))
        {
            $tax->join($this->config->get('tables.GEO_ZONE_LOCATIONS').' as z', function($t) use($country_id)
            {
                $t->on('z.geo_zone_id', '=', 't.geo_zone_id')
                        ->where('z.country_id', '=', $country_id);
            });
            if (isset($state_id) && !empty($state_id))
            {
                $tax->whereNUll('z.state_id')->orWhere('z.state_id', '=', $state_id);
            }
        }
        return $tax->get();
    }


}