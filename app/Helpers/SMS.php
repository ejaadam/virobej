<?php
namespace App\Helpers;
use DB;
use Illuminate\Support\Facades\Config;
use Mail;
use Log;
use Lang;

class SMS
{

    /**
     *
     * @param string $mobile whom SMS to be send
     * @param string $message who to be send
     *
     * @return array|false response of sent SMS or false
     */
    public static function send ($mobile, $message)
    {		
        if (!empty($mobile) && !empty($message))
        {
			$Settings = config('services.sms');
			$data = [
				'user'=>$Settings['user'],
				'key'=>$Settings['key'],
				'senderid'=>$Settings['senderid'],            
				'mobile'=>$mobile,
				//'message'=>str_replace(['$ ', '₹ ', '₱ ', '৳ ', '¥ ', '€ ', '£ ', '฿ '], '', trans('notifications.'.$key.'.sms', $arr)),
				'message'=>str_replace(['$ ', '₹ ', '₱ ', '৳ ', '¥ ', '€ ', '£ ', '฿ '], '', $message),
				'accusage'=>1
			];
			if (!empty(trim($data['message'])))
			{
				$ch = curl_init($Settings['url']);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
				curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch);
				if ($result === FALSE)
				{
					die('SMS Sending failed: '.curl_error($ch));
				}
				curl_close($ch);
				return $result;
			}				
        }
        return false;
    }

}
