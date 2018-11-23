<?php
namespace App\Models\Api\User;

use DB;
use File;
use TWMailer;
use App\Models\BaseModel;
use App\Models\LocationModel;
use App\Models\Api\CommonModel;

class AffModel extends BaseModel {
	
    public function __construct() {
        parent::__construct();		
		$this->commonObj = new CommonModel();
		$this->lcObj = new LocationModel;
    }
	
    public function update_password ($account_id, $conf_password)
    {		
        return DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update(['pass_key'=>md5($conf_password)]);
    }

    public function save_account ($arr)
    {	
		$refUser_info = '';		
		$sponsor_lineage = '';	
		extract($arr);
		$account_mst['user_code'] = $user_code = rand(1111111111, 9999999999);
		//$account_mst['uname'] = $user_code;		
		$account_mst['uname'] = strtolower(preg_replace('/\s+/', '', $arr['full_name'])).rand(11,99);
        $account_mst['email'] = $arr['email'];
        $account_mst['mobile'] = !empty($arr['mob_number']) ? $arr['mob_number'] : NULL;
		//$account_mst['is_affiliate'] = $this->config->get('constants.ON');
        $account_mst['pass_key'] = md5($arr['password']);
        $account_mst['trans_pass_key'] = md5($user_code);
        $account_mst['last_active'] =  getGTZ();
        $account_mst['signedup_on'] = getGTZ();
        $account_mst['status'] = $this->config->get('constants.ON');
        $account_mst['account_type_id'] = $this->config->get('constants.ACCOUNT_TYPE.USER');	
		DB::beginTransaction();
        $id = DB::table($this->config->get('tables.ACCOUNT_MST'))->insertGetId($account_mst);		
		if ($id > 0) {		
			$activation_key = rand(1111, 9999) . time();
			if(isset($referral_code)&&!empty($referral_code)){
				$sponsor_info = $this->referral_code_check($referral_code);
				if(!empty($sponsor_info)&&isset($sponsor_info['sponser_account_id'])){
					$setting['my_sponsor']= $sponsor_info['sponser_account_id'];
				}
			}
			/* if (isset($sponser_code) && !empty($sponser_code)) {
				$sponsor_info = $this->referral_user_check($sponser_code);		
					
				if ($sponsor_info) {
					$refUser_info = $this->getUser_treeInfo(['account_id'=>$sponsor_info['sponser_account_id']]);	
					$sponsor_lineage = $refUser_info->sponsor_lineage.$refUser_info->account_id. '/';
					
				}
			}
			$account_tree['account_id'] = $id;
			$account_tree['sponsor_id'] = isset($refUser_info->account_id) ? $refUser_info->account_id : '';
			$account_tree['sponsor_lineage'] = $sponsor_lineage;			
			$account_tree['rank'] = 0;
			$account_tree['level'] = 0;
			DB::table($this->config->get('tables.ACCOUNT_TREE'))->insertGetId($account_tree); 		 */	
			
            $account_details['account_id'] = $id;            
            //$account_details['firstname'] = $arr['full_name'];
			$name = explode(' ', $arr['full_name']);		
            $account_details['firstname'] = $name[0];
            $account_details['lastname'] = $name[1];
            //$account_details['status_id'] = 1;
            //$account_details['created_on'] = getGTZ();
            DB::table($this->config->get('tables.ACCOUNT_DETAILS'))->insertGetId($account_details);  	
			
            $setting['account_id'] = $id;
			$setting['country_id'] =  isset($country) ? $country : 0;
			//$setting['currency_id'] = 2;
			$setting['activation_key'] = $activation_key;
			$setting['is_mobile_verified'] = $this->config->get('constants.ON');			
			if (isset($country))
            {
                $res = $this->commonObj->country_details($country);
                if (!empty($res))
                {
                    $setting['currency_id'] = $res->currency_id;
                }
            }
            DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))->insertGetId($setting);
			
			$addData = [];
            $addData['post_type'] = $this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT');
            $addData['relative_post_id'] = $id;
			$addData['address_type_id'] = $this->config->get('constants.ADDRESS.PRIMARY');
            $addData['country_id'] = isset($country) ? $country : 0;   
			DB::table($this->config->get('tables.ADDRESS_MST'))->insertGetId($addData);            
			
