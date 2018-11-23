<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use SGMailer;

class TWMailer
{	
    public static function send ($arr = array(), $siteConfig, $key = '')
    {	
		$view='';
        extract($arr);
        if (!empty($key))
        {	
            $s = trans('notifications.'.$key.'.email');
            $data['subject'] = $subject = $s['subject'];
            $view = $s['view'];
            $data['content'] = $s['content'];
        }		
         $from = $siteConfig->noreplay_emailid;
         $from_user_name = $siteConfig->site_name;
         $settings = json_decode(stripslashes($siteConfig->outbound_email_configure));
		 //$settings->driver=1;
        if ($settings->service == 1)
        {
            $to = (strpos($to, '@virob.com') > -1) ? 'ejdevteam@gmail.com' : $to;
            if ($settings->driver == 1)//SMTP
            {	
               /*$config = array(
                   'driver'=>$settings->smtp->driver,
                   'host'=>$settings->smtp->host,
                   'port'=>$settings->smtp->port,
                   'from'=>array('address'=>$from, 'name'=>$from_user_name),
                   'encryption'=>$settings->smtp->encryption,
                   'username'=>$settings->smtp->username,
                   'password'=>$settings->smtp->password,
               );
               Config::set('mail', $config);*/
                 Mail::queue($view, $data, function($message) use ($from, $from_user_name, $to, $subject){
                            $message->from($from, $from_user_name);
                            $message->to($to)->subject($subject);
                        });
            }
            else if ($settings->driver == 2) //SendGrid
            {				
				SGMailer::send($view, $data, [
					'from' => $from, 
					'from_name' => $from_user_name,
					'to' => $to,
					'subject'=>$subject]);
            }
        }
        return false;
    }
}