<?php
namespace App\Models\Affiliate;

use DB;
use File;
use TWMailer;
use App\Models\BaseModel;
use App\Models\LocationModel;

class AffModel extends BaseModel {
	
    public function __construct() {
        parent::__construct();		
		$this->lcObj = new LocationModel;
    }
	
    public function account_validate($postdata) {
		$status = '';
		$fisrtQry = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->where(function($c) use($postdata){
					$c->where('uname',$postdata['uname'])
					->orWhere('email',$postdata['uname']);
				})
                ->where('is_affiliate', '=', $this->config->get('constants.ON'))
				->where('is_deleted', '=', $this->config->get('constants.OFF'))
                ->where('login_block', '=', $this->config->get('constants.OFF'))
                ->whereIn('account_type_id',[$this->config->get('constants.ACCOUNT_TYPE.USER'),$this->config->get('constants.ACCOUNT_TYPE.SELLER')])
				->select(DB::Raw('account_id,pass_key,email,uname,mobile,login_block,block,is_affiliate,is_closed,account_type_id'));
				
        $userData = DB::table(DB::raw('('.$fisrtQry->toSql().') as um'))
                ->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud', 'ud.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as st', 'st.account_id', '=', 'um.account_id')
				->join($this->config->get('tables.ACCOUNT_TREE') . ' as atr', 'atr.account_id', '=', 'um.account_id')
				->join($this->config->get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'um.account_type_id')
				->join($this->config->get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'st.currency_id')			
				->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'st.country_id')			
                ->selectRaw('um.account_id,st.language_id,um.is_affiliate,atr.can_sponsor,um.account_type_id,at.account_type_name,um.is_closed,um.pass_key,um.uname,concat_ws(\' \',ud.firstname,ud.lastname) as full_name,um.mobile,lc.phonecode,ud.profile_img,um.email,st.country_id,st.currency_id,cur.currency as currency_code,um.block,um.login_block,st.is_mobile_verified,st.is_email_verified,st.is_verified')
                ->addBinding($fisrtQry->getBindings())
				->first();	
				
