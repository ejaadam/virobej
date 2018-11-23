<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Commonsettings;
use Request;
use View;
use Lang;
use Config;
use Redirect;
use Session;

class SupplierBaseController extends Controller
{	
	public $response = '';
	public $request = '';
	public $redirect = '';
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
		$this->redirect = redirect();
		$this->config = config();
		$this->session = session();
		$this->geo = $this->session->has('geo') ? $this->session->get('geo') : (object) ['current'=>(object) ['address'=>'', 'flatno_street'=>'', 'lat'=>0, 'lng'=>0, 'country_id'=>77, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode_id'=>0, 'region_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>'', 'currency_id'=>2, 'country'=>'India', 'country_code'=>'IN'], 'browse'=>(object) ['address'=>'', 'flatno_street'=>'', 'lat'=>0, 'lng'=>0, 'country_id'=>77, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode_id'=>0, 'region_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>'', 'currency_id'=>2, 'country'=>'India', 'country_code'=>'IN']];
		$this->siteConfig = $this->config->get('settings');		
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $this->account_id = null;
		//print_r(Config::get('device_log'));
        $this->device_log_id = Config::get('device_log')->device_log_id;
        $this->token = Config::get('device_log')->token;
        $this->headers['X-Device-Token'] = $this->token;		
        $this->commonstObj = new Commonsettings();
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
		//echo '<pre>';print_r(Session::all());exit;
		//echo '<pre>';print_r($this->session->get('seller'));exit;
		
        if ($this->session->has('seller'))			
        {	
            //$this->config->get('data.user', $this->session->get('userdata'));
			//$user_details = $this->config->get('data.user');				
			$user_details = $this->session->get('seller');									
			$this->sessionName = 'seller';			
		    if (!empty($user_details))
            {	
				//print_R($user_details->account_id);exit;
				$this->acc_type_id 	= $this->account_type_id = $user_details->account_type_id;
                $this->account_id 	= $user_details->account_id;
                $this->supplier_id  = $user_details->supplier_id;
                $this->uname 		= $user_details->uname;
                $this->full_name 	= $user_details->full_name;
                $this->email 		= $user_details->email;
                $this->mobile 		= $user_details->mobile;
                $this->language_id  = $user_details->language_id;
				$this->token 		= $user_details->token;
				//$this->mobile_validation 		= $user_details->mobile_validation;
				$this->primary_store_id  = isset($user_details->primary_store_id)?$user_details->primary_store_id:0;
                //$this->time_zone_id = $user_details->time_zone_id;
                $this->currency_id = $user_details->currency_id;                
                $this->is_mobile_verified = $user_details->is_mobile_verified;
                $this->is_email_verified = $user_details->is_email_verified;               		
				$user_details->address = $this->commonstObj->getUserAddress($this->supplier_id, $this->acc_type_id);					
                $this->user_details = $this->userSess = $user_details;
                if (!empty($this->userSess))
                {
			        View::share('logged_userinfo', $user_details);
					View::share('userSess', $this->userSess);
					//print_r($this->userSess);exit;
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
            $location_info = $this->commonstObj->checkPincode();
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

    public function slug ($text)
    {
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

}
