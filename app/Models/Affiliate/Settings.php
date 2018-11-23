<?php

namespace App\Models\Affiliate;

use Illuminate\Database\Eloquent\Model;

use DB;

class Settings extends Model
{
    /* Email Update */
	
	public function user_email_check ($email = 0, $getdetailstatus = 0)
    {
        if ($email)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $status['status'] = 'error';
                $status['msg'] = trans('affiliate/validator/change_email_js.invalid_email');
                return $status;
            }
            if ($email == 'testerej88@gmail.com')
            {
                $status['status'] = 'ok';
                $status['msg'] = trans('affiliate/validator/change_email_js.email_available');
                return $status;
            }
            else if ($email != 'testerej88@gmail.com')
            {
                $result = DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('email', $email)
                        ->get();
                if (empty($result) && count($result) == 0)
                {
                    $status['status'] = 'ok';
                    $status['msg'] = trans('affiliate/validator/change_email_js.email_available');
                }
                else
                {
                    $status['status'] = 'error';
                    $status['msg'] = trans('affiliate/validator/change_email_js.email_exist');
                }
                return $status;
            }
        } else
        {
            $status['status'] = 'error';
            $status['msg'] = trans('affiliate/validator/change_email_js.invalid_email');
            return $status;
        }
    }
	
	public function get_site_settings ()
    {
        return DB::table($this->config->get('tables.SITE_SETTINGS'))
                        ->where('sid', 1)
                        ->first();
    }
	
	public function update_user_email ($account_id, $email)
    {
        $update_data = array();
        $update_data['email'] = $email;
        return DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update($update_data);
    }
	
	/* Mobile Number Update */
	
	public function user_mobile_check ($mobile = 0, $getdetailstatus = 0)
    {  
        if ($mobile)
        {
            if ($mobile == 9876543210)
            {
                $status['status'] = 'ok';
                $status['msg'] = trans('affiliate/validator/change_mobile_js.mobile_available');
                return $status;
            }
            else if ($mobile != 9876543210)
            {
                $result = DB::table($this->config->get('tables.ACCOUNT_DETAILS'))
                        ->where('mobile', $mobile)
                        ->get();
                if (empty($result) && count($result) == 0)
                {
                    $status['status'] = 'ok';
                    $status['msg'] = trans('affiliate/validator/change_mobile_js.mobile_available');
                }
                else
                {
                    $status['status'] = 'error';
                    $status['msg'] = trans('affiliate/validator/change_mobile_js.unique');
                }
                return $status;
            }
        } else
        {
            $status['status'] = 'error';
            $status['msg'] = trans('affiliate/validator/change_mobile_js.invalid_mobile');
            return $status;
        }
    }
		
	public function update_user_mobile ($account_id, $mobile)
    {  
        $update_data = array();
        $update_data['mobile'] = $mobile;
        return DB::table($this->config->get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update($update_data);
    }
	
	
	
}
