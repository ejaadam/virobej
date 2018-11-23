<?php
namespace App\Http\Controllers\Api\Affiliate;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BaseController;
use App\Models\Api\Affiliate\AffModel;
use App\Models\CommonModel;
use Session;
use TWMailer;

class ApiAffAuthcontroller extends BaseController
{
    public function __construct ()
    {
        parent::__construct();
		$this->apiaffObj = new AffModel();
		$this->commonObj = new CommonModel();
    }	
	
	public function postLoginCheck ()
    {
		//print_r(Session::all());exit;
        $op = array();
        $op['status'] = '';
        $op['msg'] = trans('auth.login_msg.comm_error');
        $res = '';
        $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        $postdata = $this->request->all();		
        if (!empty($postdata))
        {
            if ($this->request->uname && $this->request->password)
            {
                $data['account_type'] = $this->config->get('constants.ACCOUNT_TYPE.USER');
                $data['country_id'] = $this->country_id;				
                $res = $this->commonObj->checkAccount_login($postdata, $data);                
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
					$op['token'] =  $res['accinfo']->token;
					       
					$op['is_verified'] = $res['accinfo']->is_verified;     					   
                    //$op['msg'] = trans('user/auth.login_msg.acc_success');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');					 
                }
                elseif ($validate == 2)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('affiliate/auth.login_msg.acc_not_found');                    
                }
                elseif ($validate == 3)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('affiliate/auth.login_msg.invalid_uname_pwd');
                }
                elseif ($validate == 4)
                {
                    $op['status']  = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('affiliate/auth.login_msg.acc_invid');
                }
                elseif ($validate == 5)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('affiliate/auth.login_msg.acc_blocked');
                }
				elseif ($validate == 6)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                     
                    $op['msg'] = trans('affiliate/auth.login_msg.acc_blocked');
                }
                elseif ($validate == 7)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                     
                    $op['msg'] = trans('affiliate/auth.login_msg.acc_blocked');
                }
                elseif ($validate == 8)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('affiliate/auth.login_msg.comm_error');
                }
                elseif ($validate == 9)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('affiliate/auth.login_msg.acc_nofound');                    
                }
                elseif ($validate == 10)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('affiliate/auth.login_msg.invalid_username');
                }
				elseif ($validate == 14)
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                     
                    $op['msg'] = trans('affiliate/auth.login_msg.status_not_active');
                }
                else
                {
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                    
                    $op['msg'] = trans('affiliate/auth.login_msg.acc_invemail');
                }
            }
            else
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');                
                if ($this->request->uname && $this->request->password)
                {
                    $op['msg'] = trans('affiliate/auth.login_msg.comm_error');
                }
                else if (!$this->request->uname)
                {
                    $op['msg'] = trans('affiliate/auth.login_msg.comm_error');
                }
                else if (!$this->request->password)
                {
                    $op['msg'] = trans('affiliate/auth.login_msg.comm_error');
                }
            }
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
            $op['msg'] = trans('affiliate/auth.login_msg.comm_error');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	

    public function logout ()
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
    }
	
	public function changepwd ()
    {
        $op = [];
        $postdata = $this->request->all();		
        $postdata['account_id'] = $this->userSess->account_id;
		//print_r($postdata);exit;
        if ($this->userSess->pass_key == md5($this->request->current_password))
        {
            if ($this->userSess->pass_key != md5($this->request->conf_password))
            {
                if ($res = $this->apiaffObj->update_password($this->userSess->account_id, $this->request->conf_password))
                {   					
                    $this->userSess->pass_key = md5($this->request->conf_password);
                    $this->config->set('app.accountInfo', $this->userSess);
                    //$this->session->set($this->sessionName, (array) $this->userSess);
                    //CommonLib::notify($this->userSess->account_id, 'change_password', ['email'=>$this->userSess->email, 'home_url'=>route('user.home')]);
                    $op['msg'] = 'Password Updated Successfully';
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
            $op['msg'] = 'Current Password Not Matched.';
            $op['status'] = $status = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $status, $this->headers, $this->options);
    }
	
	  public function updateProfile (){
        $op = [];
        $data = $this->request->all();
        $data['account_id'] = $this->userSess->account_id;
        $str_gender = $data['gender'];
        $data['gender'] = $this->config->get('constants.GENDER.'.$data['gender']);
        if ($data)
        {
            $result = $this->apiaffObj->updateProfile($data);
            if ($result)
            {
                $this->userSess->first_name = $data['first_name'];
                $this->userSess->last_name = $data['last_name'];
                $this->userSess->gender =  $str_gender;
                $this->userSess->dob = showUTZ($data['dob'], 'M-d-Y');
                $this->config->set('app.accountInfo', $this->userSess);		
              	//$this->session->set($this->sessionName, (array) $this->userSess);
                $op['msg'] = trans('affiliate/account.profile_updated');
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
                $op['msg'] = trans('affiliate/account.no_changes');
            }
        }
        else
        {
            $op['msg'] = trans('affiliate/account.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode);
    }
	
	public function checkLoginStatus ()
    {
        $op = [];              
		$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $token = $this->userSess->token;
		if ($token) {
			$op['token'] = $token;
			$op['is_guest'] = 0;
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			$op['country_id'] = $this->userSess->country_id;
			$op['currency_id'] = $this->userSess->currency_id;			
		}      
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function saveProfilePin ()
    {
        if (empty($this->userSess->security_pin))
        {
            $data = [];
            $data['security_pin'] = $this->request->security_pin;
            $data['account_id'] = $this->userSess->account_id;
            if ($this->apiaffObj->saveProfilePIN($data))
            {
                $this->userSess->security_pin = md5($this->request->security_pin);
                $this->session->set('userdata', (array) $this->userSess);
               /*  CommonLib::notify($this->userSess->account_id, 'set_profile_pin', ['pin'=>$data['security_pin']], [], true); */
                $op['msg'] = trans('affiliate/account.profile_pin_created_successfully');
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
            $op['msg'] = trans('affiliate/account.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op, $this->statusCode);
    }
	
	  public function verifyProfilePIN(){
        $op = [];
        $data = [];
		$data['security_pin'] = md5($this->request->security_pin);
        $data['account_id'] = $this->userSess->account_id;
		$security_pin=$this->apiaffObj->VerifyProfilePIN($data);

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
                $op['msg'] = trans('affiliate/account.invalid_profile_pin');
            }
        }
        else
        {
            $op['msg'] = trans('affiliate/account.generate_profile_pin');
        }
        return $this->response->json($op, $this->statusCode);
    }
  public function changeEmailNewEmailOTP ()
    {
	    $op = [];
        $data = [];
     if (isset($this->userSess->toggle_app_lock) || ($this->session->has('profilePINHashCode'))){ 
          if ($this->session->get('profilePINHashCode') == $this->request->propin_session)
            { 
                $data['old_email'] = $this->userSess->email;
                $data['new_email'] = $this->request->new_email;
                $op['code'] = $data['code'] = rand(1111, 9999);
                $op['hash_code'] = md5($data['code']);
                $this->session->set('changeEmailID', $data);
               //CommonLib::notify(null,'verify_new_email', ['code'=>$data['code']], ['email'=>$data['new_email']], true);
			    $mstatus = TWMailer::send(array(
				 'to'=>$data['new_email'],
				 'subject'=>'OTP for Change Mail',
				 'view'=>'emails.otp_mail',	
				 'data'=>$data,
				 'from'=>$this->pagesettings->noreplay_emailid,
				 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings);	
			   
                $this->session->forget('profilePINHashCode');
                $this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('affiliate/account.code_sent_to_new_email');
            }
            else{
                $op['msg'] = trans('affiliate/account.invalid_profile_pin');
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
                    $op['email'] = $this->userSess->email = $data['new_email'];
                    $this->config->set('app.accountInfo', $this->userSess);
                 //   $this->session->set($this->sessionName, (array) $this->userSess);
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
          //  CommonLib::notify(null, 'verify_new_email', ['code'=>$data['code']], ['email'=>$data['new_email']]);
		    $mstatus = TWMailer::send(array(
				 'to'=>$data['new_email'],
				 'subject'=>'OTP for Change Mail',
				 'view'=>'emails.otp_mail',	
				 'data'=>$data,
				 'from'=>$this->pagesettings->noreplay_emailid,
				 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings);	
		  
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
            if (($this->session->get('profilePINHashCode') == $this->request->propin_session))
            {
                $data['old_mobile'] = $this->userSess->mobile;
                $data['new_mobile'] = $this->request->mobile;
                $op['code'] = $data['code'] = rand(1111, 9999);
                $this->session->set('changeMobileNo', $data);
                $op['hash_code'] = md5($data['code']);
                //CommonLib::notify(null, 'mobile_verifycode', ['code'=>$data['code']], [ 'mobile'=>$data['new_mobile']]);
                $this->session->forget('profilePINHashCode');
                $this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('Affiliate/account.code_sent_to_new_mobile');
            }
            else
            {
                $op['msg'] = trans('Affiliate/account.invalid_profile_pin');
            }
        }
        else
        {
            $op['status'] = $op['msg'] = trans('Affiliate/account.not_accessable');
            $this->statusCode = $this->config->get('httperr.FORBIDDEN');
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
            }
        }
        else
        {
            $op['msg'] = trans('Affiliate/account.not_accessable');
            $op['status'] = $this->statusCode = $this->config->get('httperr.FORBIDDEN');
        }
        return $this->response->json($op,$this->statusCode);
    }
	public function edit_profile(){
     $data = $op = [];
	 $data['account_id']=$this->userSess->account_id;
	 $details=$this->apiaffObj->edit_profile($data);
	 $op['details']=$details;
	 $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
	 return $this->response->json($op,$this->statusCode);
	
	}
	public function forgot_password(){
		$op = $wdata = [];
        $postdata = $this->request->all();
        $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
        if (strpos($postdata['uname'], '@') && preg_match('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/', $postdata['uname']))
        {
            $wdata['email'] = $postdata['uname'];
        }
        else
        {
            $op['status'] = 'error';
            $op['msg'] = trans('affiliate/auth.forgotpwd.comm_error');
            return $this->response->json($op, $status = 400);
        }
        if (!empty($wdata))
        {
            $wdata['account_type_id'] = $this->config->get('constants.ACCOUNT_TYPE.USER');
            $usrData = $this->apiaffObj->user_name_check($wdata);
            if (!empty($usrData))
            {
                if (($usrData->login_block != 1)){
					
                    $op['code'] = $usrData->verify_code = rand(100000, 999999);
                    $op['hash_code'] = $resetKey = md5($usrData->verify_code);
                    $time_out = $usrData->time_out = date('Y-m-d H:i:s', strtotime('+8 Hours'));
                    $this->session->set('forgotpwd', [$resetKey=>$usrData]);
                    $op['token'] = $token = $this->session->getId().'.'.$resetKey;
					
                    //$forgotpwd_link = route('affiliate.forgotpwd-link', ['hash_code'=>$token]);
					
					$forgotpwd_link = url('resetpwd-link/'.$token);
					$remove_forgotSess = url('remove-resetpwd-sess/'.$token);
                   // $remove_forgotSess = route('user.remove-forgotpwdSess', ['hash_code'=>$token]);
                   
				 //  CommonLib::notify(null, 'forgotpwd_verifycode', ['code'=>$usrData->verify_code,'email'=>$wdata['email']]);
                    
					$op['msg'] = trans('affiliate/auth.forgotpwd.acc_resetlink', ['email'=>$usrData->email]);
                    $op['status'] = $this->status_code = $this->config->get('httperr.SUCCESS');
                }
                else
                {
                    $op['status'] = 'error';
                    $op['msg'] = trans('affiliate/auth.forgotpwd.acc_blocked');
                    $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
            else
            {
                $data = [];
                if (isset($wdata['email']))
                {
                    $data['email'] = $wdata['email'];
                }
               // CommonLib::notify(null, 'customer_promotional', [], $data);
                $op['status'] = 'error';
                $op['msg'] = trans('affiliate/auth.forgotpwd.acc_notfound');
                $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        return $this->response->json($op, $this->status_code);
		
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
                if ($usrData->verify_code == $this->request->code)
                {
                    if (md5($this->request->newpwd) != $usrData->pass_key)
                    {
						$res=$this->apiaffObj->reset_pwd($usrData->account_id, $this->request->newpwd);
                        if (!empty($res))
                        {
                            $op['msg'] = trans('affiliate/auth.forgotpwd.reset_pwd_success');
                            $this->session->forget('forgotpwd');
                           // $home_url = route('user.home');
                           // CommonLib::notify(null, 'change_password', ['password'=>$this->request->newpwd,'email'=>$usrData->email]);
                            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                        }
                        else
                        {
							
                            $op['msg'] = trans('affiliate/auth.forgotpwd.reset_pwd_fail');
                            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                        }
                    }
                    else
                    {
                        $op['msg'] = trans('affiliate/auth.forgotpwd.new_pwd_is_same_as_old');
                        $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    }
                }
                else
                {
                    $op['msg'] = trans('affiliate/auth.forgotpwd.reset_code_nomatch');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
            else
            {
                $op['msg'] = trans('affiliate/auth.forgotpwd.reset_sess_exp');
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('affiliate/auth.forgotpwd.headermiss');
            $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        }
        return $this->response->json($op, $this->statusCode);
    }
}
