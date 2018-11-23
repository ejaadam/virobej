<?php
namespace App\Models\Api\User;
use App\Models\BaseModel;
use DB;
use CommonLib;
use App\Helpers\CommonNotifSettings;
use App\Models\Api\CommonModel;

class WithdrawalModel extends BaseModel {
	
    public function __construct() 
	{
        parent::__construct();		
		$this->commonObj = new CommonModel();		
    }
	
	public function getBankList (array $arr = array())
    {
        extract($arr);
        $banks = DB::table($this->config->get('tables.PAYOUT_BANK_MST').' as pb')
                ->join($this->config->get('tables.PAYOUT_BANK_SETTINGS').' as pbs', function($pbs) use($country_id, $currency_id)
                {
                    $pbs->on('pb.bank_id', '=', 'pbs.bank_id')
                    ->where('pbs.country_id', '=', $country_id)
                    ->where('pbs.currency_id', '=', $currency_id)
                    ->where('pbs.is_deleted', '=', $this->config->get('constants.OFF'))
                    ->where('pbs.status', '=', $this->config->get('constants.ON'));
                })
                ->where('pb.is_deleted', $this->config->get('constants.OFF'))
                ->where('pb.status', $this->config->get('constants.ON'))
                ->where('pb.is_verified', $this->config->get('constants.ON'))
                ->selectRaw('bank_name as id,bank_name as value,bank_logo as logo')
                ->get();
        array_walk($banks, function(&$bank)
        {
            $bank->logo = asset($this->config->get('path.MANAGED_BANK.LOGO_PATH.WEB').$bank->logo);
        });
        return $banks;
    }
    
    public function getAccountTypeList (array $arr = array())
    {
        extract($arr);
        $banks = DB::table($this->config->get('tables.BANKING_ACCOUNT_TYPES'))                
                ->selectRaw('account_type as value, id')
                ->get();       
        return $banks;
    }
	
	public function Save_Bank_Details (array $arr = array())
    {	
        extract($arr);
		//$account['payment_settings'] = json_encode(array_filter($account_details));		
		$account['payment_settings'] = addslashes(json_encode(array_filter($account_details)));
		$account['currency_id'] = $currency_id;
		$account['account_id'] = $account_id;            
		$account['payment_type_id'] = 12;   /* Bank Transfer */				
		$account['created_date'] = getGTZ();
		$account['updated_by'] = $account_id;		
        return DB::table($this->config->get('tables.ACCOUNT_PAYMENT_SETTINGS'))->insertGetId($account);
    }
	
	public function Delete_Bank_Details (array $arr = array())
    {	
        extract($arr);
			
        return DB::table($this->config->get('tables.ACCOUNT_PAYMENT_SETTINGS'))
				->where('account_id', $account_id)
				->where('currency_id', $currency_id)
				->where('id', $id)
				->update(['is_deleted'=>1]);
    }
	
	public function getAccountDetails (array $arr = array())
    {	
        extract($arr);
			
        $bank_account =  DB::table($this->config->get('tables.ACCOUNT_PAYMENT_SETTINGS'))
				->where('account_id', $account_id)
				->where('currency_id', $currency_id)
				->where('id', $id)
				->value('payment_settings');
				
		return json_decode(stripslashes($bank_account));		
    }
	
