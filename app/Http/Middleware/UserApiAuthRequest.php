<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Log;

class UserApiAuthRequest
{

    public function handle ($request, Closure $next)
    {
        //require(config('env.KEY_PATH'));
		define('KEY', 'e10adc3949ba59abbe56e057f20f883e');
        $resettoken = null;
        $regex = '/^[a-zA-Z0-9\-]+$/';
        $this->session = $request->session();	
		
        if ($request->hascookie(config('app.session_key')) && !$request->is('api/*'))
        {
            $resettoken = $request->cookie(config('app.session_key'));							
        }		
        else if ($request->header('usrtoken'))
        {
            $resettoken = $request->header('usrtoken');		
				
        }
		//print_r($resettoken);exit;		
			
		Log::info('From Url : '.$request->fullUrl().' with '.$request->header('User-Agent'));
        if (!empty($resettoken))
        {
			$postdata = request()->all();
			Log::info('Token:'.$resettoken.', Data:'.json_encode($postdata)); 	
			
            if (strpos($resettoken, '-') && preg_match($regex, $resettoken))
            {
                $access_token = explode('-', $resettoken);
                $this->session->setId($access_token[0], true);
				
                if ($this->session->has(Config::get('app.role')) && $this->session->get(Config::get('app.role').'.token') == $access_token[1])
                {
                    $accountInfo = (object) $this->session->get(Config::get('app.role'));
                    $accountInfo->is_guest = 0;					
                    Config::set('app.timezone', $accountInfo->timezone);
                    if (!empty($accountInfo->lang_id))
                    {
                        Config::set('app.locale', array_search($accountInfo->lang_id, config('constants.LANG')));
                        Config::set('app.locale_id', $accountInfo->lang_id);
                    }
                    if ($request->isMethod('get'))
                    {
                        view()->share('userInfo', $accountInfo);
                    }
                    $accountInfo->key = md5(KEY.md5($accountInfo->account_id));
                    Config::set('app.accountInfo', $accountInfo);
                }
                else
                {
                    $this->loadGuestInfo($request);
                }
            }
            else
            {				
                $this->session->setId($resettoken, true);
                $this->loadGuestInfo($request);
            }
        }
        else
        {			
            $this->loadGuestInfo($request);
        }
        return $next($request);
    }

    private function loadGuestInfo ($request)
    {
        if ($this->session->has('guest'))
        {
            $guestInfo = (object) $this->session->get('guest');			
            Config::set('app.timezone', $guestInfo->timezone);
            if (!empty($guestInfo->lang_id))
            {				
                Config::set('app.locale', array_search($guestInfo->lang_id, config('constants.LANG')));
                Config::set('app.locale_id', $guestInfo->lang_id);
            }
            Config::set('app.guestInfo', $guestInfo);						
        }
        else
        {
            $guestInfo = ['is_guest'=>1, 'timezone'=>'Asia/Kolkata', 'lang_id'=>1, 'fcm_registration_id'=>null];		
			$guestInfo['full_token'] = $this->session->getId().'-'.md5('guest');	
            $this->session->set('guest', $guestInfo);           
            Config::set('app.guestInfo', $guestInfo);	
        }
    }

}
