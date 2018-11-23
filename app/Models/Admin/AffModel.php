<?php
namespace App\Models\Admin;

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
 	public function save_user($postdata)
    {
        $direct_intro_code = '';
        $rank = 1;
        $level = 0;
        $currencies = '';
        $user_code = '';
        $user_role = 1;
        $referral = '';
        $referral_id = '';
        $lineage = '/';
        $position_lineage = '1';
        $status = 0;
        $account_paid_status = 0;
        $direct_lineage = $directLineAge = '/';
        $user_id_details = '';
        $date = date('Y-m-d H:i:s');
        $password = md5($postdata['password']);
		$postdata['trans_pass_key'] = rand(1111,9999);
        $activation_key = rand(1111, 9999).time();
		$country_info = $this->lcObj->getCountry(['country_code'=>$postdata['country'],'allow_signup'=>true]);
        /* --------Assigning the Post Values -------- */
        $insert_account_mst['is_affiliate'] = config('constants.ON');
        $insert_account_mst['uname'] = $postdata['uname'];
        $insert_account_mst['email'] = $postdata['email'];
		$insert_account_mst['mobile'] = $postdata['mobile'];
        $insert_account_mst['pass_key'] = $password;
        $insert_account_mst['trans_pass_key'] =md5($postdata['trans_pass_key']);
	    $insert_account_mst['last_active'] = $date;
		$insert_account_mst['signedup_on'] = $date;
		$insert_account_mst['activated_on'] = $date;
		$insert_account_mst['account_type_id'] = config('constants.ACCOUNT_TYPE.USER');
		$insert_account_mst['status'] = config('constants.ACTIVE');		
		$insert_account_mst['is_deleted'] = config('constants.OFF');
		 $id = DB::table(config('tables.ACCOUNT_MST'))
                ->insertGetId($insert_account_mst);
				
		if($id > 0){
			$insert_account_tree['account_id'] = $id;
			$insert_account_tree['upline_id'] = 0;
			$insert_account_tree['sponsor_id'] = 0;
			$insert_account_tree['sponsor_lineage'] = '/';
		    $insert_account_tree['lineage'] =  $lineage;
		    $insert_account_tree['rank'] =  $rank;
		    $insert_account_tree['level'] = $level;
			$insert_account_tree['position_lineage'] = $position_lineage;
			$insert_account_tree['can_sponsor'] = 1;
			$insert_account_tree['lft_node'] = 1;
			$insert_account_tree['rgt_node'] = 2;
			$insert_account_tree['rank'] = $position_lineage;
			//$insert_account_tree['is_deleted'] = 0;
			DB::table(config('tables.ACCOUNT_TREE'))
                ->insertGetId($insert_account_tree);
				
			$insert_account_details['account_id'] = $id;
			$insert_account_details['firstname'] = $postdata['first_name'];
			$insert_account_details['lastname'] = $postdata['last_name'];
			$insert_account_details['gender'] = $postdata['gender'];
			$insert_account_details['dob'] = $postdata['dob'];
            $insert_account_details['updated_on'] = $date;
			$usRes= DB::table(config('tables.ACCOUNT_DETAILS'))
                    ->insertGetId($insert_account_details);
					
		    $insert_setting = '';
            $insert_setting['account_id'] = $id;
			$insert_setting['country_id'] = $country_info->country_id;
			$insert_setting['currency_id'] = $country_info->currency_id;
			$insert_setting['activation_key'] = $activation_key;			
			$usRes= DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
			                    ->insertGetId($insert_setting);		
								
			$insert_ranking = '';
            $insert_ranking['account_id'] = $id;
			$insert_ranking['bv']=0;
			$insert_ranking['cv']=0;
			$insert_ranking['qv']=0;
			$insert_ranking['gqv']=0;
            DB::table($this->config->get('tables.ACCOUNT_SALE_POINTS'))
			                    ->insertGetId($insert_ranking);
            if (!empty($usRes) && isset($usRes))
            {
                $response['status'] = "success";
                $response['msg'] = " <div class='alert alert-success'>Affiliate created successfully</div>";
               return json_encode($response);; //Session::put('success','User added Successfully');
            }
            else
            {
                $response['status'] = "error";
                $response['msg'] = " <div class='alert alert-danger'>We have issue on creating Affiliate Account. Please try later</div>";
                return json_encode($response); //Session::put('error','Something Went Wrong');
            }
        }
        return false;
    }
 public function check_user ($username = 0, $param = array()) {
        $qry = '';
        $qry = DB::table(Config('tables.ACCOUNT_MST').' as um')
                ->where('um.uname', '=', $username)
                ->where('um.is_deleted',0);
             $resFound =$qry->get();
          return $resFound;
    }
		
   	 public function check_email ($email = 0, $param = array()) {
        $qry = '';
        $result = false;
        $qry = DB::table(Config('tables.ACCOUNT_MST').' as um')
                ->where('um.email', '=', $email)
                ->where('um.is_deleted', Config('constants.OFF'));
        $resFound =$qry->get();
        return $resFound;
    }
	 public function check_mobile ($mobile = 0, $param = array()) {
        $qry = '';
        $result = false;
        $qry = DB::table(Config('tables.ACCOUNT_MST').' as um')
                ->where('um.mobile', '=', $mobile)
                ->where('um.is_deleted', Config('constants.OFF'));
        $resFound =$qry->get();
        return $resFound;
    }
		public function manage_userdetails($arr = array(), $count = false){
	
				extract($arr);
				$query = DB::table(config('tables.ACCOUNT_MST').' as um')
							->join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'um.account_id')
							->join(config('tables.ACCOUNT_PREFERENCE').' as acp', 'acp.account_id', ' = ', 'um.account_id')
							->leftJoin(config('tables.LOCATION_COUNTRY').' as lcu', 'lcu.country_id', ' = ', 'acp.country_id')
							->leftJoin(config('tables.ACCOUNT_TREE').' as at', 'at.account_id', ' = ', 'um.account_id')
							->leftJoin(config('tables.ACCOUNT_MST').' as spum', 'spum.account_id', ' = ', 'at.sponsor_id')
							->leftJoin(config('tables.ACCOUNT_MST').' as upum', 'upum.account_id', ' = ', 'at.upline_id')
							->leftJoin(config('tables.ACCOUNT_MST').' as roum', 'roum.account_id', ' = ', 'at.nwroot_id') 
						 ->join(config('tables.ACCOUNT_STATUS_LOOKUPS').' as usl', 'usl.status_id', ' = ', 'um.status')
							/*->join(config('tables.ACCOUNT_STATUS_LANG').' as uslg', function($subquery)
								{
								 $subquery->on('uslg.status_id', ' = ', 'usl.status_id')
								  ->where('uslg.lang_id', '=', config('app.locale_id'));
								}) */
							 ->where("um.is_affiliate",$user_role)
                            /* ->where("at.nwroot_id", config('constants.ROOT_TYPE.nwroot_id')) */
							->select(DB::Raw('um.signedup_on,um.account_id,um.uname,um.email,um.activated_on,um.block,um.status,concat_ws(" ",ud.firstname,ud.lastname) as fullname,um.mobile,lcu.country as country_name,spum.uname as referrer_name,spum.uname as referred_by,upum.uname as referrer_group,roum.uname as rootuser,usl.status_name'));
													
				if (isset($start_date) && isset($end_date) && !empty($start_date) && !empty($end_date))	{ 
					 $query->whereDate('um.signedup_on', '>=', getGTZ($start_date,'Y-m-d'));
					 $query->whereDate('um.signedup_on', '<=', getGTZ($end_date,'Y-m-d'));
				}
				else if (!empty($start_date) && isset($start_date)){ 
					 $query->whereDate('um.signedup_on', '<=', getGTZ($start_date,'Y-m-d'));
				}
				else if (!empty($end_date) && isset($end_date)){ 
					 $query->whereDate('um.signedup_on', '>=', getGTZ($end_date,'Y-m-d'));
				} 
				if (isset($search_text) && !empty($search_text))
				{ 
					if(!empty($filterchk) && !empty($filterchk))
					{   
						$search_text='%'.$search_text.'%'; 
						$search_field=['UserName'=>'um.uname','FullName'=>'concat_ws(" ",ud.firstname,ud.lastname)','Email'=>'um.email','Mobile'=>'concat_ws(" ",ud.phonecode,ud.mobile)','ReferredBy'=>'spum.uname','ReferredGroup'=>'upum.uname'];
						$query->where(function($sub) use($filterchk,$search_text,$search_field){
							foreach($filterchk as $search)
							{   
								if(array_key_exists($search,$search_field)){
								  $sub->orWhere(DB::raw($search_field[$search]),'like',$search_text);
								} 
							}
						});
					}
					else{
						$query->where(function($wcond) use($search_text){
						   $wcond->Where('um.uname','like',$search_text)
								 ->orwhere(DB::Raw('concat_ws(" ",ud.firstname,ud.lastname)'),'like',$search_text)
								 ->orwhere('um.email','like',$search_text)
								 ->orwhere('um.mobile','like',$search_text)
								 ->orwhere('spum.uname','like',$search_text)
								 ->orwhere('upum.uname','like',$search_text);
						}); 
					} 			
				} 
				if (isset($orderby) && isset($order))
				{
				   if($orderby == 'signedup_on'){
						$query->orderBy('um.signedup_on', $order);
				   }elseif($orderby == 'uname'){
						$query->orderBy('um.uname', $order);
				   }elseif($orderby == 'country_name'){
						$query->orderBy('lcu.name', $order);
				   }elseif($orderby == 'referred_by'){
						$query->orderBy('spum.uname', $order);
				   }elseif($orderby == 'referrer_group'){
						$query->orderBy('upum.uname', $order);
				   }elseif($orderby == 'rootuser'){
						$query->orderBy('roum.uname', $order);
				   }elseif($orderby == 'activated_on'){
						$query->orderBy('um.activated_on', $order);
				   }elseif($orderby == 'status'){
						$query->orderBy('um.status', $order);
				   }
				} 
				if (isset($country) && !empty($country))
				{  
					$query->where('uad.country_id',$country);
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
					$result= $query->orderBy('um.account_id', 'ASC') 
								   ->get();
			if(!empty($result)) {
					array_walk($result, function(&$data)
					{
				        $data->signedup_on = showUTZ($data->signedup_on, 'd-M-Y H:i:s');
						$data->user_name= trans('admin/affiliate/admin.username');
						$data->full_name= trans('admin/affiliate/admin.fullname');
						$data->email_label= trans('admin/affiliate/admin.email');
						$data->mobile_label= trans('admin/affiliate/admin.mobile');
						if($data->activated_on ==''){
						$data->activated_on='';
						}else{
						$data->activated_on= date('d-M-Y H:i:s', strtotime($data->activated_on));
						}
						$data->signedup_on= date('d-M-Y H:i:s', strtotime($data->signedup_on));
						$data->username= trans('admin/affiliate/admin.username');		
				    $data->actions = [];
					
					$data->actions[] = ['url'=>route('admin.account.view-details', ['uname'=>$data->uname]), 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.view_details')]; 
					
					$data->actions[] = ['url'=>route('admin.account.edit-details', ['uname'=>$data->uname]), 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.edit')];
				
					$data->actions[] = ['url'=>route('admin.account.change-password', ['account_id'=>$data->account_id]), 'class'=>'change_password', 'data'=>[
                        'account_id'=>$data->account_id,
						'fullname'=>$data->fullname
                    ], 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.change_pwd')];
					
					$data->actions[] = ['url'=>route('admin.account.reset-pin', ['account_id'=>$data->account_id]), 'class'=>'change_pin', 'data'=>[
                        'account_id'=>$data->account_id,
						'fullname'=>$data->fullname
                    ], 'redirect'=>false, 'label'=>trans('admin/affiliate/admin.reset_pin')];
					
				if ($data->block == config('constants.OFF'))
                     {
					 
                    $data->actions[] = ['url'=>route('admin.account.block_status', ['account_id'=>$data->account_id, 'status'=>'block']),'class'=> 'block_status', 'data'=>[
                        'account_id'=>$data->account_id,
						'status'=>'block'
                    ],'label'=>trans('admin/affiliate/admin.block')];
					
                    /* if ($data->status_name == config('constants.ACCOUNT.Active'))
                    {
                        $data->actions[] = ['url'=>route('admin.account.active_status', ['uname'=>$data->uname, 'status'=>'inactive']), 'label'=>trans('admin/affiliate/admin.Active')];
                    }
                    elseif ($data->status_name =config('constants.ACCOUNT.Inactive'))
                    {
                        $data->actions[] = ['url'=>route('admin.account.active_status', ['uname'=>$data->uname, 'status'=>'active']), 'label'=>trans('admin/affiliate/admin.Active')];
                    } */
                }
                else
                {
                    $data->actions[] = ['url'=>route('admin.account.block_status', ['account_id'=>$data->account_id, 'status'=>'unblock']), 'class'=>   'block_status', 'data'=>[
                        'account_id'=>$data->account_id,
						'status'=>'block'
                    ],'label'=>trans('admin/affiliate/admin.un_block')];
                 } 		
			   $data->actions[] = ['class'=> 'edit_email',
				  'data'=>['uname'=>$data->uname,'email'=>$data->email],'redirect'=>false, 'label'=>trans('admin/affiliate/admin.change_email')];	
				  
	         	$data->actions[] = ['class'=> 'edit_mobile',
				  'data'=>['uname'=>$data->uname,'mobile'=>$data->mobile,'uname1'=>$data->uname],'redirect'=>false, 'label'=>trans('admin/affiliate/admin.change_mobile')]; 
				});

							return $result;
					} 
				}
		return false;

		}
	  public function view_details ($uname) {
        $result = DB::table(config('tables.ACCOUNT_MST').' as am')
                ->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', ' = ', 'am.account_id')
                ->join(config('tables.ACCOUNT_PREFERENCE').' as ast', 'ast.account_id', ' = ', 'am.account_id')
               ->selectRaw('am.signedup_on,am.uname,am.email,am.activated_on,am.status,am.block,ast.is_verified,ad.firstname,ad.lastname,concat_ws(" ",ad.firstname,ad.lastname) as fullname,ad.dob,am.mobile')
                ->where('am.is_deleted',config('constants.OFF'))
               // ->where('am.system_role_id',config('constants.SYSTEM_ROLE.ADMIN'))
                ->where('am.uname', $uname)
                ->first();

        if (!empty($result))
        {
            $result->signedup_on = showUTZ($result->signedup_on, 'd-M-Y H:i:s');
            $result->activated_on = showUTZ($result->activated_on, 'd-M-Y H:i:s');
            $result->dob = showUTZ($result->dob, 'd-M-Y H:i:s');
           
            if ($result->block == config('constants.OFF'))
            {
                 $result->status_name = trans('admin/account/user.user_account_status.'.$result->status);
				 
                $result->status_disp_class =config('dispclass.user_account_status.'.$result->status); 
            }
            else
            {
                $result->status_name = trans('admin/account/user.user_account_status.'.$result->status);
                $result->status_disp_class = config('dispclass.user_account_status.'.$result->status); 
            }
        }
        return $result;
    }


 public function user_block_status (array $data = array())
    {
        $op = array();
        extract($data);
		
        if (isset($status) && $status == 1)
        {
            $query= DB::table(config('tables.ACCOUNT_MST'))
                            ->where('is_deleted',config('constants.NOT_DELETED'))
                           // ->where('system_role_id',config('constants.SYSTEM_ROLE.ADMIN')) 
                            ->where('account_id', $account_id)
                            ->update(['block'=>config('constants.BLOCK')]);
							if(!empty($query)){
					     	return json_encode(array(
							'status'=>200,
						     'msg'=>trans('admin/affiliate/settings/block.user_block'),
						     'alertclass'=>'alert-success'));
							}
        }
        else
        {
            $query_unblock= DB::table(config('tables.ACCOUNT_MST'))
                            ->where('is_deleted',config('constants.NOT_DELETED'))
                          //  ->where('system_role_id',config('constants.SYSTEM_ROLE.USER'))
                            ->where('account_id', $account_id)
                            ->update(['block'=>config('constants.UNBLOCK')]);
						if(!empty($query_unblock)){
					     	 return json_encode(array(
							 'status'=>200,
						     'msg'=>trans('admin/affiliate/settings/block.user_unblock'),
						     'alertclass'=>'alert-success'));
							
							}
        }
    }
	public function Update_email($postdata){

	if(!empty($postdata['uname'])){

	$data['email'] =$postdata['email'];
	
	if ($data['email'] != DB::table(config('tables.ACCOUNT_MST'))
							->where('uname',$postdata['uname'])
							->value('email'))
			   {
	    $status = DB::table(config('tables.ACCOUNT_MST'))
					->where('uname',$postdata['uname'])
					->update($data);
				
			if (!empty($status))
				{
					return json_encode(array(
					   'status'=>200,
						'msg'=>trans('admin/affiliate/settings/change_email.update_email_success'),
						'alertclass'=>'alert-success'));
				}
				else
				{
					return json_encode(array(
						'msg'=>trans('admin/general.something_wrong'),
						'alertclass'=>'alert-danger'));
				}
				
		}
			else{
				return json_encode(array(
						'msg'=>trans('admin/affiliate/settings/change_email.same_old'),
						'alertclass'=>'alert-danger'));
			}
	  }
	  return json_encode(array('msg'=>trans('admin/affiliate/settings/change_email.missing_parameters'), 'alertclass'=>'alert-warning'));
	}
  public function Update_mobile($postdata){

     if(!empty($postdata['uname'])){
	 $data['mobile'] =$postdata['mobile'];
	    if ($data['mobile'] != DB::table(config('tables.ACCOUNT_MST'))
							->where('uname',$postdata['uname'])
							->value('mobile'))
							{
	   $status = DB::table(config('tables.ACCOUNT_MST'))
			->where('uname',$postdata['uname'])
		    ->update($data);
			if (!empty($status)){
					return json_encode(array(
					'status'=>200,
					'msg'=>trans('admin/affiliate/admin.update_mobile_success'),
					'alertclass'=>'alert-success'));
				}
				else{
				    return json_encode(array(
					'msg'=>trans('admin/general.something_wrong'),
					'alertclass'=>'alert-danger'));
				}
	        }
			else{
				return json_encode(array(
						'msg'=>trans('admin/affiliate/settings/change_email.mobile_same_old'),
						'alertclass'=>'alert-danger'));
			}
	     }
		 return json_encode(array('msg'=>trans('admin/affiliate/settings/change_email.missing_parameters'), 'alertclass'=>'alert-warning'));
  }
	public function update_password ($postdata)
    {
		if($postdata['account_id']>0 && !empty(trim($postdata['new_pwd'])))
		{
			$data['pass_key'] = md5($postdata['new_pwd']);
		   
			if ($data['pass_key'] != DB::table(config('tables.ACCOUNT_MST'))
							->where('account_id',$postdata['account_id'])
							->value('pass_key'))
			   { 
				$status = DB::table(config('tables.ACCOUNT_MST'))
					->where('account_id',$postdata['account_id'])
					->update($data);
				if (!empty($status) && isset($status))
				{
					return json_encode(array(
					    'status'=>200,
						'uname'=>$postdata['full_name'],
						'msg'=>trans('admin/affiliate/settings/changepwd.password_changed'),
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
						'msg'=>trans('admin/affiliate/settings/changepwd.same_as_old'),
						'alertclass'=>'alert-danger'));
			}
		}
        return json_encode(array('msg'=>trans('admin/affiliate/settings/changepwd.missing_parameters'), 'alertclass'=>'alert-warning'));
    }	
	public function user_edit ($uname) {
	     return  $result = DB::table(config('tables.ACCOUNT_MST').' as am')
		->join(config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', ' = ', 'am.account_id')
		->join(config('tables.ACCOUNT_PREFERENCE').' as ast', 'ast.account_id', ' = ', 'am.account_id')
		->selectRaw('am.signedup_on,am.uname,am.email,am.activated_on,am.status,am.block,ast.is_verified,ad.firstname,ad.lastname,concat_ws(" ",ad.firstname,ad.lastname) as fullname,ad.dob,am.mobile')
		->where('am.is_deleted',config('constants.NOT_DELETED'))
		//->where('am.system_role_id',config('constants.SYSTEM_ROLE.ADMIN'))
		->where('am.uname', $uname)
		->first();		
						
     }
 public function user_update (array $data = array())
    {
    extract($data);
        if (isset($uname) && !empty($uname))
        {
            $result = DB::table(config('tables.ACCOUNT_MST'))
                    ->where('is_deleted', config('constants.NOT_DELETED'))
/*                     ->where('system_role_id',config('constants.SYSTEM_ROLE.USER')) */
                    ->where('uname', $uname)
                    ->select('account_id')
                    ->first();
            if (isset($result->account_id) && $result->account_id > 0)
            {
                $result2 = DB::table(config('tables.ACCOUNT_DETAILS'))
                        ->where('account_id', $result->account_id)
                        ->update(array(
                    'firstname'=>$first_name,
                    'lastname'=>$last_name,
                    'dob'=>getGTZ($dob, 'Y-m-d'),
                    'updated_on'=>getGTZ()));
				return json_encode(array(
					    'status'=>200,
						'msg'=>trans('admin/affiliate/settings/user_edit.user_details_update'),
						'alertclass'=>'alert-success'));
            }
            return NULL;
        }
        return NULL; 
    }	
	public function update_pin ($postdata)
    {

		if($postdata['account_id']>0 && !empty(trim($postdata['new_pin'])))
		{
			$data['trans_pass_key'] = md5($postdata['new_pin']);
		   
			if ($data['trans_pass_key'] != DB::table(config('tables.ACCOUNT_MST'))
							->where('account_id',$postdata['account_id'])
							->value('trans_pass_key'))
			   { 
				$status = DB::table(config('tables.ACCOUNT_MST'))
					->where('account_id',$postdata['account_id'])
					->update($data);
				if (!empty($status) && isset($status))
				{
					return json_encode(array(
					    'status'=>200,
						'uname'=>$postdata['full_name'],
						'msg'=>trans('admin/affiliate/settings/changepwd.password_changed'),
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
						'msg'=>trans('admin/affiliate/settings/changepwd.same_as_old'),
						'alertclass'=>'alert-danger'));
			}
		}
        return json_encode(array('msg'=>trans('admin/affiliate/settings/changepwd.missing_parameters'), 'alertclass'=>'alert-warning'));
    }
	
}