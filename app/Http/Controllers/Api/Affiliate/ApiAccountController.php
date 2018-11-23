<?php
namespace App\Http\Controllers\Api\Affiliate;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BaseController;
use App\Models\Api\Affiliate\AffModel;
use App\Models\CommonModel;
use Config;
use Session;
use TWMailer;
use App\Helpers\CommonNotifSettings;

class ApiAccountController extends BaseController
{
    
    public function __construct ()
    {
        parent::__construct();
		$this->apiaffObj = new AffModel();
		$this->commonObj = new CommonModel();
    }	
	
	public function SignUp ()
    {			
        $op = [];
        $postdata = $this->request->all();		      
		if (!empty($postdata)) {
			$account_info = $this->apiaffObj->save_account($postdata);		
			if (!empty($account_info))
			{
				$data['fullname'] = $postdata['full_name'];
				$data['username'] = $account_info->user_name;
				$data['email'] = $postdata['email'];
				$data['pwd'] = $postdata['conf_password'];
				//$data['tpin'] = $postdata['ctranspin'];
				$data['country'] = $account_info->country;
				//$data['site_settings'] = $this->pagesettings;
				$data['login_link'] = url("login");
				$data['act_link'] = url("activation/".$account_info->activation_key);
				$key = md5($account_info->account_id.$data['email']);
				
				$op['email_verify_link'] = $data['email_verify_link'] = url('user/verify_email').'?verification_code='.$key;				
				$data ['domain_name'] = $this->pagesettings->site_domain;	
				
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
					TWMailer::send(array(
					 'to'=> $account_info->sponser_details->email, 
					 'subject'=>'Sponser - New User SignUo',
					 'view'=>'emails.affiliate.Api.sponser',
					 'data'=>$data,
					 'from'=>$this->pagesettings->noreplay_emailid,
					 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings);					
				}
				//$this->apiaffObj->update_verification_code($account_info->account_id, $key);
				
				/*  New User E-Mail */
				TWMailer::send(array(
				 'to'=>$postdata['email'], /* $email_data['email'], */
				 'subject'=>'New User SignUp Verification',
				 'view'=>'emails.affiliate.Api.new_user_signup',
				 'data'=>$data,
				 'from'=>$this->pagesettings->noreplay_emailid,
				 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings);	
				 
				$op['status'] = $this->status_code = $this->config->get('httperr.SUCCESS');			
				$op['msg'] = trans('affiliate/account_controller.signup_success');				
			} 
			else
			{
				$op['status'] = $this->status_code = $this->config->get('httperr.UN_PROCESSABLE');				
				$op['msg'] = trans('affiliate/account_controller.registration_failed');
			}
		}        
        return $this->response->json($op, $this->status_code, $this->headers, $this->options);
    }
	
	
	
	
	
	
}