        if (!empty($userData)) {
            if ($userData->is_closed == $this->config->get('tables.OFF')) {
                if ($userData->login_block == $this->config->get('tables.OFF')) {
                    if ($userData->pass_key == md5($postdata['password'])) {
                        $sesdata = array(
                                'account_id' => $userData->account_id,
                                'uname' => $userData->uname,                                
								'full_name' => $userData->full_name,
                                'email' => $userData->email,
								'is_affiliate' => $userData->is_affiliate,
								'can_sponsor' => $userData->can_sponsor,
								'account_type_name' => $userData->account_type_name,
								'account_type_id' => $userData->account_type_id,								
                                'currency_id' => $userData->currency_id,
								'language_id' => $userData->language_id,
								'country_id' => $userData->country_id,
								'currency_code' => $userData->currency_code,
								'is_mobile_verified' => $userData->is_mobile_verified,
								'is_email_verified' => $userData->is_email_verified,
								'currency_code' => $userData->currency_code,                                
                                'is_verified' => $userData->is_verified,
                                'mobile' => $userData->mobile,
                                'phonecode' => $userData->phonecode,								
								'profile_image' => $userData->profile_img);

                        $currentdate = date($this->config->get('constants.date_format'));                       
                        $this->session->put('userdata', (object)$sesdata);						
						
						$userData->token = $this->config->get('device_log')->token;
						$last_active = getGTZ();
						
						DB::table($this->config->get('tables.DEVICE_LOG'))
								->where('device_log_id', $this->config->get('device_log')->device_log_id)
								->update(array('account_id'=>$userData->account_id, 'status'=>$this->config->get('constants.ACTIVE'))); 
						
						DB::table($this->config->get('tables.ACCOUNT_LOGIN_LOG'))
								->insertGetID(array('device_log_id'=>$this->config->get('device_log')->device_log_id, 'account_id'=>$userData->account_id, 'login_on'=>$last_active)); 
						
						DB::table($this->config->get('tables.ACCOUNT_MST'))
								->where('account_id', $userData->account_id)
								->update(array('last_active'=>$last_active));
						
                        return ['status'=>1,'msg'=>'Your are successfully logged in'];
                    } else {
						return ['status'=>3,'msg'=>'Password not matched'];
                    }
                } else {
					return ['status'=>5,'msg'=>'Your account has been blocked. Please contact our adiminstrator'];
                }
            } else {
				return ['status'=>6,'msg'=>'Please check your Username/Email Id1'];
            }
        } else {
			return ['status'=>2,'msg'=>'Please check your Username/Email Id2'];
        }
        return ['status'=>7,'msg'=>'Please check your Username/Email Id3'];
    }
	
	public function sendPwd_resetlink($postdata)
	{
		
		$fisrtQry = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->where("email",'=',$postdata['uname'])
                ->where('is_affiliate', '=', $this->config->get('constants.ON'))
				->where('is_deleted', '=', $this->config->get('constants.OFF'))
                ->where('login_block', '=', $this->config->get('constants.OFF'))
                ->whereIn('account_type_id',[$this->config->get('constants.ACCOUNT_TYPE.USER'),$this->config->get('constants.ACCOUNT_TYPE.SUPPLIER'),$this->config->get('constants.ACCOUNT_TYPE.SELLER')]);
				
		$users = DB::table(DB::raw('('.$fisrtQry->toSql().') as um'))
		       	->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud', 'ud.account_id', '=', 'um.account_id')
				->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as ap', 'ap.account_id', '=', 'um.account_id')
                ->select(DB::Raw("um.account_id,um.pass_key,um.uname,ud.firstname,ud.lastname,concat_ws('',ud.firstname,ud.lastname) as full_name,ap.country_id,um.email"))
				->addBinding($fisrtQry->getBindings())
                ->first();
				
		if (!empty($users) && count($users) > 0)
		{			
			$data = array();
			$data['uname'] = $postdata['uname'];
			$data['reset_code'] = md5($users->account_id."/".date('dtyHis'));
			$data['reset_link'] = route('aff.recoverpwd').'?usrtoken='.$data['reset_code'];
			$data['full_name'] = $users->full_name;
			$data['email'] = $users->email;			
			$data['domain_name'] = $this->siteConfig->site_name;
			$email_data['email']    = $users->email;
			$email_data['siteConfig'] = $this->siteConfig;
			
			$update = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as um')
						->where('um.account_id', '=', $users->account_id)
						->update(array('pwd_reset_key'=>md5($data['reset_code']),'pwd_reset_key_sess'=>date('H:i:s',strtotime('+5 minuts'))));			
						
			/* new User for email */
			$mstatus = TWMailer::send(array(
			 'to'=>$email_data['email'], 
			 'subject'=>'Password reset verification',
			 'view'=>'emails.affiliate.account.send_resetpwd_code',
			 'data'=>$data,
			 'from'=>$this->siteConfig->noreplay_emailid,
			 'fromname'=>$this->siteConfig->site_domain), $this->siteConfig);				 
			
			return ['status'=>1,'msg'=>\trans('affiliate/forgotpwd.validate_msg.resetpwd_code_success'),'mail'=>$mstatus];
		}
		else
	  	{
			return ['status'=>2,'msg'=>\trans('affiliate/forgotpwd.validate_msg.uname_notfound')];
	   	}
	}
	
	public function check_pwdreset_token($postdata)
	{
		$fisrtQry = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE').' as ust1')
				->join($this->config->get('tables.ACCOUNT_MST') . ' as um1', 'um1.account_id', '=', 'ust1.account_id')
                ->where("ust1.pwd_reset_key",'=',$postdata['usrtoken'])
                ->whereRaw("TIME(ust1.pwd_reset_key_sess) >= '".date('H:i:s')."'")
				->select(DB::Raw('um1.account_id,um1.uname,um1.email,ust1.country_id'));
				
		$usrRes = DB::table(DB::raw('('.$fisrtQry->toSql().') as ust'))
				->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ud', 'ud.account_id', '=', 'ust.account_id')
                ->select(DB::Raw("ust.*,ud.firstname,ud.lastname,concat_ws('',ud.firstname,ud.lastname) as full_name,ust.country_id"))
				->addBinding($fisrtQry->getBindings())
                ->first();

		if (!empty($usrRes) && count($usrRes) > 0)
		{					
			return $usrRes;
		}
		else
	  	{
			return NULL;  
	   	}
	}
	
	public function tran_password_check ($oldpassword = 0, $user_id)
    {
        $data = '';
		$data['status'] = 'error';
        $data['msg'] = trans('affiliate/settings/security_pwd.incrct_trans_pwd');
        if ($oldpassword)
        {
            $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where(array(
                        'trans_pass_key'=>md5($oldpassword),
                        'account_id'=>$user_id,
						'is_deleted'=>'0'))
                    ->first();
            if (!empty($result) && count($result) > 0)
            {
                $data['status'] = 'ok';
                $data['msg'] = trans('affiliate/settings/security_pwd.crct_trans_pwd');
            }
        }
        return $data;
    }
	
	
  public function password_check($oldpassword = 0, $user_id)
    {
        $data['status'] = 'error';
        $data['msg'] = trans('affiliate/settings/changepwd.incorrect_pwd');
	
        if ($oldpassword)
        {
            $count = DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where(array(
                        'pass_key'=>md5($oldpassword),
                        'account_id'=>$user_id,
						'is_deleted'=>'0'))
                    ->count();
            if (!empty($count) && count($count) > 0)
            {
                $data['status'] = 'ok';
                $data['msg'] = trans('affiliate/settings/changepwd.correct_pwd');
            }
        }	
		
        return $data;
    }	
	
	
  public function update_password ($user_id, $postdata)
    {
		if($user_id>0 && !empty(trim($postdata['newpassword'])))
		{
			$data['pass_key'] = md5($postdata['newpassword']);
		   
			if ($data['pass_key'] != DB::table($this->config->get('tables.ACCOUNT_MST'))
							->where('account_id', $user_id)
							->value('pass_key'))
			{ 
				$status = DB::table($this->config->get('tables.ACCOUNT_MST'))
					->where('account_id', $user_id)
					->update($data);
				if (!empty($status) && isset($status))
				{
					return json_encode(array(
						'msg'=>trans('affiliate/settings/changepwd.password_change'),
						'alertclass'=>'alert-success'));
				}
				else
				{
					return json_encode(array(
						'msg'=>trans('general.something_wrong'),
						'alertclass'=>'alert-danger'));
				}
			}
			else{
				return json_encode(array(
						'msg'=>trans('affiliate/settings/changepwd.same_as_old'),
						'alertclass'=>'alert-danger'));
			}
		}
        return json_encode(array('msg'=>trans('affiliate/settings/changepwd.missing_parameters'), 'alertclass'=>'alert-warning'));
    }
	
	
	
	public function tran_update_password ($user_id, $postdata)
    {
		if($user_id>0 && !empty(trim($postdata['tran_newpassword'])))
		{
			$data['trans_pass_key'] = md5($postdata['tran_newpassword']);
		   
			if ($data['trans_pass_key'] != DB::table($this->config->get('tables.ACCOUNT_MST'))
							->where('account_id', $user_id)
							->value('trans_pass_key'))
			{ 
				$status = DB::table($this->config->get('tables.ACCOUNT_MST'))
					->where('account_id', $user_id)
					->update($data);
				if (!empty($status) && isset($status))
				{
					return json_encode(array(
						'msg'=>trans('affiliate/settings/security_pwd.password_change'),
						'alertclass'=>'alert-success'));
				}
				else
				{
					return json_encode(array(
						'msg'=>trans('general.something_wrong'),
						'alertclass'=>'alert-danger'));
				}
			}
			else{
				return json_encode(array(
						'msg'=>trans('affiliate/settings/security_pwd.same_as_old'),
						'alertclass'=>'alert-danger'));
			}
		}
        return json_encode(array('msg'=>trans('affiliate/settings/security_pwd.missing_parameters'), 'alertclass'=>'alert-warning'));
    }
	
	public function updateLineage($userSess,$pack_details){
		if(isset($userSess) && !empty($userSess) && !empty($pack_details)){
			$userTRInfo = $this->getUser_treeInfo(['account_id'=>$userSess->account_id]);			
			if(!empty($userTRInfo) && $userTRInfo->upline_id==0 && $userTRInfo->can_sponsor==0){
			 	$updateRightnode = $this->saveNew_linaeage(['account_id' => $userTRInfo->account_id, 'sponsor_id' => $userTRInfo->sponsor_id,'pack_details'=>$pack_details]);	
				if(!empty($updateRightnode)){
					return true;
				}
			}
		}	
	}

    public function save_account($postdata) {

	   $val=[];
        $postdata['trans_pass_key'] = rand(1111,9999);
		$direct_sponsor_id = '';
        $rank = '';
        $level = '';
        $currencies = '';
        $account_code = '';
        $cdate = getGTZ(); 	   
	    $sponser_account_id = $postdata['sponser_account_id'];	   
	    $refUser_info = $this->getUser_treeInfo(['account_id'=>$sponser_account_id]);	 
		
	     $country_info = $this->lcObj->getCountry(['country_code'=>$postdata['country'],'allow_signup'=>true]);
		
        $sponsor_lineage = $refUser_info->sponsor_lineage.$refUser_info->account_id. '/';
        $activation_key = rand(1111, 9999) . time();
        /* --------Assigning the Post Values -------- */
        $insert_account_mst['uname'] = $postdata['username'];
        $insert_account_mst['email'] = $postdata['email'];
		$insert_account_mst['is_affiliate'] = $this->config->get('constants.ON');
        $insert_account_mst['pass_key'] = md5($postdata['password']);
        $insert_account_mst['trans_pass_key'] = md5($postdata['trans_pass_key']);
        $insert_account_mst['last_active'] =  $cdate;
        $insert_account_mst['signedup_on'] = $cdate;
        $insert_account_mst['status'] = $this->config->get('constants.ON');
        $insert_account_mst['account_type_id'] = $this->config->get('constants.ACCOUNT_TYPE.USER');	

        $id = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->insertGetId($insert_account_mst);
	
        if ($id > 0) {
			$insert_account_tree['account_id'] = $id;
			$insert_account_tree['sponsor_id'] = $refUser_info->account_id;
			$insert_account_tree['sponsor_lineage'] = $sponsor_lineage;			
			$insert_account_tree['rank'] = 0;
			$insert_account_tree['level'] = 0;
			DB::table($this->config->get('tables.ACCOUNT_TREE'))
                ->insertGetId($insert_account_tree);
		
            $firstname = $postdata['firstname'];
            $lastname = $postdata['lastname'];
            $insert_account_details = '';
            $insert_account_details['account_id'] = $id;            
            $insert_account_details['firstname'] = $firstname;
            $insert_account_details['lastname'] = $lastname;
            $insert_account_details['updated_on'] = $cdate;
            $udRes = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                    ->insertGetId($insert_account_details);
					
            $insert_setting = '';
            $insert_setting['account_id'] = $id;
			$insert_setting['country_id'] = $country_info->country_id;
			$insert_setting['currency_id'] = $country_info->currency_id;
			$insert_setting['activation_key'] = $activation_key;
            $usRes_ = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
			                    ->insertGetId($insert_setting);
			$val['account_id']= $id;
			$val['user_name'] = $postdata['username'];
			$val['user_email'] = $postdata['email'];
		    $val['country'] = $country_info->country_name;
			$val['activation_key']=$activation_key;
			$val['t_pin']=$postdata['trans_pass_key'];
			$val['sponser_email']=$refUser_info->email;
		    $val['sponser_details']= $refUser_info;
			//$this->session->forget('reg_sponsor_info');		
            return (object)$val;
        }
        return false;
    }

    public function findRightmostElement($arr = array()) {	
        $res = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as ut')
                ->where('ut.rank', '=', 3)
                ->whereRaw("ut.upline_id = (select upline_id from " . $this->config->get('tables.ACCOUNT_TREE') . " where account_id = " . $arr['account_id'] . ")")
                ->select('ut.account_id')
                ->get();
				
			//	print_r($res); die;
        if (!empty($res)) {
            return $res;
        }
    }
