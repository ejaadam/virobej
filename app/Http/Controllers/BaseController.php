<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Config;
use App\Models\Commonsettings;
use Request;
use View;

class BaseController extends Controller
{
	public $response = '';
	public $request = '';
	//public $redirect = '';
	public $session = '';
	
	public $appSettings = '';
    public $settingsObj = '';
    public $statusCode = '';
    public $headers = [];
    public $op = [];
    public $geo = [];
    public $options = JSON_PRETTY_PRINT;
    public $userSess = null;
    public $sessionName = null;	
    public $data = array();  
    //public $options = JSON_NUMERIC_CHECK;	
	public $config = [];
	
    public function __construct ()
    {
		$this->request = request();
		$this->response = response();
		$this->config = config();
		$this->session = session();
		$this->geo = $this->session->has('geo') ? $this->session->get('geo') : (object) ['current'=>(object) ['address'=>'', 'flatno_street'=>'', 'lat'=>0, 'lng'=>0, 'country_id'=>77, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode_id'=>0, 'region_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>'', 'currency_id'=>2, 'country'=>'India', 'country_code'=>'IN'], 'browse'=>(object) ['address'=>'', 'flatno_street'=>'', 'lat'=>0, 'lng'=>0, 'country_id'=>77, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode_id'=>0, 'region_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>'', 'currency_id'=>2, 'country'=>'India', 'country_code'=>'IN']];
		$this->siteConfig = $this->config->get('settings');		
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $this->account_id = null;
        $this->device_log_id = Config::get('device_log')->device_log_id;
        $this->token = Config::get('device_log')->token;
        $this->headers['X-Device-Token'] = $this->token;		
        $this->commonObj = new Commonsettings();
        $this->pagesettings = (object) Config::get('site_settings');		
		//echo '<pre>';print_r($this->pagesettings);exit;
        $this->currency_id = $this->pagesettings->site_currency_id;
        $this->language_iso_code = $this->pagesettings->language_iso_code;
        $this->pagesettings->account_type = Config::get('account_type');
        $this->country_id = 77;
        $this->state_id = null;
        $this->region_id = null;
        $this->city_id = null;
        $this->postal_code = null;
        if (Config::has('data.user') && !empty(Config::has('data.user')))
        {			
            $user_details = Config::get('data.user');					
            if (!empty($user_details))
            {			
                $this->acc_type_id = $this->account_type_id = $user_details->account_type_id;
                $this->account_id = $user_details->account_id;
                $this->uname = $user_details->uname;
                $this->full_name = $user_details->full_name;
                $this->email = $user_details->email;
                $this->mobile = $user_details->mobile;
                $this->token = $user_details->token;
                $this->language_id = $user_details->language_id;
                //$this->locale_id = $user_details->locale_id;
                //$this->time_zone_id = $user_details->time_zone_id;
                $this->currency_id = $user_details->currency_id;
                $this->send_email = $user_details->send_email;
                $this->send_sms = $user_details->send_sms;
                $this->send_notification = $user_details->send_notification;
                $this->is_mobile_verified = $user_details->is_mobile_verified;
                $this->is_email_verified = $user_details->is_email_verified;
                $user_details->address = $this->commonObj->getUserAddress($this->account_id, $this->acc_type_id);				
                if (!empty($user_details->address) && isset($user_details->address[0]->postal_code) && !empty($user_details->address[0]->postal_code))
                {
                    Config::has('data.pincode', $user_details->address[0]->postal_code);
                }			
				
                $this->user_details = $this->userSess = $user_details;
				
                if (!empty($this->userSess))
                {
                    View::share('logged_userinfo', $user_details);
					View::share('userSess', $this->userSess);
                }				
            }
        }
		elseif ($this->config->has('data.guestInfo') && !empty($this->config->get('data.guestInfo')))
        {
            $this->userSess = (object) $this->config->get('data.guestInfo');
            $this->sessionName = 'guest';
            $this->currency_id = !empty($this->userSess->currency_id) ? $this->userSess->currency_id : 2;
        }
		View::share('pagesettings', $this->pagesettings);
		View::share('siteConfig', $this->pagesettings);
		Config::set('app.locale', $this->pagesettings->language_iso_code);
        if (Request::isMethod('get'))
        {            
            View::share('device_log', Config::get('device_log'));            
        }
		
        if (Config::has('data.pincode'))
        {
            $location_info = $this->commonObj->checkPincode();
            if (!empty($location_info))
            {
                $this->country_id = $location_info->country_id;
                $this->state_id = $location_info->state_id;
                $this->region_id = $location_info->region_id;
                $this->city_id = $location_info->city_id;
                $this->postal_code = $location_info->pincode;
            }
        }
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout ()
    {
        /* if (!is_null($this->layout))
          {
          $this->layout = View::make($this->layout, $this->data);
          } */
    }

    public function upload_file ($file, $destinationPath, $filename = '')
    {
        $year = date('Y');
        $path = base_path().$destinationPath;
        if (empty($filename))
        {
            $filename = $file->getClientOriginalName();
        }
        if (!is_dir($path))
        {
            mkdir($path, 0777, true);
        }
        if (!is_dir($path.$year))
        {
            mkdir($path.$year, 0777);
        }
        $file->move($path.$year.'/', $filename);
        return $year.'/'.$filename;
    }

    public function limit_words ($string, $word_limit)
    {
        $strip = strip_tags($string);
        $words = explode(' ', $strip);
        if (count($words) >= $word_limit)
        {
            return implode(' ', array_splice($words, 0, $word_limit)).'...';
        }
        else
            return $string;
    }

    public function slug ($text)
    {
        //replace non letter or digits by (_)
        $text = preg_replace('/\W|_/', '_', $text);
        // Clean up extra dashes
        $text = preg_replace('/-+/', '-', trim($text, '_')); // Clean up extra dashes
        // lowercase
        $text = strtolower($text);
        if (empty($text))
        {
            return false;
        }
        return $text;
    }
	public function profilePinVerifyLink ($token)
    {
        $op = $data = $usrdata = [];
        if (!empty($token) && strpos($token, '.'))
        {
            $access_token = explode('.', $token);
            $this->session->setId($access_token[0], true);

            if ($this->session->has('resetProfilePin'))
            {
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
        return view('user.account.forgot_profile_pin', (array) $data);
    }

}
