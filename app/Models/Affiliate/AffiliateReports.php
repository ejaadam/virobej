<?php
namespace App\Models\Affiliate;
use App\Models\BaseModel;
use DB;
use Request;
use Response;
class AffiliateReports extends BaseModel
{
	private $admincommonObj = '';
	function __construct(){
		parent::__construct();
			
	}
	/*  Fast Start Bonus  */
   public function faststart_bonus_details($account_id,$arr=array()){
   	extract($arr);
	$refSql= DB::table(config('tables.REFERRAL_EARNINGS').' as re')
	       ->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP'). ' as ast','ast.subscribe_topup_id','=','re.subscrib_topup_id')
		   ->join(config('tables.AFF_PACKAGE_PRICING'). ' as pri','pri.package_id','=','ast.package_id')
           ->join(config('tables.ACCOUNT_MST') . ' as fum','fum.account_id','=','re.from_account_id')
		   ->join(config('tables.ACCOUNT_TREE') . ' as ut','ut.account_id','=','re.from_account_id')
		   ->join(config('tables.ACCOUNT_MST') . ' as rfum','rfum.account_id','=','ut.sponsor_id')
		    ->join(config('tables.ACCOUNT_MST') . ' as racm','racm.account_id','=','ut.nwroot_id')
		   ->join(config('tables.ACCOUNT_DETAILS') . ' as rfud','rfud.account_id','=','rfum.account_id') 
		   ->join(config('tables.ACCOUNT_MST') . ' as tum','tum.account_id','=','re.to_account_id')
		   ->join(config('tables.ACCOUNT_DETAILS') . ' as tud','tud.account_id','=','tum.account_id')
		   ->join(config('tables.AFF_PACKAGE_MST') . ' as pm','pm.package_id','=','ast.package_id')
		   ->join(config('tables.AFF_PACKAGE_LANG') . ' as pl','pl.package_id','=','pm.package_id')
		   ->join(config('tables.CURRENCIES') . ' as cur','cur.currency_id','=','re.currency_id')
		   ->join(config('tables.PAYOUT_TYPES').' as pt','pt.payout_type_id','=', 're.payout_type')
		   ->join(config('tables.WALLET_LANG') . ' as wal','wal.wallet_id', '=', 're.wallet_id')
		   ->join(config('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'fum.status')
	       ->where('re.to_account_id',$this->userSess->account_id);
		   $refSql->select(DB::Raw("re.*,re.ref_id,re.payout_type,rfum.account_id,rfum.uname as sponser_uname,concat_ws('',rfud.firstname,rfud.lastname) as sponser_full_name,racm.uname as root_group,fum.account_id,re.created_date,re.qv,tum.uname as to_uname,fum.uname as from_uname,concat_ws(' ',tud.firstname, tud.lastname) as to_full_name,pl.package_name,re.amount,IF(re.payout_type=1,(select `wallet` from ".config('tables.WALLET_LANG') . " as wal where `wal`.`wallet_id` = re.wallet_id),(select `payout_name` from ".config('tables.PAYOUT_TYPES') . " as pt where `pt`.`payout_type_id` = re.payout_type)) as pay_mode,(select uname from account_mst where account_id = (select upline_id from account_tree where account_id = re.from_account_id )) as upline_username,cur.currency as currency,cur.currency_symbol,re.status,pri.price as packagepricing,usl.status_name,re.earnings,re.commission,re.tax,re.ngo_wallet,re.net_pay"));  
	 if (isset($from_date) && isset($to_date) && !empty($from_date) && !empty($to_date))
		{
		 $refSql->whereDate('re.created_date', '>=', getGTZ($from_date,'Y-m-d'));
		 $refSql->whereDate('re.created_date', '<=', getGTZ($to,'Y-m-d'));
		}
		else if (!empty($from_date) && isset($from_date))
		{
		    $refSql->whereDate('re.created_date', '<=', getGTZ($from_date,'Y-m-d'));
		}
		else if (!empty($to_date) && isset($to_date))
		{ 
		     $refSql->whereDate('re.created_date', '>=', getGTZ($to_date,'Y-m-d'));
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
					$ftdata->commission = number_format($ftdata->commission, \AppService::decimal_places($ftdata->commission), '.', ',');
					$ftdata->tax = number_format($ftdata->tax, \AppService::decimal_places($ftdata->tax), '.', ',');
					$ftdata->ngo_wallet = number_format($ftdata->ngo_wallet, \AppService::decimal_places($ftdata->ngo_wallet), '.', ',');
					$ftdata->net_pay = number_format($ftdata->net_pay, \AppService::decimal_places($ftdata->net_pay), '.', ',');
					$ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
					//$ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidamt, \AppService::decimal_places($ftdata->paidamt), '.', ',').' '.$ftdata->currency_code;
    });
				return $result;
			}
			else
			return false;
						
		}
	}
	
	/* Team Bonus */
   public function get_teambonus_details($account_id,$arr=array(),$count=false){  
    	extract($arr);
    	if (!empty($account_id)){ 
	    $query = DB::table(config('tables.ACCOUNT_MST').' as am')
       ->join(config('tables.AF_BINARY_BONUS').' as bb', function($subquery) use($account_id)
        {
         $subquery->on('bb.account_id', '=', 'am.account_id')
         ->where('bb.account_id','=',$account_id)
         ->where('bb.type','=',config('constants.BONUS.TYPE1'));
         }) 
 		 ->join(config('tables.ACCOUNT_PREFERENCE').' as acc','acc.account_id', '=','am.account_id') 
	      ->join(config('tables.CURRENCIES').' as cur',function($subquery) use($account_id)
          {
          $subquery->on('acc.currency_id','=','cur.currency_id')
         ->where('acc.account_id','=',$account_id);
         }) 
      ->select('am.account_id','am.user_code','am.uname','bb.leftbinpnt','bb.rightbinpnt','bb.leftclubpoint','bb.rightclubpoint','bb.clubpoint','bb.totleftbinpnt','bb.totrightbinpnt','bb.leftcarryfwd','bb.rightcarryfwd','bb.flushamt','bb.confirmed_date','bb.created_date','bb.status','bb.from_date','bb.to_date','bb.bonus_value','bb.paidinc','cur.currency_symbol','cur.currency as code','bb.tax','bb.ngo_wallet','bb.income');
		if (isset($from) && isset($to) && !empty($from) && !empty($to)){
		$query->whereDate('bb.from_date', '>=', getGTZ($from, 'Y-m-d'));
		$query->whereDate('bb.to_date', '<=', getGTZ($to, 'Y-m-d'));
		  }
		else if (!empty($from) && isset($from)){
			$query->whereDate('bb.from_date', '<=', getGTZ($from, 'Y-m-d'));
		}
		else if (!empty($to) && isset($to)){
		$query->whereDate('bb.from_date', '>=', getGTZ($to, 'Y-m-d'));
		}
		if ($count){
			return $query->count();
		}
		else{
			if (isset($length) && !empty($length)){
				$query->skip($start)->take($length);
			}
				$query=$query->orderBy('bb.account_id', 'ASC'); 
				$result=$query->get();
		
				if(!empty($result)) {
					array_walk($result,function(&$ftdata) {
		$ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidinc, \AppService::decimal_places($ftdata->paidinc), '.', ',').' '.$ftdata->code;
		$ftdata->leftbinpnt =number_format($ftdata->leftbinpnt, \AppService::decimal_places($ftdata->leftbinpnt));
		$ftdata->rightbinpnt =number_format($ftdata->rightbinpnt, \AppService::decimal_places($ftdata->rightbinpnt));
		$ftdata->leftclubpoint =number_format($ftdata->leftclubpoint, \AppService::decimal_places($ftdata->leftclubpoint));
		$ftdata->rightclubpoint =number_format($ftdata->rightclubpoint, \AppService::decimal_places($ftdata->rightclubpoint));
		$ftdata->totleftbinpnt =number_format($ftdata->totleftbinpnt, \AppService::decimal_places($ftdata->totleftbinpnt));
		$ftdata->totrightbinpnt =number_format($ftdata->totrightbinpnt, \AppService::decimal_places($ftdata->totrightbinpnt));
		$ftdata->leftcarryfwd =number_format($ftdata->leftcarryfwd, \AppService::decimal_places($ftdata->leftcarryfwd));
		$ftdata->rightcarryfwd =number_format($ftdata->rightcarryfwd, \AppService::decimal_places($ftdata->rightcarryfwd));
		$ftdata->flushamt =number_format($ftdata->flushamt, \AppService::decimal_places($ftdata->flushamt));
		$ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
			 if($ftdata->status==0){
					$ftdata->status=config('constants.ACCOUNT.PENDING');
					} 
					if($ftdata->status==1){
					 $ftdata->status=config('constants.BONUS.CONFIRM');
					}
					if($ftdata->confirmed_date ==''){
					$ftdata->confirmed_date=' ';
					} else{
				   $ftdata->confirmed_date = showUTZ($ftdata->confirmed_date, 'd-M-Y H:i:s');
					} 
					if($ftdata->created_date ==''){
					$ftdata->created_date=' ';
					} else{
					   $ftdata->created_date = showUTZ($ftdata->created_date, 'd-M-Y H:i:s');
					} 
				 });		
				 
			     return !empty($result) ? $result : NULL;			
		     }
		  }
		}
		return NULL;
	}
	/* Leadership Bonus */
  public function get_leadership_bonus($account_id,$arr=array(),$count=false){
	  
	extract($arr);
	if (!empty($account_id)){ 
	    $query = DB::table(config('tables.ACCOUNT_MST').' as am')
        ->join(config('tables.AF_BINARY_BONUS').' as bb', function($subquery) use($account_id)
        {
         $subquery->on('bb.account_id', '=', 'am.account_id')
         ->where('bb.account_id','=',$account_id)
         ->where('bb.type','=',config('constants.BONUS.TYPE2'));
         }) 
		 ->join(config('tables.ACCOUNT_PREFERENCE').' as acc','acc.account_id', '=','am.account_id')
		 ->join(config('tables.CURRENCIES').' as cur',function($subquery) use($account_id)
          {
          $subquery->on('acc.currency_id','=','cur.currency_id')
         ->where('acc.account_id','=',$account_id);
         }) 
       ->select('am.account_id','am.user_code','am.uname','bb.leftbinpnt','bb.rightbinpnt','bb.leftclubpoint','bb.rightclubpoint','bb.clubpoint','bb.totleftbinpnt','bb.totrightbinpnt','bb.leftcarryfwd','bb.rightcarryfwd','bb.flushamt','bb.confirmed_date','bb.created_date','bb.status','bb.from_date','bb.to_date','bb.bonus_value','bb.paidinc','cur.currency_symbol','cur.currency as code','bb.tax','bb.ngo_wallet','bb.income'); 
	  
	 if (isset($from) && isset($to) && !empty($from) && !empty($to)){
		 $query->whereDate('bb.from_date', '>=', getGTZ($from,'Y-m-d'));
		 $query->whereDate('bb.to_date', '<=', getGTZ($to,'Y-m-d'));
		  }
		else if (!empty($from) && isset($from)){
		 $query->whereDate('bb.from_date', '<=', getGTZ($from,'Y-m-d'));	
		}
		else if (!empty($to) && isset($to)){
		 $query->whereDate('bb.from_date', '>=', getGTZ($to,'Y-m-d'));
		}
		if ($count){
			return $query->count();
		}
		else{
			if (isset($length) && !empty($length)){
				$query->skip($start)->take($length);
			}
				$query=$query->orderBy('bb.account_id', 'ASC'); 
				$result=$query->get();
	
				if(!empty($result)) {
					array_walk($result,function(&$ftdata) {
					
		$ftdata->Fpaidamt = $ftdata->currency_symbol.' '.number_format($ftdata->paidinc, \AppService::decimal_places($ftdata->paidinc), '.', ',').' '.$ftdata->code;
		$ftdata->leftbinpnt =number_format($ftdata->leftbinpnt, \AppService::decimal_places($ftdata->leftbinpnt));
		$ftdata->rightbinpnt =number_format($ftdata->rightbinpnt, \AppService::decimal_places($ftdata->rightbinpnt));
		$ftdata->leftclubpoint =number_format($ftdata->leftclubpoint, \AppService::decimal_places($ftdata->leftclubpoint));
		$ftdata->rightclubpoint =number_format($ftdata->rightclubpoint, \AppService::decimal_places($ftdata->rightclubpoint));
		$ftdata->totleftbinpnt =number_format($ftdata->totleftbinpnt, \AppService::decimal_places($ftdata->totleftbinpnt));
		$ftdata->totrightbinpnt =number_format($ftdata->totrightbinpnt, \AppService::decimal_places($ftdata->totrightbinpnt));
		$ftdata->leftcarryfwd =number_format($ftdata->leftcarryfwd, \AppService::decimal_places($ftdata->leftcarryfwd));
		$ftdata->rightcarryfwd =number_format($ftdata->rightcarryfwd, \AppService::decimal_places($ftdata->rightcarryfwd));
		$ftdata->flushamt =number_format($ftdata->flushamt, \AppService::decimal_places($ftdata->flushamt));
	    $ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
					if($ftdata->status==0){
					$ftdata->status=config('constants.BONUS.PENDING');
					} 
					if($ftdata->status==1){
					 $ftdata->status=config('constants.BONUS.CONFIRM');
					}
					if($ftdata->confirmed_date ==''){
					$ftdata->confirmed_date=' ';
					} else{
				   $ftdata->confirmed_date = showUTZ($ftdata->confirmed_date, 'd-M-Y H:i:s');
					} 
					if($ftdata->created_date ==''){
					$ftdata->created_date=' ';
					} else{
				   $ftdata->created_date = showUTZ($ftdata->created_date, 'd-M-Y H:i:s');
					} 
				});		
			     return !empty($result) ? $result : NULL;			
		     }
		  }
		}
		return NULL;
	}
	/* Personal Commission */
	public function personal_commission($account_id,$arr=array(),$count=false){
		extract($arr);
	if (!empty($account_id)){ 
	  $query = DB::table(config('tables.PERSONAL_COMMISSION').' as pm')
      ->select('pm.account_id','pm.confirm_date','pm.direct_cv','pm.self_cv','pm.slab','pm.total_cv','pm.earnings','pm.commission','pm.tax','pm.ngo_wallet','pm.net_pay','pm.status');

		if (isset($from) && isset($to) && !empty($from) && !empty($to)){
		 $query->whereDate('pm.confirm_date', '>=', getGTZ($from,'Y-m-d'));
		 $query->whereDate('pm.confirm_date', '<=', getGTZ($to,'Y-m-d'));
		  }
		else if (!empty($from) && isset($from)){

		 $query->whereDate('pm.confirm_date', '<=', getGTZ($from,'Y-m-d'));	
		}
		else if (!empty($to) && isset($to)){
		 $query->whereDate('pm.confirm_date', '>=', getGTZ($to,'Y-m-d'));
		}
		if ($count){
			return $query->count();
		}
		else{
			if (isset($length) && !empty($length)){
				$query->skip($start)->take($length);
			}
				$query=$query->orderBy('pm.account_id', 'ASC'); 
				$result=$query->get();
	
				if(!empty($result)) {
					$serial_no=1;
					array_walk($result,function(&$ftdata) use($serial_no) {
						
         $ftdata->serial_no = $serial_no;
		$ftdata->commission = number_format($ftdata->commission, \AppService::decimal_places($ftdata->commission));
		$ftdata->tax =number_format($ftdata->tax, \AppService::decimal_places($ftdata->tax));
		$ftdata->ngo_wallet =number_format($ftdata->ngo_wallet, \AppService::decimal_places($ftdata->ngo_wallet));
		$ftdata->net_pay =number_format($ftdata->net_pay, \AppService::decimal_places($ftdata->net_pay));
		$ftdata->status_dispclass = config('dispclass.affiliate.'.$ftdata->status);
			if($ftdata->status==1){
					 $ftdata->status='Confirm';
					}
					if($ftdata->confirm_date ==''){
					$ftdata->confirm_date=' ';
					} else{
				   $ftdata->confirm_date = showUTZ($ftdata->confirm_date, 'd-M-Y H:i:s');
					}  
				});		
				 
			     return !empty($result) ? $result : NULL;		
            
		     }
		  }
		}
		return NULL;
	}
	
	/* Team Bonus Save */
	public function save_team_bonus($account_id,$currency_id){
		$g1_sale = 0;
		$g2_sale = 0;
	    $user_count=DB::table(config('tables.ACCOUNT_TREE').' as at')
							->where('at.upline_id',$account_id)
							->where('is_deleted',config('constants.NOT_DELETED'))
							->whereIn('rank', [config('constants.TEAM_GENARATION.1G'), config('constants.TEAM_GENARATION.2G')])
							->orderBy('rank', 'ASC')
		     				->selectRaw(DB::raw("account_id,nwroot_id,lft_node,rgt_node"))
							->get();  
						
 	       if(count($user_count)==2){
			      foreach($user_count as $key => $user_info){
				   $upline_info=DB::table(config('tables.ACCOUNT_TREE').' as at')
					 ->where('at.account_id',$user_info->account_id)
					 ->selectRaw(DB::raw("account_id,lft_node,rgt_node,nwroot_id"))
					 ->first(); 
					 
					  $pkg_qv=DB::table(config('tables.ACCOUNT_TREE').' as at')
					  ->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as ast', function($subquery){
					  $subquery->on('ast.account_id','=','at.account_id')
					  ->where('ast.confirm_date', '>=', '2018-07-01')
					  ->where('ast.confirm_date', '<=', '2018-07-30');
					  })			        
				->where('ast.status', config('constants.PACKAGE_PURCHASE_STATUS_CONFIRMED'))
				->where('ast.payment_status',config('constants.PAYMENT_PAID'))
				->whereBetween('at.lft_node',[$upline_info->lft_node,$upline_info->rgt_node])					
				->where('at.nwroot_id',$user_info->nwroot_id)	
				->select(DB::RAW('sum(ast.package_qv) as month_sale_qv'))
				->first();		
  
				 if(!empty($pkg_qv)){
			        ${'g'.($key+1).'_sale'} = $pkg_qv->month_sale_qv;
				 } 
			   } 
			 $pro_offer=DB::table(config('tables.PROMOTIONAL_OFFERS').' as pof')
			    ->join(config('tables.PROMOTIONAL_OFFER_SETTINGS').' as pos', 'pos.promo_offer_id','=','pof.promo_offer_id')
				 ->where('pof.offer_key',config('constants.BONUS.TEAM'))	
				 ->where('pos.currency',$currency_id)	
				 ->where('pos.status',config('constants.ACTIVE'))		 
				 ->first();
				/*  
				 echo '<pre>';
				 print_R($pro_offer); die; */

				  $leftbinpnt = $g1_sale; 
				 $rightbinpnt = $g2_sale;
				 $totleftpinpnt = $totrightpinpnt = 0;
				 $leftcryfwd = $rgtcryfwd  = 0;
		         $binary_bonus=DB::table(config('tables.AF_BINARY_BONUS').' as bb')
							->where('bb.account_id',$account_id)
							->orderBy('bid', 'Desc')
		     				->selectRaw(DB::raw("bid,account_id,leftcarryfwd,rightcarryfwd,totleftbinpnt,totrightbinpnt"))
							->first(); 
					
					  if(!empty($binary_bonus)){
					  $totleftbinpnt=$binary_bonus->totleftbinpnt + $leftbinpnt;
					  $totrightbinpnt=$binary_bonus->totrightbinpnt + $rightbinpnt;
					  $leftcryfwd = $binary_bonus->leftcarryfwd;
					  $rgtcryfwd = $binary_bonus->rightcarryfwd;
					  $handleamt = 0;
					  $leftclubpoint = $leftbinpnt + $leftcryfwd;
					  $rightclubpoint = $rightbinpnt + $rgtcryfwd;
					  if($leftclubpoint > $rightclubpoint){
							   $cluppnt = $rightclubpoint;				 
					}
					  else if($rightclubpoint > $leftclubpoint){
					   $cluppnt=$leftclubpoint;
					}
				$bonus_qv=$cluppnt * $pro_offer->bonus_perc/100;
			    $current_rate=1;
			   if(config('constants.DEFAULT_CURRENCY_ID')!=$currency_id){

				
				$rate= $this->get_currency_exchange(config('constants.DEFAULT_CURRENCY_ID'),$currency_id); 
		
				$current_rate=$rate->rate;				
			  }			  	
			  $bonus_amt=$bonus_qv*$current_rate;
		      $upline_capping=DB::table(config('tables.ACCOUNT_TREE').' as at')
				->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as astt','astt.account_id',"=",'at.account_id')
				->where('at.account_id',$account_id)
				->where('at.is_deleted',config('constants.OFF'))
				->selectRaw(DB::raw('sum(astt.weekly_capping_qv) as capping_qv'))
			     ->first();	
			  if($bonus_amt > $upline_capping->capping_qv){
				 $paid_amt= $bonus_amt-$upline_capping->capping_qv;
				 $flushamt =$bonus_amt-$paid_amt;
			  }
		    else{
				$paid_amt=$bonus_amt;
				$flushamt='';
		      }
			 }
			 else{
			
			 $totleftbinpnt=0;
			 $totrightbinpnt=0;
			 $leftcryfwd=0;
			 $rgtcryfwd=0;
			 $leftclubpoint=0;
		     $rightclubpoint =0;
			 $cluppnt =0;
             $bonus_qv=0;			 
             $bonus_amt=0;	
             $paid_amt=0;
			 $flushamt =0;			 
		 }
   $current_date2 = getGtz();
   $form_date=date('Y-m-d H:i:s', strtotime('-7 days', strtotime($current_date2)));
   $to_date=date('Y-m-d H:i:s', strtotime('-1 days', strtotime($current_date2))); 
                        $sdata['account_id']=$account_id;
					    //$sdata['package_id']=$upline_info->package_id;
						$sdata['bonus_value']= $bonus_qv;
						$sdata['bonus_value_in']=0;
						$sdata['bonus_type']=$pro_offer->promo_offer_id	;
						$sdata['type']=$pro_offer->promo_offer_id;;
						$sdata['leftbinpnt']= $leftbinpnt;
						$sdata['rightbinpnt']=$rightbinpnt;
					    $sdata['leftclubpoint']=$leftclubpoint;
						$sdata['rightclubpoint']=$rightclubpoint;
						$sdata['clubpoint']=$cluppnt;
						$sdata['totleftbinpnt']=$totleftbinpnt;
						$sdata['totrightbinpnt']=$totrightbinpnt;
						$sdata['leftcarryfwd']=$leftcryfwd;
						$sdata['rightcarryfwd']=$rgtcryfwd;
						$sdata['income']=$bonus_amt;
						$sdata['flushamt']=$flushamt;
						$sdata['paidinc']=$paid_amt;
						$sdata['wallet_id']=$pro_offer->wallet;
						$sdata['status']=config('constants.BONUS.STATUS_PENDING');
						$sdata['from_date']=$form_date;
						$sdata['to_date']=$to_date;
						$sdata['date_for']='';
						$sdata['created_date']=getGTZ();
						DB::table(config('tables.AF_BINARY_BONUS'))
					       ->insert($sdata);   
	            } 
		   }
	  public function get_currency_exchange($from_currency_id,$to_currency_id){
		$result = '';
		$result = DB::table(Config('tables.CURRENCY_EXCHANGE_SETTINGS'))
							->select('rate')
							->where(array('from_currency_id'=>$from_currency_id,'to_currency_id'=>$to_currency_id))
							->first();
		if(!empty($result) && count($result) > 0){              
		  return $result;
		  }
		return false;
	}
	/*Leadership Save */
	 public function leadership_bonus_commission($account_id,$currency_id){
		$g1_sale = 0;
		$g2_sale = 0;
		$g3_sale = 0;
	    $user_count=DB::table(config('tables.ACCOUNT_TREE').' as at')
							->where('at.upline_id',$account_id)
							->where('is_deleted',config('constants.NOT_DELETED'))
							->whereIn('rank', [config('constants.TEAM_GENARATION.1G'),config('constants.TEAM_GENARATION.2G'),config('constants.TEAM_GENARATION.3G')])
							->orderBy('rank', 'ASC')
		     				->selectRaw(DB::raw("account_id,nwroot_id,lft_node,rgt_node"))
							->get();
		
		                /* echo '<pre>';
					  print_R($user_count); die; */
 	       if(count($user_count)==3){
			   
			      foreach($user_count as $key => $user_info){
					  
					  $pkg_qv=DB::table(config('tables.ACCOUNT_TREE').' as at')
					  ->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as ast', function($subquery){
					  $subquery->on('ast.account_id','=','at.account_id')
					  ->where('ast.confirm_date', '>=', '2018-07-01')
					  ->where('ast.confirm_date', '<=', '2018-07-30');
					  })	        
			    ->where('ast.status', config('constants.PACKAGE_PURCHASE_STATUS_CONFIRMED'))
				->where('ast.payment_status',config('constants.PAYMENT_PAID')) 
				->whereBetween('at.lft_node',[$user_info->lft_node,$user_info->rgt_node])					
				->where('at.nwroot_id',$user_info->nwroot_id)
				->select(DB::RAW('sum(ast.package_qv) as month_sale_qv')) 
				->first();				
	
				  if(!empty($pkg_qv)){
			        ${'g'.($key+1).'_sale'} = $pkg_qv->month_sale_qv;
				 }   
				 
			   } 
			     $pro_offer=DB::table(config('tables.PROMOTIONAL_OFFERS').' as pof')
			      ->join(config('tables.PROMOTIONAL_OFFER_SETTINGS').' as pos', 'pos.promo_offer_id','=','pof.promo_offer_id')
				 ->where('pof.offer_key',config('constants.BONUS.LEADERSHIP'))	
				 ->where('pos.currency',$currency_id)	
				 ->where('pos.status',config('constants.ACTIVE'))		 
				 ->first();
				 $leftbinpnt = $g1_sale +$g2_sale; 
				 $rightbinpnt = $g3_sale;
				 $totleftpinpnt = $totrightpinpnt = 0;
				 $leftcryfwd = $rgtcryfwd  = 0;
		         $binary_bonus=DB::table(config('tables.AF_BINARY_BONUS').' as bb')
							->where('bb.account_id',$account_id)
							->orderBy('bid', 'Desc')
		     				->selectRaw(DB::raw("bid,account_id,leftcarryfwd,rightcarryfwd,totleftbinpnt,totrightbinpnt"))
							->first(); 
					  if(!empty($binary_bonus)){
					  $totleftbinpnt=$binary_bonus->totleftbinpnt + $leftbinpnt;
					  $totrightbinpnt=$binary_bonus->totrightbinpnt + $rightbinpnt;
					  $leftcryfwd = $binary_bonus->leftcarryfwd;
					  $rgtcryfwd = $binary_bonus->rightcarryfwd;
					  $handleamt = 0;
					  $leftclubpoint = $leftbinpnt + $leftcryfwd;
					  $rightclubpoint = $rightbinpnt + $rgtcryfwd;
					  if($leftclubpoint > $rightclubpoint){
							   $cluppnt = $rightclubpoint;				 
					}
					  else if($rightclubpoint >$leftclubpoint){
					   $cluppnt=$leftclubpoint;
					}
				$bonus_qv=$cluppnt * $pro_offer->bonus_perc/100;
			    $current_rate=1;
			   if(config('constants.DEFAULT_CURRENCY_ID')!=$currency_id){
				$rate=$this->get_currency_exchange(config('constants.DEFAULT_CURRENCY_ID'),$currency_id); 
				$current_rate=$rate->rate;				
			  }			  	
			  $bonus_amt=$bonus_qv*$current_rate;
		      $upline_capping=DB::table(config('tables.ACCOUNT_TREE').' as at')
				->join(config('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as astt','astt.account_id',"=",'at.account_id')
				->where('at.account_id',$account_id)
				->where('at.is_deleted',config('constants.OFF'))
				->selectRaw(DB::raw('sum(astt.weekly_capping_qv) as capping_qv'))
			     ->first();	
			  if($bonus_amt > $upline_capping->capping_qv){
				 $paid_amt= $bonus_amt-$upline_capping->capping_qv;
				 $flushamt =$bonus_amt-$paid_amt;
			  }
		    else{
				$paid_amt=$bonus_amt;
				$flushamt='';
		      }
			 }
			 else{
			 $totleftbinpnt=0;
			 $totrightbinpnt=0;
			 $leftcryfwd=0;
			 $rgtcryfwd=0;
			 $leftclubpoint=0;
		     $rightclubpoint =0;
			 $cluppnt =0;
             $bonus_qv=0;			 
             $bonus_amt=0;	
             $paid_amt=0;
			 $flushamt =0;			 
		 }
   $current_date2 = getGtz();
   $form_date=date('Y-m-d H:i:s', strtotime('-7 days', strtotime($current_date2))).'<br>';
   $to_date=date('Y-m-d H:i:s', strtotime('-1 days', strtotime($current_date2))); 
                        $sdata['account_id']=$account_id;
					    //$sdata['package_id']=$upline_info->package_id;
						$sdata['bonus_value']= $bonus_qv;
						$sdata['bonus_value_in']=0;
						$sdata['bonus_type']=$pro_offer->promo_offer_id	;
						$sdata['type']=$pro_offer->promo_offer_id;
						$sdata['leftbinpnt']= $leftbinpnt;
						$sdata['rightbinpnt']=$rightbinpnt;
					    $sdata['leftclubpoint']=$leftclubpoint;
						$sdata['rightclubpoint']=$rightclubpoint;
						$sdata['clubpoint']=$cluppnt;
						$sdata['totleftbinpnt']=$totleftbinpnt;
						$sdata['totrightbinpnt']=$totrightbinpnt;
						$sdata['leftcarryfwd']=$leftcryfwd;
						$sdata['rightcarryfwd']=$rgtcryfwd;
						$sdata['income']=$bonus_amt;
						$sdata['flushamt']=$flushamt;
						$sdata['paidinc']=$paid_amt;
						$sdata['wallet_id']=$pro_offer->wallet;
						$sdata['status']=config('constants.BONUS.STATUS_PENDING');
						$sdata['from_date']=$form_date;
						$sdata['to_date']=$to_date;
						$sdata['date_for']='';
						$sdata['created_date']=getGTZ();
						DB::table(config('tables.AF_BINARY_BONUS'))
					       ->insert($sdata);   
	            } 
    }
}