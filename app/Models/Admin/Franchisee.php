<?php
namespace App\Models\Admin;

use App\Models\BaseModel;
use App\Models\Admin\AdminCommon;
use App\Models\LocationModel;
use DB;
use URL;
use Session;
use CommonLib;
use View;
use Input;
use Config;
use Response;
use Request;

class Franchisee extends BaseModel
{
    public function __construct ()
    {
      parent::__construct();
	  $this->adminCommon = new AdminCommon();
	  $this->locationObj = new LocationModel();
    }
    
  /*   public function get_franchisee_details ($account_id = '')
    {
        $qry = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
				->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.FRANCHISEE_SETTINGS').' as fs', 'fs.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.id', '=', 'ap.currency')
                ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id')
                ->where('um.account_id', $account_id)
                ->select(DB::raw('um.uname,ud.*,um	.email, fs.office_available, lc.phonecode, fs.is_deposited, fs.deposited_amount, cur.currency as currency_code'));
        $result = $qry->first();
        return (!empty($result)) ? $result : NULL;
    } */

    public function save_franchisee ($postdata)
    {
        $user_data = array();
        $current_date = getGTZ();
        $user_data['uname'] = $postdata['uname'];
        $user_data['email'] = $postdata['email'];
        $user_data['pass_key'] = md5($postdata['password']);
        $user_data['trans_pass_key'] = md5($postdata['tpin']);
        $user_data['status'] = $this->config->get('constants.ON');
        $user_data['account_type_id'] = $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE');
		$user_data['mobile'] = $postdata['mobile'];
        $user_data['signedup_on'] = $current_date;
        $user_data['activation_key'] = rand(1111, 9999).time();
        $add = DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->insertGetId($user_data);
        if (!empty($add))
        {
            $ACCOUNT_DETAILS['firstname'] = $postdata['first_name'];
            $ACCOUNT_DETAILS['lastname'] = $postdata['last_name'];
			$ACCOUNT_DETAILS['gender'] = $postdata['sex'];
			$ACCOUNT_DETAILS['dob'] = date('Y-m-d', strtotime($postdata['dob']));
            $ACCOUNT_DETAILS['account_id'] = $add;
            $ACCOUNT_DETAILS['updated_on'] = $current_date;
            $adduser_detail = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                    ->insertGetId($ACCOUNT_DETAILS);
					
		   
		   	 /*Personal Address */			
		   $company_address['post_type'] = $this->config->get('constants.ADDRESS_POST_TYPE.ACCOUNT');
	       $address_mst['relative_post_id'] = $add;
	       $address_mst['state_id'] = $postdata['state'];
	       $address_mst['district_id'] = $postdata['district'];
           $address_mst['city_id'] = $postdata['city'];
		   $address_mst['country_id'] = $postdata['country'];
           $address_mst['postal_code'] = $postdata['zipcode'];
           $address_mst['address'] = $postdata['address'];		
		   $add_address_detail = DB::table($this->config->get('tables.ADDRESS_MST'))
                    ->insertGetId($address_mst);
		 
        }
        if (!empty($adduser_detail))
        {
            $insert_setting['account_id'] = $add;
            $insert_setting['change_email'] = $this->config->get('constants.ON');
            $insert_setting['change_payment'] = $this->config->get('constants.OFF');
            $insert_setting['transaction_pswd_user_edit'] = $this->config->get('constants.ON');
            $insert_setting['create_tickets'] = $this->config->get('constants.ON');
            $insert_setting['deposite'] = $this->config->get('constants.OFF');
            $insert_setting['refer_friend'] = $this->config->get('constants.OFF');
            $insert_setting['promotion_tool'] = $this->config->get('constants.OFF');
            $insert_setting['is_verified'] = $this->config->get('constants.OFF');
            $insert_setting['is_email_verified'] = $this->config->get('constants.ON');
            $insert_setting['is_mobile_verified'] = $this->config->get('constants.ON');
			$insert_setting['country_id'] = $postdata['country'];
			$insert_setting['currency_id'] = $postdata['currency'];
            $status = DB::table($this->config->get('tables.ACCOUNT_PREFERENCE'))
                    ->insertGetId($insert_setting);
	
            $settings['account_id'] = $add;
            $settings['franchisee_type'] = $postdata['fran_type'];
            $settings['is_deposited'] = $is_deposited = $postdata['isdeposited'];
            $package_amount = 0;
            $package_currency = 2;
            if ($is_deposited == $this->config->get('constants.ON'))
            {
                $package_details = $this->get_franchisee_package($postdata['fran_type']);
                $package_amount = $package_details->fr_pack_amount;
                $package_currency = $package_details->currency_id;
            }
            $settings['office_available'] = $postdata['office_available'];
            $settings['deposited_amount'] = $package_amount;
            $settings['currency'] = $package_currency;
            $settings['company_name'] = $postdata['company_name'];
            $settings['created_on'] = date('y-m-d H:i:s');
            $add_settings = DB::table($this->config->get('tables.FRANCHISEE_MST'))
                    ->insertGetId($settings);
					
		/*Company Address */
		   $company_address['post_type'] = $this->config->get('constants.ADDRESS_POST_TYPE.FRANCHISEE');
		   $company_address['relative_post_id'] = $add_settings;
		   $company_address['country_id'] = $postdata['country'];
		   $company_address['flatno_street'] = $postdata['cmpy_flat_no'];
           $company_address['landmark'] = $postdata['cmpy_land_mark'];
		   $company_address['postal_code'] = $postdata['cmpy_pincode'];
		   $add_address_detail = DB::table($this->config->get('tables.ADDRESS_MST'))
                    ->insertGetId($company_address);
        }
        return (!empty($add_settings)) ? $add : false;
    }

    /* public function franchisee_access_location ($postdata)
    {
        $access = array();
        $admindata = Session::get('admindata');
        $franchisee_type = $postdata['franchi_type'];
        $access['country_id'] = $access['region_id'] = $access['state_id'] = $access['district_id'] = 0;
        $access['account_id'] = $postdata['account_id'];
        $password = $postdata['pwd'];
        $tpin = $postdata['tpin'];
        $access['access_location_type'] = $franchisee_type;
        $access['created_by'] = $admindata[0]['admin_id'];
        $access['updated_by'] = $admindata[0]['admin_id'];
        $access['created_on'] = date('Y-m-d H:i:s');
        $access['updated_on'] = date('Y-m-d H:i:s');
        $relation_id = '0';
        if ($franchisee_type == $this->config->get('constants.MASTER'))
        {
            $relation_id = $postdata['country'];
        }
        else if ($franchisee_type == $this->config->get('constants.REGION'))
        {
            $relation_id = $postdata['region'];
            $access['country_id'] = $postdata['country'];
        }
        else if ($franchisee_type == $this->config->get('constants.STATE'))
        {
            $relation_id = $postdata['state'];
            if (isset($postdata['union_territory']))
            {
                if (is_array($postdata['union_territory']))
                {
                    $union_territory = implode(',', $postdata['union_territory']);
                }
                $relation_id = $relation_id.','.$union_territory;
            }
            $access['country_id'] = $postdata['country'];
            $access['region_id'] = $this->locationObj->get_region_id($postdata['state']);
        }
        else if ($franchisee_type == $this->config->get('constants.DISTRICT'))
        {            
            $relation_id = $postdata['district'];
            $access['country_id'] = $postdata['country'];
            $access['region_id'] = $this->locationObj->get_region_id($postdata['state']);
            $access['state_id'] = $postdata['state'];
        }
        else if ($franchisee_type == $this->config->get('constants.CITY'))
        {            
            $relation_id = $postdata['city'];
            $access['country_id'] = $postdata['country'];
            $access['region_id'] = $this->locationObj->get_region_id($postdata['state']);
            $access['state_id'] = $postdata['state'];
            $access['district_id'] = $postdata['district'];
        }
        $access['relation_id'] = $relation_id;
        $add_locations = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION'))
                ->insertGetId($access);
				
        $acinfo = $this->usercommonObj->get_userdetails_byid($postdata['account_id']);
        if (!empty($acinfo))
        {
            $data['email'] = $acinfo->email;
            $data['username'] = $acinfo->uname;
            $data['pwd'] = $password;
            $data['tpin'] = $tpin;
            $data['fullname'] = $acinfo->first_name.' '.$acinfo->last_name;
            $data['country'] = $this->locationObj->getCountry()[0]->name;            
        }
        return (!empty($add_locations)) ? $add_locations : false;
    } */

   

