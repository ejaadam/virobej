<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use DB;

class Wallet extends BaseModel
{
	 public function __construct ()
    {
         parent::__construct();
    }
	

    public function account_balance ($arr=array())
    {
		if(!empty($arr)){
			extract($arr);
       		$qry = DB::table($this->config->get('tables.WALLET').' as w')
				->leftjoin($this->config->get('tables.ACCOUNT_BALANCE').' as ub','ub.wallet_id','=','w.wallet_id')
				->leftjoin($this->config->get('tables.CURRENCIES').' as cur','cur.currency_id','=','ub.currency_id')
				->leftjoin($this->config->get('tables.WALLET_LANG').' as wl',function($join){
						$join->on('wl.wallet_id','=','ub.wallet_id')
						->where('wl.lang_id','=',$this->config->get('app.locale'));
				})
                ->where('ub.account_id',$account_id);							
			
			if(isset($wallet_id) && $wallet_id>0){
				$qry->where('ub.wallet_id',$wallet_id);
			}
			
			if(isset($currency_id) && $currency_id>0){
				$qry->where('ub.currency_id',$currency_id);
			}			
			$qry->select(DB::Raw('current_balance,tot_credit,tot_debit,w.wallet_id,cur.currency_id ,cur.currency as  currency_code,wl.wallet as wallet_name'));
			$result =  $qry->get();	
			return !empty($result)? (count($result)==1)? $result[0] : $result : NULL;
		}
        return NULL;
    }

	public function my_wallets ($arr=array()){
		if(!empty($arr)){
			extract($arr);
       		$qry = DB::table($this->config->get('tables.WALLET').' as w')
				->leftJoin($this->config->get('tables.ACCOUNT_BALANCE').' as ub',function($join) use($account_id,$currency_id){
					$join->on('ub.wallet_id','=','w.wallet_id');
					$join->where('ub.account_id','=',$account_id);
					$join->where('ub.currency_id','=',$currency_id);
				});
			
			if(isset($wallet_id) && $wallet_id>0){
				$qry->where('w.wallet_id',$wallet_id);
			}	
			
			$qry->select(DB::Raw("ub.current_balance,ub.tot_credit,ub.tot_debit,ub.currency_id,w.wallet_id,(select code FROM ".$this->config->get('tables.CURRENCIES')." where id=ub.currency_id) as currency_code,(select currency_symbol FROM ".$this->config->get('tables.CURRENCIES')." where id=ub.currency_id) as currency_symbol,(select wallet FROM ".$this->config->get('tables.WALLET_LANG')." where wallet_id=ub.wallet_id AND lang_id='".$this->config->get('app.locale_id')."') as wallet_name"));
			$qry->orderby('w.sort_order','asc');
			$result =  $qry->get();	
			
			return !empty($result)? (count($result)==1)? $result[0] : $result : NULL;
		}
        return NULL;
    }	

    public function update_account_balance ($arr=array()) {
		$sdata = '';
        if ($arr['type'] == $this->config->get('constants.TRANSACTION_TYPE.CREDIT')) {
            $sdata['current_balance'] = DB::raw('current_balance+' . $arr['amount']);
            $sdata['tot_credit'] = DB::raw('tot_credit+' . $arr['amount']);
        } else if ($arr['type'] == $this->config->get('constants.TRANSACTION_TYPE.DEBIT')) {
            $sdata['current_balance'] = DB::raw('current_balance-' . $arr['amount']);
            $sdata['tot_debit'] = DB::raw('tot_debit+' . $arr['amount']);
        }

        $upRes = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                ->where('account_id', $arr['account_id'])
                ->where('wallet_id', $arr['wallet_id'])
                ->where('currency_id', $arr['currency_id'])
                ->update($sdata);
		
		if(isset($arr['return'])){
			if($arr['return']=='current'){
				$upRes = $this->account_balance(['account_id'=>$arr['account_id'],'wallet_id'=>$arr['wallet_id'],'currency_id'=>$arr['currency_id']]);
			}
		}			
        return !empty($upRes)? $upRes: NULL;
	}
	
