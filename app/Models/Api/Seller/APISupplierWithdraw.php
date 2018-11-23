<?php

class APISupplierWithdraw extends Eloquent
{

    public function __construct (&$commonObj)
    {
        $this->commonObj = $commonObj;
    }

    public function withdrawal_payment_list ($arr = array())
    {
        $res = DB::table(Config::get('tables.WITHDRAWAL_PAYMENT_TYPE').' as pay')
                ->join(Config::get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'pay.payment_type_id')
                ->where('pt.status', Config::get('constants.ON'))
                ->selectRaw('pt.payment_type,pt.payment_key,pay.charges,pay.description,pt.image_name');
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
            $payment_types = $res->get();
            array_walk($payment_types, function($payment_type)
            {
                $payment_type->image_name = URL::asset($payment_type->image_name);
            });
            return $payment_types;
        }
    }

    public function paymentTypeDetails ($payment_key)
    {
        $paymentType = DB::table(Config::get('tables.WITHDRAWAL_PAYMENT_TYPE').' as pay')
                ->join(Config::get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'pay.payment_type_id')
                ->where('pt.status', Config::get('constants.ON'))
                ->where('pt.payment_key', $payment_key)
                ->select('pt.payment_type_id', 'pt.payment_type', 'pt.payment_key', 'pay.description', 'pay.charges', 'pay.is_country_based', 'pay.is_user_country_based', 'pay.countries_allowed', 'pay.currency_allowed', 'pay.countries_not_allowed')
                ->first();
        if ($paymentType)
        {
            if ($paymentType->is_country_based == Config::get('constants.ON') || $paymentType->is_user_country_based == Config::get('constants.ON'))
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
                        $all_countries = DB::table(Config::get('tables.LOCATION_COUNTRY'))->where('status', Config::get('constants.ON'))->remember(30)->lists('country_id');
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
            $paymentType->currency_allowed = $this->commonObj->getCurrencies(!empty($paymentType->currency_allowed) ? explode(',', $paymentType->currency_allowed) : []);
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
                $acct_balance = DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE').' as ub')
                        ->join(Config::get('tables.CURRENCIES').' as cr', 'cr.currency_id', '=', 'ub.currency_id')
                        ->join(Config::get('tables.WALLET').' as w', function($subquery)
                        {
                            $subquery->on('w.wallet_id', '=', 'ub.wallet_id')
                            ->where('w.withdrawal_status', '=', Config::get('constants.ON'))
                            ->where('w.status', '=', Config::get('constants.ON'));
                        })
                        ->where('ub.account_id', $account_id)
                        ->whereRaw('ub.current_balance IS NOT NULL')
                        ->whereRaw('ub.current_balance != 0')
                        ->select('ub.current_balance', 'ub.currency_id', 'cr.currency_symbol', 'cr.currency', 'w.wallet_id', 'w.wallet_name as wallet')
                        ->get();
                if ($acct_balance)
                {
                    array_walk($acct_balance, function(&$balance) use(&$current_balance, $currency_id)
                    {
                        $balance->min = 0;
                        $rate = $this->get_currency_exchange_rate($balance->currency_id, $currency_id);
                        $balance->equivalent = $balance->max = (float) $balance->current_balance * (float) $rate;
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
                        $op['charge'] = ($bws->charge[1]->charge_type == Config::get('constants.PERCENTAGE')) ? (float) ($amount * $bws->charge[1]->charge) / 100 : $bws->charge[1]->charge;
                        $op['charge_type'] = $bws->charge[1]->charge_type;
                        $op['charges'] = $bws->charge[1]->charge;
                    }
                    else
                    {
                        $op['charge'] = ($bws->charge[0]->charge_type == Config::get('constants.PERCENTAGE')) ? (float) ($amount * $bws->charge[0]->charge) / 100 : $bws->charge[0]->charge;
                        $op['charge_type'] = $bws->charge[0]->charge_type;
                        $op['charges'] = $bws->charge[0]->charge;
                    }
                }
                else
                {
                    $op['charge'] = ($bws->charge->charge_type == Config::get('constants.PERCENTAGE')) ? (float) ($amount * $bws->charge->charge) / 100 : $bws->charge->charge;
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

    public function get_withdrwal_settings ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.WITHDRAWAL_SETTINGS').' as wd')
                ->join(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'wd.currency_id')
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
            $charges = json_decode(unserialize(stripslashes($settings->charges)), true);
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

    public function saveWithdrawal ($arr = array())
    {
        extract($arr);
        $wallet_id = Config::get('constants.WALLET.PERSONAL');
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
                        $balance_bycurrency = $this->commonObj->get_user_balance($account_id, $from_wallet_id, $from_currency_id);
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
        $balance_details = $this->commonObj->get_user_balance($account_id, $wallet_id, $currency_id);
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
            $insert_withdrawal['status_id'] = Config::get('constants.PENDING');
            $insert_withdrawal['created_on'] = $creeated_on;
            $insert_withdrawal['expected_on'] = $expected_on;
            $insert_withdrawal['updated_by'] = $account_id;
            $withdrawal_id = DB::table(Config::get('tables.WITHDRAWAL'))
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
                if ($payment_type_id == Config::get('constants.BITCOIN_WITHDRAWAL'))
                {
                    $this->update_admin_btc_balance($amount, $currency_id, Config::get('constants.TRANSACTION_TYPE.DEBIT'));
                }
                DB::commit();
                return true;
            }
        }
        DB::rollback();
        return false;
    }

    public function update_admin_btc_balance ($amount, $currency_id, $trans_type)
    {
        $bitcoin_balance = $this->get_bitcoin_balance_details();
        if ($bitcoin_balance)
        {
            $tot_debit = $bitcoin_balance->tot_debit;
            $current_balance = $bitcoin_balance->current_balance;
            $bala_id = $bitcoin_balance->abb_id;
            $balance_update = 1;
        }
        if ($trans_type == 0)
        {
            $balance_details['tot_debit'] = $tot_debit = $tot_debit + $amount;
            $balance_details['current_balance'] = $arrData['current_balance'] = $current_balance = $current_balance - $amount;
        }
        elseif ($trans_type == 1)
        {
            $balance_details['tot_credit'] = $tot_credit = $tot_credit + $amount;
            $balance_details['current_balance'] = $arrData['current_balance'] = $current_balance = $current_balance + $amount;
        }
        if ($balance_update)
        {
            $result1 = DB::table(Config::Get('constants.ADMIN_BITCOIN_BALANCE'))
                    ->where('abb_id', $bala_id)
                    ->where('currency_id', $currency_id)
                    ->update($balance_details);
        }
        else
        {
            $result1 = DB::table(Config::Get('constants.ADMIN_BITCOIN_BALANCE'))
                    ->insert($balance_details);
        }
    }

    public function getWithdrawalDetails ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.WITHDRAWAL').' as wid')
                ->join(Config::get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wid.payment_type_id')
                ->join(Config::get('tables.WALLET').' as wt', 'wt.wallet_id', '=', 'wid.wallet_id')
                ->join(Config::get('tables.WITHDRAWAL_STATUS').' as st', 'st.status_id', '=', 'wid.status_id')
                ->join(Config::get('tables.CURRENCIES').' as ci', 'wid.currency_id', '=', 'ci.currency_id')
                ->where('wid.account_id', $account_id)
                ->where('wid.is_deleted', Config::get('constants.OFF'));
        if (isset($transaction_id))
        {
            $query->where('wid.transaction_id', $transaction_id);
        }
        $withdrawal = $query->selectRaw('pt.payment_type,st.status,wid.transaction_id,wid.status_id,wid.amount,wid.paidamt,wid.handleamt,wid.created_on,wid.expected_on,wid.cancelled_on,wid.confirmed_on,ci.currency,ci.currency_symbol,wid.account_info,wid.conversion_details')
                ->first();
        if (!empty($withdrawal))
        {
            $withdrawal->amount = $withdrawal->currency_symbol.' '.number_format($withdrawal->amount, 2, '.', ',').' '.$withdrawal->currency;
            $withdrawal->paidamt = $withdrawal->currency_symbol.' '.number_format($withdrawal->paidamt, 2, '.', ',').' '.$withdrawal->currency;
            $withdrawal->handleamt = $withdrawal->currency_symbol.' '.number_format($withdrawal->handleamt, 2, '.', ',').' '.$withdrawal->currency;
            $withdrawal->expected_on = date('d-M-Y', strtotime($withdrawal->expected_on));
            $withdrawal->created_on = date('d-M-Y H:i:s', strtotime($withdrawal->created_on));
            $withdrawal->confirmed_on = !empty($withdrawal->confirmed_on) ? date('d-M-Y H:i:s', strtotime($withdrawal->confirmed_on)) : null;
            $withdrawal->conversion_details = json_decode($withdrawal->conversion_details);
            $withdrawal->account_info = json_decode($withdrawal->account_info);
            if (!empty($withdrawal->conversion_details))
            {
                array_walk($withdrawal->conversion_details, function(&$convert) use($withdrawal)
                {
                    $convert->wallet = $this->commonObj->get_wallet_name($convert->wallet_id);
                    $currency = $this->commonObj->get_currency($convert->currency_id);
                    $convert->from_amount = $currency->currency.' '.number_format($convert->from_amount, 2, '.', ',').' '.$currency->currency_symbol;
                    $convert->to_amount = $withdrawal->currency.' '.number_format($convert->to_amount, 2, '.', ',').' '.$withdrawal->currency_symbol;
                    unset($convert->wallet_id);
                    unset($convert->currency_id);
                    unset($convert->rate);
                });
            }
            $withdrawal->actions = [];
            if ($withdrawal->status_id == Config::get('constants.WITHDRAWAL_STATUS.PENDING'))
            {
                $withdrawal->actions['CANCEL'] = [
                    'title'=>'Cancel',
                    'data'=>[
                        'transaction_id'=>$withdrawal->transaction_id,
                        'status_id'=>Config::get('constants.WITHDRAWAL_STATUS.CANCELLED')
                    ],
                    'url'=>URL::to('supplier/withdraw/update-status')
                ];
            }
            unset($withdrawal->status_id);
        }
        return $withdrawal;
    }

    public function get_bitcoin_balance_details ()
    {
        return DB::table(Config::get('constants.ADMIN_BITCOIN_BALANCE').' as abb')
                        ->join(Config::get('constants.CURRENCIES').' as cu', 'cu.id', '=', 'abb.currency_id')
                        ->select('abb.*', 'cu.code as currency_code')
                        ->first();
    }

    public function get_currency_exchange_rate ($from_currency_id, $to_currency_id)
    {
        if ($from_currency_id != $to_currency_id)
        {
            return DB::table(Config::get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                            ->select('rate')
                            ->where(array(
                                'from_currency_id'=>$from_currency_id,
                                'to_currency_id'=>$to_currency_id
                            ))
                            ->pluck('rate');
        }
        return 1;
    }

    public function withdrawal_list ($arr = array(), $count = false, $payment_typesList = false, $currenciesList = false)
    {
        extract($arr);
        $query = DB::table(Config::get('tables.WITHDRAWAL').' as wid')
                ->join(Config::get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wid.payment_type_id')
                ->join(Config::get('tables.WALLET').' as wt', 'wt.wallet_id', '=', 'wid.wallet_id')
                ->join(Config::get('tables.CURRENCIES').' as ci', 'ci.currency_id', '=', 'wid.currency_id')
                ->where('wid.account_id', $account_id)
                ->where('wid.is_deleted', Config::get('constants.OFF'));
        if ($payment_typesList)
        {
            return $query->selectRaw('DISTINCT(pt.payment_type_id),pt.payment_type')
                            ->orderby('pt.payment_type', 'ASC')
                            ->lists('payment_type', 'payment_type_id');
        }
        if ($currenciesList)
        {
            return $query->selectRaw('DISTINCT(wid.currency_id),ci.currency')
                            ->orderby('ci.currency', 'ASC')
                            ->lists('currency', 'currency_id');
        }
        if (isset($withdrawal_id))
        {
            $query->where('wid.withdrawal_id', $withdrawal_id);
        }
        if (isset($search_term) && !empty($search_term))
        {
            $query->whereRaw('(wid.transaction_id like \'%'.$search_term.'%\')');
        }
        if (isset($from) && !empty($from))
        {
            $query->whereDate('wid.created_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (isset($to) && !empty($to))
        {
            $product->whereDate('wid.created_on', '<=', date('Y-m-d', strtotime($to)));
        }
        if (isset($currency_id) && !empty($currency_id))
        {
            $query->where('wid.currency_id', $currency_id);
        }
        if (isset($payment_type_id) && !empty($payment_type_id))
        {
            $query->where('wid.payment_type_id', $payment_type_id);
        }
        if (isset($status))
        {
            $query->where('wid.status_id', $status);
        }
        if (isset($start) && isset($length))
        {
            $query->skip($start)->take($length);
        }
        if (isset($orderby) && !empty($orderby))
        {
            $query->orderby($orderby, $order);
        }
        if ($count)
        {
            return $query->count();
        }
        else
        {
            $withdrawals = $query->selectRaw('pt.payment_type,wid.transaction_id,wid.status_id,wid.amount,wid.paidamt,wid.handleamt,wid.created_on,wid.expected_on,wid.cancelled_on,wid.confirmed_on,ci.currency,ci.currency_symbol')
                    ->get();
            if (!empty($withdrawals))
            {
                array_walk($withdrawals, function(&$withdrawal)
                {
                    $withdrawal->amount = $withdrawal->currency_symbol.' '.number_format($withdrawal->amount, 2, '.', ',').' '.$withdrawal->currency;
                    $withdrawal->paidamt = $withdrawal->currency_symbol.' '.number_format($withdrawal->paidamt, 2, '.', ',').' '.$withdrawal->currency;
                    $withdrawal->handleamt = $withdrawal->currency_symbol.' '.number_format($withdrawal->handleamt, 2, '.', ',').' '.$withdrawal->currency;
                    $withdrawal->expected_on = date('d-M-Y', strtotime($withdrawal->expected_on));
                    $withdrawal->created_on = date('d-M-Y H:i:s', strtotime($withdrawal->created_on));
                    $withdrawal->cancelled_on = !empty($withdrawal->cancelled_on) ? date('d-M-Y H:i:s', strtotime($withdrawal->cancelled_on)) : null;
                    $withdrawal->confirmed_on = !empty($withdrawal->confirmed_on) ? date('d-M-Y H:i:s', strtotime($withdrawal->confirmed_on)) : null;
                    $withdrawal->actions = [];
                    $withdrawal->actions['DETAILS'] = [
                        'title'=>'Details',
                        'class'=>'withdraw-details',
                        'data'=>[
                            'transaction_id'=>$withdrawal->transaction_id
                        ],
                        'url'=>URL::route('api.v1.supplier.withdraw.details')
                    ];
                    if ($withdrawal->status_id == Config::get('constants.WITHDRAWAL_STATUS.PENDING'))
                    {
                        $withdrawal->actions['CANCEL'] = [
                            'title'=>'Cancel',
                            'data'=>[
                                'transaction_id'=>$withdrawal->transaction_id,
                                'status_id'=>Config::get('constants.WITHDRAWAL_STATUS.CANCELLED')
                            ],
                            'url'=>URL::route('api.v1.supplier.withdraw.update-status')
                        ];
                    }
                });
                return $withdrawals;
            }
        }
    }

    public function withdrawal_wallet_balance_list ($arr = array())
    {
        $res = DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE').' as wbt')
                ->join(Config::get('tables.WALLET').' as wa', 'wa.wallet_id', '=', 'wbt.wallet_id')
                ->join(Config::get('tables.CURRENCIES').' as ci', 'ci.currency_id', '=', 'wbt.currency_id')
                ->where('wa.withdrawal_status', Config::get('constants.ON'))
                ->where('wbt.account_id', $arr['account_id'])
                ->selectRaw('wbt.current_balance,ci.currency');
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
            return $res->get();
        }
    }

    public function updateWithdrawalStatus ($arr = array())
    {
        extract($arr);
        DB::beginTransaction();
        $updateQuery = DB::table(Config::get('tables.WITHDRAWAL'))
                ->where('transaction_id', $transaction_id)
                ->where('account_id', $account_id);
        switch ($status_id)
        {
            case Config::get('constants.WITHDRAWAL_STATUS.CANCELLED'):
                $withdrawal = DB::table(Config::get('tables.WITHDRAWAL'))
                        ->where('transaction_id', $transaction_id)
                        ->where('account_id', $account_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('withdrawal_id,transaction_id,account_id,wallet_id,currency_id,amount,conversion_details')
                        ->first();
                if (!empty($withdrawal))
                {
                    $withdrawal->conversion_details = json_decode($withdrawal->conversion_details);
                    $this->commonObj->update_account_transaction([
                        'to_account_id'=>$withdrawal->account_id,
                        'from_wallet_id'=>$withdrawal->wallet_id,
                        'currency_id'=>$withdrawal->currency_id,
                        'amt'=>$withdrawal->amount,
                        'relation_id'=>$withdrawal->withdrawal_id,
                        'transaction_for'=>'WITHDRAW_CANCEL'
                    ]);
                    if (!empty($withdrawal->conversion_details))
                    {
                        array_walk($withdrawal->conversion_details, function($convert) use($withdrawal)
                        {
                            $from_currency_code = $this->commonObj->get_currency_code($withdrawal->currency_id);
                            $from_currency_symbol = $this->commonObj->get_currency_symbol($withdrawal->currency_id);
                            $to_currency_code = $this->commonObj->get_currency_code($convert->currency_id);
                            $to_currency_symbol = $this->commonObj->get_currency_symbol($convert->currency_id);
                            $this->commonObj->update_account_transaction([
                                'from_account_id'=>$withdrawal->account_id,
                                'from_wallet_id'=>$withdrawal->wallet_id,
                                'currency_id'=>$withdrawal->currency_id,
                                'amt'=>$convert->to_amount,
                                'to_account_id'=>$withdrawal->account_id,
                                'to_wallet_id'=>$convert->wallet_id,
                                'to_currency_id'=>$convert->currency_id,
                                'to_amt'=>$convert->from_amount,
                                'relation_id'=>$withdrawal->withdrawal_id,
                                'transaction_for'=>'CURRENCY_CONVERSION',
                                'debit_remark_data'=>['from_amount'=>$from_currency_symbol.' '.number_format($convert->to_amount, 2, '.', ',').' '.$from_currency_code, 'to_amount'=>$from_currency_symbol.' '.number_format($convert->from_amount, 2, '.', ',').' '.$from_currency_code, 'rate'=>$convert->rate],
                                'credit_remark_data'=>['from_amount'=>$from_currency_symbol.' '.number_format($convert->to_amount, 2, '.', ',').' '.$from_currency_code, 'to_amount'=>$from_currency_symbol.' '.number_format($convert->from_amount, 2, '.', ',').' '.$from_currency_code, 'rate'=>$convert->rate]
                            ]);
                        });
                    }
                }
                $updateQuery->where('status_id', Config::get('constants.WITHDRAWAL_STATUS.PENDING'));
                if ($updateQuery->update(['status_id'=>Config::get('constants.WITHDRAWAL_STATUS.CANCELLED'), 'cancelled_on'=>date('Y-m-d H:i:s')]))
                {
                    DB::commit();
                    return true;
                }
                break;
        }
        DB::rollback();
        return false;
    }

    public function get_user_balance_details ($account_id)
    {
        if ($account_id)
        {
            $user_balance_details = DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                    ->select('tot_credit', 'current_balance', 'tot_debit')
                    ->where('wallet_id', '=', $ewallet_id)
                    ->where('account_id', '=', $account_id)
                    ->where('currency_id', '=', $currency_id)
                    ->first();
            if ($user_balance_details)
            {
                return $user_balance_details;
            }
        }
        return false;
    }

    public function update_status ($data = array())
    {
        if (!empty($data))
        {
            $update_withdrawal_particulars = array();
            $update_withdrawal_particulars = DB::table(Config::get('tables.WITHDRAWAL'))
                    ->where('withdrawal_id', $data['withdrawal_id']);
            switch ($data['status'])
            {
                case Config::get('constants.WITHDRAWAL_STATUS.CONFIRMED'):
                    $update_withdrawal_particulars->whereIn('status', array(Config::get('constants.WITHDRAWAL_STATUS.PENDING'), Config::get('constants.WITHDRAWAL_STATUS.PROCESSED')));
                    break;
                case Config::get('constants.WITHDRAWAL_STATUS.PROCESSED'):
                    $update_withdrawal_particulars->whereIn('status', array(Config::get('constants.WITHDRAWAL_STATUS.PENDING')));
                    break;
                case Config::get('constants.WITHDRAWAL_STATUS.CANCELLED'):
                    $update_withdrawal_particulars->whereIn('status', array(Config::get('constants.WITHDRAWAL_STATUS.PENDING'), Config::get('constants.WITHDRAWAL_STATUS.PROCESSED')));
                    break;
                default:
                    $update_withdrawal_particulars->whereIn('status', array(Config::get('constants.WITHDRAWAL_STATUS.PENDING')));
            }
            if (!empty($update_withdrawal_particulars))
            {
                return $update_withdrawal_particulars->update(array('status'=>$data['status'], 'reason'=>$data['reason']));
            }
        }
    }

    public function check_withdrawal_bank_info ($wd_id)
    {
        $chkbank = DB::table(Config::get('tables.WITHDRAWAL').' as a')
                ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as d', 'a.account_id', '=', 'd.account_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as c', 'a.account_id', '=', 'c.account_id')
                ->join(Config::get('tables.PAYMENT_TYPES').' as pay_t', 'pay_t.payment_type_id', '=', 'a.payment_type_id')
                ->join(Config::get('tables.CURRENCIES').' as ci', 'a.currency_id', '=', 'ci.currency_id')
                ->where('a.withdrawal_id', $wd_id)
                ->where('d.login_block', Config::get('constants.OFF'))
                ->where('c.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('a.*,d.uname,concat(c.firstname,\'\',c.lastname) as full_name,pay_t.payment_type_id,pay_t.payment_type,ci.currency,ci.currency_symbol')
                ->first();
        if (!empty($chkbank))
        {
            return $chkbank;
        }
        else
        {
            return NULL;
        }
    }

    public function get_payout_types ()
    {
        $res = DB::table(Config::get('tables.PAYMENT_TYPES'))
                ->where('status', Config::get('constants.ON'))
                ->select('payment_type', 'payment_type_id')
                ->get();
        if (!empty($res) && count($res) > 0)
        {
            return $res;
        }
        else
        {
            return NULL;
        }
    }

    public function checkNetwork_access ($admin_data)
    {
        if (($admin_data['admin_type']) == Config::get('constants.ADMIN_ROLE_NETWORKADMIN'))
        {
            $adminSettings = $this->get_adminSettings($admin_data['admin_id']);
            if (!empty($adminSettings) && !empty($adminSettings->accessible_root_ids))
            {
                return explode(',', $adminSettings->accessible_root_ids);
            }
        }
        return false;
    }

    public function get_withdraw_currency ()
    {
        $res = DB::table(Config::get('tables.WITHDRAWAL').' as bw')
                        ->join(Config::get('tables.CURRENCIES').' as cr', 'cr.currency_id', '=', 'bw.currency_id')
                        ->where('bw.is_deleted', 0)
                        ->select('bw.currency_id', 'cr.currency')
                        ->distinct('bw.currency_id')->get();
        if (!empty($res) && count($res) > 0)
        {
            return $res;
        }
        else
        {
            return NULL;
        }
    }

    public function get_preBank_info ($arrData)
    {
        extract($arrData);
        $result = DB::table(Config::get('tables.WITHDRAWAL'))
                ->where('account_id', $account_id)
                ->where('payment_type_id', $payment_type_id)
                ->where('currency_id', $currency_id)
                ->orderBy('withdrawal_id', 'DESC')
                ->pluck('account_info');
        if (!empty($result))
        {
            return json_decode($result);
        }
        return false;
    }

    public function get_withdrawal_payout_byid ($withdrawal_payment_type_id)
    {
        return DB::table(Config::get('tables.WITHDRAWAL_PAYMENT_TYPE'))
                        ->where('payment_type_id', $withdrawal_payment_type_id)
                        ->first();
    }

    public function Getbank_withdrawal_settingsBycurrency ($bank_currency, $withdrawal_payment_type = '')
    {
        return DB::table(Config::get('tables.WITHDRAWAL_SETTINGS').' as bw')
                        ->join(Config::get('tables.CURRENCIES').' as cr', 'cr.currency_id', '=', 'bw.currency_id')
                        ->join(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'bw.country_id')
                        ->where('bw.currency_id', $bank_currency)
                        ->where('bw.payment_type_id', $withdrawal_payment_type)
                        ->select('bw.*', 'cr.currency', 'lc.country', 'lc.country_id')
                        ->first();
    }

    public function get_withdrawBy_id ($id)
    {
        $result = DB::table(Config::get('tables.WITHDRAWAL'))
                ->where('withdrawal_id', $id)
                ->pluck('payment_details');
        if (!empty($result))
        {
            return $result;
        }
        else
        {
            return NULL;
        }
    }

    public function get_wallet ()
    {
        $result = DB::table(Config::get('tables.WALLET'))
                ->where('withdrawal_status', Config::get('constants.ON'))
                ->where('status', Config::get('constants.ON'))
                ->get();
        if (!empty($result))
        {
            return $result;
        }
        else
        {
            return NULL;
        }
    }

    public function get_related_transaction ($arrData)
    {
        extract($arrData);
        $transaction_log = DB::table(Config::get('tables.ACCOUNT_TRANSACTION').' as a')
                ->leftJoin(Config::get('tables.WALLET').' as b', 'b.wallet_id', '=', 'a.wallet_id')
                ->leftJoin(Config::get('tables.PAYMENT_TYPES').' as c', 'a.payment_type_id', '=', 'c.payment_type_id')
                ->leftJoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'a.currency_id')
                ->leftJoin(Config::get('tables.STATEMENT_LINE').' as e', 'a.statementline_id', '=', 'e.statementline_id')
                ->where('a.account_id', $account_id)
                ->where('a.transaction_id', $trans_id)
                ->selectRaw('a.*,b.wallet_name,c.payment_type,cur.currency,e.statementline as state_ment');
        $result = $transaction_log->first();
        if (!empty($result))
        {
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function withdrawal_status ()
    {
        return DB::table(Config::get('tables.WITHDRAWAL_STATUS').' as stat')
                        ->get();
    }

}