	public function List_Bank_Details (array $arr = array())
    {	
        extract($arr);			
        $result = DB::table($this->config->get('tables.ACCOUNT_PAYMENT_SETTINGS'))
				->where('account_id', $account_id)
				->where('currency_id', $currency_id)
				->where('is_deleted', $this->config->get('constants.OFF'))
				->where('status', $this->config->get('constants.ON'))
				->selectRAW('payment_settings, id')
				->get();
		$bank = [];		
		$res = [];		
		if(!empty($result)) {		
			array_walk($result, function(&$account, $key) use (&$bank) {
				//$account->{$key} = json_decode(stripslashes($account->payment_settings));
				$bank[$key] = json_decode(stripslashes($account->payment_settings));		
				$bank[$key]->id = $account->id;	
			});
			//return $bank;
			foreach ($bank as $key => $account) {
				
				 $format = ['b_accno'=>'Account Number',
						    'acc_holder_name'=>'Account Name',
						   'ifsc'=>'IFSC Code',
						   'b_acc_type'=>'Account Type',
						   'b_acc_branch'=>'Branch',
						   'b_bank_name'=>'Bank Name'];
				if (is_array($format))
				{
					array_walk($format, function(&$v, $k) use($account, &$res, $key)
					{
						if (isset($account->{$k}))
						{
							if ($k == 'b_acc_type') {
								if($account->{$k} === '1') {
									$account->{$k} = 'SAVINGS';
								} else {
									$account->{$k} = 'CURRENT';
								}
							}
							$res[$key][$k] = ['label'=>$v, 'value'=>(isset($account->{$k})) ? $account->{$k} : ''];
						}
					});
				}
				$res[$key]['logo'] = asset($this->config->get('path.MANAGED_BANK.LOGO_PATH.WEB').'sbi.png');
				$res[$key]['id'] = (String) $account->id;	
			} 
		}			
		return $res;		
		
    }
	
	public function getWithdrawalPaymentTypes (array $arr = array())
    {
        extract($arr);
        $res = DB::table($this->config->get('tables.WITHDRAWAL_PAYMENT_TYPES').' as wp')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wp.payment_type_id')
                ->join($this->config->get('tables.WITHDRAWAL_CHARGES').' as wc', function($wc) use($currency_id)
                {
                    $wc->on('wc.payment_type_id', '=', 'wp.payment_type_id')
                    ->where('wc.currency_id', '=', $currency_id);
                })                
                ->where('pt.status', $this->config->get('constants.ON'))
                ->where(function($c)use($country_id)
                {
                    $c->where('wp.is_user_country_based', $this->config->get('constants.OFF'))
                    ->orWhere(function($c1)use($country_id)
                    {
                        $c1->where('wp.is_user_country_based', $this->config->get('constants.ON'))
                        ->where('wc.country_id', $country_id);
                    });
                })
                ->groupby('wp.payment_type_id')
                ->selectRaw('pt.payment_type as title, pt.payment_key as id, wp.description as descr, pt.image_name as icon, wp.charges');
        $payment_types = $res->get();
        array_walk($payment_types, function($payment_type)
        {
            $payment_type->icon = asset($this->config->get('constants.PAYMENT_MODE_IMG_PATH.LOCAL').$payment_type->icon);
            $payment_type->charges = !empty($payment_type->charges) ? $payment_type->charges : '';
        }); 
        return $payment_types;
    }
	
	public function getPaymentDetails (array $arr = array())
    {
        extract($arr);
        $op = [];		
        $res = DB::table($this->config->get('tables.WITHDRAWAL_PAYMENT_TYPES').' as wp')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wp.payment_type_id')               
                ->join($this->config->get('tables.WITHDRAWAL_CHARGES').' as wc', function($wc) use($currency_id)
                {
                    $wc->on('wc.payment_type_id', '=', 'pt.payment_type_id')
                    ->where('wc.currency_id', '=', $currency_id);
                })
                ->where(function($c)use($country_id)
                {
                    $c->where('wp.is_user_country_based', $this->config->get('constants.OFF'))
                    ->orWhere(function($c1)use($country_id)
                    {
                        $c1->where('wp.is_user_country_based', $this->config->get('constants.ON'))
                        ->where('wc.country_id', $country_id);
                    });
                })
                ->groupby('wp.payment_type_id')
                ->where('pt.payment_key', $payment_type)
                ->where('pt.status', $this->config->get('constants.ON'))
                ->selectRaw('pt.payment_type_id, pt.payment_key as payment_code, pt.payment_type as title, pt.payment_key as id, wp.description as descr, pt.image_name as icon');
        $payment_info = $res->first();
        if (!empty($payment_info))
        {
            $payment_info->icon = asset($this->config->get('constants.PAYMENT_MODE_IMG_PATH.WEB').$payment_info->icon);
            unset($payment_info->payment_type_id);
        }
        return $payment_info;
    }
	
