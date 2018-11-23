<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;
use App\Http\Controllers\MyImage;
use App\Models\Seller\Supplier;
use App\Models\Seller\Product;
use App\Models\Seller\Account;
use App\Models\Memberauth;
use App\Models\Commonsettings;
use App\Helpers\CommonNotifSettings;
use Config;
use Lang;
use Redirect;
use Session;
use Input;
use Response;
use File;

class SupplierController extends SupplierBaseController
{  
    public $data = array();
    public $adminObj = '';

    public function __construct ()
    {
        parent::__construct();
		$this->imgObj = new MyImage();
        $this->suppObj = new Supplier();
        $this->proObj = new Product();
        $this->accObj = new Account();
        $this->commonObj = new Commonsettings();
		$this->memberObj = new MemberAuth($this->commonObj);		
    }   

    public function login ()
    {    
		if (\Request::ajax()) {
			$op = [];
			$postdata = $this->request->all();  
			$res = $this->memberObj->validateUser($postdata, Config::get('constants.ACCOUNT_TYPE.SELLER'));	
			$op = array_merge($op, $res->response);
			$this->statusCode = $res->response['status'];	
			return Response::json($op, $this->statusCode, $this->headers, $this->options);
		} else {
			//$data = ['login'=>true];
			$data['lfields'] = CommonNotifSettings::getHTMLValidation('seller.login');
			$data['ffields'] = CommonNotifSettings::getHTMLValidation('seller.forgot-password');
			$data['fofields'] = CommonNotifSettings::getHTMLValidation('seller.forgot_opt');
			$data['rfields'] = CommonNotifSettings::getHTMLValidation('seller.resetpwd');	 
			//echo "<pre>"; print_r($data);exit;			
			return view('seller.login', $data);
		}
    }	
	
	public function dashboard ()
    {
		$data = [];						
        return View('seller.dashboard', $data);		
    }
	
	public function verify_email()
	{
		$data = [];		
		$data['details'] = $this->suppObj->suppiler_acc_details($this->account_id);
	    $data['evfields'] = CommonNotifSettings::getHTMLValidation('seller.check-email-verification');			
	    $data['cefields'] = CommonNotifSettings::getHTMLValidation('seller.profile-settings.change-email.new-email-otp');			
	    return View('seller.verify_email', $data);		
	}
	
	public function slug ($text)
    {
        $text = preg_replace('/\W|_/', '_', $text);
        // Clean up extra dashes
        $text = preg_replace('/-+/', '-', trim($text, '_')); // Clean up extra dashes
        // lowercase
        $text = strtolower($text);
        if (empty($text))
        {
            return false;
        }
        return $text;
    }
	
