<?php
namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\AffModel;
use File;
class LocationController extends AffBaseController {    
    
    public function __construct ()
    {
        parent::__construct();
		$this->affObj = new AffModel();
    }	
	
	public function load_state()
	{
		$data=$this->request->all();
		$states =	$this->affObj->load_states($data);
		return $this->response->json($states);
	}
	public function load_countries()
	{
		$country =	$this->affObj->load_country();
		return $this->response->json($country);
	}
	//public function load_currency()
	//{
	
	
	//print_r("Xgxdc"); die;
		// $currency =	$this->affObj->currency();
		
		
		// print_r($currency); die;
		// return $this->response->json($currency);
	// }
}