<?php

namespace App\Models;

use Request;
use Illuminate\Database\Eloquent\Model;
use DB;

class BaseModel extends Model
{

    public $siteConfig = '';
    public $request = '';
    public $session = '';

    public function __construct ()
    {
        $this->config = config();
        $this->session = session();
        $this->siteConfig = config('settings');
    }

    public function updateAccountTransaction (array $arr = array(), $debit_only = false, $credit_only = false)
    {	
        $supplier_id = 0;
        $store_id = 0;        
        $debit_remark_data = [];
        $credit_remark_data = [];
        $debitted = $credited = false;
        $tax_amt = $handle_amt = 0;
		$paidamt = 0;
        extract($arr);
		//print_R($transaction_for); die;
		$relation_id = (isset($relation_id) && empty($relation_id)) ? (is_array($relation_id) ? implode(',', $relation_id) : $relation_id) : (isset($relation_id) && !empty($relation_id)) ? $relation_id : null;
        $from_account_id = (isset($from_account_id) && !empty($from_account_id)) ? $from_account_id : $this->config->get('constants.ACCOUNT.ADMIN_ID');
		$source_account_id = (isset($debit_source_account_id) && !empty($debit_source_account_id)) ? $debit_source_account_id : $from_account_id;
        $to_account_id = (isset($to_account_id) && !empty($to_account_id)) ? $to_account_id : $this->config->get('constants.ACCOUNT.ADMIN_ID');
        $payment_type_id = isset($payment_type_id) && !empty($payment_type_id) ? $payment_type_id : $this->config->get('constants.PAYMENT_TYPES.WALLET');
        $pay_mode = isset($pay_mode) && !empty($pay_mode) ? $pay_mode : $this->config->get('constants.PAYMENT_MODES.vim');
        $from_wallet_id = (isset($from_wallet_id) && !empty($from_wallet_id)) ? $from_wallet_id : $this->config->get('constants.WALLETS.VIM');
        $to_wallet_id = (isset($to_wallet_id) && !empty($to_wallet_id)) ? $to_wallet_id : $from_wallet_id;       
		
		if ($from_account_id == $this->config->get('constants.ACCOUNT.ADMIN_ID'))
        {
            $credit_only = true;
            $debit_only = false;
        }
        if ($to_account_id == $this->config->get('constants.ACCOUNT.ADMIN_ID'))
        {
            $credit_only = false;
            $debit_only = true;
        }	
		
		if (!$credit_only)
        {		

            if (isset($from_handle) && !empty($from_handle))
            {
                /* array_walk($from_handle, function(&$charge) use(&$handle_amt, $currency_id)
                {
                    $handle_amt += $charge['amt'];
                    $charge['currency_id'] = $currency_id;
                }); */
				
				foreach ($from_handle as &$charge)
                {
                    $handle_amt +=$charge['amt'];
                }
            }
            if (isset($from_taxes) && !empty($from_taxes))
            {
                /* array_walk($from_taxes, function(&$tax) use(&$tax_amt, $currency_id)
                {
                    $tax_amt += $tax['amt'];
                    $tax['currency_id'] = $currency_id;
                }); */
				
				foreach ($from_taxes as &$tax)
                {
                    $tax_amt += $tax['amt'];
                    $tax['currency_id'] = $currency_id;
                }
            }			
            //$paidamt = ($paidamt + $handle_amt + $tax_amt);
            //$paidamt = $paidamt > 0 ? $paidamt : $amt;
			$paidamt = $paidamt > 0 ? $paidamt : ($handle_amt + $tax_amt);
            $paidamt = $paidamt > 0 ? $paidamt : $amt;
            list($status, $bal) = $this->updateBalance($from_account_id, $from_wallet_id, $currency_id, $paidamt, false, $this->config->get('stline.'.$transaction_for.'.DEBIT'), $tax_amt);
            if ($status)
            {
                $from_transaction_id = isset($from_transaction_id) && !empty($from_transaction_id) ? $from_transaction_id : $this->generateTransactionID();					
                $debitted = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
                        ->insertGetID([
                    'account_id'=>$from_account_id,
                    'pay_mode_id'=>$pay_mode,
                    'from_account_id'=>$from_account_id,
					'source_account_id'=>$source_account_id,
                    'to_account_id'=>$to_account_id,
                    'wallet_id'=>$from_wallet_id,
                    'payment_type_id'=>$payment_type_id,                    
					'post_type'=>$this->config->get('stline.'.$transaction_for.'.POST_TYPE'),
                    'relation_id'=>$relation_id,
                    'amt'=>$amt,
                    'handle_amt'=>$handle_amt,
                    'paid_amt'=>$paidamt,
                    'tax'=>$tax_amt,
                    'currency_id'=>$currency_id,
                    'status'=>$this->config->get('constants.TRANSACTION_STATUS.CONFIRMED'),
                    'ip_address'=>Request::getClientIp(true),
                    'created_on'=>getGTZ(),
                    'transaction_id'=>$from_transaction_id,
                    'transaction_type'=>$this->config->get('constants.TRANSACTION_TYPE.DEBIT'),
                    'current_balance'=>$bal,
                    'statementline_id'=>$this->config->get('stline.'.$transaction_for.'.DEBIT'),
					'current_balance'=>$bal,
                    'remark'=>isset($remark) ? $remark : json_encode(['data'=>$debit_remark_data])
                ]);
                if ($debitted)
                {
                    if (!empty($supplier_id) && $this->isMerchant($from_account_id))
                    {
                        $merchant_trans['supplier_id'] = $supplier_id;
                        $merchant_trans['store_id'] = $store_id;
                        $merchant_trans['transaction_id'] = $debitted;
                        //$merchant_trans['statementline'] = $this->config->get('stline.'.$transaction_for.'.DEBIT');
                        DB::table($this->config->get('tables.SUPPLIER_CLIENT_TRANSACTION'))
                                ->insertGetID($merchant_trans);
                    }
					if (isset($from_taxes) && !empty($from_taxes) && $tax_amt != 0)
                    {
                        foreach ($from_taxes as $key => $tax)
                        {
							if ($key == 0) {
								DB::table($this->config->get('tables.ACCOUNT_TRANSACTION_TAXES'))
										->insert([
											'id'=>$debitted,
											'account_id'=>$from_account_id,
											'tax_id'=>$tax['tax_id'],
											'currency_id'=>$tax['currency_id'],
											'tax_amt'=>$tax['amt'],
											'created_on'=>getGTZ()
								]);
							}
                        }
                    }
                    
                }
            }
        }

        if (!$debit_only && ($credit_only || $debitted))
        {		
            $currency_id = (isset($to_currency_id) && !empty($to_currency_id)) ? $to_currency_id : $currency_id;				
            $amt = (isset($to_amt) && !empty($to_amt)) ? $to_amt : $amt;
			$tax_amt = $handle_amt = 0;
            if (isset($to_handle) && !empty($to_handle))
            {
                array_walk($to_handle, function(&$charge) use(&$handle_amt, $currency_id)
                {
                    $handle_amt += $charge['amt'];
                    $charge['currency_id'] = $currency_id;
                });
            }
            if (isset($to_taxes) && !empty($to_taxes))
            {
                array_walk($to_taxes, function(&$tax) use(&$tax_amt, $currency_id)
                {
                    $tax_amt += $tax['amt'];
                    $tax['currency_id'] = $currency_id;
                });
            }
			$paidamt = isset($paidamt) && !empty($paidamt) ? $paidamt : ($amt - ($handle_amt + $tax_amt));				
			list($status, $bal) = $this->updateBalance($to_account_id, $to_wallet_id, $currency_id, $paidamt, true, $this->config->get('stline.'.$transaction_for.'.CREDIT'), $tax_amt);
			if ($status)
            {
				$to_transaction_id = isset($to_transaction_id) && !empty($to_transaction_id) ? $to_transaction_id : $this->generateTransactionID();
				$source_account_id = (isset($credit_source_account_id) && !empty($credit_source_account_id)) ? $credit_source_account_id : $from_account_id;
                $credited = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
                        ->insertGetID([
                    'account_id'=>$to_account_id,
                    'pay_mode_id'=>$pay_mode,
                    'from_account_id'=>$from_account_id,
					'source_account_id'=>$source_account_id,
                    'to_account_id'=>$to_account_id,
                    'wallet_id'=>$to_wallet_id,
                    'payment_type_id'=>$payment_type_id,
                    'post_type'=>$this->config->get('stline.'.$transaction_for.'.POST_TYPE'),
                    'relation_id'=>$relation_id,
                    'amt'=>$amt,
                    'handle_amt'=>$handle_amt,
                    'paid_amt'=>$paidamt,
                    'tax'=>$tax_amt,
                    'currency_id'=>$currency_id,
                    'status'=>$this->config->get('constants.TRANSACTION_STATUS.CONFIRMED'),
                    'ip_address'=>Request::getClientIp(true),
                    'created_on'=>getGTZ(),
                    'transaction_id'=>$to_transaction_id,
                    'transaction_type'=>$this->config->get('constants.TRANSACTION_TYPE.CREDIT'),
                    'current_balance'=>$bal,
                    'statementline_id'=>$this->config->get('stline.'.$transaction_for.'.CREDIT'),
                    'remark'=>isset($remark) ? $remark : json_encode(['data'=>$credit_remark_data])
                ]);				
				
				if ($credited)
                {
                    if (!empty($supplier_id) && $this->isMerchant($to_account_id))
                    {
                        $merchant_trans['supplier_id'] = $supplier_id;
                        $merchant_trans['store_id'] = $store_id;
                        $merchant_trans['transaction_id'] = $credited;
                        //$merchant_trans['statementline'] = $this->config->get('stline.'.$transaction_for.'.CREDIT');
                        DB::table($this->config->get('tables.SUPPLIER_CLIENT_TRANSACTION'))
                                ->insertGetID($merchant_trans);						
                    }
					
					if (isset($to_taxes) && !empty($to_taxes) && $tax_amt != 0)
                    {
                        foreach ($to_taxes as $key => $tax)
                        {
							if ($key == 0) {
								DB::table($this->config->get('tables.ACCOUNT_TRANSACTION_TAXES'))
										->insert([
											'id'=>$credited,
											'account_id'=>$to_account_id,
											'tax_id'=>$tax['tax_id'],
											'currency_id'=>$tax['currency_id'],
											'tax_amt'=>$tax['amt'],
											'created_on'=>getGTZ()
								]);
							}
                        }
                    }                   
                }				
			}            
        } 
        if ($debit_only && $debitted)
        {
            return $from_transaction_id;
        }
        else if ($credit_only && $credited)
        {
            return $to_transaction_id;
        }
        else if ($credited)
        {
            return $to_transaction_id;
        }
        return false;
    }
	
