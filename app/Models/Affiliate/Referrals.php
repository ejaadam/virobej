<?php
namespace App\Models\Affiliate;

use App\Models\BaseModel;
use DB;

class Referrals extends BaseModel
{
	public function __construct ()
    {
         parent::__construct(); 
    }
	public function my_referrals($account_id, $arr)
	{
		$gen_data = '';
		extract($arr);
		if (!empty($account_id))
		{
			
		$gen_details=DB::table($this->config->get('tables.ACCOUNT_TREE').' as atr')
			   ->where('atr.upline_id',$account_id)
		       ->orderBy('atr.lft_node', 'ASC')
		       ->select('atr.lft_node','atr.rgt_node','atr.rank');
	    
		if(!empty($gen_details)){	
		   $curUsr_info =$account_id;
	       $parent_details = $this->getUser_treeInfo(['account_id'=>$account_id]);
		   //echo $gen_details->toSql(); die;
				/* 	 $query = DB::table(config('tables.ACCOUNT_TREE').' as ut')

								->join(DB::raw('('.$gen_details->toSql().') as atr'),function($join){
									$join->on('atr.lft_node','>','ut.lft_node');
									//->where('atr.rgt_node','<','ut.lft_node');
								})
						       ->addBinding($gen_details->getBindings(),'select');
						          $details=$query->first();
			               echo '<pre>';		
				    	    print_r($details); die;  */
						$query = DB::table(config('tables.ACCOUNT_TREE').' as ut')
							   ->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'ut.account_id')
								->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'um.account_id')
								->join(config('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.sponsor_id')
								->join(config('tables.ACCOUNT_DETAILS').' as upud', 'upud.account_id', ' = ', 'upum.account_id')
								->join(config('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'um.status')
								->join(config('tables.ACCOUNT_SUBSCRIPTION').' as asub', 'asub.account_id', ' = ', 'um.account_id')
								->join(config('tables.ACCOUNT_SALE_POINTS').' as asp', 'asp.account_id', ' = ', 'um.account_id')
								->join(config('tables.AFF_PACKAGE_LANG').' as pl', function($subquery){
									 $subquery->on('pl.package_id', ' = ', 'ut.recent_package_id')
									 ->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
								})  
						 ->select('ut.account_id','ut.rank as direrank',DB::Raw("(select uname from account_mst where account_id=ut.upline_id) as upline_name"),'um.signedup_on','um.uname','um.mobile',DB::Raw("CONCAT_WS('',ud.firstname,ud.lastname) as full_name"),'um.email','um.status','ut.sponsor_id','upum.uname as sponsor_uname',DB::Raw("CONCAT_WS('',upud.firstname,upud.lastname) as sponsor_full_name"),'ut.recent_package_purchased_on','pl.package_name',DB::Raw("amount as package_amount"),'asp.cv','asp.qv','ut.level',DB::Raw("(ut.level-".$parent_details->level.") as ref_level")); 
					if (isset($from) && isset($to) && !empty($from) && !empty($to))
					{
						$query->whereRaw("DATE(um.signedup_on) >='".date('Y-m-d', strtotime($from))."'");
						$query->whereRaw("DATE(um.signedup_on) <='".date('Y-m-d', strtotime($to))."'");
					}
					else if (!empty($from) && isset($from))
					{
						$query->whereRaw("DATE(um.signedup_on) <='".date('Y-m-d', strtotime($from))."'");
					}
					else if (!empty($to) && isset($to))
					{
						$query->whereRaw("DATE(um.signedup_on) >='".date('Y-m-d', strtotime($to))."'");
					}

					if (isset($search_term) && !empty($search_term))
					{ 
						if(!empty($filterchk) && !empty($filterchk))
						{ 
						  $search_term='%'.$search_term.'%'; 
						  $search_field=['UserName'=>'um.uname','Fullname'=>'CONCAT_WS("",ud.firstname,ud.lastname)','Mobile'=>'ud.mobile','Sponsor'=>'upum.uname'];
						  $query->where(function($sub) use($filterchk,$search_term,$search_field)
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
						  $query->where(function($wcond) use($search_term){
						  $wcond->WhereRaw("um.uname like '%$search_term%'")
						  ->orwhereRaw("concat_ws('',ud.firstname,ud.lastname) like '%$search_term%'")
						  ->orwhereRaw("CONCAT_WS('',ud.mobile) like '%$search_term%'")
						  ->orwhereRaw("upum.uname like '%$search_term%'") ;
							});
							}
					}
					if (isset($account_id) && isset($account_id))
					{
						$query->where('ut.sponsor_id',$account_id);
					}
					if (isset($orderby) && isset($order))
					{
						
						if($orderby == 'uname'){
						$query->orderBy('um.'.$orderby, $order);
						}elseif($orderby == 'mobile'){
							$query->orderBy('ud.'.$orderby, $order);
						}elseif($orderby=='level'){
						$query->orderBy('ut.'.$orderby, $order);
						}elseif($orderby=='signedup_on'){
						$query->orderBy('um.'.$orderby, $order);
						}elseif($orderby=='recent_package_purchased_on'){
						$query->orderBy('ut.'.$orderby, $order);
						}
					}

					if (isset($length) && !empty($length))
					{
						$query->skip($start)->take($length);
					}		
					if (isset($count) && !empty($count))
					{
						//$query->addBinding($gen_details->getBindings());
						return $query->count();
					}
					else
					{
						//$query->addBinding($gen_details->getBindings(),'join');
						$query=$query->get();
					
						if($query)
						array_walk($query, function(&$data) use($parent_details)
						{
							//$data->serial_no=$sr_no;
							$data->package_amount=number_format($data->package_amount,\AppService::decimal_places($data->package_amount), '.', ',');
							$data->status=$this->config->get('constants.status.'.$data->status.'');
							$data->level=$data->level - $parent_details->level;
							$data->signedup_on= showUTZ($data->signedup_on, 'd-M-Y');
							$data->recent_package_purchased_on=showUTZ($data->recent_package_purchased_on, 'd-M-Y H:i:s');
							if($data->mobile==''){
								$data->mobile='';
							}
							//date('d-M-Y H:i:s', strtotime($data->recent_package_purchased_on));
						});
				
					return $query;
					} 
		}
		}
	}
	public function getUser_treeInfo($params = array()) {
		extract($params);

        if(!empty($params)){
			$qry = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as act')
			->join($this->config->get('tables.ACCOUNT_MST') . ' as acm','acm.account_id','=','act.account_id')
			->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as acd','acd.account_id','=','act.account_id');			
				
			if(isset($params['account_id'])) {
				$qry->where('act.account_id','=',$account_id);
				$qry->where('acm.is_deleted',$this->config->get('constants.OFF'));
			}
            $qry->select('act.account_id','acm.uname',DB::Raw("concat_ws(' ',acd.firstname,acd.lastname) as full_name"),'act.upline_id','act.sponsor_id','act.my_extreme_right','act.rank','act.level','acm.signedup_on','acm.activated_on','acm.block','acm.uname',DB::Raw("((act.rgt_node-act.lft_node) DIV 2) as team_count"),'act.nwroot_id','act.lft_node','act.rgt_node');
            $res = $qry->first();

            if ($res) {
                return $res;
            }
        }
		return NULL;
    }

	public function my_team_details($account_id, $arr)
	{
		extract($arr);
		$parent_details = $this->getUser_treeInfo(['account_id'=>$account_id]);
		 if (!empty($parent_details))
		 {
			$curUsr_info =$parent_details->account_id;
			$query = DB::table(config('tables.ACCOUNT_TREE').' as ut')
						->join(config('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'ut.account_id')
						->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'um.account_id')
						->join(config('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.upline_id')
						->join(config('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'ut.sponsor_id')
					    ->join(config('tables.ACCOUNT_SALE_POINTS').' as asp', 'asp.account_id', ' = ', 'um.account_id')
						->join(config('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'um.status')
						->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)
						{ 
			        		$subquery->on('pl.package_id', ' = ', 'ut.recent_package_id')
							->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
						}) 
						->join(config('tables.ACCOUNT_AF_RANKING_LOG').' as ar', function($subquery)
						{ 
			        		$subquery->on('ar.account_id', ' = ', 'ut.account_id')
							->where('ar.status', '=', config('constants.ON'));
						}) 
						 ->join(config('tables.AF_RANKING_LOOKUP').' as arl', 'arl.af_rank_id', ' = ', 'ar.af_rank_id')
						->select('ut.account_id','um.signedup_on','um.uname','ut.upline_id','um.mobile',DB::Raw("CONCAT_WS('',ud.firstname,ud.lastname) as full_name"),'um.email','um.status','ut.sponsor_id','upum.uname as upline_uname','spum.uname as direct_sponser_uname','pl.package_name','pl.lang_id','ut.recent_package_purchased_on','ut.level','asp.qv','asp.cv',DB::Raw("(ut.level-".$parent_details->level.") as level"),'arl.rank');
					
			if (isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date))
			{
				$query->whereRaw("DATE(um.signedup_on) >='".date('Y-m-d', strtotime($from_date))."'");
				$query->whereRaw("DATE(um.signedup_on) <='".date('Y-m-d', strtotime($to_date))."'");
			}
			else if (isset($to_date) && !empty($to_date))
			{
				$query->whereRaw("DATE(um.signedup_on) <='".date('Y-m-d', strtotime($to_date))."'");
			}
			else if (isset($from_date) && !empty($from_date))
			{
				$query->whereRaw("DATE(um.signedup_on) >='".date('Y-m-d', strtotime($from_date))."'");
			}
			
			
			if (isset($account_id))  //conditions on left node and right node checking on account_id//
			{
				$query->whereBetween('ut.lft_node', [$parent_details->lft_node,$parent_details->rgt_node])
					  ->where('ut.nwroot_id','=',$parent_details->nwroot_id);
			}
			if (isset($orderby) && isset($order))
			{
			   if($orderby == 'uname'){
				    $query->orderBy('um.'.$orderby, $order);
			   }elseif($orderby == 'full_name'){
				    $query->orderBy('ud.firstname', $order);
			   }elseif($orderby=='upline_uname'){
					$query->orderBy('upum.uname', $order);
			   }elseif($orderby=='sponser_uname'){
					$query->orderBy('spum.uname', $order);
			   }elseif($orderby=='level'){
					$query->orderBy('ut.'.$orderby, $order);
			   }elseif($orderby=='package_name'){
					$query->orderBy('pl.'.$orderby, $order);
			   }elseif($orderby=='purchased_on'){
					$query->orderBy('ut.recent_package_purchased_on', $order);
			   }elseif($orderby=='signedup_on'){
					$query->orderBy('um.signedup_on', $order);
			   }elseif($orderby=='status'){
					$query->orderBy('um.'.$orderby, $order);
			   }
			}
			if (isset($length) && !empty($length))
			{
				$query->skip($start)->take($length);
			}
			if (isset($count) && !empty($count))
			{
				return $query->count();
			}
			else
			{
				$query=$query->orderBy('ut.level', 'ASC'); 
				$query=$query->orderBy('ut.rank', 'ASC');  //level and left node ordered by ascending// 
				$result=$query->get(); 
				
				if(!empty($result)) {
				
				   return $result;
				}
			}
		}		
		return NULL;
	} 
	
	public function my_directs($account_id,$arr,$count=false)
	{  
	extract($arr);
		$parent_details = $this->getUser_treeInfo(['account_id'=>$account_id]);
			
		if (!empty($account_id) && $parent_details)
		{ 
			$query = DB::table($this->config->get('tables.ACCOUNT_TREE').' as ut')
						->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'ut.account_id')
						->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'um.account_id')
						->join($this->config->get('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.upline_id')
						->join($this->config->get('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'ut.sponsor_id')
						->join($this->config->get('tables.ACCOUNT_DETAILS').' as spud', 'spud.account_id', ' = ', 'upum.account_id')
						->join($this->config->get('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'um.status')
						/*->join($this->config->get('tables.ACCOUNT_STATUS_LANG').' as uslg', function($subquery)
							{
							 $subquery->on('uslg.status_id', ' = ', 'usl.status_id')
							  ->where('uslg.lang_id', '=', $this->config->get('app.locale_id'));
							}) */
					      ->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)
							{
							 $subquery->on('pl.package_id', ' = ', 'ut.recent_package_id')
							 ->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
							}) 
						->select('ut.account_id','um.signedup_on','um.uname','um.mobile',DB::Raw("CONCAT_WS('',ud.firstname,ud.lastname) as full_name"),'um.email','um.status','ut.sponsor_id','upum.uname as upline_uname','spum.uname as direct_sponser_uname','ut.recent_package_purchased_on',DB::Raw("(ut.level-".$parent_details->level.") as level"),'ut.rank');
					
			
			if (!empty($from) && isset($from))
			{ 
				$query->whereRaw("DATE(um.signedup_on) >='".date('Y-m-d', strtotime($from))."'");
			}
			if (!empty($to) && isset($to))
			{ 
				$query->whereRaw("DATE(um.signedup_on) <='".date('Y-m-d', strtotime($to))."'");
			} 
			if (isset($search_term) && !empty($search_term))
			{ 
			  // echo "DfsFD"; die;
			
                if(!empty($filterchk) && !empty($filterchk))
				{   
				    $search_term='%'.$search_term.'%'; 
				    $search_field=['UserName'=>'um.uname','FullName'=>'CONCAT_WS("",ud.firstname,ud.lastname)','InvitedBy'=>'spum.uname'];
					$query->where(function($sub) use($filterchk,$search_term,$search_field){
						foreach($filterchk as $search)
						{   
							if(array_key_exists($search,$search_field)){
							  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_term);

							} 
						}
					});
				}else{
					$query->where(function($wcond) use($search_term){
					   $wcond->Where('um.uname','like',$search_term)
							 ->orwhere(DB::raw('CONCAT_WS("",ud.firstname,ud.lastname)'),'like',$search_term)
							 ->orwhere('spum.uname','like',$search_term);
					}); 
				} 			
			}
			if (isset($account_id))
			{
				$query->where('ut.upline_id',$account_id);
			}
			
			
			if ($count)
			{
				return $query->count();
			}
			else
			{
			if (isset($length) && !empty($length))
			{
				$query->skip($start)->take($length);
			}
			if (isset($orderby) && isset($order))
			{
			   if($orderby == 'uname'){
				    $query->orderBy('um.'.$orderby, $order);
			   }elseif($orderby == 'full_name'){
				    $query->orderBy('ud.firstname', $order);
			   }elseif($orderby=='sponser_uname'){
					$query->orderBy('spum.uname', $order);
			   }elseif($orderby=='level'){
					$query->orderBy('ut.level', $order);
			   }elseif($orderby=='package_name'){
					$query->orderBy('pl.'.$orderby, $order);
			   }elseif($orderby=='purchased_on'){
					$query->orderBy('ut.recent_package_purchased_on', $order);
			   }elseif($orderby=='signedup_on'){
					$query->orderBy('um.'.$orderby, $order);
			   }elseif($orderby=='status'){
					$query->orderBy('um.'.$orderby, $order);
			   }
			}
				$query=$query->orderBy('ut.rank', 'ASC'); 
				$result=$query->get();
				
				if(!empty($result)) {
				   return $result;
				}   
			}
		}
		return NULL;
	}
		
	public function get_users_info ($user_id)
    {
       
        $logged_user_id = $user_id;
		
		if (!empty($logged_user_id))
     	{
			 $res = DB::table($this->config->get('tables.ACCOUNT_MST') . ' as um')
                    ->leftJoin($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud', 'ud.account_id', '=', 'um.account_id')
                    ->where('um.account_id', '=', $user_id)
                    ->where('um.is_deleted', '=', 0)
                    ->select(DB::raw('um.account_id,um.uname,concat(ud.firstname," ",ud.lastname) as full_name,um.activated_on,um.signedup_on'))
                    ->first();
			
			return !empty($res) ? $res : false;
		}
	}
		
	public function	get_sponsor_users ($params)
	{   
		extract($params);
		if(isset($parent_acinfo) && $account_id > 0){
			$qry = DB::table($this->config->get('tables.ACCOUNT_TREE').' as ut');
			$qry->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'ut.account_id');		
        	$qry->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'ut.account_id');	
			$qry->join($this->config->get('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'ut.sponsor_id');
			$qry->join($this->config->get('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.upline_id');			
			$qry->where('ut.sponsor_id','=',$account_id);
			$qry->where('um.is_deleted',$this->config->get('constants.NOT_DELETED'));
        	$qry->orderBy('ut.rank', 'ASC');
			$qry->select(DB::raw("um.account_id,ut.sponsor_id,ut.upline_id,um.uname as username,concat_ws(' ',ud.firstname,ud.lastname) as fullname,spum.uname as sponser_uname,upum.uname as upline_uname,um.status,um.block,um.can_sponsor,(ut.level-".$parent_acinfo->level.") as level,um.signedup_on ,um.activated_on,um.block_login"));
			$result = $qry->get();
			if(!empty($result)) {
					array_walk($result,function(&$ftdata) {
					$ftdata->signedup_on = date('d-M-Y ',strtotime($ftdata->signedup_on));
					$ftdata->activated_on = date('d-M-Y ',strtotime($ftdata->activated_on));
				});		
			return !empty($result) ? $result : NULL;			
		    }
		return NULL;
		
	    } 
	}		

			
	public function get_direct_users ($params)
	{	
		extract($params);
		if(isset($parent_acinfo) && $account_id > 0){
			$qry = DB::table($this->config->get('tables.ACCOUNT_TREE').' as ut');
			$qry->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', ' = ', 'ut.account_id');		
        	$qry->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'ut.account_id');	
			$qry->join($this->config->get('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'ut.upline_id');
			$qry->join($this->config->get('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'ut.sponsor_id');		
			$qry->where('ut.upline_id','=',$account_id);
			$qry->where('ut.lineage','like','%/'.$parent_acinfo->account_id.'/%');
			$qry->where('um.is_deleted',$this->config->get('constants.OFF'));
        	$qry->orderBy('ut.rank', 'ASC');
			$qry->select(DB::raw("ut.upline_id,um.account_id,um.uname as username,concat_ws(' ',ud.firstname,ud.lastname) as fullname,upum.uname as upline_uname,spum.uname as sponser_uname,um.status,um.block,ut.can_sponsor,(ut.level-".$parent_acinfo->level.") as level,um.signedup_on,um.activated_on,((ut.rgt_node-ut.lft_node) DIV 2) as team_count,ut.referral_cnts,ut.referral_paid_cnts,um.login_block"));
			$res = $qry->get();	
	
				if(!empty($res)) { 
					array_walk($res,function(&$ftdata) { 
						$ftdata->signedup_on = date('d-M-Y ',strtotime($ftdata->signedup_on));
						$ftdata->activated_on = date('d-M-Y ',strtotime($ftdata->activated_on));
				    });
			        return !empty($res) ? $res : NULL;	
		        }
			return NULL;
	    }
    }	
}	