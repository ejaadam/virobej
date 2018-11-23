<?php
namespace App\Models\Api\User;
use DB;
use App\Models\BaseModel;
use CommonLib;
use Log;

class WalletModel extends BaseModel {
	
    public function __construct() 
	{
        parent::__construct();		
		
    }
	
	public function wallet_balance (array $arr = [])
    {				
        $currency_id = 0;
        extract($arr);
        $qry = DB::table($this->config->get('tables.WALLET').' as w')
                ->Join($this->config->get('tables.ACCOUNT_BALANCE').' as ub', function($join) use($account_id, $currency_id)
                {
                    $join->on('ub.wallet_id', '=', 'w.wallet_id');
                    $join->where('ub.account_id', '=', $account_id);
                    if (isset($currency_id) && $currency_id > 0)
                    {
                        $join->where('ub.currency_id', '=', $currency_id);
                    }
                })
                ->Join($this->config->get('tables.WALLET_LANG').' as wl', function($join)
                {
                    $join->on('wl.wallet_id', '=', 'w.wallet_id');                    
                })
                ->Join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'ub.currency_id'); 

        if (isset($is_affiliate) && !empty($is_affiliate))
        {
			/* For Affiliate */
            $qry->whereIn('w.wallet_id', [1,2,3]);
        } else {
			/* For User */
			$qry->whereIn('w.wallet_id', [1]);
			//$qry->whereIn('w.wallet_id', [1,2,3]);
		}      
        $qry->selectRaw('ub.current_balance, ub.tot_credit, ub.tot_debit, w.wallet_code, cur.currency as currency_code, wl.wallet_id, wl.wallet, cur.currency_id');        
        $result = $qry->get();

