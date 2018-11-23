<?php

class Mailer
{

    /**
     *
     * @param string $to whom to be mail send
     * @param string $view which mail view is to be used
     * @param string $subject subject of the mail
     * @param array $data datas which are used in view
     * @param string $email from email-id<i>(Optional)</i>
     * @param string $sender_name from user name<i>(Optional)</i>
     * @return array|bool response of mail sent or false
     */
    public static function send ($to, $view, $subject, $data, $email = '', $sender_name = '')
    {
        $settings = DB::table(Config::get('tables.SITE_MAIL_SETTINGS'))
                ->select('driver_type', 'email', 'sender_name', 'settings')
                ->where('status', Config::get('constants.ACTIVE'))
                ->where('is_deleted', Config::get('constants.OFF'))
                ->first();
        $settings->email = !empty($email) ? $email : $settings->email;
        $settings->sender_name = !empty($sender_name) ? $sender_name : $settings->sender_name;
        if (!empty($settings))
        {
		
            $settings->settings = json_decode(stripslashes($settings->settings));						
            if ($settings->driver_type == 1)//SMTP
            {
                Config::set('mail', array(
                    'driver'=>'smtp',
                    'host'=>$settings->settings->host,
                    'port'=>$settings->settings->port,
                    'from'=>array('address'=>$settings->email, 'name'=>$settings->sender_name),
                    'encryption'=>$settings->settings->encryption,
                    'username'=>$settings->settings->username,
                    'password'=>$settings->settings->password,
                    'sendmail'=>'/usr/sbin/sendmail -bs',
                    'pretend'=>false
                ));
                return Mail::send($view, $data, function($message) use ($to, $subject)
                        {
                            $message->to($to)->subject($subject);
                        });
            }
            else if ($settings->driver_type == 2) //SendGrid
            {			
				//echo $to;
                $set = Config::get('services.sendgrid');								
                $settings->settings->api_user = !empty($settings->settings->api_user) ? $settings->settings->api_user : $set['api_user'];
                $settings->settings->api_key = !empty($settings->settings->api_key) ? $settings->settings->api_key : $set['api_key'];
                $request = curl_init($set['url']);
                curl_setopt($request, CURLOPT_POST, true);
                curl_setopt($request, CURLOPT_POSTFIELDS, array(
                    'api_user'=>$settings->settings->api_user,
                    'api_key'=>$settings->settings->api_key,
                    'to'=>$to,
                    'subject'=>$subject,
                    'text'=>'',
                    'html'=>View::make($view, $data)->render(),
                    'from'=>$settings->email,
                    'fromname'=>$settings->sender_name
                ));
                curl_setopt($request, CURLOPT_HEADER, false);
                curl_setopt($request, CURLOPT_SSLVERSION, false);
                curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($request);
				print_r($response);exit;
                curl_close($request);
                return $response;
            }
        }
        return false;
    }

}
