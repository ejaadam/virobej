<?php
namespace App\Models\Seller;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use CommonLib;

class Report extends Model
{	

	public function getTransactionDetails (array $arr = array())
    {
		extract($arr);
        $trans = DB::table(Config('tables.ACCOUNT_TRANSACTION').' as a')
                ->leftJoin(Config('tables.ACCOUNT_MST').' as ra', 'ra.account_id', '=', 'a.account_id')
                ->leftJoin(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'a.account_id')
                ->join(Config('tables.PAYMENT_TYPES').' as ptl', function($ptl)
                {
                    $ptl->on('ptl.payment_type_id', '=', 'a.payment_type_id');
                })
                ->join(Config('tables.WALLET').' as w', 'w.wallet_id', '=', 'a.wallet_id')
                ->join(Config('tables.WALLET_LANG').' as wt', function($join)
                {
                    $join->on('wt.wallet_id', '=', 'a.wallet_id');
                    //$join->where('wt.lang_id', '=', Config('app.locale_id'));
                })
                ->join(Config('tables.STATEMENT_LINE').' as st', 'st.statementline_id', '=', 'a.statementline_id')                
                ->join(Config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'a.currency_id')
                ->where('a.is_deleted', Config('constants.NOT_DELETED'))
                ->where('a.status', '>', 0) 
                ->where('a.transaction_id', $id)
                ->where('a.account_id', $account_id)
                ->selectRaw('a.account_id, ra.uname, CONCAT(ad.firstname,\' \',ad.lastname) as full_name, a.created_on, w.wallet_code, a.remark, a.post_type, a.statementline_id, a.relation_id, a.transaction_type, a.transaction_id, cur.currency as code, cur.currency_symbol, cur.decimal_places, a.amt as amount, a.tax, a.handle_amt as handleamt, a.paid_amt as paidamt, a.current_balance, st.statementline, wt.wallet, ptl.payment_type, a.status, st.transaction_key, ra.email, ra.mobile, ra.user_code as account_code')
                ->first();
				
				
        if (!empty($trans))
        {
            $trans->created_on = showUTZ($trans->created_on, 'h:i A, d M Y');			
            $status_id = $trans->status;			
            $trans->status = trans('seller/reports.transaction.status.'.$status_id);
            $trans->status_class = Config('dispclass.transaction.status.'.$status_id);
			
            $trans->amount = CommonLib::currency_format($trans->amount, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>(''), 'decimal_places'=>$trans->decimal_places]);
            $trans->tax = CommonLib::currency_format($trans->tax, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>('-'), 'decimal_places'=>$trans->decimal_places]);
            $trans->handleamt = CommonLib::currency_format($trans->handleamt, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>('-'), 'decimal_places'=>$trans->decimal_places]);
            $trans->paidamt = CommonLib::currency_format($trans->paidamt, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>($trans->transaction_type == 1 ? '+' : '-'), 'decimal_places'=>$trans->decimal_places]);
            $trans->current_balance = CommonLib::currency_format($trans->current_balance, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'decimal_places'=>$trans->decimal_places]);			
            $trans->bill_amount = $trans->amount;
            if (!empty($trans->remark) && strpos($trans->remark, '}') > 0)
            {
                $trans->remark = $ordDetails = json_decode($trans->remark);
                $trans->order_code = (isset($ordDetails->data->order_code)) ? $ordDetails->data->order_code : '';
                $trans->type = $trans->remark = trans('transactions.'.$trans->statementline_id.'.seller.remarks', (array) $trans->remark->data);
                //$trans->remark 		= $trans->statementline.' ('.$trans->remark.')';
                $trans->remark = $trans->statementline.' - '.$trans->remark;
            }
            else
            {
                $trans->remark = $trans->statementline;
            }
			
            switch ($trans->statementline_id)
            {
                case Config('stline.SIGN_UP_BONUS.CREDIT'):
                    $details = DB::table(Config('tables.ACCOUNT_MST').' as am')
                            ->join(Config('tables.DEVICES').' as d', 'd.device_id', '=', 'am.signup_device')
                            ->join(Config('tables.ACCOUNT_SETTINGS').' as s', 's.account_id', '=', 'am.account_id')
                            ->leftJoin(Config('tables.ACCOUNT_MST').' as ra', 'ra.account_id', '=', 'am.referred_account_id')
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
                case Config('stline.REFERRAL_BONUS.CREDIT'):
                    $details = DB::table(Config('tables.REFERRAL_EARNINGS').' as re')
                            ->join(Config('tables.ACCOUNT_MST').' as fam', 'fam.account_id', '=', 're.from_account_id')
                            ->join(Config('tables.ACCOUNT_DETAILS').' as fad', 'fad.account_id', '=', 're.from_account_id')
                            ->join(Config('tables.ACCOUNT_MST').' as tam', 'tam.account_id', '=', 're.to_account_id')
                            ->join(Config('tables.ACCOUNT_DETAILS').' as tad', 'tad.account_id', '=', 're.to_account_id')
                            ->join(Config('tables.PROMOTIONAL_OFFERS').' as po', 'po.promo_offer_id', '=', 're.commission_type')
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
                case Config('stline.REDEEM.DEBIT'):
                    $details = DB::table(Config('tables.REDEEMS.').' as r')
                            ->join(Config('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'r.order_id')
                            ->join(Config('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join(Config('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join(Config('tables.PAY').' as p', 'p.order_id', '=', 'mo.order_id')
                            ->join(Config('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mo.approved_by')
                            ->join(Config('tables.CURRENCIES').' as cur', 'cur.id', '=', 'r.currency_id')
                            ->where('redeem_id', $trans->relation_id)
                            ->selectRaw('r.redeem_amount,mo.bill_amount,p.to_amount as amount_due,ms.store_code,ms.store_name,am.uname as staff_id,mm.mrcode,cur.code,cur.decimal_places,cur.currency_symbol')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->store_code = $details->store_code;
                        $trans->store_name = $details->store_name;
                        $trans->staff_id = $details->staff_id;
                        $trans->mrcode = $details->mrcode;
                        $trans->bill_amount = CommonLib::currency_format($details->bill_amount, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places]);
                        $trans->redeem_amount = CommonLib::currency_format($details->redeem_amount, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places]);
                        $trans->amount_due = CommonLib::currency_format($details->amount_due, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places]);
                    }
                    break;
                case Config('stline.CURRENCY_CONVERSION.DEBIT'):
                    break;
                case Config('stline.CURRENCY_CONVERSION.CREDIT'):

                    break;
                case Config('stline.FUND_TRANS_BY_SYSTEM.CREDIT'):
                    break;
                case Config('stline.FUND_TRANS_BY_SYSTEM.DEBIT'):
                    break;

                case Config('stline.ADD_FUND.CREDIT'):
                    break;
                case Config('stline.WITHDRAW.DEBIT'):
                    $details = DB::table(Config('tables.WITHDRAWAL_MST').' as wm')
                            ->where('wd_id', $trans->relation_id)
                            ->selectRaw('account_info')
                            ->first();
                    if (!empty($details))
                    {
                        $trans->title = 'You have transafer '.$trans->paidamt;
                        $trans->account_info = json_decode(stripslashes($details->account_info));
                        if (!empty($trans->account_info))
                        {
                            array_walk($trans->account_info, function(&$a, $k)
                            {
                                $a = ['label'=>trans('withdrawal.account_details.'.$k), 'value'=>$a];
                            });
                        }
                    }
                    break;
                case Config('stline.CASHBACK.CREDIT'):
                    $details = DB::table(Config('tables.CASHBACKS').' as c')
                            ->join(Config('tables.CURRENCIES').' as cur', 'cur.id', '=', 'c.currency_id')
                            ->join(Config('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'c.order_id')
                            ->join(Config('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join(Config('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join(Config('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mo.approved_by')
                            ->join(Config('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                            ->join(Config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'mo.account_id')
                            ->where('c.cashback_id', $trans->relation_id)
                            ->selectRaw('mo.bill_amount as bill_amt,cur.code,cur.decimal_places,cur.currency_symbol,mo.bill_amount,mo.order_code,mm.mrcode,ms.store_code,ms.store_name,am.uname as staff_id,am.uname as staff_id,um.account_code as user_account_code,CONCAT_WS(" ",ud.first_name,ud.last_name) as user_fullname')
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
                case Config('stline.CASHBACK.DEBIT'):
                    $details = DB::table(Config('tables.CASHBACKS').' as c')
                            ->join(Config('tables.CURRENCIES').' as cur', 'cur.id', '=', 'c.currency_id')
                            ->join(Config('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'c.order_id')
                            ->join(Config('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join(Config('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join(Config('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mo.approved_by')
                            ->join(Config('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                            ->join(Config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'mo.account_id')
                            ->where('c.cashback_id', $trans->relation_id)
                            ->selectRaw('mo.bill_amount as bill_amt,cur.code,cur.decimal_places,cur.currency_symbol,mo.order_code,mm.mrcode,ms.store_code,ms.store_name,am.uname as staff_id,um.account_code as user_account_code,CONCAT_WS(" ",ud.first_name,ud.last_name) as user_fullname')
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
                case Config('stline.ORDER_PAYMENT.DEBIT'):
                    $details = DB::table(Config('tables.PAY').' as p')
                            ->join(Config('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                            ->join(Config('tables.CURRENCIES').' as cur', 'cur.id', '=', 'mo.currency_id')
                            ->join(Config('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
                            ->join(Config('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join(Config('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                            ->join(Config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'mo.account_id')
                            ->where('p.pay_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,mm.mrcode,mm.mrbusiness_name,ms.store_code,ms.store_name,um.account_code as user_account_code,CONCAT_WS(" ",ud.first_name,ud.last_name) as user_fullname,mo.bill_amount,cur.code,cur.currency_symbol,cur.decimal_places')
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
                        $trans->bill_amount = CommonLib::currency_format($details->bill_amount, ['currency_code'=>$details->code, 'currency_symbol'=>$details->currency_symbol, 'decimal_places'=>$details->decimal_places]);
                        $trans->net_pay = $trans->paidamt;
                        $trans->remark = trans('transactions.'.$trans->statementline_id.'.retailer.details_remarks', ['site_name'=>$trans->store_name, 'amount'=>$trans->amount]);
                    }
                    break;
                case Config('stline.ORDER_TIP.CREDIT'):
                    $details = DB::table(Config('tables.MERCHANT_ORDERS').' as mo')
                            ->join(Config('tables.MERCHANT_MST').' as mm', 'mm.mrid', '=', 'mo.mrid')
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
                case Config('stline.ORDER_PAYMENT.CREDIT'):

                    $details = DB::table(Config('tables.MERCHANT_ORDERS').' as mo')
                                    ->Join(Config('tables.ACCOUNT_MST').' as cu', 'cu.account_id', '=', 'mo.account_id')
                                    ->Join(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                                    ->join(Config('tables.ORDER_COMMISSION').' as oc', 'oc.order_id', '=', 'mo.order_id')
                                    ->where('mo.order_id', $trans->relation_id)
                                    ->selectRaw('oc.store_recd_amt as store_received_amt,CONCAT(ad.first_name,\' \',ad.last_name) as full_name,cu.mobile,cu.email')->first();
                    $trans->remark = trans('transactions.'.$trans->statementline_id.'.retailer.details_remarks', ['amount'=>$trans->amount, 'site_name'=>$this->siteConfig->site_name]);
                    $trans->bill_amount = $trans->amount;
                    $trans->customer = $details->full_name;
                    $trans->email = $details->email;
                    $trans->mobile = $details->mobile;
                    $trans->store_received_amt = CommonLib::currency_format($details->store_received_amt, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>('-'), 'decimal_places'=>$trans->decimal_places]);
                    break;

                case Config('stline.ORDER_REFUND.DEBIT'):
                    $details = DB::table(Config('tables.ORDER_REFUND').' as or')
                            ->join(Config('tables.MERCHANT_ORDERS').' as mo', 'mo.order_id', '=', 'or.order_id')
                            ->join(Config('tables.CURRENCIES').' as cur', 'cur.id', '=', 'mo.currency_id')
                            ->where('or.or_id', $trans->relation_id)
                            ->selectRaw('mo.order_code,or.amount as refund_amount,cur.code,cur.decimal_places,cur.currency_symbol')
                            ->first();
                    if ($details)
                    {
                        $trans->order_code = $details->order_code;
                        $trans->refund_amount = CommonLib::currency_format($details->refund_amount, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places, true, false]);
                    }
                    break;
                case Config('stline.ORDER_DEAL_PURCHASE.DEBIT'):
                    $details = DB::table(Config('tables.MERCHANT_ORDERS').' as mo')
                            ->join(Config('tables.ORDER_ITEMS').' as oi', 'oi.order_id', '=', 'mo.order_id')
                            ->leftjoin(Config('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join(Config('tables.PAYBACK_DEALS').' as d', 'd.pb_deal_id', '=', 'oi.pb_deal_id')
                            ->join(Config('tables.BUSINESS_CATEGORY_LANG').' as dc', function($dc)
                            {
                                $dc->on('dc.bcategory_id', '=', 'd.bcategory_id')
                                ->where('dc.lang_id', '=', Config('app.locale_id'));
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
                case Config('stline.ORDER_DEAL_PURCHASE_TO_MERCHANT.CREDIT'):
                    $details = DB::table(Config('tables.MERCHANT_ORDERS').' as mo')
                            ->join(Config('tables.ORDER_ITEMS').' as oi', 'oi.order_id', '=', 'mo.order_id')
                            ->join(Config('tables.MERCHANT_STORE_MST').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join(Config('tables.PAYBACK_DEALS').' as d', 'd.pb_deal_id', '=', 'oi.pb_deal_id')
                            ->join(Config('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                            ->join(Config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
                            ->join(Config('tables.BUSINESS_CATEGORY_LANG').' as dc', function($dc)
                            {
                                $dc->on('dc.bcategory_id', '=', 'd.bcategory_id')
                                ->where('dc.lang_id', '=', Config('app.locale_id'));
                            })
                            ->where('mo.order_id', $trans->relation_id)
                            ->selectRaw('mo.bill_amount,mo.order_code,oi.voucher_code,d.deal_name,dc.bcategory_name,ms.store_name,ms.store_code,CONCAT_WS(" ",ud.first_name,last_name) as user_fullname,um.account_code')
                            ->first();
                    if ($details)
                    {
                        $trans->order_code = $details->order_code;
                        $trans->amount = CommonLib::currency_format($details->bill_amount, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'decimal_places'=>$trans->decimal_places, true, false]);
                        $trans->voucher_code = $details->voucher_code;
                        $trans->deal_name = $details->deal_name;
                        $trans->bcategory_name = $details->bcategory_name;
                        $trans->store_name = $details->store_name;
                        $trans->store_code = $details->store_code;
                        $trans->user_fullname = $details->user_fullname;
                        $trans->user_account_code = $details->account_code;
                    }
                    break;
                case Config('stline.ORDER_DEAL_PURCHASE_TAX.DEBIT'):
                    break;
                case Config('stline.ORDER_PAYMENT_COMMISSION.DEBIT'):
                    $trans->commission_amount = $trans->amount;
                    $details = DB::table(Config('tables.ORDERS').' as mo')
                                    ->Join(Config('tables.ACCOUNT_MST').' as cu', 'cu.account_id', '=', 'mo.account_id')
                                    ->Join(Config('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
                                    ->Join(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                                    ->join(Config('tables.ORDER_COMMISSION').' as oc', 'oc.order_id', '=', 'mo.order_id')
                                    ->where('mo.order_id', $trans->relation_id)
                                    ->selectRaw('oc.store_recd_amt as store_received_amt, CONCAT(ad.firstname,\' \',ad.lastname) as full_name, cu.mobile, cu.email, ms.store_name, cu.user_code as account_code')->first();
                    $trans->remark = trans('transactions.'.$trans->statementline_id.'.seller.details_remarks', ['amount'=>$trans->amount, 'store_name'=>$details->store_name]);
                    $trans->bill_amount = $trans->amount;
                    $trans->customer = $details->full_name;
                    $trans->email = $details->email;
                    $trans->mobile = $details->mobile;
                    $trans->account_code = $details->account_code;
                    $trans->store_received_amt = CommonLib::currency_format($details->store_received_amt, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>('-'), 'decimal_places'=>$trans->decimal_places]);
                    break;

                case Config('stline.ORDER_PAYMENT_TO_MERCHANT.CREDIT'):
                    $details = DB::table(Config('tables.ORDERS').' as mo')
                                    ->Join(Config('tables.ACCOUNT_MST').' as cu', 'cu.account_id', '=', 'mo.account_id')
                                    ->Join(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                                    ->join(Config('tables.ORDER_COMMISSION').' as oc', 'oc.order_id', '=', 'mo.order_id')
                                    ->where('mo.order_id', $trans->relation_id)
                                    ->selectRaw('oc.store_recd_amt as store_received_amt, CONCAT(ad.firstname,\' \',ad.lastname) as full_name, cu.mobile, cu.email')->first();
                    $trans->remark = trans('transactions.'.$trans->statementline_id.'.seller.details_remarks', ['amount'=>$trans->amount, 'site_name'=>'Virob']);
                    $trans->bill_amount = $trans->amount;                    
                    $trans->customer = $details->full_name;
                    $trans->email = $details->email;
                    $trans->mobile = $details->mobile;
                    $trans->store_received_amt = CommonLib::currency_format($details->store_received_amt, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>('-'), 'decimal_places'=>$trans->decimal_places]);
					$trans->balance = $trans->paidamt;
					break;
                default :
                    Log::error('Transaction Details Not Configured for Statementline ID: '.$trans->statementline_id);
                    return abort(500, 'Transaction Details Not Configured for Statementline ID: '.$trans->statementline_id);
            }			
            $d = trans('transactions.'.$trans->statementline_id.'.seller.fields');
            $payment_fld = trans('transactions.'.$trans->statementline_id.'.seller.payment_details');
            $payment_details = [];
            if (is_array($d))
            {
                array_walk($d, function(&$v, $k) use($trans)
                {
                    if (isset($trans->{$k}))
                    {
                        $v = ['label'=>$v, 'value'=>(isset($trans->{$k})) ? $trans->{$k} : ''];
                    }
                });
                if (is_array($payment_fld) && !empty($payment_fld))
                {
                    array_walk($payment_fld, function(&$val, $key) use($trans, &$payment_details)
                    {
                        if (isset($trans->{$key}))
                        {
                            $payment_details[$key] = ['label'=>$val, 'value'=>(isset($trans->{$key})) ? $trans->{$key} : ''];
                        }
                    });
                }
                $d['payment_details'] = array_values($payment_details);
                $d['bill_amount'] = $trans->bill_amount;
                $d['created_on'] = $trans->created_on;
                $d['status'] = $trans->status;
                $d['status_class'] = $trans->status_class;
                $d['remark'] = $trans->remark;
                if (isset($trans->account_info))
                {
                    $d = array_merge($d, (array) $trans->account_info);
                }
                $d['support'] = url('/faqs');
                return ['details'=>$d, 'statementline_id'=>$trans->statementline_id];
            }
            else
            {
                //Log::error('Transaction Details Fields Not Configured for Statementline ID: '.$trans->statementline_id);
                return abort(500, 'Transaction Details Fields Not Configured for Statementline ID: '.$trans->statementline_id);
            }
        }
        return false;
	}
	
	
	public function getTransactionDetails_bk111018 (array $arr = array())
    {
        extract($arr);
        $order = DB::table(Config('tables.ORDERS').' as mo')
                ->Join(Config('tables.ACCOUNT_MST').' as aum', 'aum.account_id', '=', 'mo.approved_by')
                ->Join(Config('tables.ACCOUNT_DETAILS').' as aad', 'aad.account_id', '=', 'mo.approved_by')
                ->Join(Config('tables.ORDER_COMMISSION').' as oc', 'oc.order_id', '=', 'mo.order_id')
                ->Join(Config('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                ->Join(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                ->Join(Config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'mo.account_id')
                ->Join(Config('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id')
                ->Join(Config('tables.STORES').' as msm', 'msm.store_id', '=', 'mo.store_id')                
                ->Join(Config('tables.ADDRESS_MST').' as add', 'add.address_id', '=', 'msm.address_id')
                ->Join(Config('tables.PAY').' as py', function($lf)
                {
                    $lf->on('py.order_id', '=', 'mo.order_id');
                    //->where('py.pay_mode_id', '=', Config('constants.PAYMENT_MODES.CASH'));
                })
                ->Join(Config('tables.CURRENCIES').' as co', 'co.currency_id', '=', 'mo.currency_id')
				->selectRaw('order_code, mo.order_id, mo.order_date, mo.status, concat(lc.phonecode,um.mobile) mobile, CONCAT(ad.firstname,\' \',ad.lastname) as customer, um.user_code as account_code, um.email, CONCAT(aad.firstname,\' \',aad.lastname) as staff_name, aum.uname as staff_id, mo.order_type, mo.pay_through, msm.store_name as store,'.'mo.bill_amount, oc.store_recd_amt as collected_amount, system_comm as charges, mr_settlement as balance, add.address, py.from_amount as received_amt, mo.currency_id, oc.sys_recd_amt, py.payment_id, msm.store_logo, msm.store_code, oc.system_comm as fees, oc.tax')                
                ->where('mo.supplier_id', $supplier_id) 
                ->where('mo.order_code', $order_code)
                ->where('mo.is_deleted', Config('constants.OFF'));
        $order = $order->first();		
        if (!empty($order))
        {   
			$order->status_class = Config('dispclass.seller.order.status.'.$order->status);
			//$order->status = trans('seller/general.order.status.'.$order->order_type.'.'.$order->pay_through.'.'.$order->status);	
			if ($order->status == 1) {
				$order->status = 'SUCCESS';
			} else {
				$order->status = 'PENDING';
			}
			if ($order->balance > 0) {
					$order->mr_settlement_class = 'success';
				} else {
					$order->mr_settlement_class = 'danger';
				}
			$order->statement_line = 51;            
            $order->order_date = showUTZ($order->order_date, 'H:i:s A, d M y');           
            $order->bill_amount = CommonLib::currency_format($order->bill_amount, $order->currency_id, true, false);			 
            $order->received_amt = CommonLib::currency_format($order->received_amt, $order->currency_id, true, false);
            $order->sys_recd_amt = CommonLib::currency_format($order->sys_recd_amt, $order->currency_id, true, false);
            $order->tax = CommonLib::currency_format($order->tax, $order->currency_id, true, false);
            $order->fees = CommonLib::currency_format($order->fees, $order->currency_id, true, false);
            $order->balance = CommonLib::currency_format($order->balance, $order->currency_id, true, false);
            $order->charges = CommonLib::currency_format($order->charges, $order->currency_id, true, false);
            $order->collected_amount = CommonLib::currency_format($order->collected_amount, $order->currency_id, true, false);			            
			$order->order_type_key = trans('seller/general.order.order_type.'.$order->order_type.'.'.$order->pay_through);	
            $order_type = $order->order_type;
			$order->order_type = trans('seller/general.order.pay_through.'.$order->order_type.'.'.$order->pay_through);
			$order->pay_through = trans('seller/general.order.pay_through.'.$order_type.'.'.$order->pay_through);
			$format = trans('user/transactions.'.$order->statement_line.'.fields.retailer');   			 
			$order->remarks = trans('user/transactions.'.$order->statement_line.'.mr_details_remarks', ['amount'=>$order->bill_amount]);	
            $order->is_refundable = isset($is_refundable) ? 1 : 0;			
            if (isset($order->is_refundable))
            {
                if (date('Y-m-d') > date('Y-m-d', strtotime($order->order_date.$refund_days.' days')))
                {
                    $order->is_refundable = 0;
                }
            }
            if (!empty($format))
            {
                array_walk($format, function(&$v, $k) use($order)
                {
                    $v = ['label'=>$v, 'value'=>$order->{$k}];
                });
            }
            
            $format['mr_settlement_class'] = $order->mr_settlement_class;
            $format['fees'] = $order->fees;
            $format['balance'] = $order->balance;
            $format['remarks'] = $order->remarks;
            $format['order_date'] = date('M d,Y H:i:s', strtotime($order->order_date));
            $format['status'] = $order->status;
            $format['status_class'] = $order->status_class;
            $format['received_amount'] = $order->received_amt;
            $format['store_name'] = $order->store;       
            $format['store_address'] = $order->address;                                  
            $format['pg_trans_no'] = $order->payment_id;       
            $format['order_type'] = $order->order_type;       
            $format['store_code'] = $order->store_code;       
			$format['tax'] = $order->tax;
            $format['support'] = URL('faqs');            
            $format['actions'] = [];                        
        }
        return $format;
    }
	
	public function getTransactionList (array $arr = array(), $count = false)
	{	//return $arr;
		$from = 0;
        $to = 0;
        $ewallet_id = 0;
        $user_id = 0;
        $display_id = 0;
        $payout_type = 0;
        extract($arr);
        $transaction_log = DB::table(Config('tables.SUPPLIER_CLIENT_TRANSACTION').' as mc')
                ->join(Config('tables.ACCOUNT_TRANSACTION').' as a', 'a.id', '=', 'mc.transaction_id')
                ->join(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'a.from_account_id')
                ->join(Config('tables.STATEMENT_LINE').' as st', 'st.statementline_id', '=', 'a.statementline_id')
                //->join(Config('tables.STATEMENTLINES').' as stl', 'stl.id', '=', 'a.statementline')
                ->join(Config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'a.currency_id')
                ->join(Config('tables.WALLET').' as w', 'w.wallet_id', '=', 'a.wallet_id')
                ->leftjoin(Config('tables.WALLET_LANG').' as wl', function($join)
                {
                    $join->on('wl.wallet_id', '=', 'a.wallet_id')
                    ->where('wl.lang_id', '=', Config('app.locale_id'));
                })
                ->where('a.is_deleted', Config('constants.NOT_DELETED'))
                ->where('a.status', '>', 0);
			
        if ($account_type_id == Config('constants.ACCOUNT_TYPE.SELLER'))
        {
            $transaction_log->where('mc.supplier_id', $supplier_id);
        }
        else if ($account_type_id == Config('constants.ACCOUNT_TYPE.MERCHANT_SUB_ADMIN'))
        {
            $transaction_log->where('mc.store_id', $store_id);
        }
        elseif ($account_type_id == Config('constants.ACCOUNT_TYPE.CLIENTS'))
        {
            $transaction_log->where('mc.client_id', $client_id);
        }
        else
        {
            $transaction_log->where('ad.account_id', $account_id);
        }
		
        if (isset($trans_type) && !is_null($trans_type))
        {
            $transaction_log->where('a.transaction_type', $trans_type);
        }
        if (!empty($from))
        {
            $transaction_log->whereDate('a.created_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (!empty($to))
        {
            $transaction_log->whereDate('a.created_on', '<=', date('Y-m-d', strtotime($to)));
        }
        if (isset($search_term) && !empty($search_term))
        {
            // $transaction_log->whereRaw('(a.remark like \'%'.$search_term.'%\'  OR   st.statementline like \'%'.$search_term.'%\')');
            $transaction_log->where(function($sub) use($search_term)
            {
                $sub->where('a.remark', 'LIKE', '%'.$search_term.'%')
                        ->orwhere('st.statementline', 'LIKE', '%'.$search_term.'%')
                        ->orwhere('a.transaction_id', 'LIKE', '%'.$search_term.'%');
            });
        }
        if (!empty($currency))
        {
            $transaction_log->where('a.currency_id', $currency);
        }
        if (!empty($wallet))
        {
            $transaction_log->where('w.wallet_code', $wallet);
        } 
        if (isset($orderby) && isset($order))
        {
            //$transaction_log->orderby($orderby, $order);
			$transaction_log->orderby('a.id', 'DESC');
        }
        else
        {
            $transaction_log->orderby('a.created_on', 'DESC');
            $transaction_log->orderby('a.id', 'DESC');
        }
        if ($count)
        {
            return $transaction_log->count();
        }
        else
        {			
            if ((isset($start)) && (isset($length)))
            {
                $transaction_log->skip($start)->take($length);
            }
            $trans = $transaction_log->selectRaw('mc.id as mc_id, a.id, a.transaction_id, a.created_on, a.remark, a.transaction_type as trans_type, cur.currency_symbol, cur.currency as code, cur.decimal_places, a.paid_amt as amount, st.statementline, st.statementline_id, CONCAT(ad.firstname,\' \',ad.lastname) as from_name, ad.profile_img as from_profile, w.wallet_code, wl.wallet, a.status, st.transaction_key')->get();
            //print_r($trans);exit;
            array_walk($trans, function(&$t)
            {
                $t->created_on = showUTZ($t->created_on, 'h:i A, d M Y');
                $t->amount = CommonLib::currency_format($t->amount, ['currency_symbol'=>$t->currency_symbol, 'value_type'=>($t->trans_type == 1 ? '+' : '-'), 'decimal_places'=>$t->decimal_places]);
                $t->from_profile = !empty($t->from_profile) ? asset(Config('path.ACCOUNT.PROFILE_IMG.WEB.160x160').$t->from_profile) : asset(Config('constants.ACCOUNT.PROFILE_IMG.DEFAULT'));
                $t->statementline = $t->statementline;
                if (!empty($t->remark))
                {					
                    $t->remark = json_decode($t->remark);
                    $t->statementline = trans('transactions.'.$t->statementline_id.'.seller.statement_line', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
                    //$t->remark = $t->statementline.' - '.trans('transactions.'.$t->statementline_id.'.seller.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
					$t->remark = trans('transactions.'.$t->statementline_id.'.seller.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
                }
                else
                {
                    $t->remark = $t->statementline;
                } 
                $t->actions = [];
                $t->actions[] = ['url'=>route('seller.reports.instore.transaction.details', ['id'=>$t->transaction_id]), 'label'=>'Details'];
                $t->status_class = config('dispclass.transaction.status.'.$t->status);
                //$t->status = trans('db_trans.transaction.status.'.$t->status);
                $t->status = trans('seller/reports.transaction.status.'.$t->status);
                unset($t->statementline_id);
                unset($t->currency_symbol);
                unset($t->code);
                unset($t->decimal_places); 
            });

            //print_r($trans);exit;
            return $trans;
        }
	}
	
	public function getTransactionList_bk111018 (array $arr = array(), $count = false)
    {
        extract($arr);
        $commissons = DB::table(Config('tables.ORDER_COMMISSION').' as oc')
                ->join(Config('tables.ORDERS').' as mord', 'mord.order_id', '=', 'oc.order_id')
                ->where('mord.supplier_id', $supplier_id)
                ->where('oc.is_deleted', Config('constants.OFF'));
		
        if (isset($search_term) && !empty($search_term))
        {
            if (isset($filterTerms) && !empty($filterTerms))
            {
                $search_term = '%'.$search_term.'%';
                $search_field = ['Store'=>'msm.store_code', 'Order'=>'mord.order_code'];
                $commissons->where(function($commissons) use($filterTerms, $search_term, $search_field)
                {
                    foreach ($filterTerms as $search)
                    {
                        if (array_key_exists($search, $search_field))
                        {
                            $commissons->orWhere(DB::raw($search_field[$search]), 'like', $search_term);
                        }
                    }
                });
            }

            $commissons->where(function($commissons) use($search_term)
            {
                $commissons->Where('mord.order_code', 'like', $search_term);
            });
        }			
        if (isset($status) && $status != '')
        {
            $commissons->where('oc.status', $status);
        }
        if (isset($from) && !empty($from))
        {
            $commissons->whereDate('oc.created_on', '<=', getGTZ($from, 'Y-m-d'));
        }
        else if (isset($to) && !empty($to))
        {
            $commissons->whereDate('oc.created_on', '>=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($store_id) && !empty($store_id))
        {
            $commissons->join(Config('tables.STORES').' as msm', 'msm.store_id', '=', 'mord.store_id')
                    ->where('mord.store_id', $store_id);
        }
        else
        {
            $commissons->join(Config('tables.STORES').' as msm', 'msm.store_id', '=', 'mord.store_id');
        }
        if (isset($orderby) && isset($order))
        {
            $commissons->orderBy('oc.created_on', $order);
        }
        else
        {
            $commissons->orderby('oc.created_on', 'DESC');
        }
        if ($count)
        {
            return $commissons->count();
        }
        /* oc.order_amt */
        else
        {
            if (isset($start) && isset($length))
            {
                $commissons->skip($start)->take($length);
            }
            $commissons->leftJoin(Config('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'oc.currency_id')
                        ->selectRaw('msm.store_name, msm.store_code, oc.comm_id as id, mord.bill_amount as order_amt, mord.order_code, oc.system_comm, oc.tax, oc.net_amt, oc.status, oc.created_on, c.currency_symbol, c.currency as currency_code, c.decimal_places, oc.mr_settlement, oc.handling_charges, oc.store_recd_amt, c.currency_id');
            if (!isset($store_id) || (isset($store_id) && empty($store_id)))
            {
                $commissons->leftJoin(Config('tables.ADDRESS_MST').' as ad', 'ad.address_id', '=', 'msm.address_id')
                        /*  ->leftjoin(Config('tables.LOCATION_COUNTRIES').' as lc', 'lc.country_id', '=', 'ad.country_id')
                          ->leftjoin(Config('tables.LOCATION_STATES').' as ls', 'ls.state', '=', 'ad.state_id')
                          ->leftjoin(Config('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'ad.district_id')
                          ->leftjoin(Config('tables.LOCATION_LOCALITIES').' as ll', 'll.locality_id', '=', 'ad.city_id') */
                        ->addSelect('msm.store_name', 'msm.store_code', 'ad.address');
            }
            $commissons = $commissons->get();
           array_walk($commissons, function(&$commisson)
            {
                $commisson->store_name = !empty($commisson->store_name) ? $commisson->store_name : '-';
                $commisson->created_on = showUTZ($commisson->created_on, 'd-M-Y H:i:s');                
                $commisson->order_amt = CommonLib::currency_format($commisson->order_amt, ['currency_symbol'=>$commisson->currency_symbol, 'currency_code'=>$commisson->currency_code,
                            'decimal_places'=>$commisson->decimal_places], true, false);
				if ($commisson->mr_settlement > 0) {
					$commisson->mr_settlement_class = 'success';
				} else {
					$commisson->mr_settlement_class = 'danger';
				}			
                $commisson->mr_settlement = CommonLib::currency_format($commisson->mr_settlement, $commisson->currency_id, true, false);
                $commisson->handling_charges = CommonLib::currency_format($commisson->handling_charges, $commisson->currency_id, true, false);
                $commisson->store_recd_amt = CommonLib::currency_format($commisson->store_recd_amt, $commisson->currency_id, true, false);
                $commisson->system_comm = CommonLib::currency_format($commisson->system_comm, $commisson->currency_id, true, false);
                $commisson->tax = CommonLib::currency_format($commisson->tax, $commisson->currency_id, true, false);
                $commisson->net_amt = CommonLib::currency_format($commisson->net_amt, $commisson->currency_id, true, false);                
                $commisson->status_class = Config('dispclass.seller.order.status.'.$commisson->status);
                $commisson->status = trans('seller/general.order.commissions.status.'.$commisson->status);
				$commisson->actions = [];
				$commisson->actions['details'] = ['url'=>route('seller.reports.instore.transaction.details', ['id'=>$commisson->order_code]), 'redirect'=>false, 'label'=>trans('seller/general.btn.details')];	   
                unset($commisson->currency_symbol);
                unset($commisson->decimal_places);
            }); 
            return $commissons;
        }
    }
	
	public function get_currency ($id)
    {
        return DB::table(Config('tables.CURRENCIES').' as c')
                        ->where('c.currency_id', $id)
                        ->first();
    }
	
	public function check_autopay (array $arr)
    {
        extract($arr);
        return DB::table(Config('tables.SUPPLIER_PAYMENT_SETTINGS').' as aps')
                        ->where('aps.supplier_id', $supplier_id)
                        ->where('aps.autopayout', Config('constants.ON'))
                        ->where('aps.is_deleted', Config('constants.OFF'))
                        ->count();
    }
	
	/* Seller Balance */
	public function wallet_balance (array $arr = [])
    {   
        extract($arr);
		$cur_code = isset($cur_code) && !empty($cur_code)? $cur_code:'';
        $qry = DB::table(Config('tables.WALLET').' as w')
                 ->leftJoin(Config('tables.ACCOUNT_BALANCE').' as ub', function($join) use($account_id, $currency_id)
                {
                    $join->on('ub.wallet_id', '=', 'w.wallet_id')
							->where('ub.account_id', '=', $account_id)
							->where('ub.currency_id', '=', $currency_id);
                }) 
                ->leftJoin(Config('tables.WALLET_LANG').' as wl', function($j)
                {
                    $j->on('wl.wallet_id', '=', 'w.wallet_id');
                    //$j->where('wl.lang_id', '=', Config('app.locale_id'));
                })
                ->leftJoin(Config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'ub.currency_id');

        if (isset($wallet_code) && $wallet_code > 0)
        {
            $qry->where('w.wallet_code', $wallet_code);
        }
        if (isset($currency_code) && $currency_code != '')
        {
            $qry->where('cur.code', $currency_code);
        }
        $qry->selectRaw('ub.current_balance, ub.tot_credit, ub.tot_debit, wl.wallet as wallet_name, w.wallet_code, wl.desc as wallet_desc, wl.terms as wallet_terms, cur.currency as currency_code, cur.currency_symbol, cur.decimal_places, wl.wallet, 0 as pending_balance, w.can_add_money');
        $qry->orderby('w.sort_order', 'asc')
                ->where('w.is_seller_wallet', Config('constants.ON'))
                ->skip(0)->take(1);
        $result = $qry->get();
		//print_r($result);exit;
        array_walk($result, function(&$r) use($cur_code, $currency_id)
        {
            $r->total = $r->current_balance + $r->pending_balance;
            $r->total = CommonLib::currency_format($r->total, $currency_id, true, false);
            if ($r->currency_code == '')
            {
                $r->currency_code = $cur_code;
            }
            $r->currency_symbol = !empty($r->currency_symbol) ? $r->currency_symbol : '';
            $r->current_balance = $r->currency_symbol.' '.number_format($r->current_balance, $r->decimal_places, '.', ',');
            $r->tot_credit = CommonLib::currency_format($r->tot_credit, $currency_id, true, false);
            $r->tot_debit = CommonLib::currency_format($r->tot_debit, $currency_id, true, false);
            $r->pending_balance = $r->currency_code.' '.number_format($r->pending_balance, $r->decimal_places, '.', ',');
            unset($r->decimal_places);
        });
        return !empty($result) ? (count($result) == 1) ? $result[0] : $result : NULL;
    }
	
	public function getOrderDetails (array $arr = array())
    {
        extract($arr);
        $order = DB::table(Config('tables.ORDERS').' as mo')
                ->Join(Config('tables.ACCOUNT_MST').' as aum', 'aum.account_id', '=', 'mo.approved_by')
                ->Join(Config('tables.ACCOUNT_DETAILS').' as aad', 'aad.account_id', '=', 'mo.approved_by')
                ->Join(Config('tables.ORDER_COMMISSION').' as oc', 'oc.order_id', '=', 'mo.order_id')
                ->Join(Config('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                ->Join(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                ->Join(Config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'mo.account_id')
                ->Join(Config('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id')
                ->Join(Config('tables.STORES').' as msm', 'msm.store_id', '=', 'mo.store_id')
                ->Join(Config('tables.ADDRESS_MST').' as add', 'add.address_id', '=', 'msm.address_id')
                ->leftJoin(Config('tables.PAY').' as py', function($lf)
                {
                    $lf->on('py.order_id', '=', 'mo.order_id');
                    //->where('py.pay_mode_id', '=', Config('constants.PAYMENT_MODES.CASH'));
                })				
                ->Join(Config('tables.CURRENCIES').' as co', 'co.currency_id', '=', 'mo.currency_id')						
				->selectRaw('order_code, mo.order_id, mo.order_date, mo.status, concat(lc.phonecode,\' \',um.mobile) mobile, CONCAT(ad.firstname,\' \',ad.lastname) as customer, um.user_code as account_code, um.email, CONCAT(aad.firstname,\' \',aad.lastname) as staff_name, aum.uname as staff_id, mo.order_type, mo.pay_through, msm.store_name as store,'.'mo.bill_amount, oc.store_recd_amt as store_received_amt, system_comm as charges, oc.tax, mr_settlement as balance, add.address, py.from_amount as received_amt, mo.currency_id, oc.sys_recd_amt as system_received_amt, py.payment_id, mo.statementline_id as statement_line, oc.net_amt')                
                ->where('mo.supplier_id', $supplier_id) 
                ->where('mo.order_code', $order_code)
                ->where('mo.is_deleted', Config('constants.OFF'));
        $order = $order->first();		
        if (!empty($order))
        {    
			$remarks = '';
			$order->status_class = Config('dispclass.order.status.'.$order->order_type.'.'.$order->pay_through.'.'.$order->status);
			//$order->status = trans('seller/general.order.status.2.'.$order->order_type.'.'.$order->pay_through.'.'.$order->status);
			if ($order->status == 1) { $order->status = 'SUCCESS'; } else { $order->status = 'PENDING'; }
			if ($order->order_type == 1)
            {            
                if ($order->pay_through == 1)
                {

                }
                elseif ($order->pay_through == 2)
                {
            
                }
                else if ($order->pay_through == 3)
                {
					
                    $remarks = trans('transactions.'.$order->statement_line.'.seller.payment_remarks', ['amount'=>CommonLib::currency_format($order->bill_amount, $order->currency_id, true, false)]);
                }
            }
			$order->transaction_id = $order->payment_id; 
			$order->order_date = showUTZ($order->order_date, 'd-M-Y h:i A');
			$order->net_amt = CommonLib::currency_format($order->net_amt, $order->currency_id, true, false);
			$order->bill_amount = $order->amount = CommonLib::currency_format($order->bill_amount, $order->currency_id, true, false);
			$order->system_received_amt = CommonLib::currency_format($order->system_received_amt, $order->currency_id, true, false);
            $order->tax = CommonLib::currency_format($order->tax, $order->currency_id, true, false);
			$order->balance = CommonLib::currency_format($order->balance, $order->currency_id, true, false);
            $order->charges = CommonLib::currency_format($order->charges, $order->currency_id, true, false);
            $order->store_received_amt = CommonLib::currency_format($order->store_received_amt, $order->currency_id, true, false);
			$order_type = $order->order_type;
			$format = trans('transactions.'.$order->statement_line.'.seller.fields');
			if ($remarks != '')
			{
				$order->remarks = $remarks;
			}
			else
			{
				$order->remarks = trans('transactions.'.$order->statement_line.'.seller.details_remarks', ['amount'=>$order->bill_amount, 'site_name'=>'Virob']);
			}
			$order->is_refundable = isset($is_refundable) ? 1 : 0;
			//return $order;
			$payment_fld = trans('transactions.'.$order->statement_line.'.seller.payment_details');
			$payment_fld_path = 'transactions.'.$order->statement_line.'.seller.payment_details.';			
			$order->order_type = trans('seller/general.order.pay_through.'.$order_type.'.'.$order->pay_through);
            $order->pay_through = trans('seller/general.order.pay_through.'.$order_type.'.'.$order->pay_through);
			//return $order;
			$payment_details = [];
            /* if (isset($order->is_refundable))
            {
                if (date('Y-m-d') > date('Y-m-d', strtotime($order->order_date.$refund_days.' days')))
                {
                    $order->is_refundable = 0;
                }
            } */
            if (!empty($payment_fld) && is_array($payment_fld))
            {
                array_walk($payment_fld, function(&$v, $k) use($order, &$payment_details, $payment_fld_path)
                {
                    if (isset($order->{$k}))
                    {
                        $payment_details[$k] = ['label'=>trans($payment_fld_path.$k, ['store_name'=>$order->store, 'site_name'=>'Virob']), 'value'=>$order->{$k}];
                    }
                });
            }
			//return $order;
            if (!empty($format) && is_array($format))
            {
                array_walk($format, function(&$v, $k) use($order)
                {
                    $v = ['label'=>$v, 'value'=>$order->{$k}];
                });
            }
            else
            {
                $format = [];
            }
			$format['payment_details'] = array_values($payment_details);
            $format['remarks'] = $order->remarks;
            $format['order_date'] = date('h:i A, d M Y', strtotime($order->order_date));
            $format['status'] = $order->status;
            $format['status_class'] = $order->status_class;
            $format['received_amount'] = $order->store_received_amt;
            $format['store_name'] = $order->store;
            $format['pay_through'] = $order->pay_through;
            $format['address'] = $order->address;
            //$format['support'] = URL('faqs');
            //$format['actions'] = [];       
        }
        return $format;
    }
	
	public function getOrderDetails_bk101018 (array $arr = array())
    {
        extract($arr);
        $order = DB::table(Config('tables.ORDERS').' as mo')
                ->Join(Config('tables.ACCOUNT_MST').' as aum', 'aum.account_id', '=', 'mo.approved_by')
                ->Join(Config('tables.ACCOUNT_DETAILS').' as aad', 'aad.account_id', '=', 'mo.approved_by')
                ->Join(Config('tables.ORDER_COMMISSION').' as oc', 'oc.order_id', '=', 'mo.order_id')
                ->Join(Config('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                ->Join(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                ->Join(Config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'mo.account_id')
                ->Join(Config('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id')
                ->Join(Config('tables.STORES').' as msm', 'msm.store_id', '=', 'mo.store_id')
                ->Join(Config('tables.ADDRESS_MST').' as add', 'add.address_id', '=', 'msm.address_id')
                ->Join(Config('tables.PAY').' as py', function($lf)
                {
                    $lf->on('py.order_id', '=', 'mo.order_id');
                    //->where('py.pay_mode_id', '=', Config('constants.PAYMENT_MODES.CASH'));
                })
                ->Join(Config('tables.CURRENCIES').' as co', 'co.currency_id', '=', 'mo.currency_id')
				//->selectRaw('order_code,mo.order_id,mo.order_date,mo.status,concat(lc.phonecode,um.mobile) mobile,CONCAT(ad.first_name,\' \',ad.last_name) as customer,um.account_code,um.email,CONCAT(aad.first_name,\' \',aad.last_name) as staff_name,aum.uname as staff_id, mo.order_type, mo.pay_through, concat(msm.store_name,\'(\',msm.store_code,\')\') as store,'.'mo.bill_amount, co.currency_symbol as ocurrency_symbol, co.decimal_places as odecimal_places, co.code as ocurrency_code,store_recd_amt as store_received_amt,system_comm as charges,tax,mr_settlement as balance,add.formated_address as address,mo.tip_amount,oc.system_comm,oc.tax,oc.net_amt,oc.sys_recd_amt system_received_amt,mo.statementline_id as statement_line')
				->selectRaw('order_code, mo.order_id, mo.order_date, mo.status, concat(lc.phonecode,um.mobile) mobile, CONCAT(ad.firstname,\' \',ad.lastname) as customer, um.user_code as account_code, um.email, CONCAT(aad.firstname,\' \',aad.lastname) as staff_name, aum.uname as staff_id, mo.order_type, mo.pay_through, msm.store_name as store,'.'mo.bill_amount, oc.store_recd_amt as collected_amount, system_comm as charges, oc.tax, mr_settlement as balance, add.address, py.from_amount as received_amt, mo.currency_id, oc.sys_recd_amt, py.payment_id, mo.statementline_id as statement_line')                
                ->where('mo.supplier_id', $supplier_id) 
                ->where('mo.order_code', $order_code)
                ->where('mo.is_deleted', Config('constants.OFF'));
        $order = $order->first();		
        if (!empty($order))
        {            
			$order->status_class = Config('dispclass.seller.order.status.'.$order->status);
			//$order->status = trans('seller/general.order.status.'.$order->order_type.'.'.$order->pay_through.'.'.$order->status);	
			if ($order->status == 1) {
				$order->status = 'SUCCESS';
			} else {
				$order->status = 'PENDING';
			}
			
            if ($order->order_type == 1)
            {
                $order->statement_line = 51;  // Order Payment Credit
                if ($order->pay_through == 1)
                {

                }
                elseif ($order->pay_through == 2)
                {

                }
                else if ($order->pay_through == 3)
                {

                }
            }           
            else
            {
                $order->statement_line = 51;
            }
            //$order->order_date = showUTZ($order->order_date, 'H:i:s A, d M y');           
            $order->order_date = showUTZ($order->order_date);           
            $order->bill_amount = CommonLib::currency_format($order->bill_amount, $order->currency_id, true, false);			 
            $order->received_amt = CommonLib::currency_format($order->received_amt, $order->currency_id, true, false);
            $order->sys_recd_amt = CommonLib::currency_format($order->sys_recd_amt, $order->currency_id, true, false);
            $order->tax = CommonLib::currency_format($order->tax, $order->currency_id, true, false);
            $order->balance = CommonLib::currency_format($order->balance, $order->currency_id, true, false);
            $order->charges = CommonLib::currency_format($order->charges, $order->currency_id, true, false);
            $order->collected_amount = CommonLib::currency_format($order->collected_amount, $order->currency_id, true, false);			            
			$order->order_type_key = trans('seller/general.order.order_type.'.$order->order_type.'.'.$order->pay_through);	
            $order_type = $order->order_type;
			$order->order_type = trans('seller/general.order.pay_through.'.$order->order_type.'.'.$order->pay_through);
			$order->pay_through = trans('seller/general.order.pay_through.'.$order_type.'.'.$order->pay_through);
			$format = trans('user/transactions.'.$order->statement_line.'.fields.retailer');   			 
			$order->remarks = trans('user/transactions.'.$order->statement_line.'.mr_details_remarks', ['amount'=>$order->bill_amount]);	
            $order->is_refundable = isset($is_refundable) ? 1 : 0;			
            if (isset($order->is_refundable))
            {
                if (date('Y-m-d') > date('Y-m-d', strtotime($order->order_date.$refund_days.' days')))
                {
                    $order->is_refundable = 0;
                }
            }
            if (!empty($format))
            {
                array_walk($format, function(&$v, $k) use($order)
                {
                    $v = ['label'=>$v, 'value'=>$order->{$k}];
                });
            }
            $format['remarks'] = $order->remarks;
            //$format['order_date'] = date('M d,Y H:i:s', strtotime($order->order_date));
            $format['order_date'] = date('h:i A d M Y', strtotime($order->order_date));
            $format['status'] = $order->status;
            $format['status_class'] = $order->status_class;
            $format['received_amount'] = $order->received_amt;
            $format['store_name'] = $order->store;       
            $format['store_address'] = $order->address;       
            $format['sys_recd_amt'] = $order->sys_recd_amt;       
            $format['collected_amount'] = $order->collected_amount;       
            $format['pg_trans_no'] = $order->payment_id;       
            $format['order_type'] = $order->order_type;       
            $format['support'] = URL('faqs');            
            $format['actions'] = [];                        
        }
        return $format;
    }
	
	public function getOrdersList (array $arr = array(), $count = false)
    {        
        extract($arr);
        $orders = DB::table(Config('tables.ORDERS').' as mo')                
                ->join(Config('tables.CURRENCIES').' as co', 'co.currency_id', '=', 'mo.currency_id')
				->join(Config('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'mo.account_id')
                ->join(Config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'mo.account_id')                
                ->where('mo.supplier_id', $supplier_id)
                ->where('mo.is_deleted', Config('constants.OFF'))                
				->selectRaw('mo.order_code, mo.order_date, mo.order_type, mo.pay_through, mo.status, CONCAT(ud.firstname,\' \',ud.lastname) as customer, mo.pay_through,    mo.currency_id, um.email, um.mobile, um.user_code, mo.bill_amount');
				
		
		//return $orders = $orders->get();			
        if (isset($from) && !empty($from))
        {
            $orders->whereDate('mo.order_date', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $orders->whereDate('mo.order_date', '<=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_term) && !empty($search_term))
        {
            $orders->where('mo.order_code', 'like', '%'.$search_term.'%')
                    ->orWhere('um.mobile', 'like', '%'.$search_term.'%');
        }
        if (isset($store_id) && !empty($store_id))
        {
            $orders->where('mo.store_id', $store_id);
        }
		if (isset($pay_through) && !empty($pay_through))
        {
            $orders->where('mo.pay_through', $pay_through);
        }
        if (isset($orderby) && isset($order))
        {
            //$orders->orderby($orderby, $order);
        }
        else
        {
            $orders->orderby('mo.order_date', 'DESC');
        }       
        if ($count)
        {
            return $orders->count();
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $orders->skip($start)->take($length);
            }
            
            if ($account_type_id == Config('constants.ACCOUNT_TYPE.SELLER'))
            {
                $orders->join(Config('tables.STORES').' as msm', 'msm.store_id', '=', 'mo.store_id')
                        ->addSelect('msm.store_name', 'msm.store_code')
                        ->leftJoin(Config('tables.ADDRESS_MST').' as ad', 'ad.address_id', '=', 'msm.address_id')
                        ->addSelect('msm.store_name', 'msm.store_code', 'ad.address');
            }
            $orders = $orders->get();
            array_walk($orders, function(&$order)
            {
                //$order->order_date = showUTZ($order->order_date, 'H:i:s A, d M y');
                $order->order_date = showUTZ($order->order_date, 'h:i A, d M Y');
                //$order->amount = CommonLib::currency_format($order->amount, $order->currency_id, true, false);
                $order->bill_amount = CommonLib::currency_format($order->bill_amount, $order->currency_id, true, false);              
				
				$order->status_class = Config('dispclass.seller.order.status.'.$order->status);
				if ($order->status == 1) {
					$order->status = 'Success';
				} else {
					$order->status = 'Pending';
				}
				//$order->status = trans('seller/general.order.status.'.$order->order_type.'.'.$order->pay_through.'.'.$order->status);			
				//$order->order_type_key = Config('constants.order.order_type.'.$order->order_type.'.'.$order->pay_through);				
				$order->order_type_key = trans('seller/general.order.order_type.'.$order->order_type.'.'.$order->pay_through);	
				$order_type = $order->order_type;
				$order->order_type = trans('seller/general.order.pay_through.'.$order->order_type.'.'.$order->pay_through);
				$order->remarks = trans('seller/reports.order_remark.'.$order_type.'.'.$order->pay_through, (array) $order);
				
				$order->actions = [];
				$order->actions['details'] = ['url'=>route('seller.reports.instore.orders.details', ['id'=>$order->order_code]), 'redirect'=>false, 'label'=>trans('seller/general.btn.details')];	                                               
            });
			return $orders;
        }
    }

	

    public function get_wallet_list ()
    {
        return DB::table(Config::get('tables.WALLET').' as wa')
					->leftjoin(Config::get('tables.WALLET_LANG').' as wl', 'wl.wallet_id', '=', 'wa.wallet_id')
                        ->where('wa.status', Config::get('constants.ON'))
                        ->where('wa.accessable', Config::get('constants.ON'))
						->selectRaw('wa.wallet_id, wa.wallet_code,wl. wallet')
                        ->get();
    }

    
	
	public function get_wallet_balance_details ($arr = array())
    {
        return DB::table(Config::get('tables.WALLET').' as w')
					->Join(Config::get('tables.WALLET_LANG').' as wl', 'wl.wallet_id', '=', 'w.wallet_id')					
					->Join(Config::get('tables.ACCOUNT_WALLET_BALANCE').' as wb', function($join) use ($arr)
					{
						$join->on('wb.wallet_id', '=', 'w.wallet_id')
								->where('wb.account_id', '=', $arr['account_id']);
					}) 
					->join(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'wb.currency_id')
					->where('w.status', Config::get('constants.ON'))
					->selectRaw('w.wallet_code, w.wallet_id, wl.wallet, wb.current_balance, c.currency, c.currency_id') 
					->get(); 
    }
	
	public function transactions (array $arr = array(), $count = false)
    {  
        $from = 0;
        $to = 0;
        $ewallet_id = 0;
        $user_id = 0;
        $display_id = 0;
        $payout_type = 0;
        extract($arr);
        $transaction_log = DB::table(Config('tables.SUPPLIER_CLIENT_TRANSACTION').' as mc')
                ->join(Config('tables.ACCOUNT_TRANSACTION').' as a', 'a.id', '=', 'mc.transaction_id')
                ->join(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'a.from_account_id')                
                ->join(Config('tables.STATEMENT_LINE').' as stl', 'stl.statementline_id', '=', 'a.statementline_id')
                ->join(Config('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'a.currency_id')
                ->join(Config('tables.WALLET').' as w', 'w.wallet_id', '=', 'a.wallet_id')
                ->leftjoin(Config('tables.WALLET_LANG').' as wl', function($join)
                {
                    $join->on('wl.wallet_id', '=', 'a.wallet_id');                    
                })
                ->where('a.is_deleted', Config('constants.NOT_DELETED'))
                ->where('a.status', '>', 0);		
        if ($account_type_id == Config('constants.ACCOUNT_TYPE.SELLER'))
        {			
            $transaction_log->where('mc.supplier_id', $supplier_id);
        }
        else if ($account_type_id == Config('constants.ACCOUNT_TYPE.MERCHANT_SUB_ADMIN'))
        {
            $transaction_log->where('mc.store_id', $store_id);
        }
        elseif ($account_type_id == Config('constants.ACCOUNT_TYPE.CASHIER'))
        {
            //$transaction_log->where('mc.client_id', $client_id);
        }
        else
        {
            $transaction_log->where('ad.account_id', $account_id);
        }		
        if (isset($trans_type) && !is_null($trans_type))
        {
            $transaction_log->where('a.transaction_type', $trans_type);
        }
        if (!empty($from))
        {
            $transaction_log->whereDate('a.created_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (!empty($to))
        {
            $transaction_log->whereDate('a.created_on', '<=', date('Y-m-d', strtotime($to)));
        }
        if (isset($search_term) && !empty($search_term))
        {
            // $transaction_log->whereRaw('(a.remark like \'%'.$search_term.'%\'  OR   st.statementline like \'%'.$search_term.'%\')');
            $transaction_log->where(function($sub) use($search_term)
            {
                $sub->where('a.remark', 'LIKE', '%'.$search_term.'%')
                        ->orwhere('st.statementline', 'LIKE', '%'.$search_term.'%')
                        ->orwhere('a.transaction_id', 'LIKE', '%'.$search_term.'%');
            });
        }
        if (!empty($currency))
        {
            $transaction_log->where('a.currency_id', $currency);
        }
        if (!empty($wallet))
        {
            $transaction_log->where('w.wallet_code', $wallet);
        }
        if (isset($orderby) && isset($order))
        {
            $transaction_log->orderby($orderby, $order);
        }
        else
        {
            $transaction_log->orderby('a.created_on', 'DESC');
            $transaction_log->orderby('a.id', 'DESC');
        }
        if ($count)
        {
            return $transaction_log->count();
        }
        else
        {
            if ((isset($start)) && (isset($length)))
            {
                $transaction_log->skip($start)->take($length);
            }
            $trans = $transaction_log->selectRaw('a.id, a.transaction_id, a.created_on, a.remark, a.transaction_type, cur.currency_symbol, cur.currency as code, cur.decimal_places, a.paid_amt as amount, stl.statementline, stl.statementline_id, CONCAT(ad.firstname,\' \',ad.lastname) as from_name, ad.profile_img as from_profile, w.wallet_code, wl.wallet, a.status, stl.transaction_key')
                    ->get();
		
	        array_walk($trans, function(&$t)
            {
                $t->created_on = showUTZ($t->created_on, 'd M, Y');
                //$t->amount = CommonLib::currency_format($t->amount, ['currency_symbol'=>$t->currency_symbol, 'value_type'=>($t->transaction_type == 1 ? ' + ' : ' - '), 'decimal_places'=>$t->decimal_places]).' '.$t->code;
                $t->trans_type = $t->transaction_type;
                $t->transaction_type = ($t->transaction_type == 1) ? '+' : '-';
                $t->amount = $t->transaction_type.' '.$t->currency_symbol.' '.$t->amount;
                $t->from_profile = !empty($t->from_profile) ? asset(Config('constants.ACCOUNT.PROFILE_IMG.WEB.160x160').$t->from_profile) : asset(Config('constants.ACCOUNT.PROFILE_IMG.DEFAULT'));
                $t->statementline = $t->statementline;

                if (!empty($t->remark))
                {
                    $t->remark = json_decode($t->remark);
                    $t->statementline = trans('transactions.'.$t->statementline_id.'.seller.statement_line', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
                    $t->remark = trans('transactions.'.$t->statementline_id.'.seller.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
                }
                else
                {
                    $t->remark = $t->statementline;
                }

                $t->actions = [];
                //$t->actions[] = ['url'=>route('retailer.wallet.transhistory.details', ['id'=>$t->transaction_id]), 'label'=>trans('general.btn.details')];
                $t->status_class = config('dispclass.transaction.status.'.$t->status);
                $t->status = trans('db_trans.transaction.status.'.$t->status);
                unset($t->statementline_id);
                //unset($t->currency_symbol);
                unset($t->code);
                unset($t->decimal_places);
                unset($t->transaction_type);
            });
			//print_r($trans);exit;
            return $trans;
        }
    }
	
	

}
