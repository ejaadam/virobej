<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BaseController;
use App\Models\Api\User\AffModel;
use App\Models\CommonModel;
use Session;
use App\Helpers\CommonNotifSettings;
use Config;
class FrontController extends BaseController
{
	public function __construct(){
		
	}
	public function sample(){
		
	echo "sdsD"; die;	
	}
  public function profilePinVerifyLink ($token)
    {

        $op = $data = $usrdata = [];
        if (!empty($token) && strpos($token, '.'))
        {
            $access_token = explode('.', $token);
		
		
	
		print_R($this->session->getId()); die;
		
            $this->session->setId($access_token[0], true);
	print_R($this->session->get('resetProfilePin')); die;
            if ($this->session->has('resetProfilePin'))
            {
				
				echo "sdasD"; die;	
                $usrdata = $this->session->get('resetProfilePin');
                $account_id = (isset($this->userSess->account_id) && !empty($this->userSess->account_id)) ? $this->userSess->account_id : '';
                if ($usrdata['account_id'] == $account_id)
                {
                    if ($usrdata['hash_code'] == $access_token[1])
                    {
                        $data['msg'] = '';
                        $data['forgotpin_frm'] = true;
                    }
                    else
                    {
                        $data['forgotpin_frm'] = false;
                        $data['msg'] = trans('user/account.forgotpin_session_expire');
                    }
                }
                else
                {
                    $data['forgotpin_frm'] = false;
                    $data['msg'] = trans('user/account.forgotpin_account_invalid');
                }
            }
            else
            {
                $data['forgotpin_frm'] = false;
                $data['msg'] = trans('user/account.forgotpin_session_expire');
            }
        }
        else
        {
            $data['forgotpin_frm'] = false;
            $data['msg'] = trans('user/account.forgotpin_session_expire');
        }
        //return view('user.account.forgot_profile_pin', (array) $data);
    }
}	