			$val['account_id']= $id;
			$val['user_name'] = $user_code;
			$val['user_email'] = $arr['email'];
		    $val['country'] = $country_name;
		    $val['country_id'] = $country; 
			$val['activation_key'] = $activation_key;
			$val['t_pin'] = $user_code;
			$val['sponser_email'] = isset($refUser_info->email) ? $refUser_info->email : '';
		    $val['sponser_details']= $refUser_info;
			DB::commit();
			return (object) $val;
		}
		DB::rollback();
        return false;
    }	
	
	public function getUser_treeInfo($params = array()) 
	{		
		extract($params);
        if(!empty($params)){
			$qry = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as act')
			->join($this->config->get('tables.ACCOUNT_MST') . ' as am','am.account_id','=','act.account_id')			
			->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as acd','acd.account_id','=','act.account_id')		
			->leftJoin($this->config->get('tables.ACCOUNT_MST') . ' as spam','spam.account_id','=','act.sponsor_id');			
			if(isset($params['account_id'])) {
				$qry->where('act.account_id','=',$account_id);
				$qry->where('am.is_deleted',$this->config->get('constants.OFF'));
			    $qry->select(DB::Raw("act.referral_cnts,am.signedup_on,act.account_id,am.account_type_id,am.uname,am.status,am.block,am.status,am.email,concat_ws(' ',acd.firstname,acd.lastname) as full_name,act.upline_id,act.sponsor_id,act.my_extream_right,act.lft_node,act.rgt_node,act.rank,act.level,FLOOR((act.rgt_node - act.lft_node)/2) as my_team_cnt,act.sponsor_lineage,spam.uname as referrer_name,spam.email as referrer_email"));
				$res = $qry->first();					
			}
            if ($res) {			
                return $res;				
            }
        }
		return NULL;
    }
	
	public function referral_user_check ($user_code)
    {
        if (isset($user_code))
        {				
		$result = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
			        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                    ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as acs', 'acs.account_id', '=', 'ad.account_id')					
					->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'acs.country_id')
                    ->where('am.user_code','=',$user_code) 
                    ->whereIn('am.account_type_id',  [2,3])
                    ->where('am.status', 1)
                    ->where('am.block', 0)
                    ->where('am.is_affiliate', 1)
                    ->where('am.login_block', 0)
                    ->where('am.is_deleted', 0)
            ->select('am.account_id','am.uname','account_type_id','am.email','am.mobile','ad.firstname','ad.lastname','lc.country as country_name','lc.phonecode', 'acs.is_verified')
		    ->first();					
            
			if (empty($result))
            {
             $status['status'] = 'err';
             $status['msg'] = trans('affiliate/general.sponsor_not_found');
            }
            else
            {
                $status['status'] = 200;
                $status['sponser_account_id'] = $result->account_id;
                $status['account_type_id'] = $result->account_type_id;
                $status['sponser_uname'] = $result->uname;
                $status['sponser_email'] = $result->email;
                $status['sponser_fullname'] = $result->firstname." ".$result->lastname;
                $status['sponser_country'] = $result->country_name;
                $status['sponser_mobile'] = $result->phonecode.' '.$result->mobile;
            }
        }
        else
        {
            $status['status'] = 'err';
            $status['msg'] = trans('affiliate/general.sponsor_id_not_exist');
        }
        return $status;
    }
	
	public function referral_code_check ($referral_code)
    {
        if (isset($referral_code))
        {				
		$result = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
			        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                    ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as acs', 'acs.account_id', '=', 'ad.account_id')					
					->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'acs.country_id')
                    ->where('acs.referral_code','=',$referral_code) 
                    ->where('am.status', 1)
                    ->where('am.block', 0)
                    ->where('am.login_block', 0)
                    ->where('am.is_deleted', 0)
            ->select('am.account_id','am.uname','account_type_id','am.email','am.mobile','ad.firstname','ad.lastname','lc.country as country_name','lc.phonecode','acs.is_verified')
		    ->first();					
            
			if (empty($result))
            {
             $status['status'] = 'err';
             $status['msg'] = trans('affiliate/general.sponsor_not_found');
            }
            else
            {
                $status['status'] = 200;
                $status['sponser_account_id'] = $result->account_id;
                $status['account_type_id'] = $result->account_type_id;
                $status['sponser_uname'] = $result->uname;
                $status['sponser_email'] = $result->email;
                $status['sponser_fullname'] = $result->firstname." ".$result->lastname;
                $status['sponser_country'] = $result->country_name;
                $status['sponser_mobile'] = $result->phonecode.' '.$result->mobile;
            }
        }
        else
        {
            $status['status'] = 'err';
            $status['msg'] = trans('affiliate/general.sponsor_id_not_exist');
        }
        return $status;
    }
	
	public function updateProfile (array $arr)
    {	
        $created_on = date('Y-m-d H:i:s');
        if (!empty($arr))
        {
            extract($arr);
			if(!empty($last_name)){
				$lastname = $last_name;
			}
			else
			{
					$lastname='';
			}
				
            $res = DB::table(config('tables.ACCOUNT_DETAILS'))
                 ->where('account_id', $account_id)
                 ->update(['firstname'=>$first_name, 'lastname'=>$lastname,'updated_on'=>$created_on]);
				 
			if(!empty($res)){
				 DB::table(config('tables.ACCOUNT_MST'))
                 ->where('account_id', $account_id)
                 ->update(['uname'=>$display_name]);
			} 
                   return $res;
        }
    }
	
	public function saveProfilePIN (array $arr = array())
	{   
        extract($arr);
        return DB::table(config('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update(['security_pin'=>md5($security_pin)]);
    }
	
	public function VerifyProfilePIN (array $arr = array()){
        extract($arr);
        $query= DB::table(config('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
						->select('security_pin');
						$result=$query->first();
						return $result;
    } 
	
	public function changeEmailID (array $arr = array()) {
        extract($arr);
        return DB::table(config('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->where('email', $old_email)
                        ->update(['email'=>$new_email]);
    }
	
	public function changeMobileNo (array $arr = array()) {
        extract($arr);
        return DB::table(config('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->where('mobile', $old_mobile)
                        ->update(['mobile'=>$new_mobile]);
    }
	
	public function country_list (array $arr = array())
    {
        $result = DB::table($this->config->get('tables.LOCATION_COUNTRY').' as lc')
                ->where('lc.status', $this->config->get('constants.ON'))                
                ->where('lc.operate', $this->config->get('constants.ON'))
                ->orderby('lc.country_id', 'ASC')
                ->selectRAW('lc.country_id, lc.country, lc.phonecode as phone_code, lc.iso2 as code')
                ->get();  
		
		foreach ($result as $value) {
			$value->icon = asset($this->config->get('path.FLAGS_PATH').strtolower($value->code).'.png'); 
			if (!empty($arr))
			{
				if($value->country_id == $arr['country_id'])
				{
					$value->isSelected = 1;
				} else {
					$value->isSelected = 0;
				}
			}
		}
		return $result;			
    }

    public function edit_profile(array $arr = array()){
	 extract($arr);
        return DB::table(config('tables.ACCOUNT_MST').' as am')
		      ->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                        ->where('am.account_id', $account_id)
                        ->select('ad.firstname','ad.lastname','am.uname')
						->first();
    }	
	
   	public function user_name_check ($arr )
    {
        extract($arr);
	    $qry = DB::table(config('tables.ACCOUNT_MST'))
			->where('account_type_id',config('constants.ACCOUNT_TYPE.USER'))
			->where('is_deleted',config('constants.OFF')); 
          if (is_array($account_type_id)){
                        $qry->whereIn('account_type_id', $account_type_id);
                     } 
           if(isset($email) && !empty($email)){
           $qry ->where('email', $email);
			} 
	   $result =$qry->first();
        if (!empty($result)) {
            return $result;
        }
        return NULL;
    }
	
	public function reset_pwd ($account_id, $newpwd){
        $qry= DB::table(Config('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update(['pass_key'=>md5($newpwd)]);
						return $qry;
    }

	
	public function country_details ($country_id)
    {
        return  DB::table($this->config->get('tables.LOCATION_COUNTRY').' as lc')
                ->where('lc.status', $this->config->get('constants.ON'))                
                ->where('lc.operate', $this->config->get('constants.ON'))
                ->where('lc.country_id', $country_id)->selectRaw('phonecode as phone_code, country_id, iso2 as country_code,country as country_name')->first();        
                    
    }
	
	public function update_browse_country (array $arr)
    {
        return true;       
    }
	
	public function get_account_id (array $arr)
    {
		extract($arr);
        return DB::table(config('tables.ACCOUNT_MST'))->where('mobile', $mobile)->value('account_id');  
    }
	
	public function get_account_details (array $arr)
    {
		extract($arr);
        return DB::table(config('tables.ACCOUNT_MST'))->where('mobile', $mobile)->selectRaw('account_id');  
    }


}