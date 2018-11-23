<?php

namespace App\Models\Admin;
use App\Models\BaseModel;
use DB;
use CommonLib;
use Log;
use Request;
class AdminFinance extends BaseModel
{

    public function __construct ()
    {
        parent::__construct();
        $this->memberObj = new Member();
        $this->baseobj = new BaseModel();
    }

    public function get_wallets ($wallet_id = '')
    {
        return DB::table($this->config->get('tables.WALLET').' as w')
                        ->join($this->config->get('tables.WALLET_LANG').' as wl', 'wl.wallet_id', '=', 'w.wallet_id')
                        ->where('w.status', 1)
                        ->where('w.fundtransfer_status', $this->config->get('constants.ACTIVE'))
                        ->where('w.fund_deduct_status', $this->config->get('constants.ACTIVE'))
                        ->lists('wl.wallet', 'w.wallet_id');
    }

    public function add_fund_merchant (array $arr = array())
    {
        $merchant = $this->get_merchant_details($arr);
        $bal = $this->get_account_bal($arr);
        if (($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.DEBIT')) && ($arr['amount'] > $bal->current_balance))
        {
            return false;
        }
        if (!empty($merchant))
        {
            $mr_accId = $merchant->account_id;
            $fund['transaction_id'] = $this->generateTransactionID();
            $fund['currency_id'] = $arr['currency_id'];
            $fund['amount'] = $arr['amount'];
            $fund['paidamt'] = $arr['amount'];
            $fund['handleamt'] = 0;
            if ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $mr_accId;
            }
            else
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.DEBIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $mr_accId;
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
            }
            $fund['created_on'] = getGTZ();
            $fund['transfered_on'] = getGTZ();
            $fund['added_by'] = $arr['admin_id'];
            $fund['status'] = $this->config->get('constants.ON');
            $fund_id = DB::table($this->config->get('tables.FUND_TRANASFER'))
                    ->insertGetId($fund);
            if (!empty($fund_id))
            {
                if ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
                {
                    $update_trans = $this->baseobj->updateAccountTransaction(['to_account_id'=>$mr_accId, 'relation_id'=>$fund_id, 'to_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], false, true);
                }
                elseif ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.DEBIT'))
                {
                    $update_trans = $this->baseobj->updateAccountTransaction(['from_account_id'=>$mr_accId, 'relation_id'=>$fund_id, 'from_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], true, false);
                }
                if (!empty($update_trans))
                {
                    if ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
                    {
                        $msg = trans('admin/finance.fund_transfer_success');
                    }
                    else
                    {
                        $msg = trans('admin/finance.fund_transfer_debit_success');
                    }
                }
                else
                {
                    return false;
                }
                return $msg;
            }
        }
        else
        {
            return 'Merchant Not found';
        }
    }

    public function fund_transfer_settings ()
    {
        return DB::table($this->config->get('tables.FUND_TRANASFER_SETTINGS'))
                        ->where('transfer_type', 0)
                        ->first();
    }

    public function add_fund_member (array $arr = array())
    {
		
        $user_details = $this->memberObj->get_member_details($arr);
        $bal = $this->get_account_bal($arr);

        if (($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.DEBIT')) && (!empty($bal)) && ($arr['amount'] > $bal->current_balance))
        {
            return false;
        }
        if (!empty($user_details))
        {
            $accId = $user_details->account_id;
            $fund['added_by'] = $arr['admin_id'];
            $fund['transaction_id'] = $this->generateTransactionID();
            $fund['currency_id'] = $arr['currency_id'];
            $fund['amount'] = $arr['amount'];
            $fund['paidamt'] = $arr['amount'];
            $fund['handleamt'] = 0;
			
            if ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');
             //$fund['from_user_ewallet_id'] = $arr['wallet'];
                $fund['from_user_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
                $fund['to_user_ewallet_id'] = $arr['wallet'];
                $fund['to_user_id'] = $accId;
            }
            else
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.DEBIT');
                $fund['from_user_ewallet_id'] = $arr['wallet'];
                $fund['from_user_id'] = $accId;
              //$fund['to_user_ewallet_id'] = $arr['wallet'];
                $fund['to_user_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
            }
			$fund['ip_address']=Request::getClientIp(true);
			$fund['timeflag']=getGTZ();
            $fund['transfered_on'] = getGTZ();
            $fund['status'] = $this->config->get('constants.ON');
            $fund_id = DB::table($this->config->get('tables.FUND_TRANASFER'))
                    ->insertGetId($fund);
            if (!empty($fund_id))
            {
                if ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
                {
                    $update_trans = $this->baseobj->updateAccountTransaction(['to_account_id'=>$accId, 'relation_id'=>$fund_id, 'to_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], false, true);
                }
                elseif ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.DEBIT'))
                {
                    $update_trans = $this->baseobj->updateAccountTransaction(['from_account_id'=>$accId, 'relation_id'=>$fund_id, 'from_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM','debit_remark_data'=>['amount'=>$arr['amount']]], true, false);
                }
                if (!empty($update_trans))
                {
                    if ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
                    {
                        $msg = trans('admin/finance.fund_transfer_success');
                    }
                    else
                    {
                        $msg = trans('admin/finance.fund_transfer_debit_success');
                    }
                }
                else
                {
                    return false;
                }
                return $msg;
            }
        }
        else
        {
            return 'Merchant Not found';
        }
    }

    public function add_fund_dsa (array $arr = array())
    {
        $user_details = $this->get_dsa_details($arr);
        $bal = $this->get_account_bal($arr);
        if (($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.DEBIT')) && ($arr['amount'] > $bal->current_balance))
        {
            return false;
        }
        if (!empty($user_details))
        {
            $accId = $user_details->account_id;
            $fund['added_by'] = $arr['admin_id'];
            $fund['transaction_id'] = $this->generateTransactionID();
            $fund['currency_id'] = $arr['currency_id'];
            $fund['amount'] = $arr['amount'];
            $fund['paidamt'] = $arr['amount'];
            $fund['handleamt'] = 0;
            if ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.CREDIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $accId;
            }
            else
            {
                $trasn_type = $this->config->get('constants.TRANSACTION_TYPE.DEBIT');
                $fund['from_account_ewallet_id'] = $arr['wallet'];
                $fund['from_account_id'] = $accId;
                $fund['to_account_ewallet_id'] = $arr['wallet'];
                $fund['to_account_id'] = $this->config->get('constants.ACCOUNT.ADMIN_ID');
            }
            $fund['created_on'] = getGTZ();
            $fund['transfered_on'] = getGTZ();
            $fund['status'] = $this->config->get('constants.ON');
            $fund_id = DB::table($this->config->get('tables.FUND_TRANASFER'))
                    ->insertGetId($fund);
            if (!empty($fund_id))
            {
                if ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
                {
                    $update_trans = $this->baseobj->updateAccountTransaction(['to_account_id'=>$accId, 'relation_id'=>$fund_id, 'to_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], false, true);
                }
                elseif ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.DEBIT'))
                {
                    $update_trans = $this->baseobj->updateAccountTransaction(['from_account_id'=>$accId, 'relation_id'=>$fund_id, 'from_wallet_id'=>$arr['wallet'], 'currency_id'=>$arr['currency_id'], 'amt'=>$arr['amount'], 'transaction_for'=>'FUND_TRANS_BY_SYSTEM'], true, false);
                }
                if (!empty($update_trans))
                {
                    if ($arr['type'] == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
                    {
                        $msg = trans('admin/finance.fund_transfer_success');
                    }
                    else
                    {
                        $msg = trans('admin/finance.fund_transfer_debit_success');
                    }
                }
                else
                {
                    return false;
                }
                return $msg;
            }
        }
        else
        {
            return 'Merchant Not found';
        }
    }

    public function withdrawals_list (array $data = array(), $count = false)
    {
        extract($data);
        $query = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as wdm')
                ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'wdm.account_id')
                ->join($this->config->get('tables.WALLET').' as wt', 'wt.wallet_id', '=', 'wdm.wallet_id')
                ->join($this->config->get('tables.WALLET_LANG').' as wtl', function($subquery)
                {
                    $subquery->on('wtl.wallet_id', '=', 'wt.wallet_id')
                    ->where('wtl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->where('wdm.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'wdm.currency_id')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'wdm.payment_type_id')
                ->join($this->config->get('tables.PAYMENT_TYPES_LANG').' as ptl', function($subquery)
                {
                    $subquery->on('ptl.payment_type_id', '=', 'pt.payment_type_id')
                    ->where('ptl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'wdm.account_id')
                ->join($this->config->get('tables.ADDRESS_MST').' as adm', function($subquery)
                {
                    $subquery->on('adm.relative_post_id', '=', 'wdm.account_id')
                    ->where('adm.post_type', '=', $this->config->get('constants.POST_TYPE.ACCOUNT'));
                })
                ->join($this->config->get('tables.LOCATION_COUNTRIES').' as loc', 'loc.country_id', '=', 'adm.country_id')
                ->where('wdm.status_id', '=', $this->config->get('constants.WITHDRAWAL_STATUS.PENDING'))
                ->selectRaw('wdm.wd_id,wdm.payment_type_id,wdm.currency_id,wdm.wallet_id,wdm.payment_details,wdm.amount,wdm.paidamt,wdm.handleamt,wdm.expected_on,wdm.created_on,wdm.status_id,wdm.payment_status,am.uname,CONCAT_WS(\' \',ad.first_name,ad.last_name) as full_name,adm.country_id,loc.country,ptl.payment_type,cur.currency,cur.currency_symbol');
        if (!empty($from) && isset($from))
        {
            $query->whereDate('wdm.created_on', '<=', getGTZ($from, 'Y-m-d'));
        }
        if (!empty($to) && isset($to))
        {
            $query->whereDate('wdm.created_on', '>=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_term) && !empty($search_term))
        {
            if (!empty($filterTerms) && !empty($filterTerms))
            {
                $search_term = '%'.$search_term.'%';
                $search_field = ['UserName'=>'am.uname', 'FullName'=>'concat_ws(\' \',ad.first_name,ad.last_name)'];
                $query->where(function($sub) use($filterTerms, $search_term, $search_field)
                {
                    foreach ($filterTerms as $search)
                    {
                        if (array_key_exists($search, $search_field))
                        {
                            $sub->orWhere(DB::raw($search_field[$search]), 'like', $search_term);
                        }
                    }
                });
            }
            $query->where(function($wcond) use($search_term)
            {
                $wcond->Where('am.uname', 'like', $search_term)
                        ->orwhere('ad.first_name', 'like', $search_term)
                        ->orwhere('ad.last_name', 'like', $search_term);
            });
        }
        if (isset($orderby) && isset($order))
        {
            if ($orderby == 'created_on')
            {
                $query->orderBy('wdm.created_on', $order);
            }
            elseif ($orderby == 'uname')
            {
                $query->orderBy('wdm.uname', $order);
            }
            elseif ($orderby == 'payment_type')
            {
                $query->orderBy('ptl.payment_type', $order);
            }
            elseif ($orderby == 'country')
            {
                $query->orderBy('loc.country', $order);
            }
            elseif ($orderby == 'amount')
            {
                $query->orderBy('wdm.amount', $order);
            }
            elseif ($orderby == 'currency')
            {
                $query->orderBy('cur.currency', $order);
            }
            elseif ($orderby == 'paidamt')
            {
                $query->orderBy('wdm.paidamt', $order);
            }
            elseif ($orderby == 'handleamt')
            {
                $query->orderBy('adm.handleamt', $order);
            }
        }
        if (isset($currency) && !empty($currency))
        {
            $query->where('loc.currency', $currency);
        }
        if (isset($payment_type) && !empty($payment_type))
        {
            $query->where('ptl.payment_type', $payment_type);
        }
        if (isset($start) && isset($length))
        {
            $query->skip($start)->take($length);
        }
        if (isset($count) && !empty($count))
        {
            return $query->count();
        }
        else
        {
            $result = $query->orderBy('wdm.created_on', 'DESC')
                    ->get();
            if (!empty($result))
            {
                return $result;
            }
        }
        return null;
    }

    public function fund_transfer_history (array $arr = array(), $count = false)
    {
        extract($arr);
        $qry = DB::Table($this->config->get('tables.FUND_TRANASFER').' as ft')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as accf', 'accf.account_id', '=', 'ft.from_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'accf.account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as acct', 'acct.account_id', '=', 'ft.to_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as accd', 'accd.account_id', '=', 'acct.account_id')
                ->leftjoin($this->config->get('tables.WALLET').' as fw', 'fw.wallet_id', '=', 'ft.from_account_ewallet_id')
                ->leftjoin($this->config->get('tables.WALLET').' as tw', 'tw.wallet_id', '=', 'ft.to_account_ewallet_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 'ft.currency_id')
                /* ->join($this->config->get('tables.ACCOUNT_MST').' as acu', 'acu.account_id', '=', 'ft.from_account_id')
                  ->join($this->config->get('tables.ACCOUNT_MST').' as act', 'act.account_id', '=', 'ft.to_account_id') */
                ->join($this->config->get('tables.SYSTEM_ROLES').' as sf', 'sf.system_role_id', '=', 'accf.system_role_id')
                ->join($this->config->get('tables.SYSTEM_ROLES').' as st', 'st.system_role_id', '=', 'acct.system_role_id')
                ->where('ft.added_by', '!=', $this->config->get('constants.ACCOUNT.ADMIN_ID'))
                ->where('ft.is_deleted', 0)
                ->select('ft.ft_id', 'ft.transaction_id', 'ft.from_account_id', 'ft.to_account_id', 'ft.from_account_ewallet_id as from_wallet_id', 'ft.to_account_ewallet_id as to_wallet_id', 'ft.currency_id', 'ft.amount', 'ft.handleamt', 'ft.paidamt', 'ft.created_on', DB::raw('CONCAT_WS(\' \',acd.first_name,acd.last_name) as trans_from'), DB::raw('CONCAT_WS(\' \',accd.first_name,accd.last_name) as trans_to'), 'tw.wallet_code as wallet_name', 'ft.status as status_id', 'c.code', 'acct.uname', 'sf.system_role_name as from_acc_roll', 'st.system_role_name as to_acc_roll', 'accf.uname as funame', 'acct.uname as tuname');
        if (isset($arr['skip']) && !empty($arr['skip']))
        {
            $qry->skip($arr['skip'])
                    ->take($arr['length']);
        }
        if (isset($from) && !empty($from))
        {
            $qry->whereDate('ft.created_on', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $qry->whereDate('ft.created_on', '<=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($sysrole) && ($sysrole != ''))
        {
            $qry->where('sf.system_role_id', $sysrole);
        }
        if (isset($terms) && !empty($terms))
        {
            if (is_numeric($terms))
            {
                $qry->where('ft.transaction_id', $terms);
            }
            else
            {
                $qry->where(DB::Raw('CONCAT_WS(\' \',acd.first_name,acd.last_name)'), 'like', $terms)
                        ->Orwhere(DB::Raw('CONCAT_WS(\' \',accd.first_name,accd.last_name)'), 'like', $terms);
            }
        }
        $qry->orderBy('ft.created_on', 'desc');
        $qry->orderBy('ft.ft_id', 'desc');
        $result = $qry->get();
        if ($count)
        {
            return $qry->count();
        }
        if (!empty($result))
        {
            array_walk($result, function($data)
            {
                /*  if ($data->transfer_type == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
                  {
                  $data->trans_type = 'Credit';
                  if ($data->from_account_id == 0)
                  {
                  $data->trans_from = 'SYSTEM';
                  }
                  }
                  else
                  {
                  $data->trans_type = 'Debit';
                  } */
                $data->statusCls = config('dispclass.fund_trasnfer.status.'.$data->status_id);
                $data->status = trans('db_trans.fund_trasnfer.status.'.$data->status_id);
                $data->created_on = showUTZ($data->created_on, 'Y-m-d H:i:s');
                $data->amount = $data->amount.' '.$data->code;
                $data->handleamt = $data->handleamt.' '.$data->code;
                $data->paidamt = $data->paidamt.' '.$data->code;
            });
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function admin_fund_transfer_history (array $arr = array(), $count = false)
    {
        extract($arr);
        $qry = DB::Table($this->config->get('tables.FUND_TRANASFER').' as ft')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as accf', 'accf.account_id', '=', 'ft.from_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'accf.account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as acct', 'acct.account_id', '=', 'ft.to_account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as accd', 'accd.account_id', '=', 'acct.account_id')
                ->leftjoin($this->config->get('tables.WALLET').' as fw', 'fw.wallet_id', '=', 'ft.from_account_ewallet_id')
                ->leftjoin($this->config->get('tables.WALLET').' as tw', 'tw.wallet_id', '=', 'ft.to_account_ewallet_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 'ft.currency_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as adb', 'adb.account_id', '=', 'ft.added_by')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acu', 'acu.account_id', '=', 'ft.from_account_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as act', 'act.account_id', '=', 'ft.to_account_id')
                ->join($this->config->get('tables.SYSTEM_ROLES').' as syt', 'syt.system_role_id', '=', 'adb.system_role_id')
                ->join($this->config->get('tables.SYSTEM_ROLES').' as sf', 'sf.system_role_id', '=', 'acu.system_role_id')
                ->join($this->config->get('tables.SYSTEM_ROLES').' as st', 'syt.system_role_id', '=', 'act.system_role_id')
                ->where('adb.system_role_id', 0)
                ->select('ft.ft_id', 'ft.transaction_id', 'ft.from_account_id', 'ft.to_account_id', 'ft.from_account_ewallet_id as from_wallet_id', 'ft.to_account_ewallet_id as to_wallet_id', 'ft.currency_id', 'ft.amount', 'ft.handleamt', 'ft.paidamt', 'ft.created_on', DB::raw('CONCAT_WS(\' \',acd.first_name,acd.last_name) as trans_from'), DB::raw('CONCAT_WS(\' \',accd.first_name,accd.last_name) as trans_to'), DB::raw('IF(ft.from_account_ewallet_id > 0,fw.wallet_code,tw.wallet_code) as wallet_name'), 'adb.uname as added_by', 'syt.system_role_name as added_by_role', 'ft.status as status_id', 'c.code', 'acct.uname', 'sf.system_role_name as from_acc_roll', 'st.system_role_name as to_acc_roll', 'acu.uname as funame', 'act.uname as tuname');
        if (isset($arr['skip']) && !empty($arr['skip']))
        {
            $qry->skip($arr['skip'])
                    ->take($arr['length']);
        }
        if (isset($from) && !empty($from))
        {
            $qry->whereDate('ft.created_on', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $qry->whereDate('ft.created_on', '<=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($sysrole) && ($sysrole != ''))
        {
            $qry->where('sf.system_role_id', $sysrole);
        }
        if (isset($terms) && !empty($terms))
        {
            if (is_numeric($terms))
            {
                $qry->where('ft.transaction_id', $terms);
            }
            else
            {
                $qry->where(DB::Raw('CONCAT_WS(\' \',acd.first_name,acd.last_name)'), 'like', $terms)
                        ->Orwhere(DB::Raw('CONCAT_WS(\' \',accd.first_name,accd.last_name)'), 'like', $terms);
            }
        }
        $qry->orderBy('ft.created_on', 'desc');
        $qry->orderBy('ft.ft_id', 'desc');
        $result = $qry->get();
        if ($count)
        {
            return $qry->count();
        }
        if (!empty($result))
        {
            array_walk($result, function($data)
            {
                /*  if ($data->transfer_type == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
                  {
                  $data->trans_type = 'Credit';
                  if ($data->from_account_id == 0)
                  {
                  $data->trans_from = 'SYSTEM';
                  }
                  }
                  else
                  {
                  $data->trans_type = 'Debit';
                  } */
                $data->statusCls = config('dispclass.fund_trasnfer.status.'.$data->status_id);
                $data->status = trans('db_trans.fund_trasnfer.status.'.$data->status_id);
                $data->created_on = showUTZ($data->created_on, 'Y-m-d H:i:s');
                $data->amount = $data->amount.' '.$data->code;
                $data->handleamt = $data->handleamt.' '.$data->code;
                $data->paidamt = $data->paidamt.' '.$data->code;
            });
            return $result;
        }
        else
        {
            return false;
        }
    }

    public function transaction_log (array $arr = array(), $count = false)
    {
        extract($arr);
        $qry = DB::Table($this->config->get('tables.ACCOUNT_TRANSACTION').' as at')
                ->join($this->config->get('tables.ACCOUNT_MST').' as am', function($am) use($system_role_id)
                {
                    $am->on('am.account_id', '=', 'at.account_id')
                    ->where('am.system_role_id', '=', $system_role_id);
                })
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'at.account_id')
                ->leftjoin($this->config->get('tables.WALLET_LANG').' as wl', function($join)
                {
                    $join->on('wl.wallet_id', '=', 'at.wallet_id')
                    ->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.PAYMENT_TYPES_LANG').' as pty', 'pty.payment_type_id', '=', 'at.payment_type_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 'at.currency_id')
                ->leftjoin($this->config->get('tables.STATEMENTLINES_LANG').' as sl', function($join)
                {
                    $join->on('sl.statementline_id', '=', 'at.statementline');
                    $join->where('sl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->select('at.transaction_id', 'sl.statementline_id', 'pty.payment_type as payment_type_name', 'sl.statementline_id', 'at.transaction_type as trans_type', 'wl.wallet', 'at.currency_id', 'at.amount', 'at.current_balance', 'at.remark', 'sl.statementline', 'at.created_on', DB::raw('CONCAT_WS(\' \',acd.first_name,acd.last_name) as fullname'), 'at.status', 'c.currency_symbol', 'c.code as currency_code', 'c.decimal_places');
        if (isset($account_id) && !empty($account_id))
        {
            $qry->where('at.account_id', $account_id);
        }
        if (isset($from) && !empty($from))
        {
            $qry->whereDate('at.created_on', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $qry->whereDate('at.created_on', '<=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($terms) && !empty($terms))
        {
            if (is_numeric($terms))
            {
                $qry->where('at.transaction_id', $terms);
            }
            else
            {
                $qry->where(DB::Raw('CONCAT_WS(\' \',acd.first_name,acd.last_name)'), 'like', $terms);
            }
        }
        if ($count)
        {
            return $qry->count();
        }
        else
        {
            if (isset($start))
            {
                $qry->skip($start)->take($length);
            }
            $qry->orderBy('at.created_on', 'desc');
            $qry->orderBy('at.id', 'desc');
            $result = $qry->get();
            array_walk($result, function($data) use($system_role_id)
            {
                if (!empty($data->remark))
                {
                    $data->remark = json_decode($data->remark);
                    $data->remark = trans('transactions.'.$data->statementline_id.'.remarks', (array) $data->remark->data);
                }
                if ($data->trans_type == $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT'))
                {
                    $data->trans_type = trans('general.credit');
                }
                else
                {
                    $data->trans_type = trans('general.debit');
                    $data->amount = ($data->amount * -1);
                    $data->current_balance = ($data->current_balance * -1);
                }
                $data->statusCls 	= config('dispclass.transaction.status.'.$data->status);
                $data->status 		= trans('db_trans.transaction.status.'.$data->status);
                $data->created_on 	= showUTZ($data->created_on, 'Y-m-d H:i:s');
                $data->famount 		= CommonLib::currency_format($data->amount, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->currency_code,'decimal_places'=>$data->decimal_places], true, true);
                $data->fcurrent_balance = CommonLib::currency_format($data->current_balance, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->currency_code, 'decimal_places'=>$data->decimal_places], true, true);
                $data->actions = [];
                if ($system_role_id == $this->config->get('constants.ACCOUNT.TYPE.USER'))
                {
                    $data->actions[] = ['url'=>route('admin.member.transaction-details', ['id'=>$data->transaction_id]), 'label'=>trans('general.btn.details')];
                }
                else
                {
                    $data->actions[] = ['url'=>route('admin.retailers.transaction-details', ['id'=>$data->transaction_id]), 'label'=>trans('general.btn.details')];
                }
                unset($data->currency_symbol);
                unset($data->currency_code);
                unset($data->decimal_places);
            });
            return $result;
        }
    }

    public function admin_credit_debit_history (array $arr = array(), $count = false)
    {

        extract($arr);
        $qry = DB::Table($this->config->get('tables.FUND_TRANASFER').' as ft')
                ->join($this->config->get('tables.ACCOUNT_MST').' as accf', 'accf.account_id', '=', 'ft.from_user_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acct', 'acct.account_id', '=', 'ft.to_user_id')
                ->where(function($c)
                {
                    $c->whereRaw('accf.account_type_id = '.$this->config->get('constants.ACCOUNT_TYPE.ADMIN').' OR acct.account_type_id='.$this->config->get('constants.ACCOUNT_TYPE.ADMIN'));
                })
                ->where('ft.is_deleted', $this->config->get('constants.OFF'));

        if (isset($trans_type))
        {
            if (!empty($trans_type))
            {
                $qry->where('ft.from_user_id', $this->config->get('constants.ACCOUNT.ADMIN_ID'));
            }
        }
        if (isset($from) && !empty($from))
        {
            $qry->whereDate('ft.transfered_on', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $qry->whereDate('ft.transfered_on', '<=', getGTZ($to, 'Y-m-d'));
        }
     if (isset($terms) && !empty($terms))
        {
		
            $terms = '%'.$terms.'%';
           
                $qry->where('ft.transaction_id', 'like', $terms);
            
           /*  else
            {
                $qry->where(DB::Raw('concat_ws(\' \',acd.firstname,acd.lastname)'), 'like', $terms)
                        ->Orwhere(DB::Raw('concat_ws(\' \',accd.firstname,accd.lastname)'), 'like', $terms);
            } */
        }
        if ($count)
        {
            return $qry->count();
        }
        if (isset($start))
        {
            $qry->skip($start)
                    ->take($length);
        }
      
        $qry->orderBy('ft.ft_id', 'desc');

        $result = $qry->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'accf.account_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_DETAILS').' as accd', 'accd.account_id', '=', 'acct.account_id')
                ->leftjoin($this->config->get('tables.WALLET').' as fw', 'fw.wallet_id', '=', 'ft.from_user_ewallet_id')
                ->leftjoin($this->config->get('tables.WALLET').' as tw', 'tw.wallet_id', '=', 'ft.to_user_ewallet_id')
				->leftjoin($this->config->get('tables.WALLET_LANG').' as twl', 'twl.wallet_id', '=', 'tw.wallet_id')
				->leftjoin($this->config->get('tables.WALLET_LANG').' as fwl', 'fwl.wallet_id', '=', 'fw.wallet_id')
                ->leftjoin($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'ft.currency_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_MST').' as adb', 'adb.account_id', '=', 'ft.added_by')
                ->leftjoin($this->config->get('tables.ACCOUNT_TYPES').' as syt', 'syt.id', '=', 'adb.account_type_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_TYPES').' as syfu', 'syfu.id', '=', 'accf.account_type_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_TYPES').' as sytu', 'sytu.id', '=', 'acct.account_type_id')
                ->select('ft.ft_id', 'ft.transaction_id', 'ft.from_user_id', 'ft.to_user_id', 'ft.from_user_ewallet_id as from_wallet_id', 'ft.to_user_ewallet_id as to_wallet_id', 'ft.currency_id', 'ft.amount', 'ft.handleamt', 'ft.paidamt', 'ft.transfered_on', DB::raw('CONCAT_WS(\' \',acd.firstname,acd.lastname) as trans_from'), DB::raw('CONCAT_WS(\' \',accd.firstname,accd.lastname) as trans_to'), DB::raw('IF(ft.from_user_ewallet_id > 0,fwl.wallet,twl.wallet) as wallet_name'), 'adb.uname as added_by', 'syt.account_type_name as added_by_role', 'ft.status as status_id', 'c.currency as code', 'c.currency_symbol', 'c.decimal_places', 'accf.uname as funame', 'acct.uname as tuname','syfu.account_type_name as from_acc_role','sytu.account_type_name as to_acc_role')
                ->get();
      
        array_walk($result, function(&$data)
        {
            if ($data->from_user_id == $this->config->get('constants.ACCOUNT.ADMIN_ID'))
            {
                $trans_type = '+';
                $data->username = $data->trans_to.'('.$data->tuname.')<br><span class="text-muted small">'.$data->to_acc_role;
            }
            else
            {
                $trans_type = '-';
                $data->username = $data->trans_from.'('.$data->funame.')<br><span class="text-muted small">'.$data->from_acc_role;
            }
            $data->added_by = $data->added_by.'<br><span class="text-muted small">'.$data->added_by_role.'</span>';
            $data->statusCls = config('dispclass.fund_trasnfer.status.'.$data->status_id);
            $data->status = trans('admin/finance.fund_trasnfer.status.'.$data->status_id);
            $data->transfered_on = showUTZ($data->transfered_on, 'Y-m-d H:i:s');
            $data->amount = CommonLib::currency_format($data->amount, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->code, 'decimal_places'=>$data->decimal_places], true, true);
            $data->handleamt = CommonLib::currency_format($data->handleamt, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->code, 'decimal_places'=>$data->decimal_places], true, true);
            $data->paidamt = $trans_type.' '.CommonLib::currency_format($data->paidamt, ['currency_symbol'=>$data->currency_symbol, 'currency_code'=>$data->code, 'decimal_places'=>$data->decimal_places], true, true);
        });
	//print_r($result); die;
        return $result;
    }

    public function getTransactionDetail (array $arr = array())
    {
        extract($arr);
        $trans = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION').' as a')
                ->leftJoin($this->config->get('tables.ACCOUNT_MST').' as ra', 'ra.account_id', '=', 'a.account_id')
                ->leftJoin($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'a.account_id')
                ->join($this->config->get('tables.PAYMENT_TYPES_LANG').' as ptl', function($ptl)
                {
                    $ptl->on('ptl.payment_type_id', '=', 'a.payment_type_id')
                    ->where('ptl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.WALLET').' as w', 'w.wallet_id', '=', 'a.wallet_id')
                ->join($this->config->get('tables.WALLET_LANG').' as wt', function($join)
                {
                    $join->on('wt.wallet_id', '=', 'a.wallet_id');
                    $join->where('wt.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.STATEMENTLINES_LANG').' as e', function($join)
                {
                    $join->on('e.statementline_id', '=', 'a.statementline');
                    $join->where('e.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'a.currency_id')
                ->where('a.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->where('a.status', '>', 0)
                ->where('a.transaction_id', $id)
                ->selectRaw('a.account_id,a.transaction_id,ra.uname,CONCAT(ad.first_name,\' \',ad.last_name) as full_name,a.created_on,w.wallet_code,a.remark,a.post_type,a.statementline as statementline_id,a.relation_id,a.transaction_type,a.transaction_id,cur.currency,cur.currency_symbol,cur.decimal_places,a.amount,a.tax,a.handleamt,a.paidamt,a.current_balance,e.statementline,wt.wallet,ptl.payment_type,a.status')
                ->first();
        if (!empty($trans))
        {
            $trans->created_on = showUTZ($trans->created_on);
            $trans->status = trans('general.transactions.status.'.$trans->status);
            $trans->amount = CommonLib::currency_format($trans->amount, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>($trans->transaction_type == 1 ? '+' : '-'), 'decimal_places'=>$trans->decimal_places], true, true);
            $trans->tax = CommonLib::currency_format($trans->tax, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>($trans->transaction_type == 1 ? '+' : '-'), 'decimal_places'=>$trans->decimal_places], true, true);
            $trans->handleamt = CommonLib::currency_format($trans->handleamt, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>($trans->transaction_type == 1 ? '+' : '-'), 'decimal_places'=>$trans->decimal_places], true, true);
            $trans->paidamt = CommonLib::currency_format($trans->paidamt, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>($trans->transaction_type == 1 ? '+' : '-'), 'decimal_places'=>$trans->decimal_places], true, true);
            $trans->current_balance = CommonLib::currency_format($trans->current_balance, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'decimal_places'=>$trans->decimal_places]);
            $trans->remark = json_decode($trans->remark);
            switch ($trans->statementline_id)
            {
                case $this->config->get('stline.SIGN_UP_BONUS.CREDIT'):
                    $details = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
                            ->join($this->config->get('tables.DEVICES').' as d', 'd.device_id', '=', 'am.signup_device')
                            ->join($this->config->get('tables.ACCOUNT_SETTINGS').' as s', 's.account_id', '=', 'am.account_id')
                            ->leftJoin($this->config->get('tables.ACCOUNT_MST').' as ra', 'ra.account_id', '=', 'am.referred_account_id')
                            ->selectRaw('d.device_label,d.icon as device_icon,ra.uname as referrer_uname')
                            ->where('am.acccount_id', '=', $details->account_id)
                            ->first();
                    if ($details)
                    {
                        $trans->device_label = $details->device_label;
                        $trans->device_icon = $details->device_icon;
                        $trans->referrer_uname = !empty($details->referrer_uname) ? $details->referrer_uname : trans('general.label.self');
                    }
                    break;
                case $this->config->get('stline.REFERRAL_BONUS.CREDIT'):
                    $details = DB::table($this->config->get('tables.REFERRAL_EARNINGS').' as re')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as fam', 'fam.account_id', '=', 're.from_account_id')
                            ->join($this->config->get('tables.ACCOUNT_DETAILS').' as fad', 'fad.account_id', '=', 're.from_account_id')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as tam', 'tam.account_id', '=', 're.to_account_id')
                            ->join($this->config->get('tables.ACCOUNT_DETAILS').' as tad', 'tad.account_id', '=', 're.to_account_id')
                            ->join($this->config->get('tables.PROMOTIONAL_OFFERS').' as po', 'po.promo_offer_id', '=', 're.commission_type')
                            ->where('earning_id', $trans->relation_id)
                            ->selectRaw('re.commission_perc,fam.uname as from_user_name,CONCAT(fad.first_name,\' \',fad.last_name) as from_name,tam.uname as to_user_name,CONCAT(tad.first_name,\' \',tad.last_name) as to_name,po.offer_name')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->commission_perc = $details->commission_perc;
                        $trans->from_user_name = $details->from_user_name;
                        $trans->from_name = $details->from_name;
                        $trans->to_user_name = $details->to_user_name;
                        $trans->to_name = $details->to_name;
                        $trans->offer_name = $details->offer_name;
                    }
                    break;
                case $this->config->get('stline.REDEEM.DEBIT'):
                    $details = DB::table($this->config->get('tables.REDEEMS.').' as r')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'r.order_id')
                            ->join($this->config->get('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join($this->config->get('tables.PAY').' as p', 'p.order_id', '=', 'mo.order_id')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mo.approved_by')
                            ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'r.currency_id')
                            ->where('redeem_id', $trans->relation_id)
                            ->selectRaw('r.redeem_amount,mo.bill_amount,p.to_amount as amount_due,ms.store_code,ms.store_name,am.uname as staff_id,mm.mrcode,cur.currency,cur.decimal_places,cur.currency_symbol')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->store_code = $details->store_code;
                        $trans->store_name = $details->store_name;
                        $trans->staff_id = $details->staff_id;
                        $trans->mrcode = $details->mrcode;
                        $trans->bill_amount = CommonLib::currency_format($details->bill_amount, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places], true, true);
                        $trans->redeem_amount = CommonLib::currency_format($details->redeem_amount, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places], true, true);
                        $trans->amount_due = CommonLib::currency_format($details->amount_due, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places], true, true);
                    }
                    break;
                case $this->config->get('stline.CURRENCY_CONVERSION.DEBIT'):
                    break;
                case $this->config->get('stline.CURRENCY_CONVERSION.CREDIT'):
                    break;
                case $this->config->get('stline.FUND_TRANS_BY_SYSTEM.CREDIT'):
                    break;
                case $this->config->get('stline.FUND_TRANS_BY_SYSTEM.DEBIT'):
                    break;

                case $this->config->get('stline.ADD_FUND.CREDIT'):
                    break;
                case $this->config->get('stline.WITHDRAW.DEBIT'):
                    $details = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as wm')
                            ->where('wd_id', $trans->relation_id)
                            ->selectRaw('account_info')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->account_info = json_decode($details->account_info);
                        if (!empty($trans->account_info))
                        {
                            array_walk($trans->account_info, function(&$a, $k)
                            {
                                $a = ['label'=>trans('withdrawal.account_details.'.$k), 'value'=>$a];
                            });
                        }
                    }
                    break;
                case $this->config->get('stline.CASHBACK.CREDIT'):
                    $details = DB::table($this->config->get('tables.CASHBACKS').' as c')
                            ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'c.currency_id')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'c.order_id')
                            ->join($this->config->get('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mo.approved_by')
                            ->where('c.cashback_id', $trans->relation_id)
                            ->selectRaw('c.bill_amt,cur.currency,cur.decimal_places,cur.currency_symbol,mo.order_code,mm.mrcode,ms.store_code,ms.store_name,am.uname as staff_id')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->store_code = $details->store_code;
                        $trans->store_name = $details->store_name;
                        $trans->staff_id = $details->staff_id;
                        $trans->mrcode = $details->mrcode;
                        $trans->order_code = $details->order_code;
                        $trans->bill_amt = CommonLib::currency_format($details->bill_amt, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places]);
                    }
                    break;
                case $this->config->get('stline.ORDER_PAYMENT.DEBIT'):
                    $details = DB::table($this->config->get('tables.PAY').' as p')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                            ->join($this->config->get('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->where('p.pay_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,mm.mrcode,mm.mrbusiness_name,ms.store_code,ms.store_name')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->order_code = $details->order_code;
                        $trans->mrcode = $details->mrcode;
                        $trans->mrbusiness_name = $details->mrbusiness_name;
                        $trans->store_code = $details->store_code;
                        $trans->store_name = $details->store_name;
                    }
                    break;
                case $this->config->get('stline.ORDER_TIP.CREDIT'):
                    $details = DB::table($this->config->get('tables.MERCHANT_ORDERS').' as mo')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                            ->join($this->config->get('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->where('mo.order_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,mm.mrcode,mm.mrbusiness_name')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->order_code = $details->order_code;
                        $trans->mrcode = $details->mrcode;
                        $trans->mrbusiness_name = $details->mrbusiness_name;
                    }
                    break;
                case $this->config->get('stline.ORDER_PAYMENT.CREDIT'):
                    $details = DB::table($this->config->get('tables.PAY').' as p')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                            ->join($this->config->get('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mo.approved_by')
                            ->where('p.pay_id', $trans->relation_id)
                            ->selectRaw('p.status as payment_status,mo.order_code,mm.mrcode,mm.mrbusiness_name,ms.store_code,ms.store_name,am.uname as staff_id')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->order_code = $details->order_code;
                        $trans->mrcode = $details->mrcode;
                        $trans->mrbusiness_name = $details->mrbusiness_name;
                        $trans->store_code = $details->store_code;
                        $trans->store_name = $details->store_name;
                        $trans->staff_id = $details->staff_id;
                        $trans->payment_status = trans('general.order.payment_status.'.$details->payment_status);
                    }
                    break;
                case $this->config->get('stline.ORDER_REFUND.DEBIT'):
                    $details = DB::table($this->config->get('tables.ORDER_REFUND').' as or')
                            ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'or.order_id')
                            ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'mo.currency_id')
                            ->where('or.or_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,or.amount as refund_amount,cur.currency,cur.decimal_places,cur.currency_symbol')
                            ->first();
                    if ($details)
                    {
                        $trans->order_code = $details->order_code;
                        $trans->refund_amount = CommonLib::currency_format($details->refund_amount, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places, true, true]);
                    }
                    break;
                case $this->config->get('stline.ORDER_DEAL_PURCHASE.DEBIT'):
                    $details = DB::table($this->config->get('tables.MERCHANT_ORDERS').' as mo')
                            ->join($this->config->get('tables.ORDER_ITEMS').' as oi', 'oi.order_id', '=', 'mo.order_id')
                            ->leftjoin($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join($this->config->get('tables.PAYBACK_DEALS').' as d', 'd.pb_deal_id', '=', 'oi.pb_deal_id')
                            ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as dc', function($dc)
                            {
                                $dc->on('dc.bcategory_id', '=', 'd.bcategory_id')
                                ->where('dc.lang_id', '=', $this->config->get('app.locale_id'));
                            })
                            ->where('mo.order_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,oi.voucher_code,d.deal_name,dc.bcategory_name,ms.store_name,ms.store_code')
                            ->first();
                    if ($details)
                    {
                        $trans->order_code = $details->order_code;
                        $trans->voucher_code = $details->voucher_code;
                        $trans->deal_name = $details->deal_name;
                        $trans->bcategory_name = $details->bcategory_name;
                        $trans->store_name = $details->store_name;
                        $trans->store_code = $details->store_code;
                    }
                    break;
                case $this->config->get('stline.ORDER_DEAL_PURCHASE.CREDIT'):
                    $details = DB::table($this->config->get('tables.MERCHANT_ORDERS').' as mo')
                            ->join($this->config->get('tables.ORDER_ITEMS').' as oi', 'oi.order_id', '=', 'mo.order_id')
                            ->join($this->config->get('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join($this->config->get('tables.PAYBACK_DEALS').' as d', 'd.pb_deal_id', '=', 'oi.pb_deal_id')
                            ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as dc', function($dc)
                            {
                                $dc->on('dc.bcategory_id', '=', 'd.bcategory_id')
                                ->where('dc.lang_id', '=', $this->config->get('app.locale_id'));
                            })
                            ->where('mo.order_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,oi.voucher_code,d.deal_name,dc.bcategory_name,ms.store_name,ms.store_code')
                            ->first();
                    if ($details)
                    {
                        $trans->order_code = $details->order_code;
                        $trans->voucher_code = $details->voucher_code;
                        $trans->deal_name = $details->deal_name;
                        $trans->bcategory_name = $details->bcategory_name;
                        $trans->store_name = $details->store_name;
                        $trans->store_code = $details->store_code;
                    }
                    break;
                case $this->config->get('stline.ORDER_DEAL_PURCHASE_TAX.DEBIT'):
                    break;
                case $this->config->get('stline.ORDER_PAYMENT_COMMISSION.DEBIT'):
                    $trans->commission_amount = $trans->amount;
                    break;
                default :
                    Log::error('Transaction Details Not Configured for Statementline ID: '.$trans->statementline_id);
                    return abort(500, 'Transaction Details Not Configured for Statementline ID: '.$trans->statementline_id);
            }
            $d = trans('transactions.'.$trans->statementline_id.'.fields.admin');
            $trans->remark = $trans->statementline.' ('.trans('transactions.'.$trans->statementline_id.'.remarks', (array) $trans->remark->data).')';
            if (is_array($d))
            {
                array_walk($d, function(&$v, $k) use($trans)
                {
                    $v = ['label'=>$v, 'value'=>$trans->{$k}];
                });
                if (isset($trans->account_info))
                {
                    $d = array_merge($d, (array) $trans->account_info);
                }
                return $d;
            }
            else
            {
                Log::error('Transaction Details Fields Not Configured for Statementline ID: '.$trans->statementline_id);
                return abort(500, 'Transaction Details Fields Not Configured for Statementline ID: '.$trans->statementline_id);
            }
        }
        return false;
    }

    public function online_payments (array $arr = array(), $count = false)
    {
        extract($arr);
        $qry = DB::Table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE').' as at')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acf', 'acf.account_id', '=', 'at.account_id')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'acf.account_id')
                ->join($this->config->get('tables.PAYMENT_TYPES_LANG').' as pty', 'pty.payment_type_id', '=', 'at.payment_type_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 'at.currency_id')
                ->select('at.id', 'acf.uname', 'pty.payment_type as payment_type_name', 'at.payment_type_id', 'at.account_id', 'at.currency_id', 'at.amount', 'at.created_on', DB::raw('CONCAT_WS(\' \',acd.first_name,acd.last_name) as fullname'), 'at.payment_status', 'at.status as status_id', 'c.currency_symbol', 'c.code as currency_code', 'c.decimal_places', 'at.response', 'at.purpose', 'at.relative_post_id');
        if (isset($from) && !empty($from))
        {
            $qry->whereDate('at.created_on', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $qry->whereDate('at.created_on', '<=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($purpose) && !empty($purpose))
        {
            $qry->where('at.purpose', $purpose);
        }
        if (isset($status))
        {
            $qry->where('at.status', $status);
        }
        if (isset($terms) && !empty($terms))
        {
            if (is_numeric($terms))
            {
                $qry->where('at.id', $terms);
            }
            else
            {
                $qry->where(function($query) use ($terms)
                {
                    $query->where('acf.uname', 'LIKE', '%'.$terms.'%')
                            ->orwhere(DB::Raw('CONCAT_WS(\' \',acd.first_name,acd.last_name)'), 'like', '%'.$terms.'%')
                            ->orWhere('pty.payment_type', 'LIKE', '%'.$terms.'%');
                });
            }
        }
        if (!empty($count))
        {
            return $qry->count();
        }
        else
        {
            if (isset($start))
            {
                $qry->skip($start)
                        ->take($length);
            }
            $qry->orderBy('at.created_on', 'desc');
            $qry->orderBy('at.id', 'desc');
            $result = $qry->get();
            array_walk($result, function($payment)
            {
                $payment->statusCls = config('dispclass.payment_gateway_response.status.'.$payment->status_id);
                $payment->statusLbl = trans('db_trans.payment_gateway_response.status.'.$payment->status_id);
                $payment->payment_statusCls = config('dispclass.payment_gateway_response.payment_status.'.$payment->payment_status);
                $payment->payment_statusLbl = trans('db_trans.payment_gateway_response.payment_status.'.$payment->payment_status);
                $payment->created_on = showUTZ($payment->created_on, 'Y-m-d H:i:s');
                $payment->amount = CommonLib::currency_format($payment->amount, ['currency_symbol'=>$payment->currency_symbol, 'currency_code'=>$payment->currency_code, 'decimal_places'=>$payment->decimal_places], true, true);
                $payment->action = [];
                $payment->action['details'] = ['label'=>trans('admin/finance.details'), 'url'=>route('admin.finance.order-payments-details', ['id'=>$payment->id])];
                if ($payment->payment_status == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PAYMENT_STATUS.CONFIRMED') && $payment->status_id == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.PENDING'))
                {
                    $payment->action['paid'] = ['label'=>trans('admin/finance.payment-paid'), 'url'=>route('admin.finance.payment-paid', ['id'=>$payment->id])];
                    $payment->action['pay_confirm'] = ['label'=>trans('admin/finance.pay-confirm'), 'url'=>route('admin.finance.pay-confirm', ['id'=>$payment->id])];
                }
                if ($payment->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY'))
                {
                    $payment->order_code = DB::table($this->config->get('tables.MERCHANT_ORDERS'))
                                    ->where('order_type', $this->config->get('constants.ORDER.TYPE.IN_STORE'))
                                    ->where('order_id', $payment->relative_post_id)->value('order_code');
                    $payment->purpose = trans('admin/finance.purpose.'.$payment->purpose).' ('.$payment->order_code.')';
                }
                if ($payment->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.DEAL-PURCHASE'))
                {
                    $payment->order_code = DB::table($this->config->get('tables.MERCHANT_ORDERS'))
                                    ->where('order_type', $this->config->get('constants.ORDER.TYPE.DEAL'))
                                    ->where('order_id', $payment->relative_post_id)->value('order_code');
                    $payment->purpose = trans('admin/finance.purpose.'.$payment->purpose).' ('.$payment->order_code.')';
                }
                if ($payment->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'))
                {
                    $payment->order_code = DB::table($this->config->get('tables.ADD_MONEY'))
                                    ->where('am_id', $payment->relative_post_id)->value('am_code');
                    $payment->purpose = trans('admin/finance.purpose.'.$payment->purpose).' ('.$payment->order_code.')';
                }
                if ($payment->payment_status == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PAYMENT_STATUS.CONFIRMED') && ($payment->status_id == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CANCELLED') || $payment->status_id == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.FAILED')))
                {
                    $payment->action['refund'] = ['label'=>trans('admin/finance.refund'), 'url'=>route('admin.finance.payment-refund', ['id'=>$payment->id]), 'data'=>['confirm'=>'Are you sure to refund this payment?']];
                }
                unset($payment->order_code);
            });
            return $result;
        }
    }

    public function getway_payment_details (array $arr = array())
    {
        extract($arr);
        $qry = DB::Table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE').' as at')
                ->join($this->config->get('tables.ACCOUNT_MST').' as acf', 'acf.account_id', '=', 'at.account_id')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as acd', 'acd.account_id', '=', 'acf.account_id')
                ->join($this->config->get('tables.PAYMENT_TYPES_LANG').' as pty', 'pty.payment_type_id', '=', 'at.payment_type_id')
                ->join($this->config->get('tables.ACCOUNT_SETTINGS').' as as', 'as.account_id', '=', 'acf.account_id')
                ->join($this->config->get('tables.LOCATION_COUNTRIES').' as lc', 'lc.country_id', '=', 'as.country_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 'at.currency_id')
                ->where('at.id', $id);
        $data = $qry;
        $res = $data->first();
        if (!empty($res) && $res->purpose != null)
        {
            if ($res->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY'))
            {
                $qry->join($this->config->get('tables.PAY').' as py', 'py.pay_id', '=', 'at.relative_post_id')
                        ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'py.order_id')
                        ->join($this->config->get('tables.MERCHANT_STORE_MST').' as mmst', 'mmst.store_id', '=', 'mo.store_id')
                        ->join($this->config->get('tables.MERCHANT_MST').' as mst', 'mst.mrid', '=', 'mo.mrid')
                        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mst.account_id')
                        ->leftjoin($this->config->get('tables.ADDRESS_MST').' as addr', 'addr.address_id', '=', 'mmst.address_id')
                        ->select('at.id', 'acf.uname', 'acf.mobile as user_mobile', 'acf.email as user_email', 'pty.payment_type as payment_type_name', 'at.payment_type_id', 'at.account_id', 'at.currency_id', 'at.amount', 'at.created_on', DB::raw('CONCAT_WS(\' \',acd.first_name,acd.last_name) as fullname'), 'at.status as status_id', 'at.payment_status', 'c.currency_symbol', 'c.code as currency_code', 'c.decimal_places', 'at.response', 'at.purpose', 'at.relative_post_id', 'mo.order_code', 'mo.bill_amount as amt', 'mo.status as order_status', 'mo.status as order_payment_status', 'mmst.store_name', 'mmst.store_code', 'mmst.mobile as store_mobile', 'mmst.email as store_email', 'mst.mrlogo as merchant_logo', 'mst.mrbusiness_name', DB::raw('CONCAT_WS(\' \',ad.first_name,ad.last_name) as merchant_name'), 'at.released_date', 'at.approved_date', 'at.cancelled_date', 'at.refund_date', 'addr.formated_address', 'lc.phonecode');
            }
            else if ($res->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.DEAL-PURCHASE'))
            {
                $qry->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'at.relative_post_id')
                        ->leftjoin($this->config->get('tables.PAY').' as py', 'py.order_id', '=', 'mo.order_id')
                        ->join($this->config->get('tables.MERCHANT_STORE_MST').' as mmst', 'mmst.store_id', '=', 'mo.store_id')
                        ->join($this->config->get('tables.MERCHANT_MST').' as mst', 'mst.mrid', '=', 'mo.mrid')
                        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mst.account_id')
                        ->leftjoin($this->config->get('tables.ADDRESS_MST').' as addr', 'addr.address_id', '=', 'mmst.address_id')
                        ->select('at.id', 'acf.uname', 'acf.mobile as user_mobile', 'acf.email as user_email', 'pty.payment_type as payment_type_name', 'at.payment_type_id', 'at.account_id', 'at.currency_id', 'at.amount', 'at.created_on', DB::raw('CONCAT_WS(\' \',acd.first_name,acd.last_name) as fullname'), 'at.status as status_id', 'at.payment_status', 'c.currency_symbol', 'c.code as currency_code', 'c.decimal_places', 'at.response', 'at.purpose', 'at.relative_post_id', 'mo.order_code', 'mo.bill_amount as amt', 'mo.status as order_status', 'mo.status as order_payment_status', 'mmst.store_name', 'mmst.store_code', 'mmst.mobile as store_mobile', 'mmst.email as store_email', 'mst.mrlogo as merchant_logo', 'mst.mrbusiness_name', DB::raw('CONCAT_WS(\' \',ad.first_name,ad.last_name) as merchant_name'), 'at.released_date', 'at.approved_date', 'at.cancelled_date', 'at.refund_date', 'addr.formated_address', 'lc.phonecode');
            }
            else if ($res->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'))
            {
                $qry->join($this->config->get('tables.ADD_MONEY').' as am', function($j)
                {
                    $j->on('am.am_id', '=', 'at.relative_post_id');
                });
                $qry->select('at.id', 'acf.uname', 'acf.mobile as user_mobile', 'acf.email as user_email', 'pty.payment_type as payment_type_name', 'at.payment_type_id', 'at.account_id', 'at.currency_id', 'at.amount', 'at.created_on', DB::raw('CONCAT_WS(\' \',acd.first_name,acd.last_name) as fullname'), 'at.status as status_id', 'at.payment_status', 'c.currency_symbol', 'c.code as currency_code', 'c.decimal_places', 'at.response', 'at.purpose', 'at.relative_post_id', 'am.am_code as order_code', 'am.amount as amt', 'am.status as order_status', 'at.released_date', 'at.approved_date', 'at.cancelled_date', 'at.refund_date', 'lc.phonecode');
            }
            $result = $qry->first();
            if (!empty($result))
            {
                //$result->user_mobile = $result->user_mobile.' '.$result->user_mobile;
                if ($result->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY') || $result->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.DEAL-PURCHASE'))
                {
                    $result->description = trans('admin/finance.purpose.'.$result->purpose).' ('.$result->order_code.')';
                }
                if ($result->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'))
                {
                    $result->order_code = DB::table($this->config->get('tables.ADD_MONEY'))
                                    ->where('am_id', $result->relative_post_id)->value('am_code');
                    $result->description = trans('admin/finance.purpose.'.$result->purpose).' ('.$result->order_code.')';
                }
                $result->statusCls = config('dispclass.payment_gateway_response.status.'.$result->status_id);
                $result->statusLbl = trans('db_trans.payment_gateway_response.status.'.$result->status_id);
                $result->payment_statusCls = config('dispclass.payment_gateway_response.payment_status.'.$result->payment_status);
                $result->payment_statusLbl = trans('db_trans.payment_gateway_response.payment_status.'.$result->payment_status);
                $result->merchant_logo = !empty($result->merchant_logo) ? asset($this->config->get('constants.MERCHANT.LOGO_PATH.WEB').$result->merchant_logo) : asset($this->config->get('constants.MERCHANT.LOGO_PATH.DEFAULT'));
                $result->famt = CommonLib::currency_format($result->amt, ['currency_symbol'=>$result->currency_symbol, 'currency_code'=>$result->currency_code, 'decimal_places'=>$result->decimal_places], true, true);
                $result->famount = CommonLib::currency_format($result->amount, ['currency_symbol'=>$result->currency_symbol, 'currency_code'=>$result->currency_code, 'decimal_places'=>$result->decimal_places], true, true);
                return $result;
            }
        }
        return null;
    }

    public function update_payment_status (array $arr)
    {
        extract($arr);
        return DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->where('id', $id)
                        ->update(['status'=>$this->config->get('constants.ON')]);
    }

    public function refundPayment (array $arr)
    {
        extract($arr);
        $payment_details = DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE').' as pg')
                ->join($this->config->get('tables.ACCOUNT_SETTINGS').' as ast', 'ast.account_id', '=', 'pg.account_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.id', '=', 'pg.currency_id')
                ->where('pg.id', $id)
                ->where('pg.payment_status', $this->config->get('constants.ON'))
                ->whereIn('pg.status', [$this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CANCELLED'), $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.FAILED')])
                ->select('pg.purpose', 'pg.relative_post_id', 'pg.currency_id as pg_currency', 'pg.amount', 'pg.id', 'ast.currency_id as user_currency', 'pg.account_id', 'c.code')
                ->first();
        if (!empty($payment_details))
        {
            if (($payment_details->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.DEAL-PURCHASE') || $payment_details->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY')))
            {
                $qry = DB::table($this->config->get('tables.PAY').' as p')
                        ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                        ->where('p.pay_id', $payment_details->relative_post_id)
                        ->where('p.status', '!=', $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.REFUND'))
                        ->select('p.pay_id', 'mo.order_id')
                        ->first();
            }
            if (!empty($qry))
            {
                $exRate = $this->exchangeRate($payment_details->pg_currency, $payment_details->user_currency);
                if ($payment_details->pg_currency != $payment_details->user_currency)
                {
                    $amount = $payment_details->amount * $exRate;
                }
                else
                {
                    $amount = $payment_details->amount;
                    $exRate = 1;
                }
                $trans_id = $this->baseobj->updateAccountTransaction([
                    'to_account_id'=>$payment_details->account_id,
                    'currency_id'=>$payment_details->user_currency,
                    'wallet_id'=>$this->config->get('constants.WALLET.xpc'),
                    'amt'=>$amount,
                    'relation_id'=>$payment_details->relative_post_id,
                    'transaction_for'=>'REFUND', 	
                    'credit_remark_data'=>['amount'=>CommonLib::currency_format($amount, $payment_details->user_currency, true, true), 'currency'=>$payment_details->code, 'rate'=>$exRate],
                    'debit_remark_data'=>['amount'=>$amount, 'currency'=>$payment_details->code, 'rate'=>$exRate]
                        ], false, true);
//                $trans['from_account_id'] = 1;
//                $trans['to_account_id'] = $payment_details->account_id;
//                $trans['account_id'] = $payment_details->account_id;
//                $trans['currency_id'] = $payment_details->user_currency;
//                $trans['wallet_id'] = $this->config->get('constants.WALLET.xpc');
//                $trans['transaction_type'] = $this->config->get('constants.FUND_TRANSFER_TYPE.CREDIT');
//                $trans['amount'] = $amount;
//                $trans['paidamt'] = $amount;
//                $trans['transaction_id'] = $this->generateTransactionID();
//                $trans['created_on'] = getGTZ('Y-m-d');
//                $trans['status'] = $this->config->get('constants.ON');
//                $trans['statementline'] = $this->config->get('stline.REFUND.CREDIT');
//                $trans['remark'] = trans('transactions.REFUND.CREDIT', ['amount'=>$amount, 'currency'=>$payment_details->code, 'rate'=>$exRate]);
//                $trans_id = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
//                        ->insertGetId($trans);
                if (!empty($trans_id))
                {
//                    $balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
//                            ->where('account_id', $payment_details->account_id)
//                            ->where('currency_id', $payment_details->user_currency)
//                            ->first();
//                    $update['current_balance'] = $balance->current_balance + $amount;
//                    $update['tot_credit'] = $balance->tot_credit + $amount;
//                    DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
//                            ->where('id', $trans_id)
//                            ->update(['current_balance'=>$update['current_balance']]);
                    $updateResponce['status'] = $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.REFUND');
                    $updateResponce['refund_date'] = getGTZ();
                    DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                            ->where('id', $payment_details->id)
                            ->update($updateResponce);
                    DB::table($this->config->get('tables.MERCHANT_ORDERS'))
                            ->where('order_id', $qry->order_id)
                            ->update(['payment_status'=>$this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.REFUND')]);
                    DB::table($this->config->get('tables.PAY'))
                            ->where('pay_id', $qry->pay_id)
                            ->update(['status'=>$this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.REFUND')]);
//                    $balance = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
//                            ->where('account_id', $payment_details->account_id)
//                            ->where('currency_id', $payment_details->user_currency)
//                            ->update($update);
                    return $balance;
                }
            }
        }
        return false;
    }

    public function confirmPayment (array $arr)
    {
        extract($arr);
        $payment_details = DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                ->where('id', $id)
                ->where('status', $this->config->get('constants.OFF'))
                ->first();
        if (!empty($payment_details))
        {
            if (($payment_details->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.DEAL-PURCHASE')) || ($payment_details->purpose == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY')))
            {
                $qry = DB::table($this->config->get('tables.PAY').' as p')
                        ->join($this->config->get('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                        ->where('p.pay_id', $payment_details->relative_post_id)
                        ->where('p.status', $this->config->get('constants.OFF'))
                        ->select('p.pay_id', 'mo.order_id')
                        ->first();
                if (!empty($qry))
                {
                    $updateResponce['status'] = $this->config->get('constants.ON');
                    $updateResponce['refund_date'] = getGTZ();
                    DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                            ->where('id', $payment_details->id)
                            ->update($updateResponce);
                    DB::table($this->config->get('tables.MERCHANT_ORDERS'))
                            ->where('order_id', $qry->order_id)
                            ->update(['status'=>$this->config->get('constants.ON')]);
                    DB::table($this->config->get('tables.PAY'))
                            ->where('pay_id', $qry->pay_id)
                            ->update(['status'=>$this->config->get('constants.ON')]);
                    return true;
                }
            }
        }
        return false;
    }

    public function wallet_balance (array $arr = [])
    {
        extract($arr);

        $qry = DB::table($this->config->get('tables.WALLET').' as w')
                ->leftJoin($this->config->get('tables.ACCOUNT_WALLET_BALANCE').' as ub', function($join) use($account_id)
                {
                    $join->on('ub.wallet_id', '=', 'w.wallet_id');
                    $join->where('ub.account_id', '=', $account_id);
                })
                ->leftJoin($this->config->get('tables.WALLET_LANG').' as wl', function($join)
                {
                    $join->on('wl.wallet_id', '=', 'w.wallet_id');
                    $join->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->leftJoin($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'ub.currency_id');
        if (isset($wallet_code) && $wallet_code > 0)
        {
            $qry->where('w.wallet_code', $wallet_code);
        }
        if (isset($currency_id) && $currency_id > 0)
        {
            $qry->where('ub.currency_id', $currency_id);
        }
        if (isset($wallet) && $wallet != '')
        {
            $qry->where('w.wallet_id', $wallet);
        }
        $qry->selectRaw('w.wallet_id,ub.current_balance,ub.currency_id,ub.tot_credit,ub.tot_debit,w.wallet_code,wl.wallet as wallet_desc,wl.terms as wallet_terms,cur.currency as currency_code,cur.currency_symbol,cur.decimal_places,wl.wallet,0 as pending_balance');
        $qry->orderby('w.sort_order', 'asc');
        $result = $qry->get();
		echo '<pre>';
		print_r($result);die;
        return $result;
    }

    public function get_roles ()
    {
        return DB::table($this->config->get('tables.SYSTEM_ROLES'))
                        ->where('status', 1)
                        ->select('system_role_name', 'system_role_id')->get();
    }

}
