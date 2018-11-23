<?php 
namespace App\Helpers;

use Illuminate\Support\ServiceProvider;
use Eloquent;
use View;

class SGMailerLib extends Eloquent{	

	private $url = 'https://api.sendgrid.com/api/mail.send.json';
	
	private $params = array(
		'api_user'  => 'onlinesensorint',
		'api_key'   => 'ejugiter@123',		
		'subject'   => '',
		'html'      => '',
		'text'      => '',
		'from'      => '',
		'fromname'  => '',
		'to'        => '' );	
	
	/**
	 * Create a new Mailer instance.
	 *
	 * @param  \Illuminate\View\Factory  $views
	 * @param  \Swift_Mailer  $swift
	 * @param  \Illuminate\Events\Dispatcher  $events
	 * @return void
	 */
	
	public function send($view, array $data, $emailData)	 {
		
		foreach($emailData as $key => $val){
			if($key=='to' && strpos($val,'@telserra.com')>0){
				$this->params[$key] = 'ejdevteam@gmail.com';
			}
			else {
				$this->params[$key] = $val;
			}		
		}
		//return true;
		$this->params['html'] = view($view,$data)->render();	
		
		
		// Generate curl request
		$session = curl_init($this->url);
		
		// Tell curl to use HTTP POST
		curl_setopt ($session, CURLOPT_POST, true);
		
		// Tell curl that this is the body of the POST
		curl_setopt ($session, CURLOPT_POSTFIELDS, $this->params);
		
		// Tell curl not to return headers, but do return the response
		curl_setopt($session, CURLOPT_HEADER, false);
		// Tell PHP not to use SSLv3 (instead opting for TLS)
		curl_setopt($session, CURLOPT_SSLVERSION, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		
		// obtain response
		$response = curl_exec($session);
		curl_close($session);
		
		// print everything out
		return $response;
	}
}