    public function franchisee_check_username ($uname = 0)
     {
        if ($uname)
        {
            $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where('uname', $uname)
                    ->where('account_type_id', $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))
                    ->get();
            if (empty($result) && count($result) == 0)
            {
                if (strlen($uname) < 6)
                {
                    $status['status'] = 'error';
                    $status['msg'] = '<span class="help-block">Username must be greater than 6 characters</span>';
                }
                elseif (!(preg_match('/^[a-zA-Z][a-zA-Z0-9]+$/', $uname)))
                {
                    $status['status'] = 'error';
                    $status['msg'] = '<span class="help-block">Username must starts with alphabets followed by numbers</span>';
                }
                else
                {
                    $status['status'] = 'ok';
                    $status['msg'] = '<span class="text-success">Username Available</span>';
                }
            }
            else
            {
                $status['status'] = 'error';
                $status['msg'] = '<span class="help-block">Username Already Exists</span>';
            }
            return $status;
        }
    }

    public function get_franchisee_list ($data = array(),$count = false)
    {
        $franchisee_type_arr = (array(
            1=>'LOCATION_COUNTRY',
            2=>'LOCATION_REGIONS',
            3=>'LOCATION_STATE',
            4=>'LOCATION_DISTRICTS',
            5=>'LOCATION_CITY'));
        $franchisee_field_arr = (array(
            1=>'name',
            2=>'region_name',
            3=>'name',
            4=>'district_name',
            5=>'city_name'));
        $franchisee_wfield_arr = (array(
            1=>'country_id',
            2=>'region_id',
            3=>'state_id',
            4=>'district_id',
            5=>'city_id'));
        $users = $from = $to = $user_name = $ustatus = '';
          $users = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as up', 'up.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'up.currency_id')
                ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'up.country_id')
                ->leftjoin($this->config->get('tables.FRANCHISEE_MST').' as fs', 'fs.account_id', '=', 'um.account_id')
                ->leftjoin($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', '=', 'um.account_id')
                ->leftjoin($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', '=', 'fs.franchisee_type')
                ->where('um.account_type_id', $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))
                ->select(DB::raw("um.*, ud.*,um.signedup_on, um.account_id, um.status as ustatus, c.*,fs.company_name,fs.franchisee_type,fl.franchisee_type as franchisee_type_name,lc.country as country_name,um.status as user_status,fs.is_deposited,fs.deposited_amount,fs.company_name, fal.relation_id,
				
				(CASE WHEN (fal.access_location_type = '1')
			    THEN (select country from  location_countries where country_id =  fal.relation_id) ELSE	'' END) as access_country_name,
				
				(CASE WHEN (fal.access_location_type = '2')
				THEN (select region from  location_regions where region_id =  fal.relation_id) ELSE	'' END) as access_region_name,
				
				(CASE WHEN (fal.access_location_type = '3')
				THEN (select state from  location_states where state_id =  fal.relation_id) ELSE	'' END) as access_state_name,
				
				(CASE WHEN (fal.access_location_type = '4')
				THEN (select district from  location_districts where district_id =  fal.relation_id) ELSE	'' END) as access_district_name,
				
				(CASE WHEN (fal.access_location_type = '5')
				THEN (select city from  location_city where city_id =  fal.relation_id) ELSE	'' END) as access_city_name,
				
				(CASE WHEN (fs.franchisee_type = '2')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join 	account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select country_id from location_regions where region_id = fal.relation_id) LIMIT 1) ELSE	'' END) as country_frname,
				
				(CASE WHEN (fs.franchisee_type = '3')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select country_id from location_states where state_id = fal.relation_id) LIMIT 1) ELSE	'' END) as country_frname1,
				
				(CASE WHEN (fs.franchisee_type = '3')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select region_id from location_states where state_id = fal.relation_id) LIMIT 1) ELSE	'' END) as region_frname,
				
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '3' and cfal.relation_id IN(select state_id from location_districts where district_id = fal.relation_id) LIMIT 1) ELSE	'' END) as state_frname,
				
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select region_id from location_states where state_id = (select state_id from location_districts where district_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as region_frname1,
				
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select country_id from location_states where state_id = (select state_id from location_districts where district_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as country_frname2,
				
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '4' and cfal.relation_id IN(select city_id from location_city where pincode_id = fal.relation_id) LIMIT 1) ELSE	'' END) as district_frname,
				
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '3' and cfal.relation_id IN(select city_id from location_city where pincode_id = fal.relation_id) LIMIT 1) ELSE	'' END) as state_frname1,

				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select district_id from location_pincodes where pincode_id = (select pincode_id from location_city where city_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as region_frname2,
				
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select district_id from location_pincodes where pincode_id = (select pincode_id from location_city where city_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as country_frname3
				
				"));
       if ($count)
        {
            return $users->count();
        }
        
/* 	$country_name = '';	
		if(!emty(country_frname3){
			$country_name = country_frname3;
		}
		if(!emty(country_frname1){
			$country_name = country_frname3;
		} */
        /*     if ($account_id > 0)
                  {
            $users = $users->where('um.account_id', $account_id);
        }
        if ($uname != '')
        {
            $users = $users->where('um.uname', $uname);
        }
        if (is_array($data) && count($data) > 0)
        {
            extract($data);
            if (!empty($to))
            {
                $users = $users->whereRaw("DATE(um.signedup_on) <='".date('Y-m-d', strtotime($to))."'");
            }
            if (!empty($from))
            {
                $users = $users->whereRaw("DATE(um.signedup_on) >='".date('Y-m-d', strtotime($from))."'");
            }
            if (isset($franchisee_type) && !empty($franchisee_type))
            {
                $users->where('fs.franchisee_type', $franchisee_type);
            }
            if (isset($search_term) && !empty($search_term))
            {
                $search_term = '%'.$search_term.'%';
                if (isset($search_feild) && !empty($search_feild) && isset($search_feilds[$search_feild]))
                {
                    $users->where(DB::raw($search_feilds[$search_feild]), 'like', $search_term);
                }
                else
                {
                    $users = $users->where(function($sub) use($search_term)
                    {
                        $sub->where('um.uname', 'like', $search_term);
                        $sub->orWhere(DB::raw('concat(ud.first_name," ",ud.last_name)'), 'like', $search_term);
                        $sub->orWhere('um.mobile', 'like', $search_term);
                        $sub->orWhere('um.email', 'like', $search_term);
                    });
                }
            }
            if (!empty($uname))
            {
                $users->where('um.uname', $uname);
            }
        } */
		
		if (!empty($uname))
            {
                $users->where('um.uname', $uname);
            }
        $users = $users->get();

        if (count($users) > 0)
        {
            if (isset($uname) && $uname != '' && count($users) == 1)
            {
                return $users[0];
            }
            else if (isset($account_id) && $account_id != 0 && count($users) == 1)
            {
                return $users[0];
            }
            else
            {
		 if (!empty($users))
            {
            array_walk($users, function($franchasee)
            {
			if(isset($franchasee->access_country_name) && !empty($franchasee->access_country_name))
               {       $franchasee->access_details=$franchasee->access_country_name; }
            if(isset($franchasee->access_region_name) && !empty($franchasee->access_region_name))
			   {	   $franchasee->access_details=$franchasee->access_region_name; }			
            if(isset($franchasee->access_state_name) && !empty($franchasee->access_state_name))
		 	   {      $franchasee->access_details=$franchasee->access_state_name;}
			 if(isset($franchasee->access_district_name) && !empty($franchasee->access_district_name))
                {      $franchasee->access_details= $franchasee->access_district_name; }	
			 if(isset($franchasee->access_city_name) && !empty($franchasee->access_city_name))
                {              $franchasee->access_details= $franchasee->access_city_name; }   
			
			
				
             if(isset($franchasee->district_frname) && !empty($franchasee->district_frname)){
                                $franchasee->district_frname= $franchasee->district_frname;}
								
								
			 if(isset($franchasee->state_frname) && !empty($franchasee->state_frname) ){
						  $franchasee->state_frame_result= $franchasee->state_frname;}
			 else if(isset($franchasee->state_frname1) && !empty($franchasee->state_frname1)){
							 $franchasee->state_frame_result=$franchasee->state_frname1;}   
			 else {$franchasee->state_frame_result='';} 
			/*region Frame */
			 if(isset($franchasee->region_frname) && !empty($franchasee->region_frname))
						   {  $franchasee->region_frname_result=$franchasee->region_frname; }
			 else if(isset($franchasee->region_frname1) && !empty($franchasee->region_frname1))
							{ $franchasee->region_frname_result= $franchasee->region_frname1;}
			 else if(isset($franchasee->region_frname2) && !empty($franchasee->region_frname2))
							{$franchasee->region_frname_result=$franchasee->region_frname2;} 
			 else {  $franchasee->region_frname_result=''; }
		
			/* Country Frname */			
			if(isset($franchasee->country_frname) && !empty($franchasee->country_frname) )
								{ $franchasee->country_frname_result=$franchasee->country_frname;}
			else if(isset($franchasee->country_frname1) && !empty($franchasee->country_frname1))
								{$franchasee->country_frname_result=$franchasee->country_frname1;}
			else if(isset($franchasee->country_frname3) && !empty($franchasee->country_frname2))
								{$franchasee->country_frname_result=$franchasee->country_frname2;}
		    else if(isset($franchasee->country_frname3) && !empty($franchasee->country_frname3))
									{$franchasee->country_frname_result=$franchasee->country_frname3;} 
		   else{ $franchasee->country_frname_result='';} 		
      
	      if ($franchasee->block == 0){ 
		          if ($franchasee->login_block == 1){
                                       $franchasee->block_status ='Login Blocked';
                                    }
                                 else{
                                        if ($franchasee->user_status == 1) {
                                              $franchasee->block_status= 'Active';}
                                        else if ($franchasee->user_status == 0)  {
                                            $franchasee->block_status='Inactive';
                                          }  }  }
                                else
                                {   $franchasee->block_status= 'Blocked'; }
							
				$franchasee->actions = [];
			
				$franchasee->actions[] = ['url'=>route('admin.franchisee.edit_profile', ['account_id'=>$franchasee->account_id]), 'data-url'=>'{{route("admin.franchisee.edit_profile")}}','data-rule-required'=>'true', 'class'=>'edit_info','data'=>[
						'uname'=>$franchasee->uname,
						'url'=>route('admin.franchisee.edit_profile')
						
                    ], 'redirect'=>false, 'label'=>trans('admin/franchisee.edit_profile')];

				//$franchasee->actions[] = ['url'=>route('admin.sample', ['uname'=>$franchasee->uname]), 'redirect'=>false, 'label'=>''];
				$franchasee->actions[] = ['url'=>route('admin.franchisee.change_password', ['account_id'=>$franchasee->account_id]), 'class'=>'change_password','data'=>[
                        'support_center'=>$franchasee->company_name,
						'account_id'=>$franchasee->account_id,
						'first_name'=>$franchasee->firstname.'('.$franchasee->uname.')'
                    ], 'redirect'=>false, 'label'=>trans('admin/franchisee.change_pwd')];
					
				 $franchasee->actions[]= ['url'=>route('admin.franchisee.change_pin', ['account_id'=>$franchasee->account_id]), 'class'=>'change_pin','data'=>[
					 'support_center'=>$franchasee->company_name,
					'account_id'=>$franchasee->account_id,
				    'first_name'=>$franchasee->firstname.'('.$franchasee->uname.')'
				], 'redirect'=>false, 'label'=>trans('admin/franchisee.change_pin')];
			 
		       if ($franchasee->block == $this->config->get('constants.OFF'))
                     {
                  $franchasee->actions[] = ['url'=>route('admin.franchisee.change_block_users', ['account_id'=>$franchasee->account_id, 'status'=>'block']),'class'=> 'block_status', 'data'=>[
                        'account_id'=>$franchasee->account_id,
						'status'=>'block'
                    ],'label'=>trans('admin/franchisee.block')];

                }
                 else
                {
                    $franchasee->actions[] = ['url'=>route('admin.franchisee.change_block_users', ['account_id'=>$franchasee->account_id, 'status'=>'unblock']), 'class'=>'block_status', 'data'=>[
                        'account_id'=>$franchasee->account_id,
						'status'=>'unblock'
                    ],'label'=>trans('admin/franchisee.un_block')];
                 }  
				 if ($franchasee->login_block == 1) {
                     $franchasee->actions[] = ['url'=>route('admin.franchisee.login_block', ['account_id'=>$franchasee->account_id, 'status'=>'unblock']),'class'=> 'login_block', 'data'=>[
                        'account_id'=>$franchasee->account_id,
						'status'=>'unblock'
                    ],'label'=>trans('admin/franchisee.login_unblock')];							   
       
                   }
             if ($franchasee->login_block == 0) {
                        $franchasee->actions[] = ['url'=>route('admin.franchisee.login_block', ['account_id'=>$franchasee->account_id, 'status'=>'block']),'class'=> 'login_block', 'data'=>[
                        'account_id'=>$franchasee->account_id,
						'status'=>'block'
                    ],'label'=>trans('admin/franchisee.login_block')];		   
                }
                                       
				 
                    });
             return $users;
               }

            }
        }
        return false;
    }

	
	public function get_franchisee_user_list($data = array(), $uname = '', $user_id = 0){
		
	
		  $franchisee_type_arr = (array(
            1=>'LOCATION_COUNTRY',
            2=>'LOCATION_REGIONS',
            3=>'LOCATION_STATE',
            4=>'LOCATION_DISTRICTS',
            5=>'LOCATION_CITY'));
        $franchisee_field_arr = (array(
            1=>'name',
            2=>'region_name',
            3=>'name',
            4=>'district_name',
            5=>'city_name'));
        $franchisee_wfield_arr = (array(
            1=>'country_id',
            2=>'region_id',
            3=>'state_id',
            4=>'district_id',
            5=>'city_id'));
        $users = $from = $to = $user_name = $ustatus = '';
          $users = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as up', 'up.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'up.currency_id')
                ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'up.country_id')
                ->leftjoin($this->config->get('tables.FRANCHISEE_MST').' as fs', 'fs.account_id', '=', 'um.account_id')
                ->leftjoin($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', '=', 'um.account_id')
                ->leftjoin($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', '=', 'fs.franchisee_type')
                ->where('um.account_type_id', $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))
                ->select(DB::raw("um.*, ud.*,um.signedup_on, um.account_id, um.status as ustatus, c.*,fs.company_name,fs.franchisee_type,fl.franchisee_type as franchisee_type_name,lc.country as country_name,um.status as user_status,fs.is_deposited,fs.deposited_amount,fs.company_name, fal.relation_id,
				
				(CASE WHEN (fal.access_location_type = '1')
			    THEN (select country from  location_countries where country_id =  fal.relation_id) ELSE	'' END) as access_country_name,
				
				(CASE WHEN (fal.access_location_type = '2')
				THEN (select region from  location_regions where region_id =  fal.relation_id) ELSE	'' END) as access_region_name,
				
				(CASE WHEN (fal.access_location_type = '3')
				THEN (select state from  location_states where state_id =  fal.relation_id) ELSE	'' END) as access_state_name,
				
				(CASE WHEN (fal.access_location_type = '4')
				THEN (select district from  location_districts where district_id =  fal.relation_id) ELSE	'' END) as access_district_name,
				
				(CASE WHEN (fal.access_location_type = '5')
				THEN (select city from  location_city where city_id =  fal.relation_id) ELSE	'' END) as access_city_name,
				
				(CASE WHEN (fs.franchisee_type = '2')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join 	account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select country_id from location_regions where region_id = fal.relation_id) LIMIT 1) ELSE	'' END) as country_frname,
				
				(CASE WHEN (fs.franchisee_type = '3')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select country_id from location_states where state_id = fal.relation_id) LIMIT 1) ELSE	'' END) as country_frname1,
				
				(CASE WHEN (fs.franchisee_type = '3')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select region_id from location_states where state_id = fal.relation_id) LIMIT 1) ELSE	'' END) as region_frname,
				
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '3' and cfal.relation_id IN(select state_id from location_districts where district_id = fal.relation_id) LIMIT 1) ELSE	'' END) as state_frname,
				
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select region_id from location_states where state_id = (select state_id from location_districts where district_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as region_frname1,
				
				(CASE WHEN (fs.franchisee_type = '4')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select country_id from location_states where state_id = (select state_id from location_districts where district_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as country_frname2,
				
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '4' and cfal.relation_id IN(select city_id from location_city where pincode_id = fal.relation_id) LIMIT 1) ELSE	'' END) as district_frname,
				
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '3' and cfal.relation_id IN(select city_id from location_city where pincode_id = fal.relation_id) LIMIT 1) ELSE	'' END) as state_frname1,

				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '2' and cfal.relation_id IN(select district_id from location_pincodes where pincode_id = (select pincode_id from location_city where city_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as region_frname2,
				
				(CASE WHEN (fs.franchisee_type = '5')
				THEN (select cum.uname from  franchisee_access_location cfal
				inner join account_mst cum  on cum.account_id = cfal.account_id  where cfal.access_location_type = '1' and cfal.relation_id IN(select district_id from location_pincodes where pincode_id = (select pincode_id from location_city where city_id = fal.relation_id)) LIMIT 1) ELSE	'' END) as country_frname3
				
				"));
	if ($user_id > 0)
        {
            $users = $users->where('um.user_id', $user_id);
        }
        if ($uname != '')
        {
		
            $users = $users->where('um.uname', $uname);
        }
       
	$users = $users->get();

        if (count($users) > 0)
        {
            if (isset($uname) && $uname != '' && count($users) == 1)
            {
                return $users[0];
            }
            else if (isset($user_id) && $user_id != 0 && count($users) == 1)
            {
                return $users[0];
            }
            else
            {
                return $users;
            }
        }
        return false;
	}
     public function add_access_location ($postdata,$admin_account_id)
    {
        $access = array();
		$postdata['admin_id'] = $admin_account_id;
        //$admindata = Session::get('admindata');
        $franchisee_type = $postdata['franchi_type'];
		
        $access['country_id'] = $access['region_id'] = $access['state_id'] = $access['district_id'] = 0;
        $access['account_id'] = $postdata['user_id'];
        $password = $postdata['pwd'];
        $tpin = $postdata['tpin'];
        $access['access_location_type'] = $franchisee_type;
         $access['created_by'] = $admin_account_id;
        $access['updated_by'] = $admin_account_id; 
        $access['created_on'] = date('Y-m-d H:i:s');
        $access['updated_on'] = date('Y-m-d H:i:s');
        $relation_id = '0';
        if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.COUNTRY'))
        {
            $relation_id = $postdata['country'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.REGION'))
        {   
            $relation_id = $postdata['region'];
            $access['country_id'] = $postdata['country'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.STATE'))
        {    
            $relation_id = $postdata['state'];
            if (isset($postdata['union_territory']))
            {
                if (is_array($postdata['union_territory']))
                {
                    $union_territory = implode(',', $postdata['union_territory']);
                }
                $relation_id = $relation_id.','.$union_territory;
            }
            $access['country_id'] = $postdata['country'];
             $region = $this->adminCommon->get_region_id($postdata['state']);
			 $access['region_id']=$region[0];
		
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.DISTRICT'))
        {
            if ($postdata['district'] == 0)
            {
                $postdata['district'] = $this->adminCommon->addnewdistrict($postdata['district_others'], $postdata['state']);
            }
             $relation_id = $postdata['district'];
             $access['country_id'] = $postdata['country'];
           // $access['region_id'] = $this->adminCommon->get_region_id($postdata['state']);
		     $region = $this->adminCommon->get_region_id($postdata['state']);
			 $access['region_id']=$region[0];
             $access['state_id'] = $postdata['state'];
        }
        else if ($franchisee_type == $this->config->get('constants.FRANCHISEE_TYPE.CITY'))
        {
            if ($postdata['city'] == 0)
            {
                $postdata['city'] = $this->adminCommon->addnewcity($postdata['city_others'], $postdata['state'], $postdata['district']);
            }
            $relation_id = $postdata['city'];
            $access['country_id'] = $postdata['country'];
           // $access['region_id'] = $this->adminCommon->get_region_id($postdata['state']);
		    $region = $this->adminCommon->get_region_id($postdata['state']);
			 $access['region_id']=$region[0];
            $access['state_id'] = $postdata['state'];
            $access['district_id'] = $postdata['district'];
        }
        $access['relation_id'] = $relation_id;
        $add_locations = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION'))
                ->insertGetId($access);
								
      /*   $user_details = $this->usercommonObj->get_userdetails_byid($postdata['user_id']);
        if (!empty($user_details))
        {
            $data['email'] = $user_details->email;
            $data['username'] = $user_details->uname;
            $data['pwd'] = $password;
            $data['tpin'] = $tpin;
            $data['fullname'] = $user_details->first_name.' '.$user_details->last_name;
            $data['country'] = $this->adminCommon->get_country_list($postdata['country'])[0]->name;
            
        } */
        return (!empty($add_locations)) ? $add_locations : false;
    }

    public function get_frachisee_access ($account_id)
    {
        $res = DB::table($this->config->get('tables.FRANCHISEE_SETTINGS').' as fs')
                ->leftjoin($this->config->get('constants.FRANCHISEE_ACCESS_LOCATION').' as fal', 'fal.account_id', '=', 'fs.account_id')
                ->where('fs.account_id', $account_id)
                ->where('fal.status', $this->config->get('constants.ON'))
                ->select(DB::raw('fal.account_id,fal.relation_id as location_access,fal.access_location_type as access_type, fal.country_id, fal.region_id, fal.state_id, fal.district_id'))
                ->groupBy('fal.account_id')
                ->first();
        return (!empty($res)) ? $res : false;
    }

    public function get_city_access ($location_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_CITY').' as ct')
                ->join($this->config->get('tables.LOCATION_DISTRICTS').' as dt', 'dt.district_id', '=', 'ct.district_id')
                ->join($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'dt.state_id')
                ->leftjoin($this->config->get('constants.LOCATION_REGIONS').' as rg', 'rg.region_id', '=', 'ls.region_id')
                ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id')
                ->where('ct.city_id', $location_id)
                ->where('ct.status', 1)
                ->select('ct.city_id', 'dt.district_id', 'ls.state_id', 'lc.country_id', 'rg.region_id')
                ->first();
        return (!empty($res)) ? $res : false;
    }

    public function get_district_access ($location_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_DISTRICTS').' as dt')
                ->join($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'dt.state_id')
                ->leftjoin($this->config->get('constants.LOCATION_REGIONS').' as rg', 'rg.region_id', '=', 'ls.region_id')
                ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id')
                ->where('dt.district_id', $location_id)
                ->where('dt.status', 1)
                ->select('dt.district_id', 'ls.state_id', 'lc.country_id', 'rg.region_id')
                ->first();
        return (!empty($res)) ? $res : false;
    }

    public function get_state_access ($location_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_STATE').' as ls')
                        ->leftjoin($this->config->get('constants.LOCATION_REGIONS').' as rg', 'rg.region_id', '=', 'ls.region_id')
                        ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id')
                        ->where('ls.state_id', $location_id)
                        ->where('ls.status', 1)
                        ->select('ls.state_id', 'lc.country_id', 'rg.region_id')->first();
        return (!empty($res)) ? $res : false;
    }

    public function get_region_access ($location_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_REGIONS').' as rg')
                        ->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'rg.country_id')
                        ->where('rg.region_id', $location_id)
                        ->where('rg.status', 1)
                        ->select('rg.region_id', 'lc.country_id')->first();
        return (!empty($res)) ? $res : false;
    }

    public function get_country_access ($location_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_COUNTRY').' as lc')
                        ->where('lc.country_id', $location_id)
                        ->where('lc.status', 1)
                        ->select('lc.country_id')->first();
        return (!empty($res)) ? $res : false;
    }

    public function get_franchisee_package ($frans_type = '')
    {
        $res = DB::table($this->config->get('tables.FRANCHISEE_PACKAGE').' as fp')
				->join($this->config->get('tables.CURRENCIES').' as cr', 'cr.currency_id', '=', 'fp.currency')
                ->select('franchisee_type', 'fr_pack_amount', 'cr.currency_id','cr.currency as currency_code');
        if (!empty($frans_type))
        {
            $res = $res->where('franchisee_type', $frans_type);
        }
        $res = $res->get();
        if (isset($res) && count($res))
        {
            if (empty($frans_type))
            {
				$arrData = [];
                foreach ($res as $row){
                    $arrData[$row->franchisee_type]['frachisee_package_amount'] = $row->fr_pack_amount.' '.$row->currency_code;
                }
                return $arrData;
            }
            else
            {
                return $res[0];
            }
        }
    }

    public function check_franchise_access ($franchise_type, $relation_id)
    {

        if (is_array($relation_id))
        {
            $relation_id = implode(',', $relation_id);
        }
        $res = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fs')
                ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'fs.account_id')
                ->where('um.status', 1)
                ->where('um.is_deleted', 0)
                ->where('fs.status', 1)
                ->whereRaw("fs.relation_id LIKE '%".$relation_id."%'")
                ->where('fs.access_location_type', $franchise_type)
                ->pluck('uname');
				
        return !empty($res) ? $res : false;
    }

  
    public function check_franchise_region ($franchise_type, $state_id)
    {
        $res = DB::table($this->config->get('tables.LOCATION_STATE').' as ls')
                ->join($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fs', 'fs.relation_id', '=', 'ls.region_id')
                ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'fs.account_id')
                ->where('ls.state_id', $state_id)
                ->whereNotNull('ls.region_id')
                ->whereRaw("ls.region_id <> ''")
                ->where('um.status', 1)
                ->where('fs.status', 1)
                ->where('fs.access_location_type', $franchise_type)
                ->pluck('uname');
        return !empty($res) ? $res : false;
    }

    public function change_block_franchisee ($post_data)
    {
        $response['status'] = 'ERR';
        $response['msg'] = 'Failed to change Block';
        $response['label'] = '';
        $response['button_status'] = '';
        if (!empty($post_data))
        {
            $userid = $post_data['account_id'];
            $status = $post_data['status'];
            $block = $post_data['block'];
            $response['status'] = 'ok';
            if ($block == 1)
                $block = 0;
            else if ($block == 0)
                $block = 1;
            $data['block'] = $block;
            DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where('account_id', $userid)
                    ->update($data);
            if ($block == 0)
            {
                if ($status == 0)
                {
                    $response['label'] = '<span class="label label-warning">Activated</span>';
                    $response['button_status'] = 'Block';
                }
                else if ($status == 1)
                {
                    $response['label'] = '<span class="label label-success">Verified</span>';
                    $response['button_status'] = 'Block';
                }
            }
            else if ($block == 1)
            {
                $response['label'] = '<span class="label label-danger">Blocked</span>';
                $response['button_status'] = 'UnBlock';
            }
            $response['msg'] = 'Block has been changed Successfully';
        }
        return $response;
    }

    public function change_franchisee_loginblock ($post_data)
    {
        $response['status'] = 'ERR';
        $response['msg'] = 'Failed to change Block';
        $response['label'] = '';
        $response['button_status'] = '';
        if (!empty($post_data))
        {
            $userid = $post_data['account_id'];
            $status = $post_data['status'];
            $block = $post_data['block'];
            $response['status'] = 'ok';
            if ($block == 1)
                $block = 0;
            else if ($block == 0)
                $block = 1;
            $data['login_block'] = $block;
            DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where('account_id', $userid)
                    ->update($data);
            if ($block == 0)
            {
                if ($status == 0)
                {
                    $response['label'] = '<span class="label label-warning">Login Activated</span>';
                    $response['button_status'] = 'Block Login';
                }
                else if ($status == 1)
                {
                    $response['label'] = '<span class="label label-success">Login Activated</span>';
                    $response['button_status'] = 'Block Login';
                }
            }
            else if ($block == 1)
            {
                $response['label'] = '<span class="label label-danger">Login Blocked</span>';
                $response['button_status'] = 'UnBlock Login';
            }
            $response['msg'] = 'Login Block has been changed Successfully';
        }
        return $response;
    }

    public function update_fixed_commission ($country_id, $month, $year)
    {
        $sql = "SELECT ust.account_id, ust.subscrib_topup_id, ust.amount, ust.currency_id, ust.topup_date, ust.payment_type, ust.currency_id, ust.topup_date, ud.country, ud.state, ud.district, (SELECT payout_types FROM payout_types WHERE type_id = ust.payment_type) AS payout_types_name, (SELECT uname FROM ACCOUNT_MST WHERE account_id = (SELECT to_account_id FROM referral_earnings WHERE subscrib_topup_id = ust.subscrib_topup_id AND is_systemfee =0 AND is_lappsed_income =0 AND is_deleted =0)) AS to_user_name FROM (SELECT *FROM user_subscription_topup WHERE DATE( topup_date ) >= '2016-11-01' AND (payment_type =9 OR payment_type =14)) AS ust INNER JOIN ACCOUNT_MST AS um ON um.account_id = ust.account_id AND um.account_type_id <=2 AND um.is_deleted =0 INNER JOIN ACCOUNT_DETAILS AS ud ON ud.account_id = ust.account_id AND ud.country = '77' ";
        $sql .= "WHERE (um.direct_lineage NOT REGEXP CONCAT( '.*/', 832, '/.*' )AND um.account_id != '832')";
        $result = DB::select(DB::raw($sql));
        if (isset($result) && count($result))
        {
            foreach ($result as $sub_ACCOUNT_DETAILS)
            {
                $franchisee_commission = $this->usercommonObj->check_franchisee_commission($sub_ACCOUNT_DETAILS->payment_type);
                if ($franchisee_commission)
                {
                    echo $sub_ACCOUNT_DETAILS->subscrib_topup_id.'<br />';
                    $this->franchisee_commission(array(
                        'account_id'=>$sub_ACCOUNT_DETAILS->account_id,
                        'userdetails'=>$sub_ACCOUNT_DETAILS,
                        'relation_id'=>$sub_ACCOUNT_DETAILS->subscrib_topup_id,
                        'commission_type'=>$this->config->Get('constants.FR_COMMISSION_TYPE.FIXED_CONTRIBUTION'),
                        'amount'=>$sub_ACCOUNT_DETAILS->amount,
                        'currency_id'=>$sub_ACCOUNT_DETAILS->currency_id,
                        'payment_gateway'=>$sub_ACCOUNT_DETAILS->payout_types_name,
                        'to_user_name'=>$sub_ACCOUNT_DETAILS->to_user_name,
                        'created_date'=>$sub_ACCOUNT_DETAILS->topup_date));
                }
            }
        }
    }

    public function update_addfunds_commission ($country_id, $month, $year)
    {
        $sql = "SELECT uaf.uaf_id, uaf.account_id, uaf.amount, uaf.currency_id, uaf.payment_type, uaf.released_date, ud.country, ud.state, ud.district, (SELECT payout_types FROM payout_types WHERE type_id = uaf.payment_type) AS payout_types_name FROM (SELECT *FROM user_add_fund WHERE DATE(released_date ) >= '2016-11-01' AND (payment_type =9 OR payment_type =14) AND payment_status = 1 AND status <=1 AND purpose = 1) AS uaf INNER JOIN ACCOUNT_MST AS um ON um.account_id = uaf.account_id AND um.account_type_id <=2 AND um.is_deleted =0 INNER JOIN ACCOUNT_DETAILS AS ud ON ud.account_id = um.account_id AND ud.country ='77' ";
        $sql .= "WHERE (um.direct_lineage NOT REGEXP CONCAT(  '.*/', 832,  '/.*' ) AND um.account_id !=  '832')";
        $result = DB::select(DB::raw($sql));
        if (isset($result) && count($result))
        {
            foreach ($result as $sub_ACCOUNT_DETAILS)
            {
                $franchisee_commission = $this->usercommonObj->check_franchisee_commission($sub_ACCOUNT_DETAILS->payment_type);
                if ($franchisee_commission)
                {
                    echo $sub_ACCOUNT_DETAILS->uaf_id.'<br />';
                    $this->franchisee_commission(array(
                        'account_id'=>$sub_ACCOUNT_DETAILS->account_id,
                        'userdetails'=>$sub_ACCOUNT_DETAILS,
                        'relation_id'=>$sub_ACCOUNT_DETAILS->uaf_id,
                        'commission_type'=>$this->config->Get('constants.FR_COMMISSION_TYPE.ADD_FUNDS'),
                        'amount'=>$sub_ACCOUNT_DETAILS->amount,
                        'currency_id'=>$sub_ACCOUNT_DETAILS->currency_id,
                        'payment_gateway'=>$sub_ACCOUNT_DETAILS->payout_types_name,
                        'created_date'=>$sub_ACCOUNT_DETAILS->released_date));
                }
            }
        }
    }

    public function update_flexible_commission ($country_id, $month, $year)
    {
        $sql = "SELECT cf.pf_id, cf.donor_id, um.account_id, cf.amount, cf.from_currency_id as currency_id, cf.payment_type_id, cf.confirmed_on, dm.country, dm.state, dm.district, (SELECT payout_types FROM payout_types WHERE type_id = cf.payment_type_id) AS payout_types_name, um.uname as to_user_name FROM (SELECT *FROM campaign_funds WHERE DATE(confirmed_on ) >= '2016-11-01' AND (payment_type_id =9 OR payment_type_id =14) AND payment_status = 1 AND status = 1) AS cf INNER join campaigns as camp on camp.project_id = cf.project_id INNER JOIN ACCOUNT_MST AS um ON um.account_id = camp.account_id AND um.account_type_id <=2 AND um.is_deleted =0 INNER JOIN donor_mst as dm ON dm.donor_id = cf.donor_id AND dm.country ='77' ";
        $sql .= "WHERE (um.direct_lineage NOT REGEXP CONCAT(  '.*/', 832,  '/.*' ) AND um.account_id !=  '832')";
        $result = DB::select(DB::raw($sql));
        if (isset($result) && count($result))
        {
            foreach ($result as $sub_ACCOUNT_DETAILS)
            {
                $franchisee_commission = $this->usercommonObj->check_franchisee_commission($sub_ACCOUNT_DETAILS->payment_type_id);
                if ($franchisee_commission)
                {
                    echo $sub_ACCOUNT_DETAILS->pf_id.'<br />';
                    $this->franchisee_commission(array(
                        'account_id'=>$sub_ACCOUNT_DETAILS->account_id,
                        'userdetails'=>$sub_ACCOUNT_DETAILS,
                        'relation_id'=>$sub_ACCOUNT_DETAILS->pf_id,
                        'commission_type'=>$this->config->Get('constants.FR_COMMISSION_TYPE.FLEXIBLE_CONTRIBUTION'),
                        'amount'=>$sub_ACCOUNT_DETAILS->amount,
                        'currency_id'=>$sub_ACCOUNT_DETAILS->currency_id,
                        'payment_gateway'=>$sub_ACCOUNT_DETAILS->payout_types_name,
                        'to_user_name'=>$sub_ACCOUNT_DETAILS->to_user_name,
                        'created_date'=>$sub_ACCOUNT_DETAILS->confirmed_on));
                }
            }
        }
    }

    public function franchisee_commission ($arrData = array())
    {
        if (count($arrData))
        {
            extract($arrData);
            if ($commission_type == $this->config->Get('constants.FR_COMMISSION_TYPE.FLEXIBLE_CONTRIBUTION'))
            {
                $campaignObj = new Campaign();
                $userinfo = $contributor_details = $campaignObj->get_donordetails_byid($userdetails->donor_id);
            }
            else
            {
                $userinfo = $this->usercommonObj->get_userdetails_byid($userdetails->account_id);
            }
            $com_details_data = [];
            if (!empty($userinfo->country))
                $data['country_id'] = $userinfo->country;
            if (!empty($userinfo->state_id))
                $data['state_id'] = $userinfo->state_id;
            if (!empty($userinfo->district_id))
                $data['district_id'] = $userinfo->district_id;
            if (!empty($userinfo->region_id))
                $data['region_id'] = $userinfo->region_id;
            $com_details_data = $data;
            //print_r($data);
            $middle_level_franchisees = '';
            $current_date = date('Y-m-d H:i:s');
            $middle_level_franchisees = $this->usercommonObj->get_franchisee($data);
            if ($middle_level_franchisees && !empty($middle_level_franchisees))
            {
                foreach ($middle_level_franchisees as $franchisee)
                {
                    $remark = '';
					
					
	
                    $com_data['account_id'] = $franchisee->account_id;
                    $com_data['commission_type'] = $commission_type;
                    $com_data['relation_id'] = $relation_id;
                    $com_data['amount'] = $amount;
                    $com_data['currency_id'] = $currency_id;
                    //check commission already exists
                    $res = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION'))
                            ->where('account_id', $franchisee->account_id)
                            ->where('relation_id', $relation_id)
                            ->where('commission_type', $commission_type)
                            ->get();
                    if (!($res && count($res)))
                    {
                        if ($commission_type == $this->config->get('constants.FR_COMMISSION_TYPE.FLEXIBLE_CONTRIBUTION'))
                            $com_data['commission_perc'] = $per = $franchisee->flexible_commission_per;
                        else
                            $com_data['commission_perc'] = $per = $franchisee->diff_commission_per;
                        $com_data['commission_amount'] = ($amount * $per) / 100;
                        if (isset($created_date))
                        {
                            $com_data['created_date'] = $created_date;
                        }
                        else
                        {
                            $com_data['created_date'] = $current_date;
                        }
                        if ($commission_type == $this->config->get('constants.FR_COMMISSION_TYPE.FLEXIBLE_CONTRIBUTION'))
                        {
                            $com_data['remark'] = "Contribution Flexible Amount to ".$to_user_name." through (".$payment_gateway.")";
                        }
                        elseif ($commission_type == $this->config->get('constants.FR_COMMISSION_TYPE.FIXED_CONTRIBUTION'))
                        {
                            $com_data['remark'] = "Contribution Fixed Amount to ".$to_user_name." through (".$payment_gateway.")";
                        }
                        elseif ($commission_type == $this->config->get('constants.FR_COMMISSION_TYPE.ADD_FUNDS'))
                        {
                            $com_data['remark'] = "Add Funds through (".$payment_gateway.")";
                        }
                        $franchiseeObj = new Franchisee();
                        //$franchisee_balance_count = $franchiseeObj->get_balance_info($franchisee->account_id);
                        $com_data['status'] = $this->config->get('constants.COMISSION_STATUS_PENDING');
                        if (!empty($franchisee->low_transaction_details))
                        {
                            $low_transaction_details = json_decode(stripslashes($franchisee->low_transaction_details), true);
                            $count = 0;
                            $year = date('Y');
                            $month = date('m');
                            if (isset($low_transaction_details['count'][$year][$month]))
                            {
                                $count = $low_transaction_details['count'][$year][$month];
                            }
                            if ($count >= 1)
                            {
                                $com_data['status'] = $this->config->get('constants.COMISSION_STATUS_WAITING');
                            }
                        }
                        $relation_id = DB::table($this->config->get('tables.FRANCHISEE_COMMISSION'))
                                ->insertGetID($com_data);
                        if ($relation_id)
                        {
                            $com_details_data['fr_com_id'] = $relation_id;
                            $this->addFranchiseeCommissionDetails($com_details_data);
                        }
                    }                   
                }
            }
        }
    }
	
	
    public function getFranchiseeTypes ()
    {
        return DB::table($this->config->get('tables.FRANCHISEE_LOOKUP'))
                        ->get();
    }
	
	
	public function addFranchiseeCommissionDetails ($arr = array())
    {
        $country_id = $state_id = $district_id = $region_id = $city_id = null;
        extract($arr);
        $data = compact('country_id', 'state_id', 'district_id', 'region_id', 'city_id');
        if (empty(array_filter($data)) && isset($account_id))
        {
            $userdetails = $this->get_franchisee_details($account_id);
            if (!empty($userdetails))
            {
                if (!empty($userdetails->country))
                    $data['country_id'] = $userdetails->country;
                if (!empty($userdetails->state_id))
                    $data['state_id'] = $userdetails->state_id;
                if (!empty($userdetails->district_id))
                    $data['district_id'] = $userdetails->district_id;
                if (!empty($userdetails->region_id))
                    $data['region_id'] = $userdetails->region_id;
                if (!empty($userdetails->city_id))
                    $data['city_id'] = $userdetails->city_id;
            }
        }
        if (!empty(array_filter($data)))
        {
            if (DB::table($this->config->get('tables.FRANCHISEE_COMMISSION_DETAILS'))
                            ->where('fr_com_id', $fr_com_id)
                            ->count() > 0)
            {
                return DB::table($this->config->get('tables.FRANCHISEE_COMMISSION_DETAILS'))
                                ->where('fr_com_id', $fr_com_id)
                                ->update($data);
            }
            else
            {
                $data['fr_com_id'] = $fr_com_id;
                return DB::table($this->config->get('tables.FRANCHISEE_COMMISSION_DETAILS'))
                                ->insertGetID($data);
            }
        }
    }

    public function get_franchisee ($arrData = array(), $get_ACCOUNT_DETAILS_only = false)
    {
        $middle_level_franchisees = [];
        if (!empty($arrData) && count($arrData))
        {
            foreach ($arrData as $k=> $v)
            {
                if (!empty($v))
                {
                    $query = DB::table($this->config->get('tables.FRANCHISEE_ACCESS_LOCATION').' as fal')
                            ->join($this->config->get('tables.ACCOUNT_MST').' as um', 'um.account_id', '=', 'fal.account_id')
                            ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', ' = ', 'fal.account_id')
							->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'um.account_id')
							->join($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id')
                            ->where('fal.status', $this->config->get('constants.ACTIVE'))
                            ->where('um.status', $this->config->get('constants.ACTIVE'))
                            ->where('um.is_deleted', $this->config->get('constants.OFF'))
                            ->where('um.block', $this->config->get('constants.OFF'))
                            ->whereRaw('FIND_IN_SET('.$v.',fal.relation_id)');
                    if ($get_ACCOUNT_DETAILS_only)
                    {
                        $query->selectRaw('um.email,um.account_id,um.uname,concat(ud.first_name," ",ud.last_name) as full_name,um.uname,concat(lc.phonecode," ",um.mobile) as mobile');
                    }
                    else
                    {
                        $query->join($this->config->get('tables.FRANCHISEE_SETTINGS').' as fs', 'fs.account_id', '=', 'fal.account_id')
                                ->join($this->config->get('tables.FRANCHISEE_LOOKUP').' as fl', 'fl.franchisee_typeid', ' = ', 'fal.access_location_type')
                                ->join($this->config->get('tables.FRANCHISEE_BENIFITS').' as fb', 'fb.franchisee_type', ' = ', 'fal.access_location_type')
                                ->where('fs.office_available', $this->config->Get('constants.ON'))
                                ->selectRaw('fal.account_id,fb.diff_commission_per,fb.flexible_commission_per,fs.low_transaction_details,ap.country_id,fl.level,um.email,um.account_id,um.uname,concat(ud.first_name," ",ud.last_name) as full_name,um.uname,concat(lc.phonecode," ",um.mobile) as mobile');
                    }
                    if ($k == 'city_id')
                    {
                        $query->where('fal.access_location_type', $this->config->get('constants.CITY_FRANCHISEE'));
                        $middle_level_franchisees['city'] = $query->first();
                    }
                    if ($k == 'district_id')
                    {
                        $query->where('fal.access_location_type', $this->config->get('constants.DISTRICT_FRANCHISEE'));
                        $middle_level_franchisees['district'] = $query->first();
                    }
                    if ($k == 'state_id')
                    {
                        $query->where('fal.access_location_type', $this->config->get('constants.STATE_FRANCHISEE'));
                        $middle_level_franchisees['state'] = $query->first();
                    }
                    if ($k == 'region_id')
                    {
                        $query->where('fal.access_location_type', $this->config->get('constants.REGION_FRANCHISEE'));
                        $middle_level_franchisees['region'] = $query->first();
                    }
                    if ($k == 'country_id')
                    {
                        $query->where('fal.access_location_type', $this->config->get('constants.COUNTRY_FRANCHISEE'));
                        $middle_level_franchisees['country'] = $query->first();
                    }
                }
            }
        }
        return array_filter($middle_level_franchisees);
    }

    public function getFranchiseeCommissionStatusByID ($status_id)
    {
        return DB::table($this->config->get('tables.FRANCHISEE_COMMISSION_STATUS_LOOKUP'))
                        ->where('com_status_id', $status_id)
                        ->pluck('status_name');
    }
	
	public function check_franchisee_commission ($payment_type_id)
    {
        $result = DB::table($this->config->get('tables.PAYMENT_TYPES'))
                ->where('type_id', $payment_type_id)
                ->pluck('franchisee_commission_status');
        return ($result) ? $result : false;
    }
	 public function user_block_status (array $data = array())
     {
        $op = array();
        extract($data);
        if (isset($status) && $status == 1)
        {
            $query= DB::table($this->config->get('tables.ACCOUNT_MST'))
                            ->where('is_deleted',$this->config->get('constants.NOT_DELETED'))
                             ->where('account_type_id',$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE')) 
                            ->where('account_id', $account_id)
                            ->update(['block'=>$this->config->get('constants.ON')]);
							if(!empty($query)){
					     	return json_encode(array(
							'status'=>200,
						     'msg'=>trans('admin/block.user_block'),
						     'alertclass'=>'alert-success'));
							}
        }
        else
        {
            $query_unblock= DB::table($this->config->get('tables.ACCOUNT_MST'))
                            ->where('is_deleted',$this->config->get('constants.NOT_DELETED'))
                            ->where('account_type_id',$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))
                            ->where('account_id', $account_id)
                            ->update(['block'=>$this->config->get('constants.OFF')]);
						if(!empty($query_unblock)){
					     	 return json_encode(array(
							 'status'=>200,
						     'msg'=>trans('admin/block.user_unblock'),
						     'alertclass'=>'alert-success'));
							}
        }
    }
	
	
	public function update_password ($postdata)
    {

		if($postdata['account_id']>0 && !empty(trim($postdata['new_pwd'])))
		{
			$data['pass_key'] = md5($postdata['new_pwd']);
		   
			if ($data['pass_key'] != DB::table($this->config->get('tables.ACCOUNT_MST'))
							->where('account_id',$postdata['account_id'])
							->value('pass_key'))
			   { 
				$status = DB::table($this->config->get('tables.ACCOUNT_MST'))
					->where('account_id',$postdata['account_id'])
					->update($data);
				if (!empty($status) && isset($status))
				{
					return json_encode(array(
					    'status'=>200,
						'uname'=>$postdata['full_name'],
						'msg'=>trans('admin/changepwd.password_changed'),
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
						'msg'=>trans('admin/changepwd.same_as_old'),
						'alertclass'=>'alert-danger'));
			}
		}
        return json_encode(array('msg'=>trans('admin/changepwd.missing_parameters'), 'alertclass'=>'alert-warning'));
    }
	
	
	
	public function update_pin ($postdata)
    {
	 if (!empty($postdata))
        {
			
		  $accountid = $postdata['account_id'];
              $data1 = array(
                'account_id'=>$accountid);
            $trans_key =DB::table($this->config->get('tables.ACCOUNT_MST'))
                    ->where($data1)
					->select('trans_pass_key')
                    ->first();
					
					
	    if (!empty($trans_key->trans_pass_key))
            {	
			 
                if ($postdata['new_tpin'] != $postdata['confirm_tpin'])
                {
					return json_encode(array(
						'msg'=>'New PIN and Confirm PIN does not match',
						'alertclass'=>'alert-danger'));
                }
                elseif ($trans_key->trans_pass_key == $postdata['confirm_tpin'])
                {
					
					return json_encode(array(
						'msg'=>'New PIN Must be Different from Old PIN',
						'alertclass'=>'alert-danger'));
                }
                elseif (DB::table($this->config->get('tables.ACCOUNT_MST'))
                                ->where('account_id',$postdata['account_id'])
                                ->update(array(
                                    'trans_pass_key'=>$postdata['confirm_tpin'])))
                {

					return json_encode(array(
					    'status'=>200,
						'msg'=>'PIN has been changed Successfully',
						'alertclass'=>'alert-success'));
                }
			}				
	          
		}
  
   }
	
	 public function user_block_login (array $data = array())
     {
        $op = array();
        extract($data);
        if (isset($status) && $status == 1)
        {
            $query= DB::table($this->config->get('tables.ACCOUNT_MST'))
                            ->where('is_deleted',$this->config->get('constants.NOT_DELETED'))
                             ->where('account_type_id',$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE')) 
                            ->where('account_id', $account_id)
                            ->update(['login_block'=>$this->config->get('constants.ON')]);
							if(!empty($query)){
					     	return json_encode(array(
							'status'=>200,
						     'msg'=>trans('admin/block.user_block'),
						     'alertclass'=>'alert-success'));
							}
        }
        else
        {
            $query_unblock= DB::table($this->config->get('tables.ACCOUNT_MST'))
                            ->where('is_deleted',$this->config->get('constants.NOT_DELETED'))
                            ->where('account_type_id',$this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))
                            ->where('account_id', $account_id)
                            ->update(['login_block'=>$this->config->get('constants.OFF')]);
						if(!empty($query_unblock)){
					     	 return json_encode(array(
							 'status'=>200,
						     'msg'=>trans('admin/block.user_unblock'),
						     'alertclass'=>'alert-success'));
							}
        }
    }
	
public function get_franchisee_details ($user_id = '')
    {
        $qry = DB::table($this->config->get('tables.ACCOUNT_MST').' as um')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'um.account_id')
                ->join($this->config->get('tables.ADDRESS_MST').' as am', 'am.relative_post_id', '=', 'um.account_id')
                ->join($this->config->get('tables.FRANCHISEE_MST').' as fs', 'fs.account_id', '=', 'um.account_id')
				->join($this->config->get('tables.ADDRESS_MST').' as cam', 'cam.relative_post_id', '=', 'fs.fr_id')
                ->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'ap.currency_id')
                ->leftJoin($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'am.country_id', 'left')
                ->leftJoin($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'am.state_id', 'left')
                ->leftJoin($this->config->get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'am.district_id', 'left')
                ->where('um.account_id', $user_id)
                ->select(DB::raw('um.uname,ud.*,um.email, fs.office_available,ls.state_id,ld.district_id,lc.country_id,lc.phonecode,am.postal_code,cam.flatno_street as company_address,am.address,um.mobile,lc.phonecode, fs.is_deposited, fs.deposited_amount, fs.company_name,cur.currency as currency_code'));
        $orderdetails = $qry->first();
	
        return (!empty($orderdetails)) ? $orderdetails : NULL;
    }
	 public function update_franchisee_profile ($user_id, $postdata)
    {
		
        $userdetails = array();
        $account_info = array();
        $email = $postdata['email'];
        $office_available = $postdata['office_available'];
        $userdetails = array_filter(array_map('trim', $postdata['user']));
		
		$franchisee_mst['company_name']= $userdetails['company_name']; 
		$franchisee_mst['office_available']=$office_available; 
		
		
		//$address_mst $userdetails['company_address']; die;
		
		/* Account Details */
        if (isset($userdetails['dob']))
        {
            $address_details['dob'] = date('Y-m-d', strtotime($userdetails['dob']));
        }
        $username = $userdetails['first_name'];
        $address_details['firstname'] = $userdetails['first_name'];
	
	   /*Account Master */
	     $account_mst['mobile']=$userdetails['mobile'];
	     $account_mst['email']=$email;
		
		  $company_address['flatno_street']=$userdetails['company_address'];
		  
		  $personal_address['address']=$userdetails['address'];
		  $personal_address['country_id']=$userdetails['country'];
		  $personal_address['state_id']=$userdetails['state'];
		  $personal_address['district_id']=$userdetails['district'];
		  $personal_address['postal_code']=$userdetails['zipcode'];
		  $personal_address['city_id']=$userdetails['city'];
		
		DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
		         ->where('account_id', $user_id)
                 ->update($address_details);
				
      DB::table($this->config->get('tables.ACCOUNT_MST'))
                ->where('account_id', $user_id)
                ->update($account_mst);
				
       DB::table($this->config->get('tables.FRANCHISEE_MST'))
                ->where('account_id', $user_id)
                ->update($franchisee_mst); 

	   DB::table($this->config->get('tables.ADDRESS_MST'))
                ->where('relative_post_id', $user_id)
                ->update($personal_address); 
				
		$franchisee_id=DB::table($this->config->get('tables.FRANCHISEE_MST'))
                ->where('account_id', $user_id)
                ->select('fr_id')
				->first();
		if(!empty($franchisee_id)){
			DB::table($this->config->get('tables.ADDRESS_MST'))
                ->where('relative_post_id', $franchisee_id->fr_id)
                ->update($company_address);
			
		}
				
        $response['status'] = 'success';
        $response['msg'] = 'Profile updated Successfully';
        return $response; 
    }
	public function franchisee_mobile_check ($mobile = '', $old_mobile = '')
    {
        if ($mobile)
        {
        $result=  DB::table($this->config->get('tables.ACCOUNT_MST').' as ud')
                    ->join($this->config->get('tables.ACCOUNT_DETAILS').' as um', 'um.account_id', '=', 'ud.account_id')
                    ->where('ud.account_type_id', $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))
                    ->where('ud.mobile', $mobile)
                    ->get();
            if (empty($result) && count($result) == 0 || (!empty($old_mobile) && $old_mobile == $mobile))
            {
                $status['status'] = 'ok';
                $status['msg'] = 'Mobile Number Available';
            }
            else
            {
                $status['status'] = 'error';
                $status['msg'] = 'Mobile Number  Already Exists';
            }
            return $status;
        }
        else
        {
            $status['status'] = 'error';
            $status['msg'] = 'Please Enter a Mobile number.';
            return $status;
        }
    }
	 public function franchisee_email_check ($email = 0, $user_details = 0, $old_email = 0)
    {
        if ($email)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $status['status'] = 'error';
                $status['msg'] = 'Please Enter a valid Email Address.';
                return $status;
            }
            if ($email == 'testerej88@gmail.com' || (!empty($old_email) && $old_email == $email))
            {
                $status['status'] = 'ok';
                $status['msg'] = 'Email ID Available';
                return $status;
            }
            else if ($email != 'testerej88@gmail.com')
            {
                $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('email', $email)
                        ->where('ud.account_type_id', $this->config->get('constants.ACCOUNT_TYPE.FRANCHISEE'))
                        ->get();
                if (empty($result) && count($result) == 0)
                {
                    $status['status'] = 'ok';
                    $status['msg'] = 'Email ID Available';
                }
                else
                {
                    $status['status'] = 'error';
                    $status['msg'] = 'Email ID Already Exists';
                }
                return $status;
            }
        }
        else
        {
            $status['status'] = 'error';
            $status['msg'] = 'Please Enter a valid Email Address';
            return $status;
        }
    }

}