	public function isMerchant ($account_id)
    {
        return DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->whereIn('account_type_id', [$this->config->get('constants.ACCOUNT_TYPE.SELLER')])
                        ->exists();
    }
	
    public function getBalance ($account_id, $wallet_id, $currency_id, $to_currency_id = null)
    {
        $balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                ->where('account_id', $account_id)
                ->where('currency_id', $currency_id)
                ->where('wallet_id', $wallet_id)
                ->first();
        if ($balance)
        {
            return !empty($to_currency_id) ? ($this->exchangeRate($currency_id, $to_currency_id) * $balance->current_balance) : $balance->current_balance;
        }
        else
        {
            DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                    ->insert(['account_id'=>$account_id, 'currency_id'=>$currency_id, 'wallet_id'=>$wallet_id, 'tot_credit'=>0, 'current_balance'=>0, 'updated_date'=>getGTZ()]);
            return 0;
        }
    }
	
    public function updateBalance ($account_id, $wallet_id, $currency_id, $amount, $increment = true, $statement_line = null, $tax = 0)
    {			
		$conditions = $values = [
            'account_id'=>$account_id,
            'currency_id'=>$currency_id,
            'wallet_id'=>$wallet_id
        ];
        $value['updated_date'] = getGTZ();
        $tax_less_amount = $amount;
		if ($statement_line)
        {
            if (($tax != 0 && DB::table($this->config->get('tables.STATEMENT_LINE'))
                            ->where('statementline_id', $statement_line)
                            ->whereNotNull('tax_type')
                            ->exists()) || DB::table($this->config->get('tables.STATEMENT_LINE'))
                            ->where('statementline_id', $statement_line)
                            ->where('allow_minus_balance', '=', $this->config->get('constants.ON'))
                            ->exists())
            {
                $tax_less_amount-=$tax;
            }
        }			
		
		if ($increment || (!$increment && DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
                        ->join($this->config->get('tables.ACCOUNT_TYPES').' as sr', 'sr.id', '=', 'am.account_type_id')
                        ->leftJoin($this->config->get('tables.ACCOUNT_BALANCE').' as ab', function($ab) use($currency_id, $wallet_id)
                        {
                            $ab->on('ab.account_id', '=', 'am.account_id')
                            ->where('currency_id', '=', $currency_id)
                            ->where('wallet_id', '=', $wallet_id);
                        })
                        ->where('am.account_id', $account_id)
                        ->where(function($b) use($tax_less_amount)
                        {
                            $b->where('sr.allow_minus_balance', $this->config->get('constants.ON'))
                            ->orWhere(function($b2) use($tax_less_amount)
                            {
                                $b2->where('sr.allow_minus_balance', $this->config->get('constants.OFF'))
                                ->where('ab.current_balance', '>=', $tax_less_amount);
                            });
                        }) 
                        ->exists()))
        {
			$balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                    ->where($conditions)
                    ->first();
			if ($balance)
            {
                if ($increment)
                {
                    $values['current_balance'] = $balance->current_balance + $amount;
                    $values['tot_credit'] = $balance->tot_credit + $amount;
                }
                else
                {
                    $values['current_balance'] = $balance->current_balance - $amount;
                    $values['tot_debit'] = $balance->tot_debit + $amount;
                }
                DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                        ->where($conditions)
                        ->update($values);
            }
            else
            {
                if ($increment)
                {
                    $values['current_balance'] = $amount;
                    $values['tot_credit'] = $amount;
                }
                else
                {
                    $values['current_balance'] = -1 * $amount;
                    $values['tot_debit'] = $amount;
                }
                DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                        ->insert($values);
            }
            return [true, DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                        ->where($conditions)
                        ->value('current_balance')];
		} 
		else
        {
            return [false, 0];
        }       
    }

