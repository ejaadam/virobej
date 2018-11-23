<?php
namespace App\Models\Affiliate;
use App\Models\BaseModel;
use App\Models\Affiliate\Payments;
use DB;

class Bonus extends BaseModel
{
	public function __construct ()
    {
         parent::__construct();
    }
	public function referral_bonus_details($account_id,$arr=array())
	{
		
	extract($arr);
	
	$refSql= DB::table($this->config->get('tables.REFERRAL_EARNINGS').' as re')
	       ->join($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'). ' as ast','ast.subscribe_topup_id','=','re.subscrib_topup_id')
		   ->join($this->config->get('tables.AFF_PACKAGE_PRICING'). ' as pri','pri.package_id','=','ast.package_id')
           ->join($this->config->get('tables.ACCOUNT_MST') . ' as fum','fum.account_id','=','re.from_account_id')
		   ->join($this->config->get('tables.ACCOUNT_TREE') . ' as ut','ut.account_id','=','re.from_account_id')
		   ->join($this->config->get('tables.ACCOUNT_MST') . ' as rfum','rfum.account_id','=','ut.sponsor_id')
		   ->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as rfud','rfud.account_id','=','rfum.account_id') 
		   ->join($this->config->get('tables.ACCOUNT_MST') . ' as tum','tum.account_id','=','re.to_account_id')
		   ->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as tud','tud.account_id','=','tum.account_id')
		   ->join($this->config->get('tables.AFF_PACKAGE_MST') . ' as pm','pm.package_id','=','ast.package_id')
		   ->join($this->config->get('tables.AFF_PACKAGE_LANG') . ' as pl','pl.package_id','=','pm.package_id')
		   ->join($this->config->get('tables.CURRENCIES') . ' as cur','cur.id','=','re.currency_id')
		   ->join($this->config->get('tables.PAYMENT_TYPES').' as pt','pt.payment_type_id','=', 're.payout_type')
		   ->join($this->config->get('tables.WALLET_LANG') . ' as wal','wal.wallet_id', '=', 're.wallet_id')
		   ->join($this->config->get('tables.ACCOUNT_STATUS_LOOKUP').' as usl', 'usl.status_id', ' = ', 'fum.status')
						->join($this->config->get('tables.ACCOUNT_STATUS_LANG').' as uslang', function($subquery)
							{
								 $subquery->on('uslang.status_id', ' = ', 'usl.status_id')
								 ->where('uslang.lang_id', '=', $this->config->get('app.locale_id'));
							 })
	       ->where('re.to_account_id',$this->userSess->account_id);
		   $refSql->select(DB::Raw("re.*,re.ref_id,re.payout_type,rfum.account_id,rfum.uname as sponser_uname,concat_ws('',rfud.first_name,rfud.last_name) as sponser_full_name,fum.account_id,re.created_date,tum.uname as to_uname,fum.uname as from_uname,concat_ws(' ',tud.first_name, tud.last_name) as to_full_name,pl.package_name,re.amount,IF(re.payout_type=1,(select `wallet` from ".$this->config->get('tables.WALLET_LANG') . " as wal where `wal`.`wallet_id` = re.wallet_id),(select `payment_type` from ".$this->config->get('tables.PAYMENT_TYPES') . " as pt where `pt`.`payment_type_id` = re.payout_type)) as pay_mode,(select uname from account_mst where account_id = (select upline_id from account_tree where account_id = re.from_account_id )) as upline_username,cur.code as currency,cur.currency_symbol,re.status,pri.price as packagepricing,usl.disp_class,uslang.status_name"));
	 	if (isset($from_date) && isset($to_date) && !empty($from_date) && !empty($to_date))
		{
			$refSql->whereRaw("DATE(re.created_date) >='".date('Y-m-d', strtotime($from_date))."'");
			$refSql->whereRaw("DATE(re.created_date) <='".date('Y-m-d', strtotime($to_date))."'");
		}
		else if (!empty($from_date) && isset($from_date))
		{
			$refSql->whereRaw("DATE(re.created_date) <='".date('Y-m-d', strtotime($from_date))."'");
		}
		else if (!empty($to_date) && isset($to_date))
		{
			$refSql->whereRaw("DATE(re.created_date) >='".date('Y-m-d', strtotime($to_date))."'");
		}
		if (isset($type_of_package) && !empty($type_of_package))
        {		
            $refSql->where("pl.package_id",$type_of_package);
        }
		
		 if (isset($search_term) && !empty($search_term))
        {		
            if(!empty($filterchk) && !empty($filterchk))
				{
				  $search_term='%'.$search_term.'%'; 
				  $search_field=['FromUser'=>'fum.uname','Referral'=>'rfum.uname'];
				  $refSql->where(function($sub) use($filterchk,$search_term,$search_field)
				  {	
					foreach($filterchk as $search)
					{  
						if(array_key_exists($search,$search_field)){
							  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_term);
						} 
					}
				  });
				}
				
				else{				
			               $refSql->where(function($wcond) use($search_term){
				           $wcond->whereRaw("concat_ws('',tud.first_name,tud.last_name) like '%$search_term%'")
					       ->orWhereRaw("concat_ws('',rfud.first_name,rfud.last_name) like '%$search_term%'")
					       ->orWhereRaw("rfum.uname like '%$search_term%'")
					       ->orWhereRaw("fum.uname like '%$search_term%'")
					       ->orWhereRaw("tum.uname like '%$search_term%'");
			});			
        }	 
		} 
		$refSql->orderBy('re.created_date', 'desc');	
        if (isset($length) && !empty($length))
        {
            $refSql->skip($start)->take($length);
        }
		
		if (isset($count) && !empty($count))
        {
            return $refSql->count();
        }	
		else
		{	
			$result = $refSql->get();
			if(!empty($result)) {
				$status_type_arr = ['0'=>'warning','1'=>'success','2'=>'danger','3'=>'info'];
				array_walk($result,function(&$ftdata) use($status_type_arr){
					$ftdata->Famount = $ftdata->currency_symbol.' '.number_format($ftdata->amount, \AppService::decimal_places($ftdata->amount), '.', ',').' '.$ftdata->currency;
					//$ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidamt, \AppService::decimal_places($ftdata->paidamt), '.', ',').' '.$ftdata->currency_code;
});
				return $result;
			}
			else
			return false;
						
		}
		
	}
	
	
	 
}