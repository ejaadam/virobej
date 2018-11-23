<?php

namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Controller;
use App\Models\Api\CommonModel;
use App\Models\Api\User\AccountModel;
use View;

class UserApiBaseController extends Controller
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

    public function __construct ()
    {
        $this->request = request();
        $this->response = response();
        $this->session = session();
        $this->config = config();
        $this->headers = ['X-Route-Selection-Time'=>round((microtime(true) - LARAVEL_START) * 1000, 3).' ms'];
		
		$this->geo = $this->session->has('geo') ? $this->session->get('geo') : (object) [
                        'current'=>(object) ['isSet'=>false, 'lat'=>0, 'lng'=>0, 'country_id'=>0, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>1, 'currency_id'=>0, 'country'=>'', 'country_code'=>''],
                        'browse'=>(object) ['isSet'=>false, 'lat'=>0, 'lng'=>0, 'country_id'=>0, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>1, 'currency_id'=>0, 'country'=>'', 'country_code'=>'']
            ];	 		
		
        $this->siteConfig = $this->config->get('settings');
		View::share('siteConfig', $this->siteConfig);
		$this->pagesettings = (object) $this->config->get('site_settings');		
		View::share('pagesettings', $this->pagesettings);
        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        $this->commonObj = new CommonModel($this);
		$this->accountObj = new AccountModel();
        //$this->currency_id = 2;
        //$this->country_id = 77;
        if ($this->config->has('app.accountInfo') && !empty($this->config->get('app.accountInfo')))
        {
            $this->userSess = (object) $this->config->get('app.accountInfo');			
            $this->sessionName = $this->config->get('app.role');
            $this->currency_id = !empty($this->userSess->currency_id) ? $this->userSess->currency_id : 2;
            $this->session->set($this->sessionName, (array) $this->userSess);
			/* $this->geo->current = $this->userSess->address;			
            $this->geo->current = !empty(array_filter((array) $this->geo->current)) ? $this->geo->current : $this->userSess->address;
			$this->geo->current->isSet = true;			 */			
        }
        elseif ($this->config->has('app.guestInfo') && !empty($this->config->get('app.guestInfo')))
        {
            $this->userSess = (object) $this->config->get('app.guestInfo');
            // print_r($this->userSess);exit;
            $this->sessionName = 'guest';
            $this->currency_id = !empty($this->userSess->currency_id) ? $this->userSess->currency_id : 2;
            $this->session->set($this->sessionName, (array) $this->userSess);
            //print_r($this->geo);exit;
        }        
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

}
