<?php
namespace App\Http\Controllers\Affiliate;
use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\AffModel;
use App\Models\LocationModel;

use TWMailer;

class AffiliateController extends AffBaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->affObj = new AffModel();
		$this->lcObj = new LocationModel();		
    }    
	
	
	public function login() {
		return view('affiliate.login');
	}
	
	public function signup($referral_name='')
	{	
		$data = [];						
		$sponsor_info=$this->affObj->referral_user_check($referral_name);		
		if($sponsor_info['status']==200){
			$data['sponsor_info']= (object)$sponsor_info;		
			$data['countries']= $this->lcObj->getCountries(['allow_signup'=>true]); 
			$this->session->set('reg_sponsor_info',$data['sponsor_info']);
		}
		else {
			$data['errmsg'] = $sponsor_info['msg'];			
		}			
		return view('affiliate.signup',$data);		
	}
	
	public function save_account ()
    {
        $data['status'] = 'error';
        $data['msg'] = '';
        $opArray = array();
        $opArray['status'] = 'error';
        $opArray['msg'] = '';
        $activation = true; /* [instant/activation] */
        $postdata = $this->request->all();	    
		if($this->session->has('reg_sponsor_info')){
			$sponsor_info = $this->session->get('reg_sponsor_info');			
			$postdata['username'] = $postdata['username'];
			$postdata['activation'] = $activation;
			$postdata['sponser_account_id'] = $sponsor_info->sponser_account_id;
			$postdata['sponser_fullname'] = $sponsor_info->sponser_fullname;
			$postdata['sponser_uname'] = $sponsor_info->sponser_name;
			$postdata['sponser_email'] = $sponsor_info->sponser_email;			
			$usrExist = $this->affObj->account_check($postdata['username'], array(
				'useravailablility'=>1,
				'existscheck'=>0,
				'referralcheck'=>0,
				'loguserlineage'=>0,
				'reqfor'=>'reg'));    				
			$account_info = $this->affObj->save_account($postdata);
			$data['site_settings'] = $site_settings = $this->pagesettings;
			if (!empty($account_info))
			{
				if (isset($postdata['firstname'])){
					$data['fullname'] = $postdata['firstname']." ".$postdata ['lastname'];
				}
				else if (isset($postdata['fullname'])){
					$data['fullname'] = $postdata['fullname'];
				}
								   
				$opArray ['msg'] = \trans('affiliate/account_controller.account_approval', array(
				 'site_name'=>$this->pagesettings->site_name,
				 'login_link'=>url('login')));
		
				$data['username'] = $postdata['username'];
				$data['email'] = $postdata['email'];
				$data['pwd'] = $postdata['confirm_password'];
				//$data['tpin'] = $postdata['ctranspin'];
				$data['country'] = $account_info->country;
				$data['site_settings'] = $this->pagesettings;
				$data['login_link'] = url("login");
				$data['act_link'] = url("activation/".$account_info->activation_key);
				$key = md5($account_info->account_id.$data['email']);
				$this->affObj->update_verification_code($account_info->account_id, $key);
				$data['email_verify_link'] = url('user/verify_email').'?verification_code='.$key;

				$data ['domain_name'] = $this->pagesettings->site_domain;
				
				/* referral details */
				$data['referral_email'] = $sponsor_info->sponser_email;
				$data['referral_name'] = $sponsor_info->sponser_name;
				$data['referral_fullname'] = $sponsor_info->sponser_fullname;
				$data['referral_contact'] = $sponsor_info->sponser_mobile;
				$data['referral_link'] = url("/".$data['referral_name']);
				$email_data = array(
					'email'=>$postdata['email'],
					'site_domain'=>$this->pagesettings->site_domain
				);			
				
				/* new User for email */
				$mstatus = TWMailer::send(array(
				 'to'=>$postdata['email'], /* $email_data['email'], */
				 'subject'=>$this->config->get('mailcontents.new_user'),
				
				 'data'=>$data,
				 'from'=>$this->pagesettings->noreplay_emailid,
				 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings);	
				 
				/* Sponser  sponsor email*/
				$mstatus = TWMailer::send(array(
				 'to'=> $sponsor_info->sponser_email , /* $email_data['email'], */
				 'subject'=>$this->config->get('mailcontents.new_user'),
				 'view'=>'emails.affiliate.customer_reviews',
				 'data'=>$data,
				 'from'=>$this->pagesettings->noreplay_emailid,
				 'fromname'=>$this->pagesettings->site_domain), $this->pagesettings);
				$this->status_code = $this->config->get('httperr.SUCCESS');				
				$opArray['status'] = $this->status_code;
				$opArray['msg'] = trans('affiliate/account_controller.signup_success');				               
			}				
			else
			{
				$this->status_code = $this->config->get('httperr.SUCCESS');
				$opArray['status'] = $this->status_code;
				$opArray['msg'] = trans('affiliate/account_controller.registration_failed');
			}
		}
        return $this->response->json($opArray,$this->status_code);
    }
	
	
	public function dashboard(){		
		$data = array();
		return view('affiliate.dashboard');
	}
	
  public function myprofile(){
	 
		$data = array();
		$data['account_id'] = $this->userSess->account_id;
		$data['profileInfo'] = $this->affObj->my_profile($data);

		return view('affiliate.settings.profile',$data);
	}
	
}
