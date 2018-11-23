<?php
namespace App\Http\Controllers\Affiliate;

use Illuminate\Support\Facades\Validator;
use App\Library\MailerLib;
use SendSMS;
use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\AffModel;
use App\Models\Affiliate\Settings;
use TWMailer;
use CommonLib;
class SettingsController extends AffBaseController {    

	private $smsObj;
    public $userObj; 
    public $settingsObj; 
    public function __construct ()
    {
        parent::__construct();
		$this->smsObj = new SendSMS;	
		$this->affObj = new AffModel;	
		$this->settingsObj = new Settings;	
    }	
		
	public function security_settings(){
		$data = array();
		$data['email']=$this->userSess->email;
		$data['mobile']=$this->userSess->mobile;
//		$data['phonecode']=$this->userSess->phonecode;
		return view('affiliate.settings.security_settings',$data);
	}
	
	/* Email Update */
	public function sendUpdate_emailVerification ()
    {   
		$new_email = '';
		$op = array();
        $postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');  
		if (!empty($postdata))
        { 
			$rules =  [            
				'email' => 'required|email|max:40|unique:'.$this->config->get('tables.ACCOUNT_MST'),
			];
			$messages = [
			  'email.required' => trans('affiliate/validator/change_email_js.email'),
			  'email.email' => trans('affiliate/validator/change_email_js.invalid_email'), 
			  'email.max' => trans('affiliate/validator/change_email_js.max_length') ,
			  'email.unique' => trans('affiliate/validator/change_email_js.unique'),
			];		
			$validator = Validator::make($postdata, $rules,$messages);
			
			if ($validator->fails())
			{	
				$errs = $validator->errors();
				foreach($rules  as $key=>$formats){
					$op['errs'][$key] =  $validator->errors()->first($key);			
				}
				return $this->response->json($op,500);
			}	
            $new_email= $postdata['email'];		
			$cur_email = $this->userSess->email; 
			$sess_key = md5($cur_email.$this->account_id); 
			$verification_code = rand(111111, 999999); 
			
			$this->session->set($sess_key, array(
				'sess_key1'=>$sess_key,
				'new_email'=>$new_email,
				'account_id'=>$this->account_id,
				'verify_code'=>$verification_code)); 
            	
	        $res = $this->session->get('userdata');			
			$res['reset_session'] = $sess_key;			
			$user_session = $this->session->set('userdata',$res);	
			$email_data = array('email'=>$new_email);				
			$data = array('email_verify_link'=>URL('affiliate/settings/update_email').'?id='.$sess_key,'verification_code'=>$verification_code);
			$htmls = view('emails.affiliate.account.settings.update_email', $data)->render();
			try {
			  $mstatus = TWMailer::send(array(
					 'to'=>$email_data['email'],
					 'subject'=>trans('affiliate/settings/change_email.verify_email'),
					 'view'=> $htmls,
					 'data'=>$data,
					 'from'=>$this->pagesettings->noreplay_emailid,
					 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings
                   );  
				$op['status'] = 'ok';
				$op['msg'] = trans('affiliate/settings/change_email.check_email_inbox',['email'=>$email_data['email']]);
				$op['verification_code'] = $verification_code;
			}
			catch(Exception $e) {
				$op['status'] = 'error';
				$op['msg'] = trans('affiliate/settings/change_email.error_msg');
			}				
		}
        return $this->response->json($op); 
    }

	public function update_email()
    {		
		$op = array();
		$verify_sess = '';
		$postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
		$checkType = 0;
		if($this->session->has('userdata'))
		{
			$res = $this->session->get('userdata');
			$sess_key = $res['reset_session'];
			$verifySess = $this->session->get($sess_key);
		} 
		if (!empty($postdata))
		{
			if(isset($postdata['verify_code']))
			{
			    $rules =[
					'verify_code' => 'required|numeric|digits_between:6,6',
				];
				$messages =[
					'verify_code.required' => trans('affiliate/validator/change_email_js.verify_code'),
					'verify_code.numeric' => trans('affiliate/validator/change_email_js.numeric'), 
					'verify_code.digits_between' => trans('affiliate/validator/change_email_js.maxlength'),
				];
				$validator = Validator::make($postdata,$rules,$messages);
				if ($validator->fails())
				{	
					$ers = $validator->errors();
					foreach($rules  as $key=>$formats){
						$op['errs'][$key] =  $validator->errors()->first($key);			
					}
					return $this->response->json($op,500);
				}
				$checkType = 1;
			}
			
		}		
        $postdata = $this->request->all();
        if ($checkType == 1)
        {	
            if ($verifySess['verify_code'] == $postdata['verify_code'])
            {	
				$updateRes = $this->settingsObj->update_user_email($verifySess['account_id'], $verifySess['new_email']);
				if ($updateRes){   
					$res['email']= $verifySess['new_email'];	
					$res['reset_session']='';
					$this->session->set('userdata',$res);
					return $this->response->json(['status'=>200,'msg'=>trans('affiliate/settings/change_email.update_email_success')]);
				}                
            }
            else
            {
			    return $this->response->json(['status'=>500,'msg'=>trans('affiliate/settings/change_email.email_req_expiry')]);
            }
        }
		
    }
	
	/* Mobile Number Update */
    public function sendUpdate_mobileVerification ()
    {   
		$new_mobile = '';
		$op = array();
		$postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
		
		if (!empty($postdata))
        {
			$rules =  [            
				'mobile' => 'required|numeric|digits_between:10,10|unique:'.$this->config->get('tables.ACCOUNT_MST'),
			];
			$messages = [
			  'mobile.required' => trans('affiliate/validator/change_mobile_js.mobile'), 
			  'mobile.numeric' => trans('affiliate/validator/change_mobile_js.invalid_mobile'), 
			  'mobile.digits_between' => trans('affiliate/validator/change_mobile_js.mobile_max'),
			  'mobile.unique' => trans('affiliate/validator/change_mobile_js.unique'),
			];
		    $validator = Validator::make($postdata, $rules,$messages);
			if ($validator->fails())
			{	
				$ers = $validator->errors();
				foreach($rules  as $key=>$formats){
					$op['errs'][$key] =  $validator->errors()->first($key);			
				}
				return $this->response->json($op,500);
			}
			$new_mobile = $postdata['mobile'];			
			$cur_mobile = $this->userSess->mobile;
			$sess_key = md5($cur_mobile.$this->account_id);
			$verification_code = rand(111111, 999999);

			$this->session->set($sess_key, array(
				'sess_key1'=>$sess_key,
				'new_mobile'=>$new_mobile,
				'account_id'=>$this->account_id,
				'verify_code'=>$verification_code));
			$res = $this->session->get('userdata');
			$res['reset_session'] = $sess_key;
			$user_session = $this->session->set('userdata',$res);
		    try{
              CommonLib::notify(null, 'mobile_verifycode', ['code'=>$verification_code], [ 'mobile'=>$new_mobile]);			
				/* $res=$this->smsObj->send_sms(['reset_code'=>$verification_code,'phonecode'=>$this->userSess->phonecode,'mobile'=>$new_mobile,'site_name'=>$this->siteConfig->site_name],$this->config->get('sms_service.MOBILEUPDATE_RESETCODE')); */
				$op['status'] = 'ok';
				$op['verification_code'] = $verification_code;
				$op['msg'] = trans('affiliate/settings/change_mobile.check_mobile_inbox',['mobile'=>$new_mobile]); 	
            }catch(Exception $e) {
				$op['status'] = 'error';
				$op['msg'] = trans('affiliate/settings/change_mobile.error_msg');
			}			
		}
        return $this->response->json($op); 
    }

	public function update_mobile()
    {		

		$op = $postdata=array();
		$verify_sess = '';
		$postdata = $this->request->all();
        $op['msg'] = trans('affiliate/general.something_wrong');
        $op['status'] = trans('affiliate/general.error');
		$checkType = 0;
		
		if($this->session->has('userdata'))
		{
			$res = $this->session->get('userdata');
			$sess_key = $res['reset_session'];
			$verifySess = $this->session->get($sess_key);
		} 
		if (!empty($postdata))
		{	
			if(isset($postdata['verify_code']))
			{ 
			    $rules =[
					'verify_code' => 'required|numeric|digits_between:6,6',
				];
				$messages =[
					'verify_code.required' => trans('affiliate/validator/change_mobile_js.verify_code'), 
					'verify_code.numeric' => trans('affiliate/validator/change_mobile_js.numeric'), 
					'verify_code.digits_between' => trans('affiliate/validator/change_mobile_js.maxlength'), 
				];
				$validator = Validator::make($postdata,$rules,$messages);
				if ($validator->fails())
				{	
					$ers = $validator->errors();
					foreach($rules  as $key=>$formats){
						$op['errs'][$key] =  $validator->errors()->first($key);			
					}
					return $this->response->json($op,500);
				}
				$checkType = 1;
			}
		}		
        $postdata = $this->request->all();;
		$postdata['email']=$this->userSess->email;
		
        if ($checkType == 1)
        { 	 
            if ($verifySess['verify_code'] == $postdata['verify_code'])
            {	
				$updateRes = $this->settingsObj->update_user_mobile($verifySess['account_id'], $verifySess['new_mobile']);
				
				if ($updateRes)
				{  
				   // $data = array('siteConfig'=>$this->siteConfig);
				    $htmls = View('emails.affiliate.account.settings.update_mobile')->render();
			        $mstatus = TWMailer::send(array(
					'to'=> $postdata['email'],
					'subject'=>trans('affiliate/settings/change_mobile.mobile_notify'),
					 'view'=> $htmls,
					 'from'=>$this->pagesettings->noreplay_emailid,
					 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings
                   ); 
					
					$res['mobile']=$verifySess['new_mobile']; 	 
					$res['reset_session']='';	
					$this->session->set('userdata',$res);
					return $this->response->json(['status'=>200,'msg'=>trans('affiliate/settings/change_mobile.update_mobile_success')]);
				}                
            }
            else
            {
			    return $this->response->json(['status'=>500,'msg'=>trans('affiliate/settings/change_mobile.mobile_req_expiry')]);
            }
        }
        else
        {
            return App::abort('404');
        }
    } 
	 
	public function change_pwd(){
		$data = array();
		return view('affiliate.settings.change_pwd',$data);
	}

	public function change_securitypin(){
		$data = array();
		return view('affiliate.settings.change_securitypin',$data);
	}
	
    public function updatepwd()
    {
        $data['status'] = '';
        $data['msg'] = '';
        $postdata = $this->request->all();
	    if (!empty($postdata))
		{
			if(isset($postdata['newpassword']))
			{ 	
         		$rules =[
				         'oldpassword'=>'required|regex:/([a-zA-Z0-9]+)/|min:5|max:20',
						 'newpassword' => 'required|regex:/([a-zA-Z0-9]+)/|min:5|max:20|different:oldpassword',
				         'confirmpassword'=> 'required|regex:/([a-zA-Z0-9]+)/|min:5|max:20|same:newpassword',
				];
				$messages =[
					 'oldpassword.regex' => trans('affiliate/validator/change_email_js.old_password'), 
					 'oldpassword.min' => trans('affiliate/validator/change_email_js.min_length') ,
					 'oldpassword.max' => trans('affiliate/validator/change_email_js.max_length') ,
					 'newpassword.different' => trans('affiliate/validator/change_email_js.different'), 
					 'newpassword.regex' => trans('affiliate/validator/change_email_js.new_password'), 
					 'newpassword.min' => trans('affiliate/validator/change_email_js.min_length') ,
					 'newpassword.max' => trans('affiliate/validator/change_email_js.max_length') ,
					 'confirmpassword.same'=>trans('affiliate/validator/change_email_js.confirm_password') ,
				];
				$validator = Validator::make($postdata,$rules,$messages);
				if ($validator->fails())
				{	
					$ers = $validator->errors();
					foreach($rules  as $key=>$formats){
						$op['errs'][$key] =  $validator->errors()->first($key);			
					}
					return response()->json($op,500);
				}
				$checkType = 1;
			}
	      $rdata = $this->affObj->update_password($this->userSess->account_id, $postdata);
	      $data = array(
			'full_name'=>$this->userSess->full_name,
			'uname'=>$this->userSess->uname,
			'password'=>$postdata['newpassword'],
			'last_activity'=>date('Y-m-d H:i:s'),
			'client_ip'=>$this->request->ip(true));
			$email_data = array('email'=>$this->userSess->email);
			 $htmls = view('emails.affiliate.account.settings.change_password', $data)->render();
		   $mstatus = TWMailer::send(array(
					 'to'=>$email_data['email'], 
					 'subject'=>'Your Password has been changed',
					 'view'=> $htmls,
					 'data'=>$data,
					 'from'=>$this->pagesettings->noreplay_emailid,
					 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings
                   ); 
        return $rdata;
        }
	}

    public function password_check()
    {
        $postdata = $this->request->all();
		if (!empty($postdata))
		{
			if(isset($postdata['oldpassword']))
			{ 	
         		$rules =['oldpassword' => 'required|regex:/([a-zA-Z0-9]+)/|min:5|max:20',];
				$messages =[
					'oldpassword.regex' => trans('affiliate/validator/change_email_js.oldpasword'), 
					 'oldpassword.min' => trans('affiliate/validator/change_email_js.min_length') ,
					 'oldpassword.max' => trans('affiliate/validator/change_email_js.max_length') ,
				];
				$validator = Validator::make($postdata,$rules,$messages);
				if ($validator->fails())
				{	
					$ers = $validator->errors();
					foreach($rules  as $key=>$formats){
						$op['errs'][$key] =  $validator->errors()->first($key);			
					}
					return response()->json($op,500);
				}
				$checkType = 1;
			}
        $oldpassword = $postdata['oldpassword'];
	     $data = $this->affObj->password_check($oldpassword, $this->userSess->account_id);	
		echo json_encode($data);
        }	
	}
	public function check_securitypwd ()
    {
		$postdata = $this->request->all();
        $oldpassword = $postdata['oldpassword'];
        $data = $this->affObj->tran_password_check($oldpassword, $this->userSess->account_id);		
        echo json_encode($data);
    }
	
    public function update_securitypwd()
    {
        $data['status'] = '';
        $data['msg'] = '';
	    $postdata = $this->request->all();
		if (!empty($postdata))
		{
			if(isset($postdata['tran_newpassword']))
			{ 	
         		$rules =[
				         'tran_oldpassword'=>'required|regex:/([a-zA-Z0-9]+)/|min:5|max:20',
						 'tran_newpassword' => 'required|regex:/([a-zA-Z0-9]+)/|min:5|max:20',
				         'tran_confirmpassword'=> 'required|regex:/([a-zA-Z0-9]+)/|min:5|max:20|same:tran_newpassword',
						
				];
				$messages =[
					 'tran_oldpassword.regex' => trans('affiliate/validator/change_email_js.old_password'), 
					 'tran_oldpassword.min' => trans('affiliate/validator/change_email_js.min_length') ,
					 'tran_oldpassword.max' => trans('affiliate/validator/change_email_js.max_length') ,
					 'tran_newpassword.regex' => trans('affiliate/validator/change_email_js.new_password'), 
					 'tran_newpassword.min' => trans('affiliate/validator/change_email_js.min_length') ,
					 'tran_newpassword.max' => trans('affiliate/validator/change_email_js.max_length') ,
					 'tran_confirmpassword.same'=>trans('affiliate/validator/change_email_js.confirm_password') ,
				];
				$validator = Validator::make($postdata,$rules,$messages);
				if ($validator->fails())
				{	
					$ers = $validator->errors();
					foreach($rules  as $key=>$formats){
						$op['errs'][$key] =  $validator->errors()->first($key);			
					}
					return response()->json($op,500);
				}
				$checkType = 1;
			}
        $rdata = $this->affObj->tran_update_password($this->userSess->account_id, $postdata);
	    $data = array(
			'full_name'=>$this->userSess->full_name,
			'uname'=>$this->userSess->uname,
			'newpassword'=>$postdata['tran_newpassword'],
			'last_activity'=>date('Y-m-d H:i:s'),
			  ); 
			 $email_data = array('email'=>$this->userSess->email);
			 $htmls = view('emails.affiliate.account.settings.change_transaction_password', $data)->render();
			 
		      $mstatus = TWMailer::send(array(
					 'to'=> $email_data['email'],
					 'subject'=>'Your Security Pin has been changed',
					 'view'=>$htmls,
					 'data'=>$data,
					 'from'=>$this->pagesettings->noreplay_emailid,
					 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings
			        ); 
        return $rdata;
        }
	}
	
	public function forgot_security_pwd ()
    {
        $op['status'] = "error";
        $op['alertclass'] = "alert-error";
		$data = array(
		/* 'siteconfig'=>$this->siteConfig, */
			'email' => $this->userSess->email,
			'uname' => $this->userSess->uname,
			'full_name' => $this->userSess->full_name);
			$data['code'] = rand(100000, 999999);
            $data['account_id'] = $this->userSess->account_id;
			$this->session->set('resetProfilePin', $data);
		    $activation_key = md5($this->userSess->account_id.date('ymshis'));
			$saveRes = $this->affObj->update_account_activationkey($this->userSess->account_id,['activation_key'=>$activation_key]);
		if($saveRes){
			$data['userinfo'] = $this->userSess;
			$data['resetlink'] = route('aff.settings.reset_security_pwd',['key'=>$activation_key]); 
			print_r($data);
		    $email_data = array('email'=>$this->userSess->email);
		    $htmls = view('emails.affiliate.account.settings.reset_security_pwd', $data)->render();
			 $mstatus = TWMailer::send(array(
					 'to'=> $this->userSess->email,
					 'subject'=>'Security PIN Reset Notification',
					 'html'=>$htmls,
					 'from'=>$this->pagesettings->noreplay_emailid,
					 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings
			        ); 
			 $op['status'] = "ok";
             $op['msg'] = trans('affiliate\settings\security_pwd.forgot_msg', array('msg'=>$data['email']));
             return response()->json($op);
		}
	    return response()->json($op); 
	}
      public function reset_security_pwd($post_val){
		
		return view('affiliate.settings.update_security_pwd');	
	 }
	 public function updatesecuritypwd(){

		$data = $sdata = [];
        if ($this->session->has('resetProfilePin'))
        {
           $sdata = $this->session->get('resetProfilePin');
		 
            $data['security_pin'] = $this->request->tran_newpassword;
				$check_profilepin=$this->affObj->profilepin_check($this->userSess->account_id);
                if ($check_profilepin->trans_pass_key != md5($data['security_pin']))
                {
                    $data['account_id'] = $this->userSess->account_id;
                    if ($this->affObj->saveProfilePIN($data))
                    {
                        $this->session->forget('resetProfilePin');
                        $op['msg'] = trans('affiliate/account.profile_pin_updated_successfully');
                        $op['status'] ='alert-success';
                    }
                    else
                    {
                        $op['msg'] = trans('general.something_wrong');
                        $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    }  
                }
                else
                {
                    $op['msg'] = trans('user/account.profile_pin_should_not_be_same');
                   $op['status'] = 'alert-danger';
                } 
        }
        else
        {
            $op['msg'] = trans('affiliate/account.not_accessable');
            $op['status']='alert-danger';
        }
        return $this->response->json($op); 
		 
	 }
	public function otp_check(){
		$data = $sdata = [];
        if ($this->session->has('resetProfilePin'))
        {
           $sdata = $this->session->get('resetProfilePin');
            if ($sdata['code'] == $this->request->otp) {
				 $op['status'] ='alert-success';
				 $op['msg']='ok';
            }
         else{
                $op['msg'] = trans('affiliate/account.invalid_otp');
				$op['status']='alert-danger';
            }
        }
        else{
            $op['msg'] = trans('affiliate/account.not_accessable');
            $op['status']='alert-danger';
        }
        return $this->response->json($op); 
	 }
}