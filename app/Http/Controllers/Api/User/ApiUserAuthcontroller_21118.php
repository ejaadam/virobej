<?php
namespace App\Http\Controllers\Api\User;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\User\UserApiBaseController;
use App\Models\Api\User\AffModel;
use App\Models\Api\User\AccountModel;
use App\Models\Api\User\StoreModel;
use Session;
use App\Helpers\CommonNotifSettings;
use Config;
use File;

class ApiUserAuthcontroller extends UserApiBaseController
{
    public function __construct ()
    {
        parent::__construct();
		$this->apiaffObj = new AffModel();		
		$this->accountObj = new AccountModel();
    }	 
	
	public function postLoginCheck ()
    {		
        $op = array();
        $op['status'] = '';
        $op['msg'] = trans('user/auth.login_msg.comm_error');
        $res = '';
        $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        $postdata = $this->request->all();						
        if (!empty($postdata))
        {
            if ($this->request->username && $this->request->password)
            {
                $postdata['account_type'] = [$this->config->get('constants.ACCOUNT_TYPE.USER'),$this->config->get('constants.ACCOUNT_TYPE.SELLER')];				
                //$data['country_id'] = $this->country_id;				
                $res = $this->accountObj->checkAccount_login($this->request, $postdata); 
				//print_r($res);exit;	
                $validate = $res['status'];				
                if ($validate == 1)
                {				
					$has_pin = !empty($res['accinfo']->security_pin) ? 1 : 0;
					//$op['msg'] = $res['msg'];
					$op['msg'] = trans('user/auth.login_msg.acc_success');
					$op['token'] =  $res['token'];
					$op['account_id'] = $res['accinfo']->account_id;
					$op['full_name'] =  $res['accinfo']->full_name;
					$op['first_name']=$res['accinfo']->firstname;
					$op['last_name']=$res['accinfo']->lastname;					
					$op['uname'] =  $res['accinfo']->uname;		
					if (!empty($res['accinfo']->store_code)) {
						$op['store_code'] = $res['accinfo']->store_code; 
					}
					$op['user_code'] = $res['accinfo']->user_code;          
					$op['account_type'] =  $res['accinfo']->account_type;
					$op['account_type_name'] = $res['accinfo']->account_type_name;       					
					$op['mobile'] =  $res['accinfo']->mobile;
					$op['email'] =  $res['accinfo']->email;
					$op['currency_id'] = $res['accinfo']->currency_id;   
                    $op['currency_code'] =  $res['accinfo']->currency_code;							
                    $op['country_flag'] =  $res['accinfo']->country_flag;							
					$op['is_mobile_verified'] = $res['accinfo']->is_mobile_verified;          
					$op['is_email_verified'] = $res['accinfo']->is_email_verified;          
					$op['is_affiliate'] = $res['accinfo']->is_affiliate;     
					if(!empty($res['accinfo']->is_affiliate)) {
						$op['can_sponser'] = 1;
					}
					$op['account_log_id'] = $res['accinfo']->account_log_id; 					
					$op['profile_img'] = asset($this->config->get('path.ACCOUNT.PROFILE_IMG.WEB.100x100').$res['accinfo']->profile_image);     					
					$op['is_verified'] = $res['accinfo']->is_verified;     					                       
					$op['country'] = $res['accinfo']->country;     					                       
					$op['country_code'] = $res['accinfo']->country_code;     					                       
					$op['country_id'] = $res['accinfo']->country_id;     					                       
					$op['phone_code'] = $res['accinfo']->phonecode;     					                       
					$op['has_pin'] = $has_pin;     					                       
					$op['is_guest'] = $res['accinfo']->is_guest;     					                       
					$op['toggle_app_lock'] = $res['accinfo']->toggle_app_lock;
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');					 
                }
                elseif ($validate == 2)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    //$op['msg'] = trans('user/auth.login_msg.acc_not_found');                    
                    $op['msg'] = trans('user/auth.login_msg.account_not_exists');                    
                }
                elseif ($validate == 3)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('user/auth.login_msg.invalid_uname_pwd');
                }
                elseif ($validate == 4)
                {
                    $op['status']  = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('user/auth.login_msg.acc_invid');
                }
                elseif ($validate == 5)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('user/auth.login_msg.acc_blocked');
                }
				elseif ($validate == 6)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                     
                    $op['msg'] = trans('user/auth.login_msg.acc_blocked');
                }
                elseif ($validate == 7)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                     
                    $op['msg'] = trans('user/auth.login_msg.acc_blocked');
                }
                elseif ($validate == 8)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('user/auth.login_msg.comm_error');
                }
                elseif ($validate == 9)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('user/auth.login_msg.invalid_uname_pwd');                    
                }
                elseif ($validate == 10)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('user/auth.login_msg.invalid_username');
                }
				elseif ($validate == 14)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                     
                    $op['msg'] = trans('user/auth.login_msg.status_not_active');
                }
                else
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('user/auth.login_msg.acc_invemail');
                }
            }
            else
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');                
                if ($this->request->username && $this->request->password)
                {
                    $op['msg'] = trans('user/auth.login_msg.comm_error');
                }
                else if (!$this->request->username)
                {
                    $op['msg'] = trans('user/auth.login_msg.comm_error');
                }
                else if (!$this->request->password)
                {
                    $op['msg'] = trans('user/auth.login_msg.comm_error');
                }
            }
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
            $op['msg'] = trans('user/auth.login_msg.comm_error');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	
	
	public function send_login_otp() 
	{		
		$data = $this->request->all();			
		if ($data) {
			$data['otp'] = $otp = rand(111111, 999999);
			$data['account_id'] = $account_id = $this->apiaffObj->get_account_id($data);		
			$this->session->set('USER.LOGIN_OTP', $data);					
			CommonNotifSettings::notify('USER.LOGIN_OTP', $account_id, Config::get('constants.ACCOUNT_TYPE.USER'), $data, false, true, false, true, false);	
			$op['OTP'] = $otp;
			$op['msg'] = 'OTP Sent Successfully';
			$op['status'] = $status_code =  $this->config->get('httperr.SUCCESS');				
		}		
		return $this->response->json($op, $status_code, $this->headers, $this->options);
	}
	
	public function login_with_otp() 
	{		
		$data = $this->request->all();			
		if ($data) {
				$session_data = $this->session->get('USER.LOGIN_OTP');	
				if ($session_data['otp'] == $data['otp']) {
					$data['uname'] = $session_data['mobile'];
					$data['account_id'] = $session_data['account_id']; 
					$this->session->forget('USER.LOGIN_OTP');
					$res = $this->accountObj->checkAccount_login($data);							
					$validate = $res['status'];
					if ($validate == 1)
					{						
						$op['msg'] = $res['msg'];
						$op['account_id'] = $res['accinfo']->account_id;
						$op['full_name'] =  $res['accinfo']->full_name;
						$op['uname'] =  $res['accinfo']->uname;
						$op['user_code'] = $res['accinfo']->user_code;          
						$op['account_type'] =  $res['accinfo']->account_type_id;
						$op['account_type_name'] = $res['accinfo']->account_type_name;       					
						$op['mobile'] =  $res['accinfo']->mobile;
						$op['email'] =  $res['accinfo']->email;
						$op['currency_id'] = $res['accinfo']->currency_id;   
						$op['currency_code'] =  $res['accinfo']->currency_code;					
						$op['is_mobile_verified'] = $res['accinfo']->is_mobile_verified;          
						$op['is_email_verified'] = $res['accinfo']->is_email_verified;          
						$op['is_affiliate'] = $res['accinfo']->is_affiliate;          
						$op['account_log_id'] = $res['accinfo']->account_log_id;            
						$op['profile_img'] = $res['accinfo']->profile_img;     
						$op['security_pin'] = $res['accinfo']->security_pin;     
						$op['token'] =  $res['accinfo']->token;
							   
						$op['is_verified'] = $res['accinfo']->is_verified;     					   
						//$op['msg'] = trans('user/auth.login_msg.acc_success');
						$op['status'] = $status_code = $this->config->get('httperr.SUCCESS');					 
					}
					elseif ($validate == 2)
					{
						$op['status'] = $status_code = $this->config->get('httperr.UN_PROCESSABLE');                    
						$op['msg'] = trans('affiliate/auth.login_msg.acc_not_found');                    
					}
					elseif ($validate == 3)
					{
						$op['status'] = $status_code = $this->config->get('httperr.UN_PROCESSABLE');                    
						$op['msg'] = trans('affiliate/auth.login_msg.invalid_uname_pwd');
					}						
				} else {
					$op['msg'] = 'Invalid OTP';   
					$op['status'] = $status_code =  $this->config->get('httperr.UN_PROCESSABLE');	
				}			
		}		
		return $this->response->json($op, $status_code, $this->headers, $this->options);
	}
	
	

   /*  public function logout ()
    {
        $op = [];
        if ($this->config->get('data.user'))
        {
            $this->memberObj = new MemberAuth($this->commonObj);
            if ($this->memberObj->logoutUser($this->account_id, $this->device_log_id))
            {
                $op['status'] = 200;
				$op['url'] = route('aff.login');
                $op['msg'] = trans('general.logout_success');
            }
            else
            {
                $op['msg'] = trans('general.something_went_wrong');
            }
        }
        return $this->response->json($op, 200);
    } */
	
	public function changepwd ()
    {
        $op = [];
        $postdata = $this->request->all();				
        $postdata['account_id'] = $this->userSess->account_id;		
		//print_r($this->sessionName);exit;
        if ($this->userSess->pass_key == md5($this->request->current_password))
        {
            if ($this->userSess->pass_key != md5($this->request->conf_password))
            {
                if ($res = $this->apiaffObj->update_password($this->userSess->account_id, $this->request->conf_password))
                {   					
                    $this->userSess->pass_key = md5($this->request->conf_password);
                    $this->config->set('app.accountInfo', $this->userSess);
                    $this->session->set($this->sessionName, (array) $this->userSess);
                    //CommonLib::notify($this->userSess->account_id, 'change_password', ['email'=>$this->userSess->email, 'home_url'=>route('user.home')]);
                    $op['msg'] = trans('user/account.changepwd.success');
                    $op['status'] = $status = $this->config->get('httperr.SUCCESS');                    
                }
                else
                {
                    $op['status'] = $status = $this->config->get('httperr.UN_PROCESSABLE');
                    $op['msg'] = trans('user/account.changepwd.savepwd_unable');
                }
            }
            else
            {
                $op['msg'] = trans('user/account.changepwd.newpwd_same');
                $op['status'] = $status = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('user/account.changepwd.curr_pwd_incorrect');
            $op['status'] = $status = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $status, $this->headers, $this->options);
    }
	
	  public function updateProfile (){
        $op = [];
        $data = $this->request->all();
        $data['account_id'] = $this->userSess->account_id;
       
        if (!empty($data))
        {
            $result = $this->apiaffObj->updateProfile($data);			
            if (!empty($result))
            {
                $this->userSess->firstname = $data['first_name'];
				if(!empty($data['last_name'])){
					$this->userSess->lastname = $data['last_name'];
				}
                $this->userSess->uname= $data['display_name'];
                $this->config->set('app.accountInfo', $this->userSess);		
			    //$this->session->set('userdata', (array) $this->userSess);  
              	$this->session->set($this->sessionName, (array) $this->userSess);
                $op['msg'] = trans('user/account.edit_profile.profile_updated');
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['msg'] = trans('user/account.edit_profile.no_changes');
				$op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
            }
        }
        else
        {
            $op['msg'] = trans('user/account.edit_profile.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode);
    }
	
	public function get_account_info ()
    {        
        $op['account_id'] = $this->userSess->account_id;        
        $op['uname'] = $this->userSess->uname;        
        $op['first_name'] = isset($this->userSess->firstname) ? $this->userSess->firstname : '';
        $op['last_name'] = isset($this->userSess->lastname) ? $this->userSess->lastname : '';
        $op['phonecode'] = $this->userSess->phonecode;
        //$op['profile'] = $this->userSess->profile_image;
        $op['email'] = $this->userSess->email;
        $op['mobile'] = $this->userSess->mobile;        
        $op['country'] = $this->userSess->country;
        $op['country_code'] = $this->userSess->country_code;        
        $op['has_pin'] = !empty($this->userSess->security_pin) ? 1 : 0;
        $this->statusCode = $this->config->get('httperr.SUCCESS');
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function setCountry ()
    {
        $postdata = $this->request->all();			
        $op = $location_info = $this->inti_Appdata();				
        /* if ($location_info)
        {            
            $this->geo->current = $this->geo->browse;
            $this->session->set('geo', $this->geo);
        }	 */	 
        if (isset($this->geo->browse->country_id) && !empty($this->geo->browse->country_id))
        {
            $data['country_id'] = $this->geo->browse->country_id;
            //$data['locality_id'] = $this->geo->browse->locality_id;
            //$this->storeObj = new StoreModel($this->accountObj);
            //$op['categories'] = $this->storeObj->storeCategories($data, true);
        }
		$op['msg'] = 'Country Updated Successfully';
		$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	/* public function setLocation ()
    {
        $postdata = $this->request->all();		
        $op = $location_info = $this->inti_Appdata();		
        if ($location_info)
        {
            $this->geo->current = $this->geo->browse;
            $this->session->set('geo', $this->geo);
        }
        if (isset($this->geo->browse->locality_id) && !empty($this->geo->browse->locality_id))
        {
            $data['country_id'] = $this->geo->browse->country_id;
            $data['locality_id'] = $this->geo->browse->locality_id;
            //$this->storeObj = new StoreModel($this->accountObj);
            //$op['categories'] = $this->storeObj->storeCategories($data, true);
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    } */
	
	public function checkLoginStatus ()
    {		
        $op = []; 		
		//print_r($this->session->all());exit;
		//$op = array_merge($op, $this->inti_Appdata());	
		$op = $this->inti_Appdata();	
		$op['is_guest'] = $this->userSess->is_guest;
        $op['token'] = $this->userSess->full_token; 
		$op['geo'] = $this->geo;
		$this->statusCode = $op['status'];		                       
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function inti_Appdata ()
    {
        $op = [];      
  		
        if (($this->geo->browse->isSet))
        {		
			$data['country_id'] = $this->geo->browse->country_id;
            $op['browse_location'] = ['locality'=>isset($this->geo->browse->locality) ? $this->geo->browse->locality : '', 
										'locality_id'=>isset($this->geo->browse->locality_id) ? $this->geo->browse->locality_id : '', 
										'country_code'=>$this->geo->browse->country_code, 
										'country'=>$this->geo->browse->country, 
										'country_id'=>$this->geo->browse->country_id, 
										'lat'=>$this->geo->browse->lat, 
										'lng'=>$this->geo->browse->lng];
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = 'Select Country';
            $location = $this->apiaffObj->country_list();	
            $op['countries'] = !empty($location) ? $location : [];
        }
        return $op;
    }
	
	public function locationsList ($opType = 'json')
    {
        $op = $data = [];
        $data['locality_id'] = $this->geo->current->locality_id;
        $op['list'] = $this->accountObj->getLocationsList($data);
        $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        if ($opType == 'json')
        {
            return $this->response->json($op, $this->statusCode, $this->header, $this->options);
        }
        else
        {
            return $op;
        }
    }
	
	public function saveProfilePin()
    {
		//$this->userSess->security_pin = '';
		//print_r($this->userSess);exit;
        if (empty($this->userSess->security_pin))
        {
            $data = [];
            $data['security_pin'] = $this->request->confirm_security_pin;
            $data['account_id'] = $this->userSess->account_id;
			
            if ($this->apiaffObj->saveProfilePIN($data))
            {
                $this->userSess->security_pin = md5($this->request->confirm_security_pin);
                //$this->session->set('userdata', (array) $this->userSess);
                //$op['msg'] = trans('user/account.save_pin.security_pin_created_successfully');
				//$this->config->set('app.accountInfo', $this->userSess);
				//$this->session->set($this->sessionName, (array) $this->userSess);
				$this->sessionName = $this->config->get('app.role');
				$this->session->set($this->sessionName, (array) $this->userSess);
                $op['msg'] = trans('user/account.security_pin_created_successfully');
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['msg'] = trans('general.something_wrong');
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('user/account.save_pin.already_exist');
            //$op['status'] =$this->statusCode = $this->config->get('httperr.FORBIDDEN');
            $op['status'] =$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode,$this->headers, $this->options);
    }
	
	public function verifyProfilePIN()
	{
        $op = [];
        $data = [];		
        $data['account_id'] = $this->userSess->account_id;					
		if (!empty($this->userSess->security_pin)) 
		{
			$data['security_pin'] = md5($this->request->security_pin);			
			$security_pin = $this->apiaffObj->VerifyProfilePIN($data);
			//print_r($security_pin);exit;
			if (!empty($security_pin->security_pin))
			{
				if ($security_pin->security_pin == md5($this->request->security_pin))
				{
					$op['propin_session'] = md5($this->userSess->account_id.rand(100000, 999999).date("YmdHis"));
					$this->session->set('profilePINHashCode', $op['propin_session']);
					$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
				}
				else
				{
					$op['msg'] = trans('user/account.save_pin.invalid_security');
					$this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
				}
			}			
		} 
		else if (empty($this->userSess->security_pin)) 	 
		{
			$data['security_pin'] = $this->request->security_pin;
			if ($this->apiaffObj->saveProfilePIN($data))
            {
                $this->userSess->security_pin = md5($this->request->security_pin);                
				$this->sessionName = $this->config->get('app.role');
				$this->session->set($this->sessionName, (array) $this->userSess);
				$op['msg'] = 'Your Security PIN set as '.$this->request->security_pin;
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['msg'] = trans('general.something_wrong');
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
		}
        return $this->response->json($op, $this->statusCode);
    }	
	
    public function forgotProfilePin ()
    {
        if (!empty($this->userSess->security_pin))
        {
				$data = [];
				if ($this->session->has('resetProfilePin')) {
					$op['code'] = $data['code'] = $this->session->get('resetProfilePin')['code'];
				} else {					
					$op['code'] = $data['code'] = rand(100000, 999999);
				}				
				$op['hash_code'] = $data['hash_code'] = md5($data['code']);
				$data['account_id'] = $this->userSess->account_id;
				$this->session->set('resetProfilePin', $data);
				$op['token'] = $token = $this->session->getId().'.'.$data['hash_code'];
				
				 CommonNotifSettings::notify('USER.VERIFY_SECURITY_PIN',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.USER'),['code'=>$data['code'], 'full_name'=>$this->userSess->full_name], true, false, false, true, false);	
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
				$op['msg'] = trans('user/account.forgot_pin.verification_code', ['email'=>$this->userSess->email]);
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $op['status'] =$this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode);
    }
	
	public function forgotProfilePinEmail ()
    {
        if (!empty($this->userSess->security_pin))
        {
				$data = [];				
				if ($this->session->has('resetProfilePin')) {
					$op['code'] = $data['code'] = $this->session->get('resetProfilePin')['code'];
				} else {					
					$op['code'] = $data['code'] = rand(100000, 999999);
				}	
				$op['hash_code'] = $data['hash_code'] = md5($data['code']);
				$data['account_id'] = $this->userSess->account_id;
				$this->session->set('resetProfilePin', $data);	
				$op['token'] = $token = $this->session->getId().'.'.$data['hash_code'];	
				$op['forg_link']= $forgotpin_link = url('reset-security-pin/'.$token);				
				$res = CommonNotifSettings::notify('USER.VERIFY_SECURITY_PIN_EMAIL_LINK',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.USER'),['reset_link'=>$forgotpin_link, 'code'=>$data['code'], 'full_name'=>$this->userSess->full_name], true, false, false, true, false);	
				//print_r($res);exit;
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
				$op['msg'] = 'Reset link has been sent to your email';
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $op['status'] =$this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode);
    }
	
	
	public function resetProfilePin ()
    {
        $data = $sdata = [];		
        if ($this->session->has('resetProfilePin'))
        {
            $sdata = $this->session->get('resetProfilePin');
            if ($sdata['code'] == $this->request->code)
            {
                $data['security_pin'] = $this->request->confirm_security_pin;
                if ($this->userSess->security_pin != md5($data['security_pin']))
                {
                    $data['account_id'] = $this->userSess->account_id;
                    if ($this->apiaffObj->saveProfilePIN($data))
                    {
						$this->userSess->security_pin = md5($this->request->confirm_security_pin);
        				$this->sessionName = $this->config->get('app.role');
        				$this->session->set($this->sessionName, (array) $this->userSess);                        
                        $op['propin_session'] = md5($this->userSess->account_id.rand(100000, 999999).getGTZ(null, 'YmdHis'));
                        $this->session->set('profilePINHashCode', $op['propin_session']);
						 CommonNotifSettings::notify('USER.RESET_PROFILE_PIN',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.USER'),[], true, false, false, true, false);	
						
                        $this->session->forget('resetProfilePin');
                        $op['msg'] = trans('user/account.reset_pin.security_pin_updated_successfully');
                        $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                    }
                    else
                    {
                        $op['msg'] = trans('user/account.reset_pin.something_wrong');
                        $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    }
                }
                else
                {
                    //$op['msg'] = trans('user/account.reset_pin.security_should_not_be_same');
                    $op['msg'] = trans('user/account.reset_pin.security_should_not_reuse');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
            else
            {
                $op['msg'] = trans('user/account.reset_pin.invalid_otp');
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('user/account.reset_pin.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode);
    }
	
    public function changeProfilePin ()
    {
        if (!empty($this->userSess->security_pin))
        {
            $data = [];
            $data['security_pin'] = $this->request->confirm_security_pin;			
            if (md5($this->request->current_security_pin) == $this->userSess->security_pin)
            {
                $data['account_id'] = $this->userSess->account_id;
                if ($this->apiaffObj->saveProfilePIN($data))
                {
					$this->userSess->security_pin = md5($this->request->confirm_security_pin);
					$this->sessionName = $this->config->get('app.role');
					$this->session->set($this->sessionName, (array) $this->userSess);
                    //$this->userSess->security_pin = md5($this->request->confirm_security_pin);
				    //$this->session->set('userdata', (array) $this->userSess);
                    //$this->session->set($this->sessionName, (array) $this->userSess);
				CommonNotifSettings::notify('USER.CHANGE_PROFILE_PIN',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.USER'),[], true, false, false, true, false);	
                    $op['msg'] = trans('user/account.change_pin.profile_pin_updated_successfully');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else
                {
                    $op['msg'] = trans('general.something_wrong');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
            else
            {
                $op['msg'] = trans('user/account.change_pin.invalid_current_pin');
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('user/account.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode);
    }
  public function changeEmailNewEmailOTP ()
    {
	    $op = [];
        $data = [];
     if (isset($this->userSess->toggle_app_lock) || ($this->session->has('profilePINHashCode'))){ 
          if ($this->session->get('profilePINHashCode') == $this->request->code)
            { 
                $data['old_email'] = $this->userSess->email;
                $data['new_email'] = $this->request->new_email;
                $op['code'] = $data['code'] = rand(1111, 9999);
                $op['hash_code'] = md5($data['code']);
                $this->session->set('changeEmailID', $data);
		        CommonNotifSettings::notify('USER.CHANGE_EMAIL_USER',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.USER'),['code'=>$data['code'],'email'=>$data['new_email']], true, false, false, true, false);	
                $this->session->forget('profilePINHashCode');
                $this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('affiliate/account.code_sent_to_new_email');
            }
            else{
                $op['msg'] = trans('affiliate/account.invalid_propin');
				$this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
            }
         } 
        else{
            $op['msg'] = trans('affiliate/account.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        } 
        return $this->response->json($op, $this->statusCode);
    }
  
  public function changeEmailIDConfirm ()
    {
        if ($this->session->has('changeEmailID'))
          {
            $data = $op = [];
            $data = $this->session->get('changeEmailID');
            $data['account_id'] = $this->userSess->account_id;
          if ($data['code'] == $this->request->code)
		     {
                $data['post_type'] = $this->config->get('constants.POST_TYPE.ACCOUNT');
                if ($this->apiaffObj->changeEmailID($data))
                {
                    //$op['email'] = $this->userSess->email = $data['new_email'];
                    $this->config->set('app.accountInfo', $this->userSess);
                   // $this->session->set($this->sessionName, (array) $this->userSess);
				    $this->session->set('userdata', (array) $this->userSess);  
                    $this->session->forget('changeEmailID');
                    $op['msg'] = trans('Affiliate/account.email_updated');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else{
                    $op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
                    $op['msg'] = trans('Affiliate/account.no_changes');
                }
            }
			 else {
				$op['msg'] = trans('Affiliate/account.invalid_otp');
				$this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
			}
        }
        else
        {
            $op['msg'] = trans('Affiliate/account.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode	);
    }
	
	 public function changeEmailNewEmailOTPResend () {
       if ($this->session->has('changeEmailID'))
        {
            $data = $op = [];
            $data = $this->session->get('changeEmailID');
            $op['code'] = $data['code'] = rand(1111, 9999);
            $op['hash_code'] = md5($data['code']);
            $this->session->set('changeEmailID', $data);
		   CommonNotifSettings::notify('USER.CHANGE_EMAIL_USER',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.USER'),['code'=>$data['code'],'email'=>$data['new_email']], true, false, false, true, false);	
		    $this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
            $op['msg'] = trans('affiliate/account.code_sent_to_new_email');
        }
        else
        {
            $op['msg'] = trans('affiliate/account.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode);
    }
	
	 public function changeMobileNewMobileOTP(){
	    $op = [];
        $data = [];
        if (isset($this->userSess->toggle_app_lock) || $this->session->has('profilePINHashCode'))
        {
            if (($this->session->get('profilePINHashCode') == $this->request->code))
            {
                $data['old_mobile'] = $this->userSess->mobile;
                $data['new_mobile'] = $this->request->mobile;
                $op['code'] = $data['code'] = rand(1111, 9999);
                $this->session->set('changeMobileNo', $data);
                $op['hash_code'] = md5($data['code']);
			    CommonNotifSettings::notify('USER.CHANGE_MOBILE_USER', $this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.USER'),['code'=>$data['code']], false,true, false, true, false);	
                $this->session->forget('profilePINHashCode');
                $this->statusCode =  $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('Affiliate/account.code_sent_to_new_mobile');
            }
            else
            {
                $op['msg'] = trans('Affiliate/account.invalid_security');
				$op['status'] =$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
             $op['msg'] = trans('Affiliate/account.not_accessable');
             $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode);
   }
	public function changeMobileNewMobileOTPResend ()
       {
        $data = $op = [];
        if ($this->session->has('changeMobileNo'))
        {
            $data = $this->session->get('changeMobileNo');
            $op['code'] = $data['code'] = rand(1111, 9999);
            $op['hash_code'] = md5($data['code']);
            $this->session->set('changeMobileNo', $data);
	        CommonNotifSettings::notify('USER.CHANGE_MOBILE_USER', $this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.USER'),['code'=>$data['code']],false,true, false, true, false);		
          //  CommonLib::notify(null, 'mobile_verifycode', ['code'=>$verify_code], [ 'mobile'=>$data['new_mobile']]);
            $this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
            $op['msg'] = trans('Affiliate/account.code_sent_to_new_mobile');
        }
        else
        {
            $op['msg'] = trans('Affiliate/account.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode);
    }
  public function changeMobileNoConfirm ()
     {
        $data = $op = [];
        if ($this->session->has('changeMobileNo'))
        {
            $data = $this->session->get('changeMobileNo');
            $data['account_id'] = $this->userSess->account_id;
            if ($data['code'] == $this->request->code)
            {
                $data['post_type'] = $this->config->get('constants.POST_TYPE.ACCOUNT');
                if ($this->apiaffObj->changeMobileNo($data))
                {
                    $op['mobile'] = $this->userSess->mobile = $data['new_mobile'];
                    $this->config->set('app.accountInfo', $this->userSess);
                  // $this->session->set($this->sessionName, (array) $this->userSess);
				    $this->session->set('userdata', (array) $this->userSess);  
                    $this->session->forget('changeMobileNo');
                    $op['msg'] = trans('Affiliate/account.mobile_updated');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
                    $op['msg'] = trans('Affiliate/account.no_changes');
                }
            }
            else
            {
                $op['msg'] = trans('Affiliate/account.invalid_otp');
				$this->statusCode = $op['status'] = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('Affiliate/account.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op,$this->statusCode);
    }
   
	public function forgot_password()
	{
		$op = $wdata = [];
        $postdata = $this->request->all();        
        if (strpos($postdata['uname'], '@') && preg_match('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/', $postdata['uname']))
        {
            $wdata['email'] = $postdata['uname'];
        }
        else
        {
            $op['status'] = $status = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('user/auth.forgotpwd.comm_error');
            return $this->response->json($op, $status, $this->headers, $this->options);
        }
        if (!empty($wdata))
        {
            $wdata['account_type_id'] = $this->config->get('constants.ACCOUNT_TYPE.USER');
            $usrData = $this->apiaffObj->user_name_check($wdata);
            if (!empty($usrData))
            {
                if (($usrData->login_block != 1))
				{
                    $verify_code =  rand(111111, 999999);
                    $op['code'] = $usrData->verify_code = $verify_code; 
					$op['hash_code'] = $resetKey = md5($verify_code);
                    //$usrData->ctime = date('Y-m-d H:i:s');
                    $usrData->time_out = date('Y-m-d H:i:s', strtotime('+5 minutes'));            /* otp valid for 5 minutes   */
					$time_out = $usrData->time_out = getGTZ($usrData->time_out, 'Y-m-d H:i:s');					
                    $this->session->set('forgotpwd', [$resetKey=>$usrData]);					
                    $op['token'] = $token = $this->session->getId().'.'.$resetKey;
                    $forgotpwd_link = url('resetpwd-link/'.$token);
				    CommonNotifSettings::notify('USER.FORGOT_PASSWORD',$usrData->account_id, Config::get('constants.ACCOUNT_TYPE.USER'),['sitename'=>$this->siteConfig->site_name, 'code'=>$verify_code, 'forgotpwd_link'=>$forgotpwd_link], true, false, false, true, false);					
					$op['msg'] = trans('user/auth.forgotpwd.acc_resetlink', ['email'=>$usrData->email]);
                    $op['status'] = $this->status_code = $this->config->get('httperr.SUCCESS');
                }
                else
                {                     
                    $op['msg'] = trans('user/auth.forgotpwd.acc_blocked');
                    $op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
            else
            {
                $data = [];
                if (isset($wdata['email']))
                {
                    $data['email'] = $wdata['email'];
                }                
                $op['msg'] = trans('user/auth.forgotpwd.acc_notfound');
                $op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        return $this->response->json($op, $this->status_code, $this->headers, $this->options);
	}	
	
	public function reset_pwd ()
    {
        $op = [];
        $resettoken = $this->request->header('token');		
        if (isset($resettoken) && !empty($resettoken) && strpos($resettoken, '.'))
        {
            $access_token = explode('.', $resettoken);			
            $this->session->setId($access_token[0], true);
            if ($this->session->has('forgotpwd.'.$access_token[1]))
            { 
                $usrData = $this->session->get('forgotpwd.'.$access_token[1]);					
				if(strtotime($usrData->time_out) > strtotime(getGTZ('Y-m-d H:i:s')))
				{
					if ($usrData->verify_code == $this->request->code)
					{					
						if (md5($this->request->newpwd) != $usrData->pass_key)
						{
							$res=$this->apiaffObj->reset_pwd($usrData->account_id, $this->request->newpwd);						
							if (!empty($res))
							{
								$op['msg'] = trans('user/account.forgotpwd.reset_pwd_success');
								$this->session->forget('forgotpwd');                    
								CommonNotifSettings::notify('USER.RESET_PASSWORD',$usrData->account_id, Config::get('constants.ACCOUNT_TYPE.USER'),['sitename'=>$this->siteConfig->site_name,'email'=>$usrData->email, 'uname'=>$usrData->uname], false, true, false, true, false);							   							
								$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							}
							else
							{
								$op['msg'] = trans('user/account.forgotpwd.reset_pwd_fail');
								$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
							}
						}
						else
						{
							$op['msg'] = trans('user/account.forgotpwd.new_pwd_is_same_as_old');
							$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
						}
					}
					else
					{
						$op['msg'] = trans('user/account.forgotpwd.reset_code_nomatch');
						$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
					}
				}
				else
				{
					$op['msg'] = trans('user/account.forgotpwd.reset_sess_exp');
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				}
            }
            else
            {
                $op['msg'] = trans('user/account.forgotpwd.reset_sess_exp');
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('user/account.forgotpwd.headermiss');
            $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        }
        return $this->response->json($op, $this->statusCode);
    }
	
	Public function profile_image_upload ()
    {
        $postdata = $this->request->all();
        $op = array();        
        $attachment = $postdata['attachment'];		
        $filename = '';
        $folder_path = getGTZ(null, 'Y').'/'.getGTZ(null, 'm').'/';		
        $path = $this->config->get('path.ACCOUNT.PROFILE_IMG.LOCAL');		
        $postdata['account_id'] = $this->userSess->account_id;		
        if (File::exists($path.getGTZ(null, 'Y')))
        {
            if (!File::exists($path.getGTZ(null, 'Y').'/'.getGTZ(null, 'm')))
            {
                File::makeDirectory($path.getGTZ(null, 'Y').'/'.getGTZ(null, 'm'));
            }
        }
        else
        {
            File::makeDirectory($path.getGTZ(null, 'Y'));
            File::makeDirectory($path.getGTZ(null, 'Y').'/'.getGTZ(null, 'm'));
        }
		
        $org_name = $attachment->getClientOriginalName();
        $ext = $attachment->getMimeType();
        $mine_type = array('image/jpeg'=>'jpg', 'image/jpg'=>'jpg', 'image/png'=>'png', 'image/gif'=>'gif');
        $ext = $mine_type[$ext];
        $filtered_name = $this->slug($org_name);
        $file_name = explode('_', $filtered_name);
        $file_name = $file_name[0];
        $file_name = $file_name.'.'.$ext;
        $filename = getGTZ(null, 'dmYHis').$file_name;
        if ($attachment->move($path.$folder_path, $filename))
        {
            $postdata['filename'] = $filename;
            $postdata['docpath'] = $folder_path.$filename;			
            if ($this->accountObj->profile_image_upload($postdata))
            {
				$op['profile_img'] = $this->userSess->profile_image = asset($this->config->get('path.ACCOUNT.PROFILE_IMG.WEB.100x100').$postdata['docpath']);
				$this->sessionName = $this->config->get('app.role');
				$this->session->set($this->sessionName, (array) $this->userSess);
				$this->config->set('app.accountInfo', $this->userSess);
                $this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('user/account.profile_image_update');                               
            }
            else
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('user/account.no_changes');
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	

}