	public function transactions($arr=array())
	{
		//print_r($arr); exit;
		extract($arr);
		$finalQry = '';
		$wQry2 = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION') . ' as trs');
		if(isset($account_id))
		{
			$wQry2->where('trs.account_id', $account_id);
				
		if (isset($from) && !empty($from) && isset($to) && !empty($to))
        {	
            $wQry2->whereRaw("DATE(trs.created_on) >='".date('Y-m-d', strtotime($from))."'");
            $wQry2->whereRaw("DATE(trs.created_on) <='".date('Y-m-d', strtotime($to))."'");
        }
       else if (isset($from) && !empty($from))
		{
				$wQry2->whereRaw("DATE(trs.created_on) <='".date('Y-m-d', strtotime($from))."'");
		}
		else if (!empty($to) && isset($to))
		{
			$wQry2->whereRaw("DATE(trs.created_on) >='".date('Y-m-d', strtotime($to))."'");
		}

        if (isset($search_term) && !empty($search_term))
        {		
            $wQry2->whereRaw("trs.remark like '%$search_term%'");
        }
        if (isset($wallet_id) && !empty($wallet_id))
        {
            $wQry2->where("trs.wallet_id", $wallet_id);
			
        }
        if (isset($currency_id) && !empty($currency_id))
        {
		    $wQry2->where("trs.currency_id", $currency_id);
        }
		if (isset($orderby) && isset($order))
        {
            $wQry2->orderBy($orderby, $order);			
        }
        if (isset($length) && !empty($length))
        {
            $wQry2->skip($start)->take($length);
        }
		
        if (isset($count) && !empty($count))
        {
            return $wQry2->count();
        }
        else
        {
			$wQry2->join($this->config->get('tables.STATEMENT_LINES_LANG') . ' as st',function($join){
				$join->on('st.statementline_id','=','trs.statementline');
				$join->where('st.lang_id','=',$this->config->get('app.locale_id'));
			});
			$wQry2->leftJoin($this->config->get('tables.WALLET_LANG').' as b',function($join){
				$join->on('b.wallet_id','=','trs.wallet_id');
				$join->where('b.lang_id','=',$this->config->get('app.locale_id'));
			});
			$wQry2->leftJoin($this->config->get('tables.PAYOUT_TYPES_LANG').' as c', function($join){
				$join->on('c.payment_type_id','=','trs.payment_type');
				$join->where('c.lang_id','=',$this->config->get('app.locale_id'));
			});
            $wQry2->leftJoin($this->config->get('tables.CURRENCIES').' as cur', 'cur.id', '=', 'trs.currency_id');
			$wQry2->select(DB::Raw('trs.id,trs.account_id,trs.created_on,trs.transaction_id,trs.amount,trs.handleamt,trs.tax,trs.tds,trs.paidamt,trs.transaction_type,trs.current_balance,st.statementline,trs.remark,trs.admin_comments,trs.wallet_id,cur.currency_symbol,cur.code as currency_code,b.wallet'));					
			$transactions = $wQry2->get();
			if($transactions)		
				array_walk($transactions, function(&$transaction)
				{
					$transaction->created_on = date('d-M-Y H:i:s', strtotime($transaction->created_on));
					
					$transaction->remark = $transaction->statementline.(!empty($transaction->remark) ? ' ('.$transaction->remark.')' : '');															
					$transaction->Fpaidamt = $transaction->currency_symbol.' '.number_format($transaction->paidamt, \AppService::decimal_places($transaction->paidamt), '.', ',').' '.$transaction->currency_code;
					if($transaction->transaction_type == 0){
						$transaction->Fpaidamt = '- '.$transaction->Fpaidamt;
					}
					$transaction->color = $transaction->transaction_type == 1 ? 'green' : 'red';
				
					$transaction->transaction_type = $transaction->transaction_type == 1 ? 'Credit' : 'Debit';	
						
					$transaction->Fcurrent_balance = $transaction->currency_symbol.' '.number_format($transaction->current_balance, \AppService::decimal_places($transaction->current_balance), '.', ',').' '.$transaction->currency_code;
					unset($transaction->statementline);
				});
				return $transactions;
		}
        }  
		
	}
	
	
	/*
     * name		: get_all_wallet_list
     * @param 	:
     * @return 	: Response
     * get_all_wallet_list
     */

public function get_all_wallet_list($arr = array())
    {   
	    $result= DB::table($this->config->get('tables.WALLET').' as w')
				->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
				{
					$subquery->on('wl.wallet_id', '=', 'w.wallet_id')
					->where('wl.lang_id', '=', $this->config->get('app.locale_id'));
				})
			    ->where('w.fundtransfer_status', $this->config->get('constants.ACTIVE'))
				->where(array('status'=>$this->config->get('constants.ACTIVE')))
				->get();
		if (!empty($result))
        {
            return $result;
        }
		return NULL;     
	}
	
	public function get_wallet_name ($wallet_id)
    {
        return DB::table($this->config->get('tables.WALLET').' as w')
                        ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                        {
                            $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                            ->where('wl.lang_id', '=',$this->config->get('app.locale_id'));
                        })
                        ->where('w.wallet_id', $wallet_id)
                        ->value('wl.wallet');
    }
	
	public function get_currencies($arr = array()) {
		extract($arr);
        $qry = DB::table($this->config->get('tables.CURRENCIES').' as c') 
				->join ($this->config->get('tables.ACCOUNT_BALANCE'). ' as abal','abal.currency_id', '=', 'c.id')
				->where ('abal.account_id',$account_id)
				->where(array('c.status'=>$this->config->get('constants.ACTIVE')))	 		
				->select('c.code','c.id','abal.wallet_id','current_balance','currency_id');
		if (isset($currency_id) && !empty($currency_id))
        {
            $query->where('currency_id', $currency_id);
        }
		
		if (isset($wallet_id) && !empty($wallet_id))
        {
            $query->where('wallet_id', $wallet_id);
        }
		
		$res = $qry->get();
		if (!empty($res) && count($res) > 0) {
            return $res;
        } 
        return false;	
    }
	
	public function get_fund_transfer_settings ($arr = array())
    {
	    
        extract($arr);
        $query = DB::table($this->config->get('tables.FUND_TRANSFER_SETTINGS'));
        if (isset($currency_id) && !empty($currency_id))
        {
		    
            $query->where('currency_id', $currency_id);
        }
        $query->where('transfer_type', $transfer_type);
        $settings = $query->get();
        return (!empty($settings) && count($settings) > 0) ? $settings : false;
    }
	
	 public function getWalletBalnceTotal($postdata)
    {
        $total = 0;
        $wallet = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as a')
                ->join($this->config->get('tables.WALLET_LANG').' as b', 'b.wallet_id', ' = ', 'a.wallet_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'a.account_id')
                ->join($this->config->get('tables.CURRENCIES').' as uc', 'uc.id', ' = ', 'a.currency_id')
                ->select(DB::raw('a.tot_credit, a.tot_debit, a.current_balance, a.account_id, b.wallet, b.wallet_id, a.currency_id, um.uname as username, uc.code as currency_code'));
        if (isset($postdata['username']) && $postdata['username'])
        {
            $wallet->whereRaw("um.uname = '$postdata[username]'");
        }
        if (isset($postdata['account_id']) && $postdata['account_id'])
        {
            $wallet->whereRaw("um.account_id = '$postdata[account_id]'");
        }
        if (isset($postdata['wallet_id']) && $postdata['wallet_id'])
        {
            $wallet->whereRaw("a.wallet_id = '$postdata[wallet_id]'");
        }
        if (isset($postdata['currency_id']) && $postdata['currency_id'])
        {
            $wallet->whereRaw("a.currency_id = '$postdata[currency_id]'");
        }
        $wallet = $wallet->orderBy('a.id', 'DESC');
        return $wallet->get();
    }
	public function get_user_verification_total ($arr = array())
    {
		extract($arr);
        $result = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->where('account_id',$account_id)
                ->get();
        return count($result);
	}
	 
	 public function get_currency_name ($currency_id)
    {
        return DB::table($this->config->get('tables.CURRENCIES'))
                        ->where('id', $currency_id)
                        ->pluck('code');
    }
	
	
	public function add_user_transaction ($dataArray = array())
    {
        //$dataArray['timeflag'] = date("Y-m-d H:i:s");
        return DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
                        ->insert($dataArray);
			

    }
	 public function fund_user_transaction ($dataArray = array())
    {
        //$dataArray['timeflag'] = date("Y-m-d H:i:s");
        return DB::table($this->config->get('tables.FUND_TRANSFER'))
                        ->insert($dataArray);

    } 
	public function update_user_balance ($dataArray=array())
    {
        $updata = array();
        if (count($dataArray) > 0)
        {
            $cur_balance = $tot_credit = $tot_debit = 0;
            $bal_details = $this->get_user_balance($dataArray['payment_type'],array('account_id'=>$dataArray['user_id']),$dataArray['wallet_id'], $dataArray['currency_id']);
            if ($bal_details && count($bal_details) > 0)
            {
                $cur_balance = $bal_details->current_balance;
                $tot_credit = $bal_details->tot_credit;
                $tot_debit = $bal_details->tot_debit;
            }
            else
                return Lang::get('general.bal_status_msg');
	//	print_R($dataArray['transaction_type']); exit;
            if ($dataArray['transaction_type'] == $this->config->get('constants.CREDIT'))
            {
                $updata['tot_credit'] = $tot_credit + $dataArray['amount'];
			    $updata['current_balance'] = $cur_balance + $dataArray['amount'];
            }
			
            else if ($dataArray['transaction_type'] == $this->config->get('constants.DEBIT'))
            {
                $updata['tot_debit'] = $tot_debit + $dataArray['amount'];
                $updata['current_balance'] = $cur_balance - $dataArray['amount'];
               
            }
            $updata['updated_date'] = date("Y-m-d H:i:s");
            if ($bal_details && count($bal_details) > 0)
            {
                $update_status = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                        ->where('account_id', $dataArray['user_id'])
                        ->where('currency_id', $dataArray['currency_id'])
                        ->where('wallet_id', $dataArray['wallet_id'])
                        ->update($updata);
            }
            return $update_status;
        }
        return false;
    }
	
	public function getSetting_key_charges()
    {
        $total = 0;
        $date = date('Y-m-d');
        //print_r( $date);exit;
        $commission_charge = DB::select(DB::raw("select setting_value from settings where setting_key='user_to_user_transfer_charge' "));
		//print_r($commission_charge);exit;
        return (!empty($commission_charge) && count($commission_charge) > 0) ? $commission_charge[0] : NULL;
    }
	
	
	
	public function get_userdetails_byid ($account_id)
    {
		
        return DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
                        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
                        ->leftjoin($this->config->get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_name', '=', 'ud.district')
                        ->leftjoin($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.name', '=', 'ud.state')
                        ->where('um.account_id', $account_id)
                        ->where('um.is_deleted', $this->config->get('constants.OFF'))
                        ->select(DB::raw('um.*,ud.*, ld.district_id, ls.region_id, ls.state_id'))
                        ->first();
					
    }

	
	
	public function get_user_balance ($payment_type = 0, $arr = array(), $wallet_id, $currency_id = 0,$purpose='')
    {		
		extract($arr);
        $balance = 0;
	    $result = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as b')				
                ->where([
                    'b.account_id'=>$account_id,
			        'b.wallet_id'=>$wallet_id,
                    'b.currency_id'=>$currency_id])
                ->count();
       
	   if (empty($result) && count($result) <= 0)
        {  
         $curresult = DB::table($this->config->get('tables.CURRENCIES'))
                    ->where(array(
                        'currency_id'=>$currency_id,
                        'status'=>$this->config->get('constants.ON')))
					->count();
	
	     $ewalresult = DB::table($this->config->get('tables.WALLET'))
                    ->where(array(
                        'wallet_id'=>$wallet_id,
                        'status'=>$this->config->get('constants.ON')))
                    ->count();
					
            if (($curresult == 1) && ($ewalresult == 1))
            {			 
			    $insert['account_id'] = $account_id;
                $insert['current_balance'] = '0';
                $insert['tot_credit'] = '0 ';
                $insert['tot_debit'] = '0';
                $insert['currency_id'] = $currency_id;
                $insert['wallet_id'] = $wallet_id;
                $status = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                ->insertGetId($insert);
			}			
        }
		
		$result = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as b')
				->join($this->config->get('tables.WALLET').' as w','w.wallet_id','=','b.wallet_id')
				->join($this->config->get('tables.WALLET_LANG').' as wl',function($join){
					$join->on('wl.wallet_id','=','b.wallet_id')
					->where('wl.lang_id','=',$this->config->get('app.locale'));
				})
				->join($this->config->get('tables.CURRENCIES').' as c','c.currency_id','=','b.currency_id')
                ->where([
                    'b.account_id'=>$account_id,
			        'b.wallet_id'=>$wallet_id,
                    'b.currency_id'=>$currency_id,					
					'w.'.$purpose =>$this->config->get('constants.ON')])
				->select('b.*','wl.wallet','w.wallet_code','c.currency as currency_code','c.decimal_places')
                ->first();		
		
        return (!empty($result)) ? $result : false;
	}
	
	public function get_user_settings ($arr = array())
    {   
	    extract($arr);
        $qry = DB::table($this->config->get('tables.ACCOUNT_SETTINGS').' as accsett')
                        ->where('account_id', $arr)
						
                        ->first();
		return (!empty($qry)) ? $qry : false;
	}	
	
	
public function transfer_history_details($arr=array())
	{
		
	extract($arr);
		$fund_data = DB::table($this->config->get('tables.FUND_TRANSFER').' as ft')					
			->leftjoin($this->config->get('tables.ACCOUNT_MST') . ' as fum','fum.account_id','=','ft.from_account_id')
			->leftjoin($this->config->get('tables.ACCOUNT_MST') . ' as tum','tum.account_id','=','ft.to_account_id')
			->join($this->config->get('tables.CURRENCIES') . ' as cur','cur.id','=','ft.currency_id')	
			->leftjoin($this->config->get('tables.ACCOUNT_DETAILS') . ' as fud','fud.account_id','=','ft.from_account_id')
			->leftjoin($this->config->get('tables.ACCOUNT_DETAILS') . ' as tud','tud.account_id','=','ft.to_account_id')
			//->join($this->config->get('tables.WALLET') . ' as fwal','fwal.wallet_id','=','ft.from_account_wallet_id')
			
			->join($this->config->get('tables.FUNDTRANSFER_STATUS_LOOKUP') . ' as fts','fts.status_id','=','ft.status')
			->join($this->config->get('tables.FUND_TRANSFER_STATUS_LANG') . ' as ftsl', function($join){
				$join->on('ftsl.status_id','=','ft.status');
				$join->where('ftsl.lang_id', '=', $this->config->get('app.locale_id'));
			})	
			
			->select('ft.from_account_id','ft.to_account_id','fum.uname as from_uname','tum.uname as to_uname','ft.amount','ft.paidamt','ft.status','ft.is_deleted','ft.currency_id','cur.code as currency_code','cur.currency_symbol',DB::Raw("CONCAT_WS('',fud.first_name,fud.last_name) as from_fullname"),DB::Raw("CONCAT_WS('',tud.first_name,tud.last_name) as to_fullname"),'ft.transfered_on','ftsl.status_name','ft.status','fts.disp_class','ft.transaction_id'
			,DB::Raw("if(ft.from_account_id='".$account_id."',(select wallet from ".$this->config->get('tables.WALLET_LANG')." where wallet_id=from_account_wallet_id and lang_id='".$this->config->get('app.locale_id')."'),(select wallet from ".$this->config->get('tables.WALLET_LANG')." where wallet_id=to_account_wallet_id and lang_id='".$this->config->get('app.locale_id')."')) as wallet_name"))
			->where('ft.is_deleted',$this->config->get('constants.NOT_DELETED'));
		
		
		if (isset($account_id) && !empty($account_id))
        {		
			$fund_data->where(function($qry) use($account_id){
				$qry->where("ft.from_account_id",$account_id)
					  ->orWhere("ft.to_account_id",$account_id);
			});			
        }
			
		if (isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date))
        {
            $fund_data->whereRaw("DATE(ft.transfered_on) >='".date('Y-m-d', strtotime($from_date))."'");
            $fund_data->whereRaw("DATE(ft.transfered_on) <='".date('Y-m-d', strtotime($to_date))."'");
        }
        else if (isset($to_date) && !empty($to_date))
        {
            $fund_data->whereRaw("DATE(ft.transfered_on) <='".date('Y-m-d', strtotime($to_date))."'");
        }
        else if (isset($from_date) && !empty($from_date))
        {
            $fund_data->whereRaw("DATE(ft.transfered_on) >='".date('Y-m-d', strtotime($from_date))."'");
        }
        if (isset($search_term) && !empty($search_term))
        {		
			$fund_data->where(function($wcond) use($search_term){
				$wcond->whereRaw("concat_ws('',fud.first_name,fud.last_name) like '%$search_term%'")
					->orWhereRaw("concat_ws('',tud.first_name,tud.last_name) like '%$search_term%'")
					->orWhereRaw("fum.uname like '%$search_term%'")
					->orWhereRaw("tum.uname like '%$search_term%'")
					->orWhereRaw("ft.transaction_id like '%$search_term%'");
			});			
        }
		
        if (isset($wallet_id) && !empty($wallet_id))
        {
            $fund_data->where(function($wcond) use($wallet_id,$account_id){
				$wcond->where(function($wcond2) use($wallet_id,$account_id){
					$wcond2->where("ft.from_account_wallet_id", $wallet_id)
						   ->Where("ft.from_account_id", $account_id);
				});
				$wcond->orWhere(function($wcond3) use($wallet_id,$account_id){
					$wcond3->where("ft.to_account_wallet_id", $wallet_id)
					       ->Where("ft.to_account_id", $account_id);
				});
			});
        }
        if (isset($currency_id) && !empty($currency_id))
        {
            $fund_data->where("ft.currency_id", $currency_id);
        }
		if (isset($orderby) && isset($order))
        {
            $fund_data->orderBy($orderby, $order);
        }
        if (isset($length) && !empty($length))
        {
            $fund_data->skip($start)->take($length);
        }
        if (isset($count) && !empty($count))
        {
            return $fund_data->count();
        }
		else
		{
			$fund_data=$fund_data->get();
			if(!empty($fund_data)) {
				$status_type_arr = ['0'=>'warning','1'=>'success','2'=>'danger','3'=>'info'];
				array_walk($fund_data,function(&$ftdata) use($status_type_arr){
					$ftdata->transfered_on = date('d-M-Y H:i:s',strtotime($ftdata->transfered_on));
					$ftdata->status_class = $status_type_arr[$ftdata->status];
					$ftdata->Ffrom_name=$ftdata->from_fullname.' ( '.$ftdata->from_uname.' ) ';
					$ftdata->Fto_name=$ftdata->to_fullname.' ( '.$ftdata->to_uname.' ) ';
					$ftdata->Famount = $ftdata->currency_symbol.' '.number_format($ftdata->amount, \AppService::decimal_places($ftdata->amount), '.', ',').' '.$ftdata->currency_code;
					$ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidamt, \AppService::decimal_places($ftdata->paidamt), '.', ',').' '.$ftdata->currency_code;
					$ftdata->tranTypeCls = ($ftdata->from_account_id==$this->userSess->account_id)? 'danger' : 'success';
					$ftdata->transType = ($ftdata->from_account_id==$this->userSess->account_id)? $this->config->get('constants.TRANSACTION_TYPE.DEBIT') : $this->config->get('constants.FUND_CREDIT');
});

				return $fund_data;
			}
			else
			    return false;
		}		
				
	}	
	public function get_wallet_list ($cond_array = array())
    {
        if (count($cond_array) > 0)
        {
            if (isset($cond_array['withdrawal_status']) && $cond_array['withdrawal_status'] >= 0)
            {
                $result = DB::table($this->config->get('tables.WALLET').' as w')
                        ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
							{
                            $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                            ->where('wl.lang_id', '=', $this->applang);
                        })
                        ->where('w.withdrawal_status', $cond_array['withdrawal_status'])
                        ->where('w.fundtransfer_status', 0)
                        ->get();
            }
            if (isset($cond_array['fundtransfer_status']) && $cond_array['fundtransfer_status'] >= 0)
            {
                $result = DB::table($this->config->get('tables.WALLET').' as w')
                        ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                        {
                            $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                            ->where('wl.lang_id', '=', $this->applang);
                        })
                        ->where('w.fundtransfer_status', $cond_array['fundtransfer_status'])
                        ->where('w.fundtransfer_status', 0)
                        ->get();
            }
            /*if (isset($cond_array['internaltransfer_status']) && $cond_array['internaltransfer_status'] >= 0)
            {
                $result = DB::table($this->config->get('tables.WALLET').' as w')
                        ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                        {
                            $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                            ->where('wl.lang_id', '=', $this->applang);
                        })
                        ->where('w.internaltransfer_status', $cond_array['internaltransfer_status'])
                        ->where('w.fr_fund_transfer_status', 0)
                        ->get();
            }
            if (isset($cond_array['purchase_status']) && $cond_array['purchase_status'] >= 0)
            {
                $result = DB::table($this->config->get('tables.WALLET').' as w')
                        ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                        {
                            $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                            ->where('wl.lang_id', '=', $this->applang);
                        })
                        ->where('w.purchase_status', $cond_array['purchase_status'])
                        ->where('w.fr_fund_transfer_status', 0)
                        ->get();
            }*/
        }
        else
        {
            $result = DB::table($this->config->get('tables.WALLET').' as w')
                    ->join($this->config->get('tables.WALLET_LANG').' as wl', function($subquery)
                    {
                        $subquery->on('wl.wallet_id', '=', 'w.wallet_id')
                        ->where('wl.lang_id', '=', $this->applang);
                    })
                    ->where('fr_fund_transfer_status', 0)
                    ->get();
        }
        if (!empty($result) && count($result) > 0)
        {
            return $result;
        }
    }
	//->select('ft.amount','ft.paidamt','ft.status','ft.is_deleted','fwal.wallet_name','cur.code','ft.to_account_id as to_account','ft.from_account_id as from_account','lud.last_name','fud.first_name','ft.transfered_on')
				 
	

	/*public function get_wallet()
	{
			return DB::table($this->config->get('tables.WALLET').' as w')
				->select('w.wallet_id','w.wallet_name')
				->get();
	}*/
}