        $currency = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE').' as as')
                ->Join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'as.currency_id')
                ->where('as.account_id', '=', $account_id)->selectRaw('cur.currency_id, cur.currency as code')
                ->first();
		$wallets=[];
        //array_walk($result, function(&$r) use($currency, &$wallets, $is_affiliate)
        array_walk($result, function(&$r) use($currency, &$wallets)
        {
            if (empty($r->currency_code))
            {
                $r->currency_id = $currency->currency_id;
                $r->currency_code = $currency->code;                
            }		
			if ($r->current_balance > 0) { $r->can_redeem = 1; } else { $r->can_redeem = 0;	}	
            $r->current_balance = CommonLib::currency_format($r->current_balance, $r->currency_id, true, false);
            $r->tot_credit = CommonLib::currency_format($r->tot_credit, $r->currency_id, true, false);
            $r->tot_debit = CommonLib::currency_format($r->tot_debit, $r->currency_id, true, false); 
			$r->visible = 1;				
            unset($r->currency_id);            
            unset($r->tot_credit);            
            unset($r->tot_debit);          
			if ($r->wallet_code == 'vim') {
				$wallets = $r->current_balance;
				//unset($result[$key]);
				$r = null;
			}	
				
        });        
		if ($result){
			return ['vimoney_bal'=>$wallets, 'wallets'=>array_values(array_filter($result))];		
		} else {
			return false;
		}
    }
	
	public function transactions (array $arr = array(), $count = false)
    {
        $from = 0;
        $to = 0;
        $currency_id = 0;
        $wallets = 0;
        $user_id = 0;
        $display_id = 0;
        $payout_type = 0;        
        extract($arr);
        $transaction_log = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION').' as a')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as ptl', function($ptl)
                {
                    $ptl->on('ptl.payment_type_id', '=', 'a.payment_type_id');                    
                })
                ->join($this->config->get('tables.WALLET').' as w', 'w.wallet_id', '=', 'a.wallet_id')
                ->join($this->config->get('tables.WALLET_LANG').' as wt', function($join)
                {
                    $join->on('wt.wallet_id', '=', 'a.wallet_id');                    
                })
				->leftjoin($this->config->get('tables.STATEMENT_LINE').' as e', function($join)
                {
                    $join->on('e.statementline_id', '=', 'a.statementline_id');                    
                })                
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'a.currency_id')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'a.account_id')
                ->where('a.is_deleted', $this->config->get('constants.NOT_DELETED'));
                
        $transaction_log->where('a.account_id', $account_id);        
		
        if (isset($status))
        {
            $transaction_log->where('a.status', '=', $this->config->get('constants.TRANSACTION_STATUS.CONFIRMED'));
        }
        else
        {
            //$transaction_log->where('a.status', '>', 0);
        }
        if (in_array($account_id, [142, 116, 113])) {
		    $transaction_log->whereDate('a.created_on', '>=', date('Y-m-d'));
        }
        if (!empty($from))
        {
            //$transaction_log->whereDate('a.created_on', '>=', getGTZ($from, 'Y-m-d'));
            $from = date('Y-m-d', strtotime('+330 minutes', strtotime($from))); 
            $transaction_log->whereDate('a.created_on', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (!empty($to))
        {
            //$transaction_log->whereDate('a.created_on', '<=', getGTZ($to, 'Y-m-d'));
            $to = date('Y-m-d', strtotime('+330 minutes', strtotime($to))); 
            $transaction_log->whereDate('a.created_on', '<=', getGTZ($to, 'Y-m-d'));
        }
		if (!empty($month))
        {
            $transaction_log->whereMonth('a.created_on', '=', $month);
        }
		if (!empty($year))
        {
            $transaction_log->whereYear('a.created_on', '=', $year);
        }
        if (isset($search_term) && !empty($search_term))
        {

            $transaction_log->where(function($sub) use($search_term)
            {
                $sub->Where('a.transaction_id', 'like', $search_term)
                        ->orwhereRaw('(a.remark like \'%'.$search_term.'%\'  OR   e.statementline like \'%'.$search_term.'%\')');
            });
        }
        if (!empty($currency_id))
        {
            $transaction_log->where('a.currency_id', $currency_id);
        }
        if (!empty($wallet))
        {
            if (is_array($wallet))
            {
                $transaction_log->whereIn('w.wallet_code', $wallet);
            }
            else
            {
                $transaction_log->where('w.wallet_code', $wallet);
            }
        }
        if ($payout_type)
        {
            if (is_numeric($payout_type))
            {
                $transaction_log->where('a.payment_type', '=', $payout_type);
            }
            else if (is_string($payout_type))
            {
                $transaction_log->where('b.slug', '=', $payout_type);
            }
        }
        if (isset($post_type) && isset($relation_id))
        {
            $transaction_log->where('a.post_type', '=', $post_type)
                    ->where('a.relation_id', '=', $relation_id);
        }
		if (isset($transaction_type))
        {
            $transaction_log->where('a.transaction_type', '=', $transaction_type);
        }
		
		if ($account_type == 2) {			
			$transaction_log->whereNotIn('a.statementline_id', [44,45]);
		}
		if (isset($filter) && !empty($filter))
        {
            if ($filter == $this->config->get('constants.TRANSACTIONS.ALL')) {
					
			}
			if ($filter == $this->config->get('constants.TRANSACTIONS.PURCHASES')) {
					$transaction_log->whereIn('a.statementline_id', [3,27]);
			}
			if ($filter == $this->config->get('constants.TRANSACTIONS.REFUNDS')) {				
					$transaction_log->whereIn('a.statementline_id', [21,39]);
			}
			if ($filter == $this->config->get('constants.TRANSACTIONS.TOPUPS')) {
					$transaction_log->whereIn('a.statementline_id', [100]);       // There is no transactions, given dummy value 100.
			}
			if ($filter == $this->config->get('constants.TRANSACTIONS.COUPONS')) {
					$transaction_log->whereIn('a.statementline_id', [100]);	      // There is no transactions, given dummy value 100.
			}
			if ($filter == $this->config->get('constants.TRANSACTIONS.WITHDRAWALS')) {
					$transaction_log->whereIn('a.statementline_id', [7,12]);
			}
        } 
        if ($count)
        {
            return $transaction_log->count();
        }
        else
        {
            if (isset($orderby) && !empty($orderby) && isset($order) && !empty($order))
            {
                $transaction_log->orderby($orderby, $order);
            }
            else
            {
                $transaction_log->orderby('a.id', 'DESC');
            }
            if (isset($length))
            {
                $transaction_log->skip($start)->take($length);
            }		
			
            $trans = $transaction_log->selectRaw('a.id, a.created_on, a.remark, CAST(a.transaction_type AS CHAR) as transaction_type, a.transaction_id, cur.currency_id, a.amt as amount, a.current_balance, a.status, e.statementline, e.statementline_id, wt.wallet, ptl.payment_type as payout_name, a.relation_id, a.paid_amt, ad.profile_img')->get();			
			
			if (!empty($trans))
			{			
				$res = ['pending'=>[],'confirmed'=>[],'cancelled'=>[]];
				array_walk($trans, function(&$t) use (&$trans, &$res, $account_type)
				{
					if ($t->transaction_type == 0) { $symbol = '-'; } else { $symbol = '+'; }
					$status = $t->status;
					//$t->created_on = showUTZ($t->created_on, 'h.i A, d M Y');
					$t->created_on = date('h:i A, d M y',strtotime('+330 minutes', strtotime($t->created_on))); 
					$t->status_class = trans('user/general.transactions.status_class.'.$t->status);				
					$t->status = $this->config->get('constants.TRANSACTION.'.$t->status) ;
					//$t->status = trans('general.transactions.status.'.$t->status);					
					$t->transaction = !empty($t->transaction_type) ? 'CREDIT' : 'DEBIT';       
					if ($account_type == 2) {		
						$t->amount = $symbol . ' ' . CommonLib::currency_format($t->amount, $t->currency_id, true, false);	
					} elseif ($account_type == 3 || $account_type == 5) {
						$t->amount = $symbol . ' ' . CommonLib::currency_format($t->paid_amt, $t->currency_id, true, false);	
					}	
					//$t->paid = floatval($t->paidamt);
					//$t->paidamt = CommonLib::currency_format($t->paidamt, $t->currency_id, true, false);	
					$t->current_balance = CommonLib::currency_format($t->current_balance, $t->currency_id, true, false);
					$t->image = asset('resources/uploads/default.png');
					if (!empty($t->remark))
					{					
						$t->remark = json_decode($t->remark);						
						
						if (!in_array($t->statementline_id, [44,45])) {
						//if ($account_type == 2) {
							$t->statementline = trans('transactions.'.$t->statementline_id.'.user.statement_line', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
							$t->remark = trans('transactions.'.$t->statementline_id.'.user.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
						} else {
							$t->statementline = trans('transactions.'.$t->statementline_id.'.seller.statement_line', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
							$t->remark = trans('transactions.'.$t->statementline_id.'.seller.remarks', array_merge((array) $t->remark->data, array_except((array) $t, ['remark'])));
							if ($t->profile_img != 'default.png') {
								$t->image = asset($this->config->get('path.ACCOUNT.PROFILE_IMG.WEB.100x100').$t->profile_img);
							}
						}
					}
					else
					{
						//$t->remark = $t->statementline;
					}       
					unset($t->profile_img);
					
					switch ($t->statementline_id)
					{
						case $this->config->get('stline.CASHBACK.CREDIT'):											
							$details = DB::table($this->config->get('tables.CASHBACKS').' as c')									
									->join($this->config->get('tables.ORDERS').' as mo', 'mo.order_id', '=', 'c.order_id')
									->join($this->config->get('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'mo.supplier_id')
									->join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
									->where('c.cashback_id', $t->relation_id)
									 ->selectRaw('ms.store_logo, mm.logo as mrlogo')
									->first();						   
							if (!empty($details))
							{
								if ($details->store_logo != null)
								{
									$t->image = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$details->store_logo);
								}															
							}
							break; 
						case $this->config->get('stline.REDEEM.DEBIT'):
						
							$details = DB::table($this->config->get('tables.REDEEMS').' as r')
									 ->join($this->config->get('tables.ORDERS').' as mo', 'mo.order_id', '=', 'r.order_id')
									 ->join($this->config->get('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'mo.supplier_id')
									 ->join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
									 ->where('redeem_id', $t->relation_id)
									 ->selectRaw('mm.logo as mrlogo, ms.store_logo')
									 ->first();
							if (!empty($details))
							{
								if ($details->store_logo != null)
								{
									$t->image = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$details->store_logo);
								}															
							}
							break;
							
						case $this->config->get('stline.ORDER_PAYMENT.DEBIT'):						
							$details = DB::table($this->config->get('tables.PAY').' as pay')
									 ->join($this->config->get('tables.ORDERS').' as mo', 'mo.order_id', '=', 'pay.order_id')
									 ->join($this->config->get('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'mo.supplier_id')
									 ->join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
									 ->where('pay.pay_id', $t->relation_id)
									 ->selectRaw('mm.logo as mrlogo, ms.store_logo')
									 ->first();
							if (!empty($details))
							{
								if ($details->store_logo != null)
								{
									$t->image = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$details->store_logo);
								}															
							}
							break;
					}
					
					
					if ($status == 0) {
						$res['pending'][] = $t;
					} elseif ($status == 1) {
						$res['confirmed'][] = $t;
					} elseif ($status == 2) {
						$res['cancelled'][] = $t;
					}			
					
					unset($t->id);
					//unset($t->statementline_id);                
					unset($t->currency_id);              
				});
			}	
            return $res;
            //return $trans;
        }
    }       
	
	public function getTransactionDetail (array $arr = array())
    {	
        extract($arr);
        $trans = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION').' as a')
                ->join($this->config->get('tables.ACCOUNT_MST').' as amst', function($ptl)
                {
                    $ptl->on('amst.account_id', '=', 'a.account_id')
                    ->where('amst.is_deleted', '=', $this->config->get('constants.OFF'));
                })
               ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'amst.account_id')
                ->join($this->config->get('tables.PAYMENT_TYPES').' as ptl', function($ptl)
                {
                    $ptl->on('ptl.payment_type_id', '=', 'a.payment_type_id');                    
                })
                ->join($this->config->get('tables.WALLET').' as w', 'w.wallet_id', '=', 'a.wallet_id')
                ->join($this->config->get('tables.WALLET_LANG').' as wt', function($join)
                {
                    $join->on('wt.wallet_id', '=', 'a.wallet_id');                    
                })
                ->join($this->config->get('tables.STATEMENT_LINE').' as e', 'e.statementline_id', '=', 'a.statementline_id')        
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'a.currency_id') 
                ->where('a.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->where('a.status', '>', 0);
		if ($account_type == 2) {		
            $trans->where('a.account_id', $account_id);
		}
        $trans = $trans->where('a.transaction_id', $id)
						->selectRaw('a.created_on, a.status, w.wallet_code, a.remark, a.post_type, a.relation_id, a.transaction_type, a.transaction_id, amst.user_code as user_account_code, concat_ws(" ",ad.firstname,ad.lastname) as user_fullname, cur.currency as code, cur.currency_symbol, cur.decimal_places, a.amt as amount, a.handle_amt as handleamt, a.paid_amt as paidamt, a.tax, a.current_balance, e.transaction_key, e.statementline, e.statementline_id, wt.wallet, wt.wallet as payment_type, a.currency_id')                
						->first();
		//return $trans;		
        if (!empty($trans))
        {
			$res = [];
            $withdraw_amt = $trans->amount;
			$handleamt = $trans->handleamt;
            $paidamt = $trans->paidamt;
            $tax = $trans->tax;
			//$trans->created_on = showUTZ($trans->created_on, 'h:i A, d M y');    
			$trans->created_on = date('h:i A, d M y',strtotime('+330 minutes', strtotime($trans->created_on))); 	
			$status_id = $trans->status;
            $trans->status_class = trans('general.transactions.status_class.'.$trans->status);
            $trans->status = trans('general.transactions.status.'.$trans->status);           
			$trans_symbol = $trans->transaction_type == 1 ? '+' : '-';
            $trans->amount = CommonLib::currency_format($trans->amount, $trans->currency_id, true, false);
            $trans->handleamt = ' - ' . CommonLib::currency_format($trans->handleamt, $trans->currency_id, true, false);
            $trans->paidamt = $trans_symbol . ' ' . CommonLib::currency_format($trans->paidamt, $trans->currency_id, true, false);
            $trans->tax = ' - ' . CommonLib::currency_format($trans->tax, $trans->currency_id, true, false);          
            
            if (!empty($trans->remark))
            {			
				$trans->site_name = $this->siteConfig->site_name;
                $trans->remark = json_decode($trans->remark);
				if (!in_array($trans->statementline_id, [44,45])) {
					$trans->type = $trans->remark = trans('transactions.'.$trans->statementline_id.'.user.details_remarks', array_merge((array) $trans->remark->data, array_except((array) $trans, ['remark'])));
				} else {
					$trans->type = $trans->remark = trans('transactions.'.$trans->statementline_id.'.seller.details_remarks', array_merge((array) $trans->remark->data, array_except((array) $trans, ['remark'])));
				}				
            }            
            			
			//return $trans;
            switch ($trans->statementline_id)
            {                
                case $this->config->get('stline.REDEEM.DEBIT'):
                    $details = DB::table($this->config->get('tables.REDEEMS').' as r')
                             ->join($this->config->get('tables.ORDERS').' as mo', 'mo.order_id', '=', 'r.order_id')
                             ->join($this->config->get('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'mo.supplier_id')
                             ->join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
                             ->join($this->config->get('tables.PAY').' as p', 'p.order_id', '=', 'mo.order_id')
                             ->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mo.approved_by')
                             ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'r.currency_id')
                             ->leftjoin($this->config->get('tables.ADDRESS_MST').' as adr', function($lf)
								{
									$lf->on('adr.address_id', '=', 'ms.address_id')->where('adr.address_type_id', '=', 1);
								}) 
							 ->leftjoin($this->config->get('tables.LOCATION_CITY').' as lc', 'lc.city_id', '=', 'adr.city_id')
                            ->where('redeem_id', $trans->relation_id)							
                            ->selectRaw('r.redeem_amount, mo.order_code, mo.bill_amount, p.to_amount as amount_due, ms.store_code, ms.store_name, am.uname as staff_id, mm.logo as mrlogo, mm.supplier_code as mrcode, cur.currency as code, cur.decimal_places, cur.currency_id as id, cur.currency_symbol, mo.order_id, adr.address as formated_address, ms.store_logo, mo.payment_status, mo.pay_through, lc.city')
                            ->first();
                    if (!empty($details))
                    {
						unset($trans->transaction_id);
						 $trans->city = $details->city;
                        $trans->store_code = $details->store_code;
                        $trans->store_name = $details->store_name;
                        $trans->staff_id = $details->staff_id;
                        $trans->mrcode = $details->mrcode;
						$res['mrcode'] = $trans->mrcode;
                        $trans->order_code = $details->order_code;
						$trans->order_id = $details->order_id;
						$trans->payment_status_class = trans('general.transactions.status_class.'.$details->payment_status);
                        $trans->payment_status = trans('general.transactions.status.'.$details->payment_status);
                        $trans->logo = asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$details->mrlogo);
                        $trans->address = $details->formated_address;
                        $trans->bill_amount = $trans->amount = CommonLib::currency_format($details->bill_amount, $details->id, true, false);
                        $trans->redeem_amount = CommonLib::currency_format($details->redeem_amount, $details->id, true, false);

                        $pay = DB::table($this->config->get('tables.PAY').' as p')
                                         ->leftjoin($this->config->get('tables.WALLET_LANG').' as wl', 'wl.wallet_id', '=', 'p.from_wallet_id')
                                        ->leftjoin($this->config->get('tables.PAYMENT_TYPES').' as ptl', 'ptl.payment_type_id', '=', 'p.payment_type_id')
                                        ->where('p.order_id', $details->order_id)
                                        ->where('p.is_deleted', $this->config->get('constants.OFF'))
                                        ->selectRaw('p.to_amount as amount, p.payment_type_id, wl.wallet, ptl.payment_type, p.status,wl.wallet_id') 
										->get();
						$wallet_codes = array_flip($this->config->get('constants.WALLETS'));
                        array_walk($pay, function(&$redeem, $key) use (&$trans, $wallet_codes, $details)
                        {
                            $redeem->paid_amount = $redeem->redeemed_amount = CommonLib::currency_format($redeem->amount, $details->id, true, false);
                            if ($redeem->wallet_id != '')
                            {
                                $trans->$wallet_codes[$redeem->wallet_id] = '- '.$redeem->paid_amount;
                            }
                            else
                            {
                                $trans->received_amt = '- '.$redeem->paid_amount;
                            }
                            unset($redeem->amount);
                            unset($redeem->wallet);
                            unset($redeem->payment_type_id);
                        });
                      //return $trans;
                    }
                    break;
				
				case $this->config->get('stline.ORDER_PAYMENT.DEBIT'):

                    $qry = DB::table($this->config->get('tables.PAY').' as p')
                            ->join($this->config->get('tables.ORDERS').' as mo', 'mo.order_id', '=', 'p.order_id')
                            ->join($this->config->get('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'mo.supplier_id')
                            ->join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'p.from_currency_id')
                            ->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'p.payment_type_id')
                            ->leftJoin($this->config->get('tables.WALLET').' as w', 'w.wallet_id', '=', 'p.from_wallet_id')
                            ->join($this->config->get('tables.APP_PAYMENT_MODES').' as apm', 'apm.pay_mode_id', '=', 'p.pay_mode_id')
							->leftjoin($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE').' as pgr', function($pgr)
                            {
                                $pgr->on('pgr.relative_post_id', '=', 'p.pay_id');
                            })
                            ->leftjoin($this->config->get('tables.ADDRESS_MST').' as adr', function($lk)
                            {
                                $lk->on('adr.address_id', '=', 'ms.address_id')
                                ->where('adr.address_type_id', '=', '1');
                            })
                            ->leftjoin($this->config->get('tables.LOCATION_CITY').' as lc', 'lc.city_id', '=', 'adr.city_id')
                            ->where('p.pay_id', $trans->relation_id)
                            //->where('mo.order_id', $trans->relation_id)
                            ->where('p.is_deleted', $this->config->get('constants.OFF'))
							->selectRaw('cur.currency as code, cur.decimal_places, cur.currency_symbol, mo.order_code, mo.bill_amount, mm.supplier_code as mrcode, ms.store_code, ms.store_name, ms.store_logo, mm.logo as mrlogo, adr.address, p.from_amount, p.order_id, p.status as payment_status, IF(p.pay_mode_id!=2,w.wallet_code,apm.code) as payment_mode, pt.payment_key as payment_code, pgr.response, cur.currency_id as id, lc.city, mo.pay_through');                        
                    $details = $qry->first();
                    if (!empty($details))
                    {
                        $trans->mrcode = $details->mrcode;
                        $trans->address = $details->address;
                        $trans->order_code = $details->order_code;
                        $trans->store_name = $details->store_name;
                        $trans->store_code = $details->store_code;
                        $trans->city = $details->city;
						if ($details->store_logo != null)
                        {
                            $logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$details->store_logo);
                        }
                        else
                        {
                            $logo = asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$details->mrlogo);
                        }
						$trans->logo = $logo;
						$trans->bill_amount = CommonLib::currency_format($details->bill_amount, $details->id, true, false);
						$trans->payment_status_class = trans('general.transactions.status_class.'.$details->payment_status);
                        $trans->payment_status = trans('general.order_payment_status.'.$details->payment_status);                        
                        $lang = trans('transactions.'.$trans->statementline_id.'.user.payment_details');
                        if (isset($lang[$details->payment_mode]))
							
                            $trans->payment_details[$details->payment_mode] = ['label'=>$lang[$details->payment_mode], 'value'=>CommonLib::currency_format($details->from_amount, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'value_type'=>'-', 'decimal_places'=>$details->decimal_places])];			 
							 
                        
						$res['mode'] = $trans->wallet;	
						
                    }
                    break;
                
                case $this->config->get('stline.CASHBACK.CREDIT'):					
							
                    $details = DB::table($this->config->get('tables.CASHBACKS').' as c')
                            ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'c.currency_id')
                            ->join($this->config->get('tables.ORDERS').' as mo', 'mo.order_id', '=', 'c.order_id')
                            ->join($this->config->get('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'mo.supplier_id')
                            ->join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
                            //->join($this->config->get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mo.approved_by')
                           /*  ->leftjoin($this->config->get('tables.PAY').' as py', function($jk)
                            {
                                $jk->on('py.order_id', '=', 'mo.order_id')->where('py.pay_mode_id', '=', 1);
                            }) */
                            ->leftjoin($this->config->get('tables.ADDRESS_MST').' as adr', function($lk)
                            {
                                $lk->on('adr.address_id', '=', 'ms.address_id')->where('adr.address_type_id', '=', 1);
                            }) 
                            ->leftjoin($this->config->get('tables.LOCATION_CITY').' as lc', 'lc.city_id', '=', 'adr.city_id')
                            ->where('c.cashback_id', $trans->relation_id)	
							->selectRaw('mo.order_id, cur.currency as code, cur.decimal_places, cur.currency_symbol, mo.payment_status, mo.order_code, mo.bill_amount, mm.supplier_code as mrcode, ms.store_code, ms.store_name, c.status as cashback_status, ms.store_logo, mm.logo as mrlogo, adr.address, c.transaction_id, mo.pay_through, lc.city')
							// ->selectRaw('c.cashback_amount, cur.currency as code, cur.decimal_places, cur.currency_id as id, cur.currency_symbol, mo.order_id, mo.payment_status, mo.order_code, mo.bill_amount, mm.supplier_code as mrcode, ms.store_code, ms.store_name, am.uname as staff_id, c.status as cashback_status, ms.store_logo, mm.logo as mrlogo, adr.address, py.from_amount as pay_at_outlet, c.transaction_id')
                            ->first();
                   
                    if (!empty($details))
                    {
						if ($details->pay_through == 3) {
							unset($trans->transaction_id);
						}
                        $trans->mrcode = $details->mrcode;                        
                        $trans->order_code = $details->order_code;
                        $trans->city = $details->city;
						if ($details->pay_through != 3) {
                        $trans->bill_amount = CommonLib::currency_format($details->bill_amount, ['currency_symbol'=>$details->currency_symbol, 'currency_code'=>$details->code, 'decimal_places'=>$details->decimal_places]);
						}
						$trans->status_class = trans('user/general.transactions.status_class.'.$details->cashback_status);
                        $trans->status = trans('user/general.transactions.status.'.$details->cashback_status);
						$details->payment_status_class = trans('user/general.transactions.status_class.'.$details->payment_status);
                        $details->payment_status = trans('user/general.order_payment_status.'.$details->payment_status);						
						if ($details->store_logo != null)
                        {
                            $logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$details->store_logo);
                        }
                        else
                        {
                            $logo = asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$details->mrlogo);
                        }
						$trans->logo = $logo;
						$trans->store_name = $details->store_name;
						$trans->store_code = $details->store_code;
						$trans->address = $details->address;
						$trans->order_id = $details->order_id;                             
                    }
                    break; 
                case $this->config->get('stline.WITHDRAW.DEBIT'):
				case $this->config->get('stline.WITHDRAW_CANCEL.CREDIT'):
                    $details = DB::table($this->config->get('tables.WITHDRAWAL_MST').' as wm')
                            ->where('wd_id', $trans->relation_id)
                            ->selectRaw('account_info')
                            ->first();
                    $trans->transaction_type = trans('transactions.'.$trans->statementline_id.'.user.transaction_type', (array) $trans);
                    if (!empty($details))
                    {
                        $trans->account_info = json_decode($details->account_info);
                        if (!empty($trans->account_info))
                        {
                            array_walk($trans->account_info, function(&$a, $k)
                            {
                                $a = ['label'=>trans('user/withdrawal.account_details.'.$k), 'value'=>$a];
                            });
                        }
                    }
                    $d = trans('transactions.'.$trans->statementline_id.'.user.fields');
                    $d_properties = trans('transactions.'.$trans->statementline_id.'.user.properties');
                    $d_properties = is_array($d_properties) ? $d_properties : [];
                    if (is_array($d))
                    {
                        array_walk($d, function(&$v, $k) use($trans, $d_properties)
                        {
                            $v = ['label'=>$v, 'value'=>$trans->{$k}];
                            if (!$this->config->get('app.is_api') && array_key_exists($k, $d_properties))
                            {
                                foreach ($d_properties[$k] as $pro=> $field)
                                {
                                    $v[$pro] = $trans->{$field};
                                }
                            }
                        });
                        if (isset($trans->account_info))
                        {
                            $d = array_merge($d, (array) $trans->account_info);
                        }
                        $res = array_merge((array) $trans, $d);
                        if (isset($trans->account_info))
                        {
                            $d = $trans->account_info;
                        }
                        $res = ['remark'=>$trans->remark, 'status'=>$trans->status, 'transaction_type'=>$res['transaction_type'], 'status_class'=>$trans->status_class, 'created_on'=>$trans->created_on, 'from'=>$trans->wallet, 'to'=>$d, 'trans_ids'=>['wallet_trans_id'=>$trans->transaction_id, 'bank_trans_id'=>$trans->transaction_id]];
                        return ['res'=>$res, 'statementline_id'=>$trans->statementline_id];
                    }
                    break;
				case $this->config->get('stline.ORDER_PAYMENT_COMMISSION.DEBIT'):
                    $trans->commission_amount = $trans->amount;
                    $details = DB::table($this->config->get('tables.ORDERS').' as mo')
                                    ->Join($this->config->get('tables.ACCOUNT_MST').' as cu', 'cu.account_id', '=', 'mo.account_id')
                                    ->Join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
                                    ->Join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                                    ->join($this->config->get('tables.ORDER_COMMISSION').' as oc', 'oc.order_id', '=', 'mo.order_id')
                                    ->where('mo.order_id', $trans->relation_id)
                                    ->selectRaw('oc.store_recd_amt as store_received_amt, CONCAT(ad.firstname,\' \',ad.lastname) as full_name, cu.mobile, cu.email, ms.store_name, cu.user_code as account_code, mo.order_code, mo.pay_through, ad.profile_img')->first();
                    $trans->remark = trans('transactions.'.$trans->statementline_id.'.seller.details_remarks', ['amount'=>$trans->amount, 'store_name'=>$details->store_name]);
                    $trans->bill_amount = $trans->amount;
                    $trans->customer = $details->full_name;
                    $trans->email = $details->email;
                    $trans->mobile = $details->mobile;
                    $trans->account_code = $details->account_code;
                    $trans->order_code = $details->order_code;
                    $trans->profile_img = asset($this->config->get('path.ACCOUNT.PROFILE_IMG.WEB.100x100').$details->profile_img);
                    //$trans->store_received_amt = CommonLib::currency_format($details->store_received_amt, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>('-'), 'decimal_places'=>$trans->decimal_places]);
                    $trans->store_received_amt = CommonLib::currency_format($details->store_received_amt, $trans->currency_id, true, false);
                    break;	
				case $this->config->get('stline.ORDER_PAYMENT_TO_MERCHANT.CREDIT'):
                    $details = DB::table($this->config->get('tables.ORDERS').' as mo')
                                    ->Join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
                                    ->Join($this->config->get('tables.ACCOUNT_MST').' as cu', 'cu.account_id', '=', 'mo.account_id')
                                    ->Join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                                    ->join($this->config->get('tables.ORDER_COMMISSION').' as oc', 'oc.order_id', '=', 'mo.order_id')
                                    ->leftjoin($this->config->get('tables.ADDRESS_MST').' as adr', function($lk)
									{
										$lk->on('adr.address_id', '=', 'ms.address_id')->where('adr.address_type_id', '=', 1);
									}) 
									->leftjoin($this->config->get('tables.LOCATION_CITY').' as lc', 'lc.city_id', '=', 'adr.city_id')
                                    ->where('mo.order_id', $trans->relation_id)
                                    ->selectRaw('mo.order_code, oc.store_recd_amt as store_received_amt, CONCAT(ad.firstname,\' \',ad.lastname) as full_name, cu.mobile, cu.email, ms.store_name, ms.store_code, ms.store_logo, cu.user_code as account_code, lc.city, mo.pay_through, ad.profile_img')
									->first();					
                    $trans->order_code = $details->order_code;
                    $trans->bill_amount = $trans->amount;
                    $city = isset($details->city) ? ' - ' . $details->city : '';
                    $trans->store_name = $details->store_name . $city;
                    $trans->customer = $details->full_name;
                    $trans->email = $details->email;
                    $trans->mobile = $details->mobile;
                    $trans->account_code = $details->account_code;
                    $trans->profile_img = asset($this->config->get('path.ACCOUNT.PROFILE_IMG.WEB.100x100').$details->profile_img);
                    if ($details->store_received_amt > 0) {
						$trans->store_received_amt = CommonLib::currency_format($details->store_received_amt, ['currency_symbol'=>$trans->currency_symbol, 'currency_code'=>$trans->code, 'value_type'=>('-'), 'decimal_places'=>$trans->decimal_places]);
					} else {
						unset($details->store_received_amt);
					}
					$trans->address = '';
					$trans->mrcode = '';
					$trans->store_code = $details->store_code;
					$trans->logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$details->store_logo);
                    break;	
                default :
                    Log::error('Transaction Details Not Configured for Statementline ID: '.$trans->statementline_id);
                    return abort(500, 'Transaction Details Not Configured for Statementline ID: '.$trans->statementline_id);
            }			
			//return $trans;
            if(!empty($trans))
            {
				if (isset($trans->store_name))
                {
                    $trans->city = isset($trans->city) ? ' - ' . $trans->city : '';    
                    $res['store_name'] = $trans->store_name . $trans->city;
                    $res['store_code'] = $trans->store_code;
                    $res['address'] = $trans->address;
                    $res['logo'] = $trans->logo;                    				
                    $res['mrcode'] = $trans->mrcode;
                }
                if (isset($trans->profile_img))
                {
                    $res['profile_img'] = $trans->profile_img;
                }
				
				if (isset($trans->order_id))
                {
                    $payments = DB::table($this->config->get('tables.PAY').' as p')
                            ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'p.from_currency_id')
							->join($this->config->get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'p.payment_type_id')                           
							->leftJoin($this->config->get('tables.WALLET').' as w', 'w.wallet_id', '=', 'p.from_wallet_id')
                            ->join($this->config->get('tables.APP_PAYMENT_MODES').' as apm', 'apm.pay_mode_id', '=', 'p.pay_mode_id')
                            ->leftjoin($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE').' as pgr', function($pgr)
                            {
                                $pgr->on('pgr.relative_post_id', '=', 'p.pay_id');
                            }) 
                            ->where('p.order_id', $trans->order_id)
                            ->where('p.is_deleted', $this->config->get('constants.OFF'))
                            ->selectRAW('cur.currency as code, cur.decimal_places, cur.currency_symbol, p.from_amount, p.status as payment_status, pt.payment_type, p.payment_id, IF(p.pay_mode_id=2,w.wallet_code,apm.code) as payment_mode')
                            ->get();
                    foreach ($payments as $pay)
                    {
                        if (in_array($pay->payment_mode, ['netbanking', 'credit-card', 'debit-card']))
						{
                            $trans->payment_id = (String) $pay->payment_id;
						}	else {
							if ($details->pay_through == 3) {
							    	$res['mode'] = $pay->payment_mode;
							} else {
								$res['mode'] = $trans->wallet;
							}
						}
                        //$trans->payment_type = $pay->payment_type;
                        $trans->payment_status_class = $this->config->get('dispclass.payment_status.'.$pay->payment_status);                        
                        $trans->payment_status = trans('general.order_payment_status.'.$pay->payment_status);						
                        $lang = trans('transactions.'.$trans->statementline_id.'.user.payment_details');
                        if (isset($lang[$pay->payment_mode]))
                            $trans->{$pay->payment_mode} = CommonLib::currency_format($pay->from_amount, $trans->currency_id, true, false);
                            //$trans->{$pay->payment_mode} = CommonLib::currency_format($pay->from_amount, ['currency_symbol'=>$pay->currency_symbol, 'currency_code'=>$pay->code, 'decimal_places'=>$pay->decimal_places, 'value_type'=>'-']);
							
						if (isset($payment->payment_mode) && !empty($payment->payment_mode)) {
							$res['mode'] = $trans->wallet;		
						}
                    }
                }
					
					$wallet_codes = array_flip($this->config->get('constants.WALLETS'));
					//if ($account_type == 2) {
					if (!in_array($trans->statementline_id, [44,45])) {
						$format = trans('transactions.'.$trans->statementline_id.'.user.fields');
						$payment_fld = trans('transactions.'.$trans->statementline_id.'.user.payment_details');
						$d_properties = trans('transactions.'.$trans->statementline_id.'.user.properties');
					} else {
						$format = trans('transactions.'.$trans->statementline_id.'.seller.fields');
						$payment_fld = trans('transactions.'.$trans->statementline_id.'.seller.payment_details');
						$d_properties = trans('transactions.'.$trans->statementline_id.'.seller.properties');
					}
					$d_properties = is_array($d_properties) ? $d_properties : [];
					//return $trans;
					
					
					if (is_array($payment_fld) && !empty($payment_fld))
					{
						array_walk($payment_fld, function(&$v, $k) use(&$trans, &$d_properties, &$payment_details, $account_type)
						{
							if (isset($trans->{$k}))
							{
								//if ($account_type == 2) {
								if (!in_array($trans->statementline_id, [44,45])) {
									$payment_details[$k] = ['label'=>trans('transactions.'.$trans->statementline_id.'.user.payment_details.'.$k, (array) $trans), 'value'=>(isset($trans->{$k})) ? $trans->{$k} : ''];
								} else {
									$payment_details[$k] = ['label'=>trans('transactions.'.$trans->statementline_id.'.seller.payment_details.'.$k, (array) $trans), 'value'=>(isset($trans->{$k})) ? $trans->{$k} : ''];
								}
							}
							if (!empty($d_properties) && is_array($d_properties) && array_key_exists($k, $d_properties))
							{
								foreach ($d_properties[$k] as $pro=> $field)
								{
									$payment_details[$k][$pro] = $trans->{$field};
								}
							}
						});
					}
					$res['payment_details'] = array_values($payment_details);
					if (is_array($format) && !empty($format))
					{
						array_walk($format, function(&$v, $k) use(&$trans, &$res, $d_properties)
						{
							if (isset($trans->{$k}))
							{
								$res[$k] = ['label'=>$v, 'value'=>(isset($trans->{$k})) ? $trans->{$k} : ''];
								if (!$this->config->get('app.is_api') && array_key_exists($k, $d_properties))
								{
									foreach ($d_properties[$k] as $pro=> $field)
									{
										$res[$pro] = $trans->{$field};
									}
								}
							}
						});
					}
				
				    
					//$res['payment_details'] = $trans->payment_details;
					$res['remark'] = $trans->remark;
					//$res['statement_line'] = $trans->statement_line;
					$res['created_on'] = $trans->created_on;
					//$res['created_on'] = showUTZ($trans->created_on, 'h.i A, d M Y');
					//$res['store_name'] = $trans->store_name;
					//$res['address'] = $trans->address;
					//$res['logo'] = $trans->logo;
					//$res['mrcode'] = $trans->mrcode;
					//$res['query_link'] = URL('/faqs');
					$res['status'] = $trans->status;
					$res['status_class'] = $trans->status_class; 
					return ['res'=>$res, 'statementline_id'=>$trans->statementline_id];
					//return $res;  					
            }
            else
            {
                Log::error('Transaction Details Fields Not Configured for Statementline ID: '.$trans->statementline_id);
                return abort(500, 'Transaction Details Fields Not Configured for Statementline ID: '.$trans->statementline_id);
            }
            unset($trans->relation_id);
            unset($trans->post_type);
            unset($trans->wallet_code);
            unset($trans->code);
            //unset($trans->statementline);
            unset($trans->statementline_id);
            unset($trans->currency_symbol);
            unset($trans->decimal_places);
            unset($trans->transaction_type);
        }
        return $trans;
    }


}