    public function updateCommissionTransaction (array $arr = array(), $system_role_id, $debit = false)
    {
        extract($arr);
        DB::Begintransaction();
        $bal = [];
        $bal['updated_on'] = getGTZ();
        if (DB::table($this->config->get('tables.ACCOUNT_COMMISSION_BALANCE'))
                        ->where('account_id', $account_id)
                        ->where('currency_id', $currency_id)
                        ->exists())
        {
            if ($debit)
            {
                $bal['com_balance'] = DB::raw('com_balance-'.$commission);
                $bal['com_debit'] = DB::raw('com_debit+'.$commission);
            }
            else
            {
                $bal['com_balance'] = DB::raw('com_balance+'.$commission);
                $bal['com_credit'] = DB::raw('com_credit+'.$commission);
            }
            $res = DB::table($this->config->get('tables.ACCOUNT_COMMISSION_BALANCE'))
                    ->where('account_id', $account_id)
                    ->where('currency_id', $currency_id)
                    ->update($bal);
        }
        else
        {
            $bal['account_id'] = $account_id;
            $bal['currency_id'] = $currency_id;
            $bal['com_balance'] = $commission;
            $bal['com_credit'] = $commission;
            $res = DB::table($this->config->get('tables.ACCOUNT_COMMISSION_BALANCE'))
                    ->insert($bal);
        }
        if (!$debit && $res)
        {
            $tds = DB::table($this->config->get('tables.ACCOUNT_COMMISSION_BALANCE'))
                    ->where('account_id', $account_id)
                    ->where('currency_id', $currency_id)
                    ->select_raw('com_balance,(com_balance-tds_paid_for) as tds_to_paid')
                    ->first();
            $tds_settings = DB::table($this->config->get('tables.SETTINGS'))
                    ->where('setting_key', 'tds-settings')
                    ->value('setting_value');
            $tds_settings = json_decode($tds_settings, true);
            $tds_settings = $tds_settings[$system_role_id];
            if ($tds_settings->limit <= $tds->com_balance)
            {
                if ($this->updateAccountTransaction([
                            'from_account_id'=>$account_id,
                            'from_currency_id'=>$currency_id,
                            'amt'=>$tds->tds_to_paid * $tds_per / 100,
                            'relation_id'=>$relation_id,
							'debit_remark_data'=>['amount'=>CommonLib::currency_format($tds->tds_to_paid * $tds_per / 100, $currency_id, true, true)],
					        'credit_remark_data'=>['amount'=>CommonLib::currency_format($tds->tds_to_paid * $tds_per / 100, $currency_id, true, true)],	
                            'transaction_for'=>'TDS_DEDUCTION'
                        ]))
                {
                    if ($relation_id = DB::table($this->config->get('tables.TDS_TRANSACTION'))
                            ->insertGetID([
                        'account_id'=>$account_id,
                        'currency_id'=>$currency_id,
                        'total_commission'=>$tds->tds_to_paid,
                        'tds_per'=>$tds_settings->per,
                        'tds'=>$tds->tds_to_paid * $tds_per / 100,
                        'transferred_on'=>getGTZ()
                            ]))
                    {
                        if (DB::table($this->config->get('tables.ACCOUNT_COMMISSION_BALANCE'))
                                        ->where('account_id', $account_id)
                                        ->where('currency_id', $currency_id)
                                        ->update(['tds_paid_for'=>DB::raw('tds_paid_for+'.$tds->tds_to_paid), 'updated_on'=>getGTZ()]))
                        {
                            DB::commit();
                            return true;
                        }
                    }
                }
            }
            else
            {
                DB::commit();
                return true;
            }
        }
        DB::rollback();
        return false;
    }

