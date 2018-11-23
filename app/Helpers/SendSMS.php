<?php
namespace App\Helpers;

use DB;

class SendSMS
{
    public $APIUsername = '';
    public $APIkey = '';
    public $Senderid = '';
    public $APISendName = '';
    public $url = '';
    public $api_status = 1;

    public function __construct ()
    {
        $this->APIUsername = 'xpayback';
        $this->APIkey = 'd205a35466XX';
        $this->APISendName = 'XPAYBK';
        $this->url = 'sms.xpayback.com/submitsms.jsp';
        $apiSetting = $this->get_sms_service_settings();
        if (is_object($apiSetting))
        {
            foreach ($apiSetting as $key=> $val)
            {
                $this->$key = $val;
            }
        }
    }

	/*	
	sample input
	---
	SendSMS::send_sms('user.user_reg_success', [
		'mobile'=>9952106187,
		'username'=>$username,
		'password'=>$password,
		'sitename'=>$this->siteConfig->sitename]);
		exit;
	*/	
    public function send_sms ($msgtype, $params = array())
    {
        $data = '';
        if (!empty($msgtype) && !empty($params))
        {
            $sms_data = $this->getMsgdata($msgtype, $params);
            $data = http_build_query(array_merge($sms_data, [
                'user'=>$this->APIUsername,
                'key'=>$this->APIkey,
                'senderid'=>$this->APISendName]));
        }
        $creq = curl_init($this->url);
        curl_setopt($creq, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($creq, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($creq, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($creq, CURLOPT_POST, 1);
        curl_setopt($creq, CURLOPT_POSTFIELDS, $data);
        curl_setopt($creq, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($creq, CURLOPT_RETURNTRANSFER, 1);
        $cres = curl_exec($creq);
        $status = curl_getinfo($creq, CURLINFO_HTTP_CODE);
        curl_close($creq);
        if ($cres)
        {
            return true;
        }
    }

	
	
    private function getMsgdata ($msgtype, $arr = array())
    {
        extract($arr);
        $message = '';
        $mobile = '9952106187';
        switch ($msgtype)
        {
            case 'test':
                $message = 'Hi, its working';
                break;
            case 'user_reg_success':
                $message = 'Thanks for signing up with '.$sitename.'. You login details are Username:'.$username.',Pwd:'.$password;
                break;            
        }
        if (!empty($message))
        {
            return ['mobile'=>$mobile, 'message'=>str_replace(['$ ', '₹ ', '₱ ', '৳ ', '¥ ', '€ ', '£ ', '฿ '], '', $message), 'accusage'=>1];
        }
        return false;
    }

    private function get_sms_service_settings ()
    {
        $resVal = DB::table(config('tblconstants.SETTINGS'))
                ->where('setting_key', 'sms_api')
                ->value('setting_value');
        if (!empty($resVal))
        {
            if (strpos($resVal, '}'))
            {
                $resVal = json_decode(stripslashes($resVal));
            }
            return $resVal;
        }
        return NULL;
    }

    // function to get gravatar photo/image from gravatar.com using email id.
    public function getGravatarURL ($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= '?s=$s&d=$d&r=$r';
        if ($img)
        {
            $url = '<img src="'.$url.'"';
            foreach ($atts as $key=> $val)
                $url .= ' '.$key.'="'.$val.'"';
            $url .= ' />';
        }
        return $url;
    }

}
