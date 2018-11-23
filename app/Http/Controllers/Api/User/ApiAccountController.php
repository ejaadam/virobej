<?php
namespace App\Http\Controllers\Api\User;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\User\UserApiBaseController;
use App\Models\Api\User\AffModel;
use App\Models\Api\User\StoreModel;
//use App\Models\CommonModel;
use App\Models\Api\User\AccountModel;
use App\Models\Api\User\ProductsModel;
use Config;
use Session;
use App\Helpers\CommonNotifSettings;

class ApiAccountController extends UserApiBaseController
{
    
    public function __construct ()
    {
        parent::__construct();
		$this->apiaffObj = new AffModel();
		//$this->commonObj = new CommonModel();
		$this->accountObj = new AccountModel();
		$this->productsObj = new ProductsModel();
		$this->storeObj = new StoreModel($this->accountObj);
    }	
	
	public function dashboard ()
    {
        $op = [];
        $data = [];      		
		if (!empty($this->request->header('usrtoken'))) {
			if (isset($this->geo->browse->country_id) && !empty($this->geo->browse->country_id))
			{
				$data['country_id'] = $this->geo->browse->country_id;            
				$op['categories'] = $this->storeObj->storeCategories($data, true);
				$data['currency_id'] = $this->geo->browse->currency_id;  				
				$op['products'] = $this->productsObj->dashboard_products($data, true);				
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
			}
			else
			{
				$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
				$op['msg'] = trans('general.set_your_location');
			}
		} else {
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$op['msg'] = 'Set the user token';
		}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function hideMobileNumber ($mobile, $length = 4)
    {
        return str_repeat('*', strlen($mobile) - $length).substr($mobile, -1 * $length);
    }
	
	public function Account_Signup ()
    {  
        $op = [];
		$this->session->forget('SIGNUP');
        $op['status'] = $this->statusCode = Config('httperr.UN_PROCESSABLE');
		$postdata = $this->request->all();		
		if ($postdata) 
		{				
			$postdata['uname'] = strtoupper($postdata['full_name'][0].$postdata['email'][0].rand(100000, 999999));		
			$postdata['account_type_id'] = $this->config->get('constants.ACCOUNT_TYPE.USER');						
			$regSess = md5($postdata['uname'].$this->request->mob_number);		
			//$postdata['code_hash'] = md5($postdata['country']);
			$data = (array) $this->apiaffObj->country_details($postdata['country']);
			if(!empty($data)){
				$postdata['country_name'] = $data['country_name'];
			}
			$this->session->set($regSess, $postdata);				
			$data['mobile'] = $postdata['mob_number'];				
			$op['data'] = $data;
			//$op['data'] = ['mobile'=>$postdata['mob_number'], 'code'=>$phone_code];	
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');				
			$op['regtoken'] = $this->session->getId().'.'.$regSess;
		} else {
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$op['msg'] = trans('user/account.something_went_wrong');
		}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	
	
	public function Verify_Mobile ()
    {
        $op = [];
        $postdata = $this->request->all();
		
        if ($this->request->header('regtoken') && !empty($this->request->header('regtoken')) && strpos($this->request->header('regtoken'), '.'))
        {			
			
            $access_token = explode('.', $this->request->header('regtoken'));			
            $this->session->setId($access_token[0], true);			
            if ($this->session->has($access_token[1]))
            {
                $sessiondata = $this->session->get($access_token[1]);	
                //$op['code'] =  $verify_code = rand(100000, 999999);
				$op['code'] = $verify_code = $this->session->has('SIGNUP.OTP') ? $this->session->get('SIGNUP.OTP') : rand(100000, 999999);
				$this->session->set('SIGNUP.OTP', $verify_code);
                $sessiondata['verify_code'] = md5($verify_code);               				
                $regSess = md5($sessiondata['uname'].$sessiondata['mob_number']);
				$sessiondata['mob_number'] = $postdata['mob_number'];	
                //$this->session->set($regSess, $sessiondata);				
                $this->session->set($access_token[1], $sessiondata);			
				//$msg =  \Lang::get('user/account.signup_mobile_verification', ['code'=>$verify_code, 'site'=>$this->siteConfig->site_name]);					
				//CommonNotifSettings::sms($postdata['mob_number'], $msg);	  

				$data['mobile'] = $sessiondata['mob_number'];
				$data['email'] = $sessiondata['email'];				
				$data['full_name'] = $sessiondata['full_name'];				
				$data['code'] = $verify_code;				
				$data['sitename'] = $this->siteConfig->site_name;				
				CommonNotifSettings::notify('USER.SIGNUP.VERIFY_MOBILE', null, Config::get('constants.ACCOUNT_TYPE.USER'), $data, false, true, false, true, false);				
				$mobile = $this->hideMobileNumber($postdata['mob_number']);
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');                
				$op['msg'] = trans('user/account.sent_verif_code', ['mobile'=>$mobile]);
					//$op['regtoken'] = $this->session->getId().'.'.$regSess;
                $op['regtoken'] = $this->session->getId().'.'.$access_token[1];
            }
            else
            {                
                $op['msg'] = 'Session Expired';
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = 'Invalid token';
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function resendSignUpCode ()
    {
        $op = [];
        $postdata = $this->request->all();
        if ($this->request->header('regtoken') && !empty($this->request->header('regtoken')) && strpos($this->request->header('regtoken'), '.'))
        {
            $access_token = explode('.', $this->request->header('regtoken'));			
            $this->session->setId($access_token[0], true);
            if ($this->session->has($access_token[1]))
            {				
                $sessiondata = $this->session->get($access_token[1]);		
				$op['code'] = $verify_code = $this->session->has('SIGNUP.OTP') ? $this->session->get('SIGNUP.OTP') : rand(100000, 999999);
                $sessiondata['verify_code'] = md5($verify_code);
                $regSess = md5($sessiondata['uname'].$sessiondata['mob_number']);				
				$this->session->set($access_token[1], $sessiondata);									
				
				$data['mobile'] = $sessiondata['mob_number'];
				$data['email'] = $sessiondata['email'];				
				$data['full_name'] = $sessiondata['full_name'];				
				$data['code'] = $verify_code;				
				$data['sitename'] = $this->siteConfig->site_name;				
				CommonNotifSettings::notify('USER.SIGNUP.VERIFY_MOBILE', null, Config::get('constants.ACCOUNT_TYPE.USER'), $data, false, true, false, true, false);
				
                $mobile = $this->hideMobileNumber($sessiondata['mob_number']);
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');                
				$op['msg'] = trans('user/account.resent_verif_code', ['mobile'=>$mobile]);					
				$op['regtoken'] = $this->session->getId().'.'.$access_token[1];
            }
            else
            {                
                $op['msg'] = 'Session Expired';
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = 'Invalid token';
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function confirm_signup ()
    {		
        $postdata = $this->request->all();	
        if ($this->request->header('regtoken') && !empty($this->request->header('regtoken')) && strpos($this->request->header('regtoken'), '.') > 0)
        {
            $access_token = explode('.', $this->request->header('regtoken'));
            $this->session->setId($access_token[0], true);
            if ($this->session->has($access_token[1]))
            {
                $signupData = $this->session->get($access_token[1]);	
			    if ($signupData['verify_code'] == md5($postdata['code']))
                {					
                    $signupData['is_mobile_verified'] = true;
					if ($account_info = $this->apiaffObj->save_account($signupData))
					{
						$this->session->forget($access_token[1]);						
						$data['fullname'] = $signupData['full_name'];
						$data['username'] = $account_info->user_name;
						$data['email'] = $signupData['email'];
						$data['pwd'] = $signupData['password'];						
						$data['country'] = $account_info->country;						
						$data['login_link'] = url("login");
						$data['act_link'] = url("activation/".$account_info->activation_key);
						$key = md5($account_info->account_id.$data['email']);
						
						$data['email_verify_link'] = url('user/verify_email').'?verification_code='.$key;				
						//$data ['domain_name'] = $this->pagesettings->site_domain;	
						
						$data['referral_email'] = '';
						$data['referral_name'] = '';
						$data['referral_fullname'] = '';
						$data['referral_link'] = '';
						
						if (!empty($account_info->sponser_details)) {	 
							$data['referral_email'] = isset($account_info->sponser_details) ? $account_info->sponser_details->email : '' ;
							$data['referral_name'] = isset($account_info->sponser_details) ? $account_info->sponser_details->referrer_name : '';
							$data['referral_fullname'] = isset($account_info->sponser_details) ? $account_info->sponser_details->full_name : '';		 	
							//$data['referral_contact'] = $account_info->sponser_details->sponser_mobile;
							$data['referral_link'] = url("/".$data['referral_name']); 
							
							
							/*  Sponser Email */					
							//CommonNotifSettings::notify('USER.SIGNUP.SPONSER_NOTIFICATION', $account_info->sponser_details->account_id, Config::get('constants.ACCOUNT_TYPE.USER'), $data, true, true, false, true, false);													 
						}
						$data['code'] = rand(100000, 999999);
                        $data['hash_code'] = md5($data['code']);
						$token = $this->session->getId().'.'.$data['hash_code'];
						$data['email_verify_link'] = url('verify-email/'.$token);
						$data['sitename'] = $this->siteConfig->site_name; 
						/*  New User E-Mail */				
					CommonNotifSettings::notify('USER.USER_SIGNUP', $account_info->account_id, Config::get('constants.ACCOUNT_TYPE.USER'), $data, true, true, false, true, false);					
						
						$res = $this->accountObj->checkAccount_login($this->request, ['account_id'=>$account_info->account_id, 'country_id'=>$account_info->country_id]);
						$validate = $res['status'];
						if ($validate == 1)
						{						
							$has_pin 		     = !empty($res['accinfo']->security_pin) ? 1 : 0;
							$op['msg'] 		     = $res['msg'];
							//$op['tes'] 		     = 'tes';
							$op['token']         =  $res['token'];	
							$op['account_id']    = $res['accinfo']->account_id;
							$op['full_name']     =  $res['accinfo']->full_name;
							$op['uname'] 	     =  !empty($res['accinfo']->uname)?$res['accinfo']->uname:'';
							$op['user_code']     = $res['accinfo']->user_code;          
							$op['account_type']  =  $res['accinfo']->account_type;
							$op['account_type_name'] = $res['accinfo']->account_type_name;       					
							$op['mobile'] 		 =  $res['accinfo']->mobile;
							$op['email'] 		 =  $res['accinfo']->email;
							$op['currency_id']   = $res['accinfo']->currency_id;   
							$op['currency_code'] =  $res['accinfo']->currency_code;		
							$op['country_flag']  =  $res['accinfo']->country_flag;		
							$op['is_mobile_verified'] = $res['accinfo']->is_mobile_verified;          
							$op['is_email_verified'] = $res['accinfo']->is_email_verified;          
							$op['is_affiliate'] = $res['accinfo']->is_affiliate;     
							if(!empty($res['accinfo']->is_affiliate)) {
								$op['can_sponser'] = 1;
							}
							$op['account_log_id'] = $res['accinfo']->account_log_id;          
							//$op['profile_img'] = $res['accinfo']->profile_img;     
							$op['profile_img'] = asset($this->config->get('path.ACCOUNT.PROFILE_IMG.WEB.100x100').$res['accinfo']->profile_image);
							$op['autologin'] = $this->config->get('constants.ON');
							$op['is_verified'] = $res['accinfo']->is_verified;   
							$op['country'] = $res['accinfo']->country;     					                       
							$op['country_code'] = $res['accinfo']->country_code;     					                       
							$op['country_id'] = $res['accinfo']->country_id;     					                       
							$op['phone_code'] = $res['accinfo']->phonecode;     					                       
							$op['has_pin'] = $has_pin;     					                       
							$op['is_guest'] = $res['accinfo']->is_guest;     					                       
							$op['toggle_app_lock'] = $res['accinfo']->toggle_app_lock;     					                       
							$op['first_name'] = $res['accinfo']->firstname;     					                       
							$op['last_name'] = $res['accinfo']->lastname;   
							$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');								
							return $this->response->json($op, $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
						}
					}
					else
                    {
						$op = ['msg'=>'Something went wrong', 'status'=>$this->config->get('httperr.UN_PROCESSABLE')];
                        return $this->response->json($op, $this->config->get('httperr.UN_PROCESSABLE'), $this->headers, $this->options);
                    }					
                }
                else
                {
					$op = ['msg'=>'OTP is invalid or expired', 'status'=>$this->config->get('httperr.UN_PROCESSABLE')];
                    return $this->response->json($op, $this->config->get('httperr.UN_PROCESSABLE'), $this->headers, $this->options);
                }
            }
            else
            {
				$op = ['msg'=>'Session Expired', 'status'=>$this->config->get('httperr.UN_PROCESSABLE')];
                return $this->response->json($op, $this->config->get('httperr.UN_PROCESSABLE'), $this->headers, $this->options);
            }
        }
        else
        {
			$op = ['msg'=>'Token Missing', 'status'=>$this->config->get('httperr.UN_PROCESSABLE')];
            return $this->response->json($op, $this->config->get('httperr.UN_PROCESSABLE'), $this->headers, $this->options);
        }
    }	
	
	public function Country_list ()
    {
        $wdata = $op = [];		
		if (isset($this->geo->current->country_id) && !empty($this->geo->current->country_id))
		{
			$wdata['country_id'] = $this->geo->current->country_id;
		}
        $res = $this->apiaffObj->country_list($wdata);		
        if ($res)
        {
            $op['data'] = (array) $res;
            $op['status'] = $this->status_code = $this->config->get('httperr.SUCCESS');            
        }
        else
        {
            $op['data'] = [];
            $op['status'] = $this->status_code = $this->config->get('httperr.NOT_FOUND');
            $op['msg'] = 'Country Not Found';
        }        
		return $this->response->json($op, $this->status_code, $this->headers, $this->options);
    }
	
	public function Country_update ()
    {
        $op = [];
        $postdata = $this->request->all();	  	
		
		//$this->userSess->country_id = $postdata['country_id'];
		//$this->sessionName = $this->config->get('app.role');
		//$this->session->set($this->sessionName, (array) $this->userSess);
		$this->geo->current->country_id = $postdata['country_id']; 
		$op['msg'] = 'Country Updated Successfully';
		$op['status'] = $status_code =  $this->config->get('httperr.SUCCESS');			
		//$op['data'] = $this->geo;	
		return $this->response->json($op, $status_code, $this->headers, $this->options);
    }	
	
	public function myOrders ($type = null)
    {
        $postdata = $this->request->all();
        $op = $wdata = [];
        $op['data'] = $filter = [];
		$types = trans('user/general.orders_list');		
		//print_r($types);exit;
        if ($this->request->has('draw'))
        {
            $op['draw'] = !empty($this->request->draw) ? $this->request->draw : 1;
        }		
        $wdata['account_id'] = $this->userSess->account_id;		
        $wdata['account_type'] = $this->userSess->account_type;		
        $wdata['store_code'] = isset($this->userSess->store_code) ? $this->userSess->store_code : '';				
		$op['recordsTotal'] = $this->accountObj->myOrders($wdata, true);
		//print_r($op);exit;
		if (!empty($type) && array_key_exists($type, $types))
        {
			$wdata['type'] = $type;
		}
        if (!empty($postdata))
        {            
            $filter['from'] = !empty($postdata['from']) ? $postdata['from'] : '';
            $filter['to'] = !empty($postdata['to']) ? $postdata['to'] : '';
            $filter['search_term'] = !empty($postdata['search_term']) ? $postdata['search_term'] : '';
        }		
        $op['recordsFiltered'] = 0;
        $op['recordsFiltered'] = $this->accountObj->myOrders($wdata, true);
		//print_r($op);exit;
        if (!empty($op['recordsFiltered']) && $op['recordsFiltered'] > 0)
        {			
            if (!empty(array_filter($filter)))
            {
                $wdata = array_merge($wdata, $filter);
                $op['recordsFiltered'] = $this->accountObj->myOrders($wdata, true);
            }
            if (!empty($op['recordsFiltered']) && $op['recordsFiltered'] > 0)
            {
		        //$op['recordsFiltered'] = $op['recordsTotal']; //if no records in data in tables 0 records//
				$wdata['length'] = $op['length']= !empty($postdata['length']) ? $postdata['length'] : 10;
				$wdata['page'] = $op['cpage'] = isset($postdata['page']) && !empty($postdata['page']) ? $postdata['page'] : 1;
				$wdata['start'] = !empty($postdata['start']) ? $postdata['start'] : ($wdata['page'] - 1) * $wdata['length'];
				$op['move_next'] = (($wdata['start'] + $wdata['length']) < $op['recordsFiltered']) ? true : false;               
                if (!empty($postdata['order']))
                {
                    $wdata['orderby'] = isset($postdata['columns']) ? $postdata['columns'][$postdata['order'][0]['column']]['name'] : (isset($postdata['orderby']) ? $postdata['orderby'] : null);
                    $wdata['order'] = isset($postdata['order'][0]['dir']) ? $postdata['order'][0]['dir'] : (isset($postdata['order']) ? $postdata['order'] : null);
                }
                $op['data'] = $this->accountObj->myOrders($wdata);
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            } 
        }
        else
        {			
            $op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
            $op['msg'] = trans('user/general.no_order');			
        }
		//print_r($op);exit;
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	
	
	public function myOrderDetails ($order_code)
    {
        $op = $data = [];
		$data['account_type'] = $this->userSess->account_type;		
        $data['store_code'] = isset($this->userSess->store_code) ? $this->userSess->store_code : '';		
        $data['account_id'] = $this->userSess->account_id;
        $data['order_code'] = $order_code;
        //print_r($this->accountObj->myOrderDetails($data));exit;
        if ($order = $this->accountObj->myOrderDetails($data))
        {   
            $op['details'] = $order['res'];
			$op['statementline_id'] = $order['statementline_id'];			
			$op['account_type'] = $this->userSess->account_type;					
			$data['company_name'] = $op['details']['store_name'];			
            $op['share']['email'] = ['title'=>trans('user/account.store_share.email.title', (array) $data), 'content'=>trans('user/account.store_share.email.content', (array) $data)];
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
            //$op['msg'] = trans('general.not_found', ['which'=>trans('general.order.label')]);
            $op['msg'] = 'Date not found';
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	
	
	public function submitRatingsFeedbacks ()
    {
		//print_r($this->request->order_code);exit;
        $data = $op = [];
        $data['order_id'] = null;
        $this->request->tip_amount = $this->request->has('tip_amount') ? $this->request->tip_amount : 0;
        $data['account_id'] = $this->userSess->account_id;
		
        if (isset($this->request->order_code) && !empty($this->request->order_code))
        {
			
            $store = $this->accountObj->getOrderInfo($this->request->order_code);			
            $data['post_type'] = $this->config->get('constants.POST_TYPE.ORDER');
            $data['order_id'] = $store->order_id;
            $data['post_id'] = $store->store_id;
            $data['account_id'] = $store->account_id;	
        }
        /* else if (isset($this->request->store_code) && !empty($this->request->store_code))
        {
            $store = $this->marchantObj->getStoreInfo($this->request->store_code);
            $data['post_type'] = $this->config->get('constants.POST_TYPE.STORE');
            $data['post_id'] = $store->store_id;
        } */
        $data['rating'] = $this->request->rating;
        $data['feedback'] = $this->request->has('feedback') ? $this->request->feedback : null;
		
        /* if ($this->request->has('order_id'))
        {
            $data['order_id'] = $this->request->order_id;
        } */
        if ($this->request->tip_amount > 0)
        {
           /*  switch ($this->commonObj->addTip(['store_id'=>$store->store_id, 'order_id'=>$data['order_id'], 'account_id'=>$this->userSess->account_id, 'full_name'=>$this->userSess->full_name, 'currency_id'=>$this->userSess->currency_id, 'tip_amount'=>$this->request->tip_amount]))
            {
                case 1:
                    $op['msg'] = trans('user/account.thanks_for_tip');
                    $op['remark'] = trans('general.rating.'.$this->request->rating);
                    $this->statusCode = $this->config->get('httperr.SUCCESS');
                    $this->commonObj->saveRatingsFeedbacks($data);
                    break;
                case 2:
                    $op['msg'] = trans('general.insufficient_balance_to_tip');
                    $op['remark'] = trans('general.rating.'.$this->request->rating);
                    $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    break;
                case 3:
                    $op['msg'] = trans('general.tip_already_given');
                    $op['remark'] = trans('general.rating.'.$this->request->rating);
                    $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
                    break;
            } */
        }
        else
        {			
            if ($this->accountObj->saveRatingsFeedbacks($data))
            {
                $op['msg'] = trans('user/account.ratings_thanks');
                $op['remark'] = trans('user/account.rating.'.$this->request->rating);
                $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
                $op['msg'] = trans('user/account.no_changes_in_your_rating');
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function MyMessages ()
    {
        $op = $data = [];
        $data['account_id'] = $this->userSess->account_id;		
        $types = $this->config->get('constants.MESSAGE.TYPES');
		
        if ($this->request->has('type') && array_key_exists($this->request->type, $types))
        {		
            $data['notification_type'] = $types[$this->request->type];			
            $op['recordsTotal'] = $op['recordsFiltered'] = $this->accountObj->getMessages($data, true);
			//print_r($op);exit;
            $op['data'] = [];
            $op['next'] = false;
            if (!empty($op['recordsFiltered']) && $op['recordsFiltered'] > 0)
            {
                if (!empty($op['recordsFiltered']) && $op['recordsFiltered'] > 0)
                {
                    $data['length'] = isset($postdata['length']) && !empty($postdata['length']) ? $postdata['length'] : 10;
                    $postdata['page'] = isset($postdata['page']) && !empty($postdata['page']) ? $postdata['page'] : 1;
                    $data['start'] = ($data['length']) * ($postdata['page'] - 1);
                    $op['next'] = $op['recordsFiltered'] > ($postdata['page'] * $data['length']) ? $postdata['page'] + 1 : false;
                    if (isset($data['order']))
                    {
                        $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                        $data['order'] = $postdata['order'][0]['dir'];
                    }
                    $op['data'] = $this->accountObj->getMessages($data);
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
            }
            else
            {
                $op['msg'] = 'Data Not Found';
                $op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
            }
        }
        else
        {
            foreach ($types as $type => $type_id)
            {
                $data['notification_type'] = $type_id;
                $op[$type]['recordsTotal'] = $op[$type]['recordsFiltered'] = $this->accountObj->getMessages($data, true);
                $op[$type]['data'] = [];
                $op[$type]['next'] = false;
                if (!empty($op[$type]['recordsFiltered']) && $op[$type]['recordsFiltered'] > 0)
                {
                    if (!empty($op[$type]['recordsFiltered']) && $op[$type]['recordsFiltered'] > 0)
                    {
                        $data['length'] = isset($postdata['length']) && !empty($postdata['length']) ? $postdata['length'] : 10;
                        $postdata['page'] = isset($postdata['page']) && !empty($postdata['page']) ? $postdata['page'] : 1;
                        $data['start'] = ($data['length']) * ($postdata['page'] - 1);
                        $op[$type]['next'] = $op[$type]['recordsFiltered'] > ($postdata['page'] * $data['length']) ? $postdata['page'] + 1 : false;
                        if (isset($data['order']))
                        {
                            $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                            $data['order'] = $postdata['order'][0]['dir'];
                        }
                        $op[$type]['data'] = $this->accountObj->getMessages($data);
                        $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                    }
                }
                else
                {
                    //$op[$type]['msg'] = trans('user/account.no_notifications');
                    $op[$type]['msg'] = 'Data Not Found';
                    $op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
                }
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function updateNotificationToken ()
    {
        $op = [];
        $data = $this->request->all();
        if (isset($this->userSess->account_id))
        {
            $data['account_id'] = $this->userSess->account_id;
            $data['account_log_id'] = $this->userSess->account_log_id;
        }
        if (isset($this->userSess))
        {
            $this->userSess->fcm_registration_id = $data['fcm_registration_id'];
            $this->session->set($this->sessionName, (array) $this->userSess);
        }
        else
        {
            $this->session->set('guest', (array) $this->userSess);
        }		
        if ($this->accountObj->updateNotificationToken($data))
        {
            $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['msg'] = trans('general.updated', ['which'=>'FCM ID', 'what'=>'updated']);
        }
        else
        {
            $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
            $op['msg'] = trans('general.already', ['which'=>'FCM ID', 'what'=>'updated']);
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function toggleAppLock ()
    {
        $data = $op = [];
        $data['account_log_id'] = $this->userSess->account_log_id;
        $data['account_id'] = $this->userSess->account_id;
        $data['toggle_app_lock'] = $this->request->toggle_app_lock;
        //$data['post_type'] = $this->config->get('constants.POST_TYPE.ACCOUNT');
        //$data['post_type'] = $this->config->get('constants.ACCOUNT_TYPE.USER');		
        if ($this->accountObj->toggleAppLock($data))
        {
            $this->userSess->toggle_app_lock = $this->request->toggle_app_lock;
            $this->config->set('app.accountInfo', $this->userSess);
            $this->session->set($this->sessionName, (array) $this->userSess);
            $op['msg'] = trans('general.updated', ['which'=>trans('general.label.toggle_app_lock'), 'what'=>trans('general.actions.updated')]);
            $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
            $op['msg'] = trans('general.no_changes');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function reviews_and_rating_list ()
    {
        $data = $op = [];
        $data = $this->request->all();
        if (isset($data['store']) && !empty($data['store']))
        {
            $data['store'] = $data['store'];
        }
        $data['account_id'] = $this->userSess->account_id;
        $op['recordsTotal'] = $op['recordsFiltered'] = $this->accountObj->reviews_and_rating_list($data, true);
		//print_r($op);exit;
        if (!empty($op['recordsTotal']) && $op['recordsTotal'] > 0)
        {
            $op['recordsFiltered'] = $op['recordsTotal'];
            $data['length'] = $op['length'] = !empty($data['length']) ? $data['length'] : 10;
            $data['page'] = $op['cpage'] = isset($data['page']) && !empty($data['page']) ? $data['page'] : 1;
            $data['start'] = !empty($data['start']) ? $data['start'] : ($data['page'] - 1) * $data['length'];
            $op['move_next'] = (($data['start'] + $data['length']) < $op['recordsFiltered']) ? true : false;
            $op['data'] = $this->accountObj->reviews_and_rating_list($data);
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['data'] = [];
            //$op['msg'] = trans('user/account.reviews_not_written');
            $op['msg'] = 'Reviews not written.';
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
}
