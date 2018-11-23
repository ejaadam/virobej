<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\AffModel;



class UserBaseController extends AffBaseController
{
	public $lang_id = 2;
	public $userObj = '';
	public $userSess = '';
	public $usercommonObj = '';
	public function __construct ()
    {	
        parent::__construct();  
		$this->affObj = new AffModel();			
        if (\Session::has('appLang'))
        {
			config(['app.locale_id' => \Session::get('appLang')]);
        }
		if (\Session::has('userdata'))
        {	            
			$this->userSess = (object)\Session::get('userdata');
			view()->share('userSess',(object)\Session::get('userdata'));
            $this->account_id=$this->userSess->account_id;
		}		 		
           	
    }		 
    public function checkUserLogin() 
	{
        $userdata = \Session::get('userdata');
        $account_name = $userdata['uname'];
        if (empty($account_name) || !isset($userdata['account_id']) || empty($userdata['account_id'])) {
        return  redirect('user/login');
        }
    }
	
public function user_verification_check ($arr = array())
    {
        if (isset($arr['req']))
        {
            switch ($arr['req'])
            {
                case 'paid_status':
                    if ($this->is_paid == $this->config->get('constants.ACTIVE'))
                    {
                        return true;
                    }
                    else
                    {
                        if (isset($arr['redirect']) && $arr['redirect'] == false)
                        {
                            return false;
                        }
                        else
                        {
                            Redirect::to('user/get_package')->send();
                        }
                    }
                    break;
			}
		}
	}
}