	public function getWalletBalance ($account_id, $currency_id, $wallet_id = null, $formatted = false, $withdrawable = false, $country_id = null)
    {
        $balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ub')
                ->join($this->config->get('tables.CURRENCIES').' as cr', 'cr.currency_id', '=', 'ub.currency_id')
                ->join($this->config->get('tables.WALLET').' as w', function($subquery)
                {
                    $subquery->on('w.wallet_id', '=', 'ub.wallet_id')
                    ->where('w.withdrawal_status', '=', $this->config->get('constants.ON'))
                    ->where('w.status', '=', $this->config->get('constants.ON'));
                })
                ->leftJoin($this->config->get('tables.WALLET_LANG').' as wl', function($wl)
                {
                    $wl->on('wl.wallet_id', '=', 'ub.wallet_id');
                    //$wl->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->where('ub.account_id', $account_id)
                ->where('ub.currency_id', $currency_id)
                ->where('ub.wallet_id', $wallet_id)
                ->selectRaw('ub.current_balance as balance, wallet, currency, currency_symbol'.($formatted ? ',decimal_places' : ''));
        if ($withdrawable)
        {
            $balance->where('ub.current_balance', '>=', function($c) use($currency_id, $country_id)
            {
                return $c->from($this->config->get('tables.WITHDRAWAL_CHARGES'))
                                ->where('currency_id', $currency_id)
                                ->where('country_id', $country_id)
                                ->select('min_amount');
            });
        }
        $balance = $balance->first();
        if (!empty($balance) && $formatted)
        {
            $balance->fbalance = CommonLib::currency_format($balance->balance, ['currency_code'=>$balance->currency, 'currency_symbol'=>$balance->currency_symbol, 'decimal_places'=>$balance->decimal_places]);
            unset($balance->decimal_places);
        }
        return $balance;
    }
	
	public function get_balance_bycurrency (array $arr)
    {	
        $op = $breakdowns = array();
        $current_balance = 0;
        if (!empty($arr))
        {	
            extract($arr);		
            $bws = $this->get_withdrwal_settings($arr);			
            if (!empty($bws))
            {
                $acct_balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ub')
                        ->join($this->config->get('tables.CURRENCIES').' as cr', 'cr.currency_id', '=', 'ub.currency_id')
                        ->join($this->config->get('tables.WALLET').' as w', function($subquery)
                        {
                            $subquery->on('w.wallet_id', '=', 'ub.wallet_id')
                            ->where('w.withdrawal_status', '=', $this->config->get('constants.ON'))
                            ->where('w.status', '=', $this->config->get('constants.ON'));
                        })
                        ->Join($this->config->get('tables.WALLET_LANG').' as wl', function($wl)
                        {
                            $wl->on('wl.wallet_id', '=', 'ub.wallet_id');                            
                        })
                        ->where('ub.account_id', $account_id)
                        ->where('ub.currency_id', $currency_id)
                        ->whereRaw('ub.current_balance IS NOT NULL')
                        ->whereRaw('ub.current_balance != 0')
                        ->select('ub.current_balance', 'ub.currency_id', 'cr.decimal_places', 'cr.currency_symbol', 'cr.currency', 'cr.currency_id', 'w.wallet_code', 'wl.wallet as label')
                        ->get();
                if ($acct_balance)
                {
                    array_walk($acct_balance, function(&$balance) use(&$current_balance, $currency_id)
                    {
                        $balance->attr = (object) [];
                        $balance->attr->min = 0;
                        $rate = $this->get_currency_rate($balance->currency_id, $currency_id);
                        $balance->equivalent = $balance->attr->max = (float) $balance->current_balance * (float) $rate;
                        $current_balance = (float) $current_balance + (float) $balance->equivalent;
                        $balance->attr->name = 'breakdowns['.$balance->wallet_code.']['.$balance->currency.']';
                        $balance->current_balance = CommonLib::currency_format($balance->current_balance, $balance->currency_id, true, false);
                        unset($balance->currency_id);
                        unset($balance->decimal_places);
                        unset($balance->currency_symbol);
                    });
                    $op['balance'] = $current_balance;
                }
                $op['min'] = (float) $bws->min_amount;
                $op['max'] = (float) ($current_balance < $bws->max_amount) ? $current_balance : $bws->max_amount;
                $op['amount'] = $amount = !empty($amount) ? $amount : $op['max'];
                $breakdown_tot = 0;
                if ($acct_balance)
                {
                    array_walk($acct_balance, function(&$balance) use($breakdowns, &$breakdown_tot, $amount, $currency_id)
                    {
                        if (isset($breakdowns[$balance->wallet_code][$balance->currency]) && !empty($breakdowns[$balance->wallet_code][$balance->currency]))
                        {
                            $balance->attr->value = $breakdowns[$balance->wallet_code][$balance->currency];
                            $breakdown_tot+=$balance->attr->value;
                        }
                        else
                        {
                            $breakdown_bal = $amount - $breakdown_tot;
                            $balance->attr->value = ($breakdown_bal > 0) ? ($breakdown_bal > $balance->equivalent ? $balance->equivalent : $breakdown_bal) : 0;
                            $breakdown_tot+=$balance->attr->value;
                        }
                        $balance->equivalent = CommonLib::currency_format($balance->equivalent, $currency_id);
                    });
                    $op['breakdowns'] = $acct_balance;
                }
                if (is_array($bws->charge))
                {
                    if ($bws->charge[1]->min <= $amount)
                    {
                        $op['charge'] = ($bws->charge[1]->charge_type == $this->config->get('constants.PERCENTAGE')) ? (float) ($amount * $bws->charge[1]->charge) / 100 : $bws->charge[1]->charge;
                        $op['charge_type'] = $bws->charge[1]->charge_type;
                        $op['charges'] = $bws->charge[1]->charge;
                    }
                    else
                    {
                        $op['charge'] = ($bws->charge[0]->charge_type == $this->config->get('constants.PERCENTAGE')) ? (float) ($amount * $bws->charge[0]->charge) / 100 : $bws->charge[0]->charge;
                        $op['charge_type'] = $bws->charge[0]->charge_type;
                        $op['charges'] = $bws->charge[0]->charge;
                    }
                }
                else
                {
                    $op['charge'] = ($bws->charge->charge_type == $this->config->get('constants.PERCENTAGE')) ? (float) ($amount * $bws->charge->charge) / 100 : $bws->charge->charge;
                    $op['charge_type'] = $bws->charge->charge_type;
                    $op['charges'] = $bws->charge->charge;
                }
                $op['currency_code'] = $bws->currency;
                $op['currency_symbol'] = $bws->currency_symbol;
                return $op;
            }
        }
        return false;
    }
	
	public function get_withdrwal_settings (array $arr = array())
    {		
        extract($arr);
        $query = DB::table($this->config->get('tables.WITHDRAWAL_CHARGES').' as wd')	
						->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'wd.currency_id')
						->select('wd.country_id', 'wd.min_amount', 'wd.max_amount', 'wd.charges', 'wd.is_range', 'wd.currency_id', 'c.currency', 'c.currency_symbol');
        if (isset($payment_type_id) && !empty($payment_type_id))
        {
            $query->where('wd.payment_type_id', $payment_type_id);
        }
        if (isset($country_id) && isset($country_id))
        {
            $query->where('wd.country_id', $country_id);
        }
        if (isset($currency_id) && isset($currency_id))
        {
            if (is_array($currency_id))
            {
                $query->whereIn('wd.currency_id', $currency_id);
            }
            else
            {
                $query->where('wd.currency_id', $currency_id);
            }
        } 
        $settings = $query->first();
        if (!empty($settings))
        {
            $charges = json_decode(stripslashes($settings->charges), true);
            if ($settings->is_range)
            {
                $settings->charge = [
                    (object) [
                        'min'=>0,
                        'charge'=>(float) $charges['default']['charge'],
                        'charge_type'=>$charges['default']['charge_type']
                    ],
                    (object) [
                        'min'=>(float) $charges['range']['min_amnt'],
                        'charge'=>(float) $charges['range']['charge'],
                        'charge_type'=>$charges['range']['charge_type']
                    ]
                ];
            }
            else
            {
                $settings->charge = (object) [
                            'charge'=>(float) $charges['default']['charge'],
                            'charge_type'=>$charges['default']['charge_type']
                ];
            }
            unset($settings->charges);
            unset($settings->is_range);
        }

        return $settings;
    }
	
	public function saveWithdrawal (array $arr = array())
    {	
        extract($arr);
        $wallet_id = $this->config->get('constants.CASHBACK_CREDIT_WALLET');
        $tds_charge = 0;
        $avaliable_balance = 0;
        $from_currency = [];
        $conversion_details = [];
        DB::beginTransaction();
        $transaction_id = $this->generateTransactionID();
        $to_currency_code = $this->commonObj->get_currency_code($currency_id);
        $balance_details = $this->commonObj->get_user_balance($account_id, $wallet_id, $currency_id);
		
        if ($balance_details && !empty($balance_details) && $balance_details->current_balance >= $amount)
        {
            $withdrawal_rel_trans_id = '';
            if (isset($relation_transaction_id))
            {
                $withdrawal_rel_trans_id = implode(',', $relation_transaction_id);
            }			
            $withdrawal_tds = json_decode(unserialize(stripslashes($this->getSetting('withdrawal_tds'))), true);
            if (isset($withdrawal_tds[$currency_id]))
            {
                $tds_charge = $withdrawal_tds[$currency_id];
            }
            $total_debit = $balance_details->tot_debit + $amount;
            $current_balance = $balance_details->current_balance - $amount;
            $tds = $amount * $tds_charge / 100;
            $paid_amt = $amount - ($charge + $tds);
            $created_on = $expected_date = getGTZ();
            $d = getGTZ($expected_date, 'd');
            if ($d <= 15)
            {
                $dd = cal_days_in_month(CAL_GREGORIAN, getGTZ($expected_date, 'm'), getGTZ($expected_date, 'Y'));
                $ds = $dd - ($d % $dd);
                $expected_on = getGTZ(date('Y-m-d', strtotime($ds.' days', strtotime($expected_date))), 'Y-m-d');
            }
            else
            {
                $ds = $d - 15;
                $expected_on = getGTZ(date('Y-m-d', strtotime('-'.$ds.' days', strtotime('1 month ', strtotime($expected_date)))), 'Y-m-d');
            }
			
            $insert_withdrawal['account_info'] = json_encode(array_filter($account_details));
            $insert_withdrawal['amount'] = $amount;
            $insert_withdrawal['wallet_id'] = $wallet_id;
            $insert_withdrawal['currency_id'] = $currency_id;
            $insert_withdrawal['account_id'] = $account_id;            
            $insert_withdrawal['payment_type_id'] = $payment_type_id;
            $insert_withdrawal['paidamt'] = $paid_amt;
            $insert_withdrawal['handleamt'] = $charge;
            $insert_withdrawal['handle_perc'] = $charges;
            $insert_withdrawal['transaction_id'] = $transaction_id;
            $insert_withdrawal['conversion_details'] = !empty($conversion_details) ? json_encode($conversion_details) : null;
            $insert_withdrawal['relation_transaction_id'] = $withdrawal_rel_trans_id;
            $insert_withdrawal['status_id'] = $this->config->get('constants.WITHDRAWAL_STATUS.PENDING');
            $insert_withdrawal['created_on'] = $created_on;
            $insert_withdrawal['expected_on'] = $expected_on;
            $insert_withdrawal['updated_by'] = $account_id;
			
            $wd_id = DB::table($this->config->get('tables.WITHDRAWAL_MST'))
                    ->insertGetId($insert_withdrawal);
            //withdrawal transaction

            $withdrawamt = $amount - $charge;
            $trans = [
                'from_account_id'=>$account_id,
                'from_wallet_id'=>$wallet_id,
                'currency_id'=>$currency_id,
                'amt'=>$amount,
                'paidamt'=>$withdrawamt,
                'from_transaction_id'=>$transaction_id,
                'relation_id'=>$wd_id,
                'payment_type_id'=>$payment_type_id,
                'transaction_for'=>'WITHDRAW',
                'tds'=>$tds,
                'debit_remark_data'=>['amount'=>CommonLib::currency_format($amount, $currency_id)],
                'credit_remark_data'=>['amount'=>CommonLib::currency_format($amount, $currency_id)]
            ];
            if ($charge > 0)
            {
                $trans['from_handle'] = [['amt'=>$charge, 
										 'transaction_for'=>'WITHDRAWAL_CHARGES', 
										 'debit_remark_data'=>['amount'=>CommonLib::currency_format($charge, $currency_id)],
										 'credit_remark_data'=>['amount'=>CommonLib::currency_format($charge, $currency_id)]]];
            }
			
            if ($wd_id && $trans_id = $this->updateAccountTransaction($trans))
            {
                //update BTC balance debit;
                /* if ($payment_type_id == $this->config->get('constants.BITCOIN_WITHDRAWAL'))
                {
                    $this->update_admin_btc_balance($amount, $currency_id, $this->config->get('constants.TRANSACTION_TYPE.DEBIT'));
                } */
                DB::commit();				
                if ($payment_type_id == $this->config->get('constants.PAYMENT_TYPES.BANK_TRANSFER'))
                {
					
                    //$paytype = $account_details['b_accno'];
                    $paytype = $account_details['b_bank_name'];
                }
                else
                {					
                    $paytype = trans('general.pay_types.'.$payment_type_id);
                }					
                CommonNotifSettings::notify('USER.WITHDRAW_MONEY', $account_id, $this->config->get('constants.ACCOUNT_TYPE.USER'), [
                    'withdraw_id'=>$trans_id,
                    'amount'=>CommonLib::currency_format($amount, $currency_id, true, true),
                    'paytype'=>$paytype,
                    'date'=>showUTZ($created_on, 'd-M-Y H:i:s'),
                ], true, false, false, true, false); 				
                return $transaction_id;
            }			
        }		
        DB::rollback();
        return false;
    }
	
	
	
	public function paymentTypeDetails (array $arr = array())
    {
        extract($arr);
        $paymentType = DB::table($this->config->get('tables.WITHDRAWAL_PAYMENT_TYPES').' as pay')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'pay.payment_type_id')                
                ->join($this->config->get('tables.WITHDRAWAL_CHARGES').' as wc', function($wc) use($currency_id)
                {
                    $wc->on('wc.payment_type_id', '=', 'pay.payment_type_id')
                    ->where('wc.currency_id', '=', $currency_id);
                })
                ->where(function($c)use($country_id)
                {
                    $c->where('pay.is_user_country_based', $this->config->get('constants.OFF'))
                    ->orWhere(function($c1)use($country_id)
                    {
                        $c1->where('pay.is_user_country_based', $this->config->get('constants.ON'))
                        ->where('wc.country_id', $country_id);
                    });
                }) 
                ->where('pt.status', $this->config->get('constants.ON'))
                //->where('pt.payment_key', $payment_type)
                ->where('pt.payment_type_id', $payment_type_id)
                ->selectRaw('pt.payment_type_id, pt.image_name as icon, wc.currency_id, pt.payment_type as withdrawal_payment_type, pt.payment_key as payment_code, pay.description as withdrawal_payment_desc, pay.charges as withdrawal_charges, pay.is_user_country_based')
                ->first();
        if ($paymentType)
        {
            $paymentType->countries = DB::table($this->config->get('tables.WITHDRAWAL_CHARGES').' as wc')
                    ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'wc.country_id')
                    ->where('wc.payment_type_id', $paymentType->payment_type_id)
                    ->where('wc.currency_id', $paymentType->currency_id)
                    ->selectRaw('lc.country as id, lc.country as value')
                    ->orderby('lc.country', 'ASC') 
                    ->get();
            $paymentType->currencies = DB::table($this->config->get('tables.WITHDRAWAL_CHARGES').' as wc')
                    ->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'wc.currency_id')
                    ->where('wc.payment_type_id', $paymentType->payment_type_id);
            if ($paymentType->is_user_country_based)
            {
                $paymentType->currencies->where('wc.country_id', $country_id);
            }
            $paymentType->currencies = $paymentType->currencies->selectRaw('c.currency as id, c.currency as value')					
						->where('wc.currency_id', $currency_id)	
                    ->orderby('c.currency', 'ASC')
                    ->get();
            $paymentType->icon = asset($paymentType->icon);
            unset($paymentType->payment_type_id);
            unset($paymentType->currency_id);
            unset($paymentType->is_user_country_based);
        }
        return $paymentType;
    }
	
	public function getUserPaymentInfo (array $arr = array())
    {
        extract($arr);
        $account_details = DB::table($this->config->get('tables.ACCOUNT_PAYMENT_SETTINGS').' as aps')
                ->where('aps.account_id', $account_id)
                ->where('aps.is_deleted', $this->config->get('constants.OFF'));

        if (isset($payment_type_id) && !empty($payment_type_id))
        {
            $account_details->where('aps.payment_type_id', $payment_type_id);
        }
        elseif (isset($payment_type) && !empty($payment_type))
        {
            $account_details->join($this->config->get('tables.PAYMENT_TYPES').' as p', function($p) use($payment_type)
            {
                $p->on('p.payment_type_id', '=', 'aps.payment_type_id')
                        ->where('p.payment_code', '=', $payment_type);
            });
        }
        if (isset($payout_id) && !empty($payout_id))
        {
            $account_details->where('aps.id', $payout_id);
        }
        if (isset($currency_id) && !empty($currency_id))
        {
            $account_details->where('aps.currency_id', $currency_id);
        }
        if (isset($currency_code) && !empty($currency_code))
        {
            $account_details->join($this->config->get('tables.CURRENCIES').' as c', function($c) use($currency_code)
            {
                $c->on('c.currency_id', '=', 'aps.currency_id')
                        ->where('c.currency', '=', $currency_code);
            });
        }

        if (isset($payout_id) && !empty($payout_id))
        {
            $account_details = $account_details->value('payment_settings');
            if (!empty($account_details))
            {
                $account_details = json_decode(stripslashes($account_details), true);
                if ($account_details['b_acc_type'] == 1) {
    				$account_details['b_acc_type'] = 'SAVINGS';
    			} else {
    				$account_details['b_acc_type'] = 'CURRENT';
    			}
            }
            return $account_details;
        }
        else
        {
            $account_details = $account_details->selectRaw('aps.id, aps.payment_settings as account_details, aps.is_verified')->get();
            array_walk($account_details, function(&$account_detail)
            {
                $account_detail->icon = '';
                $account_detail->is_verified_class = $this->config->get('dispclass.payment_verification.'.$account_detail->is_verified);
                $account_detail->is_verified = trans('withdrawal.is_verified.'.$account_detail->is_verified);
                $account_detail->account_details = json_decode(stripslashes($account_detail->account_details), true);
                array_walk($account_detail->account_details, function($value, $key) use(&$account_detail)
                {
                    $account_detail->details[] = ['key'=>trans('withdrawal.account_details.'.$key), 'value'=>$value];
                    if (in_array($key, ['us_bank_name', 'b_bank_name', 'sgd_bank_name']))
                    {
                        $logo = 'bank-transfer.png';
                        $account_detail->icon = asset($this->config->get('constants.MANAGED_BANK.LOGO_PATH.XS').$logo);
                    }
                });
                unset($account_detail->account_details);
            });
            return $account_details;
        }
    }
	
	public function getWithdrawalDetails (array $arr = array())
    {
        extract($arr);
        $query = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as wid')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wid.payment_type_id')            
                ->join($this->config->get('tables.WALLET_LANG').' as wt', function($wt)
                {
                    $wt->on('wt.wallet_id', '=', 'wid.wallet_id');                    
                })
                ->join($this->config->get('tables.CURRENCIES').' as ci', 'ci.currency_id', '=', 'wid.currency_id')
                ->where('wid.account_id', $account_id)
                ->where('wid.is_deleted', $this->config->get('constants.OFF')) 
                ->where('wid.transaction_id', $id);
         $query->selectRaw('pt.payment_type, wid.transaction_id, wid.status_id as status, wid.amount, wid.paidamt, wid.handleamt, wid.created_on, wid.expected_on, wid.cancelled_on, wid.confirmed_on, ci.currency, ci.currency_symbol, wid.account_info, wid.conversion_details, wid.reason, wid.payment_details, wt.wallet as from_wallet, wid.wd_id');
                 $withdrawal = $query->first();
        if (!empty($withdrawal))
        {
            $withdrawal->amount = $withdrawal->currency_symbol.' '.number_format($withdrawal->amount, 2, '.', ',');
            $withdrawal->paidamt = $withdrawal->currency_symbol.' '.number_format($withdrawal->paidamt, 2, '.', ',');
            $withdrawal->handleamt = $withdrawal->currency_symbol.' '.number_format($withdrawal->handleamt, 2, '.', ',');
            $withdrawal->expected_on = ($withdrawal->expected_on != null) ? showUTZ($withdrawal->expected_on, 'd-M-Y') : '';
            //$withdrawal->created_on = ($withdrawal->created_on != null) ? showUTZ($withdrawal->created_on) : '';
            $withdrawal->created_on = ($withdrawal->created_on != null) ? date('h:i A, d M y',strtotime('+330 minutes', strtotime($withdrawal->created_on))) : '';
            $withdrawal->confirmed_on = !empty($withdrawal->confirmed_on) ? showUTZ($withdrawal->confirmed_on) : '';
            $withdrawal->cancelled_on = !empty($withdrawal->cancelled_on) ? showUTZ($withdrawal->cancelled_on) : '';
            $withdrawal->conversion_details = ($withdrawal->conversion_details != null) ? json_decode($withdrawal->conversion_details) : '';
            $withdrawal->reason = ($withdrawal->reason != null) ? json_decode($withdrawal->reason) : '';
            $withdrawal->account_info = json_decode($withdrawal->account_info);
            $withdrawal->payment_details = ($withdrawal->payment_details) ? json_decode($withdrawal->payment_details) : '';

            if (!empty($withdrawal->account_info))
            {
                array_walk($withdrawal->account_info, function(&$a, $k)
                {					
					$a = ['label'=>trans('user/withdrawal.account_details.'.$k), 'value'=>$a];					
                });
            }
            if (!empty($withdrawal->payment_details))
            {
                array_walk($withdrawal->payment_details, function(&$a, $k)
                {					
                    $a = ['label'=>trans('user/withdrawal.payment_details.'.$k), 'value'=>$a];
                });
            }
            $withdrawal->account_info = array_values((array) $withdrawal->account_info);
            if (!empty($withdrawal->conversion_details))
            {
                array_walk($withdrawal->conversion_details, function(&$convert) use($withdrawal)
                {
                    $convert->wallet = $this->commonObj->get_wallet_name($convert->wallet_id);
                    $currency = $this->commonObj->get_currency($convert->currency_id);
                    $convert->from_amount = $currency->currency_symbol.' '.number_format($convert->from_amount, 2, '.', ',').' '.$currency->currency;
                    $convert->to_amount = $withdrawal->currency_symbol.' '.number_format($convert->to_amount, 2, '.', ',').' '.$withdrawal->currency;
                    unset($convert->wallet_id);
                    unset($convert->currency_id);
                    unset($convert->rate);
                });
            }           
            $withdrawal->status_class = $this->config->get('dispclass.withdrawal_status.'.$withdrawal->status);
            $withdrawal->status = trans('user/general.withdrawal_status.'.$withdrawal->status);
        }
        return $withdrawal;
    }


}