//
//    public function getNew_lineages($arr = array()) {
//        $new_rank = 0;
//        $response = '';
//        if (!empty($arr['upline_id'])) {
//            $res = DB::table($this->config->get('tables.ACCOUNT_MST') . ' as um')
//                    ->where('um.upline_id', '=', $arr['upline_id'])                    
//                    ->orderBy('rank', 'desc')
//                    ->select(DB::raw('sponsor_id as sponsor_id ,min(rank) as min_rank ,max(rank) as max_rank,(count(upline_id)) as count,(SUBSTRING_INDEX(GROUP_CONCAT(account_id), ",", -1 )) as account_3g_id'))
//                    ->first();
//            if (!empty($res)) {
//                if ($arr['sponsor_id'] == $res->account_3g_id) {
//                    if ($res->count <= 2) {
//                        if ($res->min_rank == 3) {
//                            $new_rank = 1;
//                        } else {
//                            $new_rank = $res->min_rank + 1;
//                        }
//                        $response = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
//                    } else if ($res->count == 3) {
//                        $response = $this->getNew_lineages(array('upline_id' => $res->account_3g_id, 'sponsor_id' => $arr['sponsor_id']));
//                    } else if ($res->count == 0) {
//                        $new_rank = 1;
//                        $response = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
//                    } else if ($res->min_rank == 3) {
//                        $new_rank = 1;
//                        $response = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
//                    }
//                } else if ($arr['sponsor_id'] != $res->account_3g_id) {
//                    if ($res->count == 0) {
//                        $new_rank = 3;
//                        $response = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
//                    } else if ($res->count > 0) {
//                        if ($res->max_rank == 3) {
//                            $response = $this->getNew_lineages(array('upline_id' => $res->account_3g_id, 'sponsor_id' => $arr['sponsor_id']));
//                        } else {
//                            $new_rank = 3;
//                            $response = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
//                        }
//                    }
//                }
//            }
//        }
//        return $response;
//    }

    public function getNew_lineages($arr = array()) {
        $new_rank = 3;
        $op = NULL;
		
        if (!empty($arr['upline_id'])) {                    
            $res = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as um')
                ->where('um.upline_id',$arr['upline_id'])            
                ->orderBy('rank', 'desc')
                ->select(DB::raw('um.nwroot_id,sponsor_id as sponsor_id ,min(rank) as min_rank ,max(rank) as max_rank,(count(upline_id)) as count,(SUBSTRING_INDEX(GROUP_CONCAT(account_id), ",", -1 )) as account_3g_id,(select my_extream_right from '.$this->config->get('tables.ACCOUNT_TREE').' where account_id=um.upline_id) as my_extream_right'))
                ->first();			
            if (!empty($res)) {               
			
                if ($res->count <= 2) {
                    if($res->min_rank==1 && $res->max_rank==3){
                        $new_rank = 2;
                    } else if ($res->max_rank == 3) {
                        $new_rank = 1;
                    } else {
                        $new_rank = $res->max_rank + 1;
                    }                        
                    $op = $this->getUser_lineageInfo($arr['upline_id'],$new_rank);					
                } else if ($res->count == 3) {
                    $usrRes = $this->getUser_lineageInfo($arr['upline_id'],$new_rank);
                    if(!empty($usrRes) && $usrRes->my_extream_right>0){
                       $op = $this->getUser_lineageInfo($usrRes->my_extream_right,$new_rank);
                    }
					else {
                    	$op = $this->getNew_lineages(array('upline_id' => $res->account_3g_id, 'sponsor_id' => $arr['sponsor_id']));
					}
                } else if ($res->count == 0) {
                    $new_rank = 1;
                    $op = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
                } else if ($res->max_rank == 3) {
                    $new_rank = 1;
                    $op = $this->getUser_lineageInfo($res->account_3g_id, $new_rank);
                }				
            }           
        }
        return $op;
    }
	
	/*
	purpose:  update the new lineage to those who are purchase package 
	params: sponsor_id of new user,
	*/
	
    public function saveNew_linaeage($arr = array()) {     
		if($arr['account_id']>0 && $arr['sponsor_id']>0){
			$new_account_id = $arr['account_id'];
			$lineageInfo = $this->getNew_lineages(['upline_id'=>$arr['sponsor_id'],'sponsor_id'=>$arr['sponsor_id']]);			
			if(!empty($lineageInfo)){			
				$upData = array(
							'upline_id'=>$lineageInfo->account_id,
							'rank'=>$lineageInfo->new_rank,
							'level'=>$lineageInfo->level,							
							'nwroot_id'=>$lineageInfo->nwroot_id,
							'can_sponsor'=>$this->config->get('constants.ON'));

				if(isset($arr['pack_details'])){					
					$upData['recent_package_id']= $arr['pack_details']->package_id;
					$upData['recent_package_purchased_on'] = getGTZ();
				}
				//print_r($lineageInfo);
				$tree_pos = $this->addnode($lineageInfo);
				//print_r($tree_pos);
				//die;
				$upData['lft_node'] = $tree_pos->lft_node;
				$upData['rgt_node'] = $tree_pos->rgt_node;
				
				$upRes = DB::table($this->config->get('tables.ACCOUNT_TREE'))
						->where('account_id', '=', $new_account_id)                    
						->update($upData);
	
				if($upRes){
					if($lineageInfo->new_rank==3){
						$upRes2 = DB::table($this->config->get('tables.ACCOUNT_TREE'))
							->whereRaw("(account_id ='". $lineageInfo->account_id."' OR my_extream_right='".$lineageInfo->account_id."')")
							->update(array(
								'my_extream_right'=>$new_account_id
							));
					}
				   	return true;
			   }
			   else {
				   /* Couldn't able to update user lineage info */
				   return 3;
			   }
			}
			else {
				/* Couldn't able to get lineage info */
				return 2;
			}
		}
		else  {
			/* datas missing */
			return 5;
		}		
    }

    public function getUser_lineageInfo($account_id = '',$new_rank='') {
        if (!empty($account_id)) {
            $result = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as um')
                    ->where('um.account_id', '=', $account_id)
                    ->select('um.level','um.account_id','um.nwroot_id','um.lft_node','um.rgt_node', 'um.my_extream_right')
                    ->first();
            if (!empty($result)) {
                $result->level += 1;
                $result->new_rank = $new_rank;
                $result->account_id = $result->account_id;
                return $result;
            }
        }
        return NULL;
    }
	
    public function getUserinfo($params = array()) {
	    extract($params);
        if (!empty($params) && (isset($account_id) && $account_id > 0 || isset($uname) && $uname != NULL)) {

            $query = DB::table($this->config->get('tables.ACCOUNT_MST') . ' as am')
                    ->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as ad', 'ad.account_id', '=', 'am.account_id')					
					->join($this->config->get('tables.ACCOUNT_PREFERENCE') . ' as ap', 'ap.account_id', '=', 'am.account_id')
					->join($this->config->get('tables.CURRENCIES') . ' as cur', 'cur.currency_id', '=', 'ap.currency_id')			
					->join($this->config->get('tables.LOCATION_COUNTRY') . ' as lc', 'lc.country_id', '=', 'ap.country_id')						
			        ->leftjoin($this->config->get('tables.GENDER_LANG') . ' as gl ', 'gl.gender_id', '=', 'ad.gender')
					->leftjoin($this->config->get('tables.ADDRESS_MST') . ' as adm ', function($join){
						$join->on('adm.relative_post_id', '=', 'ad.account_id')
						->where('adm.post_type','=',$this->config->get('constants.ADDRESS.PRIMARY'));
					})
					->select(DB::raw('am.account_id,am.uname,am.email,am.signedup_on,concat(ad.firstname," ",ad.lastname) as full_name,concat_ws("-",lc.phonecode,am.mobile) as mobile,ad.profile_img,concat_ws(",",adm.flatno_street,adm.landmark,adm.address,adm.postal_code) as address,gl.gender,lc.country_id,lc.country')); 
					
					// print_r($query); die;

			if ($account_id){
				$query ->where('am.account_id', '=', $account_id);
			}else {
                $query ->where('am.uname', '=', $uname);
            }	   
			
			$query->where('am.is_deleted',$this->config->get('constants.OFF'));
			
            $res =  $query ->first();
			
            if ($res) {
                return $res;
            }
        }
        return NULL;
    }
	
	public function getUser_treeInfo($params = array()) {
		extract($params);
        if(!empty($params)){
			$qry = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as act')
			->join($this->config->get('tables.ACCOUNT_MST') . ' as am','am.account_id','=','act.account_id')			
			->join($this->config->get('tables.ACCOUNT_DETAILS') . ' as acd','acd.account_id','=','act.account_id')		
			->leftJoin($this->config->get('tables.ACCOUNT_MST') . ' as spam','spam.account_id','=','act.sponsor_id');
			
			if(isset($params['account_id'])) {
				$qry->where('act.account_id','=',$account_id);
				$qry->where('am.is_deleted',$this->config->get('constants.OFF'));
			    $qry->select(DB::Raw("act.can_sponsor,am.signedup_on,act.account_id,am.account_type_id,am.uname,am.status,am.block,am.status,am.email,concat_ws(' ',acd.firstname,acd.lastname) as full_name,act.upline_id,act.sponsor_id,act.my_extream_right,act.lft_node,act.rgt_node,act.rank,act.level,FLOOR((act.rgt_node - act.lft_node)/2) as my_team_cnt,act.sponsor_lineage,spam.uname as referrer_name,spam.email as referrer_email"));
				$res = $qry->first();					
			}
            if ($res) {			
                return $res;				
            }
        }
		return NULL;
    }

		public function usercheck_for_fundtransfer($username)
		{
			$op = array();
			$op['status'] = 'error';
			$op['msg'] = trans('affiliate/wallet/fundtransfer.invalid_username');
			$op['user_id'] = 0;
			if ($username != '')
			{
				$result = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
						->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
						->where('uname', '=', $username)
						->where('is_deleted', '=', 0)
						->whereIn('account_type_id', [$this->config->get('constants.USER_ROLE_USER'), $this->config->get('constants.USER_ROLE_ROOT_USER')])
						->first();
				if (!empty($result))
				{
					$op['status'] = 'ok';
					$op['msg'] = trans('affiliate/wallet/fundtransfer.user_available');
					$op['account_id'] = $result->account_id;
					$op['full_name'] = $result->firstname.' '.$result->lastname;
					$op['email'] = $result->email;
				}
				else
				{
					$op['status'] = 'error';
					$op['msg'] = trans('affiliate/wallet/fundtransfer.invalid_username');
				}
				return $op;
			}
		}
	public function getUser_loginDetails ($arr='',$fields)
	{
	    $account_id = $arr;
	
        $qry = DB::table($this->config->get('tables.ACCOUNT_MST').' as um');
	    if (is_array($fields))
        {	
          $qry->select($fields);
        }
        if (!is_int($account_id))
        {
           $res= $qry->where('um.uname', $account_id);
        }
        else if (is_int($account_id))
        {
			
            $qry->where('um.account_id', $account_id);
        }
        if ($account_id != '')
        {	
			 
		    return $result = $qry->first();
				 return $result; 
				 //print_r($result);exit;
		}
        return false;
    }
	


    public function account_check($username = 0, $optArrary = array()) {
	
        if ($username) {
            $lineage = '';
            $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where("uname", $username)                    
                    ->where('is_deleted', 0)
                    ->first();
					
            $existscheck = $referralcheck = $account_id = 0;
            $reqfor = '';
            $loguserlineage = '';
            $useravailablility = '';
            if (count($optArrary) > 0) {
                $existscheck = $optArrary['existscheck'];
                $referralcheck = $optArrary['referralcheck'];
                $account_id = isset($optArrary['account_id']) ? $optArrary['account_id'] : '';
                $reqfor = isset($optArrary['reqfor']) ? $optArrary['reqfor'] : '';
                $useravailablility = isset($optArrary['useravailablility']) ? $optArrary['useravailablility'] : '';
                $loguserlineage = $optArrary['loguserlineage'];
            }
            if (empty($result)) {
                if ($existscheck == 1) {
                    $status['status'] = 'error';
                    $status['msg'] = Lang::get('account_common_model.pls_entr_valid_uname');
                } elseif (!(preg_match('/^[a-zA-Z][a-zA-Z0-9]+$/', $username))) {
                    $status['status'] = 'error';
                    $status['msg'] = Lang::get('account_common_model.uname_starts_alphhabets');
                } else {
                    $status['status'] = 'ok';
                    $status['msg'] = trans('account_common_model.uname_available');
                }
            } else { 
                if ($referralcheck == 1) {                    
                    if ($result->account_id == $account_id) {
                        $status['status'] = 'error';
                        $status['msg'] = Lang::get('account_common_model.pls_select_member');
                        return $status;
                    }                    
                } 
				else if ($useravailablility == 1 && $reqfor == 'reg') 
				{
                    $status['status'] = 'error';
                    $status['msg'] = trans('account_common_model.uname_already_exist');
                } 
				else if ($existscheck == 1) 
				{
                    $status['status'] = 'ok';
                    $status['msg'] = '';
                    $status['account_currency_bal'] = json_encode($this->get_account_currency_bal($result->account_id));
                } 
				else 
				{
                    $status['status'] = 'error';
                    $status['msg'] = Lang::get('account_common_model.uname_already_exist');
                }
                $status['account_id'] = $result->account_id;
                $status['uname'] = $result->uname;
            }
        }
    }
	
	
	public function saveBrowserInfo($account_id=0,$account_log_id=0,$purpose='2'){
		
		$bwInfo = \AppService::getBrowserInfo();
		
		$bwInfo['ip'] = \Request::getClientIp(true);
		
		$bwInfo['account_id'] = $account_id;
		$bwInfo['purpose'] = $purpose;
		$bwInfo['browser_info'] = '';
		$bwInfo['country'] = '';
		$bwInfo['location'] = '';
		try {
			$ipInfo = \Location::get($bwInfo['ip']);
			if(!empty($ipInfo)){
				$bwInfo['location'] = $ipInfo->countryName;
				$bwInfo['country'] = $ipInfo->countryCode;
				DB::table($this->config->get('tables.ACCOUNT_BROWSER_INFO'))
						->insertGetID([
							'account_id' => $account_id,
							'purpose' => $purpose, /*forgotpwd*/
							'browser_info' => addslashes(json_encode(\AppService::getBrowserInfo())),
							'country' => \Location::get($bwInfo['ip'])->countryCode]);				
			}
			return (object)$bwInfo;			
		}
		catch(Exception $e){
			return (object)$bwInfo;
		}
	}
	
	public function get_full_Tree ($account_id = 0)
    {
       	/*
        SELECT node.account_id
		FROM account_tree AS node,
				account_tree AS parent
		WHERE node.lft_node BETWEEN parent.lft_node AND parent.rgt_node
				AND parent.account_id = 6
		ORDER BY node.lft_node
        */
        $qry = DB::table(DB::Raw($this->config->get('tables.ACCOUNT_TREE').' as node,'.$this->config->get('tables.ACCOUNT_TREE').' as parent'))
                ->orderBy('node.lft_node', 'ASC');
        $qry->whereBetween('node.rgt_node', ['parent.lft_node','parent.lft_node']);
        $qry->select(DB::raw('node.account_id'));
        if ($account_id > 0)
        {
            $qry->where('parent.account_id', $account_id);
        }
        $result = $qry->get();
        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }
	
	/*public function addnode_org($parent_info)
    {
        $child_exist = 0;
        $increament_val = 2;        
        if (!empty($parent_info))
        {			
            $child_exist = ($parent_info->lft_node == $parent_info->rgt_node-1) ? false :true;
            if ($child_exist)
            {               
				$updateFrom = $parent_info->rgt_node;
                $lft_node = $parent_info->rgt_node+1;
                $rgt_node = $lft_node + 1;			
            }
            else
            {
                $updateFrom = $parent_info->lft_node;
                $lft_node = $parent_info->lft_node+1;
                $rgt_node = $lft_node + 1;
            }

            DB::table($this->config->get('tables.ACCOUNT_TREE'))
                    ->where('rgt_node', '>', $updateFrom)
					->where('nwroot_id', '>', $parent_info->nwroot_id)					
                    ->where('is_deleted', '=', $this->config->get('constant.OFF'))
                    ->increment('rgt_node', $increament_val);

            DB::table($this->config->get('tables.ACCOUNT_TREE'))
                    ->where('lft_node', '>', $updateFrom)
					->where('nwroot_id', '>', $parent_info->nwroot_id)	
                    ->where('is_deleted', '=', $this->config->get('constant.OFF'))
                    ->increment('lft_node', $increament_val);    
            
            // update query will come here
            return ['lft_node'=>$lft_node,'rgt_node'=>$rgt_node];
        }
    }*/
   
    public function addnode($parent_info)
    {
        $child_exist = 0;
        $increament_val = 2;        
        if (!empty($parent_info))
        {			
           $child_exist = ($parent_info->lft_node == $parent_info->rgt_node-1) ? false :true;		   
           //print_r($parent_info);
		   if ($child_exist)
            {
                if($parent_info->new_rank==1){
					$updateFrom = $parent_info->lft_node + 1;
					$lft_node = $updateFrom;
					$rgt_node = $lft_node + 1;					
				} 
				else if($parent_info->new_rank==2){
					$cildRes = DB::table($this->config->get('tables.ACCOUNT_TREE') . ' as act')
							->where('act.upline_id','=',$parent_info->account_id)
							->select('act.lft_node','act.rgt_node')
							->orderby('act.lft_node','ASC')
							->get();
							
					if(count($cildRes)==2){
						$updateFrom = $cildRes[0]->rgt_node+1;
						$lft_node = $updateFrom;
						$rgt_node = $lft_node + 1;
					} 
					else {
						$updateFrom = $parent_info->rgt_node;
						$lft_node = $parent_info->rgt_node;
						$rgt_node = $lft_node + 1;
					}
				} 
				else if($parent_info->new_rank==3){
					$updateFrom = $parent_info->rgt_node;
					$lft_node = $parent_info->rgt_node+1;
					$rgt_node = $lft_node + 1;
				}				
            }
            else
            {
                $updateFrom = $parent_info->rgt_node;
                $lft_node = $updateFrom;
                $rgt_node = $lft_node + 1;
            }
			
            DB::table($this->config->get('tables.ACCOUNT_TREE'))
                    ->where('rgt_node', '>=', $updateFrom)
					->where('nwroot_id', '=', $parent_info->nwroot_id)					
                    ->increment('rgt_node', $increament_val);

            DB::table($this->config->get('tables.ACCOUNT_TREE'))
                    ->where('lft_node', '>=', $updateFrom)
					->where('nwroot_id', '=', $parent_info->nwroot_id)	                    
                    ->increment('lft_node', $increament_val);
            
            /* update query will come here */           
            return (object)['lft_node'=>$lft_node,'rgt_node'=>$rgt_node];
        }
    }
	
	public function check_account_verification_count ($arr = array())
    {
        $datalist_qry = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as uv')
                ->join($this->config->get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'uv.document_type_id')
                ->where('uv.account_id', $arr['account_id'])
                ->whereIn('uv.status', array(0,1))
                ->where('uv.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->whereIn('dt.proof_type', $arr['prooftypes'])
                ->select(DB::Raw('distinct(proof_type) as proof_type_id,count(distinct(proof_type)) as cnt'))
                ->groupby('dt.proof_type');
        if (isset($arr['document_type']) && is_array($arr['document_type']))
        {
            $datalist_qry->whereIn('uv.document_type_id', $arr['document_type']);
        }
        if (isset($arr['prooftypes']) && is_array($arr['prooftypes']) && count($arr['prooftypes']) == 1)
        {
            $datalist = $datalist_qry->first();
        }
        else
        {
            $datalist = $datalist_qry->get();
        }
		
        $data = array();
        if (!empty($datalist))
        {
            if (is_array($datalist) && count($datalist) > 0)
            {
                foreach ($datalist as $item)
                {
                    $data[$item->proof_type_id] = $item->cnt;
                }
            }
            else
            {
                $data[$datalist->proof_type_id] = $datalist->cnt;
            }
            return $data;
        }
        return false;
    }
	
	public function save_account_upload ($arr = array(),$userSess)
    {
        $res = false;		
        if (!empty($arr))
        {
            $res = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
                    ->insert($arr);
        }
        if ($res)
        {
            if ($userSess->is_verified == $this->config->get('constants.OFF'))
            {
                DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
                        ->where('account_id', $arr['account_id'])
                        ->update(array(
                            'is_verified'=>$this->config->get('constants.OFF')));
            }
        }
        return $res;
    }
	
	public function account_kycdoc($data)
    {
        extract($data);
        $proof_types = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as uv')
                ->join($this->config->get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', ' = ', 'uv.document_type_id')
                ->where('uv.account_id', $account_id)
				->where('uv.status', $this->config->get('constants.ACTIVE'))
				->where('uv.is_deleted', $this->config->get('constants.NOT_DELETED'))                                
                ->havingRaw('count(uv.uv_id)>0')
                ->groupby('dt.proof_type')
                ->lists('dt.proof_type');		
					
		/*$prflang = DB::table($this->config->get('tables.PROOF_DOCTYPES_LANG').' as prfl')
					->where('prfl.proof_type_id','=','dt.proof_type')
					->select('desc');	*/
		
        $query = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION').' as uv')                
                ->join($this->config->get('tables.DOCUMENT_TYPES').' as dt',"dt.document_type_id",'=',"uv.document_type_id")
				->join($this->config->get('tables.PROOF_DOCTYPES').' as pdt', 'pdt.id', ' = ', 'dt.proof_type')
				->select(DB::raw("uv.uv_id,uv.account_id,uv.path,uv.document_type_id,uv.status,uv.verified_on,uv.cancelled_on,uv.created_on,uv.updated_on,uv.comments, dt.proof_type,(select a.desc FROM ".$this->config->get('tables.DOCUMENT_TYPES_LANG')." as a WHERE  a.lang_id = 1 AND a.document_type_id = uv.document_type_id) as doc_type,(select b.desc FROM ".$this->config->get('tables.PROOF_DOCTYPES_LANG')." as b WHERE  b.lang_id = 1  AND b.proof_type_id = pdt.id) as proof_type_name"))
                ->where('uv.account_id', $account_id)				
                ->where(function($fil) use($proof_types)
                {
                    $fil->where(function($fil1) use($proof_types)
                    {
                        $fil1->whereIn('dt.proof_type', $proof_types)
                        ->where('uv.status', $this->config->get('constants.ACTIVE'));
                    })
                    ->orWhere(function($fil1) use($proof_types)
                    {
                        $fil1->whereNotIn('dt.proof_type', $proof_types);
                    });
                })
                ->where('uv.is_deleted', $this->config->get('constants.NOT_DELETED'))
                ->orderby('uv.created_on', 'DESC');
        return $query->get();
    }
	
	public function get_document_types($arr = array())
    {
        extract($arr);
        $result = DB::table($this->config->get('tables.DOCUMENT_TYPES').' as dt')
                ->leftJoin($this->config->get('tables.DOCUMENT_TYPES_LANG').' as dtl', function($subquery)
                {
                    $subquery->on('dt.document_type_id', '=', 'dtl.document_type_id')
                    ->where('dtl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->where('dt.proof_type', $proof_type)
                ->where('dt.status', $this->config->get('constants.ACTIVE'))
                ->select(DB::raw('dtl.document_type_id,dtl.desc as doctype_name'))
                ->get();
        return ($result) ? $result : NULL;
    }		

	public function update_account_activationkey($account_id,$sdata)	
	{
		
		if($account_id >0 && !empty($sdata))
		{
			$res = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
					->where('account_id','=',$account_id)
					->update($sdata);
		if (!empty($res) && isset($res))
			{
			  return json_encode(array(
			   'msg'=>trans('affiliate/settings/security_pwd.forgot_msg'),
			   'alertclass'=>'alert-error',
			   'status'=> 'ok'));
			}
		}   
    }

	 public function amount_with_decimal ($amt)
    {
	
        $amt = floatval(trim($amt));
        $decimal_places = 2;
        $decimal_val = explode('.', $amt);
        if (isset($decimal_val[1]))
        {
            $decimal = rtrim($decimal_val[1], 0);
            if (strlen($decimal) > 2)
                $decimal_places = strlen($decimal);
            if ($decimal_places > 8)
                $decimal_places = 8;
        }
        return number_format($amt, $decimal_places, '.', ',');
    }
	

	public function get_user_verification_total ($account_id)
    {
        $result = DB::table($this->config->get('tables.ACCOUNT_VERIFICATION'))
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->where('account_id', $account_id)
                ->get();
			//	print_r($result); exit;
        return count($result);
    }
	
	/* my_profile */
	public function my_profile($arr = array())
	{
	    $profile_info=array();
	    extract($arr);
		if (!empty($account_id))
		{   
			$tree_info = $this->getUser_treeInfo(['account_id'=>$account_id]);

			$userdetails = $this->getUserinfo(['account_id'=>$account_id]);	
					 //print_r($userdetails);die;
			if(!empty($tree_info) && !empty($userdetails))
			{
	            $profile_info['tree_info'] = $tree_info;
                $profile_info['refferal_cnt'] = $tree_info->referral_cnts;
                $profile_info['userdetails'] = $userdetails ;
			    return $profile_info;
            }else{
			   return false;
			}
		}
		return NULL;
	}	
	
	/* profile_image upload */
	public function update_profile_image($account_id, $filename) 
	{
        $status = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                   ->where('account_id', $account_id)
                   ->update(array(
                      'profile_image' => $filename));
        return $status;
    }
	
	public function remove_profile_image ($account_id)
    {   
	    $default_image = $this->config->get('constants.DEFAULT_IMAGE');
        $profile_image = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                ->where(array(
                    'account_id'=>$account_id))
                ->pluck('profile_image');
				
        if (!empty($profile_image) && $profile_image[0] !=  $default_image)
        {
            $status = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                    ->where('account_id', $account_id)
                    ->update(array(
                'profile_image'=>$this->config->get('constants.DEFAULT_IMAGE')));
            File::delete($this->config->get('constants.PROFILE_IMAGE_PATH').$profile_image[0]);
            return true;
        }
        return false;
    }

	public function referral_user_check ($username)
    {
        if (isset($username))
        {				
		$result = DB::table($this->config->get('tables.ACCOUNT_MST').' as am')
			        ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                    ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as acs', 'acs.account_id', '=', 'ad.account_id')					
					->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'acs.country_id')
                    ->where('am.uname','=',$username) 
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
                $status['sponser_name'] = $result->uname;
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
    public function profilepin_check($account_id){
		
		
		 $qry= DB::table($this->config->get('tables.ACCOUNT_MST'))
		       ->select('trans_pass_key')
		       ->where('account_id', $account_id)
			   ->first();
            return $qry;
        }
    public function saveProfilePIN (array $arr = array())
    { 
        extract($arr);
        return   DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update(['trans_pass_key'=>md5($security_pin)]);
    }
	function update_verification_code($account_id,$key){
		
	}
	
}