<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use App\Models\Affiliate\Wallet;
use App\Models\Affiliate\Payments;

use DB;

class Withdrawal extends BaseModel
{
	 public function __construct ()
    {
         parent::__construct(); 
		 $this->paymentObj = new Payments;
		 $this->walletObj = new Wallet;
    }
	public function withdrawal_details($arr=array())
	{
		extract($arr);		
		  /*$pkg = DB::table($this->config->get('tables.BANK_WITHDRAWELS').' as bw')					
						->join($this->config->get('tables.ACCOUNT_MST') . ' as um','um.account_id','=','bw.account_id')
						->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud','ud.account_id','=','um.account_id')
						->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc','ud.country','=','lc.country_id')
						->join($this->config->get('tables.WITHDRAWAL_PAYOUT_TYPES') . ' as wpt','wpt.withdrawal_payout_type_id','=','bw.payout_type')
						->select('bw.amount','bw.expected_date','bw.created_on','bw.timeflag','bw.paidamt','bw.status','um.uname','lc.name','wpt.withdrawal_payout_type','wpt.charges');
			/*Execute query*/	
		$pkg = DB::table($this->config->get('tables.ACCOUNT_WITHDRAWAL').' as bw')					
					->join($this->config->get('tables.ACCOUNT_MST') . ' as um','um.account_id','=','bw.account_id')
					->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud','ud.account_id','=','um.account_id')		
					->join($this->config->get('tables.CURRENCIES') . ' as cur','cur.id','=','bw.currency_id')
					->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc','ud.country','=','lc.country_id')
					->join($this->config->get('tables.WITHDRAWAL_PAYOUT_TYPES') . ' as wpt','wpt.withdrawal_payout_type_id','=','bw.payout_type')
					->select('bw.amount','bw.transaction_id','bw.expected_date','bw.created_on','bw.timeflag','cur.code as currency_code','bw.paidamt','bw.status','um.uname','lc.name','wpt.withdrawal_payout_type','wpt.charges');
		if (isset($from) && !empty($from) && isset($to) && !empty($to))
        {
            $pkg->whereRaw("DATE(bw.created_on) >='".date('Y-m-d', strtotime($from))."'");
            $pkg->whereRaw("DATE(bw.created_on) <='".date('Y-m-d', strtotime($to))."'");
        }
        else if (isset($to) && !empty($to))
        {
            $pkg->whereRaw("DATE(bw.created_on) <='".date('Y-m-d', strtotime($to))."'");
        }
        else if (isset($from) && !empty($from))
        {
            $pkg->whereRaw("DATE(bw.created_on) >='".date('Y-m-d', strtotime($from))."'");
        }
		if (isset($search_term) && !empty($search_term))
        {		
            $pkg->whereRaw("um.uname like '%$search_term%'");
        }
		if (isset($payment_type_id) && !empty($payment_type_id))
        {		
            $pkg->where("wpt.withdrawal_payout_type_id",$payment_type_id);
        }
		if (isset($status))
        {		
            $pkg->where("bw.status",$status);
        }
		if (isset($currency_id) && !empty($currency_id))
        {		
            $pkg->where("bw.currency_id",$currency_id);
        }
		if (isset($orderby) && isset($order))
        {
            $pkg->orderBy($orderby, $order);
        }
		if (isset($length) && !empty($length))
        {
            $pkg->skip($start)->take($length);
        }
		if (isset($count) && !empty($count))
        {
            return $pkg->count();
        }
		else 
		{			
		//print_r($search_term);
		$pkg=$pkg->get();
		
		if(!empty($pkg)){
					array_walk($pkg,function(&$gtdata){
						$gtdata->created_on = date('d-M-Y H:i:s',strtotime($gtdata->created_on));
						$gtdata->timeflag = date('d-M-Y H:i:s',strtotime($gtdata->timeflag));
						if($gtdata->expected_date!=NULL){
						$gtdata->expected_date = date('d-M-Y',strtotime($gtdata->expected_date));
						}
						switch($gtdata->status){
						case 1:
							$gtdata->status_label = '<label class="label label-success">Transferred</label>';
						break;
						case 2:
							$gtdata->status_label = '<label class="label label-info">Processing</label>';
						break;
						case 0:
							$gtdata->status_label = '<label class="label label-warning">Pending</label>';
						break;
						case 3:
							$gtdata->status_label = '<label class="label label-danger">Cancelled</label>';
						break;
						} 
					});
					return $pkg;
				}
					return false;
				}
	}
	public function withdrawal_wallet_balance_list ($arr = array())
    {
        $res = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ab')
                ->join($this->config->get('tables.WALLET').' as wa', 'wa.wallet_id', '=', 'ab.wallet_id')
                ->join($this->config->get('tables.CURRENCIES').' as ci', 'ci.id', '=', 'ab.currency_id')
                ->where('wa.withdrawal_status', $this->config->get('constants.ON'))
                ->where('ab.account_id', $arr['account_id'])
                ->selectRaw('ab.current_balance,ci.currency');
				
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            return $res->count();
        }
        else
        {	
			$result=$res->get();
			//print_r( $result);exit;
			if ($result)return $result;
				else return false;
            
			
        }
    }
	public function withdrawal_payout_list ($arr = array())
    {
        $res = DB::table($this->config->get('tables.PAYMENT_TYPES').' as pt')
                ->where('pt.status', $this->config->get('constants.ON'))
				->where('pt.Withdrawal_payout_type', $this->config->get('constants.ON'))
                ->select('pt.payment_type','pt.charges','pt.description','pt.payment_type_id','pt.image_name','pt.payout_type_key');
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            return $res->count();
        }
        else
        {
            $withdrawal = $res->get();
			//print_r($withdrawal);exit;
            array_walk($withdrawal, function(&$withdrawal)
            {
                $withdrawal->image_name = asset($this->config->get('constants.PAYOUT_IMAGE_PATH').$withdrawal->image_name);
            }); 
			 
            return $withdrawal;
        }
    }
	public function payoutTypeDetails ($payout_type_key)
    {
        $paymentType = DB::table($this->config->get('tables.PAYMENT_TYPES').' as pt')
                ->where('pt.status', $this->config->get('constants.ON'))
                ->where('pt.payout_type_key', $payout_type_key)
                ->select('pt.payout_type_key', 'pt.payment_type', 'pt.description', 'pt.charges', 'pt.is_country_based', 'pt.is_user_country_based', 'pt.countries_allowed', 'pt.currency_allowed', 'pt.countries_not_allowed','pt.payment_type_id')
                ->first();
				//print_r(    $paymentType);exit;
        if ($paymentType)
        {
            if ($paymentType->is_country_based == $this->config->get('constants.ON') || $paymentType->is_user_country_based == $this->config->get('constants.ON'))
            {
                $paymentType->countries_allowed = !empty($paymentType->countries_allowed) ? json_decode($paymentType->countries_allowed, true) : [];

                $paymentType->countries_not_allowed = !empty($paymentType->countries_not_allowed) ? json_decode($paymentType->countries_not_allowed, true) : [];
                if (!empty($paymentType->countries_not_allowed))
                {
                    if (!empty($paymentType->countries_allowed))
                    {
                        array_walk($paymentType->countries_not_allowed, function ($countries_not_allowed, $currency_id) use(&$paymentType)
                        {
                            if (isset($paymentType->countries_allowed[$currency_id]))
                            {
                                $paymentType->countries_allowed[$currency_id] = array_diff($paymentType->countries_allowed[$currency_id], $countries_not_allowed);
                            }
                        });
                    }
                    else
                    {
                        $all_countries = DB::table($this->config->get('tables.LOCATION_COUNTRY'))->where('status', $this->config->get('constants.ON'))->remember(30)->lists('country_id');
                        array_walk($paymentType->countries_not_allowed, function ($countries_not_allowed, $currency_id) use(&$paymentType, $all_countries)
                        {
                            $paymentType->countries_allowed[$currency_id] = array_diff($all_countries, $countries_not_allowed);
                        });
                    }
                }
                array_walk($paymentType->countries_allowed, function($cou) use(&$paymentType)
                {
                    if (!isset($paymentType->all_allowed_countries) || empty($paymentType->all_allowed_countries))
                    {
                        $paymentType->all_allowed_countries = [];
                    }
                    $paymentType->all_allowed_countries = array_merge($paymentType->all_allowed_countries, (array) $cou);
                });
                unset($paymentType->countries_not_allowed);
            }			
					
			$srdata['currencies'] = !empty($paymentType->currency_allowed) ? json_decode($paymentType->currency_allowed,true) : [];			
            $paymentType->currency_allowed = $this->paymentObj->get_currencies($srdata); 
        } 
        return $paymentType;
    }
	public function get_balance_bycurrency ($arr)
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
                        ->join($this->config->get('tables.CURRENCIES').' as cr', 'cr.id', '=', 'ub.currency_id')
                        ->join($this->config->get('tables.WALLET').' as w', function($subquery)
                        {
                            $subquery->on('w.wallet_id', '=', 'ub.wallet_id')
                            ->where('w.withdrawal_status', '=', $this->config->get('constants.ON'))
                            ->where('w.status', '=', $this->config->get('constants.ON'));
                        })
						->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
						{
							$subquery->on('wl.wallet_id', '=', 'w.wallet_id')
							->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
						})
						->where('w.wallet_id', '=' , $this->config->get('constants.BOUNSWALLET'))
                        ->where('ub.account_id', $account_id)
                        ->whereRaw('ub.current_balance IS NOT NULL')
                        ->whereRaw('ub.current_balance != 0')
                        ->select('ub.current_balance', 'ub.currency_id', 'cr.currency_symbol','cr.code as currency_code', 'cr.currency', 'w.wallet_id', 'wl.wallet')
                        ->get();
                if ($acct_balance) 
                {
                    array_walk($acct_balance, function(&$balance) use(&$current_balance, $currency_id)
                    {
                        $balance->min = 0;
                        $rate = $this->get_currency_exchange_rate($balance->currency_id, $currency_id);
						//print_r( $rate);exit;
                        $balance->equivalent = $balance->max = (float) $balance->current_balance * (float) $rate; //print_r($balance->equivalent);exit;
                        $current_balance = (float) $current_balance + (float) $balance->equivalent;
                    });
                    $op['balance'] = $current_balance;
                }
                $op['min'] = (float) $bws->min_amount;
                $op['max'] = (float) ($current_balance < $bws->max_amount) ? $current_balance : $bws->max_amount;
                $op['amount'] = $amount = !empty($amount) ? $amount : $op['max'];
                $breakdown_tot = 0;
                if ($acct_balance)
                {
                    array_walk($acct_balance, function(&$balance) use($breakdowns, &$breakdown_tot, $amount)
                    {
                        if (isset($breakdowns[$balance->wallet_id][$balance->currency_id]) && !empty($breakdowns[$balance->wallet_id][$balance->currency_id]))
                        {
                            $balance->breakdown = $breakdowns[$balance->wallet_id][$balance->currency_id];
                            $breakdown_tot+=$balance->breakdown;
                        }
                        else
                        {
                            $breakdown_bal = $amount - $breakdown_tot;
                            $balance->breakdown = ($breakdown_bal > 0) ? ($breakdown_bal > $balance->equivalent ? $balance->equivalent : $breakdown_bal) : 0;
                            $breakdown_tot+=$balance->breakdown;
                        }
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
                $op['currency_code'] = $bws->currency_code;
                $op['currency_symbol'] = $bws->currency_symbol;
                return $op; //print_r($op);exit;
            }
        }
        return false;
    }
	public function get_withdrwal_settings ($arr = array())
    {
        extract($arr);
        $query = DB::table($this->config->get('tables.WITHDRAWAL_CHARGES_SETTINGS').' as wd')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 'wd.currency_id')
                ->select('wd.country_id', 'wd.min_amount', 'wd.max_amount', 'wd.charges', 'wd.is_range', 'c.id as currency_id', 'c.currency', 'c.currency_symbol','c.code as currency_code');
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
            $charges = json_decode((stripslashes($settings->charges)), true);//print_r(  $charges);exit;
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
	public function get_currency_exchange_rate ($from_currency_id, $to_currency_id)
    {	//print_r($to_currency_id);exit;
        if ($from_currency_id != $to_currency_id)
        {
            return DB::table($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                            ->select('rate')
                            ->where(array(
                                'from_currency_id'=>$from_currency_id,
                                'to_currency_id'=>$to_currency_id
                            ))
                            ->pluck('rate');
        }
        return 1;
    }
	public function get_preBank_info ($arrData)
    {
        extract($arrData);
        $result = DB::table($this->config->get('tables.ACCOUNT_WITHDRAWAL'))
                ->where('account_id', $account_id)
                ->where('payment_type_id', $payment_type_id)
                ->where('currency_id', $currency_id)
				->where('is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->orderBy('wd_id', 'DESC')
                ->select('payout_account_info')
				->first();
				
        if (!empty($result))
        {	
			return json_decode(stripslashes($result->payout_account_info));
        }
        return false;
    }
	public function saveWithdrawal ($arr = array())
    {
        extract($arr);
        $wallet_id = $this->config->get('constants.WALLET.PERSONAL');
        $tds_charge = 0;
        $avaliable_balance = 0;
        $from_currency = [];
        $conversion_details = [];
        DB::beginTransaction();
        $transaction_id = $this->commonObj->generateTransactionID($account_id);
        $to_currency_code = $this->commonObj->get_currency_code($currency_id);
        foreach ($breakdowns as $from_wallet_id=> $currencies)
        {
            foreach ($currencies as $from_currency_id=> $break_amount)
            {
                if ($from_currency_id != $currency_id)
                {
                    if (!empty($break_amount) && $break_amount > 0)
                    {
                        $balance_bycurrency = $this->walletObj->get_user_balance($account_id, $from_wallet_id, $from_currency_id);
                        if ($balance_bycurrency)
                        {
                            $rate = $this->get_currency_exchange_rate($from_currency_id, $currency_id);
                            $withdraw_exchange_amt = (float) $break_amount / (float) $rate;
                            $relation_transaction_id[] = $rel_trans_id = $this->commonObj->generateTransactionID($account_id);
                            $relation_transaction_id[] = $rel_trans_id2 = $this->commonObj->generateTransactionID($account_id);
                            $from_currency_code = $this->commonObj->get_currency_code($from_currency_id);
                            $from_currency_symbol = $this->commonObj->get_currency_symbol($from_currency_id);
                            $this->commonObj->update_account_transaction([
                                'from_account_id'=>$account_id,
                                'from_wallet_id'=>$from_wallet_id,
                                'currency_id'=>$from_currency_id,
                                'amt'=>$withdraw_exchange_amt,
                                'from_transaction_id'=>$rel_trans_id,
                                'to_account_id'=>$account_id,
                                'to_wallet_id'=>$wallet_id,
                                'to_currency_id'=>$currency_id,
                                'to_amt'=>$break_amount,
                                'to_transaction_id'=>$rel_trans_id2,
                                'relation_id'=>null,
                                'transaction_for'=>'CURRENCY_CONVERSION',
                                'debit_remark_data'=>['from_amount'=>Commonsettings::currency_format(['amt'=>$withdraw_exchange_amt, 'currency_id'=>$from_currency_id]), 'to_amount'=>Commonsettings::currency_format(['amt'=>$break_amount, 'currency_id'=>$currency_id]), 'rate'=>$rate],
                                'credit_remark_data'=>['from_amount'=>Commonsettings::currency_format(['amt'=>$withdraw_exchange_amt, 'currency_id'=>$from_currency_id]), 'to_amount'=>Commonsettings::currency_format(['amt'=>$break_amount, 'currency_id'=>$currency_id]), 'rate'=>$rate]
                            ]);
                            $conversion_details[] = [
                                'wallet_id'=>$from_wallet_id,
                                'currency_id'=>$from_currency_id,
                                'rate'=>$rate,
                                'from_amount'=>$withdraw_exchange_amt,
                                'to_amount'=>$break_amount,
                                'debit_transaction_id'=>$rel_trans_id,
                                'credit_transaction_id'=>$rel_trans_id2
                            ];
                        }
                    }
                }
            }
        }
        $balance_details = $this->walletObj->get_user_balance($account_id, $wallet_id, $currency_id);
        if ($balance_details && !empty($balance_details) && $balance_details->current_balance >= $amount)
        {
            $withdrawal_rel_trans_id = '';
            if (isset($relation_transaction_id))
            {
                $withdrawal_rel_trans_id = implode(',', $relation_transaction_id);
            }
            $withdrawal_tds = json_decode(unserialize(stripslashes($this->commonObj->get_setting_value('withdrawal_tds'))), true);
            if (isset($withdrawal_tds[$currency_id]))
            {
                $tds_charge = $withdrawal_tds[$currency_id];
            }
            $total_debit = $balance_details->tot_debit + $amount;
            $current_balance = $balance_details->current_balance - $amount;
            $tds = $amount * $tds_charge / 100;
            $paid_amt = $amount - $charge + $tds;
            $creeated_on = $expected_date = date('Y-m-d H:i:s');
            $d = date('d', strtotime($expected_date));
            if ($d <= 15)
            {
                $dd = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($expected_date)), date('Y', strtotime($expected_date)));
                $ds = $dd - ($d % $dd);
                $expected_on = date('d-M-Y', strtotime($ds.' days', strtotime($expected_date)));
            }
            else
            {
                $ds = $d - 15;
                $expected_on = date('d-M-Y', strtotime('-'.$ds.' days', strtotime('1 month ', strtotime($expected_date))));
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
            $insert_withdrawal['status_id'] = $this->config->get('constants.PENDING');
            $insert_withdrawal['created_on'] = $creeated_on;
            $insert_withdrawal['expected_on'] = $expected_on;
            $insert_withdrawal['updated_by'] = $account_id;
            $withdrawal_id = DB::table($this->config->get('tables.WITHDRAWAL'))
                    ->insertGetId($insert_withdrawal);
            //withdrawal transaction
            $trans = [
                'from_account_id'=>$account_id,
                'from_wallet_id'=>$wallet_id,
                'currency_id'=>$currency_id,
                'amt'=>$amount,
                'from_transaction_id'=>$transaction_id,
                'relation_id'=>$withdrawal_id,
                'transaction_for'=>'WITHDRAW',
                'tds'=>$tds,
                'debit_remark_data'=>['amount'=>Commonsettings::currency_format(['amt'=>$amount, 'currency_id'=>$currency_id])],
                'credit_remark_data'=>['amount'=>Commonsettings::currency_format(['amt'=>$amount, 'currency_id'=>$currency_id])]
            ];
            if ($charge > 0)
            {
                $trans['handle'] = [['amt'=>$charge, 'transaction_for'=>'WITHDRAWAL_CHARGES']];
            }
            $insert_trans_status = $this->commonObj->update_account_transaction($trans);
            if ($withdrawal_id && $insert_trans_status)
            {
                //update BTC balance debit;
                if ($payment_type_id == $this->config->get('constants.BITCOIN_WITHDRAWAL'))
                {
                    $this->update_admin_btc_balance($amount, $currency_id, $this->config->get('constants.TRANSACTION_TYPE.DEBIT'));
                }
                DB::commit();
                return true;
            }
        }
        DB::rollback();
        return false;
    }
}