    public function generateTransactionID ()
    {
        return $this->rKeyGen(2, 1).getGTZ('dmYHis').rand(111, 999);
    }

    function rKeyGen ($digits, $datatype)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $poss_ALP = array();
        $j = 0;
        if ($datatype == 1)
        {
            for ($i = 49; $i < 58; $i++)
            {
                $poss[$j] = chr($i);
                $poss_ALP[$j] = $poss[$j];
                $j = $j + 1;
            }
            for ($k = 1; $k <= $digits; $k++)
            {
                $key = $key.$poss[rand(1, 8)];
            }
            $key;
        }
        else
        {
            $key = $this->rKeyGen_ALPHA($digits, false);
        }
        return $key;
    }

    function rKeyGen_ALPHA ($digits, $lc)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $j = 0;
// Place numbers 0 to 10 in the array
        for ($i = 50; $i < 57; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
// Place A to Z in the array
        for ($i = 65; $i < 90; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
// Place a to z in the array
        for ($k = 97; $k < 122; $k++)
        {
            $poss[$j] = chr($k);
            $j = $j + 1;
        }
        $ub = 0;
        if ($lc == true)
            $ub = 61;
        else
            $ub = 35;
        for ($k = 1; $k <= 3; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        for ($k = 4; $k <= $digits; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        return $key;
    }

    public function get_currencies ()
    {
        return DB::table(config('tables.CURRENCIES'))
                        ->lists('currency as code', 'currency_id as id');
    }

    public function get_merchant_details (array $arr = array())
    {

        $qry = DB::table(config('tbl.MERCHANT_MST').' as mm')
                ->join(config('tbl.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mm.account_id')
                ->join(config('tbl.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                ->join(config('tbl.MERCHANT_SETTINGS').' as ast', 'ast.supplier_id', '=', 'mm.supplier_id')
                ->where('am.system_role_id', config('constants.SYSTEM_ROLE.MERCHANT'))
                ->where('mm.status', config('constants.ON'))
                ->where('mm.is_deleted', config('constants.OFF'))
                ->where('mm.block', config('constants.OFF'))
                ->where('ast.is_verified', config('constants.ON'))
                ->where('ast.block_trans', config('constants.OFF'));
        if (isset($arr['mrcode']) && !empty($arr['mrcode']))
        {
            $qry->where('mm.mrcode', $arr['mrcode']);
        }
        if (isset($arr['account_id']) && !empty($arr['account_id']))
        {
            $qry->where('mm.account_id', $arr['account_id']);
        }

        $result = $qry->select('mm.mrcode', 'mm.mrbusiness_name', 'mm.mrlogo', 'mm.account_id', 'am.uname', 'am.email', 'am.mobile', DB::raw('(select group_concat(DISTINCT(currency_id))  from '.$this->config->get('tbl.MERCHANT_STORE_MST').' where supplier_id=mm.supplier_id) as currency_id'), DB::raw('CONCAT_WS(\' \',ad.first_name,ad.last_name) as full_name'))->first();

        if (!empty($result))
        {
            $result->currency_id = explode(',', $result->currency_id);
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function get_dsa_details (array $arr = array())
    {
        $email = '';
        $uname = '';
        if (!empty($arr['member']))
        {
            $str = strpos($arr['member'], '@');
            if (!empty($str))
            {
                $email = $arr['member'];
            }
            else
            {
                $uname = $arr['member'];
            }

            if (!empty($email) || !empty($uname))
            {
                $qry = DB::table(config('tables.ACCOUNT_MST').' as am')
                        ->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                        ->join(config('tables.ACCOUNT_SETTINGS').' as ast', 'ast.account_id', '=', 'am.account_id')
                        ->where('am.system_role_id', config('constants.SYSTEM_ROLE.DSA'))
                        ->where('am.status', config('constants.ON'))
                        ->where('am.is_deleted', config('constants.OFF'))
                        ->where('am.block', config('constants.OFF'))
                        ->where('ast.is_email_verified', config('constants.ON'))
                        ->where('ast.is_mobile_verified', config('constants.ON'));
                if (isset($email) && !empty($email))
                {
                    $qry->where('am.email', $email);
                }
                if (isset($uname) && !empty($uname))
                {
                    $qry->where('am.uname', $uname);
                }
                if (isset($arr['account_id']) && !empty($arr['account_id']))
                {
                    $qry->where('am.account_id', $arr['account_id']);
                }
                $result = $qry->select('am.uname', 'am.email', 'am.account_id', 'ast.currency_id', 'am.mobile', DB::raw('CONCAT_WS(\' \',ad.first_name,ad.last_name) as full_name', 'ast.currency_id', 'ast.country_id'))->first();
                return (!empty($result)) ? $result : false;
            }
            else
            {
                return false;
            }
        }
    }

    public function get_account_bal (array $arr = array())
    {
        return DB::table(config('tables.ACCOUNT_BALANCE'))
                        ->where('account_id', $arr['account_id'])
                        ->where('currency_id', $arr['currency_id'])
                        ->where('wallet_id', $arr['wallet'])
                        ->first();
    }

    public function exchangeRate ($from_currency, $to_currency)
    {
        return DB::table($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                        ->where('from_currency_id', $from_currency)
                        ->where('to_currency_id', $to_currency)
                        ->value('live_rate');
    }

    public function slug ($text, $replace = '-')
    {
        //replace non letter or digits by (_)
        $text = preg_replace('/\W|_/', $replace, $text);
        // Clean up extra dashes
        $text = preg_replace('/-+/', '-', trim($text, $replace)); // Clean up extra dashes
        // lowercase
        $text = strtolower($text);
        if (empty($text))
        {
            return false;
        }
        return $text;
    }

    public function xpb_encrypt (array $encrypt = array(), $key = null)
    {
        return $encrypt;
    }

// Decrypt Function
    public function xpb_decrypt ($decrypt, $key = null)
    {
        return $decrypt;
    }

    public function get_currencies_code ($id)
    {

        return DB::table($this->config->get('tables.CURRENCIES'))
                        ->where('id', $id)
                        ->pluck('code');
    }

    public function getSetting ($key)
    {		
		return $query = DB::table($this->config->get('tables.SETTINGS'))
                        ->where('setting_key', $key)
                        ->value('setting_value');
    }

    public function setSetting ($key, $value)
    {
        return DB::table($this->config->get('tables.SETTINGS'))
                        ->where('setting_key', $key)
                        ->update(array('setting_value'=>$value));
    }

    public function generateRefferalCode ()
    {

        return strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 6));
        //return rand(100000, 999999);
        $length = 5;
        $type = $this->config->get('constants.REFCODE_CHARSET.'.$this->siteConfig->refcode_charset);
//$usrRecCnt = DB::table($this->config->get('tables.ACCOUNT_SETTINGS'))->selectRaw('MAX(LENGTH(referral_code)) as ref_lenexist'),DB::Raw('MAX(account_id) as idLength')->first();
        $usrRecCnt = (object) ['ref_lenexist'=>9, 'idLength'=>124585];
        echo '<pre>';
        if (!empty($usrRecCnt))
        {
            $preLength = str_pad(1, $length, 0);
            echo $preLength.'<br>';
            echo $usrRecCnt->idLength.'<br>';
            if ($preLength < $usrRecCnt->idLength)
            {
                echo strlen($usrRecCnt->ref_lenexist).'<br>';
                $preLength = str_pad(1, strlen($usrRecCnt->ref_lenexist) + 1, 0);
                echo $preLength.'<pre>';
            }
            exit;
            return $usrRecCnt;
        }
        return NULL;
    }
	
	public function getTax (array $arr = array(), $for_checking = false)
    {		
		$amount = 0;
        $total_tax_amount = 0;
        $state_id = null;
        extract($arr);
		$tax = DB::table($this->config->get('tables.STATEMENT_LINE').' as st')
                ->join($this->config->get('tables.TAX_CLASSES').' as tc', function($tc)
                {
                    $tc->on('tc.tax_type', '=', 'st.tax_type')
                    ->where('tc.is_deleted', '=', $this->config->get('constants.OFF'));
                })
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
                ->where('st.statementline_id', $statementline_id)
                ->selectRaw('t.tax_id,CONCAT_WS(\'-\',tc.tax_class,t.tax) as tax,t.tax_value,t.settings');		
        
        if (isset($currency_id) && !empty($currency_id))
        {
            $tax->where('t.currency_id', $currency_id);			
        }
        if (isset($geo_zone_id) && !empty($geo_zone_id))
        {
            $tax->where('t.geo_zone_id', $geo_zone_id);
        }
        elseif (isset($country_id) && !empty($country_id))
        {
            $tax->join($this->config->get('tables.GEO_ZONE_LOCATIONS').' as z', function($t) use($country_id, $state_id)
            {
                $t->on('z.geo_zone_id', '=', 't.geo_zone_id')
                        ->where('z.country_id', '=', $country_id);
				if (!empty($state_id))
                {
                    $t->nest(function($t1) use($state_id)
                    {
                        $t1->whereNUll('z.state_id')->orWhere('z.state_id', '=', $state_id);
                    });
                }  
            });            
        }
        $taxes = $tax->get();
        foreach ($taxes as &$tax)
        {
            $tax->tax_amt = ($amount * $tax->tax_value) / 100;
            $tax->settings = json_decode($tax->settings);
            if (!empty($tax->settings))
            {
                if (isset($tax->settings->state_id))
                {
                    if (!(($tax->settings->state_id->operator == '!=' && $tax->settings->state_id->value != $state_id) || ($tax->settings->state_id->operator == '==' && $tax->settings->state_id->value == $state_id)))
                    {
                        $tax->tax_amt = 0;
                    }
                }
                if (isset($tax->settings->earnings))
                {
                    $tax_payments = DB::table($this->config->get('tables.ACCOUNT_TAX_PAYMENTS'))
                            ->where('account_id', $account_id)
                            ->where('tax_id', $tax->tax_id)
                            ->first();							
                    if (empty($tax_payments))
                    {
						
                        DB::table($this->config->get('tables.ACCOUNT_TAX_PAYMENTS'))
                                ->insertGetID([
                                    'account_id'=>$account_id,
                                    'tax_id'=>$tax->tax_id,
                                    'updated_on'=>getGTZ()
                        ]);
                        $tax_payments = DB::table($this->config->get('tables.ACCOUNT_TAX_PAYMENTS'))
                                ->where('account_id', $account_id)
                                ->where('tax_id', $tax->tax_id)
                                ->first();
                    }
                    if (!empty($tax_payments))
                    {
                        if ($tax->settings->earnings->operator == '<=' && $tax->settings->earnings->value <= $tax_payments->paid_for)
                        {
                            if (!$for_checking)
                            {
                                DB::table($this->config->get('tables.ACCOUNT_TAX_PAYMENTS'))
                                        ->where('account_id', $account_id)
                                        ->where('tax_id', $tax->tax_id)
                                        ->increment('paid_for', $amount, ['updated_on'=>getGTZ()]);
                            }
                        }
                        elseif ($tax->settings->earnings->operator == '<=' && $tax->settings->earnings->value <= $tax_payments->pending_for + $amount)
                        {
                            if (!$for_checking)
                            {
                                $pending_tax = $tax_payments->pending_for * $tax->tax_value / 100;
                                if ($this->updateAccountTransaction([
                                            'currency_id'=>$currency_id,
                                            'from_account_id'=>$account_id,
                                            'amt'=>$tax_payments->pending_for,
                                            'from_taxes'=>[
                                                ['amt'=>$pending_tax, 'tax_id'=>$tax->tax_id],
                                            ],
                                            'debit_remark_data'=>['tax'=>$tax->tax, 'percentage'=>$tax->tax_value],
                                            'transaction_for'=>'TAX_DEDUCTION',
                                        ]))
                                {
                                    DB::table($this->config->get('tables.ACCOUNT_TAX_PAYMENTS'))
                                            ->where('account_id', $account_id)
                                            ->where('tax_id', $tax->tax_id)
                                            ->update(['pending_for'=>0, 'paid_for'=>$tax_payments->pending_for + $amount, 'updated_on'=>getGTZ()]);
                                }
                            }
                        }
                        else
                        {
                            if (!$for_checking)
                            {
                                DB::table($this->config->get('tables.ACCOUNT_TAX_PAYMENTS'))
                                        ->where('account_id', $account_id)
                                        ->where('tax_id', $tax->tax_id)
                                        ->increment('pending_for', $amount, ['updated_on'=>getGTZ()]);
                            }
                            $tax->tax_amt = 0;
                        }
                    }
                }
            }
            if ($tax->tax_amt > 0)
            {
                $total_tax_amount += $tax->tax_amt;
            }
            else
            {
                $tax = null;
            }
            unset($tax->settings);
        }
        return [$total_tax_amount, array_filter($taxes)];
    }    

    public function get_currency_rate ($from_currency, $to_currency)
    {
        return $from_currency == $to_currency ? 1 : DB::table($this->config->get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                        ->where('from_currency_id', $from_currency)
                        ->where('to_currency_id', $to_currency)
                        ->value('rate');
    }

}