	public function AccountSettings ()
    {
		$data = [];	
		if (\Request::ajax()) {
			$op = [];
			$postdata = Input::all();    
			$postdata['account_id'] = $this->userSess->account_id;    
			$postdata['supplier_id'] = $this->userSess->supplier_id;  
			$postdata['account_type'] = $this->userSess->account_type_id;    
			if (!empty($postdata['profile_image']))
			{
				$attachment = $postdata['profile_image'];
				$size = $attachment->getSize();
				if ($size < 2049133)
				{
					$filename = '';
					if ($attachment != '')
					{
						$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';						
						$path = $this->config->get('path.SELLER.PROFILE_IMG_PATH.LOCAL');
                        $org_path = $this->config->get('path.SELLER.PROFILE_IMG_PATH.ORIGINAL');
                        $postdata['account_id'] = $this->userSess->account_id;
                        $postdata['role_id'] = $this->userSess->account_type_id;
						
						if (File::exists($path.'/'.getGTZ('Y')))
						{
							if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($path.'/'.getGTZ('Y'));
							File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (File::exists($org_path.'/'.getGTZ('Y')))
						{
							if (!File::exists($org_path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($org_path.'/'.getGTZ('Y'));
							File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (in_array(strtolower($attachment->getClientOriginalExtension()), array('jpg', 'jpeg', 'png')))
						{
							$org_name = $attachment->getClientOriginalName();
							$ext = $attachment->getClientOriginalExtension();
							$file_extentions = strtolower($ext);
							$filtered_name = $this->slug($org_name);
							$file_name = explode('_', $filtered_name);
							$file_name = $file_name[0];
							$file_name = $file_name.'.'.$ext;							
							$filename = getGTZ('dmYHis').$file_name;
							
							$uploaded_file = $attachment->move($org_path.$folder_path, $filename);
                            $this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 200, 200);
							if ($uploaded_file) {								
								$postdata['details']['profile_img'] = $folder_path.$filename;
							}
						}
						else
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							$op['msg'] = 'Invalid Profile ImageFile Formate';
							return Response::json($op, $this->statusCode, $this->headers, $this->options);
						}
					}					
				}
				else
				{					
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = 'Profile Image Size is greater than 1 MB';
					return Response::json($op, $this->statusCode, $this->headers, $this->options);
				}				
			}
			if (!empty($postdata['shop_image']))
			{
				$attachment = $postdata['shop_image'];
				$size = $attachment->getSize();
				if ($size < 2049133)
				{
					$filename = '';
					if ($attachment != '')
					{
						$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';						
						$path = $this->config->get('path.SELLER.STORE_IMG_PATH.LOCAL');
                        $org_path = $this->config->get('path.SELLER.STORE_IMG_PATH.ORIGINAL');
                        $postdata['account_id'] = $this->userSess->account_id;
                        $postdata['role_id'] = $this->userSess->account_type_id;
						
						if (File::exists($path.'/'.getGTZ('Y')))
						{
							if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($path.'/'.getGTZ('Y'));
							File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (File::exists($org_path.'/'.getGTZ('Y')))
						{
							if (!File::exists($org_path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($org_path.'/'.getGTZ('Y'));
							File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (in_array(strtolower($attachment->getClientOriginalExtension()), array('jpg', 'jpeg', 'png')))
						{
							$org_name = $attachment->getClientOriginalName();
							$ext = $attachment->getClientOriginalExtension();
							$file_extentions = strtolower($ext);
							$filtered_name = $this->slug($org_name);
							$file_name = explode('_', $filtered_name);
							$file_name = $file_name[0];
							$file_name = $file_name.'.'.$ext;							
							$filename = getGTZ('dmYHis').$file_name;
							
							$uploaded_file = $attachment->move($org_path.$folder_path, $filename);
                            $this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 300, 300);
							if ($uploaded_file) {								
								$postdata['mrmst']['logo'] = $folder_path.$filename;
							}
						}
						else
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							$op['msg'] = 'Invalid Shop Image File Formate';
							return Response::json($op, $this->statusCode, $this->headers, $this->options);
						}
					}					
				}
				else
				{					
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = 'Shop Image Size is greater than 1 MB';
					return Response::json($op, $this->statusCode, $this->headers, $this->options);
				}				
			}
			if (!empty($postdata['banner_image']))
			{
				$attachment = $postdata['banner_image'];
				$size = $attachment->getSize();
				if ($size < 2049133)
				{
					$filename = '';
					if ($attachment != '')
					{
						$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';						
						$path = $this->config->get('path.SELLER.BANNER_IMG_PATH.LOCAL');
                        $org_path = $this->config->get('path.SELLER.BANNER_IMG_PATH.ORIGINAL');
                        $postdata['account_id'] = $this->userSess->account_id;
                        $postdata['role_id'] = $this->userSess->account_type_id;
						
						if (File::exists($path.'/'.getGTZ('Y')))
						{
							if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($path.'/'.getGTZ('Y'));
							File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (File::exists($org_path.'/'.getGTZ('Y')))
						{
							if (!File::exists($org_path.'/'.getGTZ('Y').'/'.getGTZ('m')))
							{
								File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
							}
						}
						else
						{
							File::makeDirectory($org_path.'/'.getGTZ('Y'));
							File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
						
						if (in_array(strtolower($attachment->getClientOriginalExtension()), array('jpg', 'jpeg', 'png')))
						{
							$org_name = $attachment->getClientOriginalName();
							$ext = $attachment->getClientOriginalExtension();
							$file_extentions = strtolower($ext);
							$filtered_name = $this->slug($org_name);
							$file_name = explode('_', $filtered_name);
							$file_name = $file_name[0];
							$file_name = $file_name.'.'.$ext;							
							$filename = getGTZ('dmYHis').$file_name;
							
							$uploaded_file = $attachment->move($org_path.$folder_path, $filename);
                            $this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 960, 200);
							if ($uploaded_file) {								
								$postdata['mrmst']['banner'] = $folder_path.$filename;
							}
						}
						else
						{
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
							$op['msg'] = 'Invalid Banner Image File Formate';
							return Response::json($op, $this->statusCode, $this->headers, $this->options);
						}
					}					
				}
				else
				{					
					$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
					$op['msg'] = 'Banner Image Size is greater than 1 MB';
					return Response::json($op, $this->statusCode, $this->headers, $this->options);
				}				
			}
			$res = $this->accObj->UpdateAccountInfo($postdata);			
			if ($res) 
			{
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = 'Account Updated Successfully';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}			
		} else {
			$data['account_id'] = $this->account_id;
			$data['acc_type_id'] = $this->account_type_id;
			$data['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.SELLER');
			//$data['fields'] = CommonNotifSettings::getHTMLValidation('seller.setup.bussiness-info');				
			$data['supplier_details'] = $this->commonObj->getSupplierAccountDetails($data);											
			$data['business_filing_status'] = $this->accObj->GetBussinessFilingStatus();											
			return View('seller.account_settings', $data);		
		}
    }
	
	public function store_list ()
    {
        $stores = $this->commonObj->get_stores_list($this->supplier_id);
        return Response::Json(!empty($stores) ? $stores : array());
    }
	
	/* Forgot password */
	public function forgotpwd ()
    {      
	    $wdata = [];
        $postdata = $this->request->all();
        $usrData = '';
		$this->session->forget('forgotPwd');       
        if (isset($postdata['uname']) && !empty($postdata['uname']))
        {
            $op = array('status'=>'', 'msg'=>'');
            if (strpos($postdata['uname'], '@'))
            {
                $regex = '/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/';
                if (!preg_match($regex, $postdata['uname']))
                {
                    $op['msg'] = 'Invalid Email ID';
					$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
                }
                $wdata['email'] = $postdata['uname'];             
            }
            else if (preg_match('/^[0-9]{10}$/', $postdata['uname']))
            {
                $wdata['mobile'] = $postdata['uname'];               
            }
            else
            {
                $op['msg'] = 'Please enter valid Mobile No./Email ID';
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');				
                return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
            }
        }        
        else
        {
            $op['msg'] = 'Parameter Missing....';
            $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
        
		$usrData = $this->suppObj->getAccount_info($wdata);          
		if (empty($usrData))
		{
			$data = [];
			if (isset($wdata['email']))
			{
				$data['email'] = $wdata['email'];
				// CommonNotifSettings::notify(null, 'retailer.forgotpwd_invalid_account', ['email'=>$wdata['email']], [], false);
				CommonNotifSettings::notify('FORGOT_PASSWORD_INVALID_ACCOUNT', null, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['code'=>$code, 'name'=>$usrData->full_name],true, false,false,true,false);	
			}			
			$op['msg'] = 'Account not found';
			$op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
			return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
		} 		
		if (($usrData->login_block != 1))
		{              
			$usrData->code = $code = rand(100000, 999999);			
			$usrData->hashcode = $resetKey = md5($code);
			$usrData->time_out = getGTZ(date('Y-m-d H:i:s', strtotime('+8 Hours')), 'Y-m-d H:i:s');
			$this->session->set('forgotPwd', $usrData);	
			$token = $this->session->getId().'.'.$usrData->hashcode;				
			$op['link']=$forgotpwd_link = url('seller/resetpwd-link/'.$token);
			$remove_forgotSess = url('seller/remove-resetpwd-sess/'.$token);
			CommonNotifSettings::notify('FORGOT_PASSWORD', $usrData->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['code'=>$code, 'forgotpwd_link'=>$forgotpwd_link, 'remove_forgotSess'=>$remove_forgotSess, 'name'=>$usrData->full_name],true, false,false,true,false);
			$email = maskEmail($usrData->email);
			$op['msg'] = trans('seller/account.forgotpwd.email_link', ['email'=>$email]);
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');				
			return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
		}
		else
		{
			$op['msg'] = 'Your Login Is Blocked. Please Contact Our Support Team.!';
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
		}
    }	
	
	/* Forgot Pwd Verification Link */
    public function verifyForgotpwdLink ($token)
    {   
        $op = $data = $usrdata = [];		
        if (!empty($token) && strpos($token, '.'))
        {   
            $access_token = explode('.', $token);
            $this->session->setId($access_token[0], true);
            if ($this->session->has('forgotPwd'))
            {		
                $usrdata = $this->session->get('forgotPwd');
                //$access_key = array_keys(array($usrdata));
				// echo"<pre>";print_r($usrdata->time_out .'<br>'. getGTZ(null, 'Y-m-d H:i:s'));exit;
                if (!empty($usrdata) && ($usrdata->hashcode == $access_token[1]) && ($usrdata->time_out >= getGTZ(null, 'Y-m-d H:i:s')))
                {   
			        $data['token'] = $token;
                    $data['pwd_resetfrm'] = true;
					$data['msg'] ='';
                }
                else
                {   
                    $data['pwd_resetfrm'] = false;
                    $data['msg'] = trans('seller/account.forgotpwd.session_expire');
                }
            }
            else
            { 
                $data['pwd_resetfrm'] = false;
                $data['msg'] = trans('seller/account.forgotpwd.session_expire');
            }
        }
        else
        {
            $data['pwd_resetfrm'] = false;
            $data['msg'] = trans('seller/account.forgotpwd.session_expire');
        }
		$data['rfields'] = CommonNotifSettings::getHTMLValidation('seller.pwdreset-link');	
		//echo"<pre>"; print_r($data['rfields']);exit;
        return view('seller.forgot_pwd', (array) $data);
    }
		
	/* Remove Forgot Pwd Session */
    public function removeForgotpwdSess ($token)
    {
        $data = [];
        if (!empty($token) && strpos($token, '.'))
        {
            $access_token = explode('.', $token);
            $this->session->setId($access_token[0], true);
            if ($this->session->has('forgotPwd'))
            {
                $this->session->forget('forgotPwd');
				return redirect()->route('seller.dashboard'); //or seller.dashboard
                //return view('user.home', (array) $data);
            }
            else
            {  
                $data['pwd_resetfrm'] = false;
                $data['msg'] = trans('seller/account.forgotpwd.session_already_expire');
				$data['rfields'] = CommonNotifSettings::getHTMLValidation('seller.pwdreset-link');
                return view('seller.forgot_pwd', (array) $data);
            }
        }
        else
        {
            $data['pwd_resetfrm'] = false;
            $data['msg'] = trans('seller/account.forgotpwd.session_already_expire');
        }
    }	
	
	/* Reset Password Using Link */
    public function pwdresetLink ()
    { 
        $op = $sdata = $postdata = [];
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            $access_token = explode('.', $postdata['token']);
            $this->session->setId($access_token[0], true);			
            if ($this->session->has('forgotPwd'))
            { 
                $usrData = $this->session->get('forgotPwd');
                if (md5($postdata['newpwd']) != $usrData->pass_key)
                {
                    if ($this->suppObj->reset_pwd($usrData->account_id, $postdata['newpwd']))
                    {     
                        //$op['url'] = url('seller/login');
						$this->session->forget('forgotPwd'); 
                        $op['msg'] = trans('seller/account.forgotpwd.reset_pwd_success');
						CommonNotifSettings::notify('RESET_PASSWORD', $usrData->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['name'=>$usrData->full_name],true,false,false,true,false);                         
                        $this->statusCode = $this->config->get('httperr.SUCCESS');
						$this->commonObj->logoutAllDevices($usrData->account_id);
                    }
                    else
                    {
                        $op['msg'] = trans('seller/account.forgotpwd.reset_pwd_fail');
                        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    }
                }
                else 
                { 
                    $op['msg'] = trans('seller/account.forgotpwd.new_pwd_is_same_as_old');
                    $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
            else
            { 
                $op['msg'] = trans('seller/account.forgotpwd.reset_sess_exp');
                $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('seller/account.forgotpwd.parammiss');
            $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

    /* public function forgotPassword ()
    {
        $data = ['forgot_password'=>true];
        $data['lfields'] = CommonNotifSettings::getHTMLValidation('api.v1.supplier.login');
        $data['ffields'] = CommonNotifSettings::getHTMLValidation('api.v1.supplier.forgot-password');
        return View::make('supplier.templates.login', $data);
    }
 
    public function forgotpwd_old ()
    {
        $op = $wdata = [];
        $postdata = $this->request->all();
        if (strpos($postdata['uname'], '@') && preg_match('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/', $postdata['uname']))
        {
            $wdata['email'] = $postdata['uname'];
        }
        else
        {
            $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('user/auth.forgotpwd.comm_error');
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }				
        if (!empty($wdata))
        {
            $wdata['system_role_id'] = $this->config->get('constants.ACCOUNT.TYPE.USER');
            $usrData = $this->suppObj->getAccount_info($wdata);
            if (!empty($usrData))
            {
                if (($usrData->login_block != 1))
                {     
                    $op['code'] = $usrData->verify_code = rand(100000, 999999);
                    $op['hash_code'] = $resetKey = md5($usrData->verify_code);
                    $usrData->time_out = date('Y-m-d H:i:s', strtotime('+8 Hours'));
                    $time_out = $usrData->time_out = getGTZ($usrData->time_out, 'Y-m-d H:i:s');
                    $this->session->set('forgotpwd', [$resetKey=>$usrData]);
                    $op['token'] = $token = $this->session->getId().'.'.$resetKey;
                    //$forgotpwd_link = route('user.resetpwd-link', ['hash_code'=>$token]);
                    $forgotpwd_link = url('resetpwd-link/'.$token);
                    //$remove_forgotSess = route('user.resetpwdsess', ['hash_code'=>$token]);
                    $remove_forgotSess = url('remove-resetpwd-sess/'.$token);
                    //CommonNotifSettings::notify($usrData->account_id, 'forgotpwd_verifycode', ['code'=>$usrData->verify_code, 'forgotpwd_link'=>$forgotpwd_link, 'remove_forgotSess'=>$remove_forgotSess]);
                    //$email = maskEmail($usrData->email);
                    //$op['msg'] = trans('user/auth.forgotpwd.acc_resetlink', ['email'=>$email]);
                    $this->statusCode = $this->config->get('httperr.SUCCESS');	
                }
                else
                {
                    $data = [];
				    if (isset($wdata['email']))
				    {
				        $data['email'] = $wdata['email'];
				    }
				    CommonLib::notify(null, 'customer_promotional', ['code'=>$usrData->verify_code], $data); 
                    $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    $op['msg'] = trans('user/auth.forgotpwd.acc_blocked');
                }				
            }
            else
            {
                $data = [];
                if (isset($wdata['email']))
                {
                    $data['email'] = $wdata['email'];
                }
                CommonLib::notify(null, 'customer_promotional', [], $data);
                $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('user/auth.forgotpwd.acc_notfound');
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    } 
		
	public function forgotpwd_old2 ()
    {
        $postdata['send_to'] = '';
        $postdata = $this->request->all();
        $usrData = '';
        $op['status'] = 'error';
		$this->session->forget('forgotPwd');
        $wdata = [];
        // 1-email 2-mobile 
        if (isset($postdata['uname']) && !empty($postdata['uname']))
        {
            $op = array('status'=>'', 'msg'=>'');
            if (strpos($postdata['uname'], '@'))
            {
                $regex = '/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/';
                if (!preg_match($regex, $postdata['uname']))
                {
                    $op['status'] = 400;
                    $op['msg'] = 'Invalid Email ID';
                    return $this->response->json($op, $status = 400, $this->headers, $this->options);
                }
                $wdata['email'] = $postdata['uname'];
                $postdata['send_to'] = 1;
            }
            else if (preg_match('/^[0-9]{10}$/', $postdata['uname']))
            {
                $wdata['mobile'] = $postdata['uname'];
                $postdata['send_to'] = 2;
            }
            else
            {
                $op['msg'] = 'Please enter valid Mobile No./Email ID';
                $op['status'] = 422;
                return $this->response->json($op, $status = 422, $this->headers, $this->options);
            }
        }
        else if ($this->request->has('user_id'))
        {
            if (!is_numeric($this->request->has('user_id')))
            {
                $op['msg'] = trans('');
                return $this->response->json($op, $status = 400, $this->headers, $this->options);
            }
        }
        else
        {
            $op['status'] = 'error';
            $op['msg'] = 'Parameter Missing....';
            $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
            return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
        }
        if ($op['status'] != 'error')
        {
        	$usrData = $this->suppObj->getAccount_info($wdata);          
            if (empty($usrData))
            {
                $data = [];
                if (isset($wdata['email']))
                {
                    $data['email'] = $wdata['email'];
                    // CommonNotifSettings::notify(null, 'retailer.forgotpwd_invalid_account', ['email'=>$wdata['email']], [], false);
					CommonNotifSettings::notify('FORGOT_PASSWORD_INVALID_ACCOUNT', null, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['code'=>$code, 'name'=>$usrData->full_name],true, false,false,true,false);	
                }
                $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
                $op['msg'] = 'Account not found';
                return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
            }
            $op['details']['mobile']['number'] = maskMobile($usrData->mobile);
            $op['details']['mobile']['label'] = 'Send code via SMS';
            $op['details']['mobile']['value'] = 2;
            $op['details']['email']['id'] = maskEmail($usrData->email);
            $op['details']['email']['label'] = 'Send code via email';
            $op['details']['email']['value'] = 1;
            if (($usrData->login_block != 1))
            {
                $usrData->send_to = $postdata ['send_to'];
                $usrData->opt_mobile = $op['details']['mobile']['number'];
                $usrData->opt_email = $op['details']['email']['id'];
				$usrData->time_out = getGTZ(date('Y-m-d H:i:s', strtotime('+8 Hours')), 'Y-m-d H:i:s');
                $this->session->set('forgotPwd', $usrData);
                $op['token'] = $this->session->getId().'.'.md5($usrData->account_id);
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = 'Select your option to receive verification';
                return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
            }
            else
            {
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = 'Your Login Is Blocked. Please Contact Our Support Team.!';
                return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
            }
        }
    } 
	
	public function forgot_opt ()
    {  
        if ($token = $this->request->header('token'))
        {  
            $tokens = explode('.', $token);
            $this->session->setId($tokens[0], true);			
            if ($this->session->has('forgotPwd'))
            {   		
                $option = $this->request->get('opt');
                $usrData = $this->session->get('forgotPwd');
                $op['code'] = $usrData->code = $code = rand(100000, 999999);			
				$op['hash_code'] = $usrData->hashcode = $resetKey = md5($code);
                $this->session->set('forgotPwd', $usrData);	
                //$op['token'] = $token;
				$op['token'] = $token = $tokens[0].'.'.$resetKey;
				$op['link']=$forgotpwd_link = url('seller/resetpwd-link/'.$token);
				$remove_forgotSess = url('seller/remove-resetpwd-sess/'.$token);
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                if ($option == 1)
                {  
                    // For E-mail
                    $op['msg'] = trans('seller/account.forgotpwd.email_otp', ['email'=>$usrData->opt_email]);
					CommonNotifSettings::notify('FORGOT_PASSWORD', $usrData->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['code'=>$code, 'forgotpwd_link'=>$forgotpwd_link, 'remove_forgotSess'=>$remove_forgotSess, 'name'=>$usrData->full_name],true, false,false,true,false);
                }
                else if ($option == 2)
                {   
                    // For Mobile
					$op['msg'] = trans('seller/account.forgotpwd.mobile_otp', ['mobile'=>$usrData->opt_mobile]);  
					CommonNotifSettings::notify('FORGOT_PASSWORD', $usrData->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['code'=>$code, 'site_name'=>'Virob'],false, true,false,true,false);	                   
                }
            }
            else
            {
                $op['msg'] = trans('seller/account.forgotpwd.invalid_token');
                $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('seller/account.forgotpwd.reset_sess_exp');
            $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    } 	
	
	public function resetpwd ()
    {	  
        $op = [];
        $op['status'] = 'error';
        $resettoken = $this->request->header('token');
        if (!empty($resettoken) && strpos($resettoken, '.'))
        {
            $access_token = explode('.', $resettoken);
            $reqSess = $access_token[0];
            $this->session->setId($reqSess, true);
            if ($this->session->has('forgotPwd'))
            {
                $usrData = $this->session->get('forgotPwd');				
                if ($this->request->code == $usrData->code)
                {
                    $white_space = preg_match('/\s/', $this->request->newpwd);
                    if ($white_space > 0)
                    {
                        $op['msg'] = trans('seller/account.forgotpwd.remove_whitespace');
                    }
                    else
                    {
                        if ($res = $this->suppObj->reset_pwd($usrData->account_id, $this->request->newpwd))
                        {
					        CommonNotifSettings::notify('RESET_PASSWORD', $usrData->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['name'=>$usrData->full_name],true, false,false,true,false);                         
                            $op['msg'] = trans('seller/account.forgotpwd.reset_pwd_success');
                            $this->statusCode = $this->config->get('httperr.SUCCESS');
                            $this->session->forget('forgotPwd');
                        }
                        else
                        {
                            $op['msg'] = trans('seller/account.forgotpwd.reset_pwd_fail');
                            $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                        }
                    }
                }
                else
                {
                    $op['msg'] = trans('seller/account.forgotpwd.reset_code_nomatch');
                    $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
            else
            {
                $op['msg'] = trans('seller/account.forgotpwd.reset_sess_exp');
                $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('seller/account.forgotpwd.tokenmiss');
            $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    } */	
		
    public function checkEmailVerification ($activation_key)
    {		
		$res = $this->commonObj->updateEmailVerification($activation_key);		
        if ($res)
        {			
            $data['msg'] = Lang::get('general.your_email_id_is_verified');
        }
        else
        {
            $data['msg'] = Lang::get('general.invalid_verification_code');
        }
        return View('seller.email_verification_message', $data);
    }

    public function verification ()
    {
        return View('seller.verification.verification');
    }

    public function doc_list ()
    {
        $data = $this->suppObj->doc_list();
        if (!empty($data))
        {
            return $data;
        }
        else
        {
            return false;
        }
    }   
	
	public function logout ()
    {
        $this->response = [];
        if (Config::has('data.user'))
        {
            $this->memberObj = new Memberauth($this->commonObj);
            if ($this->memberObj->logoutUser($this->account_id, $this->device_log_id))
            {
                $this->statusCode = 200;
                $this->response['msg'] = Lang::get('general.logout_success');
            }
            else
            {
                $this->response['msg'] = Lang::get('general.something_went_wrong');
            }
        }
        return Redirect::to('seller/login');
    }

    public function view_profile ()
    {
        $data = [];
        $data['details'] = $this->suppObj->view_profile($this->account_id);
        return View::make('supplier.view_profile.view_profile', $data);
    }

	public function change_email ()
    {
        return View('seller.change_email');
    }

    public function old_password_check ()
    {
        $postdata = Input::all();
        $data['password_check'] = $this->suppObj->old_password_check($this->account_id);
        if ($data['password_check']->login_password == md5($postdata['oldpassword']))
        {
            $detail = Lang::get('general.correct_password');
            return 'true';
        }
        else
        {
            return 'false';
        }
    }

   

    public function get_store_details ($store_code)
    {
        $op = array();
        if ($store_code && !empty($store_code))
        {
            $post['store_code'] = $store_code;
            $post['supplier_id'] = $this->supplier_id;
            if ($res = $this->suppObj->get_stores_list($post))
            {
                $data['store_details'] = $res;
                $op['data'] = $res;
            }
        }
        return Response::json($op);
    }

    public function stores_management ()
    {
        $data = $filter = array();
        $submit = '';
        $post = Input::all();
        if (!empty($post))
        {
            $filter['search_text'] = (isset($post['search_text']) && !empty($post['search_text'])) ? $post['search_text'] : '';
            $filter['filterTerms'] = (isset($post['filterTerms']) && !empty($post['filterTerms'])) ? $post['filterTerms'] : '';
            $filter['start_date'] = (isset($post['start_date']) && !empty($post['start_date'])) ? $post['start_date'] : '';
            $filter['end_date'] = (isset($post['end_date']) && !empty($post['end_date'])) ? $post['end_date'] : '';
        }
        if (!empty($post) && !empty($data) && isset($post['submit']) && $post['submit'] == 'Export')
        {
            $res['get_stores_list'] = $this->suppObj->get_stores_list($data);
            $output = View::make('supplier.stores.view_stores_list_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Stores_'.date('Y-m-d').'.xls',
                'Content-Transfer-Encoding'=>' binary'
            );
            return Response::make($output, 200, $headers);
        }
        else if (!empty($post) && !empty($data) && isset($post['submit']) && $post['submit'] == 'Print')
        {
            $res['get_stores_list'] = $this->suppObj->get_stores_list($data);
            return View::make('supplier.stores.view_stores_list_print', $res);
        }
        else
        {			
            $data['fields'] = CommonNotifSettings::getHTMLValidation('seller.stores.save');
			//echo '<pre>';print_r($data);exit;
            $data['days'] = $this->commonObj->getWorkingDays();
            return View('seller.stores.stores_list', $data);
        }
    }

    public function customer_management_list ()
    {
        $op = array();
        $data = array();
        $postdata = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        if (!empty($postdata))
        {
            $data['search_term'] = $postdata['search_term'];
            $data['from'] = $postdata['from'];
            $data['to'] = $postdata['to'];
        }
        if (Request::ajax())
        {
            $ajaxdata['data'] = [];
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->suppObj->customer_management_list($data, true);
            if (!empty($ajaxdata['recordsFiltered']))
            {
                $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                $data['order'] = $postdata['order'][0]['dir'];
                $data['search_term'] = $postdata['search_term'];
                $ajaxdata['data'] = $this->suppObj->customer_management_list($data);
            }
            return Response::json($ajaxdata);
        }
        else
        {
            $data['user_list'] = $this->suppObj->customer_management_list();
            return View::make('supplier.user_account.customer_management_list', $data);
        }
    }

    public function change_status ()
    {
        $postdata = Input::all();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            if ($postdata['status'] == Config::get('constants.OFF'))
            {
                $data = '<span class="label label-success">Active</span>';
            }
            else
            {
                $data = '<span class="label label-danger">Blocked</span>';
            }
            $op['status_msg'] = $data;
            $result = $this->suppObj->change_customer_status($postdata);
            if (!empty($result))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.customer.status.'.$postdata['status']);
            }
        }
        return Response::json($op);
    }

    public function change_customer_pwd ()
    {
        $postdata = Input::all();
        $update = array();
        if (!empty($postdata))
        {
            $res['password_check'] = $this->user->old_password_check($postdata);
            if ($res['password_check']->pass_key != md5($postdata['enter_new_pwd']))
            {
                $data['pass_key'] = $postdata['con_new_pwd'];
                $wdata['account_id'] = $postdata['account_id'];
                $update = $this->suppObj->update_new_password($data, $wdata);
            }
            else
            {
                $update['status'] = 'ERR';
                $update['msg'] = Lang::get('general.please_enter_new_pssword');
            }
            return Response::Json($update);
        }
    }

    public function signUp ()
    {	
		$data = [];
	    $iso2 = $this->commonObj->getIpCountry();
	    $country = $this->commonObj->getCountrybycode($iso2);
		if(!empty($country)){
			$data['flag'] = asset('assets/flags/'.$iso2.'.png');
			$data['country_id'] =$country->country_id;
			$data['country'] =$country->country;
			$data['phonecode'] =$country->phonecode;
			//$data['mobile_validation'] = $country->mobile_validation;
			$data['mobile_validation'] = str_replace('$/','',(str_replace('/^','',$country->mobile_validation)));
		}
	 	if (\Request::ajax()) {
			$data = $this->request->all();		
			$this->response = [];				
			//$res = $this->suppObj->saveSupplierRegister($data);	
			$this->session->set('regvalues',$data);	
			$this->response['url'] = 'seller/mobile-verification';
			$this->statusCode = 308;
			return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
		} else {
			$data['fields'] = CommonNotifSettings::getHTMLValidation('seller.sign-up');			
			return view('seller.sign_up', $data);
		}
    }

   /*  public function mobileVerification ()
    {
        $data = [];
        return View::make('supplier.mobile_verification', $data);
    } */

    public function signUpMobileVerification ()
    {	
        if($this->session->has('regvalues'))
        {	
	        $svalue = $this->session->get('regvalues');
			$data 	= ['verify_mobile'=>true, 'full_name'=>$svalue['account_details']['firstname'].' '.$svalue['account_details']['lastname']];		
			$data['fields'] = CommonNotifSettings::getHTMLValidation('api.v1.seller.sign-up');	
			//$data['userDetails'] = $this->commonObj->getSupplierAccountDetails(['post_type'=>$this->acc_type_id,'account_id'=>$this->account_id]);
			return View('seller.sign_up', $data);
		}else{
			$data = ['login'=>true];
			$data['lfields'] = CommonNotifSettings::getHTMLValidation('seller.login');
			$data['ffields'] = CommonNotifSettings::getHTMLValidation('seller.forgot-password');				
			return View('seller.login', $data);
		}
    }
	
	
	public function verifyMobile ()
    {		
        $this->response = [];	
        if($this->session->has('regvalues'))
        {
			$data = $this->session->get('regvalues');
	        //if ($code = $this->commonObj->updateAccountVerificationCode($this->device_log_id))
			$code = rand(100000, 999999);
		    $res = $this->session->set('reg_mobile_verify',$code);
            {				
		       $res = CommonNotifSettings::notify('SELLER_SIGNUP.VERIFY_MOBILE',0,config::get('constants.ACCOUNT_TYPE.SELLER'), ['code'=>$code, 'name'=>$data['account_details']['firstname'], 'uname'=>$data['account_details']['firstname'].' '.$data['account_details']['lastname'],'mobile'=>$data['account_mst']['mobile']], false, true, false, true);			
			    $this->statusCode = 200;
                $this->response['code'] = $code;
				$mobile = str_repeat("*", strlen($data['account_mst']['mobile'])-4).substr($data['account_mst']['mobile'], -4);
                $this->response['msg'] = trans('general.verification_code_has_been_sent_to_yo_mobile_no',['mobile'=>$mobile]);
            }
        }
        else
        {
            $this->response['msg'] = trans('general.mobile_is_already_verified');
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }
	
	public function change_reg_mobile ()
    {		
        $this->response = [];	
		$postdata = $this->request->all();
	    if($this->session->has('regvalues'))
        {
			$data = $this->session->get('regvalues');
	    	$data['account_mst']['mobile'] = $postdata['mobile_no'];
			$this->session->set('regvalues',$data);
			$code = rand(100000, 999999);
			$this->session->set('reg_mobile_verify',$code);
		 		$res = CommonNotifSettings::notify('VERIFY_MOBILE',0,config::get('constants.ACCOUNT_TYPE.SELLER'), ['code'=>$code, 'name'=>$data['account_details']['firstname'], 'uname'=>$data['account_details']['firstname'].' '.$data['account_details']['lastname'],'mobile'=>$data['account_mst']['mobile']], false, true, false, true);			
			    $this->statusCode = 200;
                $this->response['code'] = $code;
                $this->response['status'] = 'ok';
				$mobile = str_repeat("*", strlen($data['account_mst']['mobile'])-4).substr($data['account_mst']['mobile'], -4);
                $this->response['msg'] = trans('general.verification_code_has_been_sent_to_yo_mobile_no',['mobile'=>$mobile]);
            
        }
        else
        {
			 $this->response['status'] = 'error';
            $this->response['msg'] = trans('general.mobile_is_already_verified');
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }
	
	public function checkMobileVerification ()
    {
        $this->response = [];
        $post = Input::all();	
	    if($this->session->has('regvalues'))
		{
			$data = $this->session->get('regvalues'); 
			if($this->session->has('reg_mobile_verify') && ($this->session->get('reg_mobile_verify') == $post['verification_code'])){
				$account_id = $this->suppObj->saveSupplierRegister($data);
				$this->session->forget('regvalues');
				if($account_id && $this->commonObj->update_mobile_verification($account_id))
				{
					$this->statusCode = 308;
					$this->sentEmail_verification();
					$this->response['msg'] = Lang::get('general.verification_code_has_been_sent_to_your_email_id'); 
					$this->response['url'] = route('seller.verify-email');
					$this->response['verify_email'] = 1;
				}else
				{
					$this->response['msg'] = Lang::get('general.no_change');
				}
			}else{
				$this->response['msg'] = trans('general.invalid_verification_code_for_mobile');
			}
		}
		else
		{			
			$this->response['msg'] = trans('general.invalid_verification_code_for_mobile');
		}
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }
	
	public function sentEmail_verification ()
    {
        $this->response = [];
	    if (!empty($this->email) && !$this->is_email_verified)
        {
            $activation_key = $this->commonstObj->getAccountActivationKey($this->account_id);			
			$this->session->forget('email_verification');
			$data['email'] = $this->userSess->email;
			$data['account_id'] = $this->userSess->account_id;
			$data['supplier_id'] = $this->userSess->supplier_id;
			$data['full_name'] = $this->userSess->full_name;	
			$op['code'] = $data['code'] = rand(100000, 999999);
			$op['hash_code'] = $data['hash_code'] = md5($data['code']);			
			$this->session->set('email_verification', $data);		 //changeNewEmailIDConfirm		
			$token = $this->session->getId().'.'.$data['hash_code'];
			$op['link'] = $verify_link = url('seller/verifyNewEmailLink/'.$token);
			CommonNotifSettings::notify('SUPPLIER_VERIFY_EMAIL',$data['account_id'], Config::get('constants.ACCOUNT_TYPE.SELLER'),['code'=>$data['code'], 'email_verify_link'=>$verify_link, 'email'=>$data['email'], 'full_name'=>$data['full_name']],
			true, false, false, true, false); 	
			$this->statusCode = $this->config->get('httperr.SUCCESS');
			$email = maskEmail($data['email']);
			$op['msg'] = trans('seller/account.code_sent_to_email', ['email'=>$email]);	
			
			/* $code = rand(100000, 999999);	
			$this->session->set('email_verification',$code);
            if ($code)
            {						
				CommonNotifSettings::notify('SUPPLIER_VERIFY_EMAIL', $this->supplier_id, $this->account_type_id, [], true, false, false, true);						
				$this->response['code'] = $code;	
                $this->statusCode = 200;
                $this->response['msg'] = trans('general.verification_code_has_been_sent_to_your_email_id');                
            } */
			return true;
        }
        return false;		
    }
	
	public function sent_mailverification ()
    { 
        $op = $data = $completedSteps =[];      
		if (!empty($this->email))
        {         
	        if(!$this->is_email_verified){
				$this->session->forget('email_verification');
				$data['email'] = $this->userSess->email;
				$data['account_id'] = $this->userSess->account_id;
				$data['supplier_id'] = $this->userSess->supplier_id;
				$data['full_name'] = $this->userSess->full_name;	
				$op['code'] = $data['code'] = rand(100000, 999999);
				$op['hash_code'] = $data['hash_code'] = md5($data['code']);			
				$this->session->set('email_verification', $data);		
				$token = $this->session->getId().'.'.$data['hash_code'];
				$op['link'] = $verify_link = url('seller/verifyNewEmailLink/'.$token);			
				$completedSteps = $this->accObj->CompletedSteps(['supplier_id'=>$this->supplier_id]);
				$current_step = Config::get('constants.ACCOUNT_CREATION_STEPS.EMAIL_VERIFICATION');			
				$view = (in_array($current_step, explode(',', $completedSteps))) ? 'CHANGE_NEW_EMAIL_OTP': 'SUPPLIER_VERIFY_EMAIL';	
				CommonNotifSettings::notify($view,$data['account_id'], Config::get('constants.ACCOUNT_TYPE.SELLER'),['code'=>$data['code'], 'email_verify_link'=>$verify_link, 'email'=>$data['email'], 'full_name'=>$data['full_name']],true, false, false, true, false);			
				$this->statusCode = $this->config->get('httperr.SUCCESS');			
				$email = maskEmail($data['email']);
				$op['msg'] = trans('seller/account.code_sent_to_email', ['email'=>$email]);	
			}else {				
				$op['msg'] = 'Email address already verified.';
				$op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
			}		
        }else {		    
		    $op['msg'] = 'Parameter Missing..';
		    $op['status'] = $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
		}     
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	/* public function new_Email_verification ()
    {
        $op	= $data = $postdata = []; 
		$postdata = $this->request->all();
	    if (!empty($postdata['email']) && !empty($this->userSess->is_email_verified))
        { 
			$data['old_email'] = $this->userSess->email;
			$data['new_email'] = $postdata['email'];
			$data['account_id'] = $this->userSess->account_id;				
			$data['full_name']=$this->userSess->full_name;
			$op['code'] = $data['code'] = rand(100000, 999999);
			$op['hash_code'] = $data['hash_code'] = md5($data['code']);		
			$this->session->set('changeEmailID', $data);				
			$token = $this->session->getId().'.'.$op['hash_code'];
			$op['link'] = $verify_link = url('seller/verifyEmailLink/'.$token);			 
			CommonNotifSettings::notify('CHANGE_EMAIL_OTP',$this->userSess->account_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), ['code'=>$data['code'],'email_verify_link'=>$verify_link, 'email'=>$data['old_email'], 'full_name'=>$data['full_name']],
			true, false, false, true, false);
			$this->statusCode = $this->config->get('httperr.SUCCESS');
			$op['msg'] = trans('seller/account.code_sent_to_email', ['email'=>maskEmail($data['old_email'])]);	
			
		} else if(!empty($postdata['email']) && empty($this->userSess->is_email_verified)) 
		{
			$this->session->forget('email_verification');
			$data['email'] = $postdata['email'];
			$data['account_id'] = $this->userSess->account_id;
			$data['full_name'] = $this->userSess->full_name;	
			$op['code'] = $data['code'] = rand(100000, 999999);
			$op['hash_code'] = $data['hash_code'] = md5($data['code']);			
			$this->session->set('email_verification', $data);		 //changeNewEmailIDConfirm		
			$token = $this->session->getId().'.'.$data['hash_code'];
			$op['link'] = $verify_link = url('seller/verifyNewEmailLink/'.$token);
			CommonNotifSettings::notify('SUPPLIER_VERIFY_EMAIL',$data['account_id'], Config::get('constants.ACCOUNT_TYPE.SELLER'),['code'=>$data['code'], 'email_verify_link'=>$verify_link, 'email'=>$data['email'], 'full_name'=>$data['full_name']],
			true, false, false, true, false); 	
			$this->statusCode = $this->config->get('httperr.SUCCESS');
			$email = maskEmail($data['email']);
			$op['msg'] = trans('seller/account.code_sent_to_email', ['email'=>$email]);						
		}else {
		    $op['msg'] = 'Parameter Missing..';
			$op['status']  = $this->statusCode = config('httperr.UN_PROCESSABLE');			
		}
		         
		// $code = rand(100000, 999999);	
		// $this->session->set('change_acc_emal',['new_email'=>$postdata['email'],'email_verification'=>$code]);
		// $res= $this->suppObj->change_acc_email(['account_id'=>$this->account_id,'email'=>$postdata['email']]);
		// if(!empty($res) && $code)
		// {						
			// CommonNotifSettings::notify('SUPPLIER_NEW_EMAIL_VERIFY', null, $this->account_type_id, ['email'=>$postdata['email'],'code'=>$code,'name'=>$this->full_name], true, false, false, true);						
			// $this->response['code'] 	= $code;	
			// $this->statusCode 			= 200;
			// $this->response['msg'] 		= trans('general.verification_code_has_been_sent_to_your_email_id');                
			// $this->response['email_id'] 		= $postdata['email'];                
		// }   
        // return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
		return Response::json($op, $this->statusCode, $this->headers, $this->options);
    } */
	
	public function confirm_email_verification ()
    {
	    $op = $postdata = [];
		$postdata = $this->request->all();		
		if(!empty($postdata['verification_code']) && $this->session->has('email_verification'))
		{
			$scode = $this->session->get('email_verification');
			if($postdata['verification_code'] == $scode){			
				$res   = $this->commonObj->verifyEmail($this->account_id);
				if (!empty($res)) 
				{
			        //$current_step  = ($this->userSess->service_type != config('constants.SERVICE_TYPE.ONLINE')) ? config('constants.ACCOUNT_CREATION_STEPS.EMAIL_VERIFICATION') : config('constants.ACCOUNT_CREATION_STEPS.MANAGE_CASHBACK');
					$op['url'] = $this->accObj->UpdateCompletedSteps(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.EMAIL_VERIFICATION') ,
																	'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SELLER'),
																	'supplier_id'=>$this->supplier_id,
																	'account_id'=>$this->account_id]); 														
					$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
					$op['msg'] = 'Email Verified Successfully';
					return Response::json($op, $this->statusCode, $this->headers, $this->options);
				}
				$this->statusCode = config('httperr.SUCCESS');
                $op['msg'] = trans('general.email_verified');
			}else{
				$this->statusCode = config('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('general.invalid_otp');   
			}
		}
		return Response::json($op, $this->statusCode, $this->headers, $this->options);
	}
	
    public function signUpVerify ()
    {
        $op = array();
        $status = 422;
        $data = Input::all();
        $validator = Validator::make($data, Config::get('validations.SUPPLIER_SIGNUP.RULES'), Config::get('validations.SUPPLIER_SIGNUP.MESSAGES'));
        $data['account_mst'] = [];
        if (!$validator->fails())
        {
            Session::put('supplierSignUp', $data);
            $status = 200;
            $op['url'] = $this->suppObj->getNextStep(['current_step'=>Config::get('constants.ACCOUNT_CREATION_STEPS.START')]);
            $op['msg'] = '';
        }
        else
        {
            $op['error'] = $validator->messages(true);
        }
        return Response::json($op, $status);
    }

    public function BussinessInfo ()
    {
        $data = [];
        if (Session::has('supplierSignUp') || isset($this->account_id))
        {
            if (Session::has('supplierSignUp'))
            {				
                $data = Session::get('supplierSignUp');
                $data['business_types'] = $this->commonObj->getBusinessTypes();
                $data['fields'] = CommonNotifSettings::getHTMLValidation('seller.setup.bussiness-info');
                return View::make('supplier.account_details', $data);
            }
            elseif (isset($this->account_id))
            {					
                $data['account_id'] = $this->account_id;
                $data['acc_type_id'] = $this->account_type_id;
                $data['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.SELLER');
                $data['fields'] = CommonNotifSettings::getHTMLValidation('seller.setup.bussiness-info');				
                $data['supplier_account_details'] = $this->commonObj->getSupplierAccountDetails($data);								
                $data['business_types'] = $this->commonObj->getBusinessTypes();												
                return View('seller.bussiness_info', $data);
            }
        }
    }
	
	public function AccountInfo ()
    {
        $data = [];
        if (isset($this->account_id))
        {
            				
			$data['account_id'] = $this->account_id;
			$data['acc_type_id'] = $this->account_type_id;
			$data['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.SELLER');
			$data['fields'] = CommonNotifSettings::getHTMLValidation('seller.setup.account-info');			
			//echo '<pre>';print_r($data);exit;	
			$data['supplier_account_details'] = $this->commonObj->getSupplierAccountDetails($data);											
			return View('seller.account_info', $data);
            
        }
    }

    public function storeInfo ()
    {		
        $data = array();
        $data['account_id'] = $this->account_id;
		$data['acc_type_id'] = $this->account_type_id;
		$data['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.STORE');
        $data['supplier_account_details'] = $this->commonObj->getSupplierAccountDetails($data);
        $data['fields'] = CommonNotifSettings::getHTMLValidation('seller.setup.store-info');			
		//echo '<pre>';print_r($data);exit;
        return View('seller.store_info', $data);        
    }

    public function kycUpdate ()
    {
        $data = array();
        $data['account_id'] = $this->account_id;
        $data['supplier_id'] = $this->supplier_id;
        $data['acc_type_id'] = $this->account_type_id;
        $data['kyc_verifiacation'] = $this->suppObj->getKycVerification($data);		
        $data['document_types'] = $this->commonObj->getDocumentTypes(['proof_type'=>1]);		
        $data['fields'] = CommonNotifSettings::getHTMLValidation('seller.setup.update-kyc');		
		//echo '<pre>';print_r($data);exit;
        return View('seller.kyc_verification', $data);
    }

    public function storeBanking ()
    {
        $data = array();
        $data['account_id'] = $this->account_id;		
        $data['account_types'] = $this->commonObj->getBankingAccountTypes();				
        $data['states'] = $this->commonObj->get_state_list(['country_id'=>$this->country_id]);		
        $data['fields'] = CommonNotifSettings::getHTMLValidation('seller.setup.store-banking');		
        $data['payment_settings'] = $this->suppObj->getPaymentSettings($this->supplier_id); 				
        return View('seller.store_banking', $data);
    }

    public function perferences ()
    {
        $data = [];
        $data['supplier_id'] = $this->supplier_id;
        $data['preferences'] = $this->suppObj->getSupplierPreferences($data);
        return View::make('supplier.preferences', $data);
    }

    public function savePerferences ()
    {
        $op = array();
        $status = 422;
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        $validator = Validator::make($data, Config::get('validations.SUPPLIER_PREFERENCES.RULES'), Config::get('validations.SUPPLIER_PREFERENCES.MESSAGES'));
        $data['supplier_id'] = $this->supplier_id;
        $data['account_id'] = $this->account_id;
        if (!$validator->fails())
        {
            if ($this->suppObj->savePerferences($data))
            {
                $op['msg'] = Lang::get('general.updated_successfully');
                $status = 200;
            }
        }
        else
        {
            $op['error'] = $validator->messages(true);
        }
        return Response::json($op, $status);
    }

    public function profileSetup ()
    {		
        return View('seller.profile_setup');
    }

}
